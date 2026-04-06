<?php

declare(strict_types=1);

session_start();

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';
$configFile = $configDir . '/config.php';
$databaseFile = $configDir . '/database.php';
$installSqlPath = $rootDir . '/install.sql';

function installRenderEmailTemplate(string $rootDir, string $template, array $data = []): string
{
    $templatePath = $rootDir . '/templates/emails/' . $template . '.php';
    if (!is_file($templatePath)) {
        return '';
    }

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

    $errno = 0;
    $errstr = '';
    $prefix = ($port === 465) ? 'ssl://' : '';
    $conn = @fsockopen($prefix . $host, $port, $errno, $errstr, 10);

    if (!$conn) {
        return ['success' => false, 'message' => "Connexion échouée : $errstr ($errno)"];
    }

    $banner = fgets($conn, 512);

    fputs($conn, "EHLO localhost\r\n");
    $ehlo = fgets($conn, 512);

    fputs($conn, "AUTH LOGIN\r\n");
    $auth = fgets($conn, 512);

    fputs($conn, base64_encode($user) . "\r\n");
    fgets($conn, 512);

    fputs($conn, base64_encode($pass) . "\r\n");
    $loginResult = fgets($conn, 512);

    fputs($conn, "QUIT\r\n");
    fclose($conn);

    if (strpos($loginResult, '235') === 0) {
        return ['success' => true, 'message' => 'Connexion SMTP réussie et authentification OK.'];
    }

    return ['success' => false, 'message' => 'Authentification échouée : ' . trim($loginResult)];
}

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
            $dbUser,
            $dbPass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        if (!is_file($installSqlPath)) {
            throw new RuntimeException('Fichier install.sql introuvable à la racine du projet.');
        }

        $sql = (string) file_get_contents($installSqlPath);
        if ($sql === '') {
            throw new RuntimeException('Le fichier install.sql est vide.');
        }

        $pdo->exec($sql);

        $_SESSION['install_db'] = [
            'host' => $host,
            'db_name' => $dbName,
            'db_user' => $dbUser,
            'db_pass' => $dbPass,
        ];

        echo json_encode(['success' => true, 'message' => 'Connexion OK et schéma SQL appliqué.']);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'test_smtp') {
    header('Content-Type: application/json; charset=utf-8');

    $result = smtpHandshakeAndOptionalMail([
        'smtp_host' => $_POST['smtp_host'] ?? '',
        'smtp_port' => $_POST['smtp_port'] ?? '',
        'smtp_user' => $_POST['smtp_user'] ?? '',
        'smtp_pass' => $_POST['smtp_pass'] ?? '',
        'admin_email' => $_POST['admin_email'] ?? '',
    ]);

    if ($result['success'] === false) {
        http_response_code(422);
    }

    echo json_encode($result);
    exit;
}

