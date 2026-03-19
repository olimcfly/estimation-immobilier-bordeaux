<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Validator;
use App\Core\View;
use App\Models\NewsletterSubscriber;

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

    public function processusEstimation(): void
    {
        View::render('pages/processus_estimation', [
            'page_title' => 'Processus d\'Estimation - Estimation Immobilier Bordeaux',
        ]);
    }

    public function newsletter(): void
    {
        View::render('pages/newsletter', [
            'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
        ]);
    }

    public function guides(): void
    {
        View::render('pages/guides', [
            'page_title' => 'Guides Immobiliers Bordeaux - Conseils & Astuces',
        ]);
    }


    public function exemplesEstimation(): void
    {
        View::render('pages/exemples_estimation', [
            'page_title' => "Exemple Estimation - Cas Réels Bordeaux | Nos Résultats",
        ]);
    }


    public function quartiers(): void
    {
        View::render('pages/quartiers', [
            'page_title' => 'Quartiers de Bordeaux - Estimation Immobilier Bordeaux',
        ]);
    }

    public function contact(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact - Estimation Immobilier Bordeaux',
        ]);
    }


    public function podcast(): void
    {
        View::render('pages/podcast', [
            'page_title' => 'Podcast Immobilier Bordeaux - Conseils & Tendances',
        ]);
    }

    public function newsletterSubscribe(): void
    {
        $hasConsent = isset($_POST['newsletter_rgpd']) && $_POST['newsletter_rgpd'] === 'on';

        try {
            $email = mb_strtolower(Validator::email($_POST, 'newsletter_email'));
        } catch (\InvalidArgumentException) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
                'error_message' => 'Adresse email invalide. Merci de vérifier votre saisie.',
            ]);
            return;
        }

        if (!$hasConsent) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
                'error_message' => 'Le consentement RGPD est requis pour finaliser votre inscription.',
            ]);
            return;
        }

        $token = $this->generateNewsletterToken($email);
        $confirmLink = $this->buildNewsletterConfirmLink($token);

        if (!$this->sendNewsletterConfirmationEmail($email, $confirmLink)) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
                'error_message' => 'Impossible d\'envoyer l\'email de confirmation pour le moment. Réessayez dans quelques minutes.',
            ]);
            return;
        }

        View::render('pages/newsletter', [
            'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
            'success_message' => 'Un email de confirmation vient d\'être envoyé. Cliquez sur le lien reçu pour activer votre abonnement.',
        ]);
    }

    public function newsletterConfirm(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '') {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
                'error_message' => 'Lien de confirmation invalide.',
            ]);
            return;
        }

        $email = $this->validateNewsletterToken($token);
        if ($email === null) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
                'error_message' => 'Le lien de confirmation est invalide ou expiré.',
            ]);
            return;
        }

        $subscriberModel = new NewsletterSubscriber();
        $subscriberModel->confirmByEmail($email);

        View::render('pages/newsletter', [
            'page_title' => 'Newsletter - Estimation Immobilier Bordeaux',
            'success_message' => 'Inscription confirmée ✅ Vous recevrez désormais notre newsletter.',
        ]);
    }

    public function contactSubmit(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact - Estimation Immobilier Bordeaux',
            'success_message' => 'Merci ! Votre message a bien été reçu. Nous vous répondrons sous 24h.',
        ]);
    }


    public function mentionsLegales(): void
    {
        View::render('legal/mentions', [
            'page_title' => 'Mentions légales - Estimation Immobilier Bordeaux',
        ]);
    }

    public function politiqueConfidentialite(): void
    {
        View::render('legal/confidentialite', [
            'page_title' => 'Politique de confidentialité - Estimation Immobilier Bordeaux',
        ]);
    }

    public function conditionsUtilisation(): void
    {
        View::render('legal/cgu', [
            'page_title' => 'Conditions d\'utilisation - Estimation Immobilier Bordeaux',
        ]);
    }

    public function rgpd(): void
    {
        View::render('legal/rgpd', [
            'page_title' => 'RGPD - Estimation Immobilier Bordeaux',
        ]);
    }

}
