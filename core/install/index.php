<?php

declare(strict_types=1);

session_start();

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';
$configFile = $configDir . '/config.php';
$installHtaccess = __DIR__ . '/.htaccess';
$uploadDir = $rootDir . '/assets';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = (int) ($_POST['step'] ?? 1);

    if ($step >= 1 && $step <= 5) {
        $_SESSION['install_wizard'] = array_merge(
            $_SESSION['install_wizard'] ?? [],
            [
                'agence_nom' => trim((string) ($_POST['agence_nom'] ?? ($_SESSION['install_wizard']['agence_nom'] ?? ''))),
                'ville_principale' => trim((string) ($_POST['ville_principale'] ?? ($_SESSION['install_wizard']['ville_principale'] ?? ''))),
                'couleur' => trim((string) ($_POST['couleur'] ?? ($_SESSION['install_wizard']['couleur'] ?? '#1e3a5f'))),
                'email_reception' => trim((string) ($_POST['email_reception'] ?? ($_SESSION['install_wizard']['email_reception'] ?? ''))),
                'smtp_host' => trim((string) ($_POST['smtp_host'] ?? ($_SESSION['install_wizard']['smtp_host'] ?? ''))),
                'smtp_port' => (int) ($_POST['smtp_port'] ?? ($_SESSION['install_wizard']['smtp_port'] ?? 587)),
                'smtp_user' => trim((string) ($_POST['smtp_user'] ?? ($_SESSION['install_wizard']['smtp_user'] ?? ''))),
                'smtp_pass' => (string) ($_POST['smtp_pass'] ?? ($_SESSION['install_wizard']['smtp_pass'] ?? '')),
                'email_expediteur' => trim((string) ($_POST['email_expediteur'] ?? ($_SESSION['install_wizard']['email_expediteur'] ?? ''))),
                'h1_titre' => trim((string) ($_POST['h1_titre'] ?? ($_SESSION['install_wizard']['h1_titre'] ?? ''))),
                'sous_titre' => trim((string) ($_POST['sous_titre'] ?? ($_SESSION['install_wizard']['sous_titre'] ?? ''))),
                'meta_description' => trim((string) ($_POST['meta_description'] ?? ($_SESSION['install_wizard']['meta_description'] ?? ''))),
                'api_key_openai' => trim((string) ($_POST['api_key_openai'] ?? ($_SESSION['install_wizard']['api_key_openai'] ?? ''))),
                'api_key_perplexity' => trim((string) ($_POST['api_key_perplexity'] ?? ($_SESSION['install_wizard']['api_key_perplexity'] ?? ''))),
                'api_key_claude' => trim((string) ($_POST['api_key_claude'] ?? ($_SESSION['install_wizard']['api_key_claude'] ?? ''))),
                'api_key_dvf' => trim((string) ($_POST['api_key_dvf'] ?? ($_SESSION['install_wizard']['api_key_dvf'] ?? ''))),
                'api_key_mamouth' => trim((string) ($_POST['api_key_mamouth'] ?? ($_SESSION['install_wizard']['api_key_mamouth'] ?? ''))),
                'api_keys_activate_now' => isset($_POST['api_keys_activate_now'])
                    ? true
                    : (bool) ($_SESSION['install_wizard']['api_keys_activate_now'] ?? false),
            ]
        );

        if ($step === 1 && isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                die('Impossible de créer le dossier assets/.');
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string) $finfo->file($_FILES['logo']['tmp_name']);
            $allowed = [
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];

            if (isset($allowed[$mime])) {
                $extension = $allowed[$mime];
                $target = $uploadDir . '/logo.' . $extension;
                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                    die('Impossible d\'enregistrer le logo uploadé.');
                }
                $_SESSION['install_wizard']['logo'] = 'assets/logo.' . $extension;
            }
        }

        if ($step === 2) {
            $rawCities = $_POST['villes'] ?? [];
            $cities = [];
            if (is_array($rawCities)) {
                foreach ($rawCities as $city) {
                    $city = trim((string) $city);
                    if ($city !== '') {
                        $cities[] = $city;
                    }
                }
            }
            $_SESSION['install_wizard']['villes'] = array_values(array_unique($cities));
        }

function tableExists(PDO $pdo, string $tableName): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
    );
    $stmt->execute(['table_name' => $tableName]);
    return (int) $stmt->fetchColumn() > 0;
}

