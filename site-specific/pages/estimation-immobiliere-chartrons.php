<?php

declare(strict_types=1);

$page = [
    'slug' => 'estimation-immobiliere-chartrons',
    'title' => 'Estimation Immobilière Chartrons – Prix au m² 2026 | Skyline',
    'meta_description' => 'Découvrez le prix au m² à Chartrons et estimez gratuitement votre bien avec les données DVF et l’IA Skyline.',
    'quartier' => 'Chartrons',
    'ville' => 'Bordeaux',
    'prix_min' => 260000,
    'prix_max' => 890000,
    'atouts' => [
        ['titre' => '🏙 Dynamisme', 'texte' => 'Quartier recherché, mix résidentiel et commerces premium.'],
        ['titre' => '🚇 Transports', 'texte' => 'Tram B, bus et accès rapide au centre-ville.'],
        ['titre' => '🎓 Éducation', 'texte' => 'Écoles, crèches et établissements supérieurs à proximité.'],
        ['titre' => '🛍 Loisirs', 'texte' => 'Quais, marché des Chartrons, restaurants et vie de quartier.'],
    ],
    'rows' => [
        ['type' => 'Appartement', 'prix' => '5 200 €/m²', 'evolution' => '+8%'],
        ['type' => 'Maison', 'prix' => '6 300 €/m²', 'evolution' => '+6%'],
        ['type' => 'Loft', 'prix' => '5 500 €/m²', 'evolution' => '+9%'],
    ],
    'projets' => ['Rénovation des quais', 'Mobilités douces', 'Réhabilitation patrimoine local'],
    'faqs' => [
        ['question' => 'Chartrons est-il adapté à l’investissement locatif ?', 'answer' => 'Oui, la demande locative y reste soutenue.'],
        ['question' => 'Quel type de bien se vend le plus vite ?', 'answer' => 'Les appartements rénovés bien situés partent rapidement.'],
    ],
];

require __DIR__ . '/templates/quartier-landing.php';
