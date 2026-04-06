<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/seeder.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    redirect('login.php');
}

$pdo = Database::getConnection();

$asBool = static function (string $value): bool {
    return in_array($value, ['1', 'true', 'on', 'yes'], true);
};

if (isPost()) {
    $csrf = $_POST['csrf_token'] ?? null;
    if (!verifyCSRFToken(is_string($csrf) ? $csrf : null)) {
        $_SESSION['flash_error'] = 'Token CSRF invalide.';
        redirect('settings.php');
    }

    $section = sanitize($_POST['section'] ?? '');
    $updated = false;

    switch ($section) {
        case 'general':
            $updated = updateConfig('SITE_NAME', sanitize($_POST['site_name'] ?? 'EstimIA')) || $updated;
            $updated = updateConfig('SITE_URL', sanitize($_POST['site_url'] ?? '')) || $updated;
            $updated = updateConfig('SITE_PHONE', sanitize($_POST['site_phone'] ?? '')) || $updated;
            $updated = updateConfig('DEBUG_MODE', $asBool((string) ($_POST['debug_mode'] ?? '0'))) || $updated;
            break;

        case 'smtp':
            $smtpHost = sanitize($_POST['smtp_host'] ?? '');
            $smtpPort = (int) ($_POST['smtp_port'] ?? 465);
            $smtpSecure = sanitize($_POST['smtp_secure'] ?? 'ssl');
            $smtpFromEmail = sanitize($_POST['smtp_from_email'] ?? '');
            if (!in_array($smtpSecure, ['ssl', 'tls', ''], true)) {
                $smtpSecure = 'ssl';
            }
            if ($smtpPort < 1 || $smtpPort > 65535) {
                $smtpPort = 465;
            }
            if ($smtpFromEmail !== '' && !filter_var($smtpFromEmail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['flash_error'] = 'Email expéditeur invalide.';
                redirect('settings.php#smtp');
            }
            $updated = updateConfig('SMTP_HOST', $smtpHost) || $updated;
            $updated = updateConfig('SMTP_PORT', $smtpPort) || $updated;
            $updated = updateConfig('SMTP_USER', sanitize($_POST['smtp_user'] ?? '')) || $updated;
            $updated = updateConfig('SMTP_PASS', (string) ($_POST['smtp_pass'] ?? '')) || $updated;
            $updated = updateConfig('SMTP_SECURE', $smtpSecure) || $updated;
            $updated = updateConfig('SMTP_FROM_EMAIL', $smtpFromEmail) || $updated;
            $updated = updateConfig('SMTP_FROM_NAME', sanitize($_POST['smtp_from_name'] ?? siteConfig('name', 'EstimIA'))) || $updated;

            if (!empty($_POST['smtp_test_email'])) {
                $testEmail = sanitize((string) $_POST['smtp_test_email']);
                if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['flash_error'] = 'Email de test invalide.';
                    redirect('settings.php#smtp');
                }
                $mailer = new Mailer();
                $sent = $mailer->send(
                    $testEmail,
                    '[' . siteConfig('name', 'EstimIA') . '] Test SMTP',
                    '<p>Test SMTP réussi.</p>',
                    'Test SMTP réussi.'
                );
                $_SESSION['flash_' . ($sent ? 'success' : 'error')] = $sent ? 'Email test envoyé.' : 'Échec envoi email test.';
                redirect('settings.php#smtp');
            }
            break;

        case 'notifications':
            $adminEmail = sanitize($_POST['admin_email'] ?? '');
            if ($adminEmail !== '' && !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['flash_error'] = 'Email admin invalide.';
                redirect('settings.php#notifications');
            }
            $weeklyDay = sanitize($_POST['weekly_day'] ?? 'monday');
            $allowedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            if (!in_array($weeklyDay, $allowedDays, true)) {
                $weeklyDay = 'monday';
            }
            $weeklyHour = (int) ($_POST['weekly_hour'] ?? 8);
            if ($weeklyHour < 0 || $weeklyHour > 23) {
                $weeklyHour = 8;
            }
            $updated = updateConfig('NOTIF_NEW_ESTIMATION', $asBool((string) ($_POST['notif_new_estimation'] ?? '0'))) || $updated;
            $updated = updateConfig('NOTIF_NEW_RDV', $asBool((string) ($_POST['notif_new_rdv'] ?? '0'))) || $updated;
            $updated = updateConfig('NOTIF_HOT_LEAD', $asBool((string) ($_POST['notif_hot_lead'] ?? '0'))) || $updated;
            $updated = updateConfig('NOTIF_WEEKLY_REPORT', $asBool((string) ($_POST['notif_weekly'] ?? '0'))) || $updated;
            $updated = updateConfig('ADMIN_EMAIL', $adminEmail) || $updated;
            $updated = updateConfig('HOT_LEAD_THRESHOLD', (int) ($_POST['hot_lead_threshold'] ?? 70)) || $updated;
            $updated = updateConfig('WEEKLY_REPORT_DAY', $weeklyDay) || $updated;
            $updated = updateConfig('WEEKLY_REPORT_HOUR', $weeklyHour) || $updated;
            break;

        case 'maps':
            $updated = updateConfig('GOOGLE_MAPS_API_KEY', sanitize($_POST['maps_key'] ?? '')) || $updated;
            break;

        case 'zone':
            $updated = updateConfig('CITY_NAME', sanitize($_POST['city_name'] ?? siteConfig('city', ''))) || $updated;
            $updated = updateConfig('CITY_LAT', (float) ($_POST['city_lat'] ?? siteConfig('city_lat', 44.8378))) || $updated;
            $updated = updateConfig('CITY_LNG', (float) ($_POST['city_lng'] ?? siteConfig('city_lng', -0.5792))) || $updated;
            $updated = updateConfig('CITY_RADIUS_KM', (int) ($_POST['city_radius'] ?? siteConfig('radius', 30))) || $updated;

            if (!empty($_POST['recalculate_zone'])) {
                $cityLat = (float) ($_POST['city_lat'] ?? siteConfig('city_lat', 44.8378));
                $cityLng = (float) ($_POST['city_lng'] ?? siteConfig('city_lng', -0.5792));
                $radius = (int) ($_POST['city_radius'] ?? siteConfig('radius', 30));
                $seeder = new CitySeeder();
                $seededCount = $seeder->seedForZone($cityLat, $cityLng, $radius);
                $_SESSION['flash_success'] = 'Zone recalculée : ' . $seededCount . ' villes référentielles importées.';
                $updated = true;
            }
            break;

        case 'agents':
            $agentAction = sanitize($_POST['agent_action'] ?? '');
            if ($agentAction === 'add') {
                $agentNom = sanitize($_POST['agent_nom'] ?? '');
                $agentEmail = sanitize($_POST['agent_email'] ?? '');
                if ($agentNom === '' || !filter_var($agentEmail, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['flash_error'] = 'Nom et email agent sont obligatoires et doivent être valides.';
                    redirect('settings.php#agents');
                }
                $stmt = $pdo->prepare('INSERT INTO agents (nom, prenom, email, telephone, secteur_geographique, actif) VALUES (:nom, :prenom, :email, :telephone, :secteur, 1)');
                $stmt->execute([
                    'nom' => $agentNom,
                    'prenom' => sanitize($_POST['agent_prenom'] ?? ''),
                    'email' => $agentEmail,
                    'telephone' => sanitize($_POST['agent_telephone'] ?? ''),
                    'secteur' => json_encode($_POST['agent_secteur'] ?? [], JSON_UNESCAPED_UNICODE),
                ]);
                $updated = true;
            }
            if ($agentAction === 'toggle') {
                $stmt = $pdo->prepare('UPDATE agents SET actif = :actif WHERE id = :id');
                $stmt->execute(['actif' => (int) ($_POST['actif'] ?? 0), 'id' => (int) ($_POST['agent_id'] ?? 0)]);
                $updated = true;
            }
            if ($agentAction === 'delete') {
                $stmt = $pdo->prepare('DELETE FROM agents WHERE id = :id');
                $stmt->execute(['id' => (int) ($_POST['agent_id'] ?? 0)]);
                $updated = true;
            }
            break;

        case 'appearance':
            $updated = updateConfig('SITE_COLOR', sanitize($_POST['site_color'] ?? '#1a56db')) || $updated;

            if (isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
                @mkdir(__DIR__ . '/../assets/img', 0755, true);
                move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../assets/img/logo.png');
            }
            if (isset($_FILES['favicon']) && is_uploaded_file($_FILES['favicon']['tmp_name'])) {
                move_uploaded_file($_FILES['favicon']['tmp_name'], __DIR__ . '/../favicon.ico');
            }
            break;

        case 'security':
            $updated = updateConfig('MAX_REQUESTS_PER_HOUR', (int) ($_POST['max_requests'] ?? 50)) || $updated;
            if (!empty($_POST['regen_secret'])) {
                $updated = updateConfig('SECRET_KEY', bin2hex(random_bytes(32))) || $updated;
            }

            if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                if ((string) ($_POST['new_password'] ?? '') !== (string) ($_POST['new_password_confirm'] ?? '')) {
                    $_SESSION['flash_error'] = 'Les mots de passe ne correspondent pas.';
                    redirect('settings.php#security');
                }
                $stmt = $pdo->query('SELECT id, password_hash FROM admin_users ORDER BY id ASC LIMIT 1');
                $admin = $stmt->fetch();
                if ($admin && password_verify((string) $_POST['old_password'], (string) $admin['password_hash'])) {
                    $up = $pdo->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
                    $up->execute(['hash' => password_hash((string) $_POST['new_password'], PASSWORD_BCRYPT), 'id' => (int) $admin['id']]);
                    $updated = true;
                } else {
                    $_SESSION['flash_error'] = 'Ancien mot de passe incorrect.';
                    redirect('settings.php#security');
                }
            }
            break;

        case 'maintenance':
            if (!empty($_POST['reset_stats'])) {
                $pdo->exec('DELETE FROM activity_log');
                $updated = true;
            }
            break;
    }

    $_SESSION['flash_' . ($updated ? 'success' : 'error')] = $updated ? 'Configuration mise à jour.' : 'Aucune modification appliquée.';
    redirect('settings.php#' . ($section !== '' ? $section : 'general'));
}

