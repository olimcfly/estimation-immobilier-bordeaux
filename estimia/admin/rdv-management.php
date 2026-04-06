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

$pdo = Database::getConnection();

$weekOffset = (int) ($_GET['week'] ?? 0);
$today = new DateTimeImmutable('today');
$weekStart = $today->modify('monday this week')->modify(($weekOffset >= 0 ? '+' : '') . $weekOffset . ' week');
$weekEnd = $weekStart->modify('+6 day');

$filterStatut = (string) ($_GET['statut'] ?? '');
$filterAgent = (int) ($_GET['agent'] ?? 0);
$filterDateFrom = (string) ($_GET['date_from'] ?? '');
$filterDateTo = (string) ($_GET['date_to'] ?? '');

$stmtAgents = $pdo->query('SELECT id, nom, prenom FROM agents WHERE actif = 1 ORDER BY nom, prenom');
$agents = $stmtAgents->fetchAll();

$stmtStats = $pdo->prepare(
    'SELECT
        SUM(CASE WHEN date_souhaitee = CURDATE() THEN 1 ELSE 0 END) AS rdv_today,
        SUM(CASE WHEN date_souhaitee BETWEEN :week_start AND :week_end THEN 1 ELSE 0 END) AS rdv_week,
        SUM(CASE WHEN statut = "nouveau" THEN 1 ELSE 0 END) AS rdv_pending,
        SUM(CASE WHEN statut = "confirme" THEN 1 ELSE 0 END) AS rdv_done,
        COUNT(*) AS rdv_total
     FROM rdv'
);
$stmtStats->execute([
    'week_start' => $weekStart->format('Y-m-d'),
    'week_end' => $weekEnd->format('Y-m-d'),
]);
$stats = $stmtStats->fetch() ?: [];

$rdvDone = (int) ($stats['rdv_done'] ?? 0);
$rdvTotal = (int) ($stats['rdv_total'] ?? 0);
$completionRate = $rdvTotal > 0 ? round(($rdvDone / $rdvTotal) * 100, 1) : 0;

$calendarStmt = $pdo->prepare(
    'SELECT r.*, e.id AS lead_id, e.ville, e.prix_estime, e.type_bien, e.surface, e.agent_assigne,
            a.nom AS agent_nom, a.prenom AS agent_prenom
     FROM rdv r
     INNER JOIN estimations e ON e.id = r.estimation_id
     LEFT JOIN agents a ON a.id = e.agent_assigne
     WHERE r.date_souhaitee BETWEEN :week_start AND :week_end
     ORDER BY r.date_souhaitee ASC,
              FIELD(r.creneau, "matin", "apres_midi", "soir") ASC,
              r.created_at ASC'
);
$calendarStmt->execute([
    'week_start' => $weekStart->format('Y-m-d'),
    'week_end' => $weekEnd->format('Y-m-d'),
]);
$calendarRdvs = $calendarStmt->fetchAll();

$calendarByDay = [];
for ($i = 0; $i < 7; $i++) {
    $date = $weekStart->modify('+' . $i . ' day')->format('Y-m-d');
    $calendarByDay[$date] = [];
}
foreach ($calendarRdvs as $rdv) {
    $dayKey = (string) ($rdv['date_souhaitee'] ?? '');
    if (isset($calendarByDay[$dayKey])) {
        $calendarByDay[$dayKey][] = $rdv;
    }
}

$where = [];
$params = [];

if ($filterStatut !== '' && in_array($filterStatut, ['nouveau', 'confirme', 'contacte', 'annule'], true)) {
    $where[] = 'r.statut = :statut';
    $params['statut'] = $filterStatut;
}

if ($filterAgent > 0) {
    $where[] = 'e.agent_assigne = :agent';
    $params['agent'] = $filterAgent;
}

if ($filterDateFrom !== '') {
    $where[] = 'r.date_souhaitee >= :date_from';
    $params['date_from'] = $filterDateFrom;
}

