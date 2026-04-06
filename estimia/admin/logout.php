<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

session_destroy();

header('Location: login.php');
exit;
