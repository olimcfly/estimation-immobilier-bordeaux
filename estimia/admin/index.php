<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';

$adminPageTitle = 'Tableau de bord - ' . siteConfig('city', 'Zone');
require_once __DIR__ . '/includes/admin_header.php';

$pdo = Database::getConnection();

$periode = sanitize($_GET['periode'] ?? '30j');
$validPeriodes = ['today', '7j', '30j', 'mois', 'trimestre'];
if (!in_array($periode, $validPeriodes, true)) {
    $periode = '30j';
}

$now = new DateTimeImmutable('now');
$todayStart = $now->setTime(0, 0, 0);
$todayEnd = $now->setTime(23, 59, 59);

switch ($periode) {
    case 'today':
        $start = $todayStart;
        $end = $todayEnd;
        break;
    case '7j':
        $start = $todayStart->modify('-6 days');
        $end = $todayEnd;
        break;
    case 'mois':
        $start = $now->modify('first day of this month')->setTime(0, 0, 0);
        $end = $todayEnd;
        break;
    case 'trimestre':
        $month = (int) $now->format('n');
        $quarterStartMonth = (int) (floor(($month - 1) / 3) * 3) + 1;
        $start = (new DateTimeImmutable($now->format('Y') . '-' . str_pad((string) $quarterStartMonth, 2, '0', STR_PAD_LEFT) . '-01'))->setTime(0, 0, 0);
        $end = $todayEnd;
        break;
    case '30j':
    default:
        $start = $todayStart->modify('-29 days');
        $end = $todayEnd;
        break;
}

$durationDays = max(1, (int) $start->diff($end)->format('%a') + 1);
$previousStart = $start->modify('-' . $durationDays . ' days');
$previousEnd = $start->modify('-1 second');

$paramsCurrent = ['start' => $start->format('Y-m-d H:i:s'), 'end' => $end->format('Y-m-d H:i:s')];
$paramsPrevious = ['start' => $previousStart->format('Y-m-d H:i:s'), 'end' => $previousEnd->format('Y-m-d H:i:s')];
$zoneClause = ' AND latitude IS NOT NULL AND longitude IS NOT NULL AND
    (6371 * acos(cos(radians(:city_lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(:city_lng))
    + sin(radians(:city_lat)) * sin(radians(latitude)))) <= :city_radius';
$zoneParams = [
    'city_lat' => (float) siteConfig('city_lat', 44.8378),
    'city_lng' => (float) siteConfig('city_lng', -0.5792),
    'city_radius' => (float) siteConfig('radius', 30),
];

$fetchCount = static function (PDO $pdo, string $sql, array $params): int {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) ($stmt->fetch()['total'] ?? 0);
};

$totalLeads = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM estimations WHERE created_at BETWEEN :start AND :end' . $zoneClause, array_merge($paramsCurrent, $zoneParams));
$totalLeadsPrev = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM estimations WHERE created_at BETWEEN :start AND :end' . $zoneClause, array_merge($paramsPrevious, $zoneParams));

$simpleLeads = $fetchCount($pdo, "SELECT COUNT(*) AS total FROM estimations WHERE lead_type = 'estimation_gratuite' AND created_at BETWEEN :start AND :end" . $zoneClause, array_merge($paramsCurrent, $zoneParams));
$simpleLeadsPrev = $fetchCount($pdo, "SELECT COUNT(*) AS total FROM estimations WHERE lead_type = 'estimation_gratuite' AND created_at BETWEEN :start AND :end" . $zoneClause, array_merge($paramsPrevious, $zoneParams));

$detailLeads = $fetchCount($pdo, "SELECT COUNT(*) AS total FROM estimations WHERE lead_type = 'estimation_detaillee' AND created_at BETWEEN :start AND :end" . $zoneClause, array_merge($paramsCurrent, $zoneParams));
$detailLeadsPrev = $fetchCount($pdo, "SELECT COUNT(*) AS total FROM estimations WHERE lead_type = 'estimation_detaillee' AND created_at BETWEEN :start AND :end" . $zoneClause, array_merge($paramsPrevious, $zoneParams));

