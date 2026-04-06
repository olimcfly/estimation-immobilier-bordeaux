<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/admin-auth.php';
require_once __DIR__ . '/../includes/activity-log.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

if (!verifyCsrf()) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalide.']);
    exit;
}

/**
 * Limite les actions critiques à 5 requêtes / minute / IP.
 */
function checkMinuteRateLimit(PDO $db, string $action, int $maxPerMinute = 5): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $cleanup = $db->prepare('DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
    $cleanup->execute();

    $countStmt = $db->prepare(
        'SELECT COUNT(*) FROM rate_limits
         WHERE ip = :ip
           AND action = :action
           AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)'
    );

    $countStmt->execute([
        'ip' => $ip,
        'action' => $action,
    ]);

    $count = (int) $countStmt->fetchColumn();
    if ($count >= $maxPerMinute) {
        return false;
    }

    $insert = $db->prepare('INSERT INTO rate_limits (ip, action) VALUES (:ip, :action)');
    $insert->execute([
        'ip' => $ip,
        'action' => $action,
    ]);

    return true;
}

$db = Database::getConnection();
$actionName = 'admin_toggle_module';
if (!checkMinuteRateLimit($db, $actionName, 5)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Trop de requêtes. Réessayez dans 1 minute.']);
    exit;
}

$moduleIdentifier = trim((string) ($_POST['module'] ?? $_POST['module_key'] ?? $_POST['slug'] ?? ''));
$moduleId = (int) ($_POST['id'] ?? 0);
$enabledRaw = $_POST['enabled'] ?? $_POST['is_active'] ?? null;

if ($enabledRaw === null || ($moduleIdentifier === '' && $moduleId <= 0)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Paramètres incomplets.']);
    exit;
}

$enabled = in_array((string) $enabledRaw, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;

try {
    $columnsStmt = $db->query('SHOW COLUMNS FROM modules');
    $columns = array_map(
        static fn (array $row): string => (string) ($row['Field'] ?? ''),
        $columnsStmt ? $columnsStmt->fetchAll(PDO::FETCH_ASSOC) : []
    );

    if (!in_array('is_active', $columns, true)) {
        throw new RuntimeException('La colonne is_active est introuvable dans la table modules.');
    }

    $whereField = null;
    if ($moduleId > 0 && in_array('id', $columns, true)) {
        $whereField = 'id';
    } elseif ($moduleIdentifier !== '' && in_array('module_key', $columns, true)) {
        $whereField = 'module_key';
    } elseif ($moduleIdentifier !== '' && in_array('slug', $columns, true)) {
        $whereField = 'slug';
    } elseif ($moduleIdentifier !== '' && in_array('key', $columns, true)) {
        $whereField = '`key`';
    } elseif ($moduleIdentifier !== '' && in_array('name', $columns, true)) {
        $whereField = 'name';
    }

    if ($whereField === null) {
        throw new RuntimeException('Impossible de déterminer la clé de recherche pour le module.');
    }

    $sql = 'UPDATE modules SET is_active = :is_active WHERE ' . $whereField . ' = :module_ref LIMIT 1';
    $stmt = $db->prepare($sql);

    $moduleRef = $moduleId > 0 && $whereField === 'id' ? $moduleId : $moduleIdentifier;

    $stmt->execute([
        'is_active' => $enabled,
        'module_ref' => $moduleRef,
    ]);

    if ($stmt->rowCount() < 1) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Module introuvable ou inchangé.']);
        exit;
    }

    adminLogAction(sprintf(
        'toggle_module: %s => %s',
        $moduleIdentifier !== '' ? $moduleIdentifier : ('#' . $moduleId),
        $enabled === 1 ? 'enabled' : 'disabled'
    ));

    echo json_encode([
        'success' => true,
        'message' => 'Module mis à jour.',
        'data' => [
            'module' => $moduleIdentifier !== '' ? $moduleIdentifier : $moduleId,
            'enabled' => $enabled,
        ],
    ]);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur lors de la mise à jour du module.',
        'error' => $exception->getMessage(),
    ]);
}
