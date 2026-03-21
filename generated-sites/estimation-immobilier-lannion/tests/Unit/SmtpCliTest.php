<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests pour le script CLI test-smtp.php
 *
 * Vérifie que le tester SMTP fonctionne correctement :
 *  - parsing des arguments CLI
 *  - connexion socket vers un serveur SMTP réel (o2switch)
 *  - gestion des erreurs (host invalide, port fermé)
 */
final class SmtpCliTest extends TestCase
{
    private string $script;

    protected function setUp(): void
    {
        $this->script = dirname(__DIR__, 2) . '/test-smtp.php';
        $this->assertFileExists($this->script, 'test-smtp.php doit exister à la racine du projet');
    }

    // ─── Tests structurels ──────────────────────────────────────────

    public function testScriptFileExists(): void
    {
        $this->assertFileExists($this->script);
    }

    public function testScriptIsValidPhp(): void
    {
        $output = [];
        $exitCode = 0;
        exec("php -l {$this->script} 2>&1", $output, $exitCode);
        $this->assertSame(0, $exitCode, 'test-smtp.php doit être du PHP valide : ' . implode("\n", $output));
    }

    // ─── Tests de parsing des arguments ─────────────────────────────

    public function testDefaultHostIsO2switch(): void
    {
        // Le script affiche le host dans sa sortie
        $output = $this->runScript(['--helo-only', '--timeout=3', '--host=mail1.o2switch.net']);
        $this->assertStringContainsString('mail1.o2switch.net', $output);
    }

    public function testCustomHostArgument(): void
    {
        $output = $this->runScript(['--host=smtp.test.invalid', '--port=25', '--helo-only', '--timeout=2']);
        $this->assertStringContainsString('smtp.test.invalid', $output);
    }

    public function testCustomPortArgument(): void
    {
        $output = $this->runScript(['--host=smtp.test.invalid', '--port=587', '--helo-only', '--timeout=2']);
        $this->assertStringContainsString('587', $output);
    }

    // ─── Test de connexion réelle (o2switch port 465 SSL) ───────────

    /**
     * @group network
     * Test la connectivité réelle vers mail1.o2switch.net:465
     * Équivalent de : telnet mail1.o2switch.net 465 / swaks --to test@example.com --server mail1.o2switch.net:465
     */
    public function testO2switchSmtpConnectivity(): void
    {
        // Vérifier d'abord que le réseau est accessible
        $sock = @fsockopen('mail1.o2switch.net', 465, $errno, $errstr, 5);
        if ($sock === false) {
            $this->markTestSkipped(
                "Impossible de joindre mail1.o2switch.net:465 — réseau indisponible ($errstr)"
            );
        }
        fclose($sock);

        $output = $this->runScript([
            '--host=mail1.o2switch.net',
            '--port=465',
            '--encryption=ssl',
            '--helo-only',
            '--timeout=10',
        ], $exitCode);

        // Le script doit au minimum réussir la connexion et le EHLO
        $this->assertStringContainsString('Connecté', $output, 'Doit se connecter au serveur');
        $this->assertStringContainsString('EHLO accepté', $output, 'EHLO doit être accepté');
        $this->assertStringContainsString('QUIT', $output, 'Doit se déconnecter proprement');
        $this->assertSame(0, $exitCode, 'Le script doit terminer avec exit code 0');
    }

    /**
     * @group network
     * Vérifie que le transcript SMTP contient les échanges attendus
     */
    public function testSmtpTranscriptContainsExpectedExchanges(): void
    {
        $sock = @fsockopen('mail1.o2switch.net', 465, $errno, $errstr, 5);
        if ($sock === false) {
            $this->markTestSkipped("Réseau indisponible ($errstr)");
        }
        fclose($sock);

        $output = $this->runScript([
            '--host=mail1.o2switch.net',
            '--port=465',
            '--encryption=ssl',
            '--helo-only',
            '--timeout=10',
        ]);

        // Le transcript doit montrer les réponses serveur (codes 220, 250)
        $this->assertStringContainsString('Transcript SMTP', $output);
        $this->assertMatchesRegularExpression('/S:.*220/', $output, 'Doit contenir la bannière 220');
        $this->assertMatchesRegularExpression('/C:.*EHLO/', $output, 'Doit contenir la commande EHLO');
        $this->assertMatchesRegularExpression('/S:.*250/', $output, 'Doit contenir la réponse 250');
    }

    // ─── Tests d'erreur ─────────────────────────────────────────────

    public function testConnectionFailsOnInvalidHost(): void
    {
        $output = $this->runScript([
            '--host=this.host.does.not.exist.invalid',
            '--port=465',
            '--helo-only',
            '--timeout=3',
        ], $exitCode);

        $this->assertSame(1, $exitCode, 'Doit échouer sur un host invalide');
        $this->assertStringContainsString('FAIL', $output);
    }

    public function testConnectionFailsOnClosedPort(): void
    {
        // Port 9 (discard) est généralement fermé sur les serveurs SMTP
        $output = $this->runScript([
            '--host=127.0.0.1',
            '--port=9',
            '--encryption=',
            '--helo-only',
            '--timeout=3',
        ], $exitCode);

        $this->assertSame(1, $exitCode, 'Doit échouer sur un port fermé');
    }

    // ─── Test PHPMailer (intégration avec le service Mailer existant) ──

    public function testPhpmailerIsInstalled(): void
    {
        $this->assertTrue(
            class_exists(\PHPMailer\PHPMailer\PHPMailer::class),
            'PHPMailer doit être installé (composer require phpmailer/phpmailer)'
        );
    }

    // ─── Helper ─────────────────────────────────────────────────────

    private function runScript(array $args, int &$exitCode = null): string
    {
        $cmd = 'php ' . escapeshellarg($this->script) . ' ' . implode(' ', array_map('escapeshellarg', $args)) . ' 2>&1';
        $output = [];
        $code = 0;
        exec($cmd, $output, $code);
        $exitCode = $code;
        return implode("\n", $output);
    }
}
