<?php

declare(strict_types=1);

$lockPath = __DIR__ . '/../installed.lock';
if (!file_exists($lockPath)) {
    header('Location: /install/index.php');
    exit;
}

require_once __DIR__ . '/../config/config.php';

if (!defined('INSTALLED') || !INSTALLED) {
    header('Location: /install/index.php');
    exit;
}
