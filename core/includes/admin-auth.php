<?php

declare(strict_types=1);

require_once __DIR__ . '/security.php';

initSecureSession();

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Accès administrateur requis.');
}