$rdvCount = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM rdv WHERE created_at BETWEEN :start AND :end', $paramsCurrent);
$rdvPrev = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM rdv WHERE created_at BETWEEN :start AND :end', $paramsPrevious);

$hotLeads = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM estimations WHERE lead_score > 70 AND created_at BETWEEN :start AND :end' . $zoneClause, array_merge($paramsCurrent, $zoneParams));
$hotLeadsPrev = $fetchCount($pdo, 'SELECT COUNT(*) AS total FROM estimations WHERE lead_score > 70 AND created_at BETWEEN :start AND :end' . $zoneClause, array_merge($paramsPrevious, $zoneParams));

$conversion = $totalLeads > 0 ? round(($rdvCount / $totalLeads) * 100, 1) : 0;

$kpi = [
    [
        'label' => 'Total leads', 'value' => $totalLeads, 'previous' => $totalLeadsPrev,
        'icon' => 'users', 'iconBg' => 'bg-blue-50', 'iconColor' => 'text-blue-600',
    ],
    [
        'label' => 'Estimations simples', 'value' => $simpleLeads, 'previous' => $simpleLeadsPrev,
        'icon' => 'calculator', 'iconBg' => 'bg-indigo-50', 'iconColor' => 'text-indigo-600',
    ],
    [
        'label' => 'Estimations détaillées', 'value' => $detailLeads, 'previous' => $detailLeadsPrev,
        'icon' => 'clipboard-list', 'iconBg' => 'bg-purple-50', 'iconColor' => 'text-purple-600',
    ],
    [
        'label' => 'RDV pris', 'value' => $rdvCount, 'previous' => $rdvPrev,
        'icon' => 'calendar', 'iconBg' => 'bg-green-50', 'iconColor' => 'text-green-600', 'sub' => 'Taux conversion : ' . $conversion . '%',
    ],
    [
        'label' => 'Leads chauds', 'value' => $hotLeads, 'previous' => $hotLeadsPrev,
        'icon' => 'flame', 'iconBg' => 'bg-red-50', 'iconColor' => 'text-red-600',
    ],
];

$barStmt = $pdo->prepare('SELECT DATE(created_at) AS day, COUNT(*) AS total FROM estimations WHERE created_at >= :start GROUP BY DATE(created_at) ORDER BY day ASC');
$barStmt->execute(['start' => $todayStart->modify('-29 days')->format('Y-m-d H:i:s')]);
$barRows = $barStmt->fetchAll();
$bars = [];
$maxBar = 1;
for ($i = 29; $i >= 0; $i--) {
    $day = $todayStart->modify('-' . $i . ' days')->format('Y-m-d');
    $bars[$day] = 0;
}
foreach ($barRows as $row) {
    $day = (string) $row['day'];
    if (isset($bars[$day])) {
        $bars[$day] = (int) $row['total'];
        $maxBar = max($maxBar, (int) $row['total']);
    }
}

