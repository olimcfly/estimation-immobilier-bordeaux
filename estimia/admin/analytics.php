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

$periode = (string) ($_GET['periode'] ?? '30j');
$allowedPeriods = ['7j', '30j', '90j', '365j'];
if (!in_array($periode, $allowedPeriods, true)) {
    $periode = '30j';
}

$days = (int) rtrim($periode, 'j');
$dateFinObj = new DateTimeImmutable('today 23:59:59');
$dateDebutObj = $dateFinObj->modify('-' . ($days - 1) . ' day')->setTime(0, 0, 0);

$prevDateFinObj = $dateDebutObj->modify('-1 second');
$prevDateDebutObj = $prevDateFinObj->modify('-' . ($days - 1) . ' day')->setTime(0, 0, 0);

$dateDebut = $dateDebutObj->format('Y-m-d H:i:s');
$dateFin = $dateFinObj->format('Y-m-d H:i:s');
$prevDateDebut = $prevDateDebutObj->format('Y-m-d H:i:s');
$prevDateFin = $prevDateFinObj->format('Y-m-d H:i:s');

$stmtLeadsByDay = $pdo->prepare(
    'SELECT DATE(created_at) as jour, COUNT(*) as total,
            SUM(CASE WHEN lead_type = "estimation_gratuite" THEN 1 ELSE 0 END) as simples,
            SUM(CASE WHEN lead_type = "estimation_detaillee" THEN 1 ELSE 0 END) as detaillees,
            SUM(CASE WHEN lead_type = "rdv" THEN 1 ELSE 0 END) as rdv
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin
     GROUP BY DATE(created_at)
     ORDER BY jour'
);
$stmtLeadsByDay->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$leadsByDayRaw = $stmtLeadsByDay->fetchAll();

$leadsByDayIndexed = [];
foreach ($leadsByDayRaw as $row) {
    $dayKey = (string) ($row['jour'] ?? '');
    $leadsByDayIndexed[$dayKey] = [
        'total' => (int) ($row['total'] ?? 0),
        'simples' => (int) ($row['simples'] ?? 0),
        'detaillees' => (int) ($row['detaillees'] ?? 0),
        'rdv' => (int) ($row['rdv'] ?? 0),
    ];
}

$leadsByDay = [];
for ($i = 0; $i < $days; $i++) {
    $day = $dateDebutObj->modify('+' . $i . ' day')->format('Y-m-d');
    $leadsByDay[] = [
        'jour' => $day,
        'total' => $leadsByDayIndexed[$day]['total'] ?? 0,
        'simples' => $leadsByDayIndexed[$day]['simples'] ?? 0,
        'detaillees' => $leadsByDayIndexed[$day]['detaillees'] ?? 0,
        'rdv' => $leadsByDayIndexed[$day]['rdv'] ?? 0,
    ];
}

$stmtFunnel = $pdo->prepare(
    'SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN lead_type = "estimation_gratuite" THEN 1 ELSE 0 END) AS simples,
        SUM(CASE WHEN lead_type = "estimation_detaillee" THEN 1 ELSE 0 END) AS detaillees,
        SUM(CASE WHEN rdv_pris = 1 OR lead_type = "rdv" THEN 1 ELSE 0 END) AS rdv,
        SUM(CASE WHEN lead_statut = "converti" THEN 1 ELSE 0 END) AS convertis,
        SUM(CASE WHEN lead_statut = "converti" THEN prix_estime ELSE 0 END) AS revenu_converti
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin'
);
$stmtFunnel->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$funnel = $stmtFunnel->fetch() ?: [];

