<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-loft-bordeaux',
    'title' => 'Estimation Loft à Bordeaux – Gratuite en 30 secondes | Skyline',
    'meta_description' => 'Estimez gratuitement votre loft à Bordeaux. Données DVF + IA locale pour une valeur précise et un rapport complet en 2 minutes.',
    'type_bien' => 'Loft',
    'ville' => 'Bordeaux',
    'zone_focus' => 'Bacalan, Darwin, Bassins à Flot',
    'prix_min' => 310000,
    'prix_max' => 680000,
    'hero_alt' => 'Loft rénové à Bordeaux – estimation immobilière gratuite',
    'prix_quartiers' => [
        ['zone' => 'Bacalan', 'prix_m2' => 4950, 'evolution' => '+7%', 'projet' => 'Développement Bassins à Flot'],
        ['zone' => 'Darwin Bastide', 'prix_m2' => 5120, 'evolution' => '+6%', 'projet' => 'Nouveaux espaces mixtes'],
        ['zone' => 'Chartrons', 'prix_m2' => 5480, 'evolution' => '+5%', 'projet' => 'Valorisation des quais'],
    ],
    'criteres' => [
        'Hauteur sous plafond et luminosité',
        'État de la rénovation et matériaux',
        'Surface exploitable réellement habitable',
        'Destination du bien (habitation/atelier)',
        'Loft vs appartement : vitesse de vente selon secteur',
    ],
    'testimonials' => [
        ['name' => 'Julien M. — Bacalan', 'quote' => 'Estimation réaliste malgré un bien atypique, excellente base de négociation.'],
        ['name' => 'Nora P. — Bastide', 'quote' => 'Très utile pour calibrer la mise en vente de mon loft atelier.'],
    ],
    'faqs' => [
        ['question' => 'Un loft est-il estimé différemment d’un appartement classique ?', 'answer' => 'Oui, nous appliquons des critères spécifiques aux biens atypiques.'],
        ['question' => 'Puis-je inclure une mezzanine ou un atelier ?', 'answer' => 'Oui, indiquez ces éléments pour ajuster la valeur estimée.'],
    ],
    'keywords' => ['valeur loft Bordeaux centre', 'estimation loft Bacalan', 'prix loft Bassins à Flot'],
];

require __DIR__ . '/templates/type-bien-landing.php';
