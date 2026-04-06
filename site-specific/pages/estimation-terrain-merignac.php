<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-terrain-merignac',
    'title' => 'Estimation Terrain Constructible à Mérignac – Gratuite | Skyline',
    'meta_description' => 'Estimez gratuitement votre terrain constructible à Mérignac. Analyse DVF + IA et potentiel de construction inclus.',
    'type_bien' => 'Terrain constructible',
    'ville' => 'Mérignac',
    'zone_focus' => 'Mérignac et Bordeaux Nord',
    'prix_min' => 170000,
    'prix_max' => 420000,
    'hero_alt' => 'Terrain constructible à Mérignac – estimation immobilière gratuite',
    'prix_quartiers' => [
        ['zone' => 'Mérignac Centre', 'prix_m2' => 640, 'evolution' => '+6%', 'projet' => 'Densification urbaine'],
        ['zone' => 'Arlac', 'prix_m2' => 710, 'evolution' => '+5%', 'projet' => 'Amélioration desserte tram'],
        ['zone' => 'Bordeaux Nord', 'prix_m2' => 590, 'evolution' => '+7%', 'projet' => 'Nouvelles ZAC'],
    ],
    'criteres' => [
        'Surface cadastrale et forme de parcelle',
        'Emprise au sol et règles PLU',
        'Viabilisation (eau, électricité, fibre)',
        'Accès voirie et orientation',
        'Potentiel de construction estimé (m² plancher)',
    ],
    'testimonials' => [
        ['name' => 'Anne D. — Mérignac', 'quote' => 'Le calcul de potentiel m’a aidée à mieux valoriser mon terrain.'],
        ['name' => 'Paul T. — Bordeaux Nord', 'quote' => 'J’ai obtenu une fourchette crédible pour négocier avec un promoteur.'],
    ],
    'faqs' => [
        ['question' => 'Le PLU est-il pris en compte ?', 'answer' => 'Oui, nos ajustements incluent les contraintes de constructibilité connues.'],
        ['question' => 'Puis-je estimer un terrain non viabilisé ?', 'answer' => 'Oui, la viabilisation impacte la valeur finale et est intégrée.'],
    ],
    'keywords' => ['estimation terrain constructible Mérignac', 'terrain Mérignac prix m²', 'valeur terrain Bordeaux Nord'],
];

require __DIR__ . '/templates/type-bien-landing.php';