$stmtFunnelPrev = $pdo->prepare(
    'SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN lead_type = "estimation_gratuite" THEN 1 ELSE 0 END) AS simples,
        SUM(CASE WHEN lead_type = "estimation_detaillee" THEN 1 ELSE 0 END) AS detaillees,
        SUM(CASE WHEN rdv_pris = 1 OR lead_type = "rdv" THEN 1 ELSE 0 END) AS rdv,
        SUM(CASE WHEN lead_statut = "converti" THEN 1 ELSE 0 END) AS convertis,
        SUM(CASE WHEN lead_statut = "converti" THEN prix_estime ELSE 0 END) AS revenu_converti
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin'
);
$stmtFunnelPrev->execute(['date_debut' => $prevDateDebut, 'date_fin' => $prevDateFin]);
$funnelPrev = $stmtFunnelPrev->fetch() ?: [];

$stmtCity = $pdo->prepare(
    'SELECT ville, COUNT(*) as total, AVG(prix_estime) as prix_moy, AVG(lead_score) as score_moy
     FROM estimations
     WHERE ville IS NOT NULL AND ville <> "" AND created_at BETWEEN :date_debut AND :date_fin
     GROUP BY ville
     ORDER BY total DESC
     LIMIT 10'
);
$stmtCity->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$cities = $stmtCity->fetchAll();

$stmtTypeBien = $pdo->prepare(
    'SELECT type_bien, COUNT(*) as total, AVG(surface) as surface_moy, AVG(prix_estime) as prix_moy
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin
     GROUP BY type_bien
     ORDER BY total DESC'
);
$stmtTypeBien->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$typeBienStats = $stmtTypeBien->fetchAll();

$stmtSource = $pdo->prepare(
    'SELECT COALESCE(NULLIF(utm_source, ""), "direct") as source, COUNT(*) as total,
            SUM(CASE WHEN rdv_pris = 1 THEN 1 ELSE 0 END) as rdv_total
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin
     GROUP BY source
     ORDER BY total DESC'
);
$stmtSource->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$sourceStats = $stmtSource->fetchAll();

$stmtAgent = $pdo->prepare(
    'SELECT a.nom, a.prenom, COUNT(e.id) as leads_assignes,
            SUM(CASE WHEN e.lead_statut = "converti" THEN 1 ELSE 0 END) as convertis
     FROM agents a
     LEFT JOIN estimations e ON a.id = e.agent_assigne
        AND e.created_at BETWEEN :date_debut AND :date_fin
     GROUP BY a.id
     ORDER BY leads_assignes DESC, convertis DESC'
);
$stmtAgent->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$agentStats = $stmtAgent->fetchAll();

$stmtPrixVille = $pdo->prepare(
    'SELECT ville, AVG(prix_estime) as prix_moyen
     FROM estimations
     WHERE ville IS NOT NULL AND ville <> "" AND created_at BETWEEN :date_debut AND :date_fin
     GROUP BY ville
     ORDER BY prix_moyen DESC'
);
$stmtPrixVille->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$prixVilleStats = $stmtPrixVille->fetchAll();

$stmtHours = $pdo->prepare(
    'SELECT HOUR(created_at) as heure, COUNT(*) as total
     FROM estimations
     WHERE created_at BETWEEN :date_debut AND :date_fin
     GROUP BY HOUR(created_at)
     ORDER BY heure'
);
$stmtHours->execute(['date_debut' => $dateDebut, 'date_fin' => $dateFin]);
$hoursRaw = $stmtHours->fetchAll();

$hours = array_fill(0, 24, 0);
foreach ($hoursRaw as $hourRow) {
    $h = (int) ($hourRow['heure'] ?? 0);
    $hours[$h] = (int) ($hourRow['total'] ?? 0);
}

$totalLeads = (int) ($funnel['total'] ?? 0);
$totalSimples = (int) ($funnel['simples'] ?? 0);
$totalDetaillees = (int) ($funnel['detaillees'] ?? 0);
$totalRdv = (int) ($funnel['rdv'] ?? 0);
$totalConvertis = (int) ($funnel['convertis'] ?? 0);
$revenuConverti = (float) ($funnel['revenu_converti'] ?? 0);

