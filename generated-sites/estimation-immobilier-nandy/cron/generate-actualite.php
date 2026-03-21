#!/usr/bin/env php
<?php

/**
 * Automated weekly actualité generator.
 *
 * This script:
 * 1. Searches Perplexity for recent real estate news in Nandy
 * 2. Selects the best article idea
 * 3. Generates a full article via OpenAI
 * 4. Generates an AI image
 * 5. Publishes the article automatically
 *
 * Usage:
 *   php cron/generate-actualite.php [--query="custom search query"] [--dry-run]
 *
 * Crontab (weekly, every Monday at 8am):
 *   0 8 * * 1 /usr/bin/php /path/to/cron/generate-actualite.php >> /var/log/actualites-cron.log 2>&1
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Models\Actualite;
use App\Services\ActualiteService;

echo "[" . date('Y-m-d H:i:s') . "] === Génération automatique d'actualité ===\n";

// Parse CLI arguments
$dryRun = in_array('--dry-run', $argv ?? [], true);
$customQuery = null;
foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--query=')) {
        $customQuery = substr($arg, 8);
    }
}

$service = new ActualiteService();
$model = new Actualite();

try {
    // Step 1: Run the automated pipeline
    echo "  Recherche et génération en cours...\n";
    $result = $service->runAutomatedPipeline($customQuery);

    if (!($result['success'] ?? false)) {
        $error = $result['error'] ?? 'Erreur inconnue';
        echo "  ERREUR: {$error}\n";
        $model->logCron($result['query'] ?? '', 0, null, 'error', $error);
        exit(1);
    }

    $article = $result['article'];
    $ideasCount = $result['ideas_count'] ?? 0;
    $query = $result['query'] ?? '';

    echo "  Requête: {$query}\n";
    echo "  Idées trouvées: {$ideasCount}\n";
    echo "  Article: {$article['title']}\n";
    echo "  Image: " . ($article['image_url'] ?? 'aucune') . "\n";

    if ($dryRun) {
        echo "\n  [DRY RUN] Article non sauvegardé.\n";
        echo "  Titre: {$article['title']}\n";
        echo "  Extrait: {$article['excerpt']}\n";
        exit(0);
    }

    // Step 2: Save to database
    $articleId = $model->create([
        'title' => $article['title'],
        'slug' => slugify($article['title']),
        'content' => $article['content'],
        'excerpt' => $article['excerpt'],
        'meta_title' => $article['meta_title'],
        'meta_description' => $article['meta_description'],
        'image_url' => $article['image_url'],
        'image_prompt' => $article['image_prompt'],
        'source_query' => $article['source_query'],
        'source_results' => $result['source_results'] ?? null,
        'status' => 'published',
        'generated_by' => 'cron',
    ]);

    echo "  Article publié avec ID: {$articleId}\n";

    // Step 3: Log success
    $model->logCron($query, $ideasCount, $articleId, 'success');

    echo "[" . date('Y-m-d H:i:s') . "] === Terminé avec succès ===\n\n";

} catch (\Throwable $e) {
    echo "  EXCEPTION: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    try {
        $model->logCron($customQuery ?? 'unknown', 0, null, 'error', $e->getMessage());
    } catch (\Throwable) {
        // Ignore logging errors
    }

    exit(1);
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
    $text = trim($text, '-');
    return $text !== '' ? $text : 'actualite';
}
