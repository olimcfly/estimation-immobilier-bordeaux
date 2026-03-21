<?php

declare(strict_types=1);

/**
 * Extracted slugify function for unit testing.
 * Mirrors the logic in generate-actualite.php.
 */
function cronSlugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
    $text = trim($text, '-');
    return $text !== '' ? $text : 'actualite';
}
