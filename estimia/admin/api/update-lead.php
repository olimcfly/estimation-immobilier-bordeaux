<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$payload = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payload invalide']);
    exit;
}

$leadId = (int) ($payload['lead_id'] ?? 0);
$field = (string) ($payload['field'] ?? '');
$value = (string) ($payload['value'] ?? '');

if ($leadId <= 0 || $field !== 'lead_statut') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

$validStatuts = ['nouveau', 'contacte', 'qualifie', 'en_negociation', 'converti', 'perdu'];
if (!in_array($value, $validStatuts, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Statut invalide']);
    exit;
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare('UPDATE estimations SET lead_statut = :value WHERE id = :id');
$stmt->execute([
    'value' => $value,
    'id' => $leadId,
]);

$stmtInteraction = $pdo->prepare(
    'INSERT INTO lead_interactions (estimation_id, type_interaction, description)
     VALUES (:id, "changement_statut", :description)'
);
$stmtInteraction->execute([
    'id' => $leadId,
    'description' => 'Statut changé vers: ' . $value,
]);

echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
