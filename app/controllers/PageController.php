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
        View::render('pages/about', [
            'page_title' => 'À Propos - Estimation Immobilier Bordeaux',
        ]);
    }

    public function aPropos(): void
    {
        $this->about();
    }


    public function processusEstimation(): void
    {
        View::render('pages/processus_estimation', [
            'page_title' => 'Processus Estimation - Notre Méthodologie',
        ]);
    }

    public function contact(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact - Estimation Immobilier Bordeaux',
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
