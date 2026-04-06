<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-pessac',
    'title' => 'Estimation Immobilière Pessac – Prix au m² 2026 | Skyline',
    'meta_description' => 'Obtenez une estimation immobilière gratuite à Pessac et consultez les prix au m² par type de bien.',
    'quartier' => 'Pessac',
    'ville' => 'Pessac',
    'prix_min' => 210000,
    'prix_max' => 680000,
    'atouts' => [
        ['titre' => '🏙 Cadre résidentiel', 'texte' => 'Quartiers calmes recherchés par les familles.'],
        ['titre' => '🚇 Accessibilité', 'texte' => 'Tram, TER et axes rapides vers Bordeaux.'],
        ['titre' => '🎓 Éducation', 'texte' => 'Écoles et proximité universités.'],
        ['titre' => '🛍 Vie pratique', 'texte' => 'Commerces, services de santé et équipements.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '4 180 €/m²', 'evolution' => '+3%'],
        ['type' => 'Maison', 'prix' => '4 380 €/m²', 'evolution' => '+4%'],
        ['type' => 'Terrain', 'prix' => '560 €/m²', 'evolution' => '+5%'],
    ],
    'projets' => ['Requalification centre Pessac', 'Nouveaux aménagements mobilité', 'Rénovation équipements publics'],
    'faqs' => [
        ['question' => 'Pourquoi les familles quittent Bordeaux pour Pessac ?', 'answer' => 'Surface plus grande pour un budget équivalent, avec un cadre plus résidentiel.'],
        ['question' => 'Quel type de bien est le plus demandé ?', 'answer' => 'Les maisons avec jardin et les appartements proches tram.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
