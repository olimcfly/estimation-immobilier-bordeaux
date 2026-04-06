<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$action = (string) ($_POST['action'] ?? $_GET['action'] ?? '');

$respond = static function (bool $success, string $message = '', array $extra = []): void {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

$translatePdoError = static function (string $message): string {
    $lower = strtolower($message);
    if (str_contains($lower, 'access denied')) {
        return 'Identifiants incorrects. Vérifiez le mot de passe.';
    }
    if (str_contains($lower, 'unknown database')) {
        return 'Base de données introuvable. Créez-la d\'abord dans cPanel.';
    }
    if (str_contains($lower, 'connection refused')) {
        return 'Impossible de se connecter au serveur MySQL.';
    }
    return 'Erreur base de données : ' . $message;
};

$getInstallData = static function (): array {
    return is_array($_SESSION['install_data'] ?? null) ? $_SESSION['install_data'] : [];
};

$setInstallData = static function (array $data): void {
    $_SESSION['install_data'] = $data;
};

$getPdo = static function (array $db): PDO {
    $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=utf8mb4';
    return new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
};

$haversine = static function (float $lat1, float $lon1, float $lat2, float $lon2): float {
    $earthRadius = 6371.0;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
};

$cityReference = require __DIR__ . '/data/cities_france.php';

if ($action === 'test_db') {
    $host = trim((string) ($_POST['host'] ?? 'localhost'));
    $dbName = trim((string) ($_POST['db_name'] ?? ''));
    $dbUser = trim((string) ($_POST['db_user'] ?? ''));
    $dbPass = (string) ($_POST['db_pass'] ?? '');

    try {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
        new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $respond(true, '✓ Connexion réussie !');
    } catch (Throwable $e) {
        $respond(false, $translatePdoError($e->getMessage()));
    }
}

if ($action === 'test_email') {
    $to = trim((string) ($_POST['notification_email'] ?? ''));
    $siteName = trim((string) ($_POST['site_name'] ?? 'EstimIA'));
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $respond(false, 'Adresse email invalide.');
    }

    $subject = 'Test email EstimIA';
    $message = "Bonjour,\n\nCet email confirme que la configuration email fonctionne.\n\n- {$siteName}";
    $headers = 'From: ' . $siteName . ' <no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";

    $sent = @mail($to, $subject, $message, $headers);
    if ($sent) {
        $respond(true, '✓ Email envoyé ! Vérifiez votre boîte.');
    }

    $respond(false, 'Échec de l\'envoi. Vérifiez la configuration mail du serveur.');
}