if ($filterDateTo !== '') {
    $where[] = 'r.date_souhaitee <= :date_to';
    $params['date_to'] = $filterDateTo;
}

$sql =
    'SELECT r.*, e.id AS lead_id, e.ville, e.prix_estime, e.type_bien, e.surface, e.agent_assigne,
            e.nom AS lead_nom, e.telephone AS lead_telephone,
            a.nom AS agent_nom, a.prenom AS agent_prenom
     FROM rdv r
     INNER JOIN estimations e ON e.id = r.estimation_id
     LEFT JOIN agents a ON a.id = e.agent_assigne';

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY r.date_souhaitee DESC, FIELD(r.creneau, "matin", "apres_midi", "soir") ASC';

$stmtTable = $pdo->prepare($sql);
$stmtTable->execute($params);
$rdvTable = $stmtTable->fetchAll();

$adminPageTitle = 'Gestion des RDV';
require_once __DIR__ . '/includes/admin_header.php';

$dayLabels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
$slotLabels = ['matin' => 'Matin', 'apres_midi' => 'Après-midi', 'soir' => 'Soir'];
$slotClasses = ['matin' => 'bg-blue-100 text-blue-700', 'apres_midi' => 'bg-amber-100 text-amber-700', 'soir' => 'bg-purple-100 text-purple-700'];
$statusClasses = ['nouveau' => 'bg-gray-100 text-gray-700', 'contacte' => 'bg-yellow-100 text-yellow-700', 'confirme' => 'bg-green-100 text-green-700', 'annule' => 'bg-red-100 text-red-700'];
?>

