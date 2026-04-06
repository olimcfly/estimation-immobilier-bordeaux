<?php

declare(strict_types=1);

require_once __DIR__ . '/security.php';

initSecureSession();

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Accès administrateur requis.');
}

try {
    $db = Database::getConnection();
    refreshOnlineStatuses();

    $adminEmail = (string) ($_SESSION['admin_email'] ?? '');
    if ($adminEmail !== '') {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $adminEmail]);
        $linkedUserId = (int) $stmt->fetchColumn();

        if ($linkedUserId > 0) {
            $currentPage = (string) ($_SERVER['REQUEST_URI'] ?? '/admin/');
            trackUserActivity($linkedUserId, $currentPage);
            $_SESSION['linked_user_id'] = $linkedUserId;
        }
    }
} catch (Throwable $exception) {
    // no-op: ne bloque jamais l'accès admin en cas d'erreur de tracking
}
