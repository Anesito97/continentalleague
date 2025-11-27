<?php

namespace App\Services;

use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IdealElevenService
{
    private PlayerRatingService $ratingService;

    public function __construct(PlayerRatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function getBestEleven(?int $teamId = null): array
    {
        // 4-3-3 Formation
        // Use lowercase position names as found in the database
        return [
            'forwards' => $this->getBestPlayers('delantero', 3, $teamId),
            'midfielders' => $this->getBestPlayers('medio', 3, $teamId),
            'defenders' => $this->getBestPlayers('defensa', 4, $teamId),
            'goalkeeper' => $this->getBestPlayers('portero', 1, $teamId)->first(),
        ];
    }

    private function getBestPlayers(string $position, int $limit, ?int $teamId = null): Collection
    {
        // Get all players of the position, optionally filtered by team
        $query = Jugador::where('posicion_general', $position)
            ->with('equipo');

        if ($teamId) {
            $query->where('equipo_id', $teamId);
        }

        $players = $query->get();

        // Calculate score for each player
        $players->each(function ($player) {
            $player->rating = $this->calculateRating($player);
        });

        // Sort by rating desc, then by goals desc, then by name asc for stability
        return $players->sort(function ($a, $b) {
            if ($a->rating === $b->rating) {
                if ($a->goles === $b->goles) {
                    return strcmp($a->nombre, $b->nombre);
                }
                return $b->goles <=> $a->goles;
            }
            return $b->rating <=> $a->rating;
        })->take($limit);
    }

    private function calculateRating(Jugador $player): float
    {
        $stats = [
            'goals' => $player->goles ?? 0,
            'assists' => $player->asistencias ?? 0,
            'saves' => $player->paradas ?? 0,
            'yellow_cards' => $player->amarillas ?? 0,
            'red_cards' => $player->rojas ?? 0,
            'goals_conceded' => 0,
            'clean_sheets' => 0,
            'matches_lost' => 0,
        ];

        if ($player->equipo_id) {
            $stats['goals_conceded'] = $this->calculateGoalsConceded($player->equipo_id);
            $stats['clean_sheets'] = $this->calculateCleanSheets($player->equipo_id);
            $stats['matches_lost'] = $this->calculateMatchesLost($player->equipo_id);
        }

        return $this->ratingService->calculate($player->posicion_general, $stats);
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

    private function calculateCleanSheets(?int $teamId): int
    {
        if (!$teamId)
            return 0;

        // Count matches where the team conceded 0 goals
        $homeCleanSheets = Partido::where('equipo_local_id', $teamId)
            ->where('estado', 'finalizado')
            ->where('goles_visitante', 0)
            ->count();

        $awayCleanSheets = Partido::where('equipo_visitante_id', $teamId)
            ->where('estado', 'finalizado')
            ->where('goles_local', 0)
            ->count();

        return $homeCleanSheets + $awayCleanSheets;
    }

    private function calculateMatchesLost(?int $teamId): int
    {
        if (!$teamId)
            return 0;

        $lostAsHome = Partido::where('equipo_local_id', $teamId)
            ->where('estado', 'finalizado')
            ->whereColumn('goles_local', '<', 'goles_visitante')
            ->count();

        $lostAsAway = Partido::where('equipo_visitante_id', $teamId)
            ->where('estado', 'finalizado')
            ->whereColumn('goles_visitante', '<', 'goles_local')
            ->count();

        return $lostAsHome + $lostAsAway;
    }
}
