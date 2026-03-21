<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Services\Mailer;

final class AdminDiagnosticController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $diagnostics = [];

        // 1. Fichier .env
        $envFile = dirname(__DIR__, 2) . '/.env';
        $diagnostics['env'] = [
            'label' => 'Fichier .env',
            'status' => is_file($envFile) ? 'ok' : 'error',
            'message' => is_file($envFile) ? 'Fichier .env pr&eacute;sent' : 'Fichier .env absent &mdash; Copiez .env.example en .env',
        ];

        // 2. Configuration DB
        $dbConfig = [
            'host' => Config::get('db.host', '(non d&eacute;fini)'),
            'port' => Config::get('db.port', '(non d&eacute;fini)'),
            'name' => Config::get('db.name', '(non d&eacute;fini)'),
            'user' => Config::get('db.user', '(non d&eacute;fini)'),
            'pass' => Config::get('db.pass', '') !== '' ? '(d&eacute;fini)' : '(VIDE)',
        ];

        $dbConfigOk = Config::get('db.name', '') !== '' && Config::get('db.host', '') !== '';
        $diagnostics['db_config'] = [
            'label' => 'Configuration base de donn&eacute;es',
            'status' => $dbConfigOk ? 'ok' : 'warning',
            'details' => $dbConfig,
        ];

        // 3. Connexion DB
        $dbConnected = false;
        $pdo = null;
        try {
            $pdo = Database::connection();
            $dbConnected = true;
            $diagnostics['db_connection'] = [
                'label' => 'Connexion base de donn&eacute;es',
                'status' => 'ok',
                'message' => 'Connexion MySQL r&eacute;ussie',
            ];
        } catch (\Throwable $e) {
            $diagnostics['db_connection'] = [
                'label' => 'Connexion base de donn&eacute;es',
                'status' => 'error',
                'message' => 'Connexion &eacute;chou&eacute;e : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
            ];
        }

        // 4. Tables
        $tables = [];
        $tableCount = 0;
        if ($dbConnected && $pdo !== null) {
            try {
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
                $tableCount = count($tables);
                $diagnostics['tables'] = [
                    'label' => 'Tables de la base',
                    'status' => $tableCount > 0 ? 'ok' : 'warning',
                    'message' => $tableCount . ' table(s) trouv&eacute;e(s)',
                    'list' => $tables,
                ];
            } catch (\Throwable $e) {
                $diagnostics['tables'] = [
                    'label' => 'Tables de la base',
                    'status' => 'error',
                    'message' => 'Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
                ];
            }
        }

        // 5. Table admin_users
        if ($dbConnected && $pdo !== null) {
            if (in_array('admin_users', $tables, true)) {
                try {
                    $columns = $pdo->query('SHOW COLUMNS FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                    $count = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
                    $admins = $pdo->query('SELECT email, name FROM admin_users')->fetchAll(\PDO::FETCH_ASSOC);
                    $hasLoginCode = in_array('login_code', $columns, true);

                    $diagnostics['admin_users'] = [
                        'label' => 'Table admin_users',
                        'status' => ($count > 0 && $hasLoginCode) ? 'ok' : 'warning',
                        'columns' => $columns,
                        'admin_count' => $count,
                        'admins' => $admins,
                        'has_login_code' => $hasLoginCode,
                    ];
                } catch (\Throwable $e) {
                    $diagnostics['admin_users'] = [
                        'label' => 'Table admin_users',
                        'status' => 'error',
                        'message' => 'Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
                    ];
                }
            } else {
                $diagnostics['admin_users'] = [
                    'label' => 'Table admin_users',
                    'status' => 'error',
                    'message' => 'Table absente &mdash; Ex&eacute;cutez setup-admin.php',
                ];
            }
        }

        // 6. Configuration SMTP
        $smtpHost = (string) Config::get('mail.smtp_host');
        $smtpPort = (int) Config::get('mail.smtp_port', 587);
        $smtpUser = (string) Config::get('mail.smtp_user');
        $smtpPass = (string) Config::get('mail.smtp_pass');
        $smtpEnc = (string) Config::get('mail.smtp_encryption', 'tls');
        $mailFrom = (string) Config::get('mail.from', '');
        $mailFromName = (string) Config::get('mail.from_name', '');

        $smtpConfigured = $smtpHost !== '' && $smtpUser !== '' && $smtpPass !== '';
        $diagnostics['smtp_config'] = [
            'label' => 'Configuration SMTP',
            'status' => $smtpConfigured ? 'ok' : 'warning',
            'details' => [
                'host' => $smtpHost !== '' ? $smtpHost : '(VIDE)',
                'port' => (string) $smtpPort,
                'user' => $smtpUser !== '' ? $smtpUser : '(VIDE)',
                'pass' => $smtpPass !== '' ? '(d&eacute;fini)' : '(VIDE)',
                'encryption' => $smtpEnc,
                'from' => $mailFrom !== '' ? $mailFrom : '(VIDE)',
                'from_name' => $mailFromName !== '' ? $mailFromName : '(VIDE)',
            ],
        ];

        // 7. Test connexion SMTP
        if ($smtpConfigured) {
            try {
                if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                    $diagnostics['smtp_test'] = [
                        'label' => 'Test SMTP',
                        'status' => 'error',
                        'message' => 'PHPMailer non install&eacute; &mdash; Ex&eacute;cutez "composer install"',
                    ];
                } else {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
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

                    $diagnostics['smtp_test'] = [
                        'label' => 'Test SMTP',
                        'status' => 'ok',
                        'message' => 'Connexion SMTP r&eacute;ussie',
                    ];
                }
            } catch (\Throwable $e) {
                $smtpError = $e->getMessage();
                $smtpDiag = Mailer::diagnose(['error_message' => $smtpError]);
                $diagnostics['smtp_test'] = [
                    'label' => 'Test SMTP',
                    'status' => 'error',
                    'message' => 'Connexion &eacute;chou&eacute;e : ' . htmlspecialchars($smtpError, ENT_QUOTES, 'UTF-8'),
                    'suggestions' => $smtpDiag,
                ];
            }
        } else {
            $diagnostics['smtp_test'] = [
                'label' => 'Test SMTP',
                'status' => 'warning',
                'message' => 'SMTP non configur&eacute; &mdash; Renseignez les variables MAIL_* dans .env',
            ];
        }

        // 8. PHP Info
        $diagnostics['php'] = [
            'label' => 'Environnement PHP',
            'status' => 'ok',
            'details' => [
                'version' => PHP_VERSION,
                'extensions' => implode(', ', array_filter(['pdo_mysql', 'mbstring', 'openssl', 'curl', 'json'], 'extension_loaded')),
                'missing' => implode(', ', array_filter(['pdo_mysql', 'mbstring', 'openssl', 'curl', 'json'], fn($ext) => !extension_loaded($ext))),
            ],
        ];

        $missingExt = array_filter(['pdo_mysql', 'mbstring', 'openssl', 'curl', 'json'], fn($ext) => !extension_loaded($ext));
        if (!empty($missingExt)) {
            $diagnostics['php']['status'] = 'warning';
        }

        // Overall status
        $statuses = array_column($diagnostics, 'status');
        $errorCount = count(array_filter($statuses, fn($s) => $s === 'error'));
        $warningCount = count(array_filter($statuses, fn($s) => $s === 'warning'));
        $okCount = count(array_filter($statuses, fn($s) => $s === 'ok'));

        View::renderAdmin('admin/diagnostic', [
            'page_title' => 'Diagnostic - Admin',
            'admin_page' => 'diagnostic',
            'breadcrumb' => 'Diagnostic',
            'diagnostics' => $diagnostics,
            'errorCount' => $errorCount,
            'warningCount' => $warningCount,
            'okCount' => $okCount,
            'totalChecks' => count($diagnostics),
        ]);
    }
}
