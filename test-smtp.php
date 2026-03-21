#!/usr/bin/env php
<?php
/**
 * test-smtp.php — CLI SMTP tester (équivalent swaks / telnet)
 *
 * Teste la connectivité SMTP vers le serveur configuré (ou mail1.o2switch.net par défaut).
 * Supporte SSL/TLS (port 465) et STARTTLS (port 587).
 *
 * Usage :
 *   php test-smtp.php                          # utilise les valeurs .env / par défaut
 *   php test-smtp.php --host=smtp.example.com --port=587
 *   php test-smtp.php --host=mail1.o2switch.net --port=465 --user=xxx --pass=yyy
 *   php test-smtp.php --helo-only              # teste uniquement la connexion + EHLO
 */

declare(strict_types=1);

// ─── Couleurs terminal ──────────────────────────────────────────────
function c(string $text, string $color): string
{
    $codes = [
        'green'  => "\033[32m",
        'red'    => "\033[31m",
        'yellow' => "\033[33m",
        'cyan'   => "\033[36m",
        'bold'   => "\033[1m",
        'reset'  => "\033[0m",
    ];
    return ($codes[$color] ?? '') . $text . $codes['reset'];
}

function info(string $msg): void  { fwrite(STDOUT, c('  [INFO] ', 'cyan')   . $msg . PHP_EOL); }
function ok(string $msg): void    { fwrite(STDOUT, c('  [OK]   ', 'green')  . $msg . PHP_EOL); }
function warn(string $msg): void  { fwrite(STDOUT, c('  [WARN] ', 'yellow') . $msg . PHP_EOL); }
function fail(string $msg): void  { fwrite(STDERR, c('  [FAIL] ', 'red')    . $msg . PHP_EOL); }
function banner(string $msg): void { fwrite(STDOUT, PHP_EOL . c($msg, 'bold') . PHP_EOL); }

// ─── Chargement .env (basique) ──────────────────────────────────────
function loadEnv(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $value = trim($value, "\" \t\n\r");
            $_ENV[trim($key)] = $value;
        }
    }
}

loadEnv(__DIR__ . '/.env');

// ─── Parse arguments CLI ────────────────────────────────────────────
function parseArgs(array $argv): array
{
    $opts = [
        'host'      => $_ENV['MAIL_HOST'] ?? $_ENV['MAIL_SMTP_HOST'] ?? 'mail1.o2switch.net',
        'port'      => (int) ($_ENV['MAIL_PORT'] ?? $_ENV['MAIL_SMTP_PORT'] ?? 465),
        'user'      => $_ENV['MAIL_USERNAME'] ?? $_ENV['MAIL_SMTP_USER'] ?? '',
        'pass'      => $_ENV['MAIL_PASSWORD'] ?? $_ENV['MAIL_SMTP_PASS'] ?? '',
        'from'      => $_ENV['MAIL_FROM'] ?? 'test@example.com',
        'to'        => $_ENV['ADMIN_EMAIL'] ?? 'test@example.com',
        'encryption'=> $_ENV['MAIL_ENCRYPTION'] ?? $_ENV['MAIL_SMTP_ENCRYPTION'] ?? 'ssl',
        'helo-only' => false,
        'timeout'   => 10,
    ];

    foreach ($argv as $arg) {
        if (preg_match('/^--([a-z\-]+)(?:=(.+))?$/', $arg, $m)) {
            $key = $m[1];
            $val = $m[2] ?? true;
            if (array_key_exists($key, $opts)) {
                $opts[$key] = is_int($opts[$key]) ? (int) $val : $val;
            }
        }
    }

    return $opts;
}

// ─── Classe SmtpTester ──────────────────────────────────────────────
class SmtpTester
{
    /** @var resource|null */
    private $socket = null;
    private string $lastResponse = '';
    private array $log = [];

    public function __construct(
        private readonly string $host,
        private readonly int    $port,
        private readonly string $user,
        private readonly string $pass,
        private readonly string $encryption,
        private readonly int    $timeout,
    ) {}

