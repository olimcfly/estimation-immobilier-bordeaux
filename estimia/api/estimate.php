<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (!isPost()) {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$surface = (int) ($_POST['surface'] ?? 0);
$typeBien = sanitize($_POST['type_bien'] ?? 'appartement');
$ville = sanitize($_POST['ville'] ?? '');

$resultat = calculerEstimation($surface, $typeBien, $ville);

echo json_encode($resultat, JSON_UNESCAPED_UNICODE);
