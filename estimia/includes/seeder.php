<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class CitySeeder
{
    private PDO $db;

    /** @var array<int, array<string, mixed>> */
    private array $allCities;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->allCities = require __DIR__ . '/../install/data/cities_france.php';
    }

    /** @return array<int, array<string, mixed>> */
    public function getCitiesInRadius(float $lat, float $lng, float $radiusKm): array
    {
        $cities = [];
        foreach ($this->allCities as $city) {
            $distance = $this->haversineDistance($lat, $lng, (float) $city['lat'], (float) $city['lng']);
            if ($distance <= $radiusKm) {
                $city['distance'] = round($distance, 1);
                $cities[] = $city;
            }
        }

        usort($cities, static fn(array $a, array $b): int => ($a['distance'] <=> $b['distance']));

        return $cities;
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function seedForZone(float $centerLat, float $centerLng, float $radiusKm): int
    {
        $cities = $this->getCitiesInRadius($centerLat, $centerLng, $radiusKm);

        $this->db->exec('TRUNCATE TABLE villes_prix');

        $stmt = $this->db->prepare(
            'INSERT INTO villes_prix
            (ville, code_postal, departement, region, lat, lng,
             prix_m2_appartement, prix_m2_maison, prix_m2_studio, prix_m2_terrain,
             tendance_annuelle, nb_transactions, population, distance_centre)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );

        $count = 0;
        foreach ($cities as $city) {
            $population = max(1000, (int) ($city['population'] ?? 10000));
            $stmt->execute([
                $city['ville'],
                $city['code_postal'],
                $city['departement'],
                $city['region'],
                $city['lat'],
                $city['lng'],
                $city['prix_m2_appartement'],
                $city['prix_m2_maison'],
                $city['prix_m2_studio'],
                $city['prix_m2_terrain'],
                $city['tendance'],
                $this->estimateTransactions($population),
                $population,
                $city['distance'] ?? 0,
            ]);
            $count++;
        }

        return $count;
    }

    private function estimateTransactions(int $population): int
    {
        return max(10, (int) ($population * 0.005 * (mt_rand(70, 130) / 100)));
    }

    /** @return array<string, mixed>|null */
    public function findNearestCity(float $lat, float $lng): ?array
    {
        $nearest = null;
        $minDist = PHP_FLOAT_MAX;

        foreach ($this->allCities as $city) {
            $dist = $this->haversineDistance($lat, $lng, (float) $city['lat'], (float) $city['lng']);
            if ($dist < $minDist) {
                $minDist = $dist;
                $nearest = $city;
                $nearest['distance'] = round($dist, 1);
            }
        }

        return $nearest;
    }
}
