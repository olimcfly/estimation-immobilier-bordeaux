<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Services\SmtpLogger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('Mailer error: PHPMailer is not installed. Run "composer install".');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $smtpHost = (string) Config::get('mail.smtp_host');

            if ($smtpHost !== '') {
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $smtpPort = (int) Config::get('mail.smtp_port', 587);
                $mail->Port = $smtpPort;
                $mail->SMTPAuth = true;
                $mail->Username = (string) Config::get('mail.smtp_user');
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
            $mail->setFrom(
                (string) Config::get('mail.from', 'no-reply@estimation-immobilier-bordeaux.fr'),
                (string) Config::get('mail.from_name', 'Estimation Immobilier Bordeaux')
            );
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
            error_log('Mailer error: ' . $e->getMessage());
            error_log('Mailer debug: host=' . ($mail->Host ?? '') . ' port=' . ($mail->Port ?? '') . ' user=' . ($mail->Username ?? ''));
            SmtpLogger::log(
                (string) ($mail->Host ?? ''),
                (int) ($mail->Port ?? 0),
                'ECHEC',
                $e->getMessage()
            );
            return false;
        } catch (\Throwable $e) {
            error_log('Mailer unexpected error: ' . $e->getMessage());
            SmtpLogger::log(
                (string) ($mail->Host ?? ''),
                (int) ($mail->Port ?? 0),
                'ECHEC',
                $e->getMessage()
            );
            return false;
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

        return $issues;
    }
}
