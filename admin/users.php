<?php

declare(strict_types=1);

$pageTitle = 'Gestion des utilisateurs';
$currentPage = 'users';
$topNavCurrent = 'settings';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/database.php';

$isSuperUser = (string) ($_SESSION['admin_email'] ?? '') === 'superuser@estimation-immobilier-bordeaux.fr'
    && (string) ($_SESSION['user_role'] ?? '') === 'admin';
if (!$isSuperUser) {
    http_response_code(403);
    echo '<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">Accès réservé au superutilisateur.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
    if ($sessionToken === '' || !hash_equals($sessionToken, $token)) {
        $_SESSION['users_flash_error'] = 'Token CSRF invalide.';
        header('Location: /admin/users.php');
        exit;
    }

    if (isset($_POST['toggle_user_id'])) {
        $userId = (int) $_POST['toggle_user_id'];
        $stmt = $db->prepare('UPDATE users SET actif = IF(actif = 1, 0, 1) WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $_SESSION['users_flash_success'] = 'Statut utilisateur mis à jour.';
        header('Location: /admin/users.php');
        exit;
    }
}

$users = $db->query('SELECT id, nom, prenom, email, role, actif, is_online, last_activity, last_page_visited, last_login FROM users ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$modules = $db->query('SELECT id, name, description, is_active FROM modules ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = (string) $_SESSION['csrf_token'];
?>

<div class="space-y-6">
    <?php if (!empty($_SESSION['users_flash_success'])): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700"><?php echo htmlspecialchars((string) $_SESSION['users_flash_success'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['users_flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['users_flash_error'])): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700"><?php echo htmlspecialchars((string) $_SESSION['users_flash_error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['users_flash_error']); ?>
    <?php endif; ?>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Comptes utilisateurs</h1>
            <a href="/cron/backup_db.php" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Lancer une sauvegarde DB</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Nom</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Rôle</th>
                        <th class="px-3 py-2">Actif</th>
                        <th class="px-3 py-2">En ligne</th>
                        <th class="px-3 py-2">Dernière activité</th>
                        <th class="px-3 py-2">Dernière page</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-3 py-2"><?php echo (int) $user['id']; ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars(trim((string) $user['prenom'] . ' ' . (string) $user['nom']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars((string) $user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2"><?php echo (int) $user['actif'] === 1 ? 'Oui' : 'Non'; ?></td>
                            <td class="px-3 py-2"><?php echo (int) $user['is_online'] === 1 ? 'Oui' : 'Non'; ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars((string) ($user['last_activity'] ?? 'Jamais'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2"><?php echo htmlspecialchars((string) ($user['last_page_visited'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-3 py-2">
                                <form method="post" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="toggle_user_id" value="<?php echo (int) $user['id']; ?>">
                                    <button type="submit" class="rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">
                                        <?php echo (int) $user['actif'] === 1 ? 'Désactiver' : 'Activer'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-xl font-semibold text-slate-800">Modules activables</h2>
        <div class="grid gap-3 md:grid-cols-2">
            <?php foreach ($modules as $module): ?>
                <article class="rounded-xl border border-slate-200 p-4">
                    <h3 class="text-base font-semibold"><?php echo htmlspecialchars((string) $module['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="mt-1 text-sm text-slate-600"><?php echo htmlspecialchars((string) ($module['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                    <button type="button" class="toggle-module mt-3 rounded-md px-3 py-1.5 text-xs font-semibold <?php echo (int) $module['is_active'] === 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-800'; ?>" data-id="<?php echo (int) $module['id']; ?>" data-token="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo (int) $module['is_active'] === 1 ? 'Désactiver' : 'Activer'; ?>
                    </button>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script>
document.querySelectorAll('.toggle-module').forEach((button) => {
    button.addEventListener('click', async () => {
        const moduleId = button.dataset.id;
        const token = button.dataset.token;

        const response = await fetch('/admin/ajax/toggle_module.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ module_id: moduleId, csrf_token: token })
        });

        if (!response.ok) {
            alert('Impossible de modifier ce module.');
            return;
        }

        window.location.reload();
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
