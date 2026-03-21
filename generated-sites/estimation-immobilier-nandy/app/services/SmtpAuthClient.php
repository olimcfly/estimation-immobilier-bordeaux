<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Low-level SMTP AUTH LOGIN client.
 *
 * Opens a raw socket to an SMTP server, sends EHLO + AUTH LOGIN,
 * and returns structured results with parsed SMTP response codes.
 */
final class SmtpAuthClient
{
    /** @var array<int, array{code: int, message: string}> */
    private array $transcript = [];

    private string $host;
    private int $port;
    private int $timeoutSeconds;

    public function __construct(string $host, int $port = 587, int $timeoutSeconds = 10)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeoutSeconds = $timeoutSeconds;
    }

    // ------------------------------------------------------------------ public

    /**
     * Attempt a full AUTH LOGIN handshake.
     *
     * @return array{success: bool, transcript: array<int, array{code: int, message: string}>}
     */
    public function authenticate(string $username, string $password): array
    {
        $this->transcript = [];

        $socket = $this->connect();
        if ($socket === false) {
            return $this->result(false);
        }

        try {
            // 1. Read server greeting
            $greeting = $this->readResponse($socket);
            if ($greeting === null || $greeting['code'] !== 220) {
                return $this->result(false);
            }

            // 2. EHLO
            $ehlo = $this->sendCommand($socket, 'EHLO ' . gethostname());
            if ($ehlo === null || $ehlo['code'] !== 250) {
                return $this->result(false);
            }

            // 3. AUTH LOGIN
            $authStart = $this->sendCommand($socket, 'AUTH LOGIN');
            if ($authStart === null || $authStart['code'] !== 334) {
                return $this->result(false);
            }

            // 4. Send base64-encoded username
            $userResp = $this->sendCommand($socket, base64_encode($username));
            if ($userResp === null || $userResp['code'] !== 334) {
                return $this->result(false);
            }

            // 5. Send base64-encoded password
            $passResp = $this->sendCommand($socket, base64_encode($password));
            if ($passResp === null) {
                return $this->result(false);
            }

            $success = $passResp['code'] === 235;

            // 6. QUIT (best-effort)
            $this->sendCommand($socket, 'QUIT');

            return $this->result($success);
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }

    /**
     * Return the full conversation transcript.
     *
     * @return array<int, array{code: int, message: string}>
     */
    public function getTranscript(): array
    {
        return $this->transcript;
    }

    // ---------------------------------------------------------------- internals

    /**
     * @return resource|false
     */
    private function connect()
    {
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeoutSeconds);

        if ($socket === false) {
            $this->transcript[] = [
                'code' => 0,
                'message' => "Connection failed: [$errno] $errstr",
            ];
            return false;
        }

        stream_set_timeout($socket, $this->timeoutSeconds);

        return $socket;
    }

    /**
     * Send a command and return the parsed response.
     *
     * @param resource $socket
     * @return array{code: int, message: string}|null
     */
    private function sendCommand($socket, string $command): ?array
    {
        fwrite($socket, $command . "\r\n");

        return $this->readResponse($socket);
    }

    /**
     * Read a (possibly multi-line) SMTP response.
     *
     * @param resource $socket
     * @return array{code: int, message: string}|null
     */
    private function readResponse($socket): ?array
    {
        $response = '';
        $code = 0;

        while (true) {
            $line = fgets($socket, 512);
            if ($line === false) {
                $this->transcript[] = ['code' => 0, 'message' => 'Read error / timeout'];
                return null;
            }

            $response .= $line;
            $code = (int) substr($line, 0, 3);

            // A space at position 3 signals the last line of a multi-line reply.
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
            // Single-line responses shorter than 4 chars.
            if (strlen($line) < 4) {
                break;
            }
        }

        $parsed = [
            'code' => $code,
            'message' => trim($response),
        ];

        $this->transcript[] = $parsed;

        return $parsed;
    }

    /**
     * @param array<int, array{code: int, message: string}>|null $transcript
     * @return array{success: bool, transcript: array<int, array{code: int, message: string}>}
     */
    private function result(bool $success): array
    {
        return [
            'success' => $success,
            'transcript' => $this->transcript,
        ];
    }

    // --------------------------------------------------------- static helpers

    /**
     * Parse a raw SMTP response line into code + message.
     */
    public static function parseSmtpResponse(string $raw): array
    {
        $code = (int) substr($raw, 0, 3);
        $message = trim(substr($raw, 4));

        return ['code' => $code, 'message' => $message];
    }

    /**
     * Human-readable description for common SMTP codes.
     */
    public static function describeCode(int $code): string
    {
        return match ($code) {
            220 => 'Service ready',
            235 => 'Authentication successful',
            250 => 'OK',
            334 => 'Server challenge (continue auth)',
            421 => 'Service not available',
            435 => 'Authentication not accepted',
            454 => 'Temporary authentication failure',
            500 => 'Syntax error / command unrecognised',
            501 => 'Syntax error in parameters',
            503 => 'Bad sequence of commands',
            504 => 'Command parameter not implemented',
            530 => 'Authentication required',
            534 => 'Authentication mechanism too weak',
            535 => 'Authentication credentials invalid',
            default => 'Unknown SMTP code',
        };
    }
}
