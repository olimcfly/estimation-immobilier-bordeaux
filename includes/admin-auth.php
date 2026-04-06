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
        $stmt = $db->prepare('SELECT id, role, actif FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $adminEmail]);
        $linkedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($linkedUser) && (int) ($linkedUser['id'] ?? 0) > 0) {
            if ((int) ($linkedUser['actif'] ?? 0) !== 1) {
                session_destroy();
                http_response_code(403);
                exit('Compte utilisateur désactivé.');
            }

            $linkedUserId = (int) $linkedUser['id'];
            $currentPage = (string) ($_SERVER['REQUEST_URI'] ?? '/admin/');
            trackUserActivity($linkedUserId, $currentPage);
            $_SESSION['linked_user_id'] = $linkedUserId;
            $_SESSION['user_role'] = (string) ($linkedUser['role'] ?? 'agent');
        }
    }
} catch (Throwable $exception) {
    // no-op: ne bloque jamais l'accès admin en cas d'erreur de tracking
}
