<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-saint-michel',
    'title' => 'Estimation Immobilière Saint-Michel – Prix au m² 2026 | Skyline',
    'meta_description' => 'Prix au m² à Saint-Michel et estimation gratuite de votre bien avec analyse DVF et IA locale.',
    'quartier' => 'Saint-Michel',
    'ville' => 'Bordeaux',
    'prix_min' => 180000,
    'prix_max' => 620000,
    'atouts' => [
        ['titre' => '🏙 Potentiel de plus-value', 'texte' => 'Quartier en transformation avec fort potentiel.'],
        ['titre' => '🚇 Connexions', 'texte' => 'Accès rapide centre-ville et gare.'],
        ['titre' => '🎓 Vie de quartier', 'texte' => 'Commerces, écoles et tissu associatif dynamique.'],
        ['titre' => '🛍 Attractivité', 'texte' => 'Marché Saint-Michel, restaurants et ambiance cosmopolite.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '4 100 €/m²', 'evolution' => '+5%'],
        ['type' => 'Maison', 'prix' => '5 000 €/m²', 'evolution' => '+4%'],
        ['type' => 'Loft', 'prix' => '4 600 €/m²', 'evolution' => '+7%'],
    ],
    'projets' => ['Renouvellement urbain', 'Amélioration espaces publics', 'Nouveaux commerces de proximité'],
    'faqs' => [
        ['question' => 'Saint-Michel est-il en gentrification ?', 'answer' => 'Le quartier attire de nouveaux profils, ce qui soutient les prix.'],
        ['question' => 'Peut-on estimer un bien à rénover ?', 'answer' => 'Oui, la décote travaux est intégrée dans la fourchette.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
