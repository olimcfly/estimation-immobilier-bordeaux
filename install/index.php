<?php

declare(strict_types=1);

session_start();

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';
$configFile = $configDir . '/config.php';
$lockFile = __DIR__ . '/INSTALLED.lock';
$htaccessFile = __DIR__ . '/.htaccess';
$logoDir = $rootDir . '/assets/images';

if (is_file($lockFile)) {
    header('Location: /');
    exit;
}

function s(?string $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

function baseUrlSanitized(string $url): string
{
    $clean = rtrim(trim($url), '/');
    return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
}

function normalizeColor(string $color): string
{
    $color = trim($color);
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        return '#1e40af';
    }

    return strtoupper($color);
}

function normalizeCities(array $cities): array
{
    $out = [];
    foreach ($cities as $city) {
        $name = s((string) $city);
        if ($name === '') {
            continue;
        }
        $out[$name] = $name;
    }

    return array_values($out);
}

function configValue(string $value): string
{
    return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'install')) {
    $siteName = s($_POST['site_name'] ?? '');
    $cityName = s($_POST['city_name'] ?? '');
    $siteColor = normalizeColor((string) ($_POST['site_color'] ?? '#1e40af'));
    $sitePhone = s($_POST['site_phone'] ?? '');

    $cities = normalizeCities((array) ($_POST['cities'] ?? []));

    $homeH1 = s($_POST['home_h1'] ?? '');
    $homeSousTitre = s($_POST['home_sous_titre'] ?? '');
    $homeMetaDesc = s($_POST['home_meta_desc'] ?? '');
    $baseUrl = baseUrlSanitized((string) ($_POST['base_url'] ?? ''));

    $dbHost = s($_POST['db_host'] ?? 'localhost');
    $dbName = s($_POST['db_name'] ?? '');
    $dbUser = s($_POST['db_user'] ?? '');
    $dbPass = s($_POST['db_pass'] ?? '');

    $smtpHost = s($_POST['smtp_host'] ?? '');
    $smtpPort = (int) ($_POST['smtp_port'] ?? 465);
    $smtpUser = s($_POST['smtp_user'] ?? '');
    $smtpPass = s($_POST['smtp_pass'] ?? '');
    $adminEmail = s($_POST['admin_email'] ?? '');

    $dbTested = (($_POST['db_tested'] ?? '0') === '1');
    $smtpTested = (($_POST['smtp_tested'] ?? '0') === '1');

    if ($siteName === '' || $cityName === '' || $homeH1 === '' || $homeMetaDesc === '') {
        $errors[] = 'Veuillez remplir les champs obligatoires (agence + textes).';
    }

    if (empty($cities)) {
        $errors[] = 'Ajoutez au moins une ville desservie.';
    }

    if ($dbName === '' || $dbUser === '') {
        $errors[] = 'Les informations DB sont incomplètes.';
    }

    if ($smtpHost === '' || $smtpUser === '' || $adminEmail === '') {
        $errors[] = 'Les informations SMTP sont incomplètes.';
    }

    if (!$dbTested) {
        $errors[] = 'Vous devez tester la connexion DB avant l\'installation.';
    }

    if (!$smtpTested) {
        $errors[] = 'Vous devez tester SMTP avant l\'installation.';
    }

    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'ADMIN_EMAIL est invalide.';
    }

    $logoExt = 'png';
    if (isset($_FILES['logo']) && (int) ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $upload = $_FILES['logo'];
        if ((int) $upload['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur upload logo.';
        } elseif ((int) $upload['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Logo trop volumineux (2MB max).';
        } else {
            $ext = strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION));
            $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Format logo invalide (png/jpg/jpeg/svg/webp).';
            } else {
                if (!is_dir($logoDir) && !mkdir($logoDir, 0775, true) && !is_dir($logoDir)) {
                    $errors[] = 'Impossible de créer assets/images.';
                } else {
                    $logoExt = $ext === 'jpeg' ? 'jpg' : $ext;
                    $target = $logoDir . '/logo.' . $logoExt;
                    foreach (['png', 'jpg', 'svg', 'webp'] as $oldExt) {
                        $old = $logoDir . '/logo.' . $oldExt;
                        if (is_file($old) && $old !== $target) {
                            @unlink($old);
                        }
                    }
                    if (!move_uploaded_file((string) $upload['tmp_name'], $target)) {
                        $errors[] = 'Impossible de sauvegarder le logo.';
                    }
                }
            }
        }
    } else {
        foreach (['png', 'jpg', 'svg', 'webp'] as $candidate) {
            if (is_file($logoDir . '/logo.' . $candidate)) {
                $logoExt = $candidate;
                break;
            }
        }
    }

    if (empty($errors)) {
        $secretKey = bin2hex(random_bytes(32));
        $citiesJson = json_encode($cities, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $content = "<?php\n"
            . "// ============================================\n"
            . "// Configuration — généré par le wizard le " . date('Y-m-d H:i:s') . "\n"
            . "// ============================================\n\n"
            . "define('DEBUG_MODE', false);\n"
            . "define('MAINTENANCE_MODE', false);\n\n"
            . "// Site\n"
            . "define('SITE_NAME',            '" . configValue($siteName) . "');\n"
            . "define('CITY_NAME',            '" . configValue($cityName) . "');\n"
            . "define('SITE_COLOR',           '" . configValue($siteColor) . "');\n"
            . "define('SITE_PHONE',           '" . configValue($sitePhone) . "');\n"
            . "define('OPERATION_RADIUS_KM',  30);\n\n"
            . "// Villes desservies\n"
            . "define('CITIES_LIST', '" . configValue((string) $citiesJson) . "');\n\n"
            . "// Textes homepage\n"
            . "define('HOME_H1',         '" . configValue($homeH1) . "');\n"
            . "define('HOME_SOUS_TITRE', '" . configValue($homeSousTitre) . "');\n"
            . "define('HOME_META_DESC',  '" . configValue($homeMetaDesc) . "');\n\n"
            . "// Logo\n"
            . "define('LOGO_PATH', 'assets/images/logo." . configValue($logoExt) . "');\n\n"
            . "// Base de données\n"
            . "define('DB_HOST', '" . configValue($dbHost) . "');\n"
            . "define('DB_NAME', '" . configValue($dbName) . "');\n"
            . "define('DB_USER', '" . configValue($dbUser) . "');\n"
            . "define('DB_PASS', '" . configValue($dbPass) . "');\n\n"
            . "// Email SMTP — ALWAYS keep SMTP_FROM = SMTP_USER\n"
            . "define('SMTP_HOST',      '" . configValue($smtpHost) . "');\n"
            . "define('SMTP_PORT',      " . max(1, $smtpPort) . ");\n"
            . "define('SMTP_USER',      '" . configValue($smtpUser) . "');\n"
            . "define('SMTP_PASS',      '" . configValue($smtpPass) . "');\n"
            . "define('SMTP_FROM',      '" . configValue($smtpUser) . "');   // never change this line\n"
            . "define('SMTP_FROM_NAME', '" . configValue($siteName) . "');   // never change this line\n"
            . "define('MAIL_FROM',      SMTP_FROM);       // retrocompat alias\n"
            . "define('MAIL_FROM_NAME', SMTP_FROM_NAME);  // retrocompat alias\n\n"
            . "// Sécurité\n"
            . "define('ADMIN_EMAIL', '" . configValue($adminEmail) . "');\n"
            . "define('SECRET_KEY',  '" . $secretKey . "');     // auto-generated: bin2hex(random_bytes(32))\n\n"
            . "// Chemins\n"
            . "define('BASE_URL',  '" . configValue($baseUrl) . "');\n"
            . "define('BASE_PATH', __DIR__ . '/..');\n\n"
            . "require_once BASE_PATH . '/includes/error-handler.php';\n";

        if (!is_dir($configDir) && !mkdir($configDir, 0775, true) && !is_dir($configDir)) {
            $errors[] = 'Impossible de créer le dossier config.';
        } elseif (file_put_contents($configFile, $content) === false) {
            $errors[] = 'Impossible d\'écrire config/config.php.';
        } else {
            file_put_contents($lockFile, "Installed at " . date('c') . PHP_EOL);
            file_put_contents($htaccessFile, "Deny from all\n");
            $success = 'Installation terminée. Le fichier config/config.php a été généré.';
        }
    }
}

