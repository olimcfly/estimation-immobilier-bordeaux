<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/AIRouter.php';

$city = trim((string) ($_POST['city'] ?? ''));
$radius = max(1, (int) ($_POST['radius'] ?? 30));

if ($city === '') {
    echo json_encode(['error' => 'city required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$router = new AIRouter();
$result = $router->getCities($city, $radius);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
