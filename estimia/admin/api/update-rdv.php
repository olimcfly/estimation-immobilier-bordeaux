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

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verifyCSRFToken(is_string($csrfToken) ? $csrfToken : null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
    exit;
}

$payload = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payload invalide']);
    exit;
}

$rdvId = (int) ($payload['rdv_id'] ?? 0);
$field = (string) ($payload['field'] ?? '');
$value = (string) ($payload['value'] ?? '');

if ($rdvId <= 0 || $field !== 'statut') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

$allowedStatus = ['nouveau', 'contacte', 'confirme', 'annule'];
if (!in_array($value, $allowedStatus, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Statut invalide']);
    exit;
}

$pdo = Database::getConnection();

$stmtRdv = $pdo->prepare('SELECT id, estimation_id, statut FROM rdv WHERE id = :id LIMIT 1');
$stmtRdv->execute(['id' => $rdvId]);
$rdv = $stmtRdv->fetch();

if (!$rdv) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'RDV introuvable']);
    exit;
}

$stmtUpdate = $pdo->prepare('UPDATE rdv SET statut = :statut WHERE id = :id');
$stmtUpdate->execute(['statut' => $value, 'id' => $rdvId]);

$interactionType = $value === 'confirme' ? 'rdv_fixe' : 'note';
$description = 'Statut RDV mis à jour : ' . $value;
$stmtInteraction = $pdo->prepare(
    'INSERT INTO lead_interactions (estimation_id, type_interaction, description)
     VALUES (:estimation_id, :type_interaction, :description)'
);
$stmtInteraction->execute([
    'estimation_id' => (int) $rdv['estimation_id'],
    'type_interaction' => $interactionType,
    'description' => $description,
]);

echo json_encode(['success' => true]);
