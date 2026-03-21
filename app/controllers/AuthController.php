<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Models\AdminUser;
use App\Services\Mailer;

final class AuthController
{
    public function loginForm(): void
    {
        if ($this->isLoggedIn()) {
            header('Location: /admin');
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

        try {
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
        } catch (\Throwable $e) {
            error_log('Auth error: ' . $e->getMessage());
            http_response_code(500);

            $message = 'Erreur serveur. Veuillez réessayer plus tard.';
            if ($e instanceof \RuntimeException) {
                $message = 'Erreur serveur : impossible de se connecter à la base de données. Vérifiez la configuration.';
            } elseif ($e instanceof \PDOException) {
                $message = 'Erreur de base de données. Vérifiez que les tables sont correctement créées.';
            } elseif ($e instanceof \Error) {
                $message = 'Erreur serveur : une dépendance est manquante. Exécutez "composer install".';
            }

            View::renderBare('admin/login', [
                'page_title' => 'Connexion Admin - Estimation Immobilier Bordeaux',
                'step' => $action === 'verify_code' ? 'code' : 'email',
                'login_email' => $action === 'verify_code' ? trim((string) ($_POST['email'] ?? '')) : '',
                'error_message' => $message,
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

        header('Location: /admin');
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
        if (self::isDevSkipAuth()) {
            // Auto-login en mode développeur
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_email'] = 'dev@localhost';
            $_SESSION['admin_user_name'] = 'Dev Admin';
            return;
        }

        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        if (self::isDevSkipAuth()) {
            return true;
        }
        return !empty($_SESSION['admin_logged_in']);
    }

    /**
     * Vérifie si le mode développeur sans authentification est activé.
     */
    private static function isDevSkipAuth(): bool
    {
        $value = $_ENV['DEV_SKIP_AUTH'] ?? $_SERVER['DEV_SKIP_AUTH'] ?? 'false';
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
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

    public function diagnostic(): void
    {
        self::requireAuth();

        $data = [];
        $issues = [];

        // 1. Fichier .env
        $envFile = dirname(__DIR__, 2) . '/.env';
        $data['envExists'] = is_file($envFile);
        if (!$data['envExists']) {
            $issues[] = 'Fichier .env absent — copiez .env.example en .env';
        }

        // 2. Config DB
        $data['dbConfig'] = [
            'host' => Config::get('db.host', '(non défini)'),
            'port' => Config::get('db.port', '(non défini)'),
            'name' => Config::get('db.name', '(non défini)'),
            'user' => Config::get('db.user', '(non défini)'),
        ];
        $data['dbPassDefined'] = Config::get('db.pass', '') !== '';

        // 3. Connexion DB
        $data['dbConnected'] = false;
        $data['dbError'] = '';
        $data['dbVersion'] = '';
        $data['tables'] = [];
        $data['adminTableOk'] = false;
        $data['adminColumns'] = [];
        $data['loginCodeExists'] = false;
        $data['adminCount'] = 0;
        $data['adminEmails'] = [];

        try {
            $pdo = Database::connection();
            $data['dbConnected'] = true;
            $data['dbVersion'] = (string) $pdo->query('SELECT VERSION()')->fetchColumn();
        } catch (\Throwable $e) {
            $data['dbError'] = $e->getMessage();
            $issues[] = 'Connexion DB échouée : ' . $e->getMessage();
        }

        // 4. Tables
        if ($data['dbConnected']) {
            $data['tables'] = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($data['tables'])) {
                $issues[] = 'Aucune table — importez database/schema.sql';
            }

            // 5. admin_users
            if (in_array('admin_users', $data['tables'], true)) {
                $data['adminTableOk'] = true;
                $data['adminColumns'] = $pdo->query('SHOW COLUMNS FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                $data['loginCodeExists'] = in_array('login_code', $data['adminColumns'], true);
                if (!$data['loginCodeExists']) {
                    $issues[] = 'Colonne login_code manquante dans admin_users';
                }
                $data['adminCount'] = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
                if ($data['adminCount'] > 0) {
                    $data['adminEmails'] = $pdo->query('SELECT email FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                } else {
                    $issues[] = 'Aucun administrateur — exécutez setup-admin.php';
                }
            } else {
                $issues[] = 'Table admin_users absente — exécutez setup-admin.php';
            }
        }

        // 6. SMTP
        $data['smtpHost'] = (string) Config::get('mail.smtp_host');
        $data['smtpPort'] = (int) Config::get('mail.smtp_port', 587);
        $data['smtpUser'] = (string) Config::get('mail.smtp_user');
        $data['smtpPassDefined'] = (string) Config::get('mail.smtp_pass') !== '';
        $data['smtpEncryption'] = (string) Config::get('mail.smtp_encryption', 'tls');
        $data['smtpFrom'] = (string) Config::get('mail.from', '');
        $data['smtpConfigured'] = $data['smtpHost'] !== '';
        $data['smtpConnected'] = false;
        $data['smtpError'] = '';
        $data['smtpDiagnostics'] = [];
        $data['smtpAdvice'] = '';

        if ($data['smtpConfigured'] && class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $data['smtpHost'];
                $mail->Port = $data['smtpPort'];
                $mail->SMTPAuth = true;
                $mail->Username = (string) Config::get('mail.smtp_user');
                $mail->Password = (string) Config::get('mail.smtp_pass');
                $mail->Timeout = 10;
                $mail->SMTPDebug = 0;

                if ($data['smtpPort'] === 465) {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($data['smtpEncryption'] === 'tls' || $data['smtpPort'] === 587) {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = $data['smtpEncryption'];
                }
                $mail->AuthType = '';

                $mail->smtpConnect();
                $mail->smtpClose();
                $data['smtpConnected'] = true;
            } catch (\Throwable $e) {
                $data['smtpError'] = $e->getMessage();
                $data['smtpDiagnostics'] = Mailer::diagnose(['error_message' => $e->getMessage()]);

                if (str_contains($e->getMessage(), 'Could not authenticate')) {
                    $data['smtpAdvice'] = 'Identifiants incorrects. Si vous utilisez Gmail, créez un "mot de passe d\'application".';
                } elseif (str_contains($e->getMessage(), 'connect()') || str_contains($e->getMessage(), 'Connection')) {
                    $data['smtpAdvice'] = 'Impossible de se connecter au serveur SMTP. Vérifiez le host et le port (587 pour TLS, 465 pour SSL).';
                }

                $issues[] = 'Connexion SMTP échouée : ' . $e->getMessage();
            }
        } elseif (!$data['smtpConfigured']) {
            $issues[] = 'SMTP non configuré — définissez MAIL_SMTP_HOST dans .env';
        }

        $data['issues'] = $issues;

        View::renderAdmin('admin/diagnostic', array_merge($data, [
            'page_title' => 'Diagnostic — Admin',
            'admin_page' => 'diagnostic',
            'breadcrumb' => 'Diagnostic',
        ]));
    }

    public function testSmtp(): void
    {
        self::requireAuth();

        $overrides = Config::getSmtpOverrides();
        $hasOverrides = !empty($overrides);

        // If ?from_env=1, show raw .env values (ignoring overrides)
        $fromEnv = ($_GET['from_env'] ?? '') === '1';

        if ($fromEnv && $hasOverrides) {
            // Read raw env values directly
            $smtpHost = (string) ($_ENV['MAIL_SMTP_HOST'] ?? $_ENV['MAIL_HOST'] ?? '');
            $smtpPort = (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT'] ?? 587);
            $smtpUser = (string) ($_ENV['MAIL_SMTP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? '');
            $smtpPass = (string) ($_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '');
            $smtpEnc = (string) ($_ENV['MAIL_SMTP_ENCRYPTION'] ?? $_ENV['MAIL_ENCRYPTION'] ?? 'tls');
            $mailFrom = (string) ($_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? '');
            $mailFromName = (string) ($_ENV['MAIL_FROM_NAME'] ?? '');
        } else {
            $smtpHost = (string) Config::get('mail.smtp_host');
            $smtpPort = (int) Config::get('mail.smtp_port', 587);
            $smtpUser = (string) Config::get('mail.smtp_user');
            $smtpPass = (string) Config::get('mail.smtp_pass');
            $smtpEnc = (string) Config::get('mail.smtp_encryption', 'tls');
            $mailFrom = (string) Config::get('mail.from', '');
            $mailFromName = (string) Config::get('mail.from_name', '');
        }

        $flashSuccess = $_SESSION['smtp_flash_success'] ?? '';
        $flashError = $_SESSION['smtp_flash_error'] ?? '';
        unset($_SESSION['smtp_flash_success'], $_SESSION['smtp_flash_error']);

        View::renderAdmin('admin/test-smtp', [
            'admin_page'     => 'smtp',
            'admin_page_title' => 'Test SMTP',
            'page_title'     => 'Configuration SMTP',
            'breadcrumb'     => 'Configuration SMTP',
            'smtp_host'      => $smtpHost,
            'smtp_port'      => $smtpPort,
            'smtp_user'      => $smtpUser,
            'smtp_pass'      => $smtpPass,
            'smtp_enc'       => $smtpEnc,
            'mail_from'      => $mailFrom,
            'mail_from_name' => $mailFromName,
            'has_overrides'  => $hasOverrides,
            'flash_success'  => $flashSuccess,
            'flash_error'    => $flashError,
        ]);
    }

    public function testSmtpSave(): void
    {
        self::requireAuth();

        $data = [
            'smtp_host'       => trim((string) ($_POST['smtp_host'] ?? '')),
            'smtp_port'       => trim((string) ($_POST['smtp_port'] ?? '587')),
            'smtp_user'       => trim((string) ($_POST['smtp_user'] ?? '')),
            'smtp_pass'       => (string) ($_POST['smtp_pass'] ?? ''),
            'smtp_encryption' => trim((string) ($_POST['smtp_encryption'] ?? 'tls')),
            'from'            => trim((string) ($_POST['mail_from'] ?? '')),
            'from_name'       => trim((string) ($_POST['mail_from_name'] ?? '')),
        ];

        // Don't save if password field is the placeholder
        if ($data['smtp_pass'] === '********') {
            // Keep existing password
            $existing = Config::getSmtpOverrides();
            $data['smtp_pass'] = $existing['smtp_pass'] ?? (string) Config::get('mail.smtp_pass', '');
        }

        $saved = Config::saveSmtpOverrides($data);

        if ($saved) {
            $_SESSION['smtp_flash_success'] = 'Configuration SMTP sauvegardee avec succes.';
        } else {
            $_SESSION['smtp_flash_error'] = 'Erreur lors de la sauvegarde. Verifiez les permissions du dossier config/.';
        }

        header('Location: /admin/test-smtp');
        exit;
    }

    public function testSmtpReset(): void
    {
        self::requireAuth();

        $path = Config::getSmtpOverridePath();
        if (is_file($path)) {
            unlink($path);
        }

        $_SESSION['smtp_flash_success'] = 'Configuration reintialisee. Les valeurs du fichier .env sont utilisees.';
        header('Location: /admin/test-smtp');
        exit;
    }

    public function testSmtpRun(): void
    {
        self::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        // Use values from POST (form fields) for testing, not saved config
        $smtpHost = trim((string) ($_POST['smtp_host'] ?? (string) Config::get('mail.smtp_host')));
        $smtpPort = (int) ($_POST['smtp_port'] ?? (int) Config::get('mail.smtp_port', 587));
        $smtpUser = trim((string) ($_POST['smtp_user'] ?? (string) Config::get('mail.smtp_user')));
        $smtpPass = (string) ($_POST['smtp_pass'] ?? '');
        $smtpEnc = trim((string) ($_POST['smtp_encryption'] ?? (string) Config::get('mail.smtp_encryption', 'tls')));

        // If password is placeholder, use saved password
        if ($smtpPass === '********' || $smtpPass === '') {
            $overrides = Config::getSmtpOverrides();
            $smtpPass = $overrides['smtp_pass'] ?? (string) Config::get('mail.smtp_pass', '');
        }

        $steps = [];

        // Step 1: Config check
        $steps[] = [
            'label' => 'Configuration SMTP',
            'status' => $smtpHost !== '' ? 'ok' : 'error',
            'detail' => $smtpHost !== '' ? "Host: $smtpHost | Port: $smtpPort | Encryption: $smtpEnc" : 'Le champ Host SMTP est vide.',
        ];

        if ($smtpHost === '') {
            echo json_encode(['success' => false, 'steps' => $steps]);
            return;
        }

        // Step 2: PHPMailer check
        $phpmailerOk = class_exists(\PHPMailer\PHPMailer\PHPMailer::class);
        $steps[] = [
            'label' => 'PHPMailer',
            'status' => $phpmailerOk ? 'ok' : 'error',
            'detail' => $phpmailerOk ? 'Installe et disponible' : 'Absent - Executez "composer install"',
        ];

        if (!$phpmailerOk) {
            echo json_encode(['success' => false, 'steps' => $steps]);
            return;
        }

        // Step 3: SMTP connection
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->Port = $smtpPort;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->Timeout = 10;
            $mail->SMTPDebug = 0;

            if ($smtpPort === 465) {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtpEnc === 'tls' || $smtpPort === 587) {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = $smtpEnc;
            }
            $mail->AuthType = '';

            $mail->smtpConnect();
            $mail->smtpClose();

            $steps[] = [
                'label' => 'Connexion SMTP',
                'status' => 'ok',
                'detail' => 'Connexion reussie !',
            ];

            echo json_encode(['success' => true, 'steps' => $steps]);
        } catch (\Throwable $e) {
            $diagnostics = Mailer::diagnose(['error_message' => $e->getMessage()]);
            $advice = '';
            if (str_contains($e->getMessage(), 'Could not authenticate')) {
                $advice = 'Identifiants incorrects. Verifiez username/password.';
            } elseif (str_contains($e->getMessage(), 'connect()') || str_contains($e->getMessage(), 'Connection')) {
                $advice = 'Impossible de se connecter. Verifiez le host et le port (587 pour TLS, 465 pour SSL).';
            }

            $steps[] = [
                'label' => 'Connexion SMTP',
                'status' => 'error',
                'detail' => $e->getMessage(),
                'diagnostics' => $diagnostics,
                'advice' => $advice,
            ];

            echo json_encode(['success' => false, 'steps' => $steps]);
        }
    }

    public function testSmtpSendEmail(): void
    {
        self::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $to = trim($_POST['to'] ?? '');
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Adresse email invalide.']);
            return;
        }

        $subject = 'Test SMTP - Estimation Immobilier Bordeaux';
        $body = '<h2>Test SMTP</h2><p>Ce message confirme que la configuration SMTP fonctionne correctement.</p><p><small>Envoye depuis l\'interface d\'administration.</small></p>';

        $result = Mailer::send($to, $subject, $body);
        echo json_encode(['success' => $result, 'error' => $result ? '' : 'Echec de l\'envoi. Consultez les logs pour plus de details.']);
    }
}
