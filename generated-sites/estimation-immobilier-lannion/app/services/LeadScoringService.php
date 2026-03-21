<?php

declare(strict_types=1);

namespace App\Services;

final class LeadScoringService
{
    private const HOT_THRESHOLD = 8;
    private const WARM_THRESHOLD = 5;

    public function score(float $estimation, string $urgency, string $motivation): string
    {
        $budgetPoints = $estimation >= 450000 ? 3 : ($estimation >= 250000 ? 2 : 1);

        $urgency = mb_strtolower(trim($urgency));
        $urgencyPoints = match ($urgency) {
            'rapide' => 3,
            'moyen' => 2,
            default => 1,
        };

        $motivation = mb_strtolower(trim($motivation));
        $motivationPoints = match ($motivation) {
            'vente', 'divorce' => 3,
            'succession' => 2,
            default => 1,
        };

        $score = $budgetPoints + $urgencyPoints + $motivationPoints;

        if ($score >= self::HOT_THRESHOLD) {
            return 'chaud';
        }

        if ($score >= self::WARM_THRESHOLD) {
            return 'tiede';
        }

        return 'froid';
    }
}
