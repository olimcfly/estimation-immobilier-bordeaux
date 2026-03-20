<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\AdminUser;
use App\Services\Mailer;

final class AuthController
{
    public function loginForm(): void
    {
        if ($this->isLoggedIn()) {
            header('Location: /admin/leads');
            exit;
        }

        View::renderBare('admin/login', [
            'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
            'step' => 'email',
        ]);
    }

    public function login(): void
    {
        $action = (string) ($_POST['action'] ?? '');
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (!$this->verifyCsrfToken($csrfToken)) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Session expirée. Veuillez réessayer.',
            ]);
            return;
        }

        if ($action === 'send_code') {
            $this->handleSendCode();
        } elseif ($action === 'verify_code') {
            $this->handleVerifyCode();
        } else {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Requête invalide.',
            ]);
        }
    }

    private function handleSendCode(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($email === '') {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Veuillez saisir votre adresse email.',
            ]);
            return;
        }

        $user = AdminUser::findByEmail($email);

        if ($user === null) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Aucun compte administrateur associé à cet email.',
            ]);
            return;
        }

        $code = AdminUser::generateCode();
        AdminUser::storeLoginCode($email, $code);

        $sent = Mailer::send(
            $email,
            'Votre code de connexion - Estimation Immobilier Bordeaux',
            $this->buildCodeEmail($code, (string) ($user['name'] ?? 'Administrateur'))
        );

        if (!$sent) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Impossible d\'envoyer l\'email. Vérifiez la configuration SMTP.',
            ]);
            return;
        }

        View::renderBare('admin/login', [
            'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
            'step' => 'code',
            'login_email' => $email,
            'success_message' => 'Un code de connexion a été envoyé à votre adresse email.',
        ]);
    }

    private function handleVerifyCode(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $code = trim((string) ($_POST['code'] ?? ''));

        if ($email === '' || $code === '') {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'email',
                'error_message' => 'Veuillez remplir tous les champs.',
            ]);
            return;
        }

        if (!AdminUser::verifyLoginCode($email, $code)) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => 'code',
                'login_email' => $email,
                'error_message' => 'Code invalide ou expiré. Veuillez réessayer.',
            ]);
            return;
        }

        AdminUser::clearLoginCode($email);

        $user = AdminUser::findByEmail($email);

        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_user_email'] = (string) $user['email'];
        $_SESSION['admin_user_name'] = (string) $user['name'];
        $_SESSION['admin_logged_in'] = true;

        header('Location: /admin/leads');
        exit;
    }

    private function buildCodeEmail(string $code, string $name): string
    {
        return <<<HTML
        <div style="font-family: 'Helvetica Neue', Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 2rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-block; width: 50px; height: 50px; background: linear-gradient(135deg, #8B1538, #C41E3A); border-radius: 12px; line-height: 50px; color: #fff; font-size: 1.4rem;">&#128274;</div>
            </div>
            <h2 style="text-align: center; color: #1a1410; margin-bottom: 0.5rem;">Votre code de connexion</h2>
            <p style="text-align: center; color: #6b6459; margin-bottom: 2rem;">Bonjour {$name}, voici votre code pour accéder à l'espace administrateur :</p>
            <div style="background: #f8f5f2; border: 2px solid #e8dfd7; border-radius: 12px; padding: 1.5rem; text-align: center; margin-bottom: 2rem;">
                <span style="font-size: 2.2rem; font-weight: 700; letter-spacing: 0.5rem; color: #8B1538;">{$code}</span>
            </div>
            <p style="text-align: center; color: #6b6459; font-size: 0.85rem;">Ce code est valable <strong>10 minutes</strong>.<br>Si vous n'avez pas demandé ce code, ignorez cet email.</p>
            <hr style="border: none; border-top: 1px solid #e8dfd7; margin: 2rem 0;">
            <p style="text-align: center; color: #999; font-size: 0.8rem;">Estimation Immobilier Bordeaux</p>
        </div>
        HTML;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: /admin/login');
        exit;
    }

    public static function requireAuth(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['admin_logged_in']);
    }

    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken(string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if ($sessionToken === '' || $token === '') {
            return false;
        }
        $valid = hash_equals($sessionToken, $token);
        unset($_SESSION['csrf_token']);
        return $valid;
    }
}
