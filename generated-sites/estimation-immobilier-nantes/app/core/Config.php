<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $data = [];

    public static function load(string $path): void
    {
        if (!is_file($path)) {
            throw new \RuntimeException('Config file not found: ' . $path);
        }

        self::$data = require $path;

        // Apply SMTP overrides from admin panel if they exist
        self::loadSmtpOverrides();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Load SMTP overrides saved from admin panel.
     */
    private static function loadSmtpOverrides(): void
    {
        $overridePath = dirname(__DIR__, 2) . '/config/smtp_override.json';
        if (!is_file($overridePath)) {
            return;
        }

        $json = file_get_contents($overridePath);
        if ($json === false) {
            return;
        }

        $overrides = json_decode($json, true);
        if (!is_array($overrides)) {
            return;
        }

        $map = [
            'smtp_host' => 'smtp_host',
            'smtp_port' => 'smtp_port',
            'smtp_user' => 'smtp_user',
            'smtp_pass' => 'smtp_pass',
            'smtp_encryption' => 'smtp_encryption',
            'from' => 'from',
            'from_name' => 'from_name',
        ];

        foreach ($map as $jsonKey => $configKey) {
            if (isset($overrides[$jsonKey]) && $overrides[$jsonKey] !== '') {
                $value = $overrides[$jsonKey];
                if ($jsonKey === 'smtp_port') {
                    $value = (int) $value;
                }
                self::$data['mail'][$configKey] = $value;
            }
        }
    }

    /**
     * Get the path to the SMTP override file.
     */
    public static function getSmtpOverridePath(): string
    {
        return dirname(__DIR__, 2) . '/config/smtp_override.json';
    }

    /**
     * Save SMTP overrides from admin panel.
     */
    public static function saveSmtpOverrides(array $data): bool
    {
        $allowed = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption', 'from', 'from_name'];
        $filtered = [];
        foreach ($allowed as $key) {
            if (isset($data[$key])) {
                $filtered[$key] = $data[$key];
            }
        }

        $json = json_encode($filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        $path = self::getSmtpOverridePath();
        $result = file_put_contents($path, $json, LOCK_EX);

        if ($result !== false) {
            // Reload overrides into current config
            self::loadSmtpOverrides();
        }

        return $result !== false;
    }

    /**
     * Read current SMTP overrides (or empty array if none).
     */
    public static function getSmtpOverrides(): array
    {
        $path = self::getSmtpOverridePath();
        if (!is_file($path)) {
            return [];
        }

        $json = file_get_contents($path);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
}
