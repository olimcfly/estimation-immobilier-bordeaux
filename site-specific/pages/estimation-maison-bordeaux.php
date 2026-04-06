<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-maison-bordeaux',
    'title' => 'Estimation Maison à Bordeaux – Gratuite en 30 secondes | Skyline',
    'meta_description' => 'Estimez gratuitement votre maison à Bordeaux en 30 secondes. Outil basé sur les données DVF + IA, rapport détaillé offert.',
    'type_bien' => 'Maison',
    'ville' => 'Bordeaux',
    'zone_focus' => 'Talence, Pessac, Mérignac',
    'prix_min' => 390000,
    'prix_max' => 790000,
    'hero_alt' => 'Maison familiale à Bordeaux Métropole – estimation immobilière gratuite',
    'prix_quartiers' => [
        ['zone' => 'Talence', 'prix_m2' => 4650, 'evolution' => '+4%', 'projet' => 'Nouvelles pistes cyclables'],
        ['zone' => 'Pessac', 'prix_m2' => 4380, 'evolution' => '+3%', 'projet' => 'Réaménagement centre-ville'],
        ['zone' => 'Mérignac', 'prix_m2' => 4520, 'evolution' => '+5%', 'projet' => 'Extension tram A'],
    ],
    'criteres' => [
        'Surface du terrain et exposition du jardin',
        'État de la toiture et isolation énergétique',
        'Nombre de chambres / stationnement',
        'Distance des écoles et transports',
        'Maison avec jardin vs sans jardin',
    ],
    'testimonials' => [
        ['name' => 'Sophie R. — Talence', 'quote' => 'Notre maison a été estimée très juste, ce qui a accéléré la vente.'],
        ['name' => 'Marc L. — Mérignac', 'quote' => 'Le rapport m’a aidé à expliquer mon prix aux acheteurs.'],
    ],
    'faqs' => [
        ['question' => 'L’estimation prend-elle en compte la taille du jardin ?', 'answer' => 'Oui, le terrain et ses caractéristiques influencent directement la valeur estimée.'],
        ['question' => 'Puis-je estimer une maison à rénover ?', 'answer' => 'Oui, le modèle applique des ajustements selon l’état et les travaux nécessaires.'],
    ],
    'keywords' => ['estimation maison Bordeaux', 'prix maison Bordeaux 120m²', 'maison avec jardin Bordeaux prix'],
];

require __DIR__ . '/templates/type-bien-landing.php';
