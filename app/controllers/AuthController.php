<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\AdminUser;

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
        ]);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (!$this->verifyCsrfToken($csrfToken)) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'error_message' => 'Session expirée. Veuillez réessayer.',
                'old_email' => $email,
            ]);
            return;
        }

        if ($email === '' || $password === '') {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'error_message' => 'Veuillez remplir tous les champs.',
                'old_email' => $email,
            ]);
            return;
        }

        $user = AdminUser::findByEmail($email);

        if ($user === null || !AdminUser::verifyPassword($password, (string) $user['password_hash'])) {
            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'error_message' => 'Email ou mot de passe incorrect.',
                'old_email' => $email,
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_user_email'] = (string) $user['email'];
        $_SESSION['admin_user_name'] = (string) $user['name'];
        $_SESSION['admin_logged_in'] = true;

        header('Location: /admin/leads');
        exit;
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
