<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

final class ActualiteService
{
    private const SEARCH_TOPICS = [
        'marché immobilier Lannion actualité prix',
        'immobilier Lannion Métropole tendances',
        'vente immobilière Côtes-d'Armor nouveautés',
        'prix immobilier quartiers Lannion évolution',
        'investissement immobilier Lannion CUB',
        'immobilier neuf Lannion programmes',
        'taux crédit immobilier impact Lannion',
        'urbanisme Lannion projets aménagement',
    ];

    /**
     * Search Perplexity for real estate news around Lannion.
     */
    public function searchNews(?string $customQuery = null): array
    {
        $query = $customQuery ?? self::SEARCH_TOPICS[array_rand(self::SEARCH_TOPICS)];

        $apiKey = (string) Config::get('perplexity.api_key', '');
        if ($apiKey === '') {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        $prompt = sprintf(
            "Recherche les actualités immobilières récentes à Lannion et ses alentours (Côtes-d'Armor, Bretagne) sur le thème : \"%s\".\n"
            . "Retourne exactement 5 idées d'articles sous forme JSON avec les clés : title, summary, angle (l'angle éditorial unique).\n"
            . "Concentre-toi sur les données les plus récentes (dernière semaine/mois).\n"
            . "Réponds UNIQUEMENT en JSON valide : {\"articles\": [...]}",
            $query
        );

        $endpoint = (string) Config::get('perplexity.endpoint', 'https://api.perplexity.ai/chat/completions');
        $model = (string) Config::get('perplexity.model', 'sonar-pro');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un expert en veille immobilière sur Lannion et sa métropole. Tu fournis des actualités factuelles et récentes.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        $content = $response['choices'][0]['message']['content'] ?? '';
        $content = trim((string) $content);

        // Try to extract JSON from response
        if (preg_match('/\{[\s\S]*"articles"[\s\S]*\}/u', $content, $matches)) {
            $content = $matches[0];
        }

        $decoded = json_decode($content, true);
        $articles = $decoded['articles'] ?? [];

        if (empty($articles)) {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        return [
            'query' => $query,
            'results' => $articles,
            'source' => 'perplexity',
        ];
    }

    /**
     * Select the best article idea and generate a full article using OpenAI.
     */
    public function generateArticleFromIdeas(array $ideas, string $query): array
    {
        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '' || empty($ideas)) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Lannion']);
        }

        $ideasText = '';
        foreach ($ideas as $i => $idea) {
            $ideasText .= ($i + 1) . ". Titre: " . ($idea['title'] ?? 'Sans titre')
                . " | Résumé: " . ($idea['summary'] ?? '')
                . " | Angle: " . ($idea['angle'] ?? '') . "\n";
        }

        $prompt = "Tu es un rédacteur expert en immobilier à Lannion. Voici 5 idées d'articles d'actualité immobilière :\n\n"
            . $ideasText . "\n"
            . "1. Choisis la MEILLEURE idée (la plus intéressante, actuelle et utile pour des propriétaires/vendeurs de lannion).\n"
            . "2. Rédige un article complet en HTML (balises h2, h3, p, ul, li, strong) de 800-1200 mots.\n"
            . "3. L'article doit être factuel, informatif, avec des données chiffrées quand possible.\n"
            . "4. Inclus un CTA vers l'estimation immobilière à la fin.\n\n"
            . "Réponds en JSON avec les clés : title, meta_title, meta_description, excerpt (2 phrases), content_html, image_prompt (prompt pour générer une image illustrative en anglais).";

        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un journaliste immobilier spécialisé sur Lannion et la Côtes-d'Armor. Tu rédiges des articles professionnels et engageants.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Lannion']);
        }

        $content = $response['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode((string) $content, true);

        if (!is_array($decoded) || !isset($decoded['title'], $decoded['content_html'])) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Lannion']);
        }

        return [
            'title' => (string) $decoded['title'],
            'meta_title' => (string) ($decoded['meta_title'] ?? $decoded['title']),
            'meta_description' => (string) ($decoded['meta_description'] ?? ''),
            'excerpt' => (string) ($decoded['excerpt'] ?? ''),
            'content' => (string) $decoded['content_html'],
            'image_prompt' => (string) ($decoded['image_prompt'] ?? ''),
        ];
    }

    /**
     * Generate an image for the article using OpenAI.
     */
    public function generateImage(string $imagePrompt): ?string
    {
        if (trim($imagePrompt) === '') {
            $imagePrompt = 'Professional real estate photography of beautiful Lannion architecture, stone buildings, sunny day, editorial style';
        }

        $imageService = new ImageGeneratorService();
        $result = $imageService->generate($imagePrompt, '1536x1024', 'medium');

        if (($result['success'] ?? false) === true) {
            return $result['url'] ?? null;
        }

        return null;
    }

    /**
     * Full automated pipeline: search → select → write → image → save.
     */
    public function runAutomatedPipeline(?string $customQuery = null): array
    {
        // Step 1: Search for news
        $searchResults = $this->searchNews($customQuery);
        $ideas = $searchResults['results'];
        $query = $searchResults['query'];

        if (empty($ideas)) {
            return ['success' => false, 'error' => 'Aucun résultat trouvé.', 'query' => $query];
        }

        // Step 2: Generate article from best idea
        $article = $this->generateArticleFromIdeas($ideas, $query);

        // Step 3: Generate image
        $imageUrl = null;
        $imagePrompt = $article['image_prompt'] ?? '';
        if ($imagePrompt !== '') {
            $imageUrl = $this->generateImage($imagePrompt);
        }

        return [
            'success' => true,
            'query' => $query,
            'ideas_count' => count($ideas),
            'source_results' => json_encode($ideas, JSON_UNESCAPED_UNICODE),
            'article' => [
                'title' => $article['title'],
                'meta_title' => $article['meta_title'],
                'meta_description' => $article['meta_description'],
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
                'image_url' => $imageUrl,
                'image_prompt' => $imagePrompt,
                'source_query' => $query,
            ],
        ];
    }

    private function fallbackNewsResults(string $query): array
    {
        return [
            ['title' => 'Évolution des prix immobiliers à Lannion ce mois', 'summary' => 'Les prix au m² continuent leur ajustement dans les quartiers centraux.', 'angle' => 'Analyse quartier par quartier'],
            ['title' => 'Nouveaux projets urbains en métropole de lannione', 'summary' => 'Plusieurs projets d\'aménagement transforment le paysage immobilier.', 'angle' => 'Impact sur les valeurs immobilières'],
            ['title' => 'Taux de crédit : impact sur le marché de lannion', 'summary' => 'L\'évolution des taux influence les décisions d\'achat et de vente.', 'angle' => 'Opportunités pour vendeurs'],
            ['title' => 'Le marché locatif étudiant à Lannion', 'summary' => 'La demande locative étudiante reste forte dans certains quartiers.', 'angle' => 'Investissement locatif'],
            ['title' => 'Rénovation énergétique : les aides disponibles en Côtes-d'Armor', 'summary' => 'Les nouvelles réglementations impactent la valeur des biens.', 'angle' => 'Valorisation du patrimoine'],
        ];
    }

    private function fallbackArticle(array $idea): array
    {
        $title = $idea['title'] ?? 'Actualité immobilière Lannion';
        $summary = $idea['summary'] ?? 'Les dernières nouvelles du marché immobilier de lannion.';

        return [
            'title' => $title,
            'meta_title' => $title . ' | Actualités Immobilier Lannion',
            'meta_description' => $summary,
            'excerpt' => $summary,
            'content' => '<h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>'
                . '<p>' . htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<h2>Ce que cela signifie pour vous</h2>'
                . '<p>Le marché immobilier de lannion continue d\'évoluer. Que vous soyez propriétaire souhaitant vendre ou simplement curieux de la valeur de votre bien, il est important de rester informé des dernières tendances.</p>'
                . '<h2>Les quartiers à surveiller</h2>'
                . '<ul><li><strong>Chartrons</strong> : un quartier en constante valorisation</li>'
                . '<li><strong>Bastide</strong> : le renouveau de la rive droite</li>'
                . '<li><strong>Saint-Michel</strong> : authenticité et dynamisme</li>'
                . '<li><strong>Caudéran</strong> : le calme résidentiel prisé des familles</li></ul>'
                . '<h2>Estimez votre bien gratuitement</h2>'
                . '<p>Vous souhaitez connaître la valeur actuelle de votre bien immobilier à Lannion ? '
                . '<strong><a href="/estimation">Lancez votre estimation gratuite</a></strong> et obtenez un résultat en moins de 2 minutes.</p>',
            'image_prompt' => 'Professional editorial photo of Lannion city skyline with stone architecture and Garonne river, warm lighting, real estate magazine style',
        ];
    }

    private function postJson(string $endpoint, array $payload, array $headers): ?array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }
}
