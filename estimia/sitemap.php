<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://www.estimia.fr';
$today = date('Y-m-d');

$urls = [
    ['loc' => $baseUrl . '/', 'priority' => '1.0'],
    ['loc' => $baseUrl . '/resultat.php', 'priority' => '0.5'],
    ['loc' => $baseUrl . '/rdv.php', 'priority' => '0.5'],
];

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT ville FROM villes_prix ORDER BY ville ASC');
    $stmt->execute();
    $villes = $stmt->fetchAll();

    foreach ($villes as $row) {
        $ville = strtolower(trim((string) ($row['ville'] ?? '')));
        if ($ville === '') {
            continue;
        }

        $slug = str_replace(' ', '-', $ville);
        $urls[] = [
            'loc' => $baseUrl . '/?ville=' . rawurlencode($slug),
            'priority' => '0.8',
        ];
    }
} catch (Throwable $e) {
    // En cas d'erreur DB, le sitemap reste valide avec les pages statiques.
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?php echo htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8'); ?></loc>
            <lastmod><?php echo $today; ?></lastmod>
            <priority><?php echo $url['priority']; ?></priority>
        </url>
    <?php endforeach; ?>
</urlset>
