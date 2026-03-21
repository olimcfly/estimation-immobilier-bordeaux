<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

final class PerplexityService
{
    public function fetchMarketRange(string $city, string $propertyType): array
    {
        $apiKey = (string) Config::get('perplexity.api_key', '');
        if ($apiKey === '') {
            return $this->fallback($city, $propertyType);
        }

        $endpoint = (string) Config::get('perplexity.endpoint');
        $model = (string) Config::get('perplexity.model');

        $prompt = sprintf(
            'Donne un JSON strict avec low, mid, high (prix m2 en EUR) pour un %s à %s.',
            $propertyType,
            $city
        );

        $payload = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.1,
        ];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return $this->fallback($city, $propertyType);
        }

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '';
        $market = json_decode((string) $content, true);

        if (!is_array($market) || !isset($market['low'], $market['mid'], $market['high'])) {
            return $this->fallback($city, $propertyType);
        }

        return [
            'low' => (float) $market['low'],
            'mid' => (float) $market['mid'],
            'high' => (float) $market['high'],
        ];
    }

    private function fallback(string $city, string $propertyType): array
    {
        $baseline = 4200.0;

        if (str_contains(mb_strtolower($city), 'angers')) {
            $baseline = 4800.0;
        }

        if (str_contains(mb_strtolower($propertyType), 'maison')) {
            $baseline *= 1.08;
        }

        return [
            'low' => round($baseline * 0.85, 2),
            'mid' => round($baseline, 2),
            'high' => round($baseline * 1.18, 2),
        ];
    }
}
