<?php

declare(strict_types=1);

if (!isset($page) || !is_array($page)) {
    throw new RuntimeException('Configuration de page manquante.');
}

$typeBien = (string) ($page['type_bien'] ?? 'Bien immobilier');
$ville = (string) ($page['ville'] ?? 'Bordeaux');
$zoneFocus = (string) ($page['zone_focus'] ?? $ville);
$prixMin = (int) ($page['prix_min'] ?? 0);
$prixMax = (int) ($page['prix_max'] ?? 0);
$slug = (string) ($page['slug'] ?? 'estimation-bien');
$heroImageAlt = (string) ($page['hero_alt'] ?? ($typeBien . ' typique à ' . $ville));
$priceRows = $page['prix_quartiers'] ?? [];
$criteres = $page['criteres'] ?? [];
$faqs = $page['faqs'] ?? [];
$testimonials = $page['testimonials'] ?? [];
$keywords = $page['keywords'] ?? [];

$faqEntities = array_map(
    static fn (array $faq): array => [
        '@type' => 'Question',
        'name' => (string) ($faq['question'] ?? ''),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => (string) ($faq['answer'] ?? ''),
        ],
    ],
    $faqs
);

$schemaFaq = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqEntities,
];

$schemaLocalBusiness = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => 'Skyline de Bordeaux',
    'description' => 'Outil d’estimation immobilière basé sur les données DVF et l’IA.',
    'areaServed' => ['Bordeaux', 'Mérignac', 'Pessac', 'Talence', 'Bègles'],
    'telephone' => '+33 5 00 00 00 00',
    'url' => 'https://www.example.com/' . $slug . '.php',
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($page['title'] ?? ('Estimation ' . $typeBien . ' à ' . $ville)), ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?= htmlspecialchars((string) ($page['meta_description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="canonical" href="https://www.example.com/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>.php">
    <script type="application/ld+json"><?= json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <script type="application/ld+json"><?= json_encode($schemaLocalBusiness, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <style>
        body{margin:0;font-family:Inter,Arial,sans-serif;background:#f8fafc;color:#0f172a;line-height:1.55}
        .wrap{max-width:1060px;margin:0 auto;padding:20px}
        .hero{background:linear-gradient(130deg,#1e3a8a,#1d4ed8);color:#fff;padding:28px;border-radius:14px}
        .hero p{color:#dbeafe}
        .cta{display:inline-block;padding:11px 14px;border-radius:8px;text-decoration:none;font-weight:700;margin:6px 8px 0 0}
        .cta-main{background:#f97316;color:#fff}.cta-alt{color:#fff;border:1px solid #bfdbfe}
        .card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;margin:14px 0}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .item{border:1px solid #e2e8f0;border-radius:10px;padding:12px}
        .ok{color:#047857;font-weight:700}
        table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #e2e8f0;text-align:left}
        th{background:#f1f5f9}
        details{border:1px solid #e2e8f0;border-radius:9px;padding:10px;margin:8px 0}
        @media(max-width:760px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<main class="wrap">
    <section class="hero">
        <p>✨ Estimation gratuite en 30 secondes</p>
        <h1>Estimation <?= htmlspecialchars($typeBien, ENT_QUOTES, 'UTF-8'); ?> à <?= htmlspecialchars($ville, ENT_QUOTES, 'UTF-8'); ?> – Gratuite en 30 secondes</h1>
        <h2>Votre <?= strtolower(htmlspecialchars($typeBien, ENT_QUOTES, 'UTF-8')); ?> à <?= htmlspecialchars($zoneFocus, ENT_QUOTES, 'UTF-8'); ?> vaut entre <?= number_format($prixMin, 0, ',', ' '); ?> € et <?= number_format($prixMax, 0, ',', ' '); ?> €.</h2>
        <p>Découvrez sa valeur exacte grâce à notre outil basé sur les données DVF et l’IA.</p>
        <img src="https://placehold.co/1200x450" alt="<?= htmlspecialchars($heroImageAlt, ENT_QUOTES, 'UTF-8'); ?>" style="max-width:100%;border-radius:10px;border:1px solid #bfdbfe;">
        <div>
            <a href="/#estimation-form" class="cta cta-main">Obtenir mon estimation précise →</a>
            <a href="#fiabilite" class="cta cta-alt">Pourquoi choisir notre estimation ?</a>
        </div>
    </section>

    <section class="card" id="fiabilite">
        <h2>Pourquoi notre estimation est-elle fiable ?</h2>
        <div class="grid">
            <article class="item"><p class="ok">✅ Données officielles DVF</p><p>Transactions réelles des 12 derniers mois.</p></article>
            <article class="item"><p class="ok">✅ Algorithme IA</p><p>Analyse de milliers de ventes sur Bordeaux Métropole.</p></article>
            <article class="item"><p class="ok">✅ Précision jusqu’à 95%</p><p>Modèle contextualisé par quartier et type de bien.</p></article>
            <article class="item"><p class="ok">✅ Rapport détaillé gratuit</p><p>Prix au m² local et recommandations de mise en vente.</p></article>
        </div>
    </section>

    <section class="card">
        <h2>Prix moyen des <?= htmlspecialchars(strtolower($typeBien), ENT_QUOTES, 'UTF-8'); ?>s à <?= htmlspecialchars($ville, ENT_QUOTES, 'UTF-8'); ?></h2>
        <table>
            <thead><tr><th>Secteur</th><th>Prix moyen au m²</th><th>Évolution 1 an</th><th>Projet</th></tr></thead>
            <tbody>
            <?php foreach ($priceRows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $row['zone'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= number_format((int) $row['prix_m2'], 0, ',', ' '); ?> €</td>
                    <td><?= htmlspecialchars((string) $row['evolution'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars((string) $row['projet'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Comment estimer votre <?= htmlspecialchars(strtolower($typeBien), ENT_QUOTES, 'UTF-8'); ?> en 3 étapes</h2>
        <ol>
            <li>Sélectionnez votre type de bien.</li>
            <li>Indiquez surface et localisation.</li>
            <li>Recevez votre estimation par email en 2 minutes.</li>
        </ol>
    </section>

    <section class="card">
        <h2>Critères clés qui font varier le prix</h2>
        <ul>
            <?php foreach ($criteres as $critere): ?>
                <li><?= htmlspecialchars((string) $critere, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card">
        <h2>Témoignages clients</h2>
        <div class="grid">
            <?php foreach ($testimonials as $t): ?>
                <article class="item">
                    <h3><?= htmlspecialchars((string) $t['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?= htmlspecialchars((string) $t['quote'], ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
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
        <h2>Prêt à connaître la valeur exacte de votre <?= htmlspecialchars(strtolower($typeBien), ENT_QUOTES, 'UTF-8'); ?> ?</h2>
        <a href="/#estimation-form" class="cta cta-main">Obtenir mon estimation gratuite →</a>
        <a href="tel:+33500000000" class="cta" style="border:1px solid #e2e8f0;color:#0f172a">Besoin d’aide ? 05 00 00 00 00</a>
        <p>Mots-clés cibles : <?= htmlspecialchars(implode(' • ', $keywords), ENT_QUOTES, 'UTF-8'); ?></p>
    </section>
</main>
</body>
</html>
