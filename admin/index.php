<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/database.php';

session_start();

if (empty($_SESSION['admin_logged']) || empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = Database::getConnection();
$totalEstimations = 0;
$lastEstimations = [];
$dashboardError = null;

try {
    $totalEstimations = (int) $db->query('SELECT COUNT(*) FROM estimations')->fetchColumn();
    $lastEstimationsStmt = $db->query(
        'SELECT id, prenom, email, type_bien, surface, ville, created_at
         FROM estimations
         ORDER BY created_at DESC
         LIMIT 10'
    );
    $lastEstimations = $lastEstimationsStmt->fetchAll();
} catch (Throwable $exception) {
    $dashboardError = 'Le tableau de bord est temporairement indisponible. Vérifiez la connexion à la base de données et la table "estimations".';
}

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$topNavCurrent = 'dashboard';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<h1 class="text-3xl font-bold text-slate-800">Dashboard administrateur</h1>
<p class="mt-2 text-slate-600">Vue rapide de l'activité des estimations.</p>

<?php if ($dashboardError !== null): ?>
    <div class="mt-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-amber-800">
        <?php echo htmlspecialchars($dashboardError, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<section class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
    <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm text-slate-500">Nombre total d'estimations</p>
        <p class="mt-2 text-3xl font-extrabold text-blue-700"><?php echo $totalEstimations; ?></p>
    </article>
</section>

<section class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <h2 class="text-lg font-semibold text-slate-800">Dernières estimations</h2>
    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
            <tr>
                <th class="px-4 py-3 font-medium">ID</th>
                <th class="px-4 py-3 font-medium">Prénom</th>
                <th class="px-4 py-3 font-medium">Email</th>
                <th class="px-4 py-3 font-medium">Type</th>
                <th class="px-4 py-3 font-medium">Surface</th>
                <th class="px-4 py-3 font-medium">Ville</th>
                <th class="px-4 py-3 font-medium">Date</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php if ($lastEstimations === []): ?>
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">Aucune estimation trouvée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lastEstimations as $estimation): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><?php echo (int) $estimation['id']; ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars((string) $estimation['prenom'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars((string) $estimation['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars((string) $estimation['type_bien'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="px-4 py-3"><?php echo (int) $estimation['surface']; ?> m²</td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars((string) $estimation['ville'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars((string) $estimation['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