$totalLeadsPrev = (int) ($funnelPrev['total'] ?? 0);
$totalSimplesPrev = (int) ($funnelPrev['simples'] ?? 0);
$totalDetailleesPrev = (int) ($funnelPrev['detaillees'] ?? 0);
$totalRdvPrev = (int) ($funnelPrev['rdv'] ?? 0);
$totalConvertisPrev = (int) ($funnelPrev['convertis'] ?? 0);
$revenuConvertiPrev = (float) ($funnelPrev['revenu_converti'] ?? 0);

$avgLeadsPerDay = $days > 0 ? $totalLeads / $days : 0;
$avgLeadsPerDayPrev = $days > 0 ? $totalLeadsPrev / $days : 0;
$detailRate = $totalSimples > 0 ? ($totalDetaillees / $totalSimples) * 100 : 0;
$detailRatePrev = $totalSimplesPrev > 0 ? ($totalDetailleesPrev / $totalSimplesPrev) * 100 : 0;
$rdvRate = $totalLeads > 0 ? ($totalRdv / $totalLeads) * 100 : 0;
$rdvRatePrev = $totalLeadsPrev > 0 ? ($totalRdvPrev / $totalLeadsPrev) * 100 : 0;
$conversionRate = $totalLeads > 0 ? ($totalConvertis / $totalLeads) * 100 : 0;
$conversionRatePrev = $totalLeadsPrev > 0 ? ($totalConvertisPrev / $totalLeadsPrev) * 100 : 0;

$variance = static function (float $current, float $previous): float {
    if ($previous == 0.0) {
        return $current > 0 ? 100.0 : 0.0;
    }
    return (($current - $previous) / abs($previous)) * 100;
};

$formatCompactEuro = static function (float $amount): string {
    if ($amount >= 1000000) {
        return number_format($amount / 1000000, 1, ',', ' ') . 'M €';
    }
    if ($amount >= 1000) {
        return number_format($amount / 1000, 1, ',', ' ') . 'K €';
    }
    return number_format($amount, 0, ',', ' ') . ' €';
};

$kpis = [
    ['icon' => 'users', 'label' => 'Total leads', 'value' => number_format($totalLeads, 0, ',', ' '), 'variation' => $variance((float) $totalLeads, (float) $totalLeadsPrev)],
    ['icon' => 'calendar-days', 'label' => 'Leads / jour moyen', 'value' => number_format($avgLeadsPerDay, 1, ',', ' '), 'variation' => $variance($avgLeadsPerDay, $avgLeadsPerDayPrev)],
    ['icon' => 'file-text', 'label' => 'Taux détaillée', 'value' => number_format($detailRate, 1, ',', ' ') . '%', 'variation' => $variance($detailRate, $detailRatePrev)],
    ['icon' => 'calendar-check', 'label' => 'Taux RDV', 'value' => number_format($rdvRate, 1, ',', ' ') . '%', 'variation' => $variance($rdvRate, $rdvRatePrev)],
    ['icon' => 'target', 'label' => 'Taux conversion', 'value' => number_format($conversionRate, 1, ',', ' ') . '%', 'variation' => $variance($conversionRate, $conversionRatePrev)],
    ['icon' => 'euro', 'label' => 'Revenu potentiel', 'value' => $formatCompactEuro($revenuConverti), 'variation' => $variance($revenuConverti, $revenuConvertiPrev)],
];

$maxDailyTotal = 1;
foreach ($leadsByDay as $day) {
    $maxDailyTotal = max($maxDailyTotal, (int) $day['total']);
}

$maxHourTotal = max(1, max($hours));

