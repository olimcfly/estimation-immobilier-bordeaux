<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

session_start();

if (empty($_SESSION['admin_logged']) || empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = Database::getConnection();

$totalEstimations = (int) $db->query('SELECT COUNT(*) FROM estimations')->fetchColumn();
$lastEstimationsStmt = $db->query(
    'SELECT id, prenom, email, type_bien, surface, ville, created_at
     FROM estimations
     ORDER BY created_at DESC
     LIMIT 10'
);
$lastEstimations = $lastEstimationsStmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | EstimIA Bordeaux</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="flex min-h-screen">
    <aside class="w-64 bg-blue-900 p-6 text-white">
        <h1 class="text-xl font-bold">EstimIA Admin</h1>
        <p class="mt-1 text-sm text-blue-200">Bordeaux</p>
        <nav class="mt-8 space-y-2 text-sm">
            <a href="/admin/index.php" class="block rounded-md bg-blue-700 px-4 py-2">Dashboard</a>
            <a href="/admin/lead.php" class="block rounded-md px-4 py-2 hover:bg-blue-800">Leads</a>
            <a href="/admin/settings.php" class="block rounded-md px-4 py-2 hover:bg-blue-800">Paramètres</a>
            <a href="/admin/webhooks.php" class="block rounded-md px-4 py-2 hover:bg-blue-800">Webhooks</a>
            <a href="/admin/logout.php" class="block rounded-md px-4 py-2 hover:bg-blue-800">Déconnexion</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <h2 class="text-3xl font-bold text-slate-800">Dashboard administrateur</h2>
        <p class="mt-2 text-slate-600">Vue rapide de l'activité des estimations.</p>

        <section class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Nombre total d'estimations</p>
                <p class="mt-2 text-3xl font-extrabold text-blue-700"><?php echo $totalEstimations; ?></p>
            </article>
        </section>

        <section class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Dernières estimations</h3>
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
    </main>
</div>
</body>
</html>
