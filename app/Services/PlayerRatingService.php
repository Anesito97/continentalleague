<?php

namespace App\Services;

class PlayerRatingService
{
    // Weights
    private const WEIGHTS = [
        'portero' => [
            'goal' => 50,
            'assist' => 20,
            'save' => 2,
            'goal_conceded' => -0.7,
            'clean_sheet' => 5,
            'match_lost' => -0.3,
        ],
        'defensa' => [
            'goal' => 12,
            'assist' => 8,
            'goal_conceded' => -1.5,
            'clean_sheet' => 5,
        ],
        'medio' => [
            'goal' => 8,
            'assist' => 8,
        ],
        'delantero' => [
            'goal' => 10,
            'assist' => 7,
        ],
    ];

    private const PENALTIES = [
        'yellow_card' => -1,
        'red_card' => -3,
        'own_goal' => -10,
    ];

    public function calculate(string $position, array $stats): float
    {
        $position = strtolower($position);
        $weights = self::WEIGHTS[$position] ?? self::WEIGHTS['delantero']; // Default to forward if unknown

        $score = 0;

        // Positive Actions
        $score += ($stats['goals'] ?? 0) * ($weights['goal'] ?? 0);
        $score += ($stats['assists'] ?? 0) * ($weights['assist'] ?? 0);
        $score += ($stats['saves'] ?? 0) * ($weights['save'] ?? 0);
        $score += ($stats['clean_sheets'] ?? 0) * ($weights['clean_sheet'] ?? 0);

        // Negative Actions (Weights are already negative in constant)
        $score += ($stats['goals_conceded'] ?? 0) * ($weights['goal_conceded'] ?? 0);
        $score += ($stats['matches_lost'] ?? 0) * ($weights['match_lost'] ?? 0);

        // General Penalties
        $score += ($stats['yellow_cards'] ?? 0) * self::PENALTIES['yellow_card'];
        $score += ($stats['red_cards'] ?? 0) * self::PENALTIES['red_card'];
        $score += ($stats['own_goals'] ?? 0) * self::PENALTIES['own_goal'];

        return $score;
    }
}
