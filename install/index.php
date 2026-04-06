<?php

declare(strict_types=1);

session_start();

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';
$configFile = $configDir . '/config.php';
$databaseFile = $configDir . '/database.php';
$installSqlPath = $rootDir . '/install.sql';
$createLeadsSqlPath = $rootDir . '/sql/create_leads_table.sql';

/* ─── Helpers ─── */

function installRenderEmailTemplate(string $rootDir, string $template, array $data = []): string
{
    $templatePath = $rootDir . '/templates/emails/' . $template . '.php';
    if (!is_file($templatePath)) return '';
    extract($data, EXTR_SKIP);
    ob_start();
    require $templatePath;
    return (string) ob_get_clean();
}

function installSendEmail(string $to, string $subject, string $html, string $fromName): bool
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        sprintf('From: %s <%s>', $fromName, 'contact@estimia-bordeaux.fr'),
        'Reply-To: contact@estimia-bordeaux.fr',
        'X-Mailer: PHP/' . phpversion(),
    ];
    return mail($to, $subject, $html, implode("\r\n", $headers));
}

function smtpHandshakeAndOptionalMail(array $cfg): array
{
    $host = trim($cfg['smtp_host'] ?? '');
    $port = (int) ($cfg['smtp_port'] ?? 465);
    $user = trim($cfg['smtp_user'] ?? '');
    $pass = trim($cfg['smtp_pass'] ?? '');

    if ($host === '' || $user === '' || $pass === '') {
        return ['success' => false, 'message' => 'Paramètres SMTP incomplets.'];
    }

    $prefix = ($port === 465) ? 'ssl://' : '';
    $conn = @fsockopen($prefix . $host, $port, $errno, $errstr, 10);

    if (!$conn) {
        return ['success' => false, 'message' => "Connexion échouée : $errstr ($errno)"];
    }

    $readReply = function () use ($conn) {
        $reply = '';
        while ($line = fgets($conn, 512)) {
            $reply .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $reply;
    };

    $banner = $readReply();
    fputs($conn, "EHLO localhost\r\n");
    $ehlo = $readReply();

    fputs($conn, "AUTH LOGIN\r\n");
    $auth = $readReply();

    if (strpos($auth, '334') !== 0) {
        fputs($conn, "QUIT\r\n");
        fclose($conn);
        return ['success' => false, 'message' => 'AUTH LOGIN non supporté : ' . trim($auth)];
    }

    fputs($conn, base64_encode($user) . "\r\n");
    $readReply();

    fputs($conn, base64_encode($pass) . "\r\n");
    $loginResult = $readReply();

    fputs($conn, "QUIT\r\n");
    fclose($conn);

    if (strpos($loginResult, '235') === 0) {
        return ['success' => true, 'message' => 'Connexion SMTP réussie et authentification OK.'];
    }

    return ['success' => false, 'message' => 'Authentification échouée : ' . trim($loginResult)];
}

function extractInstallTables(string $sqlPath): array
{
    if (!is_file($sqlPath)) return [];
    $sql = (string) file_get_contents($sqlPath);
    preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $sql, $m);
    return array_unique($m[1] ?? []);
}

function getTablesChecklist(array $db, array $expected): array
{
    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['db_name']),
            $db['db_user'],
            $db['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $result = [];
        foreach ($expected as $t) {
            $result[$t] = in_array($t, $existing, true);
        }
        return $result;
    } catch (Throwable $e) {
        return [];
    }
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

/* ─── AJAX: Test DB ─── */