<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border bg-white p-5">
            <p class="text-xs font-semibold uppercase text-gray-500">RDV ce jour</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900"><?php echo (int) ($stats['rdv_today'] ?? 0); ?></p>
        </article>
        <article class="rounded-xl border bg-white p-5">
            <p class="text-xs font-semibold uppercase text-gray-500">RDV cette semaine</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900"><?php echo (int) ($stats['rdv_week'] ?? 0); ?></p>
        </article>
        <article class="rounded-xl border bg-white p-5">
            <p class="text-xs font-semibold uppercase text-gray-500">RDV en attente</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900"><?php echo (int) ($stats['rdv_pending'] ?? 0); ?></p>
        </article>
        <article class="rounded-xl border bg-white p-5">
            <p class="text-xs font-semibold uppercase text-gray-500">Taux de réalisation</p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900"><?php echo number_format($completionRate, 1, ',', ' '); ?>%</p>
        </article>
    </section>

    <section class="rounded-xl border bg-white p-4">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Calendrier hebdomadaire</h2>
                <p class="text-sm text-gray-500">Semaine du <?php echo $weekStart->format('d/m/Y'); ?> au <?php echo $weekEnd->format('d/m/Y'); ?></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="?week=<?php echo $weekOffset - 1; ?>" class="rounded-lg border px-3 py-2 text-sm">← Semaine précédente</a>
                <a href="?week=<?php echo $weekOffset + 1; ?>" class="rounded-lg border px-3 py-2 text-sm">Semaine suivante →</a>
            </div>
        </div>

        <div class="hidden gap-3 lg:grid lg:grid-cols-7">
            <?php for ($i = 0; $i < 7; $i++):
                $date = $weekStart->modify('+' . $i . ' day');
                $dayKey = $date->format('Y-m-d');
                $items = $calendarByDay[$dayKey] ?? [];
                ?>
                <div class="min-h-[280px] rounded-lg border bg-gray-50 p-3">
                    <p class="mb-3 text-sm font-semibold text-gray-900"><?php echo $dayLabels[$i]; ?> <?php echo $date->format('d'); ?></p>

                    <?php if (empty($items)): ?>
                        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-3 text-xs text-gray-500">Aucun RDV</div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($items as $item): ?>
                                <a href="lead.php?id=<?php echo (int) $item['lead_id']; ?>" class="block rounded-lg border bg-white p-2 text-xs hover:border-blue-300 hover:bg-blue-50">
                                    <div class="mb-1 flex items-center justify-between gap-2">
                                        <span class="rounded px-2 py-0.5 font-semibold <?php echo $slotClasses[(string) $item['creneau']] ?? 'bg-gray-100 text-gray-700'; ?>"><?php echo $slotLabels[(string) $item['creneau']] ?? '-'; ?></span>
                                        <span class="rounded-full px-2 py-0.5 <?php echo $statusClasses[(string) $item['statut']] ?? 'bg-gray-100 text-gray-700'; ?>"><?php echo sanitize((string) $item['statut']); ?></span>
                                    </div>
                                    <p class="font-semibold text-gray-900"><?php echo sanitize((string) ($item['nom'] ?? 'Contact')); ?></p>
                                    <p class="truncate text-gray-500"><?php echo sanitize((string) ($item['ville'] ?? '-')); ?></p>
                                    <p class="text-gray-600"><?php echo formatPrice((int) ($item['prix_estime'] ?? 0)); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>

        <div class="space-y-3 lg:hidden">
            <?php for ($i = 0; $i < 7; $i++):
                $date = $weekStart->modify('+' . $i . ' day');
                $dayKey = $date->format('Y-m-d');
                $items = $calendarByDay[$dayKey] ?? [];
                ?>
                <div class="rounded-lg border bg-gray-50 p-3">
                    <p class="mb-2 text-sm font-semibold text-gray-900"><?php echo $dayLabels[$i]; ?> <?php echo $date->format('d'); ?></p>
                    <?php if (empty($items)): ?>
                        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-3 text-xs text-gray-500">Aucun RDV</div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($items as $item): ?>
                                <a href="lead.php?id=<?php echo (int) $item['lead_id']; ?>" class="block rounded-lg border bg-white p-2 text-xs">
                                    <p class="font-semibold text-gray-900"><?php echo sanitize((string) ($item['nom'] ?? 'Contact')); ?></p>
                                    <p class="text-gray-500"><?php echo sanitize((string) ($item['ville'] ?? '-')); ?> • <?php echo $slotLabels[(string) $item['creneau']] ?? '-'; ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </section>

    <section class="rounded-xl border bg-white p-4">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Tableau des RDV</h2>
        </div>

        <form method="GET" class="mb-4 grid gap-3 md:grid-cols-5">
            <input type="hidden" name="week" value="<?php echo $weekOffset; ?>">
            <select name="statut" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">Tous statuts</option>
                <?php foreach (['nouveau', 'contacte', 'confirme', 'annule'] as $status): ?>
                    <option value="<?php echo $status; ?>" <?php echo $filterStatut === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                <?php endforeach; ?>
            </select>

            <select name="agent" class="rounded-lg border px-3 py-2 text-sm">
                <option value="0">Tous agents</option>
                <?php foreach ($agents as $agent):
                    $name = trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''));
                    ?>
                    <option value="<?php echo (int) $agent['id']; ?>" <?php echo $filterAgent === (int) $agent['id'] ? 'selected' : ''; ?>><?php echo sanitize($name); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="date_from" value="<?php echo htmlspecialchars($filterDateFrom, ENT_QUOTES, 'UTF-8'); ?>" class="rounded-lg border px-3 py-2 text-sm">
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($filterDateTo, ENT_QUOTES, 'UTF-8'); ?>" class="rounded-lg border px-3 py-2 text-sm">
            <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Filtrer</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="border-b text-left text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-3 py-2">Date</th>
                    <th class="px-3 py-2">Créneau</th>
                    <th class="px-3 py-2">Contact</th>
                    <th class="px-3 py-2">Ville</th>
                    <th class="px-3 py-2">Bien</th>
                    <th class="px-3 py-2">Prix estimé</th>
                    <th class="px-3 py-2">Statut RDV</th>
                    <th class="px-3 py-2">Agent</th>
                    <th class="px-3 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rdvTable as $row):
                    $agentName = trim((string) ($row['agent_prenom'] ?? '') . ' ' . (string) ($row['agent_nom'] ?? ''));
                    $contact = (string) (($row['lead_nom'] ?? '') !== '' ? $row['lead_nom'] : ($row['nom'] ?? 'Anonyme'));
                    ?>
                    <tr class="border-b align-top">
                        <td class="px-3 py-3"><?php echo sanitize((string) ($row['date_souhaitee'] ?? '-')); ?></td>
                        <td class="px-3 py-3">
                            <span class="rounded px-2 py-0.5 text-xs font-semibold <?php echo $slotClasses[(string) $row['creneau']] ?? 'bg-gray-100 text-gray-700'; ?>"><?php echo $slotLabels[(string) $row['creneau']] ?? '-'; ?></span>
                        </td>
                        <td class="px-3 py-3">
                            <p class="font-semibold text-gray-900"><?php echo sanitize($contact); ?></p>
                            <p class="text-xs text-gray-500"><?php echo sanitize((string) ($row['lead_telephone'] ?? $row['telephone'] ?? '-')); ?></p>
                        </td>
                        <td class="px-3 py-3"><?php echo sanitize((string) ($row['ville'] ?? '-')); ?></td>
                        <td class="px-3 py-3"><?php echo sanitize((string) ($row['type_bien'] ?? '-')); ?> • <?php echo (int) ($row['surface'] ?? 0); ?> m²</td>
                        <td class="px-3 py-3 font-semibold text-primary"><?php echo formatPrice((int) ($row['prix_estime'] ?? 0)); ?></td>
                        <td class="px-3 py-3">
                            <select class="rdv-status rounded border px-2 py-1 text-xs" data-rdv-id="<?php echo (int) $row['id']; ?>">
                                <?php foreach (['nouveau', 'contacte', 'confirme', 'annule'] as $status): ?>
                                    <option value="<?php echo $status; ?>" <?php echo ((string) $row['statut'] === $status) ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-3 py-3"><?php echo sanitize($agentName !== '' ? $agentName : '-'); ?></td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" class="rdv-action rounded border px-2 py-1 text-xs" data-rdv-id="<?php echo (int) $row['id']; ?>" data-status="confirme">Confirmer</button>
                                <button type="button" class="rdv-action rounded border px-2 py-1 text-xs" data-rdv-id="<?php echo (int) $row['id']; ?>" data-status="confirme">Marquer effectué</button>
                                <button type="button" class="rdv-action rounded border px-2 py-1 text-xs" data-rdv-id="<?php echo (int) $row['id']; ?>" data-status="annule">Annuler</button>
                                <a class="rounded border px-2 py-1 text-xs" href="lead.php?id=<?php echo (int) $row['lead_id']; ?>">Voir la fiche</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    async function updateRdvStatus(rdvId, status) {
        const response = await fetch('api/update-rdv.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.adminCsrfToken || '',
            },
            body: JSON.stringify({rdv_id: Number(rdvId), field: 'statut', value: status}),
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur de mise à jour');
        }
    }

    document.querySelectorAll('.rdv-status').forEach((select) => {
        select.addEventListener('change', async function () {
            const previous = this.dataset.previous || this.querySelector('option[selected]')?.value || '';
            try {
                await updateRdvStatus(this.dataset.rdvId, this.value);
                this.dataset.previous = this.value;
            } catch (error) {
                alert(error.message);
                if (previous !== '') {
                    this.value = previous;
                }
            }
        });
    });

    document.querySelectorAll('.rdv-action').forEach((button) => {
        button.addEventListener('click', async function () {
            const targetStatus = this.dataset.status;
            try {
                await updateRdvStatus(this.dataset.rdvId, targetStatus);
                const row = this.closest('tr');
                const select = row ? row.querySelector('.rdv-status') : null;
                if (select) {
                    select.value = targetStatus;
                    select.dataset.previous = targetStatus;
                }
            } catch (error) {
                alert(error.message);
            }
        });
    });
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
