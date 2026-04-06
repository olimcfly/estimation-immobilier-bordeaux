<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';


$adminPageTitle = 'Gestion des leads';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = Database::getConnection();

$type = sanitize($_GET['type'] ?? 'tous');
$vue = sanitize($_GET['vue'] ?? 'tableau');
$ville = sanitize($_GET['ville'] ?? '');
$statut = sanitize($_GET['statut'] ?? '');
$scoreMin = (int) ($_GET['score_min'] ?? 0);
$dateFrom = sanitize($_GET['date_from'] ?? '');
$dateTo = sanitize($_GET['date_to'] ?? '');
$source = sanitize($_GET['source'] ?? '');
$agentId = (int) ($_GET['agent_id'] ?? 0);
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$typesTabs = [
    'tous' => 'Tous',
    'gratuite' => 'Estimation gratuite',
    'detaillee' => 'Estimation détaillée',
    'rdv' => 'RDV pris',
    'chaud' => 'Leads chauds 🔥',
];
if (!isset($typesTabs[$type])) {
    $type = 'tous';
}

$where = [];
$params = [];

switch ($type) {
    case 'gratuite':
        $where[] = "lead_type = 'estimation_gratuite'";
        break;
    case 'detaillee':
        $where[] = "lead_type = 'estimation_detaillee'";
        break;
    case 'rdv':
        $where[] = 'rdv_pris = 1';
        break;
    case 'chaud':
        $where[] = 'lead_score > 70';
        break;
}

if ($ville !== '') {
    $where[] = 'ville = :ville';
    $params['ville'] = $ville;
}
if ($statut !== '') {
    $where[] = 'lead_statut = :statut';
    $params['statut'] = $statut;
}
if ($scoreMin > 0) {
    $where[] = 'lead_score >= :score_min';
    $params['score_min'] = $scoreMin;
}
if ($dateFrom !== '') {
    $where[] = 'DATE(created_at) >= :date_from';
    $params['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'DATE(created_at) <= :date_to';
    $params['date_to'] = $dateTo;
}
if ($source !== '') {
    $where[] = 'LOWER(COALESCE(utm_source, "direct")) = :source';
    $params['source'] = strtolower($source);
}
if ($agentId > 0) {
    $where[] = 'agent_assigne = :agent_id';
    $params['agent_id'] = $agentId;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$distinctVillesStmt = $pdo->prepare('SELECT DISTINCT ville FROM estimations WHERE ville IS NOT NULL AND ville <> "" ORDER BY ville ASC');
$distinctVillesStmt->execute();
$distinctVilles = $distinctVillesStmt->fetchAll();

$agentsStmt = $pdo->prepare('SELECT id, nom, prenom FROM agents WHERE actif = 1 ORDER BY nom, prenom');
$agentsStmt->execute();
$agents = $agentsStmt->fetchAll();
$agentMap = [];
foreach ($agents as $agent) {
    $agentMap[(int) $agent['id']] = trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''));
}

$countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM estimations {$whereSql}");
foreach ($params as $key => $value) {
    $countStmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$countStmt->execute();
$totalLeads = (int) ($countStmt->fetch()['total'] ?? 0);
$totalPages = max(1, (int) ceil($totalLeads / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$listStmt = $pdo->prepare("SELECT e.id, e.nom, e.email, e.telephone, e.ville, e.code_postal, e.type_bien, e.surface,
    e.prix_estime, e.lead_type, e.lead_statut, e.lead_score, e.created_at, e.agent_assigne,
    e.latitude, e.longitude, e.adresse, e.adresse_complete
    FROM estimations e
    {$whereSql}
    ORDER BY e.created_at DESC
    LIMIT :limit OFFSET :offset");

foreach ($params as $key => $value) {
    $listStmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$leads = $listStmt->fetchAll();

$mapStmt = $pdo->prepare("SELECT id, nom, ville, adresse, adresse_complete, type_bien, surface, prix_estime, lead_score, lead_statut, latitude, longitude
    FROM estimations
    {$whereSql} AND latitude IS NOT NULL AND longitude IS NOT NULL");
if ($whereSql === '') {
    $mapStmt = $pdo->prepare('SELECT id, nom, ville, adresse, adresse_complete, type_bien, surface, prix_estime, lead_score, lead_statut, latitude, longitude
        FROM estimations
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL');
}
foreach ($params as $key => $value) {
    if (strpos($mapStmt->queryString, ':' . $key) !== false) {
        $mapStmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
}
$mapStmt->execute();
$mapLeads = $mapStmt->fetchAll();

$leadsMapData = array_map(static function (array $lead): array {
    return [
        'id' => (int) $lead['id'],
        'lat' => (float) $lead['latitude'],
        'lng' => (float) $lead['longitude'],
        'nom' => (string) ($lead['nom'] ?: 'Anonyme'),
        'adresse' => (string) ($lead['adresse_complete'] ?: $lead['adresse']),
        'type' => (string) $lead['type_bien'],
        'surface' => (int) $lead['surface'],
        'prix' => (int) $lead['prix_estime'],
        'score' => (int) $lead['lead_score'],
        'statut' => (string) $lead['lead_statut'],
    ];
}, $mapLeads);

$queryBase = $_GET;
unset($queryBase['page']);

$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

$startItem = $totalLeads > 0 ? (($page - 1) * $perPage) + 1 : 0;
$endItem = min($page * $perPage, $totalLeads);
?>
<div class="mb-6 flex items-center gap-3">
    <h1 class="text-3xl font-extrabold text-gray-900">Gestion des leads</h1>
    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600"><?php echo number_format($totalLeads, 0, ',', ' '); ?> leads</span>
</div>

<?php if ($flashMessage): ?>
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?php echo sanitize((string) $flashMessage); ?></div>
<?php endif; ?>

<div class="mb-6 flex flex-wrap gap-2">
    <?php foreach ($typesTabs as $tabKey => $label): ?>
        <?php $active = $type === $tabKey; $tabQuery = http_build_query(array_merge($_GET, ['type' => $tabKey, 'page' => 1])); ?>
        <a href="?<?php echo $tabQuery; ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            <?php echo $label; ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="mb-4">
    <button id="toggleFilters" type="button" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
        <i data-lucide="filter" class="h-4 w-4"></i>
        Filtres avancés
    </button>
    <div id="advancedFilters" class="mt-2 rounded-xl bg-gray-50 p-4 <?php echo (isset($_GET['ville']) || isset($_GET['statut']) || isset($_GET['score_min']) || isset($_GET['date_from']) || isset($_GET['date_to']) || isset($_GET['source']) || isset($_GET['agent_id'])) ? '' : 'hidden'; ?>">
        <form method="GET" class="grid gap-4 md:grid-cols-4">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="vue" value="<?php echo htmlspecialchars($vue, ENT_QUOTES, 'UTF-8'); ?>">

            <select name="ville" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm"><option value="">Toutes les villes</option><?php foreach ($distinctVilles as $v): $vv=(string)$v['ville']; ?><option value="<?php echo htmlspecialchars($vv, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $ville === $vv ? 'selected' : ''; ?>><?php echo htmlspecialchars($vv, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select>
            <select name="statut" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm"><option value="">Tous les statuts</option><?php foreach (['nouveau','contacte','qualifie','en_negociation','converti','perdu'] as $s): ?><option value="<?php echo $s; ?>" <?php echo $statut === $s ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_',' ',$s)); ?></option><?php endforeach; ?></select>
            <select name="score_min" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm"><?php foreach ([0,25,50,75] as $score): ?><option value="<?php echo $score; ?>" <?php echo $scoreMin === $score ? 'selected' : ''; ?>>Score min <?php echo $score; ?></option><?php endforeach; ?></select>
            <select name="source" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm"><option value="">Toutes les sources</option><?php foreach (['google'=>'Google','facebook'=>'Facebook','direct'=>'Direct'] as $sv=>$sl): ?><option value="<?php echo $sv; ?>" <?php echo strtolower($source) === $sv ? 'selected' : ''; ?>><?php echo $sl; ?></option><?php endforeach; ?></select>

            <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom, ENT_QUOTES, 'UTF-8'); ?>" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo, ENT_QUOTES, 'UTF-8'); ?>" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm">
            <select name="agent_id" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm"><option value="0">Tous les agents</option><?php foreach ($agents as $a): ?><option value="<?php echo (int)$a['id']; ?>" <?php echo $agentId === (int)$a['id'] ? 'selected' : ''; ?>><?php echo sanitize(trim(($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? ''))); ?></option><?php endforeach; ?></select>
            <div class="flex gap-2">
                <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Appliquer</button>
                <a href="estimations.php?type=<?php echo urlencode($type); ?>&vue=<?php echo urlencode($vue); ?>" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-4 flex justify-end gap-2">
    <a href="?<?php echo http_build_query(array_merge($_GET, ['vue' => 'tableau'])); ?>" class="rounded-lg border px-3 py-2 <?php echo $vue !== 'carte' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-200 text-gray-600'; ?>"><i data-lucide="list" class="h-4 w-4"></i></a>
    <a href="?<?php echo http_build_query(array_merge($_GET, ['vue' => 'carte'])); ?>" class="rounded-lg border px-3 py-2 <?php echo $vue === 'carte' ? 'border-primary bg-primary/10 text-primary' : 'border-gray-200 text-gray-600'; ?>"><i data-lucide="map" class="h-4 w-4"></i></a>
</div>

<?php if ($vue === 'carte'): ?>
    <div class="relative overflow-hidden rounded-xl border bg-white">
        <div id="leadsMap" style="height: 600px;"></div>
        <div class="absolute bottom-4 left-4 rounded-lg bg-white p-3 text-sm shadow">
            <div>🔵 Froid (0-25)</div>
            <div>🟠 Tiède (26-50)</div>
            <div>🟡 Chaud (51-75)</div>
            <div>🔴 Très chaud (76-100)</div>
        </div>
    </div>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode((string) siteConfig('maps_key', '')); ?>"></script>
    <script>
        const leadsMapData = <?php echo json_encode($leadsMapData, JSON_UNESCAPED_UNICODE); ?>;

        function scoreColor(score) {
            if (score <= 25) return '#2563eb';
            if (score <= 50) return '#f97316';
            if (score <= 75) return '#eab308';
            return '#dc2626';
        }

        const map = new google.maps.Map(document.getElementById('leadsMap'), {
            center: {lat: 46.6, lng: 2.3},
            zoom: 6,
        });

        const infoWindow = new google.maps.InfoWindow();

        const markers = leadsMapData.map((lead) => {
            const marker = new google.maps.Marker({
                position: {lat: lead.lat, lng: lead.lng},
                map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: scoreColor(lead.score),
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                },
            });

            marker.addListener('click', () => {
                infoWindow.setContent(`<div style="padding:8px;min-width:200px"><strong>${lead.nom}</strong><br><span style="color:gray">${lead.adresse}</span><br><span style="color:gray">${lead.type} • ${lead.surface}m²</span><br><strong style="color:#1a56db">${new Intl.NumberFormat('fr-FR').format(lead.prix)} €</strong><br><span>Score: ${lead.score}/100</span><br><a href="lead.php?id=${lead.id}">Voir le lead →</a></div>`);
                infoWindow.open(map, marker);
            });

            return marker;
        });

        if (window.markerClusterer && markers.length > 0) {
            new markerClusterer.MarkerClusterer({ map, markers });
        }
    </script>
<?php else: ?>
    <form id="bulkForm" method="POST" action="actions.php">
        <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars(http_build_query($_GET), ENT_QUOTES, 'UTF-8'); ?>">
        <div id="bulkBar" class="mb-3 hidden items-center justify-between rounded-xl border bg-blue-50 px-4 py-3 text-sm">
            <span id="bulkCount">0 sélectionnés</span>
            <div class="flex flex-wrap gap-2">
                <select name="agent_assigne" class="rounded border border-gray-200 px-2 py-1 text-xs">
                    <option value="">Assigner à...</option>
                    <?php foreach ($agents as $a): ?><option value="<?php echo (int)$a['id']; ?>"><?php echo sanitize(trim(($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? ''))); ?></option><?php endforeach; ?>
                </select>
                <button name="action" value="assigner" class="rounded bg-white px-3 py-1 text-xs font-semibold">Assigner</button>
                <select name="nouveau_statut" class="rounded border border-gray-200 px-2 py-1 text-xs">
                    <option value="">Changer statut...</option>
                    <?php foreach (['nouveau','contacte','qualifie','en_negociation','converti','perdu'] as $s): ?><option value="<?php echo $s; ?>"><?php echo ucfirst(str_replace('_',' ',$s)); ?></option><?php endforeach; ?>
                </select>
                <button name="action" value="statut" class="rounded bg-white px-3 py-1 text-xs font-semibold">Appliquer</button>
                <button name="action" value="exporter" class="rounded bg-white px-3 py-1 text-xs font-semibold">Exporter</button>
                <button name="action" value="supprimer" class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white" onclick="return confirm('Supprimer les leads sélectionnés ?')">Supprimer</button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-3 py-3"><input type="checkbox" id="checkAll"></th>
                    <th class="px-3 py-3">Score</th>
                    <th class="px-3 py-3">Contact</th>
                    <th class="px-3 py-3">Localisation</th>
                    <th class="px-3 py-3">Bien</th>
                    <th class="px-3 py-3">Prix estimé</th>
                    <th class="px-3 py-3">Type lead</th>
                    <th class="px-3 py-3">Statut</th>
                    <th class="px-3 py-3">Agent</th>
                    <th class="px-3 py-3">Date</th>
                    <th class="px-3 py-3">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($leads as $lead):
                    $id = (int) $lead['id'];
                    $score = (int) ($lead['lead_score'] ?? 0);
                    $scoreColor = getLeadColor($score);
                    $typeLead = (string) ($lead['lead_type'] ?? 'estimation_gratuite');
                    $typeClass = match ($typeLead) {
                        'estimation_detaillee' => 'bg-purple-100 text-purple-700',
                        'rdv' => 'bg-green-100 text-green-700',
                        default => 'bg-blue-100 text-blue-700',
                    };
                    $assignedAgent = $agentMap[(int) ($lead['agent_assigne'] ?? 0)] ?? 'Non assigné';
                ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-3 py-3"><input type="checkbox" class="lead-checkbox" name="ids[]" value="<?php echo $id; ?>"></td>
                        <td class="px-3 py-3"><span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-<?php echo $scoreColor; ?>-100 text-xs font-bold text-<?php echo $scoreColor; ?>-700"><?php echo $score; ?></span></td>
                        <td class="px-3 py-3">
                            <?php if (!empty($lead['nom'])): ?><div class="font-medium text-gray-900"><?php echo sanitize((string) $lead['nom']); ?></div><?php else: ?><div class="italic text-gray-400">Anonyme</div><?php endif; ?>
                            <div class="text-xs text-gray-500"><?php echo sanitize((string) ($lead['email'] ?? '')); ?></div>
                        </td>
                        <td class="px-3 py-3 text-gray-600"><?php echo sanitize((string) ($lead['ville'] ?? '-')); ?> <?php echo sanitize((string) ($lead['code_postal'] ?? '')); ?></td>
                        <td class="px-3 py-3 text-gray-600"><?php echo ucfirst((string) ($lead['type_bien'] ?? '')); ?> • <?php echo (int) ($lead['surface'] ?? 0); ?>m²</td>
                        <td class="px-3 py-3 font-semibold"><?php echo formatPrice((int) ($lead['prix_estime'] ?? 0)); ?></td>
                        <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $typeClass; ?>"><?php echo sanitize($typeLead); ?></span></td>
                        <td class="px-3 py-3">
                            <form method="POST" action="actions.php" class="flex items-center gap-2">
                                <input type="hidden" name="ids[]" value="<?php echo $id; ?>">
                                <input type="hidden" name="action" value="statut">
                                <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars(http_build_query($_GET), ENT_QUOTES, 'UTF-8'); ?>">
                                <select name="nouveau_statut" onchange="this.form.submit()" class="rounded border border-gray-200 px-2 py-1 text-xs">
                                    <?php foreach (['nouveau','contacte','qualifie','en_negociation','converti','perdu'] as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo ($lead['lead_statut'] ?? '') === $s ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $s)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td class="px-3 py-3 text-gray-600"><?php echo sanitize((string) $assignedAgent); ?></td>
                        <td class="px-3 py-3 text-gray-500"><?php echo formatDateRelative((string) ($lead['created_at'] ?? '')); ?></td>
                        <td class="px-3 py-3">
                            <details class="relative">
                                <summary class="cursor-pointer list-none rounded p-1 hover:bg-gray-100"><i data-lucide="ellipsis" class="h-4 w-4"></i></summary>
                                <div class="absolute right-0 z-10 mt-2 w-36 rounded-lg border bg-white p-2 text-xs shadow">
                                    <a class="block rounded px-2 py-1 hover:bg-gray-50" href="lead.php?id=<?php echo $id; ?>">Voir</a>
                                    <button type="submit" name="action" value="assigner" class="w-full rounded px-2 py-1 text-left hover:bg-gray-50">Assigner</button>
                                    <button type="submit" name="action" value="supprimer" class="w-full rounded px-2 py-1 text-left text-red-600 hover:bg-red-50" onclick="return confirm('Supprimer ce lead ?')">Supprimer</button>
                                </div>
                            </details>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-gray-600">
        <p>Affichage <?php echo $startItem; ?>-<?php echo $endItem; ?> sur <?php echo $totalLeads; ?></p>
        <div class="flex items-center gap-2">
            <?php $prev = max(1, $page - 1); $next = min($totalPages, $page + 1); ?>
            <a class="rounded border px-3 py-1 <?php echo $page <= 1 ? 'pointer-events-none opacity-40' : ''; ?>" href="?<?php echo http_build_query(array_merge($queryBase, ['page' => $prev])); ?>">← Précédent</a>
            <?php for ($p = 1; $p <= $totalPages; $p++): if ($p > 3 && $p < $totalPages - 2 && abs($p - $page) > 1) { continue; } ?>
                <a class="rounded px-3 py-1 <?php echo $p === $page ? 'bg-primary text-white' : 'border'; ?>" href="?<?php echo http_build_query(array_merge($queryBase, ['page' => $p])); ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <a class="rounded border px-3 py-1 <?php echo $page >= $totalPages ? 'pointer-events-none opacity-40' : ''; ?>" href="?<?php echo http_build_query(array_merge($queryBase, ['page' => $next])); ?>">Suivant →</a>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggleFilters');
        const filters = document.getElementById('advancedFilters');
        toggleBtn?.addEventListener('click', () => filters?.classList.toggle('hidden'));

        const checkAll = document.getElementById('checkAll');
        const checkboxes = Array.from(document.querySelectorAll('.lead-checkbox'));
        const bulkBar = document.getElementById('bulkBar');
        const bulkCount = document.getElementById('bulkCount');

        const refreshBulk = () => {
            const selected = checkboxes.filter((cb) => cb.checked).length;
            if (selected > 0) {
                bulkBar?.classList.remove('hidden');
                bulkBar?.classList.add('flex');
            } else {
                bulkBar?.classList.add('hidden');
                bulkBar?.classList.remove('flex');
            }
            if (bulkCount) bulkCount.textContent = `${selected} sélectionnés`;
        };

        checkAll?.addEventListener('change', () => {
            checkboxes.forEach((cb) => { cb.checked = checkAll.checked; });
            refreshBulk();
        });
        checkboxes.forEach((cb) => cb.addEventListener('change', refreshBulk));
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
