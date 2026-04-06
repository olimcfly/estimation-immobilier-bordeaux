<?php

declare(strict_types=1);

if (!function_exists('adminLogAction')) {
    function adminLogAction(string $action): void
    {
        $logDirectory = __DIR__ . '/../logs';
        if (!is_dir($logDirectory)) {
            @mkdir($logDirectory, 0775, true);
        }

        $actor = isset($_SESSION['admin_email']) && is_string($_SESSION['admin_email']) && $_SESSION['admin_email'] !== ''
            ? $_SESSION['admin_email']
            : 'admin_inconnu';

        $line = sprintf(
            "%s - %s - %s\n",
            date('Y-m-d H:i:s'),
            $actor,
            trim($action)
        );

        @file_put_contents($logDirectory . '/admin_actions.log', $line, FILE_APPEND);
    }
}
