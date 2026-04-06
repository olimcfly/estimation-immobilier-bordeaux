<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/mailer.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function siteConfig(string $key, $default = '')
{
    $constants = [
        'name' => defined('SITE_NAME') ? SITE_NAME : 'EstimIA',
        'url' => defined('SITE_URL') ? SITE_URL : '',
        'color' => defined('SITE_COLOR') ? SITE_COLOR : '#1a56db',
        'phone' => defined('SITE_PHONE') ? SITE_PHONE : '',
        'city' => defined('CITY_NAME') ? CITY_NAME : '',
        'city_lat' => defined('CITY_LAT') ? CITY_LAT : 44.8378,
        'city_lng' => defined('CITY_LNG') ? CITY_LNG : -0.5792,
        'radius' => defined('CITY_RADIUS_KM') ? CITY_RADIUS_KM : 30,
        'admin_email' => defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '',
        'maps_key' => defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '',
    ];

    return $constants[$key] ?? $default;
}

function sanitize($input): string
{
    return htmlspecialchars(trim((string) $input), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function formatPrice($number): string
{
    return number_format((float) $number, 0, ',', ' ') . ' €';
}

function getClientIp(): string
{
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ];

    foreach ($keys as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $raw = explode(',', (string) $_SERVER[$key]);
        $ip = trim($raw[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '0.0.0.0';
}

function generateCSRFToken(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function verifyCSRFToken(?string $token): bool
{
    if (!isset($_SESSION['csrf_token']) || !is_string($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function calculerEstimation(int $surface, string $type_bien, string $ville): array
{
    $types = ['appartement', 'maison', 'studio', 'terrain'];
    $type = in_array($type_bien, $types, true) ? $type_bien : 'appartement';
    $surface = max(1, $surface);

    $prixM2Column = 'prix_m2_' . $type;
    $prixM2 = null;
    $tendance = 0.00;
    $nbTransactions = 0;

    $fiabilite = 'Élevée';
    $messageZone = '';

    try {
        $pdo = Database::getConnection();

        $villesDansRayon = getVillesDansRayon($pdo);
        $villesAutorisees = array_map(static fn(array $row): string => (string) ($row['ville'] ?? ''), $villesDansRayon);
        if ($ville !== '' && !in_array($ville, $villesAutorisees, true)) {
            $fiabilite = 'Faible';
            $messageZone = 'Cette adresse est en dehors de notre zone d’expertise';
        }

        $stmtVille = $pdo->prepare("SELECT {$prixM2Column} AS prix_m2, tendance_annuelle, nb_transactions FROM villes_prix WHERE ville = :ville LIMIT 1");
        $stmtVille->execute(['ville' => $ville]);
        $row = $stmtVille->fetch();

        if ($row && !empty($row['prix_m2'])) {
            $prixM2 = (int) $row['prix_m2'];
            $tendance = (float) ($row['tendance_annuelle'] ?? 0);
            $nbTransactions = (int) ($row['nb_transactions'] ?? 0);
        } else {
            $stmtMoyenne = $pdo->query("SELECT AVG({$prixM2Column}) AS moyenne_prix_m2, AVG(tendance_annuelle) AS moyenne_tendance, AVG(nb_transactions) AS moyenne_transactions FROM villes_prix");
            $moyenne = $stmtMoyenne->fetch();

            $prixM2 = (int) round((float) ($moyenne['moyenne_prix_m2'] ?? 2500));
            $tendance = round((float) ($moyenne['moyenne_tendance'] ?? 0), 2);
            $nbTransactions = (int) round((float) ($moyenne['moyenne_transactions'] ?? 0));
        }
    } catch (Throwable $e) {
        $fallback = [
            'appartement' => 3400,
            'maison' => 3200,
            'studio' => 3800,
            'terrain' => 900,
        ];

        $prixM2 = $fallback[$type] ?? 3000;
        $tendance = 0.00;
        $nbTransactions = 0;
    }

    $prixEstime = (int) round($surface * $prixM2);
    $prixBas = (int) round($prixEstime * 0.92);
    $prixHaut = (int) round($prixEstime * 1.08);

    return [
        'prix_estime' => $prixEstime,
        'prix_bas' => $prixBas,
        'prix_haut' => $prixHaut,
        'prix_m2' => (int) $prixM2,
        'tendance' => $tendance,
        'nb_transactions' => $nbTransactions,
        'fiabilite' => $fiabilite,
        'message_zone' => $messageZone,
    ];
}

function getVillesDansRayon(PDO $pdo): array
{
    $sql = 'SELECT *,
            (6371 * acos(cos(radians(:lat1)) * cos(radians(lat)) * cos(radians(lng) - radians(:lng))
            + sin(radians(:lat2)) * sin(radians(lat)))) AS distance
            FROM villes_prix
            HAVING distance <= :radius
            ORDER BY distance';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'lat1' => (float) siteConfig('city_lat', 44.8378),
        'lng' => (float) siteConfig('city_lng', -0.5792),
        'lat2' => (float) siteConfig('city_lat', 44.8378),
        'radius' => (float) siteConfig('radius', 30),
    ]);
    return $stmt->fetchAll();
}

function calculerLeadScore(array $estimation): int
{
    $score = 0;

    $hasAdresseComplete = !empty($estimation['adresse_complete']);
    $hasGps = !empty($estimation['latitude']) && !empty($estimation['longitude']);
    if ($hasAdresseComplete && $hasGps) {
        $score += 10;
    }

    if (!empty($estimation['telephone'])) {
        $score += 15;
    }

    if (!empty($estimation['email'])) {
        $score += 10;
    }

    if ((int) ($estimation['rdv_pris'] ?? 0) === 1) {
        $score += 20;
    }

    $hasDetailedLead = !empty($estimation['lead_detaille']) || !empty($estimation['has_detailed']) || !empty($estimation['projet']) || !empty($estimation['delai_vente']);
    if ($hasDetailedLead) {
        $score += 15;
    }

    $projet = (string) ($estimation['projet'] ?? '');
    if (in_array($projet, ['vendre', 'urgent'], true)) {
        $score += 10;
    }

    $delai = (string) ($estimation['delai_vente'] ?? '');
    if (in_array($delai, ['urgent', '3_mois'], true)) {
        $score += 10;
    }

    if (strtolower((string) ($estimation['utm_source'] ?? '')) === 'google') {
        $score += 5;
    }

    $budget = (string) ($estimation['budget_estimation'] ?? '');
    $prixEstime = (int) ($estimation['prix_estime'] ?? 0);
    if (in_array($budget, ['300k_500k', 'plus_500k'], true) || $prixEstime > 300000) {
        $score += 5;
    }

    return max(0, min(100, $score));
}

function getLeadColor(int $score): string
{
    if ($score <= 25) {
        return 'red';
    }

    if ($score <= 50) {
        return 'orange';
    }

    if ($score <= 75) {
        return 'yellow';
    }

    return 'green';
}

function envoyerNotification(string $type, array $data): void
{
    $sujet = '';
    $corps = '';

    switch ($type) {
        case 'new_estimation':
            if (defined('NOTIF_NEW_ESTIMATION') && !NOTIF_NEW_ESTIMATION) {
                return;
            }
            $sujet = '[' . siteConfig('name', 'EstimIA') . '] Nouvelle estimation reçue';
            $corps = buildEmailNotification('estimation', $data);
            break;
        case 'new_rdv':
            if (defined('NOTIF_NEW_RDV') && !NOTIF_NEW_RDV) {
                return;
            }
            $sujet = '[' . siteConfig('name', 'EstimIA') . '] Nouveau RDV pris !';
            $corps = buildEmailNotification('rdv', $data);
            break;
        case 'hot_lead':
            if (defined('NOTIF_HOT_LEAD') && !NOTIF_HOT_LEAD) {
                return;
            }
            $sujet = '🔥 [' . siteConfig('name', 'EstimIA') . '] Lead chaud détecté !';
            $corps = buildEmailNotification('hot_lead', $data);
            break;
        default:
            return;
    }

    envoyerEmail((string) siteConfig('admin_email', ''), $sujet, $corps);
}

function envoyerEmail(string $to, string $subject, string $htmlBody): void
{
    if ($to === '') {
        return;
    }

    $mailer = new Mailer();
    $mailer->send($to, $subject, $htmlBody, strip_tags($htmlBody));
}

function buildEmailNotification(string $type, array $data): string
{
    $templateMap = [
        'estimation' => __DIR__ . '/email_templates/estimation_notification.php',
        'rdv' => __DIR__ . '/email_templates/rdv_notification.php',
        'hot_lead' => __DIR__ . '/email_templates/hot_lead_notification.php',
        'weekly' => __DIR__ . '/email_templates/weekly_report.php',
        'welcome' => __DIR__ . '/email_templates/welcome.php',
    ];

    $template = $templateMap[$type] ?? null;
    if ($template === null || !is_file($template)) {
        return '';
    }

    return (string) include $template;
}

function updateConfig($key, $value): bool
{
    $configFile = __DIR__ . '/../config/config.php';
    if (!is_file($configFile)) {
        return false;
    }

    $content = (string) file_get_contents($configFile);
    $pattern = null;
    $replacement = null;

    if (is_string($value)) {
        $pattern = "/(define\\('" . preg_quote((string) $key, '/') . "',\\s*')([^']*)('\\);)/";
        $replacement = '${1}' . addslashes($value) . '${3}';
    } elseif (is_numeric($value)) {
        $pattern = "/(define\\('" . preg_quote((string) $key, '/') . "',\\s*)([^)]*)([\\s]*\\);)/";
        $replacement = '${1}' . $value . '${3}';
    } elseif (is_bool($value)) {
        $pattern = "/(define\\('" . preg_quote((string) $key, '/') . "',\\s*)(true|false)([\\s]*\\);)/";
        $replacement = '${1}' . ($value ? 'true' : 'false') . '${3}';
    }

    if ($pattern === null || $replacement === null) {
        return false;
    }

    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent && $newContent !== $content) {
        @copy($configFile, $configFile . '.bak');
        file_put_contents($configFile, $newContent);
        return true;
    }

    return false;
}

function getStatutBadge(string $statut): string
{
    $map = [
        'nouveau' => 'bg-blue-100 text-blue-700',
        'contacte' => 'bg-yellow-100 text-yellow-700',
        'qualifie' => 'bg-orange-100 text-orange-700',
        'en_negociation' => 'bg-purple-100 text-purple-700',
        'converti' => 'bg-green-100 text-green-700',
        'perdu' => 'bg-red-100 text-red-700',
    ];

    $safeStatut = sanitize($statut);
    $class = $map[$statut] ?? 'bg-gray-100 text-gray-700';

    return '<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ' . $class . '">' . $safeStatut . '</span>';
}

function calculerEstimationDetaillee(array $estimation, array $details): array
{
    $surface = max(1, (int) ($estimation['surface'] ?? 0));
    $prixM2Base = max(500, (float) ($estimation['prix_m2'] ?? 0));
    $coefficient = 1.0;

    $etatCoeff = [
        'neuf' => 1.15,
        'tres_bon' => 1.08,
        'bon' => 1.00,
        'a_rafraichir' => 0.90,
        'a_renover' => 0.80,
    ];
    $coefficient *= $etatCoeff[$details['etat_general'] ?? 'bon'] ?? 1.00;

    $dpe = (string) ($details['dpe'] ?? 'non_renseigne');
    if (in_array($dpe, ['A', 'B'], true)) {
        $coefficient *= 1.05;
    } elseif (in_array($dpe, ['E'], true)) {
        $coefficient *= 0.95;
    } elseif (in_array($dpe, ['F', 'G'], true)) {
        $coefficient *= 0.88;
    }

    $bonusPercent = 0.0;
    $bonusPercent += !empty($details['balcon']) ? 0.03 : 0;
    $bonusPercent += !empty($details['terrasse']) ? 0.05 : 0;
    $bonusPercent += !empty($details['jardin']) ? 0.08 : 0;
    $bonusPercent += !empty($details['piscine']) ? 0.10 : 0;
    $bonusPercent += !empty($details['parking']) ? 0.05 : 0;
    $bonusPercent += !empty($details['garage']) ? 0.07 : 0;
    $bonusPercent += !empty($details['cave']) ? 0.02 : 0;
    $coefficient *= (1 + $bonusPercent);

    $etage = (int) ($details['etage'] ?? 0);
    $nbEtagesImmeuble = (int) ($details['nb_etages_immeuble'] ?? 0);
    $estMaison = !empty($details['maison_individuelle']);

    if (!$estMaison && $etage > 3) {
        $coefficient *= 0.97;
    }
    if (!$estMaison && $etage > 0 && $nbEtagesImmeuble > 0 && $etage === $nbEtagesImmeuble) {
        $coefficient *= 1.05;
    }
    if (!$estMaison && $etage === 0) {
        $coefficient *= 0.95;
    }
    if (!$estMaison && $etage === 1) {
        $coefficient *= 0.98;
    }

    $anneeConstruction = (int) ($details['annee_construction'] ?? 0);
    if ($anneeConstruction > 0) {
        $age = (int) date('Y') - $anneeConstruction;
        if ($age < 5) {
            $coefficient *= 1.05;
        }
        if ($age > 50) {
            $coefficient *= 0.97;
        }
    }

    $prixEstime = (int) round($surface * $prixM2Base * $coefficient);
    $prixBas = (int) round($prixEstime * 0.94);
    $prixHaut = (int) round($prixEstime * 1.06);

    return [
        'prix_estime' => $prixEstime,
        'prix_bas' => $prixBas,
        'prix_haut' => $prixHaut,
        'coefficient' => round($coefficient, 4),
    ];
}

function formatDateRelative(string $datetime): string
{
    $timestamp = strtotime($datetime);
    if ($timestamp === false) {
        return '';
    }

    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 3600) {
        $minutes = max(1, (int) floor($diff / 60));
        return 'il y a ' . $minutes . ' min';
    }

    if ($diff < 86400) {
        $hours = (int) floor($diff / 3600);
        return 'il y a ' . $hours . 'h';
    }

    if ($diff < 172800) {
        return 'hier';
    }

    if ($diff < 604800) {
        $days = (int) floor($diff / 86400);
        return 'il y a ' . $days . 'j';
    }

    return date('d M', $timestamp);
}

function getPercentChange(float $current, float $previous): float
{
    if ($previous === 0.0) {
        return $current > 0 ? 100.0 : 0.0;
    }

    return round((($current - $previous) / abs($previous)) * 100, 1);
}
