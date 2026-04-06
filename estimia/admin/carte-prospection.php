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

$stmtLeads = $pdo->query(
    'SELECT e.*, ld.projet, ld.delai_vente,
            (6371 * acos(cos(radians(' . (float) siteConfig('city_lat', 44.8378) . ')) * cos(radians(e.latitude)) * cos(radians(e.longitude) - radians(' . (float) siteConfig('city_lng', -0.5792) . '))
            + sin(radians(' . (float) siteConfig('city_lat', 44.8378) . ')) * sin(radians(e.latitude)))) AS distance_to_center
     FROM estimations e
     LEFT JOIN leads_detailles ld ON e.id = ld.estimation_id
     WHERE e.latitude IS NOT NULL AND e.longitude IS NOT NULL
     ORDER BY e.created_at DESC'
);
$leads = $stmtLeads->fetchAll();

$stmtStats = $pdo->query(
    'SELECT ville, code_postal, COUNT(*) as nb_leads,
            AVG(prix_estime) as prix_moyen, AVG(lead_score) as score_moyen
     FROM estimations
     WHERE ville IS NOT NULL
     GROUP BY ville, code_postal
     ORDER BY nb_leads DESC'
);
$zoneStats = $stmtStats->fetchAll();

$availableCities = [];
foreach ($zoneStats as $zoneStat) {
    $city = trim((string) ($zoneStat['ville'] ?? ''));
    if ($city !== '') {
        $availableCities[$city] = true;
    }
}
$availableCities = array_keys($availableCities);
sort($availableCities, SORT_NATURAL | SORT_FLAG_CASE);

