<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-merignac',
    'title' => 'Estimation Immobilière Mérignac – Prix au m² 2026 | Skyline',
    'meta_description' => 'Consultez les prix immobiliers à Mérignac en 2026 et estimez gratuitement votre maison ou appartement.',
    'quartier' => 'Mérignac',
    'ville' => 'Mérignac',
    'prix_min' => 220000,
    'prix_max' => 760000,
    'atouts' => [
        ['titre' => '🏙 Emploi', 'texte' => 'Pôles économiques majeurs à proximité.'],
        ['titre' => '🚇 Mobilité', 'texte' => 'Tram, rocade et accès rapide à Bordeaux.'],
        ['titre' => '🎓 Familles', 'texte' => 'Écoles et infrastructures sportives attractives.'],
        ['titre' => '🛍 Cadre de vie', 'texte' => 'Quartiers résidentiels et commerces de proximité.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '4 200 €/m²', 'evolution' => '+4%'],
        ['type' => 'Maison', 'prix' => '4 520 €/m²', 'evolution' => '+5%'],
        ['type' => 'Terrain', 'prix' => '640 €/m²', 'evolution' => '+6%'],
    ],
    'projets' => ['Extension tram A', 'Requalification zones mixtes', 'Nouvelles infrastructures scolaires'],
    'faqs' => [
        ['question' => 'Pourquoi les familles choisissent Mérignac ?', 'answer' => 'Pour le compromis prix/surface et l’accessibilité.'],
        ['question' => 'Les terrains sont-ils encore accessibles ?', 'answer' => 'Ils sont plus rares, mais toujours recherchés.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
