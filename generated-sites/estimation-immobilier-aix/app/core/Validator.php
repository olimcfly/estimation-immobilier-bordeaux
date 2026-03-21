<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function string(array $input, string $key, int $min = 1, int $max = 255): string
    {
        $value = trim((string) ($input[$key] ?? ''));

        if ($value === '' || mb_strlen($value) < $min || mb_strlen($value) > $max) {
            throw new \InvalidArgumentException("Champ invalide: {$key}");
        }

        return $value;
    }

    public static function float(array $input, string $key, float $min = 0.1, float $max = 100000000): float
    {
        $value = filter_var($input[$key] ?? null, FILTER_VALIDATE_FLOAT);
        if ($value === false || $value < $min || $value > $max) {
            throw new \InvalidArgumentException("Nombre invalide: {$key}");
        }
        return (float) $value;
    }

    public static function int(array $input, string $key, int $min = 1, int $max = 1000): int
    {
        $value = filter_var($input[$key] ?? null, FILTER_VALIDATE_INT);
        if ($value === false || $value < $min || $value > $max) {
            throw new \InvalidArgumentException("Entier invalide: {$key}");
        }
        return (int) $value;
    }

    public static function email(array $input, string $key): string
    {
        $value = trim((string) ($input[$key] ?? ''));
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email invalide: {$key}");
        }
        return $value;
    }
}