function applySqlFileIfTableMissing(PDO $pdo, string $tableName, string $sqlPath): void
{
    if (tableExists($pdo, $tableName)) {
        return;
    }
    if (!is_file($sqlPath)) {
        throw new RuntimeException(basename($sqlPath) . ' introuvable.');
    }
    $sql = trim((string) file_get_contents($sqlPath));
    if ($sql === '') {
        throw new RuntimeException(basename($sqlPath) . ' est vide.');
    }
    $pdo->exec($sql);
}

    if ($step === 6) {
        $wizard = $_SESSION['install_wizard'] ?? [];

        $agenceNom = trim((string) ($wizard['agence_nom'] ?? ''));
        $villePrincipale = trim((string) ($wizard['ville_principale'] ?? ''));
        $villes = $wizard['villes'] ?? [];

        if ($agenceNom === '' || $villePrincipale === '' || empty($villes)) {
            header('Location: index.php?step=1&error=missing_data');
            exit;
        }

        if (!is_dir($configDir) && !mkdir($configDir, 0755, true) && !is_dir($configDir)) {
            die('Impossible de créer le dossier config/.');
        }

        $config = [
            'installed' => true,
            'agence_nom' => $agenceNom,
            'ville_principale' => $villePrincipale,
            'logo' => (string) ($wizard['logo'] ?? ''),
            'couleur' => preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($wizard['couleur'] ?? '')) ? $wizard['couleur'] : '#1e3a5f',
            'email_reception' => (string) ($wizard['email_reception'] ?? ''),
            'smtp_host' => (string) ($wizard['smtp_host'] ?? ''),
            'smtp_port' => (int) ($wizard['smtp_port'] ?? 587),
            'smtp_user' => (string) ($wizard['smtp_user'] ?? ''),
            'smtp_pass' => (string) ($wizard['smtp_pass'] ?? ''),
            'email_expediteur' => (string) ($wizard['email_expediteur'] ?? ''),
            'h1_titre' => (string) ($wizard['h1_titre'] ?: ('Combien vaut votre bien à ' . $villePrincipale . ' ?')),
            'sous_titre' => (string) ($wizard['sous_titre'] ?: 'Obtenez une estimation instantanée basée sur les données du marché local.'),
            'meta_description' => (string) ($wizard['meta_description'] ?: ('Estimation gratuite à ' . $villePrincipale)),
            'villes' => array_values($villes),
            'api_key_openai' => (string) ($wizard['api_key_openai'] ?? ''),
            'api_key_perplexity' => (string) ($wizard['api_key_perplexity'] ?? ''),
            'api_key_claude' => (string) ($wizard['api_key_claude'] ?? ''),
            'api_key_dvf' => (string) ($wizard['api_key_dvf'] ?? ''),
            'api_key_mamouth' => (string) ($wizard['api_key_mamouth'] ?? ''),
            'api_keys_activate_now' => (bool) ($wizard['api_keys_activate_now'] ?? false),
        ];



