<?php

declare(strict_types=1);

$pageTitle = 'Gestion des utilisateurs';
$currentPage = 'users';
$topNavCurrent = 'users';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

if ((string) ($_SESSION['admin_role'] ?? '') !== 'superadmin') {
    header('Location: /admin/');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function user_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$db = Database::getConnection();
$users = [];
$errorMessage = null;

try {
    $columnsStmt = $db->prepare(
        'SELECT COLUMN_NAME
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name'
    );

    $columnsStmt->execute(['table_name' => 'admins']);
    $adminColumns = array_map('strval', $columnsStmt->fetchAll(PDO::FETCH_COLUMN) ?: []);

    $columnsStmt->execute(['table_name' => 'user_sessions']);
    $sessionColumns = array_map('strval', $columnsStmt->fetchAll(PDO::FETCH_COLUMN) ?: []);

    $hasRole = in_array('role', $adminColumns, true);
    $hasIsOnline = in_array('is_online', $adminColumns, true);
    $hasIsActive = in_array('is_active', $adminColumns, true);
    $hasSessionTable = $sessionColumns !== [] && in_array('id', $sessionColumns, true) && in_array('user_id', $sessionColumns, true);

    $roleSelect = $hasRole ? 'a.role' : "'admin'";
    $onlineSelect = $hasIsOnline ? 'a.is_online' : '0';
    $activeSelect = $hasIsActive ? 'a.is_active' : '1';
    $sessionSelect = $hasSessionTable ? 'COUNT(s.id)' : '0';
    $sessionJoin = $hasSessionTable ? 'LEFT JOIN `user_sessions` s ON a.id = s.user_id' : '';

    $usersQuery = sprintf(
        'SELECT
            a.id,
            a.email,
            %s AS role,
            %s AS is_online,
            %s AS is_active,
            %s AS session_count
        FROM `admins` a
        %s
        GROUP BY a.id
        ORDER BY a.email ASC',
        $roleSelect,
        $onlineSelect,
        $activeSelect,
        $sessionSelect,
        $sessionJoin
    );

    $users = $db->query($usersQuery)->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $exception) {
    $errorMessage = 'Impossible de charger la liste des utilisateurs pour le moment.';
}
?>

<section class="space-y-5">
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Gestion des utilisateurs</h1>
        <p class="text-sm text-slate-500">Comptes admins, état de connexion et activations.</p>
    </div>

    <?php if ($errorMessage !== null): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= user_h($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto rounded-2xl border admin-card">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100/70 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900/50 dark:text-slate-300">
            <tr>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Rôle</th>
                <th class="px-4 py-3">En ligne</th>
                <th class="px-4 py-3">Sessions actives</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--admin-border)">
            <?php if ($users === []): ?>
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <?php
                $isOnline = ((int) ($user['is_online'] ?? 0)) === 1;
                $isActive = ((int) ($user['is_active'] ?? 1)) === 1;
                ?>
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                    <td class="px-4 py-3 font-medium"><?= user_h((string) ($user['email'] ?? '')) ?></td>
                    <td class="px-4 py-3"><?= user_h((string) ($user['role'] ?? 'admin')) ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $isOnline ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800' ?>">
                            <?= $isOnline ? 'Oui' : 'Non' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3"><?= (int) ($user['session_count'] ?? 0) ?></td>
                    <td class="px-4 py-3">
                        <button
                            type="button"
                            class="rounded px-3 py-1 text-xs font-semibold text-white <?= $isActive ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' ?>"
                            data-user-id="<?= (int) ($user['id'] ?? 0) ?>"
                            data-next-active="<?= $isActive ? '0' : '1' ?>"
                        >
                            <?= $isActive ? 'Désactiver' : 'Activer' ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
(function () {
    const csrfToken = <?= json_encode((string) $_SESSION['csrf_token'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const buttons = document.querySelectorAll('button[data-user-id][data-next-active]');

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const userId = button.dataset.userId;
            const nextActive = button.dataset.nextActive;

            if (!userId || !nextActive) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch('/admin/ajax/toggle_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-CSRF-Token': csrfToken
                    },
                    body: new URLSearchParams({
                        user_id: userId,
                        is_active: nextActive
                    }).toString()
                });

                const data = await response.json();
                if (data && data.success) {
                    window.location.reload();
                    return;
                }

                alert('Erreur : ' + ((data && data.message) ? data.message : 'Action impossible.'));
            } catch (error) {
                alert('Erreur réseau, veuillez réessayer.');
            } finally {
                button.disabled = false;
            }
        });
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
