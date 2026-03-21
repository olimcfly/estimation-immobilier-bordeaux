<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Captures, stores and forwards UTM parameters from Google Ads campaigns.
 *
 * UTM parameters tracked:
 *  - utm_source   (e.g. google)
 *  - utm_medium   (e.g. cpc)
 *  - utm_campaign (e.g. estimation-nantes)
 *  - utm_term     (e.g. estimation immobilière nantes)
 *  - utm_content  (e.g. ad-variante-a)
 *  - gclid        (Google Click ID — auto-tagged by Google Ads)
 */
final class UtmTrackingService
{
    private const UTM_KEYS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
    ];

    private const SESSION_KEY = 'utm_params';

    /**
     * Capture UTM parameters from the current request and persist them in session.
     * First-touch attribution: only stores the first set of UTM params per session.
     */
    public static function capture(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $params = self::extractFromRequest();

        if ($params === []) {
            return;
        }

        // First-touch: don't overwrite if already captured in this session
        if (!empty($_SESSION[self::SESSION_KEY])) {
            return;
        }

        $params['captured_at'] = date('Y-m-d H:i:s');
        $params['landing_url'] = self::getCurrentUrl();
        $params['referrer'] = trim((string) ($_SERVER['HTTP_REFERER'] ?? ''));

        $_SESSION[self::SESSION_KEY] = $params;
    }

    /**
     * Retrieve stored UTM parameters from session.
     *
     * @return array<string, string>
     */
    public static function get(): array
    {
        return is_array($_SESSION[self::SESSION_KEY] ?? null)
            ? $_SESSION[self::SESSION_KEY]
            : [];
    }

    /**
     * Build a note string with UTM data to append to lead notes.
     */
    public static function toLeadNote(): string
    {
        $params = self::get();
        if ($params === []) {
            return '';
        }

        $lines = ['--- Tracking Google Ads ---'];

        $labels = [
            'utm_source'   => 'Source',
            'utm_medium'   => 'Medium',
            'utm_campaign' => 'Campagne',
            'utm_term'     => 'Mot-clé',
            'utm_content'  => 'Contenu annonce',
            'gclid'        => 'Google Click ID',
            'landing_url'  => 'Page de destination',
            'referrer'     => 'Referrer',
            'captured_at'  => 'Date de capture',
        ];

        foreach ($labels as $key => $label) {
            $value = trim((string) ($params[$key] ?? ''));
            if ($value !== '') {
                $lines[] = "{$label}: {$value}";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Build hidden form fields to forward UTM params through form submissions.
     */
    public static function hiddenFields(): string
    {
        $params = self::get();
        if ($params === []) {
            return '';
        }

        $html = '';
        foreach (self::UTM_KEYS as $key) {
            $value = trim((string) ($params[$key] ?? ''));
            if ($value !== '') {
                $html .= '<input type="hidden" name="' . e($key) . '" value="' . e($value) . '">' . "\n";
            }
        }

        return $html;
    }

    /**
     * Extract UTM parameters from the current GET request.
     *
     * @return array<string, string>
     */
    private static function extractFromRequest(): array
    {
        $params = [];

        foreach (self::UTM_KEYS as $key) {
            $value = trim((string) ($_GET[$key] ?? ''));
            if ($value !== '') {
                $params[$key] = mb_substr($value, 0, 500);
            }
        }

        return $params;
    }

    /**
     * Get the full current URL.
     */
    private static function getCurrentUrl(): string
    {
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }
}
