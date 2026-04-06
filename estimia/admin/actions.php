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

if (!isPost()) {
    redirect('estimations.php');
}

$action = sanitize($_POST['action'] ?? '');
$idsRaw = $_POST['ids'] ?? [];
$redirectQuery = sanitize($_POST['redirect_query'] ?? '');

$ids = [];
if (is_array($idsRaw)) {
    foreach ($idsRaw as $id) {
        $idInt = (int) $id;
        if ($idInt > 0) {
            $ids[] = $idInt;
        }
    }
}
$ids = array_values(array_unique($ids));

$redirectUrl = 'estimations.php' . ($redirectQuery !== '' ? '?' . $redirectQuery : '');

if (!$ids && $action !== 'exporter') {
    $_SESSION['flash_message'] = 'Aucun lead sélectionné.';
    redirect($redirectUrl);
}

$pdo = Database::getConnection();

$logActivity = static function (PDO $pdo, string $actionName, string $details): void {
    $stmt = $pdo->prepare('INSERT INTO activity_log (action, details, admin_email) VALUES (:action, :details, :admin_email)');
    $stmt->execute([
        'action' => $actionName,
        'details' => $details,
        'admin_email' => (string) ($_SESSION['admin_email'] ?? ''),
    ]);
};

$buildIn = static function (array $values): array {
    $placeholders = [];
    $params = [];
    foreach ($values as $index => $value) {
        $key = ':id' . $index;
        $placeholders[] = $key;
        $params[$key] = (int) $value;
    }
    return [$placeholders, $params];
};

switch ($action) {
    case 'assigner':
        $agentAssigne = (int) ($_POST['agent_assigne'] ?? 0);
        if ($agentAssigne <= 0) {
            $_SESSION['flash_message'] = 'Veuillez sélectionner un agent.';
            redirect($redirectUrl);
        }

        [$placeholders, $params] = $buildIn($ids);
        $sql = 'UPDATE estimations SET agent_assigne = :agent WHERE id IN (' . implode(',', $placeholders) . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':agent', $agentAssigne, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();

        $logActivity($pdo, 'assigner', 'Assignation agent #' . $agentAssigne . ' sur leads: ' . implode(',', $ids));
        $_SESSION['flash_message'] = count($ids) . ' lead(s) assigné(s).';
        redirect($redirectUrl);
        break;

    case 'statut':
        $newStatut = sanitize($_POST['nouveau_statut'] ?? '');
        $validStatuts = ['nouveau', 'contacte', 'qualifie', 'en_negociation', 'converti', 'perdu'];
        if (!in_array($newStatut, $validStatuts, true)) {
            $_SESSION['flash_message'] = 'Statut invalide.';
            redirect($redirectUrl);
        }

        [$placeholders, $params] = $buildIn($ids);
        $sql = 'UPDATE estimations SET lead_statut = :statut WHERE id IN (' . implode(',', $placeholders) . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':statut', $newStatut, PDO::PARAM_STR);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();

        $logActivity($pdo, 'statut', 'Changement statut ' . $newStatut . ' sur leads: ' . implode(',', $ids));
        $_SESSION['flash_message'] = 'Statut mis à jour pour ' . count($ids) . ' lead(s).';
        redirect($redirectUrl);
        break;

    case 'supprimer':
        [$placeholders, $params] = $buildIn($ids);
        $sql = 'DELETE FROM estimations WHERE id IN (' . implode(',', $placeholders) . ')';
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();

        $logActivity($pdo, 'supprimer', 'Suppression leads: ' . implode(',', $ids));
        $_SESSION['flash_message'] = count($ids) . ' lead(s) supprimé(s).';
        redirect($redirectUrl);
        break;

    case 'exporter':
        if (!$ids) {
            $_SESSION['flash_message'] = 'Aucun lead sélectionné pour export.';
            redirect($redirectUrl);
        }

        [$placeholders, $params] = $buildIn($ids);
        $sql = 'SELECT id, created_at, nom, email, telephone, ville, type_bien, surface, prix_estime, lead_type, lead_statut, lead_score
                FROM estimations
                WHERE id IN (' . implode(',', $placeholders) . ')
                ORDER BY created_at DESC';
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $logActivity($pdo, 'exporter', 'Export CSV leads: ' . implode(',', $ids));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leads-selection.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Date', 'Nom', 'Email', 'Téléphone', 'Ville', 'Type', 'Surface', 'Prix', 'Type lead', 'Statut', 'Score'], ';');
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['id'] ?? '', $row['created_at'] ?? '', $row['nom'] ?? '', $row['email'] ?? '',
                $row['telephone'] ?? '', $row['ville'] ?? '', $row['type_bien'] ?? '', $row['surface'] ?? '',
                $row['prix_estime'] ?? '', $row['lead_type'] ?? '', $row['lead_statut'] ?? '', $row['lead_score'] ?? '',
            ], ';');
        }
        fclose($output);
        exit;

    default:
        $_SESSION['flash_message'] = 'Action inconnue.';
        redirect($redirectUrl);
}
