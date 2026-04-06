<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-talence',
    'title' => 'Estimation Immobilière Talence – Prix au m² 2026 | Skyline',
    'meta_description' => 'Prix au m² à Talence, estimation gratuite et comparaison avec les transactions récentes du secteur.',
    'quartier' => 'Talence',
    'ville' => 'Talence',
    'prix_min' => 230000,
    'prix_max' => 690000,
    'atouts' => [
        ['titre' => '🏙 Universités', 'texte' => 'Forte attractivité grâce au campus universitaire.'],
        ['titre' => '🚇 Transports', 'texte' => 'Tram B et liaisons directes vers Bordeaux.'],
        ['titre' => '🎓 Population active', 'texte' => 'Mix étudiants, familles et cadres.'],
        ['titre' => '🛍 Services', 'texte' => 'Commerces, santé, écoles et équipements.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '4 450 €/m²', 'evolution' => '+4%'],
        ['type' => 'Maison', 'prix' => '4 650 €/m²', 'evolution' => '+3%'],
        ['type' => 'Terrain', 'prix' => '590 €/m²', 'evolution' => '+5%'],
    ],
    'projets' => ['Amélioration axe universitaire', 'Requalification espaces publics', 'Modernisation équipements'],
    'faqs' => [
        ['question' => 'Talence est-elle adaptée à un investissement locatif ?', 'answer' => 'Oui, la demande étudiante maintient une tension locative élevée.'],
        ['question' => 'Les maisons familiales se vendent-elles rapidement ?', 'answer' => 'Oui, surtout proches des écoles et transports.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