$alreadyInstalled = is_file($configFile);
$step = (int) ($_GET['step'] ?? 1);
if ($step < 1 || $step > 4) {
    $step = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyInstalled) {
    if ($step === 3) {
        $_SESSION['install_site'] = [
            'site_name' => trim((string) ($_POST['site_name'] ?? 'EstimIA')),
            'city_name' => trim((string) ($_POST['city_name'] ?? 'Bordeaux')),
            'operation_radius_km' => max(1, (int) ($_POST['operation_radius_km'] ?? 30)),
            'admin_email' => trim((string) ($_POST['admin_email'] ?? '')),
            'site_phone' => trim((string) ($_POST['site_phone'] ?? '')),
            'admin_password' => (string) ($_POST['admin_password'] ?? ''),
            'base_url' => trim((string) ($_POST['base_url'] ?? '')),
            'smtp_host' => trim((string) ($_POST['smtp_host'] ?? '')),
            'smtp_port' => (int) ($_POST['smtp_port'] ?? 587),
            'smtp_user' => trim((string) ($_POST['smtp_user'] ?? '')),
            'smtp_pass' => (string) ($_POST['smtp_pass'] ?? ''),
        ];

        header('Location: ?step=4');
        exit;
    }

    if ($step === 4) {
        $db = $_SESSION['install_db'] ?? null;
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

                $secret = bin2hex(random_bytes(32));
                $siteName = addslashes((string) $site['site_name']);
                $cityName = addslashes((string) $site['city_name']);
                $sitePhone = addslashes((string) $site['site_phone']);
                $adminEmail = addslashes((string) $site['admin_email']);
                $baseUrl = addslashes((string) $site['base_url']);
                $radius = max(1, (int) ($site['operation_radius_km'] ?? 30));
                $smtpHost = addslashes((string) ($site['smtp_host'] ?? ''));
                $smtpPort = max(1, (int) ($site['smtp_port'] ?? 587));
                $smtpUser = addslashes((string) ($site['smtp_user'] ?? ''));
                $smtpPass = addslashes((string) ($site['smtp_pass'] ?? ''));
                $dbHost = addslashes((string) $db['host']);
                $dbName = addslashes((string) $db['db_name']);
                $dbUser = addslashes((string) $db['db_user']);
                $dbPass = addslashes((string) $db['db_pass']);

                $configContent = "<?php\n"
                    . "// Configuration EstimIA - Bordeaux\n"
                    . "define('DEBUG_MODE', false);\n"
                    . "define('MAINTENANCE_MODE', false);\n"
                    . "define('SITE_NAME', '{$siteName}');\n"
                    . "define('CITY_NAME', '{$cityName}');\n"
                    . "define('OPERATION_RADIUS_KM', {$radius});\n"
                    . "define('SITE_PHONE', '{$sitePhone}');\n\n"
                    . "// Base de données\n"
                    . "define('DB_HOST', '{$dbHost}');\n"
                    . "define('DB_NAME', '{$dbName}');\n"
                    . "define('DB_USER', '{$dbUser}');\n"
                    . "define('DB_PASS', '{$dbPass}');\n\n"
                    . "// Email\n"
                    . "define('SMTP_HOST', '{$smtpHost}');\n"
                    . "define('SMTP_USER', '{$smtpUser}');\n"
                    . "define('SMTP_PASS', '{$smtpPass}');\n"
                    . "define('SMTP_PORT', {$smtpPort});\n"
                    . "define('MAIL_FROM', 'contact@estimia-bordeaux.fr');\n"
                    . "define('MAIL_FROM_NAME', 'EstimIA Bordeaux');\n\n"
                    . "// Sécurité\n"
                    . "define('ADMIN_EMAIL', '{$adminEmail}');\n"
                    . "define('SECRET_KEY', '{$secret}');\n\n"
                    . "// Chemins\n"
                    . "define('BASE_URL', '{$baseUrl}');\n"
                    . "define('BASE_PATH', __DIR__ . '/..');\n\n"
                    . "require_once BASE_PATH . '/includes/error-handler.php';\n";

                $databaseContent = "<?php\n\n"
                    . "declare(strict_types=1);\n\n"
                    . "final class Database\n"
                    . "{\n"
                    . "    private static ?PDO $connection = null;\n\n"
                    . "    private function __construct()\n"
                    . "    {\n"
                    . "    }\n\n"
                    . "    public static function getConnection(): PDO\n"
                    . "    {\n"
                    . "        if (self::$connection instanceof PDO) {\n"
                    . "            return self::$connection;\n"
                    . "        }\n\n"
                    . "        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);\n\n"
                    . "        self::$connection = new PDO($dsn, DB_USER, DB_PASS, [\n"
                    . "            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n"
                    . "            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n"
                    . "            PDO::ATTR_EMULATE_PREPARES => false,\n"
                    . "        ]);\n\n"
                    . "        return self::$connection;\n"
                    . "    }\n"
                    . "}\n";

                if (file_put_contents($configFile, $configContent) === false) {
                    throw new RuntimeException('Échec d\'écriture de config/config.php');
                }

                if (file_put_contents($databaseFile, $databaseContent) === false) {
                    throw new RuntimeException('Échec d\'écriture de config/database.php');
                }

                $pdo = new PDO(
                    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['db_name']),
                    (string) $db['db_user'],
                    (string) $db['db_pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );

                $stmt = $pdo->prepare(
                    'INSERT INTO users (nom, prenom, email, password, role, actif) VALUES (:nom, :prenom, :email, :password, :role, :actif)'
                );

                $stmt->execute([
                    'nom' => 'Admin',
                    'prenom' => 'EstimIA',
                    'email' => (string) $site['admin_email'],
                    'password' => password_hash((string) $site['admin_password'], PASSWORD_DEFAULT),
                    'role' => 'admin',
                    'actif' => 1,
                ]);

                $nameParts = preg_split('/\s+/', trim((string) $site['site_name'])) ?: [];
                $defaultPrenom = isset($nameParts[0]) && $nameParts[0] !== '' ? $nameParts[0] : 'Admin';
                $defaultNom = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'EstimIA';

                $adminStmt = $pdo->prepare(
                    'INSERT INTO admins (prenom, nom, email) VALUES (:prenom, :nom, :email)
                     ON DUPLICATE KEY UPDATE prenom = VALUES(prenom), nom = VALUES(nom)'
                );
                $adminStmt->execute([
                    'prenom' => $defaultPrenom,
                    'nom' => $defaultNom,
                    'email' => (string) $site['admin_email'],
                ]);

                $emailHtml = installRenderEmailTemplate($rootDir, 'install-success', [
                    'prenom' => $defaultPrenom,
                    'nom' => $defaultNom,
                    'siteName' => (string) $site['site_name'],
                    'cityName' => (string) $site['city_name'],
                    'baseUrl' => (string) $site['base_url'],
                ]);

                installSendEmail(
                    (string) $site['admin_email'],
                    'Installation terminée - Accès administration',
                    $emailHtml,
                    (string) $site['site_name']
                );

                $installCompleted = true;
                unset($_SESSION['install_db'], $_SESSION['install_site']);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$requirements = [
    'PHP >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'Extension pdo' => extension_loaded('pdo'),
    'Extension pdo_mysql' => extension_loaded('pdo_mysql'),
    'Extension mbstring' => extension_loaded('mbstring'),
    'Extension json' => extension_loaded('json'),
    'Extension curl' => extension_loaded('curl'),
    'Dossier config/ writable' => is_dir($configDir) && is_writable($configDir),
    'Dossier assets/ writable' => is_dir($rootDir . '/assets') && is_writable($rootDir . '/assets'),
];

$dbSession = $_SESSION['install_db'] ?? ['host' => 'localhost', 'db_name' => '', 'db_user' => '', 'db_pass' => ''];
$siteSession = $_SESSION['install_site'] ?? [
    'site_name' => 'EstimIA',
    'city_name' => 'Bordeaux',
    'operation_radius_km' => 30,
    'admin_email' => '',
    'site_phone' => '',
    'admin_password' => '',
    'base_url' => '',
    'smtp_host' => '',
    'smtp_port' => 587,
    'smtp_user' => '',
    'smtp_pass' => '',
];

$tableDescriptions = [
    'estimations' => 'Stocke toutes les demandes d’estimation des visiteurs.',
    'users' => 'Comptes administrateurs/agents pour accéder au back-office.',
    'settings' => 'Paramètres du site (nom, ville, téléphone, couleurs...).',
    'villes_prix' => 'Prix au m² par ville/quartier pour le calcul d’estimation.',
    'lead_activities' => 'Historique des notes et actions sur les leads.',
    'rate_limits' => 'Protection anti-abus et limitation de requêtes.',
    'login_attempts' => 'Suivi des tentatives de connexion à l’admin.',
    'email_logs' => 'Historique des emails envoyés (estimation, relance...).',
    'webhook_logs' => 'Journal des appels webhooks sortants.',
    'sessions' => 'Sessions admin actives.',
    'admin_users' => 'Comptes d’administration avancée supplémentaires.',
    'ads_checklist_progress' => 'Progression de la checklist Google Ads.',
    'google_ads_drafts' => 'Brouillons d’annonces Google Ads.',
];
$expectedTables = extractInstallTables($installSqlPath);
$tableChecklist = [];
if (is_array($_SESSION['install_db'] ?? null)) {
    $tableChecklist = getTablesChecklist($_SESSION['install_db'], $expectedTables);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation EstimIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: #e2e8f0; }
        .card { background: #111827; border: 1px solid #334155; }
        .text-muted { color: #cbd5e1 !important; }
        .form-label { color: #ffffff; font-weight: 600; }
        .step-title { color: #ffffff; font-size: 1.25rem; font-weight: 700; }
        .help-text { color: #cbd5e1; }
        .step-pill { border: 1px solid #334155; color: #94a3b8; padding: .35rem .75rem; border-radius: 999px; font-size: .85rem; }
        .step-pill.active { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
    </style>
</head>
<body>
<div class="container py-5" style="max-width: 900px;">
    <h1 class="h3 mb-3">Wizard d'installation EstimIA</h1>
    <p class="help-text mb-4">Assistant en 4 étapes.</p>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <span class="step-pill <?= $step === $i ? 'active' : '' ?>">Étape <?= $i ?></span>
        <?php endfor; ?>
    </div>

    <?php if ($alreadyInstalled): ?>
        <div class="alert alert-warning">Déjà installé : <code>config/config.php</code> existe déjà. L'installation est bloquée.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($installCompleted)): ?>
        <div class="alert alert-success">
            <h2 class="h5">Installation terminée ✅</h2>
            <p class="mb-1">Les fichiers de configuration ont été générés et le compte admin créé.</p>
            <a class="btn btn-success btn-sm me-2" href="../">Aller au site</a>
            <a class="btn btn-outline-light btn-sm" href="../admin/">Aller à l'admin</a>
        </div>

        <?php if ($tableChecklist !== []): ?>
            <div class="card p-4 mb-4">
                <h3 class="h5 mb-3">Checklist des tables créées</h3>
                <ul class="list-group">
                    <?php foreach ($expectedTables as $table): ?>
                        <?php $exists = (bool) ($tableChecklist[$table] ?? false); ?>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            <div class="d-flex align-items-start gap-2">
                                <span><?= $exists ? '✅' : '❌' ?></span>
                                <div>
                                    <strong><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="text-muted small"><?= htmlspecialchars($tableDescriptions[$table] ?? 'Table de support installée par le wizard.', ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$alreadyInstalled && empty($installCompleted) && $step === 1): ?>
        <div class="card p-4">
            <h2 class="step-title mb-3">Étape 1 — Vérification pré-requis</h2>
            <ul class="list-group">
                <?php foreach ($requirements as $label => $ok): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light border-secondary">
                        <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="badge <?= $ok ? 'bg-success' : 'bg-danger' ?>"><?= $ok ? 'OK' : 'KO' ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="mt-3 text-end">
                <a class="btn btn-primary" href="?step=2">Continuer</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$alreadyInstalled && empty($installCompleted) && $step === 2): ?>
        <div class="card p-4">
            <h2 class="step-title mb-3">Étape 2 — Configuration base de données</h2>
            <form id="dbForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Host</label>
                    <input class="form-control" name="host" value="<?= htmlspecialchars((string) $dbSession['host'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom BDD</label>
                    <input class="form-control" name="db_name" value="<?= htmlspecialchars((string) $dbSession['db_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Utilisateur</label>
                    <input class="form-control" name="db_user" value="<?= htmlspecialchars((string) $dbSession['db_user'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" name="db_pass" value="<?= htmlspecialchars((string) $dbSession['db_pass'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a class="btn btn-outline-light" href="?step=1">Retour</a>
                    <button type="button" class="btn btn-primary" id="testDbBtn">Tester la connexion</button>
                    <a class="btn btn-success" href="?step=3">Suivant</a>
                </div>
            </form>
            <div id="dbResult" class="mt-3"></div>
        </div>
    <?php endif; ?>

    <?php if (!$alreadyInstalled && empty($installCompleted) && $step === 3): ?>
        <div class="card p-4">
            <h2 class="step-title mb-3">Étape 3 — Configuration site</h2>
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom du site</label>
                    <input class="form-control" name="site_name" value="<?= htmlspecialchars((string) $siteSession['site_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ville</label>
                    <input class="form-control" name="city_name" value="<?= htmlspecialchars((string) $siteSession['city_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rayon d'opération (km)</label>
                    <input type="number" min="1" class="form-control" name="operation_radius_km" value="<?= htmlspecialchars((string) $siteSession['operation_radius_km'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email admin</label>
                    <input type="email" class="form-control" name="admin_email" value="<?= htmlspecialchars((string) $siteSession['admin_email'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input class="form-control" name="site_phone" value="<?= htmlspecialchars((string) $siteSession['site_phone'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mot de passe admin</label>
                    <input type="password" class="form-control" name="admin_password" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL du site</label>
                    <input class="form-control" name="base_url" placeholder="https://bordeaux.estimia.fr" value="<?= htmlspecialchars((string) $siteSession['base_url'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12 mt-2">
                    <hr class="border-secondary">
                    <h3 class="h5">Configuration SMTP</h3>
                    <p class="help-text mb-2">Le test SMTP est optionnel et ne bloque pas l'installation.</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SMTP Host</label>
                    <input class="form-control" name="smtp_host" value="<?= htmlspecialchars((string) $siteSession['smtp_host'], ENT_QUOTES, 'UTF-8') ?>" placeholder="smtp.example.com">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Port</label>
                    <input type="number" min="1" class="form-control" name="smtp_port" value="<?= htmlspecialchars((string) $siteSession['smtp_port'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SMTP User</label>
                    <input class="form-control" name="smtp_user" value="<?= htmlspecialchars((string) $siteSession['smtp_user'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">SMTP Password</label>
                    <input type="password" class="form-control" name="smtp_pass" value="<?= htmlspecialchars((string) $siteSession['smtp_pass'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12">
                    <button class="btn btn-outline-info" type="button" id="testSmtpBtn">🔌 Tester la connexion SMTP</button>
                </div>
                <div class="col-12">
                    <div id="smtpResult"></div>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a class="btn btn-outline-light" href="?step=2">Retour</a>
                    <button class="btn btn-primary" type="submit">Enregistrer et continuer</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!$alreadyInstalled && empty($installCompleted) && $step === 4): ?>
        <div class="card p-4">
            <h2 class="step-title mb-3">Étape 4 — Finalisation</h2>
            <div class="alert alert-secondary">
                <div class="fw-semibold mb-1">🏙️ Ville cible : <?= htmlspecialchars((string) $siteSession['city_name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="fw-semibold">📍 Rayon d'opération : <?= (int) $siteSession['operation_radius_km'] ?> km</div>
                <img
                    class="img-fluid rounded border border-secondary mt-3"
                    alt="Carte de la ville cible"
                    src="https://maps.googleapis.com/maps/api/staticmap?size=800x240&zoom=11&maptype=roadmap&markers=color:red%7C<?= rawurlencode((string) $siteSession['city_name']) ?>"
                    onerror="this.style.display='none';"
                >
            </div>

            <?php if (trim((string) ($siteSession['smtp_host'] ?? '')) === '' || trim((string) ($siteSession['smtp_user'] ?? '')) === ''): ?>
                <div class="alert alert-warning">
                    ⚠️ SMTP non configuré, les emails ne seront pas envoyés.
                </div>
            <?php endif; ?>

            <p class="help-text">Cette étape va :</p>
            <ul>
                <li>Générer <code>config/config.php</code>.</li>
                <li>Générer <code>config/database.php</code>.</li>
                <li>Créer le compte admin dans <code>users</code>.</li>
            </ul>
            <?php if ($expectedTables !== []): ?>
                <h3 class="h5 mt-4 mb-3">Checklist des tables créées (pré-vérification)</h3>
                <ul class="list-group mb-4">
                    <?php foreach ($expectedTables as $table): ?>
                        <?php $exists = (bool) ($tableChecklist[$table] ?? false); ?>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            <div class="d-flex align-items-start gap-2">
                                <span><?= $exists ? '✅' : '❌' ?></span>
                                <div>
                                    <strong><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="text-muted small"><?= htmlspecialchars($tableDescriptions[$table] ?? 'Table de support installée par le wizard.', ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form method="post" class="d-flex gap-2 justify-content-end">
                <a class="btn btn-outline-light" href="?step=3">Retour</a>
                <button class="btn btn-success" type="submit">Terminer l'installation</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    const testBtn = document.getElementById('testDbBtn');
    if (testBtn) {
        testBtn.addEventListener('click', async () => {
            const form = document.getElementById('dbForm');
            const result = document.getElementById('dbResult');
            const formData = new FormData(form);
            result.innerHTML = '<div class="alert alert-info">Test en cours...</div>';

            try {
                const response = await fetch('?action=test_db', {method: 'POST', body: formData});
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erreur inconnue');
                }
                result.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            } catch (error) {
                result.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            }
        });
    }

    const smtpBtn = document.getElementById('testSmtpBtn');
    if (smtpBtn) {
        smtpBtn.addEventListener('click', async () => {
            const form = smtpBtn.closest('form');
            const result = document.getElementById('smtpResult');
            if (!form || !result) {
                return;
            }

            const formData = new FormData();
            formData.append('smtp_host', form.querySelector('[name="smtp_host"]')?.value || '');
            formData.append('smtp_port', form.querySelector('[name="smtp_port"]')?.value || '');
            formData.append('smtp_user', form.querySelector('[name="smtp_user"]')?.value || '');
            formData.append('smtp_pass', form.querySelector('[name="smtp_pass"]')?.value || '');
            formData.append('admin_email', form.querySelector('[name="admin_email"]')?.value || '');

            result.innerHTML = '<div class="alert alert-info">Test SMTP en cours...</div>';

            try {
                const response = await fetch('?action=test_smtp', {method: 'POST', body: formData});
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Échec SMTP');
                }

                let html = `<div class="alert alert-success">${data.message || '✅ Connexion SMTP réussie'}</div>`;
                if (data.smtp_warning) {
                    html += `<div class="alert alert-warning">⚠️ ${data.smtp_warning}</div>`;
                }
                result.innerHTML = html;
            } catch (error) {
                result.innerHTML = `<div class="alert alert-danger">❌ Échec : ${error.message}</div><div class="alert alert-warning mt-2 mb-0">⚠️ SMTP non configuré, les emails ne seront pas envoyés.</div>`;
            }
        });
    }
</script>
</body>
</html>
