<?php

declare(strict_types=1);

if (!isset($page) || !is_array($page)) {
    throw new RuntimeException('Configuration quartier manquante.');
}

$quartier = (string) ($page['quartier'] ?? 'Quartier');
$ville = (string) ($page['ville'] ?? 'Bordeaux');
$slug = (string) ($page['slug'] ?? 'estimation-immobiliere-quartier');
$prixMin = (int) ($page['prix_min'] ?? 0);
$prixMax = (int) ($page['prix_max'] ?? 0);
$atouts = $page['atouts'] ?? [];
$rows = $page['rows'] ?? [];
$projets = $page['projets'] ?? [];
$faqs = $page['faqs'] ?? [];

$schemaFaq = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static fn(array $faq): array => [
        '@type' => 'Question',
        'name' => (string) $faq['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => (string) $faq['answer']],
    ], $faqs),
];
?>
<!doctype html>
<html lang="fr"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) $page['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?= htmlspecialchars((string) $page['meta_description'], ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="https://www.example.com/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>.php">
    <script type="application/ld+json"><?= json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <style>
        body{margin:0;background:#f8fafc;color:#0f172a;font-family:Inter,Arial,sans-serif;line-height:1.55}
        .wrap{max-width:1060px;margin:0 auto;padding:20px}.hero{background:#0f766e;color:#fff;padding:26px;border-radius:14px}
        .card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;margin:12px 0}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.item{border:1px solid #e2e8f0;border-radius:10px;padding:12px}
        table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #e2e8f0;text-align:left}th{background:#f1f5f9}
        .cta{display:inline-block;padding:11px 14px;border-radius:8px;background:#f97316;color:#fff;text-decoration:none;font-weight:700}
        @media(max-width:760px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<main class="wrap">
    <section class="hero">
        <h1>Estimation Immobilière <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> – Prix au m² 2026</h1>
        <h2>Votre bien dans <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> vaut entre <?= number_format($prixMin, 0, ',', ' '); ?> € et <?= number_format($prixMax, 0, ',', ' '); ?> €.</h2>
        <p>Découvrez sa valeur exacte avec notre outil basé sur les données DVF.</p>
        <a class="cta" href="/#estimation-form">Estimer mon bien dans <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> →</a>
    </section>

    <section class="card">
        <h2>Pourquoi investir dans <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> ?</h2>
        <div class="grid">
            <?php foreach ($atouts as $atout): ?>
                <article class="item"><h3><?= htmlspecialchars((string) $atout['titre'], ENT_QUOTES, 'UTF-8'); ?></h3><p><?= htmlspecialchars((string) $atout['texte'], ENT_QUOTES, 'UTF-8'); ?></p></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="card">
        <h2>Prix de l’immobilier à <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> en 2026</h2>
        <table>
            <thead><tr><th>Type</th><th>Prix moyen</th><th>Évolution 1 an</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $row['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars((string) $row['prix'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars((string) $row['evolution'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Projets urbains qui boostent la valeur des biens</h2>
        <ul>
            <?php foreach ($projets as $projet): ?>
                <li><?= htmlspecialchars((string) $projet, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card">
        <h2>Questions fréquentes</h2>
        <?php foreach ($faqs as $faq): ?>
            <details>
                <summary><strong><?= htmlspecialchars((string) $faq['question'], ENT_QUOTES, 'UTF-8'); ?></strong></summary>
                <p><?= htmlspecialchars((string) $faq['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
            </details>
        <?php endforeach; ?>
    </section>

    <section class="card">
        <h2>Vous possédez un bien à <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> ?</h2>
        <p>Ne le vendez pas sans connaître sa valeur exacte.</p>
        <a class="cta" href="/#estimation-form">Obtenir mon estimation →</a>
    </section>
</main>
</body></html>
