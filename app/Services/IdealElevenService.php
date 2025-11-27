<?php

namespace App\Services;

use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IdealElevenService
{
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
        $score = 0;
        $goals = $player->goles ?? 0;
        $assists = $player->asistencias ?? 0;
        $saves = $player->paradas ?? 0;

        // Calculate team stats
        $goalsConceded = 0;
        if ($player->equipo_id) {
            $goalsConceded = $this->calculateGoalsConceded($player->equipo_id);
        }

        switch ($player->posicion_general) {
            case 'delantero':
                // FWD: Goals + Assists
                $score += ($goals * 10);
                $score += ($assists * 7);
                break;

            case 'medio':
                // MID: Goals + Assists
                $score += ($goals * 8);
                $score += ($assists * 8);
                break;

            case 'defensa':
                // DEF: Goals + Assists + Goals Received (Negative)
                $score += ($goals * 12); // High value for scoring defenders
                $score += ($assists * 8);
                $score -= ($goalsConceded * 1.5); // Penalty for conceding

                // Bonus for clean sheets (still good to have)
                $cleanSheets = $this->calculateCleanSheets($player->equipo_id);
                $score += ($cleanSheets * 5);
                break;

            case 'portero':
                // GK: Saves + Goals Received (Negative)
                $score += ($saves * 5);
                $score -= ($goalsConceded * 2);

                // Bonus for clean sheets
                $cleanSheets = $this->calculateCleanSheets($player->equipo_id);
                $score += ($cleanSheets * 10);
                break;
        }

        // General Penalties
        $score -= ($player->amarillas ?? 0) * 1;
        $score -= ($player->rojas ?? 0) * 3;

        return $score;
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
}