if ($action === 'install_step') {
    $step = (string) ($_POST['step'] ?? '');
    $data = $getInstallData();

    if ($step === 'connect') {
        $data['db'] = [
            'host' => trim((string) ($_POST['host'] ?? 'localhost')),
            'name' => trim((string) ($_POST['db_name'] ?? '')),
            'user' => trim((string) ($_POST['db_user'] ?? '')),
            'pass' => (string) ($_POST['db_pass'] ?? ''),
        ];

        $data['site'] = [
            'city_name' => trim((string) ($_POST['city_name'] ?? '')),
            'city_lat' => (float) ($_POST['city_lat'] ?? 0),
            'city_lng' => (float) ($_POST['city_lng'] ?? 0),
            'city_radius' => (int) ($_POST['city_radius'] ?? 30),
            'notification_email' => trim((string) ($_POST['notification_email'] ?? '')),
            'site_name' => trim((string) ($_POST['site_name'] ?? 'EstimIA')),
            'site_url' => trim((string) ($_POST['site_url'] ?? '')),
            'site_color' => trim((string) ($_POST['site_color'] ?? '#1a56db')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'smtp_host' => trim((string) ($_POST['smtp_host'] ?? '')),
            'smtp_port' => (int) ($_POST['smtp_port'] ?? 465),
            'smtp_user' => trim((string) ($_POST['smtp_user'] ?? '')),
            'smtp_pass' => (string) ($_POST['smtp_pass'] ?? ''),
            'smtp_secure' => trim((string) ($_POST['smtp_secure'] ?? 'ssl')),
            'smtp_from' => trim((string) ($_POST['smtp_from'] ?? '')),
            'notif_new_estimation' => !empty($_POST['notif_new_estimation']) ? 'true' : 'false',
            'notif_new_rdv' => !empty($_POST['notif_new_rdv']) ? 'true' : 'false',
            'notif_hot_lead' => !empty($_POST['notif_hot_lead']) ? 'true' : 'false',
            'notif_weekly' => !empty($_POST['notif_weekly']) ? 'true' : 'false',
        ];

        $data['admin'] = [
            'name' => trim((string) ($_POST['admin_name'] ?? '')),
            'email' => trim((string) ($_POST['admin_email'] ?? '')),
            'password' => (string) ($_POST['admin_password'] ?? ''),
        ];

        try {
            $pdo = $getPdo($data['db']);
            $pdo = null;
        } catch (Throwable $e) {
            $respond(false, $translatePdoError($e->getMessage()));
        }

        $setInstallData($data);
        $respond(true, 'Connexion établie');
    }

    if (empty($data['db'])) {
        $respond(false, 'Session d\'installation expirée. Recommencez.');
    }

    try {
        $pdo = $getPdo($data['db']);
    } catch (Throwable $e) {
        $respond(false, $translatePdoError($e->getMessage()));
    }

    if ($step === 'tables') {
        $sqlPath = dirname(__DIR__) . '/sql/install.sql';
        if (!is_file($sqlPath)) {
            $respond(false, 'Fichier SQL introuvable.');
        }

        $sql = (string) file_get_contents($sqlPath);
        preg_match_all('/CREATE TABLE IF NOT EXISTS[^;]+;/i', $sql, $matches);
        $count = 0;
        foreach ($matches[0] as $statement) {
            $pdo->exec($statement);
            $count++;
        }
        $respond(true, $count . ' tables créées', ['count' => $count]);
    }

    if ($step === 'seed') {
        $site = $data['site'] ?? [];
        $cityName = (string) ($site['city_name'] ?? 'Bordeaux');
        $cityLat = (float) ($site['city_lat'] ?? 44.8378);
        $cityLng = (float) ($site['city_lng'] ?? -0.5792);
        $radius = (int) ($site['city_radius'] ?? 30);

        $inRange = [];
        foreach ($cityReference as $city) {
            $distance = $haversine($cityLat, $cityLng, (float) $city['lat'], (float) $city['lng']);
            if ($distance <= $radius || $city['ville'] === $cityName) {
                $city['distance'] = round($distance, 1);
                $inRange[] = $city;
            }
        }

        $already = false;
        foreach ($inRange as $city) {
            if ($city['ville'] === $cityName) {
                $already = true;
                break;
            }
        }
        if (!$already) {
            $inRange[] = [
                'ville' => $cityName,
                'lat' => $cityLat,
                'lng' => $cityLng,
                'code_postal' => '00000',
                'departement' => 'N/A',
                'region' => 'N/A',
                'prix_m2_appartement' => 3500,
                'prix_m2_maison' => 3000,
                'prix_m2_studio' => 3850,
                'prix_m2_terrain' => 1050,
                'tendance' => 0.0,
                'population' => 10000,
                'distance' => 0.0,
            ];
        }

        $data['seed_cities'] = $inRange;
        $setInstallData($data);
        $respond(true, count($inRange) . ' villes ciblées');
    }

    if ($step === 'prices') {
        $seedCities = $data['seed_cities'] ?? $cityReference;

        $stmt = $pdo->prepare(
            'INSERT INTO villes_prix (ville, code_postal, departement, region, lat, lng, prix_m2_appartement, prix_m2_maison, prix_m2_studio, prix_m2_terrain, tendance_annuelle, nb_transactions, population, distance_centre)
             VALUES (:ville, :code_postal, :departement, :region, :lat, :lng, :appartement, :maison, :studio, :terrain, :tendance, :transactions, :population, :distance)
             ON DUPLICATE KEY UPDATE
             code_postal = VALUES(code_postal),
             departement = VALUES(departement),
             region = VALUES(region),
             lat = VALUES(lat),
             lng = VALUES(lng),
             prix_m2_appartement = VALUES(prix_m2_appartement),
             prix_m2_maison = VALUES(prix_m2_maison),
             prix_m2_studio = VALUES(prix_m2_studio),
             prix_m2_terrain = VALUES(prix_m2_terrain),
             tendance_annuelle = VALUES(tendance_annuelle),
             nb_transactions = VALUES(nb_transactions),
             population = VALUES(population),
             distance_centre = VALUES(distance_centre)'
        );

        foreach ($seedCities as $city) {
            $population = max(1000, (int) ($city['population'] ?? 10000));
            $stmt->execute([
                'ville' => (string) $city['ville'],
                'code_postal' => (string) ($city['code_postal'] ?? ''),
                'departement' => (string) ($city['departement'] ?? ''),
                'region' => (string) ($city['region'] ?? ''),
                'lat' => (float) ($city['lat'] ?? 0),
                'lng' => (float) ($city['lng'] ?? 0),
                'appartement' => (int) ($city['prix_m2_appartement'] ?? 3200),
                'maison' => (int) ($city['prix_m2_maison'] ?? 2700),
                'studio' => (int) ($city['prix_m2_studio'] ?? 3500),
                'terrain' => (int) ($city['prix_m2_terrain'] ?? 960),
                'tendance' => (float) ($city['tendance'] ?? 0),
                'transactions' => max(10, (int) ($population * 0.005 * (mt_rand(70, 130) / 100))),
                'population' => $population,
                'distance' => (float) ($city['distance'] ?? 0),
            ]);
        }

        $respond(true, 'Prix par ville configurés');
    }

    if ($step === 'admin') {
        $admin = $data['admin'] ?? [];
        $name = trim((string) ($admin['name'] ?? 'Administrateur'));
        $email = trim((string) ($admin['email'] ?? 'admin@example.com'));
        $password = (string) ($admin['password'] ?? 'Admin123!');

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmtAdmin = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (:email, :hash)');
        $stmtAdmin->execute(['email' => $email, 'hash' => $passwordHash]);

        $nameParts = preg_split('/\s+/', $name) ?: [];
        $prenom = $nameParts[0] ?? 'Admin';
        $nom = implode(' ', array_slice($nameParts, 1));
        if ($nom === '') {
            $nom = 'EstimIA';
        }

        $stmtAgent = $pdo->prepare(
            'INSERT INTO agents (nom, prenom, email, telephone, secteur_geographique, actif)
             VALUES (:nom, :prenom, :email, :telephone, :secteur, 1)'
        );
        $stmtAgent->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => (string) (($data['site']['phone'] ?? '') ?: '0000000000'),
            'secteur' => json_encode(['zone' => $data['site']['city_name'] ?? 'France'], JSON_UNESCAPED_UNICODE),
        ]);

        $respond(true, 'Compte administrateur créé');
    }

    if ($step === 'config') {
        $templatePath = __DIR__ . '/templates/config.template.php';
        $template = (string) file_get_contents($templatePath);
        $site = $data['site'] ?? [];

        $replacements = [
            '{{DB_HOST}}' => (string) ($data['db']['host'] ?? 'localhost'),
            '{{DB_NAME}}' => (string) ($data['db']['name'] ?? ''),
            '{{DB_USER}}' => (string) ($data['db']['user'] ?? ''),
            '{{DB_PASS}}' => (string) ($data['db']['pass'] ?? ''),
            '{{SITE_NAME}}' => (string) ($site['site_name'] ?? 'EstimIA'),
            '{{SITE_URL}}' => (string) ($site['site_url'] ?? ''),
            '{{SITE_COLOR}}' => (string) ($site['site_color'] ?? '#1a56db'),
            '{{CITY_NAME}}' => (string) ($site['city_name'] ?? 'Bordeaux'),
            '{{CITY_LAT}}' => (string) ($site['city_lat'] ?? '44.8378'),
            '{{CITY_LNG}}' => (string) ($site['city_lng'] ?? '-0.5792'),
            '{{CITY_RADIUS}}' => (string) ($site['city_radius'] ?? '30'),
            '{{SMTP_HOST}}' => (string) ($site['smtp_host'] ?? ''),
            '{{SMTP_PORT}}' => (string) (($site['smtp_port'] ?? 465)),
            '{{SMTP_USER}}' => (string) ($site['smtp_user'] ?? ''),
            '{{SMTP_PASS}}' => (string) ($site['smtp_pass'] ?? ''),
            '{{SMTP_SECURE}}' => (string) ($site['smtp_secure'] ?? 'ssl'),
            '{{SMTP_FROM}}' => (string) ($site['smtp_from'] ?? ''),
            '{{ADMIN_EMAIL}}' => (string) (($data['admin']['email'] ?? 'admin@example.com')),
            '{{NOTIF_NEW_ESTIMATION}}' => (string) ($site['notif_new_estimation'] ?? 'true'),
            '{{NOTIF_NEW_RDV}}' => (string) ($site['notif_new_rdv'] ?? 'true'),
            '{{NOTIF_HOT_LEAD}}' => (string) ($site['notif_hot_lead'] ?? 'true'),
            '{{NOTIF_WEEKLY}}' => (string) ($site['notif_weekly'] ?? 'false'),
            '{{PHONE}}' => (string) ($site['phone'] ?? ''),
            '{{INSTALL_DATE}}' => date('Y-m-d H:i:s'),
            '{{SECRET_KEY}}' => bin2hex(random_bytes(32)),
        ];

        $content = strtr($template, $replacements);
        $configPath = dirname(__DIR__) . '/config/config.php';
        if (file_put_contents($configPath, $content) === false) {
            $respond(false, 'Impossible d\'écrire config/config.php');
        }
        @chmod($configPath, 0644);

        $respond(true, 'Fichier de configuration généré');
    }

    if ($step === 'email') {
        $site = $data['site'] ?? [];
        $admin = $data['admin'] ?? [];
        $to = (string) ($admin['email'] ?? '');
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $subject = 'EstimIA installé avec succès !';
            $message = '<html><body style="font-family:Arial,sans-serif;color:#1f2937">'
                . '<h2 style="color:#1a56db">Votre site d\'estimation est prêt</h2>'
                . '<p>Ville cible : <strong>' . htmlspecialchars((string) ($site['city_name'] ?? '')) . '</strong></p>'
                . '<p>Rayon : <strong>' . htmlspecialchars((string) ($site['city_radius'] ?? '30')) . ' km</strong></p>'
                . '<p><a href="' . htmlspecialchars((string) (($site['site_url'] ?? '') . '/admin/')) . '">Accéder à mon tableau de bord</a></p>'
                . '</body></html>';
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= 'From: ' . (($site['site_name'] ?? 'EstimIA') . ' <no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n");
            @mail($to, $subject, $message, $headers);
        }
        $respond(true, 'Email de bienvenue envoyé');
    }

    if ($step === 'secure') {
        $lockPath = dirname(__DIR__) . '/installed.lock';
        $lockContent = date('c') . '|' . hash('sha256', bin2hex(random_bytes(32)));
        file_put_contents($lockPath, $lockContent);

        $configPath = dirname(__DIR__) . '/config/config.php';
        @chmod($configPath, 0644);

        $htaccessTemplatePath = __DIR__ . '/templates/htaccess.template';
        $htaccessTemplate = (string) file_get_contents($htaccessTemplatePath);
        file_put_contents(dirname(__DIR__) . '/.htaccess', $htaccessTemplate);

        $respond(true, 'Installation sécurisée');
    }

    if ($step === 'finalize') {
        unset($_SESSION['install_data']);
        $respond(true, 'Installation finalisée', ['redirect' => '../admin/']);
    }

    $respond(false, 'Étape inconnue.');
}

$respond(false, 'Action invalide.');
