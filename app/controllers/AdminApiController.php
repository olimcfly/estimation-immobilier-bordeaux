<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\View;

final class AdminApiController
{
    /**
     * API management page - show all configured APIs with their status.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $apis = $this->getApiDefinitions();

        View::renderAdmin('admin/api-management', [
            'page_title' => 'Gestion des API - Admin',
            'admin_page_title' => 'Gestion des API',
            'admin_current_page' => 'api-management',
            'apis' => $apis,
        ]);
    }

    /**
     * Test a specific API via AJAX (POST).
     */
    public function testApi(string $apiKey): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        $apis = $this->getApiDefinitions();
        if (!isset($apis[$apiKey])) {
            echo json_encode(['success' => false, 'error' => 'API inconnue : ' . $apiKey]);
            return;
        }

        $result = match ($apiKey) {
            'openai' => $this->testOpenAI(),
            'claude' => $this->testClaude(),
            'perplexity' => $this->testPerplexity(),
            'google_maps' => $this->testGoogleMaps(),
            'sms_partner' => $this->testSmsPartner(),
            'twilio' => $this->testTwilio(),
            'dvf' => $this->testDvf(),
            default => ['success' => false, 'error' => 'Test non disponible'],
        };

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Save API keys via AJAX (POST).
     */
    public function saveKeys(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        $apiName = trim((string) ($_POST['api_name'] ?? ''));
        $fields = $_POST['fields'] ?? [];

        if ($apiName === '' || !is_array($fields) || empty($fields)) {
            echo json_encode(['success' => false, 'error' => 'Donnees invalides']);
            return;
        }

        $envFile = dirname(__DIR__, 2) . '/.env';
        if (!is_file($envFile)) {
            echo json_encode(['success' => false, 'error' => 'Fichier .env introuvable']);
            return;
        }

        $envContent = (string) file_get_contents($envFile);

        foreach ($fields as $key => $value) {
            $key = preg_replace('/[^A-Z0-9_]/', '', strtoupper((string) $key));
            if ($key === '') {
                continue;
            }
            $safeValue = str_replace('"', '\\"', (string) $value);

            if (preg_match('/^' . preg_quote($key, '/') . '=/m', $envContent)) {
                $envContent = preg_replace(
                    '/^' . preg_quote($key, '/') . '=.*$/m',
                    $key . '="' . $safeValue . '"',
                    $envContent
                );
            } else {
                $envContent = rtrim($envContent) . "\n" . $key . '="' . $safeValue . '"' . "\n";
            }

            $_ENV[$key] = (string) $value;
        }

        $written = file_put_contents($envFile, $envContent);
        if ($written === false) {
            echo json_encode(['success' => false, 'error' => 'Impossible d\'ecrire dans .env']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Configuration sauvegardee']);
    }

    // ─── API Definitions ─────────────────────────────────

    private function getApiDefinitions(): array
    {
        return [
            'openai' => [
                'name' => 'OpenAI',
                'icon' => 'fa-brain',
                'color' => '#10a37f',
                'description' => 'GPT-4, DALL-E - Generation de contenu et images',
                'configured' => ($_ENV['OPENAI_API_KEY'] ?? '') !== '',
                'env_keys' => ['OPENAI_API_KEY', 'OPENAI_MODEL', 'OPENAI_ENDPOINT'],
                'pricing_url' => 'https://openai.com/pricing',
                'pricing_info' => 'GPT-4o-mini: ~$0.15/1M input, ~$0.60/1M output',
                'category' => 'ia',
            ],
            'claude' => [
                'name' => 'Claude (Anthropic)',
                'icon' => 'fa-robot',
                'color' => '#d97706',
                'description' => 'Claude - Assistant IA avance',
                'configured' => ($_ENV['ANTHROPIC_API_KEY'] ?? '') !== '',
                'env_keys' => ['ANTHROPIC_API_KEY', 'ANTHROPIC_MODEL'],
                'pricing_url' => 'https://anthropic.com/pricing',
                'pricing_info' => 'Claude Sonnet 4: ~$3/1M input, ~$15/1M output',
                'category' => 'ia',
            ],
            'perplexity' => [
                'name' => 'Perplexity',
                'icon' => 'fa-magnifying-glass',
                'color' => '#1fb8cd',
                'description' => 'Recherche IA - Tendances du marche immobilier',
                'configured' => ($_ENV['PERPLEXITY_API_KEY'] ?? '') !== '',
                'env_keys' => ['PERPLEXITY_API_KEY', 'PERPLEXITY_MODEL', 'PERPLEXITY_ENDPOINT'],
                'pricing_url' => 'https://docs.perplexity.ai/docs/pricing',
                'pricing_info' => 'Sonar Pro: ~$3/1M input, ~$15/1M output',
                'category' => 'ia',
            ],
            'google_maps' => [
                'name' => 'Google Maps Places',
                'icon' => 'fa-map-location-dot',
                'color' => '#4285f4',
                'description' => 'Geocodage et donnees de localisation immobiliere',
                'configured' => ($_ENV['GOOGLE_MAPS_API_KEY'] ?? '') !== '',
                'env_keys' => ['GOOGLE_MAPS_API_KEY'],
                'pricing_url' => 'https://developers.google.com/maps/billing-and-pricing/pricing',
                'pricing_info' => 'Places API: $17/1000 requetes (apres credit $200/mois)',
                'category' => 'geo',
            ],
            'sms_partner' => [
                'name' => 'SMS Partner',
                'icon' => 'fa-comment-sms',
                'color' => '#e91e63',
                'description' => 'Envoi de SMS - Notifications leads',
                'configured' => ($_ENV['SMSPARTNER_API_KEY'] ?? '') !== '',
                'env_keys' => ['SMSPARTNER_API_KEY'],
                'pricing_url' => 'https://www.smspartner.fr/tarifs',
                'pricing_info' => 'A partir de 0.049EUR/SMS (France)',
                'category' => 'comm',
            ],
            'twilio' => [
                'name' => 'Twilio',
                'icon' => 'fa-phone',
                'color' => '#f22f46',
                'description' => 'SMS et appels - Communication multi-canal',
                'configured' => ($_ENV['TWILIO_ACCOUNT_SID'] ?? '') !== '' && ($_ENV['TWILIO_AUTH_TOKEN'] ?? '') !== '',
                'env_keys' => ['TWILIO_ACCOUNT_SID', 'TWILIO_AUTH_TOKEN', 'TWILIO_PHONE_NUMBER'],
                'pricing_url' => 'https://www.twilio.com/pricing',
                'pricing_info' => 'SMS France: ~0.0725EUR/SMS',
                'category' => 'comm',
            ],
            'dvf' => [
                'name' => 'DVF (Valeurs Foncieres)',
                'icon' => 'fa-landmark',
                'color' => '#000091',
                'description' => 'Donnees publiques des transactions immobilieres',
                'configured' => true, // API publique, pas de clé nécessaire
                'env_keys' => [],
                'pricing_url' => 'https://app.dvf.etalab.gouv.fr/',
                'pricing_info' => 'Gratuit - API publique gouvernementale',
                'category' => 'data',
            ],
        ];
    }

    // ─── API Test Methods ────────────────────────────────

    private function testOpenAI(): array
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (OPENAI_API_KEY)'];
        }

        $endpoint = $_ENV['OPENAI_ENDPOINT'] ?? 'https://api.openai.com/v1/chat/completions';
        $model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';

        $payload = json_encode([
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Reponds uniquement "OK" sans rien d\'autre.']],
            'max_tokens' => 5,
        ]);

        $start = microtime(true);
        $result = $this->curlPost($endpoint, $payload, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (isset($data['error'])) {
            return ['success' => false, 'error' => $data['error']['message'] ?? 'Erreur inconnue', 'latency_ms' => $latency];
        }

        $response = $data['choices'][0]['message']['content'] ?? '';
        $usage = $data['usage'] ?? null;

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'model' => $data['model'] ?? $model,
            'response' => $response,
            'latency_ms' => $latency,
            'usage' => $usage,
        ];
    }

    private function testClaude(): array
    {
        $apiKey = $_ENV['ANTHROPIC_API_KEY'] ?? '';
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (ANTHROPIC_API_KEY)'];
        }

        $model = $_ENV['ANTHROPIC_MODEL'] ?? 'claude-sonnet-4-20250514';

        $payload = json_encode([
            'model' => $model,
            'max_tokens' => 10,
            'messages' => [['role' => 'user', 'content' => 'Reponds uniquement "OK" sans rien d\'autre.']],
        ]);

        $start = microtime(true);
        $result = $this->curlPost('https://api.anthropic.com/v1/messages', $payload, [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json',
        ]);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (isset($data['error'])) {
            return ['success' => false, 'error' => $data['error']['message'] ?? 'Erreur inconnue', 'latency_ms' => $latency];
        }

        $response = $data['content'][0]['text'] ?? '';
        $usage = $data['usage'] ?? null;

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'model' => $data['model'] ?? $model,
            'response' => $response,
            'latency_ms' => $latency,
            'usage' => $usage,
        ];
    }

    private function testPerplexity(): array
    {
        $apiKey = $_ENV['PERPLEXITY_API_KEY'] ?? '';
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (PERPLEXITY_API_KEY)'];
        }

        $endpoint = $_ENV['PERPLEXITY_ENDPOINT'] ?? 'https://api.perplexity.ai/chat/completions';
        $model = $_ENV['PERPLEXITY_MODEL'] ?? 'sonar-pro';

        $payload = json_encode([
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Reponds uniquement "OK" sans rien d\'autre.']],
            'max_tokens' => 5,
        ]);

        $start = microtime(true);
        $result = $this->curlPost($endpoint, $payload, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (isset($data['error'])) {
            return ['success' => false, 'error' => $data['error']['message'] ?? 'Erreur inconnue', 'latency_ms' => $latency];
        }

        $response = $data['choices'][0]['message']['content'] ?? '';
        $usage = $data['usage'] ?? null;

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'model' => $data['model'] ?? $model,
            'response' => $response,
            'latency_ms' => $latency,
            'usage' => $usage,
        ];
    }

    private function testGoogleMaps(): array
    {
        $apiKey = $_ENV['GOOGLE_MAPS_API_KEY'] ?? '';
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (GOOGLE_MAPS_API_KEY)'];
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode('Bordeaux, France') . '&key=' . urlencode($apiKey);

        $start = microtime(true);
        $result = $this->curlGet($url);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (($data['status'] ?? '') !== 'OK') {
            $errorMsg = $data['error_message'] ?? ($data['status'] ?? 'Erreur inconnue');
            return ['success' => false, 'error' => $errorMsg, 'latency_ms' => $latency];
        }

        $address = $data['results'][0]['formatted_address'] ?? 'Bordeaux';

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'response' => 'Geocodage OK: ' . $address,
            'latency_ms' => $latency,
        ];
    }

    private function testSmsPartner(): array
    {
        $apiKey = $_ENV['SMSPARTNER_API_KEY'] ?? '';
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (SMSPARTNER_API_KEY)'];
        }

        $url = 'https://api.smspartner.fr/v1/me?apiKey=' . urlencode($apiKey);

        $start = microtime(true);
        $result = $this->curlGet($url);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (!isset($data['success']) || $data['success'] !== true) {
            return ['success' => false, 'error' => $data['error'] ?? 'Cle API invalide', 'latency_ms' => $latency];
        }

        $credits = $data['credits'] ?? '?';

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'response' => 'Credits restants: ' . $credits,
            'latency_ms' => $latency,
            'usage' => ['credits' => $credits],
        ];
    }

    private function testTwilio(): array
    {
        $sid = $_ENV['TWILIO_ACCOUNT_SID'] ?? '';
        $token = $_ENV['TWILIO_AUTH_TOKEN'] ?? '';
        if ($sid === '' || $token === '') {
            return ['success' => false, 'error' => 'Cle API non configuree (TWILIO_ACCOUNT_SID / TWILIO_AUTH_TOKEN)'];
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . urlencode($sid) . '.json';

        $start = microtime(true);
        $result = $this->curlGet($url, ['Authorization: Basic ' . base64_encode($sid . ':' . $token)]);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        if (isset($data['code']) && $data['code'] !== 200 && $data['code'] !== 0) {
            return ['success' => false, 'error' => $data['message'] ?? 'Erreur d\'authentification', 'latency_ms' => $latency];
        }

        $status = $data['status'] ?? 'unknown';
        $friendlyName = $data['friendly_name'] ?? '';

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'response' => 'Compte: ' . $friendlyName . ' (statut: ' . $status . ')',
            'latency_ms' => $latency,
        ];
    }

    private function testDvf(): array
    {
        $url = 'https://api.cquest.org/dvf?code_postal=33000&nature_mutation=Vente&limit=1';

        $start = microtime(true);
        $result = $this->curlGet($url);
        $latency = round((microtime(true) - $start) * 1000);

        if ($result['error'] !== null) {
            return ['success' => false, 'error' => $result['error'], 'latency_ms' => $latency];
        }

        $data = json_decode($result['body'], true);
        $count = $data['nb_resultats'] ?? ($data['count'] ?? null);
        $features = $data['resultats'] ?? ($data['features'] ?? []);

        if ($count === null && empty($features)) {
            return ['success' => false, 'error' => 'Reponse inattendue de l\'API DVF', 'latency_ms' => $latency];
        }

        return [
            'success' => true,
            'message' => 'API fonctionnelle',
            'response' => 'DVF accessible - ' . ($count ?? count($features)) . ' transaction(s) trouvee(s) pour 33000',
            'latency_ms' => $latency,
        ];
    }

    // ─── HTTP Helpers ────────────────────────────────────

    private function curlPost(string $url, string $payload, array $headers = []): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['body' => '', 'error' => 'Impossible d\'initialiser cURL'];
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $error !== '') {
            return ['body' => '', 'error' => 'Erreur cURL: ' . $error];
        }

        if ($httpCode >= 400) {
            $decoded = json_decode((string) $body, true);
            $msg = $decoded['error']['message'] ?? $decoded['message'] ?? ('HTTP ' . $httpCode);
            return ['body' => (string) $body, 'error' => $msg];
        }

        return ['body' => (string) $body, 'error' => null];
    }

    private function curlGet(string $url, array $headers = []): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return ['body' => '', 'error' => 'Impossible d\'initialiser cURL'];
        }

        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($ch, $opts);

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $error !== '') {
            return ['body' => '', 'error' => 'Erreur cURL: ' . $error];
        }

        if ($httpCode >= 400) {
            $decoded = json_decode((string) $body, true);
            $msg = $decoded['error']['message'] ?? $decoded['message'] ?? ('HTTP ' . $httpCode);
            return ['body' => (string) $body, 'error' => $msg];
        }

        return ['body' => (string) $body, 'error' => null];
    }
}
