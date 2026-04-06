<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';

if (!defined('NOTIF_WEEKLY_REPORT') || !NOTIF_WEEKLY_REPORT) {
    exit("NOTIF_WEEKLY_REPORT disabled\n");
}

$pdo = Database::getConnection();
$today = new DateTimeImmutable('today');
$weekStart = $today->modify('monday last week')->setTime(0, 0, 0);
$weekEnd = $weekStart->modify('+6 days')->setTime(23, 59, 59);
$prevStart = $weekStart->modify('-7 days');
$prevEnd = $weekStart->modify('-1 second');

$stmtCurrent = $pdo->prepare('SELECT COUNT(*) as total FROM estimations WHERE created_at BETWEEN :start AND :end');
$stmtCurrent->execute(['start' => $weekStart->format('Y-m-d H:i:s'), 'end' => $weekEnd->format('Y-m-d H:i:s')]);
$currentTotal = (int) ($stmtCurrent->fetch()['total'] ?? 0);

$stmtPrev = $pdo->prepare('SELECT COUNT(*) as total FROM estimations WHERE created_at BETWEEN :start AND :end');
$stmtPrev->execute(['start' => $prevStart->format('Y-m-d H:i:s'), 'end' => $prevEnd->format('Y-m-d H:i:s')]);
$prevTotal = (int) ($stmtPrev->fetch()['total'] ?? 0);

$stmtTop = $pdo->prepare('SELECT id, adresse, lead_score FROM estimations WHERE created_at BETWEEN :start AND :end ORDER BY lead_score DESC, created_at DESC LIMIT 5');
$stmtTop->execute(['start' => $weekStart->format('Y-m-d H:i:s'), 'end' => $weekEnd->format('Y-m-d H:i:s')]);
$topLeads = $stmtTop->fetchAll();

$stmtRdv = $pdo->prepare('SELECT nom, date_souhaitee FROM rdv WHERE date_souhaitee BETWEEN :start AND :end ORDER BY date_souhaitee ASC LIMIT 10');
$stmtRdv->execute(['start' => $today->format('Y-m-d'), 'end' => $today->modify('+7 days')->format('Y-m-d')]);
$nextRdv = $stmtRdv->fetchAll();

$data = [
    'current_week_total' => $currentTotal,
    'previous_week_total' => $prevTotal,
    'top_leads' => $topLeads,
    'next_rdv' => $nextRdv,
];

$templatePath = __DIR__ . '/../includes/email_templates/weekly_report.php';
$html = '';
if (is_file($templatePath)) {
    $html = (string) include $templatePath;
}

$mailer = new Mailer();
$success = $mailer->send((string) siteConfig('admin_email', ''), '[' . siteConfig('name', 'EstimIA') . '] Rapport hebdomadaire', $html, 'Rapport hebdomadaire EstimIA');

$log = $pdo->prepare('INSERT INTO activity_log (action, details, admin_email) VALUES (:action, :details, :admin_email)');
$log->execute([
    'action' => 'weekly_report',
    'details' => json_encode(['success' => $success, 'week_start' => $weekStart->format('Y-m-d')], JSON_UNESCAPED_UNICODE),
    'admin_email' => (string) siteConfig('admin_email', ''),
]);

echo $success ? "weekly report sent\n" : "weekly report failed\n";
