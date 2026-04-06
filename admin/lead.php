<?php

require_once __DIR__ . '/../classes/Webhook.php';

function updateLeadStatus(array $lead, string $newStatus): void
{
    $oldStatus = $lead['status'] ?? 'unknown';

    // ... logique métier de mise à jour du statut

    Webhook::statusChanged($lead, $oldStatus, $newStatus);
}
