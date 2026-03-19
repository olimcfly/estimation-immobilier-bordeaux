<?php

declare(strict_types=1);

require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Database;

try {
    Database::connection();
    echo 'DB OK';
} catch (\Throwable $exception) {
    http_response_code(500);
    echo 'DB ERROR: ' . $exception->getMessage();
}
