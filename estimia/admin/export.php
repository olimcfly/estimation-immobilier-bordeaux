<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    redirect('login.php');
}

if (($_GET['format'] ?? '') !== 'csv') {
    redirect('estimations.php');
}

$pdo = Database::getConnection();

$villeFilter = sanitize($_GET['ville'] ?? '');
$typeFilter = sanitize($_GET['type_bien'] ?? '');
$dateFrom = sanitize($_GET['date_from'] ?? '');
$dateTo = sanitize($_GET['date_to'] ?? '');

$where = [];
$params = [];

if ($villeFilter !== '') {
    $where[] = 'ville = :ville';
    $params['ville'] = $villeFilter;
}
if ($typeFilter !== '') {
    $where[] = 'type_bien = :type_bien';
    $params['type_bien'] = $typeFilter;
}
if ($dateFrom !== '') {
    $where[] = 'DATE(created_at) >= :date_from';
    $params['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'DATE(created_at) <= :date_to';
    $params['date_to'] = $dateTo;
}
if (isset($_GET['rdv']) && $_GET['rdv'] === '1') {
    $where[] = 'rdv_pris = 1';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT id, created_at, adresse, ville, type_bien, surface, budget_estimation, prix_estime, prix_m2, rdv_pris, nom, email, telephone FROM estimations {$whereSql} ORDER BY created_at DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="estimations-export.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Date', 'Adresse', 'Ville', 'Type', 'Surface', 'Budget', 'Prix estime', 'Prix m2', 'RDV', 'Nom', 'Email', 'Telephone'], ';');

foreach ($rows as $row) {
    fputcsv($output, [
        $row['id'] ?? '',
        $row['created_at'] ?? '',
        $row['adresse'] ?? '',
        $row['ville'] ?? '',
        $row['type_bien'] ?? '',
        $row['surface'] ?? '',
        $row['budget_estimation'] ?? '',
        $row['prix_estime'] ?? '',
        $row['prix_m2'] ?? '',
        (int) ($row['rdv_pris'] ?? 0) === 1 ? 'Oui' : 'Non',
        $row['nom'] ?? '',
        $row['email'] ?? '',
        $row['telephone'] ?? '',
    ], ';');
}

fclose($output);
exit;
