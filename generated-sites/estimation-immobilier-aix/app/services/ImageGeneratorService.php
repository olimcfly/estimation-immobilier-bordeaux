<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

final class ImageGeneratorService
{
    private const STORAGE_DIR = 'public/assets/images/ai-generated';
    private const ALLOWED_SIZES = ['1024x1024', '1536x1024', '1024x1536'];
    private const DEFAULT_SIZE = '1024x1024';
    private const DEFAULT_QUALITY = 'medium';

    public function generate(string $prompt, string $size = self::DEFAULT_SIZE, string $quality = self::DEFAULT_QUALITY): array
    {
        $prompt = trim($prompt);
        if ($prompt === '') {
            return ['success' => false, 'error' => 'Le prompt ne peut pas être vide.'];
        }

        if (!in_array($size, self::ALLOWED_SIZES, true)) {
            $size = self::DEFAULT_SIZE;
        }

        if (!in_array($quality, ['low', 'medium', 'high'], true)) {
            $quality = self::DEFAULT_QUALITY;
        }

        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Clé API OpenAI non configurée.'];
        }

        $payload = [
            'model' => 'gpt-image-1',
            'prompt' => $prompt,
            'n' => 1,
            'size' => $size,
            'quality' => $quality,
        ];

        $response = $this->callApi($apiKey, $payload);

        if ($response === null) {
            return ['success' => false, 'error' => 'Erreur lors de l\'appel à l\'API OpenAI.'];
        }

        if (isset($response['error'])) {
            $errorMsg = $response['error']['message'] ?? 'Erreur API inconnue.';
            return ['success' => false, 'error' => 'API OpenAI : ' . $errorMsg];
        }

        $b64Data = $response['data'][0]['b64_json'] ?? null;
        if ($b64Data === null) {
            return ['success' => false, 'error' => 'Aucune image retournée par l\'API.'];
        }

        $imageData = base64_decode($b64Data, true);
        if ($imageData === false) {
            return ['success' => false, 'error' => 'Données image invalides.'];
        }

        return $this->saveImage($imageData, $prompt);
    }

    public function generateSeoPrompt(string $type, string $quartier = '', string $style = ''): string
    {
        $quartier = $quartier !== '' ? $quartier : 'Aix-en-Provence centre';
        $style = $style !== '' ? $style : 'moderne et lumineux';

        $prompts = [
            'estimation' => "Photo professionnelle immobilière d'une belle propriété {$style} dans le quartier {$quartier} à Aix-en-Provence. Vue extérieure avec façade en pierre de aix-en-provencee typique, lumière naturelle dorée du soleil couchant, végétation soignée. Style éditorial haut de gamme pour agence immobilière.",
            'interieur' => "Photo d'intérieur immobilier professionnel, salon {$style} dans un appartement de aix-en-provence haussmannien, parquet en point de Hongrie, hauts plafonds avec moulures, grandes fenêtres lumineuses. Décoration épurée et chaleureuse. Photographie immobilière éditoriale.",
            'quartier' => "Vue panoramique du quartier {$quartier} à Aix-en-Provence, architecture typique en pierre blonde, rues piétonnes animées, terrasses de cafés. Ambiance chaleureuse et authentique du Sud-Ouest. Photographie urbaine professionnelle.",
            'blog' => "Illustration éditoriale pour blog immobilier, concept de l'estimation immobilière à Aix-en-Provence. Maison de aix-en-provencee en pierre avec une loupe ou des éléments graphiques subtils représentant l'analyse de marché. Style professionnel et moderne.",
            'cta' => "Image d'appel à l'action pour site immobilier, couple souriant devant une belle maison de aix-en-provencee {$style}, ambiance positive et professionnelle. Espace pour superposition de texte. Photographie lifestyle immobilier.",
        ];

        return $prompts[$type] ?? $prompts['blog'];
    }

    public function listGeneratedImages(): array
    {
        $dir = base_path(self::STORAGE_DIR);
        if (!is_dir($dir)) {
            return [];
        }

        $images = [];
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);

        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
                continue;
            }

            $filePath = $dir . '/' . $file;
            $images[] = [
                'filename' => $file,
                'url' => '/assets/images/ai-generated/' . $file,
                'size' => filesize($filePath),
                'created_at' => date('Y-m-d H:i:s', filemtime($filePath) ?: time()),
            ];
        }

        return $images;
    }

    public function deleteImage(string $filename): bool
    {
        $filename = basename($filename);
        $filePath = base_path(self::STORAGE_DIR . '/' . $filename);

        if (!is_file($filePath)) {
            return false;
        }

        return unlink($filePath);
    }

    private function callApi(string $apiKey, array $payload): ?array
    {
        $ch = curl_init('https://api.openai.com/v1/images/generations');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 120,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log('[ImageGenerator] cURL error: ' . $curlError);
            return null;
        }

        if ($httpCode >= 500) {
            error_log('[ImageGenerator] API server error: HTTP ' . $httpCode);
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function saveImage(string $imageData, string $prompt): array
    {
        $dir = base_path(self::STORAGE_DIR);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return ['success' => false, 'error' => 'Impossible de créer le répertoire de stockage.'];
            }
        }

        $slug = $this->slugify(mb_substr($prompt, 0, 50));
        $filename = date('Ymd_His') . '_' . $slug . '.png';
        $filePath = $dir . '/' . $filename;

        if (file_put_contents($filePath, $imageData) === false) {
            return ['success' => false, 'error' => 'Impossible de sauvegarder l\'image.'];
        }

        $url = '/assets/images/ai-generated/' . $filename;

        return [
            'success' => true,
            'filename' => $filename,
            'url' => $url,
            'html_tag' => '<img src="' . $url . '" alt="' . htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') . '" loading="lazy">',
            'size' => strlen($imageData),
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');

        return $text !== '' ? $text : 'image';
    }
}