if (isset($_GET['action']) && $_GET['action'] === 'test_db') {
    header('Content-Type: application/json; charset=utf-8');
    $host = trim((string) ($_POST['host'] ?? 'localhost'));
    $dbName = trim((string) ($_POST['db_name'] ?? ''));
    $dbUser = trim((string) ($_POST['db_user'] ?? ''));
    $dbPass = (string) ($_POST['db_pass'] ?? '');

    if ($dbName === '' || $dbUser === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Nom de base et utilisateur requis.']);
        exit;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbName),
            $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
        if (!is_file($installSqlPath)) throw new RuntimeException('Fichier install.sql introuvable.');
        $sql = (string) file_get_contents($installSqlPath);
        if ($sql === '') throw new RuntimeException('Le fichier install.sql est vide.');
        $pdo->exec($sql);
        applySqlFileIfTableMissing($pdo, 'leads', $createLeadsSqlPath);
        $_SESSION['install_db'] = ['host' => $host, 'db_name' => $dbName, 'db_user' => $dbUser, 'db_pass' => $dbPass];
        echo json_encode(['success' => true, 'message' => 'Connexion OK et schéma SQL appliqué.']);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

/* ─── AJAX: Test SMTP ─── */

if (isset($_GET['action']) && $_GET['action'] === 'test_smtp') {
    header('Content-Type: application/json; charset=utf-8');
    $result = smtpHandshakeAndOptionalMail([
        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => $_POST['smtp_port'] ?? '',
        'smtp_user' => $_POST['smtp_user'] ?? '',
        'smtp_pass' => $_POST['smtp_pass'] ?? '',
    ]);
    if (!$result['success']) http_response_code(422);
    echo json_encode($result);
    exit;
}

/* ─── Logique Wizard ─── */

$alreadyInstalled = is_file($configFile);
$step = (int) ($_GET['step'] ?? 1);
if ($step < 1 || $step > 4) $step = 1;
$error = '';
$installCompleted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyInstalled) {

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
        ];
        header('Location: ?step=4');
        exit;
    }

    if ($step === 4) {
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
define('MAIL_FROM', 'contact@estimia-bordeaux.fr');
define('MAIL_FROM_NAME', 'EstimIA Bordeaux');

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

/* ─── Pré-calculs vue ─── */

$requirements = [
    'PHP >= 8.0'              => version_compare(PHP_VERSION, '8.0.0', '>='),
    'Extension pdo'           => extension_loaded('pdo'),
    'Extension pdo_mysql'     => extension_loaded('pdo_mysql'),
    'Extension mbstring'      => extension_loaded('mbstring'),
    'Extension json'          => extension_loaded('json'),
    'Extension curl'          => extension_loaded('curl'),
    'Dossier config/ writable' => is_dir($configDir) && is_writable($configDir),
    'Dossier assets/ writable' => is_dir($rootDir . '/assets') && is_writable($rootDir . '/assets'),
];

$dbSession   = $_SESSION['install_db'] ?? ['host' => 'localhost', 'db_name' => '', 'db_user' => '', 'db_pass' => ''];
$siteSession = $_SESSION['install_site'] ?? [
    'site_name' => 'EstimIA', 'city_name' => 'Bordeaux', 'operation_radius_km' => 30,
    'admin_email' => '', 'site_phone' => '', 'admin_password' => '', 'base_url' => '',
    'smtp_host' => '', 'smtp_port' => 587, 'smtp_user' => '', 'smtp_pass' => '',
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
$stepLabels = ['Pré-requis', 'Base de données', 'Configuration', 'Finalisation'];

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
    </style>
</head>
<body>

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
        <?php for ($i = 1; $i <= 4; $i++): ?>
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
            <?php endif; ?>
        </div>

    <!-- ════════ STEP 1 ════════ -->
    <?php elseif (!$alreadyInstalled && $step === 1): ?>
        <div class="card animate-in" style="animation-delay:.1s">
            <div class="card-title">Vérification des pré-requis</div>
            <div class="card-desc">Votre serveur doit remplir ces conditions avant de continuer.</div>

            <ul class="checklist">
                <?php foreach ($requirements as $label => $ok): ?>
                    <li class="checklist-item">
                        <span class="check-icon <?= $ok ? 'check-ok' : 'check-fail' ?>"><?= $ok ? '✓' : '✗' ?></span>
                        <span class="checklist-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="btn-row">
                <?php if ($allReqOk): ?>
                    <a class="btn btn-primary" href="?step=2">Continuer →</a>
                <?php else: ?>
                    <span class="btn btn-ghost" style="opacity:.5;cursor:not-allowed">Pré-requis manquants</span>
                <?php endif; ?>
            </div>
        </div>

    <!-- ════════ STEP 2 ════════ -->
    <?php elseif (!$alreadyInstalled && $step === 2): ?>
        <div class="card animate-in" style="animation-delay:.1s">
            <div class="card-title">Base de données</div>
            <div class="card-desc">Connexion MySQL et création automatique du schéma.</div>

            <form id="dbForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Hôte</label>
                        <input class="form-input" name="host" value="<?= htmlspecialchars((string) $dbSession['host'], ENT_QUOTES, 'UTF-8') ?>" placeholder="localhost">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom de la base</label>
                        <input class="form-input" name="db_name" value="<?= htmlspecialchars((string) $dbSession['db_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Utilisateur</label>
                        <input class="form-input" name="db_user" value="<?= htmlspecialchars((string) $dbSession['db_user'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <input class="form-input" type="password" name="db_pass" value="<?= htmlspecialchars((string) $dbSession['db_pass'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="btn-row">
                    <a class="btn btn-ghost" href="?step=1">← Retour</a>
                    <button type="button" class="btn btn-outline-test" id="testDbBtn">
                        <span class="spinner" id="dbSpinner"></span>
                        Tester la connexion
                    </button>
                    <a class="btn btn-primary" href="?step=3">Suivant →</a>
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
                        <input class="form-input" name="city_name" value="<?= htmlspecialchars((string) $siteSession['city_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rayon d'opération (km)</label>
                        <input class="form-input" type="number" name="operation_radius_km" value="<?= (int) $siteSession['operation_radius_km'] ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input class="form-input" name="site_phone" value="<?= htmlspecialchars((string) $siteSession['site_phone'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
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

    <!-- ════════ STEP 4 ════════ -->
    <?php elseif (!$alreadyInstalled && $step === 4): ?>
        <div class="card animate-in" style="animation-delay:.1s">
            <div class="card-title">Finalisation</div>
            <div class="card-desc">Vérifiez le récapitulatif puis lancez l'installation.</div>

            <div class="recap-grid">
                <div class="recap-item">
                    <div class="recap-label">Site</div>
                    <div class="recap-value"><?= htmlspecialchars((string) $siteSession['site_name'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="recap-item">
                    <div class="recap-label">Ville cible</div>
                    <div class="recap-value"><?= htmlspecialchars((string) $siteSession['city_name'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="recap-item">
                    <div class="recap-label">Rayon</div>
                    <div class="recap-value"><?= (int) $siteSession['operation_radius_km'] ?> km</div>
                </div>
                <div class="recap-item">
                    <div class="recap-label">Admin</div>
                    <div class="recap-value"><?= htmlspecialchars((string) $siteSession['admin_email'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="recap-item">
                    <div class="recap-label">SMTP</div>
                    <div class="recap-value"><?= trim((string) ($siteSession['smtp_host'] ?? '')) !== '' ? htmlspecialchars((string) $siteSession['smtp_host'], ENT_QUOTES, 'UTF-8') : '<span style="color:var(--warning)">Non configuré</span>' ?></div>
                </div>
                <div class="recap-item">
                    <div class="recap-label">URL</div>
                    <div class="recap-value" style="font-size:.8rem;word-break:break-all"><?= htmlspecialchars((string) $siteSession['base_url'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>

            <?php if (trim((string) ($siteSession['smtp_host'] ?? '')) === ''): ?>
                <div class="alert alert-warning">SMTP non configuré — les emails ne seront pas envoyés.</div>
            <?php endif; ?>

            <?php if ($expectedTables !== []): ?>
                <div class="form-section-title">Tables à créer</div>
                <div class="table-grid" style="margin-bottom:24px">
                    <?php foreach ($expectedTables as $table): ?>
                        <?php $ok = (bool) ($tableChecklist[$table] ?? false); ?>
                        <div class="table-row">
                            <span class="status"><?= $ok ? '✅' : '⏳' ?></span>
                            <div>
                                <div class="table-name"><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="table-desc-text"><?= htmlspecialchars($tableDescriptions[$table] ?? 'Table système.', ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="btn-row">
                    <a class="btn btn-ghost" href="?step=3">← Retour</a>
                    <button type="submit" class="btn btn-success" id="finalizeBtn">
                        <span class="spinner" id="finalSpinner"></span>
                        Lancer l'installation
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

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
                const r = await fetch('?action=test_db', { method: 'POST', body: fd });
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
    }

    /* ── SMTP Test ── */
    const smtpBtn = document.getElementById('testSmtpBtn');
    if (smtpBtn) {
        smtpBtn.addEventListener('click', async () => {
            const result = document.getElementById('smtpResult');
            const spinner = document.getElementById('smtpSpinner');
            const fd = new FormData();
            fd.append('smtp_host', document.getElementById('smtp_host').value);
            fd.append('smtp_port', document.getElementById('smtp_port').value);
            fd.append('smtp_user', document.getElementById('smtp_user').value);
            fd.append('smtp_pass', document.getElementById('smtp_pass').value);

            spinner.style.display = 'inline-block';
            smtpBtn.disabled = true;
            result.innerHTML = '';

            try {
                const r = await fetch('?action=test_smtp', { method: 'POST', body: fd });
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
