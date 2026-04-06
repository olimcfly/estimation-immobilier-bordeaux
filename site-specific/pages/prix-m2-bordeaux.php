<?php

declare(strict_types=1);

$prixParQuartier = [
    ['quartier' => 'Centre-ville', 'appartement' => 5600, 'maison' => 6200, 'evolution' => '+3,1%'],
    ['quartier' => 'Chartrons', 'appartement' => 5900, 'maison' => 6600, 'evolution' => '+2,6%'],
    ['quartier' => 'Caudéran', 'appartement' => 5100, 'maison' => 5800, 'evolution' => '+1,9%'],
    ['quartier' => 'Bastide', 'appartement' => 4700, 'maison' => 5400, 'evolution' => '+2,2%'],
    ['quartier' => 'Saint-Michel', 'appartement' => 5000, 'maison' => 5600, 'evolution' => '+2,9%'],
];

$prixAppartementMoyen = (int) round(array_sum(array_column($prixParQuartier, 'appartement')) / count($prixParQuartier));
$prixMaisonMoyen = (int) round(array_sum(array_column($prixParQuartier, 'maison')) / count($prixParQuartier));
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prix au m² à Bordeaux : carte des quartiers et tendances 2026</title>
    <meta name="description" content="Consultez les prix immobiliers au m² à Bordeaux par quartier : appartements, maisons, évolution des prix et conseils pour estimer votre bien.">
    <link rel="canonical" href="https://www.example.com/prix-au-m2-bordeaux.php">
    <meta property="og:title" content="Prix au m² à Bordeaux en 2026">
    <meta property="og:description" content="Données de prix au m² par quartier bordelais pour affiner l'estimation de votre bien.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://www.example.com/prix-au-m2-bordeaux.php">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "Prix au m² à Bordeaux : quartiers et tendances",
      "description": "Analyse des prix immobiliers à Bordeaux avec données utiles pour vendeurs et acheteurs.",
      "author": {"@type": "Organization", "name": "EstimIA"},
      "datePublished": "2026-04-06",
      "dateModified": "2026-04-06"
    }
    </script>
    <style>
        :root {
            color-scheme: light;
            --text: #111827;
            --muted: #6b7280;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e5e7eb;
            --brand: #2563eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, Arial, sans-serif;
            line-height: 1.55;
        }
        .container { max-width: 980px; margin: 0 auto; padding: 24px; }
        .hero, .card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; }
        .hero { padding: 28px; margin-bottom: 20px; }
        .hero h1 { margin-top: 0; margin-bottom: 8px; }
        .muted { color: var(--muted); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
        .card { padding: 18px; }
        .kpi { font-size: 1.8rem; font-weight: 700; color: var(--brand); margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
        th, td { border-bottom: 1px solid var(--border); text-align: left; padding: 10px 8px; }
        th { background: #f1f5f9; }
        .cta {
            display: inline-block;
            margin-top: 16px;
            background: var(--brand);
            color: #fff;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
        }
        @media (max-width: 700px) {
            .grid { grid-template-columns: 1fr; }
            .container { padding: 16px; }
        }
    </style>
</head>
<body>
<main class="container">
    <section class="hero">
        <h1>Prix au m² à Bordeaux en 2026</h1>
        <p class="muted">Cette page SEO présente une vision claire des prix immobiliers à Bordeaux, quartier par quartier, pour aider les particuliers à mieux estimer leur bien.</p>
        <a class="cta" href="/">Estimer mon bien gratuitement</a>
    </section>

    <section class="grid" aria-label="Indicateurs principaux">
        <article class="card">
            <p class="muted">Prix moyen appartement</p>
            <p class="kpi"><?= number_format($prixAppartementMoyen, 0, ',', ' ') ?> € / m²</p>
        </article>
        <article class="card">
            <p class="muted">Prix moyen maison</p>
            <p class="kpi"><?= number_format($prixMaisonMoyen, 0, ',', ' ') ?> € / m²</p>
        </article>
    </section>

    <section class="card" aria-labelledby="table-title">
        <h2 id="table-title">Prix au m² par quartier</h2>
        <table>
            <thead>
            <tr>
                <th>Quartier</th>
                <th>Appartement (€/m²)</th>
                <th>Maison (€/m²)</th>
                <th>Évolution annuelle</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($prixParQuartier as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['quartier'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= number_format((int) $row['appartement'], 0, ',', ' ') ?> €</td>
                    <td><?= number_format((int) $row['maison'], 0, ',', ' ') ?> €</td>
                    <td><?= htmlspecialchars($row['evolution'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="muted">Données indicatives, mises à jour régulièrement. Pour une estimation personnalisée, utilisez notre simulateur.</p>
    </section>
</main>
</body>
</html>