$alreadyInstalled = is_file($configFile);
$step = (int) ($_GET['step'] ?? 1);
if ($step < 1 || $step > 5) $step = 1;
$error = '';
$installCompleted = false;

        unset($_SESSION['install_wizard']);

    if ($step === 3) {
        $_SESSION['install_site'] = [
            'site_name'            => trim((string) ($_POST['site_name'] ?? 'EstimIA')),
            'city_name'            => trim((string) ($_POST['city_name'] ?? 'Bordeaux')),
            'operation_radius_km'  => max(1, (int) ($_POST['operation_radius_km'] ?? 30)),
            'admin_email'          => trim((string) ($_POST['admin_email'] ?? '')),
            'site_phone'           => trim((string) ($_POST['site_phone'] ?? '')),
            'admin_password'       => (string) ($_POST['admin_password'] ?? ''),
            'base_url'             => trim((string) ($_POST['base_url'] ?? '')),
            'smtp_host'            => trim((string) ($_POST['smtp_host'] ?? '')),
            'smtp_port'            => (int) ($_POST['smtp_port'] ?? 587),
            'smtp_user'            => trim((string) ($_POST['smtp_user'] ?? '')),
            'smtp_pass'            => (string) ($_POST['smtp_pass'] ?? ''),
            'ai_openai_key'        => trim((string) ($_POST['ai_openai_key'] ?? '')),
            'ai_anthropic_key'     => trim((string) ($_POST['ai_anthropic_key'] ?? '')),
            'ai_perplexity_key'    => trim((string) ($_POST['ai_perplexity_key'] ?? '')),
            'ai_mistral_key'       => trim((string) ($_POST['ai_mistral_key'] ?? '')),
            'operation_cities_json'=> (string) ($_POST['operation_cities_json'] ?? '[]'),
        ];
        header('Location: ?step=4');
        exit;
    }
}

    if ($step === 4) {
        $site = $_SESSION['install_site'] ?? [];
        if (!is_array($site)) {
            $site = [];
        }
        $site['ai_openai_key'] = trim((string) ($_POST['ai_openai_key'] ?? ($site['ai_openai_key'] ?? '')));
        $site['ai_anthropic_key'] = trim((string) ($_POST['ai_anthropic_key'] ?? ($site['ai_anthropic_key'] ?? '')));
        $site['ai_perplexity_key'] = trim((string) ($_POST['ai_perplexity_key'] ?? ($site['ai_perplexity_key'] ?? '')));
        $site['ai_mistral_key'] = trim((string) ($_POST['ai_mistral_key'] ?? ($site['ai_mistral_key'] ?? '')));
        $_SESSION['install_site'] = $site;
        header('Location: ?step=5');
        exit;
    }

    if ($step === 5) {
        $db   = $_SESSION['install_db'] ?? null;
        $site = $_SESSION['install_site'] ?? null;

        if (!is_array($db) || !is_array($site)) {
            $error = 'Les étapes 2 et 3 doivent être complétées avant la finalisation.';
        } elseif (($site['admin_email'] ?? '') === '' || ($site['admin_password'] ?? '') === '') {
            $error = 'Email admin et mot de passe admin requis.';
        } else {
            try {
                if (!is_dir($configDir) && !mkdir($configDir, 0755, true) && !is_dir($configDir)) {
                    throw new RuntimeException('Impossible de créer le dossier config/.');
                }

                $secret    = bin2hex(random_bytes(32));
                $e         = fn(string $v): string => addslashes($v);
                $siteName  = $e((string) $site['site_name']);
                $cityName  = $e((string) $site['city_name']);
                $sitePhone = $e((string) $site['site_phone']);
                $adminEmail = $e((string) $site['admin_email']);
                $baseUrl   = $e((string) $site['base_url']);
                $radius    = max(1, (int) ($site['operation_radius_km'] ?? 30));
                $smtpHost  = $e((string) ($site['smtp_host'] ?? ''));
                $smtpPort  = max(1, (int) ($site['smtp_port'] ?? 587));
                $smtpUser  = $e((string) ($site['smtp_user'] ?? ''));
                $smtpPass  = $e((string) ($site['smtp_pass'] ?? ''));
                $aiOpenAi = $e((string) ($site['ai_openai_key'] ?? ''));
                $aiAnthropic = $e((string) ($site['ai_anthropic_key'] ?? ''));
                $aiPerplexity = $e((string) ($site['ai_perplexity_key'] ?? ''));
                $aiMistral = $e((string) ($site['ai_mistral_key'] ?? ''));
                $operationCitiesJson = $e((string) ($site['operation_cities_json'] ?? '[]'));
                $dbHost    = $e((string) $db['host']);
                $dbName    = $e((string) $db['db_name']);
                $dbUser    = $e((string) $db['db_user']);
                $dbPass    = $e((string) $db['db_pass']);

                $configContent = <<<PHP
<?php
// Configuration EstimIA - Bordeaux
define('DEBUG_MODE', false);
define('MAINTENANCE_MODE', false);
define('SITE_NAME', '{$siteName}');
define('CITY_NAME', '{$cityName}');
define('OPERATION_RADIUS_KM', {$radius});
define('SITE_PHONE', '{$sitePhone}');

// Base de données
define('DB_HOST', '{$dbHost}');
define('DB_NAME', '{$dbName}');
define('DB_USER', '{$dbUser}');
define('DB_PASS', '{$dbPass}');

// Email
define('SMTP_HOST', '{$smtpHost}');
define('SMTP_USER', '{$smtpUser}');
define('SMTP_PASS', '{$smtpPass}');
define('SMTP_PORT', {$smtpPort});
define('SMTP_FROM', '{$smtpUser}');
define('MAIL_FROM', SMTP_FROM);
define('MAIL_FROM_NAME', 'EstimIA Bordeaux');
define('OPERATION_CITIES_JSON', '{$operationCitiesJson}');

// IA — Multi-provider fallback
define('AI_OPENAI_KEY', '{$aiOpenAi}');
define('AI_ANTHROPIC_KEY', '{$aiAnthropic}');
define('AI_PERPLEXITY_KEY', '{$aiPerplexity}');
define('AI_MISTRAL_KEY', '{$aiMistral}');

// Sécurité
define('ADMIN_EMAIL', '{$adminEmail}');
define('SECRET_KEY', '{$secret}');

// Chemins
define('BASE_URL', '{$baseUrl}');
define('BASE_PATH', __DIR__ . '/..');

require_once BASE_PATH . '/includes/error-handler.php';
PHP;

                file_put_contents($configFile, $configContent);

                $databaseContent = <<<PHP
<?php
return [
    'host'    => '{$dbHost}',
    'dbname'  => '{$dbName}',
    'user'    => '{$dbUser}',
    'pass'    => '{$dbPass}',
    'charset' => 'utf8mb4',
];
PHP;

                file_put_contents($databaseFile, $databaseContent);

                $pdo = new PDO(
                    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['db_name']),
                    $db['db_user'], $db['db_pass'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                applySqlFileIfTableMissing($pdo, 'leads', $createLeadsSqlPath);

                $nameParts     = preg_split('/\s+/', trim((string) $site['site_name'])) ?: [];
                $defaultPrenom = isset($nameParts[0]) && $nameParts[0] !== '' ? $nameParts[0] : 'Admin';
                $defaultNom    = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'EstimIA';

                $adminStmt = $pdo->prepare(
                    'INSERT INTO admins (prenom, nom, email) VALUES (:prenom, :nom, :email)
                     ON DUPLICATE KEY UPDATE prenom = VALUES(prenom), nom = VALUES(nom)'
                );
                $adminStmt->execute([
                    'prenom' => $defaultPrenom,
                    'nom'    => $defaultNom,
                    'email'  => (string) $site['admin_email'],
                ]);

                $emailHtml = installRenderEmailTemplate($rootDir, 'install-success', [
                    'prenom'   => $defaultPrenom,
                    'nom'      => $defaultNom,
                    'siteName' => (string) $site['site_name'],
                    'cityName' => (string) $site['city_name'],
                    'baseUrl'  => (string) $site['base_url'],
                ]);

                installSendEmail(
                    (string) $site['admin_email'],
                    'Installation terminée - Accès administration',
                    $emailHtml,
                    (string) $site['site_name']
                );

                $installCompleted = true;
                unset($_SESSION['install_db'], $_SESSION['install_site']);
            } catch (Throwable $ex) {
                $error = $ex->getMessage();
            }
        }
    }
}

