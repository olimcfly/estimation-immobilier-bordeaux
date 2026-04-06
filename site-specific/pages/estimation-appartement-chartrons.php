<?php

declare(strict_types=1);

$quartier = 'Chartrons';
$ville = 'Bordeaux';
$prixMin = 285000;
$prixMax = 465000;

$prixParQuartier = [
    ['quartier' => 'Chartrons', 'prix_m2' => 5200, 'evolution' => '+8%', 'projet' => 'Rénovation des quais'],
    ['quartier' => 'Saint-Pierre', 'prix_m2' => 5450, 'evolution' => '+5%', 'projet' => 'Piétonnisation centre ancien'],
    ['quartier' => 'Mériadeck', 'prix_m2' => 4600, 'evolution' => '+3%', 'projet' => 'Modernisation pôle tertiaire'],
    ['quartier' => 'Saint-Michel', 'prix_m2' => 4100, 'evolution' => '+5%', 'projet' => 'Opérations de renouvellement urbain'],
    ['quartier' => 'Bacalan', 'prix_m2' => 4300, 'evolution' => '+6%', 'projet' => 'Développement Bassins à Flot'],
];

$criteresPrix = [
    'Étage et présence d’ascenseur',
    'Vue (quais, cour, axe passant)',
    'État général et rénovation énergétique',
    'Proximité tram, écoles et commerces',
];

$faqs = [
    [
        'question' => 'Pourquoi mon estimation est-elle gratuite ?',
        'answer' => 'L’estimation en ligne est gratuite pour vous permettre d’obtenir rapidement une fourchette fiable. Le service est financé par nos offres d’accompagnement immobilier complémentaires.',
    ],
    [
        'question' => 'Comment votre algorithme calcule-t-il la valeur de mon appartement ?',
        'answer' => 'Notre modèle combine les ventes DVF récentes, les tendances par micro-secteur, les caractéristiques de votre bien (surface, état, étage) et les signaux du marché local pour produire une estimation contextualisée.',
    ],
    [
        'question' => 'Puis-je faire estimer un bien atypique ?',
        'answer' => 'Oui. Vous pouvez estimer un loft, un duplex, un bien avec terrasse, ou un appartement à rénover. Le rapport détaillé précise la marge d’ajustement selon les spécificités du bien.',
    ],
];

