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

    private function generateNewsletterToken(string $email): string
    {
        $payload = json_encode([
            'email' => $email,
            'exp' => time() + (60 * 60 * 24),
            'nonce' => bin2hex(random_bytes(8)),
        ], JSON_THROW_ON_ERROR);

        $encodedPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $encodedPayload, $this->newsletterSecret(), true);

        return $encodedPayload . '.' . $this->base64UrlEncode($signature);
    }

    private function validateNewsletterToken(string $token): ?string
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return null;
        }

        [$encodedPayload, $encodedSignature] = $parts;
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $this->newsletterSecret(), true);
        $providedSignature = $this->base64UrlDecode($encodedSignature);

        if ($providedSignature === '' || !hash_equals($expectedSignature, $providedSignature)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($encodedPayload);
        if ($payloadJson === '') {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }

        $email = isset($payload['email']) ? (string) $payload['email'] : '';
        $exp = isset($payload['exp']) ? (int) $payload['exp'] : 0;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $exp < time()) {
            return null;
        }

        return mb_strtolower($email);
    }

    private function buildNewsletterConfirmLink(string $token): string
    {
        $configuredBaseUrl = trim((string) Config::get('base_url', ''));
        $origin = $configuredBaseUrl !== ''
            ? rtrim($configuredBaseUrl, '/')
            : $this->requestOrigin();

        return $origin . '/newsletter/confirm?token=' . rawurlencode($token);
    }

    private function sendNewsletterConfirmationEmail(string $email, string $confirmLink): bool
    {
        $subject = 'Confirmez votre inscription à la newsletter';
        $message = "Bonjour,\n\n";
        $message .= "Merci pour votre inscription. Confirmez votre email via ce lien :\n";
        $message .= $confirmLink . "\n\n";
        $message .= "Ce lien expire dans 24 heures.\n\n";
        $message .= "Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce message.";

        $fromAddress = (string) Config::get('mail.from', 'no-reply@localhost');
        $headers = [
            'From: ' . $fromAddress,
            'Content-Type: text/plain; charset=UTF-8',
        ];

        return mail($email, $subject, $message, implode("\r\n", $headers));
    }

    private function newsletterSecret(): string
    {
        $configuredSecret = trim((string) Config::get('app_key', ''));
        if ($configuredSecret !== '') {
            return $configuredSecret;
        }

        return hash('sha256', (string) Config::get('app_name', 'estimateur-immobilier'));
    }

    private function requestOrigin(): string
    {
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $isHttps ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $normalized = strtr($value, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        return $decoded === false ? '' : $decoded;
    }
}