$step = max(1, min(6, (int) ($_GET['step'] ?? 1)));
$data = $_SESSION['install_wizard'] ?? [];

$dbSession   = $_SESSION['install_db'] ?? ['host' => 'localhost', 'db_name' => '', 'db_user' => '', 'db_pass' => ''];
$siteSession = $_SESSION['install_site'] ?? [
    'site_name' => 'EstimIA', 'city_name' => 'Bordeaux', 'operation_radius_km' => 30,
    'admin_email' => '', 'site_phone' => '', 'admin_password' => '', 'base_url' => '',
    'smtp_host' => '', 'smtp_port' => 587, 'smtp_user' => '', 'smtp_pass' => '',
    'ai_openai_key' => '', 'ai_anthropic_key' => '', 'ai_perplexity_key' => '', 'ai_mistral_key' => '',
    'operation_cities_json' => '[]',
];

$tableDescriptions = [
    'estimations'             => 'Stocke toutes les demandes d\'estimation des visiteurs.',
    'users'                   => 'Comptes administrateurs/agents pour accéder au back-office.',
    'settings'                => 'Paramètres du site (nom, ville, téléphone, couleurs…).',
    'villes_prix'             => 'Prix au m² par ville/quartier pour le calcul d\'estimation.',
    'lead_activities'         => 'Historique des notes et actions sur les leads.',
    'rate_limits'             => 'Protection anti-abus et limitation de requêtes.',
    'login_attempts'          => 'Suivi des tentatives de connexion à l\'admin.',
    'email_logs'              => 'Historique des emails envoyés (estimation, relance…).',
    'webhook_logs'            => 'Journal des appels webhooks sortants.',
    'sessions'                => 'Sessions admin actives.',
    'admin_users'             => 'Comptes d\'administration avancée supplémentaires.',
    'ads_checklist_progress'  => 'Progression de la checklist Google Ads.',
    'google_ads_drafts'       => 'Brouillons d\'annonces Google Ads.',
    'admins'                  => 'Table principale des comptes administrateurs.',
];

$expectedTables  = extractInstallTables($installSqlPath);
$tableChecklist  = [];
if (is_array($_SESSION['install_db'] ?? null)) {
    $tableChecklist = getTablesChecklist($_SESSION['install_db'], $expectedTables);
}