$adminPageTitle = 'Carte de prospection';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="sticky top-0 z-20 -mx-8 -mt-8 mb-6 border-b bg-white px-8 py-4 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-lg font-bold text-gray-900">Carte de prospection</h1>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                <?php echo count($leads); ?> estimations géolocalisées
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select id="filterCity" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Toutes les villes</option>
                <?php foreach ($availableCities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterLeadType" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Tous types</option>
                <option value="estimation_gratuite">Estimation gratuite</option>
                <option value="estimation_detaillee">Détaillée</option>
                <option value="rdv">RDV</option>
            </select>

            <select id="filterScore" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Tous scores</option>
                <option value="cold">Froids 0-25</option>
                <option value="warm">Tièdes 26-50</option>
                <option value="hot">Chauds 51-75</option>
                <option value="very_hot">Très chauds 76+</option>
            </select>

            <select id="filterPeriod" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Tout</option>
                <option value="7">7j</option>
                <option value="30">30j</option>
                <option value="90">90j</option>
            </select>

            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <span>Heatmap</span>
                <input id="toggleHeatmap" type="checkbox" class="h-4 w-4" />
            </label>

            <label class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <span>Clusters</span>
                <input id="toggleClusters" type="checkbox" class="h-4 w-4" checked />
            </label>
        </div>
    </div>
</div>

<div class="flex" style="height: calc(100vh - 180px);">
    <div class="flex-1 overflow-hidden rounded-l-xl border border-r-0 bg-white">
        <div id="prospectionMap" style="width: 100%; height: 100%;"></div>
    </div>

    <aside class="w-80 overflow-y-auto rounded-r-xl border border-l-0 bg-white">
        <section class="border-b p-4">
            <h2 class="text-sm font-bold text-gray-900">Zone visible</h2>
            <p id="zoneLeadCount" class="mt-2 text-2xl font-extrabold text-blue-700">0 lead</p>
            <p id="zoneAvgPrice" class="mt-1 text-sm text-gray-600">Prix moyen : -</p>
            <p id="zoneAvgScore" class="text-sm text-gray-600">Score moyen : -</p>
            <div id="zoneTypeBars" class="mt-3 space-y-2 text-xs"></div>
        </section>

        <section class="border-b p-4">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Leads visibles</h2>
                <span id="visibleCount" class="text-xs text-gray-500">0</span>
            </div>
            <div id="visibleLeadsList" class="space-y-0"></div>
            <p id="zoomHint" class="mt-2 hidden text-xs text-amber-600">Zoomez pour voir plus.</p>
        </section>

        <section class="p-4">
            <h2 class="mb-3 text-sm font-bold text-gray-900">Zones de forte demande</h2>
            <div id="hotZonesList" class="space-y-2"></div>
        </section>
    </aside>
</div>

<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
<script>
    const mapData = <?php echo json_encode($leads, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const cityStats = <?php echo json_encode($zoneStats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    let map;
    let markers = [];
    let markerCluster = null;
    let heatmap = null;
    const infoWindow = new google.maps.InfoWindow();

    const cityFilterEl = document.getElementById('filterCity');
    const typeFilterEl = document.getElementById('filterLeadType');
    const scoreFilterEl = document.getElementById('filterScore');
    const periodFilterEl = document.getElementById('filterPeriod');
    const heatmapToggleEl = document.getElementById('toggleHeatmap');
    const clustersToggleEl = document.getElementById('toggleClusters');

    function formatPriceJS(value) {
        const parsed = Number(value || 0);
        return new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR', maximumFractionDigits: 0}).format(parsed);
    }

    function getMarkerColor(score, isOutOfZone = false) {
        if (isOutOfZone) return '#9ca3af';
        const numericScore = Number(score || 0);
        if (numericScore >= 76) return '#16a34a';
        if (numericScore >= 51) return '#ca8a04';
        if (numericScore >= 26) return '#ea580c';
        return '#64748b';
    }

    function getMarkerSize(score) {
        const numericScore = Number(score || 0);
        return Math.max(7, Math.min(15, 7 + (numericScore / 100) * 8));
    }

    function getScoreBand(score) {
        const numericScore = Number(score || 0);
        if (numericScore <= 25) return 'cold';
        if (numericScore <= 50) return 'warm';
        if (numericScore <= 75) return 'hot';
        return 'very_hot';
    }

    function clearMarkers() {
        markers.forEach((marker) => marker.setMap(null));
        markers = [];

        if (markerCluster) {
            markerCluster.clearMarkers();
            markerCluster = null;
        }
    }

    function buildInfoContent(lead) {
        return `
        <div style="font-family:Inter,sans-serif;padding:12px;min-width:280px;max-width:320px">
          <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
            <strong style="font-size:15px">${lead.nom || 'Anonyme'}</strong>
            <span style="background:${getMarkerColor(lead.lead_score)};color:white;
              padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">
              ${lead.lead_score || 0}/100
            </span>
          </div>
          <div style="color:#64748b;font-size:13px;margin-bottom:8px">
            📍 ${lead.adresse_complete || lead.adresse || ''}<br>
            🏠 ${lead.type_bien || '-'} • ${lead.surface || 0} m²
          </div>
          <div style="font-size:20px;font-weight:800;color:#1a56db;margin:8px 0">
            ${formatPriceJS(lead.prix_estime)}
          </div>
          <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap">
            <span style="background:#e8effc;color:#1a56db;padding:2px 8px;
              border-radius:6px;font-size:11px">${lead.lead_type || '-'}</span>
            <span style="background:#f1f5f9;color:#475569;padding:2px 8px;
              border-radius:6px;font-size:11px">${lead.lead_statut || '-'}</span>
          </div>
          <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0">
            <a href="lead.php?id=${lead.id}"
              style="color:#1a56db;text-decoration:none;font-weight:600;font-size:13px">
              Voir la fiche complète →
            </a>
          </div>
        </div>`;
    }

    function loadMarkers(data) {
        clearMarkers();

        data.forEach((lead) => {
            const isOutOfZone = Number(lead.distance_to_center || 0) > Number(<?php echo (float) siteConfig('radius', 30); ?>);
            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(lead.latitude),
                    lng: parseFloat(lead.longitude),
                },
                map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: getMarkerColor(lead.lead_score, isOutOfZone),
                    fillOpacity: 0.9,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                    scale: getMarkerSize(lead.lead_score),
                },
                title: lead.adresse || 'Lead',
            });

            marker.leadData = lead;
            marker.isOutOfZone = isOutOfZone;
            marker.addListener('click', () => {
                infoWindow.setContent(buildInfoContent(lead));
                infoWindow.open(map, marker);
            });

            markers.push(marker);
        });

        if (clustersToggleEl.checked) {
            markerCluster = new markerClusterer.MarkerClusterer({
                map,
                markers,
                renderer: {
                    render: ({count, position}) => new google.maps.Marker({
                        position,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            fillColor: '#1a56db',
                            fillOpacity: 0.8,
                            strokeColor: '#ffffff',
                            strokeWeight: 3,
                            scale: Math.min(20, 10 + count),
                        },
                        label: {
                            text: String(count),
                            color: 'white',
                            fontWeight: 'bold',
                            fontSize: '12px',
                        },
                        zIndex: count,
                    }),
                },
            });
        }

        updateZonePanel();
    }

    function initHeatmap() {
        const heatmapData = mapData.map((lead) => ({
            location: new google.maps.LatLng(parseFloat(lead.latitude), parseFloat(lead.longitude)),
            weight: Number(lead.lead_score || 0) / 100,
        }));

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: heatmapData,
            map: null,
            radius: 40,
            opacity: 0.6,
            gradient: [
                'rgba(0, 0, 0, 0)',
                'rgba(26, 86, 219, 0.3)',
                'rgba(99, 102, 241, 0.5)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(239, 68, 68, 0.9)'
            ],
        });
    }

    function toggleHeatmap() {
        if (!heatmap) {
            return;
        }
        heatmap.setMap(heatmap.getMap() ? null : map);
    }

    function applyFilters() {
        const city = cityFilterEl.value.trim().toLowerCase();
        const leadType = typeFilterEl.value;
        const scoreBand = scoreFilterEl.value;
        const periodDays = Number(periodFilterEl.value || 0);

        const now = new Date();

        const filtered = mapData.filter((lead) => {
            if (city && String(lead.ville || '').trim().toLowerCase() !== city) {
                return false;
            }

            if (leadType && lead.lead_type !== leadType) {
                return false;
            }

            if (scoreBand && getScoreBand(lead.lead_score) !== scoreBand) {
                return false;
            }

            if (periodDays > 0 && lead.created_at) {
                const createdAt = new Date(String(lead.created_at).replace(' ', 'T'));
                const diffMs = now.getTime() - createdAt.getTime();
                const diffDays = diffMs / (1000 * 60 * 60 * 24);
                if (diffDays > periodDays) {
                    return false;
                }
            }

            return true;
        });

        loadMarkers(filtered);

        if (heatmap) {
            const heatmapData = filtered.map((lead) => ({
                location: new google.maps.LatLng(parseFloat(lead.latitude), parseFloat(lead.longitude)),
                weight: Number(lead.lead_score || 0) / 100,
            }));
            heatmap.setData(heatmapData);
        }
    }

    function renderTypeBars(leadsInBounds) {
        const target = document.getElementById('zoneTypeBars');
        const total = leadsInBounds.length;
        const counts = {
            estimation_gratuite: 0,
            estimation_detaillee: 0,
            rdv: 0,
        };

        leadsInBounds.forEach((lead) => {
            const key = String(lead.lead_type || 'estimation_gratuite');
            if (Object.prototype.hasOwnProperty.call(counts, key)) {
                counts[key] += 1;
            }
        });

        const labels = {
            estimation_gratuite: 'Gratuite',
            estimation_detaillee: 'Détaillée',
            rdv: 'RDV',
        };

        target.innerHTML = Object.keys(counts).map((key) => {
            const count = counts[key];
            const percent = total > 0 ? Math.round((count / total) * 100) : 0;
            return `
                <div>
                    <div class="mb-1 flex justify-between"><span>${labels[key]}</span><span>${count}</span></div>
                    <div class="h-1.5 w-full rounded bg-gray-100">
                        <div class="h-1.5 rounded bg-blue-600" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateVisibleLeadsList(leadsInBounds) {
        const listEl = document.getElementById('visibleLeadsList');
        const countEl = document.getElementById('visibleCount');
        const zoomHint = document.getElementById('zoomHint');

        countEl.textContent = String(leadsInBounds.length);

        const sorted = [...leadsInBounds].sort((a, b) => Number(b.lead_score || 0) - Number(a.lead_score || 0));
        const capped = sorted.slice(0, 50);

        zoomHint.classList.toggle('hidden', leadsInBounds.length <= 50);

        listEl.innerHTML = capped.map((lead) => `
            <button type="button" data-lead-id="${lead.id}" class="w-full cursor-pointer border-b p-3 text-left transition hover:bg-gray-50">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <p class="truncate text-sm font-semibold text-gray-900">${lead.nom || `Anonyme #${lead.id}`}</p>
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold text-white" style="background:${getMarkerColor(lead.lead_score)}">
                        ${lead.lead_score || 0}
                    </span>
                </div>
                <p class="truncate text-xs text-gray-500">${lead.adresse || '-'}</p>
                <p class="mt-1 text-xs text-gray-700">${formatPriceJS(lead.prix_estime)} • ${lead.lead_type || '-'}</p>
            </button>
        `).join('');

        listEl.querySelectorAll('button[data-lead-id]').forEach((button) => {
            button.addEventListener('click', () => {
                const leadId = Number(button.getAttribute('data-lead-id'));
                const marker = markers.find((item) => Number(item.leadData.id) === leadId);
                if (marker) {
                    map.panTo(marker.getPosition());
                    map.setZoom(Math.max(map.getZoom(), 14));
                    infoWindow.setContent(buildInfoContent(marker.leadData));
                    infoWindow.open(map, marker);
                }
            });
        });
    }

    function updateZonePanel() {
        if (!map) {
            return;
        }

        const bounds = map.getBounds();
        if (!bounds) {
            return;
        }

        const leadsInBounds = markers
            .map((marker) => marker.leadData)
            .filter((lead) => bounds.contains(new google.maps.LatLng(parseFloat(lead.latitude), parseFloat(lead.longitude))));

        const leadCount = leadsInBounds.length;
        const avgPrice = leadCount > 0
            ? leadsInBounds.reduce((sum, lead) => sum + Number(lead.prix_estime || 0), 0) / leadCount
            : 0;
        const avgScore = leadCount > 0
            ? leadsInBounds.reduce((sum, lead) => sum + Number(lead.lead_score || 0), 0) / leadCount
            : 0;

        document.getElementById('zoneLeadCount').textContent = `${leadCount} lead${leadCount > 1 ? 's' : ''}`;
        document.getElementById('zoneAvgPrice').textContent = `Prix moyen : ${leadCount > 0 ? formatPriceJS(avgPrice) : '-'}`;
        document.getElementById('zoneAvgScore').textContent = `Score moyen : ${leadCount > 0 ? avgScore.toFixed(1) : '-'} / 100`;

        renderTypeBars(leadsInBounds);
        updateVisibleLeadsList(leadsInBounds);
    }

    function renderHotZones() {
        const hotZonesEl = document.getElementById('hotZonesList');
        const topZones = [...cityStats].slice(0, 5);

        hotZonesEl.innerHTML = topZones.map((zone) => `
            <button
                type="button"
                data-zip="${zone.code_postal || ''}"
                class="w-full rounded-lg border border-gray-200 p-3 text-left transition hover:border-blue-300 hover:bg-blue-50"
            >
                <p class="text-sm font-semibold text-gray-900">${zone.code_postal || '-'} • ${zone.ville || '-'}</p>
                <p class="mt-1 text-xs text-gray-600">${zone.nb_leads} leads • Score moyen ${Number(zone.score_moyen || 0).toFixed(1)}</p>
            </button>
        `).join('');

        hotZonesEl.querySelectorAll('button[data-zip]').forEach((button) => {
            button.addEventListener('click', () => {
                const zip = button.getAttribute('data-zip');
                if (!zip) {
                    return;
                }

                const zipLeads = mapData.filter((lead) => String(lead.code_postal || '') === zip);
                if (zipLeads.length === 0) {
                    return;
                }

                const bounds = new google.maps.LatLngBounds();
                zipLeads.forEach((lead) => {
                    bounds.extend(new google.maps.LatLng(parseFloat(lead.latitude), parseFloat(lead.longitude)));
                });
                map.fitBounds(bounds);
            });
        });
    }

    function debounce(fn, wait) {
        let timeout;
        return function debounced(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    function initProspectionMap() {
        map = new google.maps.Map(document.getElementById('prospectionMap'), {
            center: {lat: <?php echo (float) siteConfig('city_lat', 44.8378); ?>, lng: <?php echo (float) siteConfig('city_lng', -0.5792); ?>},
            zoom: <?php
                $r=(int) siteConfig('radius',30);
                echo $r<=10?12:($r<=20?11:($r<=30?10:9));
            ?>,
            styles: [
                {featureType: 'administrative', stylers: [{saturation: -100}]},
                {featureType: 'water', stylers: [{color: '#e0e8f0'}]},
                {featureType: 'poi', stylers: [{visibility: 'off'}]},
                {featureType: 'transit', stylers: [{visibility: 'off'}]},
            ],
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
        });

        loadMarkers(mapData);
        new google.maps.Circle({
            map,
            center: {lat: <?php echo (float) siteConfig('city_lat', 44.8378); ?>, lng: <?php echo (float) siteConfig('city_lng', -0.5792); ?>},
            radius: <?php echo (float) siteConfig('radius', 30) * 1000; ?>,
            fillColor: '#1a56db',
            fillOpacity: 0.12,
            strokeColor: '#1a56db',
            strokeOpacity: 0.7,
            strokeWeight: 2,
        });
        initHeatmap();
        renderHotZones();

        const handleBoundsChanged = debounce(updateZonePanel, 500);
        map.addListener('bounds_changed', handleBoundsChanged);

        cityFilterEl.addEventListener('change', applyFilters);
        typeFilterEl.addEventListener('change', applyFilters);
        scoreFilterEl.addEventListener('change', applyFilters);
        periodFilterEl.addEventListener('change', applyFilters);

        heatmapToggleEl.addEventListener('change', () => {
            toggleHeatmap();
        });

        clustersToggleEl.addEventListener('change', () => {
            const filtered = markers.map((marker) => marker.leadData);
            loadMarkers(filtered);
        });
    }

    window.initProspectionMap = initProspectionMap;
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode((string) siteConfig('maps_key', '')); ?>&libraries=places,visualization&callback=initProspectionMap" async defer></script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
