<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class PageController
{
    public function home(): void
    {
        View::render('pages/home', [
            'page_title' => 'Accueil - Estimation Immobilier Bordeaux',
        ]);
    }

    public function services(): void
    {
        View::render('pages/services', [
            'page_title' => 'Nos Services - Estimation Immobilier Bordeaux',
        ]);
    }

    public function about(): void
    {
        View::render('pages/a_propos', [
            'page_title' => 'À Propos - Estimation Immobilier Bordeaux',
        ]);
    }

    public function aPropos(): void
    {
        $this->about();
    }


    public function exemplesEstimation(): void
    {
        View::render('pages/exemples_estimation', [
            'page_title' => "Exemple Estimation - Cas Réels Bordeaux | Nos Résultats",
        ]);
    }


    public function processusEstimation(): void
    {
        View::render('pages/processus_estimation', [
            'page_title' => "Processus d'Estimation - Estimation Immobilier Bordeaux",
        ]);
    }

    public function contact(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact - Estimation Immobilier Bordeaux',
        ]);
    }

    public function faq(): void
    {
        View::render('pages/faq', [
            'page_title' => 'FAQ - Estimation Immobilier Bordeaux',
        ]);
    }

    public function contactSubmit(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact - Estimation Immobilier Bordeaux',
            'success_message' => 'Merci ! Votre message a bien été reçu. Nous vous répondrons sous 24h.',
        ]);
    }
}
