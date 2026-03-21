<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Services\SmtpLogger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Mailer
{
    private const MAX_RETRIES = 2;

    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('Mailer error: PHPMailer is not installed. Run "composer install".');
            self::logToDb($to, $subject, $htmlBody, 'failed');
            return false;
        }

        $lastError = '';

        for ($attempt = 0; $attempt <= self::MAX_RETRIES; $attempt++) {
            if ($attempt > 0) {
                usleep($attempt * 500_000); // 0.5s, 1s backoff
            }

            $result = self::attemptSend($to, $subject, $htmlBody, $lastError);
            if ($result) {
                self::logToDb($to, $subject, $htmlBody, 'sent');
                return true;
            }

            // Only retry on transient connection errors, not auth or recipient errors
            if (!self::isTransientError($lastError)) {
                break;
            }

            error_log("Mailer: retry attempt " . ($attempt + 1) . " for $to");
        }

        self::logToDb($to, $subject, $htmlBody, 'failed');
        return false;
    }

    private static function attemptSend(string $to, string $subject, string $htmlBody, string &$errorOut): bool
    {
        $mail = new PHPMailer(true);

        try {
            $smtpHost = (string) Config::get('mail.smtp_host');
            $fromAddress = (string) Config::get('mail.from', 'no-reply@estimation-immobilier-nandy.fr');
            $fromName = (string) Config::get('mail.from_name', 'Estimation Immobilier Nandy');
            $smtpUser = (string) Config::get('mail.smtp_user');

            if ($smtpHost !== '') {
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $smtpPort = (int) Config::get('mail.smtp_port', 587);
                $mail->Port = $smtpPort;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                $mail->Password = (string) Config::get('mail.smtp_pass');
                $mail->Timeout = 15;

                $smtpEnc = (string) Config::get('mail.smtp_encryption', 'tls');
                if ($smtpPort === 465) {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($smtpEnc === 'tls' || $smtpPort === 587) {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = $smtpEnc;
                }
            } else {
                error_log('Mailer warning: SMTP host is empty. Check MAIL_HOST or MAIL_SMTP_HOST in .env');
            }

            $mail->CharSet = 'UTF-8';
            $mail->XMailer = 'Estimation Immobilier Nandy';

            $mail->setFrom($fromAddress, $fromName);

            // Set envelope sender (Return-Path) to the SMTP authenticated user
            // for SPF alignment — critical for o2switch and shared hosting
            $envelopeSender = ($smtpUser !== '') ? $smtpUser : $fromAddress;
            $mail->Sender = $envelopeSender;

            $mail->addReplyTo($fromAddress, $fromName);

            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

            $mail->send();
            SmtpLogger::log(
                (string) ($mail->Host ?? ''),
                (int) ($mail->Port ?? 0),
                'OK'
            );
            return true;
        } catch (Exception $e) {
            $errorOut = $e->getMessage();
            error_log('Mailer error: ' . $errorOut);
            error_log('Mailer debug: host=' . ($mail->Host ?? '') . ' port=' . ($mail->Port ?? '') . ' user=' . ($mail->Username ?? ''));
            SmtpLogger::log(
                (string) ($mail->Host ?? ''),
                (int) ($mail->Port ?? 0),
                'ECHEC',
                $errorOut
            );
            return false;
        } catch (\Throwable $e) {
            $errorOut = $e->getMessage();
            error_log('Mailer unexpected error: ' . $errorOut);
            SmtpLogger::log(
                (string) ($mail->Host ?? ''),
                (int) ($mail->Port ?? 0),
                'ECHEC',
                $errorOut
            );
            return false;
        }
    }

    private static function isTransientError(string $message): bool
    {
        $transientPatterns = [
            'Connection timed out',
            'Connection refused',
            'connect() failed',
            'Failed to connect',
            'Connection reset',
            'Temporary failure',
            'try again',
            '421 ',
            '451 ',
            '452 ',
        ];

        foreach ($transientPatterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    private static function logToDb(string $recipient, string $subject, string $bodyHtml, string $status): void
    {
        try {
            $pdo = \App\Core\Database::connection();
            $stmt = $pdo->prepare(
                'INSERT INTO email_logs (recipient, subject, body_html, status, sent_at) VALUES (?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$recipient, $subject, $bodyHtml, $status]);
        } catch (\Throwable $e) {
            error_log('Mailer: failed to log email to DB: ' . $e->getMessage());
        }
    }

    /**
     * Analyse automatique des résultats d'un test SMTP et retourne les diagnostics détectés.
     *
     * @param array{
     *     auth_error?: bool,
     *     server_error?: bool,
     *     ssl_error?: bool,
     *     port_error?: bool,
     *     account_error?: bool,
     *     error_message?: string
     * } $results Résultats du test SMTP
     * @return string[] Liste des problèmes détectés
     */
    public static function diagnose(array $results): array
    {
        $issues = [];

        $message = $results['error_message'] ?? '';

        // ❌ Mauvais mot de passe
        if (
            !empty($results['auth_error'])
            || stripos($message, 'Could not authenticate') !== false
            || stripos($message, 'Authentication failed') !== false
            || stripos($message, 'Invalid credentials') !== false
            || stripos($message, 'Username and Password not accepted') !== false
        ) {
            $issues[] = '❌ Mauvais mot de passe';
        }

        // ❌ Serveur invalide
        if (
            !empty($results['server_error'])
            || stripos($message, 'Could not resolve host') !== false
            || stripos($message, 'getaddrinfo failed') !== false
            || stripos($message, 'Name or service not known') !== false
            || stripos($message, 'No such host') !== false
        ) {
            $issues[] = '❌ Serveur invalide';
        }

        // ❌ SSL cassé
        if (
            !empty($results['ssl_error'])
            || stripos($message, 'ssl') !== false
            || stripos($message, 'tls') !== false
            || stripos($message, 'certificate') !== false
            || stripos($message, 'STARTTLS') !== false
            || stripos($message, 'crypto') !== false
        ) {
            $issues[] = '❌ SSL cassé';
        }

        // ❌ Port bloqué
        if (
            !empty($results['port_error'])
            || stripos($message, 'Connection timed out') !== false
            || stripos($message, 'Connection refused') !== false
            || stripos($message, 'connect() failed') !== false
            || stripos($message, 'Failed to connect') !== false
        ) {
            $issues[] = '❌ Port bloqué';
        }

        // ❌ Compte inexistant
        if (
            !empty($results['account_error'])
            || stripos($message, 'Mailbox not found') !== false
            || stripos($message, 'User unknown') !== false
            || stripos($message, 'Recipient rejected') !== false
            || stripos($message, 'does not exist') !== false
            || stripos($message, 'account has been disabled') !== false
        ) {
            $issues[] = '❌ Compte inexistant';
        }

        // ❌ SPF/DKIM/DMARC rejection
        if (
            stripos($message, 'SPF') !== false
            || stripos($message, 'DKIM') !== false
            || stripos($message, 'DMARC') !== false
            || stripos($message, '550 5.7.1') !== false
            || stripos($message, 'policy rejection') !== false
            || stripos($message, 'not authorized') !== false
            || stripos($message, 'sender verify failed') !== false
        ) {
            $issues[] = '❌ Rejet SPF/DKIM — Vérifiez que l\'expéditeur (Sender) correspond au compte SMTP authentifié';
        }

        // ❌ Destinataire rejeté par le serveur distant
        if (
            stripos($message, '550 ') !== false
            || stripos($message, '553 ') !== false
            || stripos($message, '554 ') !== false
            || stripos($message, 'relay not permitted') !== false
            || stripos($message, 'relay access denied') !== false
        ) {
            if (empty($issues)) {
                $issues[] = '❌ Le serveur destinataire a rejeté le message';
            }
        }

        return $issues;
    }
}