$adminPageTitle = 'Analytiques';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="space-y-6">
    <section class="flex flex-wrap items-center justify-between gap-3 rounded-xl border bg-white p-4">
        <div class="flex items-center gap-2">
            <label for="periode" class="text-sm font-semibold text-gray-600">Période :</label>
            <select id="periode" class="rounded-lg border px-3 py-2 text-sm" onchange="window.location='analytics.php?periode='+this.value">
                <option value="7j" <?php echo $periode === '7j' ? 'selected' : ''; ?>>7 jours</option>
                <option value="30j" <?php echo $periode === '30j' ? 'selected' : ''; ?>>30 jours</option>
                <option value="90j" <?php echo $periode === '90j' ? 'selected' : ''; ?>>90 jours</option>
                <option value="365j" <?php echo $periode === '365j' ? 'selected' : ''; ?>>365 jours</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <a href="export.php?type=analytics_csv&period=<?php echo urlencode($periode); ?>" class="rounded-lg border px-3 py-2 text-sm font-semibold">Exporter CSV</a>
            <button type="button" onclick="window.print()" class="rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white">Exporter PDF</button>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
        <?php foreach ($kpis as $kpi):
            $isPositive = $kpi['variation'] >= 0;
            ?>
            <article class="rounded-xl border bg-white p-4">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase text-gray-500"><?php echo sanitize($kpi['label']); ?></p>
                    <i data-lucide="<?php echo sanitize($kpi['icon']); ?>" class="h-4 w-4 text-gray-400"></i>
                </div>
                <p class="text-2xl font-extrabold text-gray-900"><?php echo sanitize($kpi['value']); ?></p>
                <p class="mt-1 text-xs <?php echo $isPositive ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $isPositive ? '↑' : '↓'; ?> <?php echo number_format(abs($kpi['variation']), 1, ',', ' '); ?>% vs période précédente
                </p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <article class="rounded-xl border bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Évolution des leads</h2>
            <div class="flex items-end gap-1 overflow-x-auto pb-2" style="height:260px;">
                <?php foreach ($leadsByDay as $day):
                    $height = (int) round(((int) $day['total'] / $maxDailyTotal) * 200);
                    $simples = (int) $day['simples'];
                    $detaillees = (int) $day['detaillees'];
                    $rdv = (int) $day['rdv'];
                    $sumSegments = max(1, $simples + $detaillees + $rdv);
                    ?>
                    <div class="flex min-w-[28px] flex-col items-center gap-1 text-[10px]">
                        <div class="flex w-5 flex-col overflow-hidden rounded-sm" style="height: <?php echo max(4, $height); ?>px;" title="<?php echo $day['jour']; ?>">
                            <span style="height: <?php echo max(1, (int) round(($simples / $sumSegments) * 100)); ?>%; background:#93c5fd;"></span>
                            <span style="height: <?php echo max(1, (int) round(($detaillees / $sumSegments) * 100)); ?>%; background:#fbbf24;"></span>
                            <span style="height: <?php echo max(1, (int) round(($rdv / $sumSegments) * 100)); ?>%; background:#34d399;"></span>
                        </div>
                        <span class="text-gray-400"><?php echo (new DateTimeImmutable((string) $day['jour']))->format('d/m'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 flex flex-wrap gap-4 text-xs">
                <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-blue-300"></i> Simples</span>
                <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-amber-300"></i> Détaillées</span>
                <span class="inline-flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-green-400"></i> RDV</span>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Funnel de conversion</h2>
            <?php
            $funnelStages = [
                ['label' => 'Estimations simples', 'value' => $totalSimples, 'rate' => 100],
                ['label' => 'Estimation détaillée', 'value' => $totalDetaillees, 'rate' => $totalSimples > 0 ? ($totalDetaillees / $totalSimples) * 100 : 0],
                ['label' => 'RDV pris', 'value' => $totalRdv, 'rate' => $totalLeads > 0 ? ($totalRdv / $totalLeads) * 100 : 0],
                ['label' => 'Leads convertis', 'value' => $totalConvertis, 'rate' => $totalLeads > 0 ? ($totalConvertis / $totalLeads) * 100 : 0],
            ];
            ?>
            <div class="space-y-3">
                <?php foreach ($funnelStages as $stage): ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="text-gray-600"><?php echo sanitize($stage['label']); ?></span>
                            <span class="font-semibold"><?php echo (int) $stage['value']; ?> (<?php echo number_format((float) $stage['rate'], 1, ',', ' '); ?>%)</span>
                        </div>
                        <div class="h-2 w-full rounded bg-gray-100">
                            <div class="h-2 rounded bg-primary" style="width: <?php echo max(2, min(100, (int) round((float) $stage['rate']))); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Heures de pic</h2>
            <div class="space-y-2">
                <?php foreach ($hours as $hour => $count): ?>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-10 text-gray-500"><?php echo str_pad((string) $hour, 2, '0', STR_PAD_LEFT); ?>h</span>
                        <div class="h-2 flex-1 rounded bg-gray-100">
                            <div class="h-2 rounded bg-indigo-500" style="width: <?php echo (int) round(($count / $maxHourTotal) * 100); ?>%;"></div>
                        </div>
                        <span class="w-7 text-right text-gray-700"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Top villes</h2>
            <div class="space-y-2">
                <?php foreach ($cities as $city): ?>
                    <div class="rounded-lg bg-gray-50 p-3 text-sm">
                        <p class="font-semibold text-gray-900"><?php echo sanitize((string) ($city['ville'] ?? '-')); ?> <span class="text-xs text-gray-400">(<?php echo (int) ($city['total'] ?? 0); ?>)</span></p>
                        <p class="text-xs text-gray-500">Prix moyen : <?php echo formatPrice((int) ($city['prix_moy'] ?? 0)); ?> • Score : <?php echo number_format((float) ($city['score_moy'] ?? 0), 1, ',', ' '); ?>/100</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Types de bien</h2>
            <div class="space-y-2 text-sm">
                <?php foreach ($typeBienStats as $type): ?>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <p class="font-semibold"><?php echo sanitize((string) ($type['type_bien'] ?? '-')); ?> <span class="text-xs text-gray-400">(<?php echo (int) ($type['total'] ?? 0); ?>)</span></p>
                        <p class="text-xs text-gray-500">Surface moy. : <?php echo number_format((float) ($type['surface_moy'] ?? 0), 1, ',', ' '); ?> m² • Prix moy. : <?php echo formatPrice((int) ($type['prix_moy'] ?? 0)); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Sources</h2>
            <div class="space-y-2 text-sm">
                <?php foreach ($sourceStats as $source):
                    $total = (int) ($source['total'] ?? 0);
                    $rdvTotal = (int) ($source['rdv_total'] ?? 0);
                    $rate = $total > 0 ? ($rdvTotal / $total) * 100 : 0;
                    ?>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <p class="font-semibold"><?php echo sanitize((string) ($source['source'] ?? 'direct')); ?> <span class="text-xs text-gray-400">(<?php echo $total; ?>)</span></p>
                        <p class="text-xs text-gray-500">RDV : <?php echo $rdvTotal; ?> (<?php echo number_format($rate, 1, ',', ' '); ?>%)</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Performance par agent</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="border-b text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="py-2">Agent</th>
                        <th class="py-2">Leads assignés</th>
                        <th class="py-2">Convertis</th>
                        <th class="py-2">Taux</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($agentStats as $agent):
                        $assigned = (int) ($agent['leads_assignes'] ?? 0);
                        $converted = (int) ($agent['convertis'] ?? 0);
                        $rate = $assigned > 0 ? ($converted / $assigned) * 100 : 0;
                        ?>
                        <tr class="border-b">
                            <td class="py-2 font-semibold"><?php echo sanitize(trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''))); ?></td>
                            <td class="py-2"><?php echo $assigned; ?></td>
                            <td class="py-2"><?php echo $converted; ?></td>
                            <td class="py-2"><?php echo number_format($rate, 1, ',', ' '); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-xl border bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Prix moyen par ville</h2>
            <div class="grid gap-2 md:grid-cols-2">
                <?php foreach ($prixVilleStats as $prixVille): ?>
                    <div class="rounded-lg bg-gray-50 p-3 text-sm">
                        <p class="font-semibold text-gray-900"><?php echo sanitize((string) ($prixVille['ville'] ?? '-')); ?></p>
                        <p class="text-xs text-gray-500">Prix moyen estimé : <?php echo formatPrice((int) ($prixVille['prix_moyen'] ?? 0)); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
