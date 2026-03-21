<?php

declare(strict_types=1);

namespace App\Services;

final class SmtpLogger
{
    private static string $logFile = __DIR__ . '/../../logs/smtp_test.log';

    /**
     * Log an SMTP test result.
     */
    public static function log(string $host, int $port, string $result, string $error = ''): void
    {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'host'      => $host,
            'port'      => $port,
            'result'    => $result,
            'error'     => $error,
        ];

        $line = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

        file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Read all log entries.
     *
     * @return array<int, array<string, string|int>>
     */
    public static function readAll(): array
    {
        if (!is_file(self::$logFile)) {
            return [];
        }

        $lines = array_filter(explode("\n", file_get_contents(self::$logFile)));

        return array_map(static fn(string $line) => json_decode($line, true) ?? [], $lines);
    }

    /**
     * Clear the log file.
     */
    public static function clear(): void
    {
        if (is_file(self::$logFile)) {
            file_put_contents(self::$logFile, '');
        }
    }
}
