<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/mailer.php';

$pdo = Database::getConnection();
$mailer = new Mailer();

$stmt = $pdo->prepare(
    'SELECT id, nom, adresse, relance_prevue
     FROM estimations
     WHERE relance_prevue IS NOT NULL
       AND relance_prevue <= NOW()
       AND lead_statut NOT IN ("converti", "perdu")
     ORDER BY relance_prevue ASC'
);
$stmt->execute();
$leads = $stmt->fetchAll();

$sent = 0;
foreach ($leads as $lead) {
    $subject = '[EstimIA] Rappel de relance';
    $html = '<p>Rappel : relancer <strong>' . htmlspecialchars((string) ($lead['nom'] ?? 'Prospect'), ENT_QUOTES, 'UTF-8') . '</strong> pour son estimation à <strong>' . htmlspecialchars((string) ($lead['adresse'] ?? ''), ENT_QUOTES, 'UTF-8') . '</strong>.</p>'
        . '<p><a href="' . htmlspecialchars(rtrim((string) siteConfig('url', ''), '/') . '/admin/lead.php?id=' . (int) $lead['id'], ENT_QUOTES, 'UTF-8') . '">Ouvrir la fiche lead</a></p>';

    if ($mailer->send((string) siteConfig('admin_email', ''), $subject, $html, strip_tags($html))) {
        $sent++;
    }

    $update = $pdo->prepare('UPDATE estimations SET dernier_contact = NOW() WHERE id = :id');
    $update->execute(['id' => (int) $lead['id']]);

    $log = $pdo->prepare('INSERT INTO activity_log (action, details, admin_email) VALUES (:action, :details, :admin_email)');
    $log->execute([
        'action' => 'relance_reminder',
        'details' => json_encode(['lead_id' => (int) $lead['id'], 'adresse' => (string) ($lead['adresse'] ?? '')], JSON_UNESCAPED_UNICODE),
        'admin_email' => (string) siteConfig('admin_email', ''),
    ]);
}

echo 'Relances traitées: ' . $sent . '/' . count($leads) . "\n";
