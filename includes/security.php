<?php

require_once __DIR__ . '/database.php';

// Démarrer session sécurisée
function initSecureSession(): void
{
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.use_strict_mode', '1');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Régénérer session ID toutes les 30 min
    if (!isset($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }
}

// Générer token CSRF
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Input HTML caché CSRF
function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

// Vérifier token CSRF
function verifyCsrf(): bool
{
    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    return hash_equals($_SESSION['csrf_token'] ?? '', (string) $token);
}

// Rate limiting par IP (table rate_limits en DB)
function checkRateLimit(string $action, int $maxPerHour = 10): bool
{
    $db = Database::getConnection();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Nettoyer les anciennes entrées (> 1h)
    $db->prepare('DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)')->execute();

    // Compter les tentatives
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM rate_limits
         WHERE ip = ? AND action = ?
         AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)'
    );
    $stmt->execute([$ip, $action]);
    $count = (int) $stmt->fetchColumn();

    if ($count >= $maxPerHour) {
        return false; // Rate limit atteint
    }

    // Enregistrer cette tentative
    $db->prepare('INSERT INTO rate_limits (ip, action) VALUES (?, ?)')->execute([$ip, $action]);

    return true;
}

// Sanitizer les inputs
function clean(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function cleanEmail(string $email): string
{
    return (string) filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function cleanPhone(string $phone): string
{
    return (string) preg_replace('/[^0-9+]/', '', $phone);
}

function cleanInt($val): int
{
    return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
}

function cleanFloat($val): float
{
    return (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

// Headers de sécurité
function setSecurityHeaders(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://maps.googleapis.com https://cdn.tailwindcss.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https://*.googleapis.com https://*.gstatic.com; connect-src 'self' https://maps.googleapis.com;");
}

// Protection brute force login
function checkLoginAttempts(string $email): bool
{
    $db = Database::getConnection();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE (ip = ? OR email = ?)
         AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
    );
    $stmt->execute([$ip, $email]);

    return (int) $stmt->fetchColumn() < 5; // Max 5 tentatives / 15 min
}

function recordLoginAttempt(string $email, bool $success): void
{
    $db = Database::getConnection();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $db->prepare('INSERT INTO login_attempts (ip, email, success) VALUES (?, ?, ?)')
        ->execute([$ip, $email, (int) $success]);

    if ($success) {
        // Nettoyer les tentatives après un login réussi
        $db->prepare('DELETE FROM login_attempts WHERE email = ?')->execute([$email]);
    }
}


// Suivi activité utilisateur admin
function trackUserActivity(int $userId, string $page): void
{
    $db = Database::getConnection();

    $db->prepare(
        'UPDATE users
         SET last_page_visited = :page,
             last_activity = NOW(),
             is_online = 1
         WHERE id = :id'
    )->execute([
        'page' => mb_substr($page, 0, 255),
        'id' => $userId,
    ]);

    $sessionId = session_id();
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $db->prepare(
        'INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, page_visited, is_active)
         VALUES (:user_id, :session_id, :ip_address, :user_agent, :page_visited, 1)
         ON DUPLICATE KEY UPDATE
            page_visited = VALUES(page_visited),
            ip_address = VALUES(ip_address),
            user_agent = VALUES(user_agent),
            is_active = 1'
    )->execute([
        'user_id' => $userId,
        'session_id' => $sessionId,
        'ip_address' => $ip,
        'user_agent' => $userAgent,
        'page_visited' => mb_substr($page, 0, 255),
    ]);
}

function logoutUser(int $userId): void
{
    $db = Database::getConnection();
    $sessionId = session_id();

    $db->prepare('UPDATE users SET is_online = 0 WHERE id = :id')->execute(['id' => $userId]);

    $db->prepare(
        'UPDATE user_sessions
         SET is_active = 0,
             logout_at = NOW()
         WHERE user_id = :user_id
           AND session_id = :session_id
           AND is_active = 1'
    )->execute([
        'user_id' => $userId,
        'session_id' => $sessionId,
    ]);
}

function refreshOnlineStatuses(int $inactiveThresholdMinutes = 15): void
{
    $db = Database::getConnection();
    $db->prepare(
        'UPDATE users
         SET is_online = 0
         WHERE is_online = 1
           AND (last_activity IS NULL OR last_activity < DATE_SUB(NOW(), INTERVAL :minutes MINUTE))'
    )->execute(['minutes' => max(1, $inactiveThresholdMinutes)]);
}
