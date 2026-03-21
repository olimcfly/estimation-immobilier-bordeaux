<?php

declare(strict_types=1);

namespace App\Services;

final class EstimationService
{
    public function estimate(string $city, string $propertyType, float $surface, int $rooms): array
    {
        $cityFactor = $this->resolveCityFactor($city);
        $typeFactor = $this->resolvePropertyTypeFactor($propertyType);
        $surfaceFactor = $this->resolveSurfaceFactor($surface);
        $roomsFactor = $this->resolveRoomsFactor($rooms);

        // Simulation locale simple (pas d'appel API pour l'instant)
        $basePerSqm = 4200.0;
        $perSqmMid = round($basePerSqm * $cityFactor * $typeFactor * $surfaceFactor * $roomsFactor, 2);

        $perSqmLow = round($perSqmMid * 0.9, 2);
        $perSqmHigh = round($perSqmMid * 1.1, 2);

        $estimatedLow = round($perSqmLow * $surface, 2);
        $estimatedMid = round($perSqmMid * $surface, 2);
        $estimatedHigh = round($perSqmHigh * $surface, 2);

        return [
            'city' => $city,
            'property_type' => $propertyType,
            'surface' => $surface,
            'rooms' => $rooms,
            'per_sqm_low' => $perSqmLow,
            'per_sqm_mid' => $perSqmMid,
            'per_sqm_high' => $perSqmHigh,
            'estimated_low' => $estimatedLow,
            'estimated_mid' => $estimatedMid,
            'estimated_high' => $estimatedHigh,
        ];
    }

    private function resolveCityFactor(string $city): float
    {
        $cityLower = mb_strtolower($city);

        if (str_contains($cityLower, 'lannion')) {
            return 0.72;
        }

        if (str_contains($cityLower, 'paris')) {
            return 2.35;
        }

        if (str_contains($cityLower, 'lyon')) {
            return 1.35;
        }

        return 1.0;
    }

    private function resolvePropertyTypeFactor(string $propertyType): float
    {
        $propertyTypeLower = mb_strtolower($propertyType);

        if (str_contains($propertyTypeLower, 'maison')) {
            return 1.08;
        }

        if (str_contains($propertyTypeLower, 'studio')) {
            return 1.12;
        }

        return 1.0;
    }

    private function resolveSurfaceFactor(float $surface): float
    {
        if ($surface < 30) {
            return 1.12;
        }

        if ($surface > 140) {
            return 0.92;
        }

        return 1.0;
    }

    private function resolveRoomsFactor(int $rooms): float
    {
        if ($rooms <= 1) {
            return 1.05;
        }

        if ($rooms >= 6) {
            return 0.95;
        }

        return 1.0;
    }
}