$csrfToken = generateCSRFToken();
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$agentsStmt = $pdo->query('SELECT a.*, COUNT(e.id) as leads_assignes FROM agents a LEFT JOIN estimations e ON e.agent_assigne = a.id GROUP BY a.id ORDER BY a.nom, a.prenom');
$agents = $agentsStmt->fetchAll();

$cityPricesStmt = $pdo->query('SELECT v.ville, v.prix_m2_appartement, v.prix_m2_maison, v.distance_centre AS distance
    FROM villes_prix v ORDER BY v.distance_centre ASC, v.ville ASC');
$cityPricesStmt->execute();
$cityPrices = $cityPricesStmt->fetchAll();

$adminLogStmt = $pdo->query('SELECT created_at, details FROM activity_log WHERE action IN ("admin_login", "admin_login_failed") ORDER BY created_at DESC LIMIT 10');
$adminLogins = $adminLogStmt->fetchAll();

$statsStmt = $pdo->query('SELECT COUNT(*) as total_leads FROM estimations');
$totalLeads = (int) ($statsStmt->fetch()['total_leads'] ?? 0);

$dbVersion = (string) ($pdo->query('SELECT VERSION() as version')->fetch()['version'] ?? '-');
$phpVersion = PHP_VERSION;
$freeDisk = function_exists('disk_free_space') ? disk_free_space(__DIR__ . '/..') : 0;

$adminPageTitle = 'Paramètres';
require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="flex gap-0 -mx-8 -my-8 min-h-[calc(100vh-88px)]">
    <aside class="w-64 shrink-0 border-r bg-white p-4 sticky top-0 self-start h-screen overflow-auto">
        <nav class="space-y-1 text-sm">
            <?php
            $nav = [
                'general' => '🏠 Général',
                'zone' => '📍 Zone géographique',
                'smtp' => '📧 Email & SMTP',
                'notifications' => '🔔 Notifications',
                'maps' => '🗺 Google Maps',
                'agents' => '👥 Agents',
                'appearance' => '🎨 Apparence',
                'security' => '🔒 Sécurité',
                'maintenance' => '📊 Maintenance',
            ];
            foreach ($nav as $id => $label): ?>
                <a href="#<?php echo $id; ?>" class="block border-l-2 border-transparent px-3 py-2 hover:bg-primary/5 hover:text-primary hover:border-primary rounded-r"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="flex-1 p-8 max-w-3xl space-y-8">
        <?php if ($flashSuccess): ?><div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700"><?php echo sanitize((string) $flashSuccess); ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700"><?php echo sanitize((string) $flashError); ?></div><?php endif; ?>

        <section id="general" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Général</h2><p class="text-sm text-gray-500 mb-4">Paramètres globaux du site.</p>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="general">
                <input name="site_name" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('name', 'EstimIA'), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom du site">
                <input type="url" name="site_url" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('url', ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="URL">
                <input type="tel" name="site_phone" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('phone', ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Téléphone">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="debug_mode" value="1" <?php echo (defined('DEBUG_MODE') && DEBUG_MODE) ? 'checked' : ''; ?>> Mode debug</label>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
        </section>

        <section id="zone" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Zone géographique</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="zone">
                <input id="city_name" name="city_name" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('city', ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ville cible">
                <input type="hidden" id="city_lat" name="city_lat" value="<?php echo (float) siteConfig('city_lat', 44.8378); ?>">
                <input type="hidden" id="city_lng" name="city_lng" value="<?php echo (float) siteConfig('city_lng', -0.5792); ?>">
                <label class="block">Rayon : <span id="radius_label"><?php echo (int) siteConfig('radius', 30); ?> km</span></label>
                <input id="city_radius" type="range" min="10" max="50" step="5" name="city_radius" value="<?php echo (int) siteConfig('radius', 30); ?>" class="w-full">
                <div id="zoneMap" class="rounded border" style="height:250px"></div>

                <div class="overflow-x-auto mt-3">
                    <table class="min-w-full text-sm">
                        <thead><tr class="text-left border-b"><th>Ville</th><th>Prix appart</th><th>Prix maison</th><th>Distance</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($cityPrices as $city): ?>
                            <tr class="border-b"><td><?php echo sanitize((string) ($city['ville'] ?? '-')); ?></td><td><?php echo formatPrice((int) ($city['prix_m2_appartement'] ?? 0)); ?></td><td><?php echo formatPrice((int) ($city['prix_m2_maison'] ?? 0)); ?></td><td><?php echo is_numeric($city['distance'] ?? null) ? number_format((float) $city['distance'], 1, ',', ' ') . ' km' : '-'; ?></td><td><button type="button" class="text-xs rounded border px-2 py-1">éditer</button> <button type="button" class="text-xs rounded border px-2 py-1">supprimer</button></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="recalculate_zone" value="1"> Recalculer les villes du rayon</label>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
        </section>

        <section id="smtp" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Email & SMTP</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="smtp">
                <input name="smtp_host" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) (defined('SMTP_HOST') ? SMTP_HOST : ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="SMTP host">
                <input type="number" min="1" max="65535" name="smtp_port" class="w-full rounded border px-3 py-2" value="<?php echo (int) (defined('SMTP_PORT') ? SMTP_PORT : 465); ?>" placeholder="Port">
                <input name="smtp_user" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) (defined('SMTP_USER') ? SMTP_USER : ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="SMTP user">
                <input type="password" name="smtp_pass" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) (defined('SMTP_PASS') ? SMTP_PASS : ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="SMTP pass">
                <select name="smtp_secure" class="w-full rounded border px-3 py-2"><option value="ssl" <?php echo (defined('SMTP_SECURE') && SMTP_SECURE === 'ssl') ? 'selected' : ''; ?>>SSL</option><option value="tls" <?php echo (defined('SMTP_SECURE') && SMTP_SECURE === 'tls') ? 'selected' : ''; ?>>TLS</option><option value="" <?php echo (defined('SMTP_SECURE') && SMTP_SECURE === '') ? 'selected' : ''; ?>>Aucune</option></select>
                <input type="email" name="smtp_from_email" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Expéditeur email">
                <input name="smtp_from_name" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : siteConfig('name', 'EstimIA')), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom expéditeur">
                <input type="email" name="smtp_test_email" class="w-full rounded border px-3 py-2" placeholder="Email de test">
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer / Tester</button>
            </form>
        </section>

        <section id="notifications" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Notifications</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="notifications">
                <label><input type="checkbox" name="notif_new_estimation" value="1" <?php echo (defined('NOTIF_NEW_ESTIMATION') && NOTIF_NEW_ESTIMATION) ? 'checked' : ''; ?>> Nouvelle estimation</label><br>
                <label><input type="checkbox" name="notif_new_rdv" value="1" <?php echo (defined('NOTIF_NEW_RDV') && NOTIF_NEW_RDV) ? 'checked' : ''; ?>> Nouveau RDV</label><br>
                <label><input type="checkbox" name="notif_hot_lead" value="1" <?php echo (defined('NOTIF_HOT_LEAD') && NOTIF_HOT_LEAD) ? 'checked' : ''; ?>> Lead chaud</label><br>
                <label><input type="checkbox" name="notif_weekly" value="1" <?php echo (defined('NOTIF_WEEKLY_REPORT') && NOTIF_WEEKLY_REPORT) ? 'checked' : ''; ?>> Rapport hebdo</label>
                <input type="email" name="admin_email" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('admin_email', ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Email de réception">
                <label>Seuil lead chaud: <input type="range" min="50" max="100" name="hot_lead_threshold" value="<?php echo (int) (defined('HOT_LEAD_THRESHOLD') ? HOT_LEAD_THRESHOLD : 70); ?>"></label>
                <div class="grid grid-cols-2 gap-2"><select name="weekly_day" class="rounded border px-3 py-2"><?php $weeklyDayValue = (string) siteConfig('weekly_day', 'monday'); foreach (['monday'=>'Lundi','tuesday'=>'Mardi','wednesday'=>'Mercredi','thursday'=>'Jeudi','friday'=>'Vendredi','saturday'=>'Samedi','sunday'=>'Dimanche'] as $k=>$v): ?><option value="<?php echo $k; ?>" <?php echo $weeklyDayValue === $k ? 'selected' : ''; ?>><?php echo $v; ?></option><?php endforeach; ?></select><select name="weekly_hour" class="rounded border px-3 py-2"><?php $weeklyHourValue = (int) siteConfig('weekly_hour', 8); for($i=0;$i<24;$i++): ?><option value="<?php echo $i; ?>" <?php echo $weeklyHourValue === $i ? 'selected' : ''; ?>><?php echo str_pad((string)$i,2,'0',STR_PAD_LEFT); ?>h</option><?php endfor; ?></select></div>
                <p class="text-xs text-gray-500">Cron conseillé : 0 8 * * 1 /usr/bin/php /home/user/public_html/estimia/cron/weekly_report.php</p>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
        </section>

        <section id="maps" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Google Maps</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="maps">
                <input id="maps_key" name="maps_key" class="w-full rounded border px-3 py-2" value="<?php echo htmlspecialchars((string) siteConfig('maps_key', ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Clé API Google Maps">
                <details open class="rounded border p-3 text-sm text-gray-600"><summary class="font-semibold">Guide de configuration</summary><ol class="list-decimal pl-5 mt-2 space-y-1"><li>console.cloud.google.com</li><li>Créer un projet</li><li>Activer Maps JavaScript API, Places API, Geocoding API</li><li>Créer une clé API restreinte</li><li>Ajouter votre domaine en referrer</li></ol></details>
                <?php if ((string) siteConfig('maps_key', '') === ''): ?><p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded p-3">Le site fonctionne sans Google Maps mais l’autocomplétion d’adresse et les cartes admin seront désactivées.</p><?php endif; ?>
                <button type="button" id="testMapsKey" class="rounded border px-4 py-2">Tester la clé</button>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
        </section>

        <section id="agents" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Agents</h2>
            <div class="overflow-x-auto mb-4"><table class="min-w-full text-sm"><thead><tr class="border-b"><th>Nom</th><th>Email</th><th>Téléphone</th><th>Secteur</th><th>Leads</th><th>Actif</th><th>Actions</th></tr></thead><tbody><?php foreach ($agents as $agent): ?><tr class="border-b"><td><?php echo sanitize(trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''))); ?></td><td><?php echo sanitize((string) ($agent['email'] ?? '')); ?></td><td><?php echo sanitize((string) ($agent['telephone'] ?? '')); ?></td><td><?php echo sanitize((string) ($agent['secteur_geographique'] ?? '')); ?></td><td><?php echo (int) ($agent['leads_assignes'] ?? 0); ?></td><td><?php echo (int) ($agent['actif'] ?? 0) === 1 ? 'Oui' : 'Non'; ?></td><td><form method="POST" class="inline"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="agents"><input type="hidden" name="agent_action" value="toggle"><input type="hidden" name="agent_id" value="<?php echo (int) $agent['id']; ?>"><input type="hidden" name="actif" value="<?php echo (int) ($agent['actif'] ?? 0) === 1 ? '0' : '1'; ?>"><button class="rounded border px-2 py-1 text-xs">Toggle</button></form> <form method="POST" class="inline" onsubmit="return confirm('Supprimer cet agent ?');"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="agents"><input type="hidden" name="agent_action" value="delete"><input type="hidden" name="agent_id" value="<?php echo (int) $agent['id']; ?>"><button class="rounded border px-2 py-1 text-xs">Supprimer</button></form></td></tr><?php endforeach; ?></tbody></table></div>
            <form method="POST" class="grid gap-2 md:grid-cols-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="agents"><input type="hidden" name="agent_action" value="add">
                <input name="agent_nom" class="rounded border px-3 py-2" placeholder="Nom" required>
                <input name="agent_prenom" class="rounded border px-3 py-2" placeholder="Prénom">
                <input type="email" name="agent_email" class="rounded border px-3 py-2" placeholder="Email" required>
                <input type="tel" name="agent_telephone" class="rounded border px-3 py-2" placeholder="Téléphone">
                <select name="agent_secteur[]" multiple class="rounded border px-3 py-2 md:col-span-2"><?php foreach ($cityPrices as $city): ?><option value="<?php echo htmlspecialchars((string) $city['ville'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo sanitize((string) $city['ville']); ?></option><?php endforeach; ?></select>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold md:col-span-2">Ajouter</button>
            </form>
        </section>

        <section id="appearance" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Apparence</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="appearance">
                <input id="site_color_picker" type="color" name="site_color" value="<?php echo htmlspecialchars((string) siteConfig('color', '#1a56db'), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="flex gap-2"><?php foreach (['#1a56db','#059669','#dc2626','#7c3aed','#ea580c','#db2777'] as $preset): ?><button type="button" class="h-8 w-8 rounded border" style="background:<?php echo $preset; ?>" onclick="document.getElementById('site_color_picker').value='<?php echo $preset; ?>';updateColorPreview();"></button><?php endforeach; ?></div>
                <div class="rounded border p-3"><div id="previewHeader" class="h-8 rounded" style="background:<?php echo htmlspecialchars((string) siteConfig('color', '#1a56db'), ENT_QUOTES, 'UTF-8'); ?>"></div><button id="previewButton" type="button" class="mt-2 rounded px-3 py-1 text-white" style="background:<?php echo htmlspecialchars((string) siteConfig('color', '#1a56db'), ENT_QUOTES, 'UTF-8'); ?>">Bouton</button></div>
                <label>Logo <input type="file" name="logo" accept="image/*"></label>
                <label>Favicon <input type="file" name="favicon" accept="image/x-icon,image/png"></label>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
        </section>

        <section id="security" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Sécurité</h2>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="security">
                <input type="password" name="old_password" class="w-full rounded border px-3 py-2" placeholder="Ancien mot de passe">
                <input type="password" name="new_password" class="w-full rounded border px-3 py-2" placeholder="Nouveau mot de passe">
                <input type="password" name="new_password_confirm" class="w-full rounded border px-3 py-2" placeholder="Confirmer le mot de passe">
                <input type="number" name="max_requests" class="w-full rounded border px-3 py-2" value="<?php echo (int) (defined('MAX_REQUESTS_PER_HOUR') ? MAX_REQUESTS_PER_HOUR : 50); ?>" placeholder="Limite requêtes / heure">
                <label><input type="checkbox" name="regen_secret" value="1"> Régénérer la clé secrète</label>
                <button class="rounded bg-primary px-4 py-2 text-white font-semibold">Enregistrer</button>
            </form>
            <h3 class="mt-6 font-semibold">Dernières connexions admin</h3>
            <table class="min-w-full text-xs mt-2"><thead><tr class="border-b"><th>Date</th><th>Détails</th></tr></thead><tbody><?php foreach ($adminLogins as $log): ?><tr class="border-b"><td><?php echo sanitize((string) ($log['created_at'] ?? '')); ?></td><td><?php echo sanitize(substr((string) ($log['details'] ?? ''), 0, 120)); ?></td></tr><?php endforeach; ?></tbody></table>
        </section>

        <section id="maintenance" class="rounded-xl border bg-white p-6">
            <h2 class="text-xl font-bold">Maintenance</h2>
            <form method="POST" class="space-y-3 mb-4" onsubmit="return confirm('Confirmer la réinitialisation des statistiques ?');">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="section" value="maintenance"><input type="hidden" name="reset_stats" value="1">
                <button class="rounded border px-4 py-2">Réinitialiser les statistiques</button>
            </form>
            <a href="export.php?format=sql" class="rounded border px-4 py-2 inline-block">Exporter la base (SQL)</a>
            <div class="mt-4 text-sm text-gray-600 space-y-1">
                <p>PHP : <?php echo sanitize($phpVersion); ?></p>
                <p>MySQL : <?php echo sanitize($dbVersion); ?></p>
                <p>Espace disque libre : <?php echo number_format($freeDisk / 1024 / 1024 / 1024, 2, ',', ' '); ?> Go</p>
                <p>Leads total : <?php echo number_format($totalLeads, 0, ',', ' '); ?></p>
                <p>Version : 1.0.0</p>
            </div>
        </section>
    </div>
</div>

<script>
function updateColorPreview(){
    const c=document.getElementById('site_color_picker').value;
    document.getElementById('previewHeader').style.background=c;
    document.getElementById('previewButton').style.background=c;
}
document.getElementById('site_color_picker')?.addEventListener('input', updateColorPreview);
document.getElementById('city_radius')?.addEventListener('input', e=>{document.getElementById('radius_label').textContent=e.target.value+' km'; if(window.zoneCircle){window.zoneCircle.setRadius(Number(e.target.value)*1000);}});
document.getElementById('testMapsKey')?.addEventListener('click',()=>{alert('Test visuel: si la carte ci-dessous se charge, la clé semble valide.');});

function initSettingsMap(){
    if(!window.google || !google.maps) return;
    const lat = parseFloat(document.getElementById('city_lat').value || '44.8378');
    const lng = parseFloat(document.getElementById('city_lng').value || '-0.5792');
    const radius = parseFloat(document.getElementById('city_radius').value || '30');
    const map = new google.maps.Map(document.getElementById('zoneMap'), {center:{lat,lng}, zoom:11});
    const marker = new google.maps.Marker({position:{lat,lng}, map});
    window.zoneCircle = new google.maps.Circle({map, center:{lat,lng}, radius: radius*1000, fillColor:'#1a56db', fillOpacity:0.15, strokeColor:'#1a56db', strokeWeight:2});

    const cityInput = document.getElementById('city_name');
    if(google.maps.places && cityInput){
        const ac = new google.maps.places.Autocomplete(cityInput, {types:['(cities)'], componentRestrictions:{country:'fr'}, fields:['geometry','name']});
        ac.addListener('place_changed', ()=>{
            const place = ac.getPlace();
            if(!place.geometry) return;
            const nlat = place.geometry.location.lat();
            const nlng = place.geometry.location.lng();
            document.getElementById('city_lat').value=nlat;
            document.getElementById('city_lng').value=nlng;
            map.setCenter({lat:nlat,lng:nlng});
            marker.setPosition({lat:nlat,lng:nlng});
            window.zoneCircle.setCenter({lat:nlat,lng:nlng});
        });
    }

    const points = <?php echo json_encode(array_map(static fn($c) => ['ville' => $c['ville'], 'distance' => $c['distance']], $cityPrices), JSON_UNESCAPED_UNICODE); ?>;
}
window.initSettingsMap = initSettingsMap;
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode((string) siteConfig('maps_key', '')); ?>&libraries=places&callback=initSettingsMap" async defer></script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