$allReqOk = !in_array(false, $requirements, true);
$stepLabels = ['Pré-requis', 'Base de données', 'Configuration', 'Clés IA', 'Finalisation'];

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation — EstimIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #030712;
            --bg-card: #0a0f1e;
            --bg-card-hover: #111827;
            --bg-input: #111827;
            --border: rgba(255,255,255,.06);
            --border-focus: rgba(99,102,241,.5);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --accent-glow: rgba(99,102,241,.15);
            --success: #10b981;
            --success-glow: rgba(16,185,129,.15);
            --warning: #f59e0b;
            --warning-glow: rgba(245,158,11,.1);
            --danger: #ef4444;
            --danger-glow: rgba(239,68,68,.1);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 6px;
            --shadow-card: 0 0 0 1px var(--border), 0 24px 48px -12px rgba(0,0,0,.5);
            --transition: .2s cubic-bezier(.4,0,.2,1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Fond subtil ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 0%, rgba(99,102,241,.08), transparent),
                radial-gradient(ellipse 40% 40% at 80% 100%, rgba(16,185,129,.05), transparent);
            pointer-events: none;
            z-index: 0;
        }

        .installer {
            position: relative;
            z-index: 1;
            max-width: 720px;
            margin: 0 auto;
            padding: 60px 24px 80px;
        }

        /* ── Header ── */
        .installer-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .installer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -.02em;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .installer-logo .dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--accent);
        }

        .installer-subtitle {
            color: var(--text-muted);
            font-size: .85rem;
            font-weight: 500;
        }

        /* ── Stepper ── */
        .stepper {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 40px;
        }

        .stepper-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 100px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: 1px solid var(--border);
            transition: var(--transition);
            text-decoration: none;
            cursor: default;
        }

        .stepper-item.active {
            background: var(--accent-glow);
            border-color: rgba(99,102,241,.3);
            color: var(--accent-light);
        }

        .stepper-item.done {
            color: var(--success);
            border-color: rgba(16,185,129,.2);
            background: var(--success-glow);
        }

        .stepper-num {
            width: 22px; height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: .7rem;
            font-weight: 700;
            background: var(--border);
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .stepper-item.active .stepper-num {
            background: var(--accent);
            color: #fff;
        }

        .stepper-item.done .stepper-num {
            background: var(--success);
            color: #fff;
        }

        /* ── Card ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            padding: 40px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: 6px;
        }

        .card-desc {
            color: var(--text-muted);
            font-size: .85rem;
            margin-bottom: 32px;
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: .9rem;
            font-family: inherit;
            transition: var(--transition);
            outline: none;
        }

        .form-input::placeholder { color: var(--text-muted); }

        .form-input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .card { padding: 24px; }
        }

        .form-separator {
            border: none;
            border-top: 1px solid var(--border);
            margin: 32px 0;
        }

        .form-section-title {
            font-size: .9rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-section-desc {
            font-size: .8rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            font-size: .85rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            line-height: 1;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .btn-primary:hover {
            background: var(--accent-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 24px rgba(99,102,241,.3);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-outline-test {
            background: transparent;
            color: var(--accent-light);
            border: 1px solid rgba(99,102,241,.3);
        }

        .btn-outline-test:hover {
            background: var(--accent-glow);
        }

        .btn-row {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
        }

        /* ── Checklist ── */
        .checklist { list-style: none; }

        .checklist-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            font-size: .88rem;
        }

        .checklist-item:last-child { border-bottom: none; }

        .check-icon {
            width: 28px; height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: .75rem;
            flex-shrink: 0;
        }

        .check-ok {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16,185,129,.2);
        }

        .check-fail {
            background: var(--danger-glow);
            color: var(--danger);
            border: 1px solid rgba(239,68,68,.2);
        }

        .checklist-label { font-weight: 500; }
        .checklist-desc { color: var(--text-muted); font-size: .78rem; margin-top: 2px; }

        /* ── Alerts ── */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            font-size: .85rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .alert-success {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16,185,129,.15);
        }

        .alert-danger {
            background: var(--danger-glow);
            color: var(--danger);
            border: 1px solid rgba(239,68,68,.15);
        }

        .alert-warning {
            background: var(--warning-glow);
            color: var(--warning);
            border: 1px solid rgba(245,158,11,.15);
        }

        .alert-info {
            background: var(--accent-glow);
            color: var(--accent-light);
            border: 1px solid rgba(99,102,241,.15);
        }

        /* ── Success banner ── */
        .success-banner {
            text-align: center;
            padding: 48px 32px;
        }

        .success-icon {
            width: 64px; height: 64px;
            margin: 0 auto 20px;
            background: var(--success-glow);
            border: 2px solid rgba(16,185,129,.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .success-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .success-desc {
            color: var(--text-muted);
            font-size: .9rem;
            margin-bottom: 28px;
        }

        .success-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        /* ── Table checklist compacte ── */
        .table-grid {
            display: grid;
            gap: 8px;
            margin-top: 20px;
        }

        .table-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(255,255,255,.02);
            border-radius: var(--radius-xs);
            border: 1px solid var(--border);
        }

        .table-row .status {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .table-name {
            font-size: .82rem;
            font-weight: 600;
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        .table-desc-text {
            font-size: .75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── Recap ── */
        .recap-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .recap-item {
            padding: 16px;
            background: rgba(255,255,255,.02);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }

        .recap-label {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .recap-value {
            font-size: .9rem;
            font-weight: 600;
        }

        /* ── Spinner ── */
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: none;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Anim ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-in { animation: fadeUp .4s ease-out both; }

        .cities-box {
            position: relative;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.015);
            border-radius: var(--radius-sm);
            padding: 16px;
            margin-bottom: 16px;
        }

        .cities-list { display: grid; gap: 8px; margin: 12px 0; }
        .city-line { display: flex; align-items: center; gap: 8px; font-size: .86rem; }
        .source-badge {
            position: absolute;
            right: 12px;
            bottom: 10px;
            font-size: .72rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="mx-auto max-w-3xl p-4 md:p-8">
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold">Installation de votre site d'estimation</h1>
        <p class="mt-2 text-sm text-slate-600">Étape <?= $step; ?>/6</p>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_data'): ?>
            <p class="mt-4 rounded-lg bg-red-100 px-3 py-2 text-sm text-red-700">Merci de compléter toutes les données obligatoires avant de générer le site.</p>
        <?php endif; ?>

<div class="installer">

    <!-- Header -->
    <div class="installer-header animate-in">
        <div class="installer-logo">
            <span class="dot"></span>
            EstimIA
        </div>
        <div class="installer-subtitle">Assistant d'installation</div>
    </div>

    <!-- Stepper -->
    <div class="stepper animate-in" style="animation-delay:.05s">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="stepper-item <?= $i === $step ? 'active' : ($i < $step ? 'done' : '') ?>">
                <span class="stepper-num"><?= $i < $step ? '✓' : $i ?></span>
                <span><?= $stepLabels[$i - 1] ?></span>
            </div>
        <?php endfor; ?>
    </div>

    <?php if ($alreadyInstalled && !$installCompleted): ?>
        <div class="alert alert-warning">Installation déjà effectuée — <code>config/config.php</code> existe.</div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- ════════ SUCCESS ════════ -->
    <?php if (!empty($installCompleted)): ?>
        <div class="card animate-in">
            <div class="success-banner">
                <div class="success-icon">✓</div>
                <div class="success-title">Installation terminée</div>
                <div class="success-desc">Les fichiers de configuration ont été générés et le compte administrateur créé avec succès.</div>
                <div class="success-actions">
                    <a class="btn btn-success" href="../">Accéder au site</a>
                    <a class="btn btn-ghost" href="../admin/">Administration</a>
                </div>
            </div>

            <?php if ($tableChecklist !== []): ?>
                <hr class="form-separator">
                <div class="form-section-title">Tables créées</div>
                <div class="table-grid">
                    <?php foreach ($expectedTables as $table): ?>
                        <?php $ok = (bool) ($tableChecklist[$table] ?? false); ?>
                        <div class="table-row">
                            <span class="status"><?= $ok ? '✅' : '❌' ?></span>
                            <div>
                                <div class="table-name"><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="table-desc-text"><?= htmlspecialchars($tableDescriptions[$table] ?? 'Table système.', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-city" class="mt-3 rounded-lg border px-3 py-2 text-sm">+ Ajouter une ville</button>
                <div class="mt-5 flex gap-2">
                    <a href="?step=1" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
                </div>
            </form>
            <div id="dbResult" style="margin-top:16px"></div>
        </div>

    <!-- ════════ STEP 3 ════════ -->
    <?php elseif (!$alreadyInstalled && $step === 3): ?>
        <div class="card animate-in" style="animation-delay:.1s">
            <div class="card-title">Configuration du site</div>
            <div class="card-desc">Informations générales et paramètres d'accès.</div>

            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom du site</label>
                        <input class="form-input" name="site_name" value="<?= htmlspecialchars((string) $siteSession['site_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ville cible</label>
                        <input class="form-input" id="city_name" name="city_name" value="<?= htmlspecialchars((string) $siteSession['city_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rayon d'opération (km)</label>
                        <input class="form-input" type="number" id="operation_radius_km" name="operation_radius_km" value="<?= (int) $siteSession['operation_radius_km'] ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input class="form-input" name="site_phone" value="<?= htmlspecialchars((string) $siteSession['site_phone'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="cities-box">
                    <div class="form-section-title">Étape 2 — Villes voisines assistées par IA</div>
                    <div class="form-section-desc">Sélectionnez les villes à couvrir puis ajoutez-en manuellement si besoin.</div>
                    <input type="hidden" id="operation_cities_json" name="operation_cities_json" value="<?= htmlspecialchars((string) $siteSession['operation_cities_json'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="button" class="btn btn-outline-test" id="loadCitiesBtn">
                        <span class="spinner" id="citiesSpinner"></span>
                        Rechercher les villes voisines
                    </button>
                    <div id="citiesStatus" style="margin-top:10px"></div>
                    <div id="citiesList" class="cities-list"></div>
                    <div class="form-row" style="margin-top:8px">
                        <div class="form-group" style="margin-bottom:0">
                            <input class="form-input" id="manualCityInput" placeholder="Ajouter une ville manuellement">
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <button type="button" class="btn btn-ghost" id="addManualCityBtn">Ajouter</button>
                        </div>
                    </div>
                    <div class="source-badge" id="aiSourceBadge"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email administrateur</label>
                        <input class="form-input" type="email" name="admin_email" value="<?= htmlspecialchars((string) $siteSession['admin_email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe admin</label>
                        <input class="form-input" type="password" name="admin_password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">URL du site</label>
                    <input class="form-input" name="base_url" placeholder="https://bordeaux.estimia.fr" value="<?= htmlspecialchars((string) $siteSession['base_url'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <hr class="form-separator">

                <div class="form-section-title">Configuration SMTP</div>
                <div class="form-section-desc">Optionnel — le test ne bloque pas l'installation.</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Hôte SMTP</label>
                        <input class="form-input" name="smtp_host" id="smtp_host" value="<?= htmlspecialchars((string) $siteSession['smtp_host'], ENT_QUOTES, 'UTF-8') ?>" placeholder="smtp.example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Port SMTP</label>
                        <input class="form-input" type="number" name="smtp_port" id="smtp_port" value="<?= (int) $siteSession['smtp_port'] ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Utilisateur SMTP</label>
                        <input class="form-input" name="smtp_user" id="smtp_user" value="<?= htmlspecialchars((string) $siteSession['smtp_user'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe SMTP</label>
                        <input class="form-input" type="password" name="smtp_pass" id="smtp_pass" value="<?= htmlspecialchars((string) $siteSession['smtp_pass'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div style="margin-bottom:16px">
                    <button type="button" class="btn btn-outline-test" id="testSmtpBtn">
                        <span class="spinner" id="smtpSpinner"></span>
                        Tester SMTP
                    </button>
                </div>
                <div id="smtpResult"></div>

                <div class="btn-row">
                    <a class="btn btn-ghost" href="?step=2">← Retour</a>
                    <button type="submit" class="btn btn-primary">Suivant →</button>
                </div>
            </form>
        </div>

        <?php if ($step === 4): ?>
            <form class="mt-6 space-y-4" method="post">
                <input type="hidden" name="step" value="4">
                <h2 class="text-lg font-semibold">Configuration des Clés d'API</h2>
                <p class="text-sm text-slate-600">
                    Bienvenue dans l'assistant d'installation ! Avant de continuer, configurez les clés d'API ci-dessous.
                </p>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <p class="font-medium">Instructions :</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>Saisissez vos clés d'API dans les champs correspondants.</li>
                        <li>Validez pour passer à l'étape suivante.</li>
                        <li>Cette étape n'est pas bloquante : vous pouvez continuer sans clé, mais certaines fonctionnalités ne seront pas disponibles.</li>
                    </ul>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <label class="block text-sm font-medium">OpenAI
                        <input name="api_key_openai" value="<?= htmlspecialchars((string) ($data['api_key_openai'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="sk-...">
                    </label>
                    <label class="block text-sm font-medium">Perplexity
                        <input name="api_key_perplexity" value="<?= htmlspecialchars((string) ($data['api_key_perplexity'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="pplx-...">
                    </label>
                    <label class="block text-sm font-medium">Claude
                        <input name="api_key_claude" value="<?= htmlspecialchars((string) ($data['api_key_claude'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="claude-...">
                    </label>
                    <label class="block text-sm font-medium">DVF
                        <input name="api_key_dvf" value="<?= htmlspecialchars((string) ($data['api_key_dvf'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="dvf-...">
                    </label>
                    <label class="block text-sm font-medium md:col-span-2">Mamouth
                        <input name="api_key_mamouth" value="<?= htmlspecialchars((string) ($data['api_key_mamouth'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="mamouth-...">
                    </label>
                </div>
                <label class="flex items-start gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    <input type="checkbox" name="api_keys_activate_now" value="1" class="mt-0.5" <?= !empty($data['api_keys_activate_now']) ? 'checked' : ''; ?>>
                    <span><strong>Optionnel :</strong> Activer les clés dès la validation.</span>
                </label>
                <p class="text-xs text-slate-500">
                    Remarque : Les clés seront sauvegardées en toute sécurité. Vous pourrez les modifier ou les désactiver ultérieurement dans les paramètres.
                </p>
                <div class="flex gap-2">
                    <a href="?step=3" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Suivant</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($step === 5): ?>
            <form class="mt-6 space-y-4" method="post">
                <input type="hidden" name="step" value="5">
                <label class="block text-sm font-medium">Titre H1
                    <input name="h1_titre" value="<?= htmlspecialchars((string) ($data['h1_titre'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Combien vaut votre bien à {ville} ?" required>
                </label>
                <label class="block text-sm font-medium">Sous-titre
                    <textarea name="sous_titre" class="mt-1 w-full rounded-lg border px-3 py-2" rows="3" required><?= htmlspecialchars((string) ($data['sous_titre'] ?? ''), ENT_QUOTES); ?></textarea>
                </label>
                <label class="block text-sm font-medium">Meta description
                    <textarea name="meta_description" class="mt-1 w-full rounded-lg border px-3 py-2" rows="2" required><?= htmlspecialchars((string) ($data['meta_description'] ?? ''), ENT_QUOTES); ?></textarea>
                </label>
                <div class="flex gap-2">
                    <a href="?step=4" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($step === 6): ?>
            <div class="mt-6 space-y-4 text-sm">
                <p>Vérifiez les informations ci-dessous puis générez votre site.</p>
                <ul class="list-disc space-y-1 pl-5 text-slate-700">
                    <li><strong>Agence :</strong> <?= htmlspecialchars((string) ($data['agence_nom'] ?? ''), ENT_QUOTES); ?></li>
                    <li><strong>Ville principale :</strong> <?= htmlspecialchars((string) ($data['ville_principale'] ?? ''), ENT_QUOTES); ?></li>
                    <li><strong>Villes :</strong> <?= htmlspecialchars(implode(', ', $data['villes'] ?? []), ENT_QUOTES); ?></li>
                    <li><strong>Email réception :</strong> <?= htmlspecialchars((string) ($data['email_reception'] ?? ''), ENT_QUOTES); ?></li>
                </ul>
                <form method="post">
                    <input type="hidden" name="step" value="6">
                    <div class="flex gap-2">
                        <a href="?step=5" class="rounded-lg border px-4 py-2">Retour</a>
                        <button class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white">Générer mon site</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    /* ── DB Test ── */
    const dbBtn = document.getElementById('testDbBtn');
    if (dbBtn) {
        dbBtn.addEventListener('click', async () => {
            const form = document.getElementById('dbForm');
            const result = document.getElementById('dbResult');
            const spinner = document.getElementById('dbSpinner');
            const fd = new FormData(form);

            spinner.style.display = 'inline-block';
            dbBtn.disabled = true;
            result.innerHTML = '';

            try {
                const r = await fetch('test-db.php', { method: 'POST', body: fd });
                const data = await r.json();
                result.innerHTML = data.success
                    ? `<div class="alert alert-success">${data.message}</div>`
                    : `<div class="alert alert-danger">${data.message}</div>`;
            } catch (e) {
                result.innerHTML = `<div class="alert alert-danger">${e.message}</div>`;
            } finally {
                spinner.style.display = 'none';
                dbBtn.disabled = false;
            }
        });

            spinner.style.display = 'inline-block';
            smtpBtn.disabled = true;
            result.innerHTML = '';

            try {
                const r = await fetch('test-smtp.php', { method: 'POST', body: fd });
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Échec');
                result.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            } catch (e) {
                result.innerHTML = `<div class="alert alert-danger">${e.message}</div><div class="alert alert-warning" style="margin-top:8px">Le SMTP est optionnel — vous pouvez continuer.</div>`;
            } finally {
                spinner.style.display = 'none';
                smtpBtn.disabled = false;
            }
        });
    }

    /* ── AI Cities ── */
    const loadCitiesBtn = document.getElementById('loadCitiesBtn');
    const citiesHidden = document.getElementById('operation_cities_json');
    const citiesList = document.getElementById('citiesList');
    const citiesStatus = document.getElementById('citiesStatus');
    const aiSourceBadge = document.getElementById('aiSourceBadge');
    const sourceLabels = {
        openai: '🟢 Powered by OpenAI',
        anthropic: '🟣 Powered by Claude',
        perplexity: '🔵 Powered by Perplexity',
        mistral: '🟠 Powered by Mistral',
        fallback: '⚪ Mode hors-ligne',
    };
    let selectedCities = [];

    function persistCities() {
        citiesHidden.value = JSON.stringify(selectedCities);
    }

    function renderCities() {
        citiesList.innerHTML = '';
        selectedCities.forEach((city, idx) => {
            const id = `city_option_${idx}`;
            const line = document.createElement('label');
            line.className = 'city-line';
            line.innerHTML = `<input type="checkbox" id="${id}" checked data-city="${city.replace(/"/g, '&quot;')}"> <span>✅ ${city}</span>`;
            const input = line.querySelector('input');
            input.addEventListener('change', () => {
                if (!input.checked) {
                    selectedCities = selectedCities.filter(c => c !== city);
                    renderCities();
                    persistCities();
                }
            });
            citiesList.appendChild(line);
        });
    }

    function loadCitiesFromHidden() {
        if (!citiesHidden || !citiesHidden.value) return;
        try {
            const parsed = JSON.parse(citiesHidden.value);
            if (Array.isArray(parsed)) {
                selectedCities = parsed.filter(v => typeof v === 'string' && v.trim() !== '');
                renderCities();
            }
        } catch (_) {}
    }

    if (loadCitiesBtn) {
        loadCitiesFromHidden();
        loadCitiesBtn.addEventListener('click', async () => {
            const city = (document.getElementById('city_name')?.value || '').trim();
            const radius = (document.getElementById('operation_radius_km')?.value || '30').trim();
            const spinner = document.getElementById('citiesSpinner');
            if (!city) return;

            const fd = new FormData();
            fd.append('city', city);
            fd.append('radius', radius);
            spinner.style.display = 'inline-block';
            loadCitiesBtn.disabled = true;
            citiesStatus.innerHTML = '<div class="alert alert-info">Analyse géographique en cours...</div>';

            try {
                const r = await fetch('ai-cities.php', { method: 'POST', body: fd });
                const data = await r.json();
                if (Array.isArray(data.cities) && data.cities.length > 0) {
                    selectedCities = data.cities;
                    renderCities();
                    persistCities();
                    citiesStatus.innerHTML = '';
                    aiSourceBadge.textContent = sourceLabels[data.source] || sourceLabels.fallback;
                } else {
                    aiSourceBadge.textContent = sourceLabels.fallback;
                    citiesStatus.innerHTML = '';
                }
            } catch (_) {
                aiSourceBadge.textContent = sourceLabels.fallback;
                citiesStatus.innerHTML = '';
            } finally {
                spinner.style.display = 'none';
                loadCitiesBtn.disabled = false;
            }
        });
    }

    const addManualCityBtn = document.getElementById('addManualCityBtn');
    if (addManualCityBtn) {
        addManualCityBtn.addEventListener('click', () => {
            const input = document.getElementById('manualCityInput');
            const value = (input.value || '').trim();
            if (!value) return;
            if (!selectedCities.includes(value)) {
                selectedCities.push(value);
                renderCities();
                persistCities();
            }
            input.value = '';
        });
    }

    /* ── Finalize spinner ── */
    const finalBtn = document.getElementById('finalizeBtn');
    if (finalBtn) {
        finalBtn.closest('form').addEventListener('submit', () => {
            const spinner = document.getElementById('finalSpinner');
            if (spinner) spinner.style.display = 'inline-block';
            finalBtn.disabled = true;
            finalBtn.style.opacity = '.7';
        });
    }
</script>
</body>
</html>