    // ── Résultats ───────────────────────────────────────────────────
    /** @return array<array{direction: string, line: string}> */
    public function getLog(): array { return $this->log; }

    // ── Étapes du test ──────────────────────────────────────────────

    /** 1. Connexion TCP (+ SSL si port 465) */
    public function connect(): bool
    {
        $useImplicitSsl = ($this->encryption === 'ssl' || $this->port === 465);
        $prefix = $useImplicitSsl ? 'ssl://' : '';
        $address = $prefix . $this->host . ':' . $this->port;

        info("Connexion à {$address} (timeout {$this->timeout}s)…");

        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
                'allow_self_signed'=> true,
            ],
        ]);

        $errno = 0;
        $errstr = '';
        $this->socket = @stream_socket_client(
            $address,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $ctx,
        );

        if (!$this->socket) {
            fail("Impossible de se connecter : [$errno] $errstr");
            return false;
        }

        stream_set_timeout($this->socket, $this->timeout);
        ok("Connecté à {$this->host}:{$this->port}" . ($useImplicitSsl ? ' (SSL implicite)' : ''));

        // Lire la bannière du serveur
        $banner = $this->readResponse();
        if (!str_starts_with($banner, '220')) {
            fail("Bannière inattendue : $banner");
            return false;
        }
        ok("Bannière : $banner");
        return true;
    }

    /** 2. EHLO */
    public function ehlo(): bool
    {
        $hostname = gethostname() ?: 'localhost';
        $resp = $this->command("EHLO $hostname");
        if (!str_starts_with($resp, '250')) {
            fail("EHLO refusé : $resp");
            return false;
        }
        ok("EHLO accepté");
        return true;
    }

    /** 3. STARTTLS (si port != 465) */
    public function starttls(): bool
    {
        if ($this->encryption === 'ssl' || $this->port === 465) {
            info("SSL implicite — STARTTLS non nécessaire");
            return true;
        }

        $resp = $this->command('STARTTLS');
        if (!str_starts_with($resp, '220')) {
            fail("STARTTLS refusé : $resp");
            return false;
        }

        $crypto = stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
        if (!$crypto) {
            fail('Impossible d\'activer le chiffrement TLS');
            return false;
        }

        ok('STARTTLS activé — connexion chiffrée');

        // Ré-EHLO après STARTTLS (RFC 3207)
        return $this->ehlo();
    }

    /** 4. AUTH LOGIN */
    public function authenticate(): bool
    {
        if ($this->user === '' || $this->pass === '') {
            warn('Pas de credentials SMTP configurés — auth ignorée');
            return true;
        }

        $resp = $this->command('AUTH LOGIN');
        if (!str_starts_with($resp, '334')) {
            fail("AUTH LOGIN refusé : $resp");
            return false;
        }

        $resp = $this->command(base64_encode($this->user));
        if (!str_starts_with($resp, '334')) {
            fail("Username refusé : $resp");
            return false;
        }

        $resp = $this->command(base64_encode($this->pass), mask: true);
        if (!str_starts_with($resp, '235')) {
            fail("Authentification échouée : $resp");
            return false;
        }

        ok("Authentifié en tant que {$this->user}");
        return true;
    }

    /** 5. MAIL FROM / RCPT TO (dry-run, pas de DATA) */
    public function testEnvelope(string $from, string $to): bool
    {
        $resp = $this->command("MAIL FROM:<$from>");
        if (!str_starts_with($resp, '250')) {
            fail("MAIL FROM refusé : $resp");
            return false;
        }
        ok("MAIL FROM:<$from> accepté");

        $resp = $this->command("RCPT TO:<$to>");
        if (!str_starts_with($resp, '250') && !str_starts_with($resp, '251')) {
            fail("RCPT TO refusé : $resp");
            return false;
        }
        ok("RCPT TO:<$to> accepté");
        return true;
    }

    /** 6. QUIT proprement */
    public function quit(): void
    {
        if ($this->socket) {
            $this->command('QUIT');
            fclose($this->socket);
            $this->socket = null;
            ok('Déconnecté (QUIT)');
        }
    }

    /** 7. Infos TLS / certificat */
    public function tlsInfo(): void
    {
        if (!$this->socket) {
            return;
        }

        $meta = stream_get_meta_data($this->socket);
        $params = stream_context_get_params($this->socket);

        if (isset($params['options']['ssl']['peer_certificate'])) {
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            if ($cert) {
                info('Certificat CN : ' . ($cert['subject']['CN'] ?? 'N/A'));
                info('Émetteur      : ' . ($cert['issuer']['O'] ?? 'N/A'));
                info('Valide jusqu\'au: ' . date('Y-m-d H:i', $cert['validTo_time_t'] ?? 0));
            }
        }

        if (isset($meta['crypto'])) {
            info('Protocole : ' . ($meta['crypto']['protocol'] ?? 'N/A'));
            info('Cipher    : ' . ($meta['crypto']['cipher_name'] ?? 'N/A'));
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function command(string $cmd, bool $mask = false): string
    {
        $display = $mask ? '***' : $cmd;
        $this->log[] = ['direction' => 'C', 'line' => $display];
        fwrite($this->socket, $cmd . "\r\n");
        return $this->readResponse();
    }

    private function readResponse(): string
    {
        $response = '';
        while (true) {
            $line = fgets($this->socket, 4096);
            if ($line === false) {
                break;
            }
            $response .= $line;
            $this->log[] = ['direction' => 'S', 'line' => rtrim($line)];
            // RFC 5321 : continuation si 4e caractère est '-'
            if (isset($line[3]) && $line[3] !== '-') {
                break;
            }
        }
        $this->lastResponse = rtrim($response);
        return $this->lastResponse;
    }
}

// ─── Main ───────────────────────────────────────────────────────────

$opts = parseArgs($argv);

banner('╔══════════════════════════════════════════════════════════════╗');
banner('║          🔧  SMTP CLI Tester — o2switch / PHPMailer        ║');
banner('╚══════════════════════════════════════════════════════════════╝');

info("Serveur    : {$opts['host']}:{$opts['port']}");
info("Encryption : {$opts['encryption']}");
info("User       : " . ($opts['user'] !== '' ? $opts['user'] : '(non configuré)'));

$tester = new SmtpTester(
    host:       $opts['host'],
    port:       $opts['port'],
    user:       $opts['user'],
    pass:       $opts['pass'],
    encryption: $opts['encryption'],
    timeout:    (int) $opts['timeout'],
);

$steps = [
    'Connexion TCP/SSL'     => fn() => $tester->connect(),
    'EHLO'                  => fn() => $tester->ehlo(),
    'STARTTLS'              => fn() => $tester->starttls(),
    'TLS Info'              => fn() => (($tester->tlsInfo()) || true),
];

if (!$opts['helo-only']) {
    $steps['AUTH LOGIN']    = fn() => $tester->authenticate();
    if ($opts['user'] !== '') {
        $steps['Envelope (MAIL FROM/RCPT TO)'] = fn() => $tester->testEnvelope($opts['from'], $opts['to']);
    }
}

$steps['QUIT'] = fn() => (($tester->quit()) || true);

$passed = 0;
$failed = 0;

foreach ($steps as $name => $fn) {
    banner("── $name ──");
    $result = $fn();
    if ($result === false) {
        $failed++;
        fail("Étape \"$name\" échouée — arrêt");
        break;
    }
    $passed++;
}

// ─── Résumé ─────────────────────────────────────────────────────────
banner('── Résumé ──');
info("Étapes réussies : $passed");
if ($failed > 0) {
    fail("Étapes échouées : $failed");
}

// Transcript complet
banner('── Transcript SMTP ──');
foreach ($tester->getLog() as $entry) {
    $prefix = $entry['direction'] === 'C' ? c('C: ', 'cyan') : c('S: ', 'green');
    fwrite(STDOUT, "  $prefix{$entry['line']}" . PHP_EOL);
}

exit($failed > 0 ? 1 : 0);
