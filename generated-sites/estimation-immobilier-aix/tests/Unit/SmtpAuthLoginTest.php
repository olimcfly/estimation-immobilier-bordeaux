<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SmtpAuthClient;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the SMTP AUTH LOGIN handshake client.
 *
 * These tests spin up a minimal TCP server on localhost to simulate an SMTP
 * server, so they run entirely offline — no real mail server required.
 */
final class SmtpAuthLoginTest extends TestCase
{
    // ---------------------------------------------------------------- helpers

    /**
     * Start a local TCP server that replays the given SMTP script.
     *
     * Each entry in $script is a callable receiving the client line and
     * returning the response to send (or null to close).
     *
     * @param list<string> $responses  Ordered server responses (including greeting).
     * @return array{0: resource, 1: int}  [server socket, port]
     */
    private function startFakeSmtp(array $responses): array
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        $this->assertNotFalse($server, "Failed to start fake SMTP: $errstr");

        $name = stream_socket_get_name($server, false);
        $this->assertIsString($name);
        $port = (int) substr($name, (int) strrpos($name, ':') + 1);

        return [$server, $port, $responses];
    }

    /**
     * Serve exactly one SMTP session in the current process (blocking).
     *
     * @param resource    $server
     * @param list<string> $responses
     */
    private function serveFakeSession($server, array $responses): void
    {
        $client = @stream_socket_accept($server, 5);
        if ($client === false) {
            return;
        }

        $idx = 0;

        // Send greeting first.
        if (isset($responses[$idx])) {
            fwrite($client, $responses[$idx] . "\r\n");
            $idx++;
        }

        // For each client command, send the next scripted response.
        while ($idx < count($responses)) {
            $line = fgets($client, 512);
            if ($line === false) {
                break;
            }
            fwrite($client, $responses[$idx] . "\r\n");
            $idx++;
        }

        fclose($client);
        fclose($server);
    }

    /**
     * Run a fake SMTP server + SmtpAuthClient::authenticate() together.
     *
     * Uses a child process so the server and client can run concurrently.
     *
     * @param list<string> $responses
     * @return array{success: bool, transcript: array}
     */
    private function runWithFakeSmtp(
        array $responses,
        string $user = 'user@example.com',
        string $pass = 'secret',
    ): array {
        [$server, $port, $responses] = $this->startFakeSmtp($responses);

        $pid = pcntl_fork();
        $this->assertNotSame(-1, $pid, 'Failed to fork');

        if ($pid === 0) {
            // Child — run the fake server then exit.
            $this->serveFakeSession($server, $responses);
            // Use _exit to avoid PHPUnit teardown in child.
            posix_kill(getmypid(), SIGKILL);
        }

        // Parent — close the server socket (child owns it) and run client.
        fclose($server);

        $client = new SmtpAuthClient('127.0.0.1', $port, 5);
        $result = $client->authenticate($user, $pass);

        // Reap child.
        pcntl_waitpid($pid, $status);

        return $result;
    }

    // ----------------------------------------------------- test: EHLO + AUTH LOGIN success

    public function testSuccessfulAuthLogin(): void
    {
        $responses = [
            '220 smtp.example.com ESMTP ready',       // greeting
            '250 smtp.example.com Hello',              // EHLO
            '334 VXNlcm5hbWU6',                       // AUTH LOGIN → "Username:"
            '334 UGFzc3dvcmQ6',                       // username   → "Password:"
            '235 2.7.0 Authentication successful',     // password   → success
            '221 Bye',                                 // QUIT
        ];

        $result = $this->runWithFakeSmtp($responses, 'admin@test.com', 'p4ssw0rd');

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['transcript']);

        // Verify we received the 235 code somewhere in transcript.
        $codes = array_column($result['transcript'], 'code');
        $this->assertContains(235, $codes, 'Transcript should contain 235 (auth success)');
    }

    // ----------------------------------------------------- test: AUTH LOGIN failure (535)

    public function testFailedAuthLoginInvalidCredentials(): void
    {
        $responses = [
            '220 smtp.example.com ESMTP ready',
            '250 smtp.example.com Hello',
            '334 VXNlcm5hbWU6',
            '334 UGFzc3dvcmQ6',
            '535 5.7.8 Authentication credentials invalid',
            '221 Bye',
        ];

        $result = $this->runWithFakeSmtp($responses, 'wrong@test.com', 'bad');

        $this->assertFalse($result['success']);
        $codes = array_column($result['transcript'], 'code');
        $this->assertContains(535, $codes, 'Transcript should contain 535 (auth failed)');
    }

    // ----------------------------------------------------- test: server rejects AUTH LOGIN

    public function testServerRejectsAuthLogin(): void
    {
        $responses = [
            '220 smtp.example.com ESMTP ready',
            '250 smtp.example.com Hello',
            '504 Unrecognized authentication type',
        ];

        $result = $this->runWithFakeSmtp($responses);

        $this->assertFalse($result['success']);
        $codes = array_column($result['transcript'], 'code');
        $this->assertContains(504, $codes);
    }

    // ----------------------------------------------------- test: connection refused

    public function testConnectionRefused(): void
    {
        // Use a port where nothing listens.
        $client = new SmtpAuthClient('127.0.0.1', 19999, 2);
        $result = $client->authenticate('user@test.com', 'pass');

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['transcript']);
        $this->assertSame(0, $result['transcript'][0]['code']);
        $this->assertStringContainsString('Connection failed', $result['transcript'][0]['message']);
    }

    // ----------------------------------------------------- test: bad greeting

    public function testBadGreeting(): void
    {
        $responses = [
            '421 Service not available',
        ];

        $result = $this->runWithFakeSmtp($responses);

        $this->assertFalse($result['success']);
        $codes = array_column($result['transcript'], 'code');
        $this->assertContains(421, $codes);
    }

    // ----------------------------------------------------- test: parseSmtpResponse

    public function testParseSmtpResponse(): void
    {
        $parsed = SmtpAuthClient::parseSmtpResponse('250 OK');
        $this->assertSame(250, $parsed['code']);
        $this->assertSame('OK', $parsed['message']);

        $parsed = SmtpAuthClient::parseSmtpResponse('535 5.7.8 Authentication credentials invalid');
        $this->assertSame(535, $parsed['code']);
        $this->assertSame('5.7.8 Authentication credentials invalid', $parsed['message']);
    }

    // ----------------------------------------------------- test: describeCode

    public function testDescribeCode(): void
    {
        $this->assertSame('Authentication successful', SmtpAuthClient::describeCode(235));
        $this->assertSame('Authentication credentials invalid', SmtpAuthClient::describeCode(535));
        $this->assertSame('Service ready', SmtpAuthClient::describeCode(220));
        $this->assertSame('Server challenge (continue auth)', SmtpAuthClient::describeCode(334));
        $this->assertSame('Unknown SMTP code', SmtpAuthClient::describeCode(999));
    }

    // ----------------------------------------------------- test: EHLO rejected

    public function testEhloRejected(): void
    {
        $responses = [
            '220 smtp.example.com ESMTP ready',
            '500 Syntax error',
        ];

        $result = $this->runWithFakeSmtp($responses);

        $this->assertFalse($result['success']);
        $codes = array_column($result['transcript'], 'code');
        $this->assertContains(500, $codes);
    }
}