$defaults = [
    'site_name' => $_POST['site_name'] ?? 'Mon Agence',
    'city_name' => $_POST['city_name'] ?? '',
    'site_color' => $_POST['site_color'] ?? '#1e40af',
    'site_phone' => $_POST['site_phone'] ?? '',
    'home_h1' => $_POST['home_h1'] ?? '',
    'home_sous_titre' => $_POST['home_sous_titre'] ?? '',
    'home_meta_desc' => $_POST['home_meta_desc'] ?? '',
    'base_url' => $_POST['base_url'] ?? '',
    'db_host' => $_POST['db_host'] ?? 'localhost',
    'db_name' => $_POST['db_name'] ?? '',
    'db_user' => $_POST['db_user'] ?? '',
    'db_pass' => $_POST['db_pass'] ?? '',
    'smtp_host' => $_POST['smtp_host'] ?? '',
    'smtp_port' => $_POST['smtp_port'] ?? '465',
    'smtp_user' => $_POST['smtp_user'] ?? '',
    'smtp_pass' => $_POST['smtp_pass'] ?? '',
    'admin_email' => $_POST['admin_email'] ?? '',
];
$citiesInput = (array) ($_POST['cities'] ?? ['']);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation - Estimation immobilière</title>
    <style>
        :root { --primary: #1e40af; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #1f2937; }
        .wrap { max-width: 980px; margin: 24px auto; background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.08); overflow: hidden; }
        header { padding: 20px; background: #111827; color: #fff; }
        .progress { display: flex; gap: 8px; margin-top: 12px; }
        .progress div { flex: 1; height: 8px; border-radius: 999px; background: #374151; opacity: .35; }
        .progress div.active { background: #34d399; opacity: 1; }
        .content { padding: 20px; }
        .step { display: none; }
        .step.active { display: block; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
        label { display: block; margin-bottom: 6px; font-weight: 700; font-size: 14px; }
        input, button, textarea { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #d1d5db; }
        .btn { background: var(--primary); color: #fff; border: none; cursor: pointer; font-weight: 700; }
        .btn.secondary { background: #4b5563; }
        .btn.success { background: #059669; }
        .actions { display: flex; justify-content: space-between; gap: 10px; margin-top: 16px; }
        .city-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .city-row button { max-width: 130px; }
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 14px; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.ok { background: #dcfce7; color: #065f46; }
        .summary { background: #f9fafb; padding: 14px; border-radius: 12px; border: 1px solid #e5e7eb; }
        .summary pre { white-space: pre-wrap; font-family: inherit; margin: 0; }
        .small { font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1 style="margin:0;">Wizard d'installation</h1>
        <div class="progress" id="progressBar">
            <div class="active"></div><div></div><div></div><div></div><div></div><div></div>
        </div>
    </header>
    <div class="content">
        <?php if (!empty($errors)): ?>
            <div class="alert error"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert ok"><?php echo $success; ?></div>
            <p><a href="/" class="btn success" style="display:inline-block;text-decoration:none;padding:10px 16px;width:auto;">Aller au site</a></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="installForm">
            <input type="hidden" name="action" value="install">
            <input type="hidden" name="db_tested" id="db_tested" value="0">
            <input type="hidden" name="smtp_tested" id="smtp_tested" value="0">

            <section class="step active" data-step="1">
                <h2>Étape 1 — Agence</h2>
                <div class="grid">
                    <div><label>Nom de l'agence</label><input required name="site_name" value="<?php echo s($defaults['site_name']); ?>"></div>
                    <div><label>Ville principale</label><input required name="city_name" value="<?php echo s($defaults['city_name']); ?>"></div>
                    <div><label>Couleur principale (hex)</label><input name="site_color" value="<?php echo s($defaults['site_color']); ?>" placeholder="#1E40AF"></div>
                    <div><label>Téléphone</label><input name="site_phone" value="<?php echo s($defaults['site_phone']); ?>"></div>
                    <div style="grid-column: 1 / -1;"><label>Logo (png/jpg/jpeg/svg/webp, 2MB max)</label><input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp,image/png,image/jpeg,image/svg+xml,image/webp"></div>
                </div>
            </section>

            <section class="step" data-step="2">
                <h2>Étape 2 — Villes desservies</h2>
                <div id="citiesWrapper">
                    <?php foreach ($citiesInput as $city): ?>
                        <div class="city-row"><input name="cities[]" value="<?php echo s((string) $city); ?>" placeholder="Ville"><button type="button" class="btn secondary" onclick="removeCity(this)">Supprimer</button></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn" onclick="addCity()">Ajouter une ville</button>
            </section>

            <section class="step" data-step="3">
                <h2>Étape 3 — Textes homepage</h2>
                <div class="grid">
                    <div><label>H1 principal</label><input required name="home_h1" value="<?php echo s($defaults['home_h1']); ?>"></div>
                    <div><label>Sous-titre</label><input name="home_sous_titre" value="<?php echo s($defaults['home_sous_titre']); ?>"></div>
                    <div style="grid-column: 1 / -1;"><label>Meta description</label><textarea required name="home_meta_desc"><?php echo s($defaults['home_meta_desc']); ?></textarea></div>
                    <div style="grid-column: 1 / -1;"><label>URL du site (sans slash final)</label><input required name="base_url" value="<?php echo s($defaults['base_url']); ?>" placeholder="https://exemple.fr"></div>
                </div>
            </section>

            <section class="step" data-step="4">
                <h2>Étape 4 — Base de données</h2>
                <div class="grid">
                    <div><label>DB_HOST</label><input name="db_host" value="<?php echo s($defaults['db_host']); ?>"></div>
                    <div><label>DB_NAME</label><input required name="db_name" value="<?php echo s($defaults['db_name']); ?>"></div>
                    <div><label>DB_USER</label><input required name="db_user" value="<?php echo s($defaults['db_user']); ?>"></div>
                    <div><label>DB_PASS</label><input type="password" name="db_pass" value="<?php echo s($defaults['db_pass']); ?>"></div>
                </div>
                <button type="button" class="btn" onclick="testDb()">Tester la connexion</button>
                <p id="dbResult" class="small"></p>
            </section>

            <section class="step" data-step="5">
                <h2>Étape 5 — Email SMTP</h2>
                <div class="grid">
                    <div><label>SMTP_HOST</label><input required name="smtp_host" value="<?php echo s($defaults['smtp_host']); ?>"></div>
                    <div><label>SMTP_PORT</label><input name="smtp_port" value="<?php echo s($defaults['smtp_port']); ?>"></div>
                    <div><label>SMTP_USER</label><input required name="smtp_user" value="<?php echo s($defaults['smtp_user']); ?>"></div>
                    <div><label>SMTP_PASS</label><input type="password" name="smtp_pass" value="<?php echo s($defaults['smtp_pass']); ?>"></div>
                    <div><label>ADMIN_EMAIL</label><input required name="admin_email" value="<?php echo s($defaults['admin_email']); ?>"></div>
                    <div>
                        <label>SMTP_FROM (auto)</label>
                        <input id="smtp_from_preview" readonly>
                        <p class="small">Toujours égal à SMTP_USER</p>
                    </div>
                </div>
                <button type="button" class="btn" onclick="testSmtp()">Tester l'envoi SMTP</button>
                <p id="smtpResult" class="small"></p>
            </section>

            <section class="step" data-step="6">
                <h2>Étape 6 — Récapitulatif</h2>
                <div class="summary"><pre id="summary"></pre></div>
                <p class="small">Confirmez pour générer <code>config/config.php</code> et <code>install/INSTALLED.lock</code>.</p>
            </section>

            <div class="actions">
                <button type="button" class="btn secondary" id="prevBtn" onclick="prevStep()">Précédent</button>
                <button type="button" class="btn" id="nextBtn" onclick="nextStep()">Suivant</button>
                <button type="submit" class="btn success" id="installBtn" style="display:none;">Installer</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 6;

function syncSmtpPreview() {
    const user = document.querySelector('[name="smtp_user"]').value;
    document.getElementById('smtp_from_preview').value = user;
}

document.querySelector('[name="smtp_user"]').addEventListener('input', syncSmtpPreview);
syncSmtpPreview();

function showStep(step) {
    currentStep = step;
    document.querySelectorAll('.step').forEach(section => {
        section.classList.toggle('active', Number(section.dataset.step) === step);
    });

    const bars = document.querySelectorAll('#progressBar div');
    bars.forEach((bar, idx) => bar.classList.toggle('active', idx < step));

    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
    document.getElementById('installBtn').style.display = step === totalSteps ? 'inline-block' : 'none';

    if (step === 6) {
        updateSummary();
    }
}

function nextStep() {
    if (currentStep < totalSteps) showStep(currentStep + 1);
}

function prevStep() {
    if (currentStep > 1) showStep(currentStep - 1);
}

function addCity() {
    const wrapper = document.getElementById('citiesWrapper');
    const row = document.createElement('div');
    row.className = 'city-row';
    row.innerHTML = '<input name="cities[]" placeholder="Ville"><button type="button" class="btn secondary" onclick="removeCity(this)">Supprimer</button>';
    wrapper.appendChild(row);
}

function removeCity(button) {
    const wrapper = document.getElementById('citiesWrapper');
    if (wrapper.querySelectorAll('.city-row').length === 1) {
        wrapper.querySelector('input').value = '';
        return;
    }
    button.parentElement.remove();
}

async function testDb() {
    const form = new FormData();
    ['db_host', 'db_name', 'db_user', 'db_pass'].forEach(name => {
        form.append(name, document.querySelector(`[name="${name}"]`).value);
    });
    const res = await fetch('test-db.php', { method: 'POST', body: form });
    const data = await res.json();
    document.getElementById('dbResult').textContent = data.message;
    document.getElementById('db_tested').value = data.success ? '1' : '0';
}

async function testSmtp() {
    const form = new FormData();
    ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'admin_email'].forEach(name => {
        form.append(name, document.querySelector(`[name="${name}"]`).value);
    });
    const res = await fetch('test-smtp.php', { method: 'POST', body: form });
    const data = await res.json();
    document.getElementById('smtpResult').textContent = data.message;
    document.getElementById('smtp_tested').value = data.success ? '1' : '0';
}

function updateSummary() {
    const cities = Array.from(document.querySelectorAll('[name="cities[]"]')).map(i => i.value.trim()).filter(Boolean);
    const summary = {
        site_name: document.querySelector('[name="site_name"]').value,
        city_name: document.querySelector('[name="city_name"]').value,
        site_color: document.querySelector('[name="site_color"]').value,
        site_phone: document.querySelector('[name="site_phone"]').value,
        cities,
        home_h1: document.querySelector('[name="home_h1"]').value,
        home_sous_titre: document.querySelector('[name="home_sous_titre"]').value,
        home_meta_desc: document.querySelector('[name="home_meta_desc"]').value,
        base_url: document.querySelector('[name="base_url"]').value,
        db_host: document.querySelector('[name="db_host"]').value,
        db_name: document.querySelector('[name="db_name"]').value,
        db_user: document.querySelector('[name="db_user"]').value,
        smtp_host: document.querySelector('[name="smtp_host"]').value,
        smtp_port: document.querySelector('[name="smtp_port"]').value,
        smtp_user: document.querySelector('[name="smtp_user"]').value,
        smtp_from: document.querySelector('[name="smtp_user"]').value,
        smtp_from_name: document.querySelector('[name="site_name"]').value,
        admin_email: document.querySelector('[name="admin_email"]').value
    };
    document.getElementById('summary').textContent = JSON.stringify(summary, null, 2);
}

showStep(1);
</script>
</body>
</html>
