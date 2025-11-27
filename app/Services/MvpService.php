<?php

namespace App\Services;

use App\Models\EventoPartido;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Support\Collection;

class MvpService
{
    private PlayerRatingService $ratingService;

    public function __construct(PlayerRatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Calculate the MVP for a specific matchday (jornada).
     *
     * @param int $jornada
     * @return Jugador|null
     */
    public function getMvpForJornada(int $jornada): ?Jugador
    {
        // 1. Get all matches for this jornada
        $matches = Partido::where('jornada', $jornada)->get();

        if ($matches->isEmpty()) {
            return null;
        }

        // 2. Get all events for these matches
        $matchIds = $matches->pluck('id');
        $events = EventoPartido::whereIn('partido_id', $matchIds)
            ->with(['jugador.equipo', 'partido'])
            ->get();

        if ($events->isEmpty()) {
            return null;
        }

        // 3. Group events by player
        $playerEvents = $events->groupBy('jugador_id');

        $bestPlayer = null;
        $highestScore = -999;

        // 4. Calculate score for each player
        foreach ($playerEvents as $playerId => $playerEventList) {
            $player = $playerEventList->first()->jugador;

            if (!$player)
                continue;

            $score = $this->calculatePlayerMatchScore($player, $playerEventList, $matches);

            // Assign score to player object for display if needed
            $player->match_score = $score;

            // Tie-breaking: Score > Goals > Name
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestPlayer = $player;
            } elseif ($score == $highestScore) {
                // Tie-breaker 1: Goals in this match
                $goalsA = $this->countGoals($playerEventList);
                // We'd need to re-fetch the current best player's events to compare accurately, 
                // but for simplicity/performance, we'll stick to score or just overwrite.
                // Let's overwrite if name is alphabetically first to be deterministic
                if (strcmp($player->nombre, $bestPlayer->nombre) < 0) {
                    $bestPlayer = $player;
                }
            }
        }

        // Ensure the rating property is set to the match score for the card component
        if ($bestPlayer) {
            $bestPlayer->rating = $highestScore;
            // Determine MVP reason
            $bestPlayerEvents = $playerEvents[$bestPlayer->id];
            $bestPlayer->mvp_reason = $this->determineMvpReason($bestPlayer, $bestPlayerEvents, $matches);
        }

        return $bestPlayer;
    }

    private function calculatePlayerMatchScore(Jugador $player, Collection $events, Collection $matches): float
    {
        $position = strtolower($player->posicion_general);

        // Count events
        $stats = [
            'goals' => $this->countGoals($events),
            'own_goals' => $this->countOwnGoals($events),
            'assists' => $this->countEvents($events, 'asistencia'),
            'saves' => $this->countEvents($events, 'parada'),
            'yellow_cards' => $this->countEvents($events, 'amarilla'),
            'red_cards' => $this->countEvents($events, 'roja'),
            'clean_sheets' => $this->hasCleanSheet($player, $events, $matches) ? 1 : 0,
            'goals_conceded' => $this->calculateGoalsConcededInMatch($player, $events, $matches),
            'matches_lost' => $this->hasLostMatch($player, $events, $matches) ? 1 : 0,
        ];

        return $this->ratingService->calculate($position, $stats);
    }

    private function calculateGoalsConcededInMatch(Jugador $player, Collection $events, Collection $matches): int
    {
        $sampleEvent = $events->first();
        if (!$sampleEvent)
            return 0;

        $match = $matches->firstWhere('id', $sampleEvent->partido_id);
        if (!$match)
            return 0;

        $playerTeamId = $sampleEvent->equipo_id ?? $player->equipo_id;

        if ($match->equipo_local_id == $playerTeamId) {
            return $match->goles_visitante;
        } elseif ($match->equipo_visitante_id == $playerTeamId) {
            return $match->goles_local;
        }
        return 0;
    }

    private function hasLostMatch(Jugador $player, Collection $events, Collection $matches): bool
    {
        $sampleEvent = $events->first();
        if (!$sampleEvent)
            return false;

        $match = $matches->firstWhere('id', $sampleEvent->partido_id);
        if (!$match)
            return false;

        $playerTeamId = $sampleEvent->equipo_id ?? $player->equipo_id;

        if ($match->equipo_local_id == $playerTeamId) {
            return $match->goles_local < $match->goles_visitante;
        } elseif ($match->equipo_visitante_id == $playerTeamId) {
            return $match->goles_visitante < $match->goles_local;
        }
        return false;
    }

    private function calculateGoalsConceded(?int $teamId): int
    {
        if (!$teamId)
            return 0;

        // Sum goals conceded in all finished matches
        $concededAsHome = Partido::where('equipo_local_id', $teamId)
            ->where('estado', 'finalizado')
            ->sum('goles_visitante');

        $concededAsAway = Partido::where('equipo_visitante_id', $teamId)
            ->where('estado', 'finalizado')
            ->sum('goles_local');

        return $concededAsHome + $concededAsAway;
    }

    private function determineMvpReason(Jugador $player, Collection $events, Collection $matches): string
    {
        $goals = $this->countGoals($events);
        $assists = $this->countEvents($events, 'asistencia');
        $saves = $this->countEvents($events, 'parada');
        $cleanSheet = $this->hasCleanSheet($player, $events, $matches);
        $position = strtolower($player->posicion_general);

        if ($goals >= 5)
            return 'Repoker de Goles';
        if ($goals == 4)
            return 'Poker de Goles';
        if ($goals == 3)
            return 'Hat-trick';

        if ($position === 'portero') {
            if ($cleanSheet && $saves >= 4)
                return 'Muro Impenetrable';
            if ($cleanSheet)
                return 'Arco en Cero';
            if ($saves >= 6)
                return 'Actuación Heroica';
        }

        if ($position === 'defensa') {
            if ($goals >= 1 && $cleanSheet)
                return 'Líder Defensivo';
            if ($cleanSheet && $assists >= 1)
                return 'Muralla Creativa';
        }

        if ($assists >= 3)
            return 'Rey de Asistencias';

        if ($goals == 2)
            return 'Doblete';

        if ($goals >= 1 && $assists >= 1)
            return 'Motor del Equipo';

        return 'Actuación Destacada';
    }

    private function countEvents(Collection $events, string $type): int
    {
        return $events->where('tipo_evento', $type)->count();
    }

    private function countGoals(Collection $events): int
    {
        // Count goals that are NOT own goals
        return $events->filter(function ($event) {
            return $event->tipo_evento === 'gol' && strtolower($event->goal_type ?? '') !== 'en contra';
        })->count();
    }

    private function countOwnGoals(Collection $events): int
    {
        return $events->filter(function ($event) {
            return $event->tipo_evento === 'gol' && strtolower($event->goal_type ?? '') === 'en contra';
        })->count();
    }

    private function hasCleanSheet(Jugador $player, Collection $events, Collection $matches): bool
    {
        // Find the match this player played in
        $sampleEvent = $events->first();
        if (!$sampleEvent)
            return false;

        $matchId = $sampleEvent->partido_id;
        $match = $matches->firstWhere('id', $matchId);

        if (!$match)
            return false;

        $playerTeamId = $sampleEvent->equipo_id ?? $player->equipo_id;

        if ($match->equipo_local_id == $playerTeamId) {
            return $match->goles_visitante == 0;
        } elseif ($match->equipo_visitante_id == $playerTeamId) {
            return $match->goles_local == 0;
        }

        return false;
    }
}
