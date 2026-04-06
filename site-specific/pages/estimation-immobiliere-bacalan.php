<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-bacalan',
    'title' => 'Estimation Immobilière Bacalan – Prix au m² 2026 | Skyline',
    'meta_description' => 'Analyse des prix immobiliers à Bacalan et estimation gratuite de votre bien en 30 secondes.',
    'quartier' => 'Bacalan',
    'ville' => 'Bordeaux',
    'prix_min' => 195000,
    'prix_max' => 640000,
    'atouts' => [
        ['titre' => '🏙 Renouveau urbain', 'texte' => 'Quartier en mutation autour des Bassins à Flot.'],
        ['titre' => '🚇 Mobilité', 'texte' => 'Tram B et axes vers le centre bordelais.'],
        ['titre' => '🎓 Attractivité', 'texte' => 'Nouveaux programmes et profils jeunes actifs.'],
        ['titre' => '🛍 Loisirs', 'texte' => 'Cité du Vin, quais et nouvelles adresses.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '4 300 €/m²', 'evolution' => '+6%'],
        ['type' => 'Maison', 'prix' => '4 950 €/m²', 'evolution' => '+5%'],
        ['type' => 'Loft', 'prix' => '5 100 €/m²', 'evolution' => '+7%'],
    ],
    'projets' => ['Développement Bassins à Flot', 'Nouveaux programmes résidentiels', 'Amélioration espaces publics'],
    'faqs' => [
        ['question' => 'Bacalan est-il un quartier en gentrification ?', 'answer' => 'Oui, avec une montée progressive de la demande et des prix.'],
        ['question' => 'Quel potentiel de plus-value sur 5 ans ?', 'answer' => 'Le potentiel dépend du micro-secteur et des projets à proximité.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