$faqEntities = array_map(
    static fn (array $faq): array => [
        '@type' => 'Question',
        'name' => $faq['question'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['answer'],
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
    'description' => 'Plateforme d’estimation immobilière à Bordeaux basée sur les données DVF et l’IA.',
    'areaServed' => ['Bordeaux', 'Mérignac', 'Pessac', 'Talence'],
    'telephone' => '+33 5 00 00 00 00',
    'url' => 'https://www.example.com/estimation-appartement-chartrons.php',
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estimation Appartement à Bordeaux Chartrons – Gratuite en 30 secondes | Skyline</title>
    <meta name="description" content="Estimez gratuitement votre appartement à Bordeaux Chartrons en 30 secondes. Outil DVF + IA, rapport détaillé inclus, précision jusqu’à 95%.">
    <link rel="canonical" href="https://www.example.com/estimation-appartement-chartrons.php">
    <meta property="og:title" content="Estimation Appartement Chartrons | Skyline Bordeaux">
    <meta property="og:description" content="Découvrez la valeur de votre appartement à Chartrons en 30 secondes grâce aux données DVF et à l’IA.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.example.com/estimation-appartement-chartrons.php">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json"><?= json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
    <script type="application/ld+json"><?= json_encode($schemaLocalBusiness, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
    <style>
        :root {
            color-scheme: light;
            --brand: #1d4ed8;
            --brand-dark: #1e3a8a;
            --accent: #f97316;
            --bg: #f8fafc;
            --text: #0f172a;
            --muted: #475569;
            --card: #ffffff;
            --border: #e2e8f0;
            --ok: #047857;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        .container { max-width: 1080px; margin: 0 auto; padding: 24px; }
        .hero {
            border-radius: 16px;
            background: linear-gradient(130deg, var(--brand-dark), var(--brand));
            color: #fff;
            padding: 32px;
            margin-bottom: 20px;
        }
        .hero p { color: #dbeafe; }
        .buttons { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
        .btn {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-secondary { background: #ffffff22; color: #fff; border: 1px solid #ffffff55; }
        section.card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 16px;
        }
        h1, h2, h3 { margin-top: 0; }
        .usp-grid, .steps, .testimonials {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .item {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }
        .ok { color: var(--ok); font-weight: 700; }
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.95rem;
        }
        .pricing-table th, .pricing-table td {
            border-bottom: 1px solid var(--border);
            text-align: left;
            padding: 10px;
        }
        .pricing-table th { background: #f1f5f9; }
        .faq details {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 8px;
            background: #fff;
        }
        .muted { color: var(--muted); }
        .map-note {
            margin-top: 10px;
            border-left: 4px solid var(--brand);
            background: #eff6ff;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--brand-dark);
        }
        ul { margin-bottom: 0; }
        @media (max-width: 760px) {
            .container { padding: 14px; }
            .hero { padding: 24px; }
            .usp-grid, .steps, .testimonials { grid-template-columns: 1fr; }
            .buttons .btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
<main class="container">
    <section class="hero">
        <p>✨ Estimation gratuite en 30 secondes</p>
        <h1>Estimation Appartement à Bordeaux – Gratuite en 30 secondes</h1>
        <h2>Votre appartement à <?= htmlspecialchars($quartier, ENT_QUOTES, 'UTF-8'); ?> vaut entre <?= number_format($prixMin, 0, ',', ' '); ?> € et <?= number_format($prixMax, 0, ',', ' '); ?> €.</h2>
        <p>Découvrez sa valeur exacte grâce à notre modèle basé sur les ventes DVF et l’analyse IA de plus de 12 000 transactions locales.</p>
        <div class="buttons">
            <a href="/#estimation-form" class="btn btn-primary">Obtenir mon estimation précise →</a>
            <a href="#fiabilite" class="btn btn-secondary">Pourquoi choisir notre estimation ?</a>
        </div>
    </section>

    <section class="card" id="fiabilite">
        <h2>Pourquoi notre estimation est-elle fiable ?</h2>
        <div class="usp-grid">
            <article class="item"><p class="ok">✅ Données officielles DVF</p><p>Transactions réelles analysées sur les 12 derniers mois à Bordeaux Métropole.</p></article>
            <article class="item"><p class="ok">✅ Algorithme IA locale</p><p>Segmentation par micro-quartier pour mieux refléter le prix réel au m².</p></article>
            <article class="item"><p class="ok">✅ Précision jusqu’à 95%</p><p>Jusqu’à 2x plus précis qu’une estimation généraliste non contextualisée.</p></article>
            <article class="item"><p class="ok">✅ Rapport détaillé gratuit</p><p>Recommandations de prix, tension du marché et comparables récents.</p></article>
        </div>
    </section>

    <section class="card">
        <h2>Prix moyen des appartements à Bordeaux</h2>
        <p class="muted">Comparatif des secteurs les plus demandés pour les propriétaires vendeurs.</p>
        <table class="pricing-table" aria-label="Prix au m² appartements Bordeaux">
            <thead>
            <tr>
                <th>Quartier</th>
                <th>Prix moyen au m² (2026)</th>
                <th>Évolution 1 an</th>
                <th>Projet structurant</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($prixParQuartier as $ligne): ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['quartier'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= number_format((int) $ligne['prix_m2'], 0, ',', ' '); ?> €</td>
                    <td><?= htmlspecialchars($ligne['evolution'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($ligne['projet'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="map-note">Carte interactive recommandée (Google Maps / Mapbox) : afficher les prix par quartier, la variation annuelle et les zones impactées par les projets urbains.</p>
    </section>

    <section class="card">
        <h2>Comment estimer votre appartement en 3 étapes</h2>
        <div class="steps">
            <article class="item"><h3>1. Sélectionnez le type de bien</h3><p>Choisissez “Appartement” puis précisez votre secteur exact.</p></article>
            <article class="item"><h3>2. Indiquez surface et caractéristiques</h3><p>Surface, étage, balcon, état général, exposition et annexes.</p></article>
            <article class="item"><h3>3. Recevez votre estimation par email</h3><p>Obtenez la fourchette de valeur et le rapport détaillé sous 2 minutes.</p></article>
            <article class="item"><h3>+ Bonus recommandation vente</h3><p>Conseils personnalisés pour positionner votre prix de mise en vente.</p></article>
        </div>
    </section>

    <section class="card">
        <h2>Les critères qui font varier le prix d’un appartement</h2>
        <ul>
            <?php foreach ($criteresPrix as $critere): ?>
                <li><?= htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card">
        <h2>Témoignages clients</h2>
        <div class="testimonials">
            <article class="item">
                <h3>Jean D. — Chartrons</h3>
                <p>“L’estimation Skyline était à moins de 2% du prix final signé. J’ai gagné du temps sur la mise en vente.”</p>
            </article>
            <article class="item">
                <h3>Claire M. — Saint-Pierre</h3>
                <p>“Le rapport détaillé m’a aidée à justifier mon prix auprès des acheteurs et à vendre plus vite.”</p>
            </article>
        </div>
    </section>

    <section class="card faq">
        <h2>Questions fréquentes</h2>
        <?php foreach ($faqs as $faq): ?>
            <details>
                <summary><strong><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?></strong></summary>
                <p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
            </details>
        <?php endforeach; ?>
    </section>

    <section class="card">
        <h2>Prêt à connaître la valeur exacte de votre appartement ?</h2>
        <p>Ne vendez pas à l’aveugle : obtenez une estimation locale fiable avant de fixer votre prix.</p>
        <div class="buttons">
            <a href="/#estimation-form" class="btn btn-primary">Obtenir mon estimation gratuite →</a>
            <a href="tel:+33500000000" class="btn" style="border:1px solid var(--border); color:var(--text);">Besoin d’aide ? 05 00 00 00 00</a>
        </div>
    </section>

    <section class="card">
        <h2>Suggestion Ads & SEO pour cette page</h2>
        <p class="muted">Mots-clés cibles : “estimation appartement Bordeaux Chartrons”, “prix appartement Chartrons m²”, “valeur appartement Bordeaux centre”.</p>
        <p class="muted">Alt text image suggéré : “Appartement typique des Chartrons à Bordeaux – Estimation immobilière gratuite”.</p>
    </section>
</main>
</body>
</html>
