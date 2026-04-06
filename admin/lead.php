<?php

require_once __DIR__ . '/../classes/Webhook.php';

if (
    isset($_SERVER['SCRIPT_FILENAME'])
    && realpath((string) $_SERVER['SCRIPT_FILENAME']) === __FILE__
) {
    require_once __DIR__ . '/../includes/admin-auth.php';
}

function updateLeadStatus(array $lead, string $newStatus): void
{
    $oldStatus = $lead['status'] ?? 'unknown';

    // ... logique métier de mise à jour du statut

    Webhook::statusChanged($lead, $oldStatus, $newStatus);
}