$latestStmt = $pdo->prepare('SELECT id, nom, ville, lead_type, prix_estime, lead_statut, lead_score, created_at
    FROM estimations
    WHERE created_at BETWEEN :start AND :end
    ORDER BY created_at DESC
    LIMIT 8');
$latestStmt->execute($paramsCurrent);
$latestLeads = $latestStmt->fetchAll();

$pipelineStmt = $pdo->prepare('SELECT lead_statut, COUNT(*) AS total FROM estimations WHERE created_at BETWEEN :start AND :end GROUP BY lead_statut');
$pipelineStmt->execute($paramsCurrent);
$pipelineRows = $pipelineStmt->fetchAll();
$pipelineMap = ['nouveau' => 0, 'contacte' => 0, 'qualifie' => 0, 'en_negociation' => 0, 'converti' => 0, 'perdu' => 0];
foreach ($pipelineRows as $row) {
    $pipelineMap[(string) $row['lead_statut']] = (int) $row['total'];
}
$pipelineTotal = max(1, array_sum($pipelineMap));

$topVillesStmt = $pdo->prepare('SELECT ville, COUNT(*) AS total FROM estimations WHERE created_at BETWEEN :start AND :end AND ville IS NOT NULL AND ville <> "" GROUP BY ville ORDER BY total DESC LIMIT 8');
$topVillesStmt->execute($paramsCurrent);
$topVilles = $topVillesStmt->fetchAll();
$maxVille = 1;
foreach ($topVilles as $row) {
    $maxVille = max($maxVille, (int) $row['total']);
}

$sourcesStmt = $pdo->prepare('SELECT COALESCE(NULLIF(LOWER(utm_source), ""), "autre") AS source, COUNT(*) AS total
    FROM estimations
    WHERE created_at BETWEEN :start AND :end
    GROUP BY source');
$sourcesStmt->execute($paramsCurrent);
$sourceRows = $sourcesStmt->fetchAll();
$sourceMap = ['google' => 0, 'facebook' => 0, 'direct' => 0, 'autre' => 0];
foreach ($sourceRows as $row) {
    $source = (string) $row['source'];
    if (!isset($sourceMap[$source])) {
        $source = 'autre';
    }
    $sourceMap[$source] += (int) $row['total'];
}
$sourceTotal = max(1, array_sum($sourceMap));

$periodeOptions = [
    'today' => "Aujourd'hui",
    '7j' => '7 derniers jours',
    '30j' => '30 derniers jours',
    'mois' => 'Ce mois',
    'trimestre' => 'Ce trimestre',
];
?>
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900">Tableau de bord - <?php echo sanitize((string) siteConfig('city', 'Zone')); ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?php echo date('l d F Y'); ?></p>
    </div>
    <div class="flex items-center gap-3">
        <form method="GET">
            <select name="periode" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700">
                <?php foreach ($periodeOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo $periode === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="export.php?format=csv&periode=<?php echo urlencode($periode); ?>" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            <i data-lucide="download" class="h-4 w-4"></i>
            Exporter CSV
        </a>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 xl:grid-cols-5">
    <?php foreach ($kpi as $card):
        $change = getPercentChange((float) $card['value'], (float) $card['previous']);
        $positive = $change >= 0;
    ?>
        <article class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo $card['iconBg']; ?>">
                <i data-lucide="<?php echo $card['icon']; ?>" class="h-5 w-5 <?php echo $card['iconColor']; ?>"></i>
            </div>
            <p class="mt-4 text-3xl font-bold text-gray-900"><?php echo number_format((int) $card['value'], 0, ',', ' '); ?></p>
            <p class="text-sm text-gray-500"><?php echo $card['label']; ?></p>
            <p class="mt-2 inline-flex items-center gap-1 text-xs font-semibold <?php echo $positive ? 'text-green-600' : 'text-red-600'; ?>">
                <i data-lucide="<?php echo $positive ? 'arrow-up' : 'arrow-down'; ?>" class="h-3.5 w-3.5"></i>
                <?php echo ($positive ? '+' : '') . number_format($change, 1, ',', ''); ?>%
            </p>
            <?php if (!empty($card['sub'])): ?>
                <p class="mt-1 text-xs text-gray-500"><?php echo $card['sub']; ?></p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="text-lg font-semibold text-gray-900">Évolution des leads</h3>
            <div class="mt-6 flex h-[200px] items-end gap-1">
                <?php $index = 0; foreach ($bars as $day => $count): $height = max(4, (int) round(($count / $maxBar) * 100)); ?>
                    <div class="group flex-1" title="<?php echo date('d M', strtotime($day)); ?> : <?php echo $count; ?> leads">
                        <div class="w-full rounded-t-sm bg-primary/80 transition hover:bg-primary" style="height: <?php echo $height; ?>%"></div>
                        <?php if ($index % 5 === 0): ?>
                            <p class="mt-1 text-[10px] text-gray-400"><?php echo date('d/m', strtotime($day)); ?></p>
                        <?php endif; ?>
                    </div>
                <?php $index++; endforeach; ?>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-gray-100 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Derniers leads</h3>
                <a href="estimations.php" class="text-sm font-semibold text-primary">Voir tout →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Score</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Prix</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($latestLeads as $row):
                        $score = (int) ($row['lead_score'] ?? 0);
                        $scoreColor = getLeadColor($score);
                        $type = (string) ($row['lead_type'] ?? 'estimation_gratuite');
                        $typeClass = match ($type) {
                            'estimation_detaillee' => 'bg-purple-100 text-purple-700',
                            'rdv' => 'bg-green-100 text-green-700',
                            default => 'bg-blue-100 text-blue-700',
                        };
                    ?>
                        <tr onclick="window.location.href='lead.php?id=<?php echo (int) $row['id']; ?>'" class="cursor-pointer border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-<?php echo $scoreColor; ?>-100 text-xs font-bold text-<?php echo $scoreColor; ?>-700"><?php echo $score; ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (!empty($row['nom'])): ?>
                                    <p class="font-medium text-gray-900"><?php echo sanitize((string) $row['nom']); ?></p>
                                <?php else: ?>
                                    <p class="italic text-gray-400">Anonyme</p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500"><?php echo sanitize((string) ($row['ville'] ?? '-')); ?></p>
                            </td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $typeClass; ?>"><?php echo sanitize($type); ?></span></td>
                            <td class="px-4 py-3 font-semibold text-gray-900"><?php echo formatPrice((int) ($row['prix_estime'] ?? 0)); ?></td>
                            <td class="px-4 py-3"><?php echo getStatutBadge((string) ($row['lead_statut'] ?? 'nouveau')); ?></td>
                            <td class="px-4 py-3 text-gray-500"><?php echo formatDateRelative((string) ($row['created_at'] ?? '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="text-lg font-semibold">Pipeline</h3>
            <div class="mt-4 space-y-3">
                <?php
                $labels = ['nouveau' => 'Nouveau', 'contacte' => 'Contacté', 'qualifie' => 'Qualifié', 'en_negociation' => 'En négociation', 'converti' => 'Converti', 'perdu' => 'Perdu'];
                $colors = ['nouveau' => 'bg-blue-500', 'contacte' => 'bg-yellow-500', 'qualifie' => 'bg-orange-500', 'en_negociation' => 'bg-purple-500', 'converti' => 'bg-green-500', 'perdu' => 'bg-red-500'];
                foreach ($labels as $key => $label):
                    $count = $pipelineMap[$key] ?? 0;
                    $width = ($count / $pipelineTotal) * 100;
                ?>
                    <div>
                        <div class="mb-1 flex justify-between text-sm"><span><?php echo $label; ?></span><span><?php echo $count; ?></span></div>
                        <div class="h-3 rounded-full bg-gray-100"><div class="h-full rounded-full <?php echo $colors[$key]; ?>" style="width: <?php echo round($width, 1); ?>%"></div></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="text-lg font-semibold">Top villes</h3>
            <div class="mt-4 space-y-3">
                <?php foreach ($topVilles as $ville): $width = ((int) $ville['total'] / $maxVille) * 100; ?>
                    <div>
                        <div class="mb-1 flex justify-between text-sm"><span><?php echo sanitize((string) $ville['ville']); ?></span><span><?php echo (int) $ville['total']; ?></span></div>
                        <div class="h-2 rounded-full bg-gray-100"><div class="h-full rounded-full bg-primary" style="width: <?php echo round($width, 1); ?>%"></div></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="text-lg font-semibold">Sources</h3>
            <div class="mt-4 space-y-3">
                <?php
                $sourceLabels = ['google' => 'Google', 'facebook' => 'Facebook', 'direct' => 'Direct', 'autre' => 'Autre'];
                $sourceColors = ['google' => 'bg-blue-500', 'facebook' => 'bg-indigo-500', 'direct' => 'bg-gray-500', 'autre' => 'bg-slate-400'];
                foreach ($sourceLabels as $key => $label):
                    $count = $sourceMap[$key] ?? 0;
                    $width = ($count / $sourceTotal) * 100;
                ?>
                    <div>
                        <div class="mb-1 flex justify-between text-sm"><span><?php echo $label; ?></span><span><?php echo $count; ?></span></div>
                        <div class="h-3 rounded-full bg-gray-100"><div class="h-full rounded-full <?php echo $sourceColors[$key]; ?>" style="width: <?php echo round($width, 1); ?>%"></div></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
