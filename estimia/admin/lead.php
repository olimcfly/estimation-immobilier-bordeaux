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

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('estimations.php');
}

$pdo = Database::getConnection();

$validInteractionTypes = [
    'creation',
    'vue',
    'appel_sortant',
    'appel_entrant',
    'email_envoye',
    'email_recu',
    'note',
    'rdv_fixe',
    'rdv_effectue',
    'relance',
    'changement_statut',
];
$validStatuts = ['nouveau', 'contacte', 'qualifie', 'en_negociation', 'converti', 'perdu'];
$csrfToken = generateCSRFToken();

if (isPost()) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        redirect('lead.php?id=' . $id);
    }
    $action = sanitize($_GET['action'] ?? ($_POST['action'] ?? ''));

    if ($action === 'add_note') {
        $typeInteraction = sanitize($_POST['type_interaction'] ?? 'note');
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($description !== '' && in_array($typeInteraction, $validInteractionTypes, true)) {
            $stmtAdd = $pdo->prepare(
                'INSERT INTO lead_interactions (estimation_id, type_interaction, description, agent_id)
                 VALUES (:id, :type, :description, :agent_id)'
            );
            $stmtAdd->execute([
                'id' => $id,
                'type' => $typeInteraction,
                'description' => $description,
                'agent_id' => null,
            ]);
        }

        redirect('lead.php?id=' . $id);
    }

    if ($action === 'status') {
        $newStatus = sanitize($_POST['lead_statut'] ?? 'nouveau');

        if (in_array($newStatus, $validStatuts, true)) {
            $stmt = $pdo->prepare('UPDATE estimations SET lead_statut = :status WHERE id = :id');
            $stmt->execute(['status' => $newStatus, 'id' => $id]);

            $stmtLog = $pdo->prepare(
                'INSERT INTO lead_interactions (estimation_id, type_interaction, description)
                 VALUES (:id, "changement_statut", :description)'
            );
            $stmtLog->execute(['id' => $id, 'description' => 'Statut modifié en ' . $newStatus]);
        }

        redirect('lead.php?id=' . $id);
    }

    if ($action === 'assign') {
        $agentAssigne = (int) ($_POST['agent_assigne'] ?? 0);
        $stmt = $pdo->prepare('UPDATE estimations SET agent_assigne = :agent WHERE id = :id');
        $stmt->execute([
            'agent' => $agentAssigne > 0 ? $agentAssigne : null,
            'id' => $id,
        ]);

        redirect('lead.php?id=' . $id);
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM estimations WHERE id = :id');
        $stmt->execute(['id' => $id]);

        redirect('estimations.php');
    }

    if ($action === 'rdv_done') {
        $stmt = $pdo->prepare('UPDATE rdv SET statut = "confirme" WHERE estimation_id = :id');
        $stmt->execute(['id' => $id]);

        $stmtLog = $pdo->prepare(
            'INSERT INTO lead_interactions (estimation_id, type_interaction, description)
             VALUES (:id, "rdv_effectue", :description)'
        );
        $stmtLog->execute(['id' => $id, 'description' => 'RDV marqué comme effectué']);

        redirect('lead.php?id=' . $id);
    }

    if ($action === 'create_rdv') {
        $nom = trim((string) ($_POST['nom'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $telephone = trim((string) ($_POST['telephone'] ?? ''));
        $dateSouhaitee = trim((string) ($_POST['date_souhaitee'] ?? ''));
        $creneau = sanitize($_POST['creneau'] ?? 'matin');
        $message = trim((string) ($_POST['message'] ?? 'RDV créé depuis la fiche lead'));

        $stmtRdv = $pdo->prepare(
            'INSERT INTO rdv (estimation_id, nom, email, telephone, date_souhaitee, creneau, message, statut)
             VALUES (:id, :nom, :email, :telephone, :date_souhaitee, :creneau, :message, "nouveau")'
        );
        $stmtRdv->execute([
            'id' => $id,
            'nom' => $nom !== '' ? $nom : 'Prospect',
            'email' => $email,
            'telephone' => $telephone,
            'date_souhaitee' => $dateSouhaitee !== '' ? $dateSouhaitee : null,
            'creneau' => in_array($creneau, ['matin', 'apres_midi', 'soir'], true) ? $creneau : 'matin',
            'message' => $message,
        ]);

        $stmtLead = $pdo->prepare('UPDATE estimations SET rdv_pris = 1 WHERE id = :id');
        $stmtLead->execute(['id' => $id]);

        $stmtLog = $pdo->prepare(
            'INSERT INTO lead_interactions (estimation_id, type_interaction, description)
             VALUES (:id, "rdv_fixe", :description)'
        );
        $stmtLog->execute(['id' => $id, 'description' => 'RDV créé depuis la fiche lead']);

        redirect('lead.php?id=' . $id);
    }
}

$stmtLead = $pdo->prepare(
    'SELECT
        e.*,
        ld.id AS lead_detail_id,
        ld.prix_estime_detaille,
        ld.prix_bas_detaille,
        ld.prix_haut_detaille,
        ld.nb_pieces,
        ld.nb_chambres,
        ld.etage,
        ld.annee_construction,
        ld.etat_general,
        ld.type_chauffage,
        ld.dpe,
        ld.balcon,
        ld.terrasse,
        ld.jardin,
        ld.parking,
        ld.garage,
        ld.piscine,
        ld.cave,
        ld.projet,
        ld.delai_vente,
        ld.agence_actuelle,
        a.id AS agent_id,
        a.nom AS agent_nom,
        a.prenom AS agent_prenom,
        a.email AS agent_email,
        a.telephone AS agent_telephone
     FROM estimations e
     LEFT JOIN leads_detailles ld ON ld.estimation_id = e.id
     LEFT JOIN agents a ON a.id = e.agent_assigne
     WHERE e.id = :id
     ORDER BY ld.created_at DESC
     LIMIT 1'
);
$stmtLead->execute(['id' => $id]);
$lead = $stmtLead->fetch();

if (!$lead) {
    redirect('estimations.php');
}

$stmtRdvs = $pdo->prepare('SELECT * FROM rdv WHERE estimation_id = :id ORDER BY created_at DESC');
$stmtRdvs->execute(['id' => $id]);
$rdvs = $stmtRdvs->fetchAll();

$stmtInteractions = $pdo->prepare(
    'SELECT li.*, a.nom AS agent_nom, a.prenom AS agent_prenom
     FROM lead_interactions li
     LEFT JOIN agents a ON a.id = li.agent_id
     WHERE li.estimation_id = :id
     ORDER BY li.created_at DESC'
);
$stmtInteractions->execute(['id' => $id]);
$interactions = $stmtInteractions->fetchAll();

$stmtAgents = $pdo->prepare('SELECT id, nom, prenom, email, telephone FROM agents WHERE actif = 1 ORDER BY nom, prenom');
$stmtAgents->execute();
$agents = $stmtAgents->fetchAll();

$adminPageTitle = 'Lead #' . $id;

$leadName = !empty($lead['nom']) ? (string) $lead['nom'] : 'Lead anonyme #' . $id;
$leadTypeLabel = (string) ($lead['lead_type'] ?? 'estimation_gratuite');
$leadTypeClass = match ($leadTypeLabel) {
    'estimation_detaillee' => 'bg-purple-100 text-purple-700',
    'rdv' => 'bg-green-100 text-green-700',
    default => 'bg-blue-100 text-blue-700',
};

$score = (int) ($lead['lead_score'] ?? 0);
$scoreRawColor = getLeadColor($score);
$scoreUi = match ($scoreRawColor) {
    'green' => ['stroke' => '#16a34a', 'pill' => 'bg-green-100 text-green-700', 'label' => 'très chaud'],
    'yellow' => ['stroke' => '#ca8a04', 'pill' => 'bg-yellow-100 text-yellow-700', 'label' => 'chaud'],
    'orange' => ['stroke' => '#ea580c', 'pill' => 'bg-orange-100 text-orange-700', 'label' => 'tiède'],
    default => ['stroke' => '#6b7280', 'pill' => 'bg-gray-100 text-gray-700', 'label' => 'froid'],
};

$leadStatut = (string) ($lead['lead_statut'] ?? 'nouveau');
$timelineColors = [
    'creation' => 'bg-blue-500',
    'appel_sortant' => 'bg-green-500',
    'email_envoye' => 'bg-indigo-500',
    'note' => 'bg-gray-500',
    'rdv_fixe' => 'bg-purple-500',
    'changement_statut' => 'bg-amber-500',
];

$lastActivity = !empty($interactions)
    ? (string) ($interactions[0]['created_at'] ?? '')
    : (string) ($lead['created_at'] ?? '');

$scoreBreakdown = [
    ['ok' => !empty($lead['telephone']), 'label' => 'Téléphone renseigné', 'pts' => 15],
    ['ok' => !empty($lead['email']), 'label' => 'Email renseigné', 'pts' => 10],
    ['ok' => (int) ($lead['rdv_pris'] ?? 0) === 1, 'label' => 'RDV pris', 'pts' => 20],
    ['ok' => strtolower((string) ($lead['utm_source'] ?? '')) === 'google', 'label' => 'Source Google', 'pts' => 5],
];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="rounded-xl border bg-white p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo sanitize($leadName); ?></h2>
                    <p class="mt-1 text-gray-500">
                        <?php echo sanitize((string) ($lead['email'] ?? '-')); ?>
                        <?php if (!empty($lead['telephone'])): ?>• <?php echo sanitize((string) $lead['telephone']); ?><?php endif; ?>
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $leadTypeClass; ?>"><?php echo sanitize($leadTypeLabel); ?></span>
                        <?php echo getStatutBadge($leadStatut); ?>
                        <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $scoreUi['pill']; ?>">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-current text-[10px] font-bold"><?php echo $score; ?></span>
                            Score <?php echo sanitize($scoreUi['label']); ?>
                        </span>
                    </div>
                </div>

                <details class="relative">
                    <summary class="cursor-pointer rounded-xl border px-3 py-2 text-sm font-semibold">Actions</summary>
                    <div class="absolute right-0 z-10 mt-2 w-72 space-y-3 rounded-xl border bg-white p-3 text-sm shadow-xl">
                        <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=status" class="space-y-2">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <label class="text-xs font-semibold text-gray-500">Changer statut</label>
                            <select name="lead_statut" onchange="this.form.submit()" class="w-full rounded border px-2 py-1">
                                <?php foreach ($validStatuts as $statut): ?>
                                    <option value="<?php echo $statut; ?>" <?php echo $leadStatut === $statut ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(str_replace('_', ' ', $statut)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=assign" class="space-y-2">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <label class="text-xs font-semibold text-gray-500">Assigner un agent</label>
                            <select name="agent_assigne" onchange="this.form.submit()" class="w-full rounded border px-2 py-1">
                                <option value="0">Non assigné</option>
                                <?php foreach ($agents as $agent):
                                    $agentName = trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''));
                                    ?>
                                    <option value="<?php echo (int) $agent['id']; ?>" <?php echo (int) ($lead['agent_assigne'] ?? 0) === (int) $agent['id'] ? 'selected' : ''; ?>>
                                        <?php echo sanitize($agentName); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <?php if (!empty($lead['email'])): ?>
                            <a class="block rounded px-2 py-1 hover:bg-gray-50" href="mailto:<?php echo sanitize((string) $lead['email']); ?>">Envoyer un email</a>
                        <?php endif; ?>
                        <?php if (!empty($lead['telephone'])): ?>
                            <a class="block rounded px-2 py-1 hover:bg-gray-50" href="tel:<?php echo sanitize((string) $lead['telephone']); ?>">Appeler</a>
                        <?php endif; ?>

                        <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=delete" onsubmit="return confirm('Supprimer ce lead ?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <button class="w-full rounded bg-red-50 px-2 py-1 text-left text-red-600 hover:bg-red-100">Supprimer</button>
                        </form>
                    </div>
                </details>
            </div>
        </section>

        <section class="rounded-xl bg-gradient-to-br from-primary to-indigo-700 p-6 text-white">
            <p class="text-sm text-white/80"><?php echo sanitize((string) ($lead['adresse_complete'] ?: $lead['adresse'])); ?></p>
            <p class="mt-1 text-sm text-white/80"><?php echo ucfirst((string) ($lead['type_bien'] ?? '')); ?> • <?php echo (int) ($lead['surface'] ?? 0); ?> m²</p>
            <p class="mt-4 text-4xl font-black"><?php echo formatPrice((int) ($lead['prix_estime'] ?? 0)); ?></p>
            <p class="mt-1 text-white/80"><?php echo formatPrice((int) ($lead['prix_bas'] ?? 0)); ?> - <?php echo formatPrice((int) ($lead['prix_haut'] ?? 0)); ?></p>
            <p class="mt-2 text-sm text-white/70">Prix au m² : <?php echo formatPrice((int) ($lead['prix_m2'] ?? 0)); ?></p>

            <?php if (!empty($lead['prix_estime_detaille'])):
                $prixSimple = (int) ($lead['prix_estime'] ?? 0);
                $prixDetaille = (int) ($lead['prix_estime_detaille'] ?? 0);
                $delta = $prixDetaille - $prixSimple;
                $deltaPercent = $prixSimple > 0 ? ($delta / $prixSimple) * 100 : 0;
                ?>
                <p class="mt-3 text-sm">
                    Simple: <?php echo formatPrice($prixSimple); ?> → Détaillée: <?php echo formatPrice($prixDetaille); ?>
                    <span class="font-semibold">(<?php echo $delta >= 0 ? '+' : ''; ?><?php echo number_format($deltaPercent, 1, ',', ''); ?>%)</span>
                </p>
            <?php endif; ?>
        </section>

        <?php if (!empty($lead['latitude']) && !empty($lead['longitude'])): ?>
            <section class="overflow-hidden rounded-xl border bg-white">
                <div id="leadMap" style="height:250px"></div>
                <div id="leadStreetView" class="border-t" style="height:140px"></div>
            </section>
            <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode((string) siteConfig('maps_key', '')); ?>"></script>
            <script>
                (function () {
                    const center = {lat: <?php echo (float) $lead['latitude']; ?>, lng: <?php echo (float) $lead['longitude']; ?>};
                    const mapEl = document.getElementById('leadMap');
                    if (!mapEl || typeof google === 'undefined' || !google.maps) {
                        return;
                    }

                    const map = new google.maps.Map(mapEl, {center, zoom: 15});
                    new google.maps.Marker({position: center, map});

                    const svEl = document.getElementById('leadStreetView');
                    if (svEl) {
                        const streetViewService = new google.maps.StreetViewService();
                        streetViewService.getPanorama({location: center, radius: 80}, function (data, status) {
                            if (status === google.maps.StreetViewStatus.OK) {
                                new google.maps.StreetViewPanorama(svEl, {
                                    position: center,
                                    pov: {heading: 0, pitch: 0},
                                    zoom: 1,
                                });
                            } else {
                                svEl.innerHTML = '<div class="p-3 text-xs text-gray-500">Street View indisponible pour ce point.</div>';
                            }
                        });
                    }
                })();
            </script>
        <?php endif; ?>

        <?php if (!empty($lead['lead_detail_id'])): ?>
            <section class="rounded-xl border bg-white p-6">
                <h3 class="mb-4 text-lg font-semibold">Caractéristiques détaillées</h3>
                <div class="grid gap-3 text-sm md:grid-cols-3">
                    <?php
                    $items = [
                        'Pièces' => $lead['nb_pieces'] ?? '-',
                        'Chambres' => $lead['nb_chambres'] ?? '-',
                        'Étage' => $lead['etage'] ?? '-',
                        'Construction' => $lead['annee_construction'] ?? '-',
                        'État' => $lead['etat_general'] ?? '-',
                        'Chauffage' => $lead['type_chauffage'] ?? '-',
                        'DPE' => $lead['dpe'] ?? '-',
                        'Balcon' => !empty($lead['balcon']) ? '✓' : '✗',
                        'Terrasse' => !empty($lead['terrasse']) ? '✓' : '✗',
                        'Jardin' => !empty($lead['jardin']) ? '✓' : '✗',
                        'Parking' => !empty($lead['parking']) ? '✓' : '✗',
                        'Garage' => !empty($lead['garage']) ? '✓' : '✗',
                        'Piscine' => !empty($lead['piscine']) ? '✓' : '✗',
                        'Cave' => !empty($lead['cave']) ? '✓' : '✗',
                    ];
                    foreach ($items as $label => $value):
                        ?>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500"><?php echo $label; ?></p>
                            <p class="font-semibold text-gray-900"><?php echo sanitize((string) $value); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    <p>
                        Projet : <strong><?php echo sanitize((string) ($lead['projet'] ?? '-')); ?></strong>
                        • Délai : <strong><?php echo sanitize((string) ($lead['delai_vente'] ?? '-')); ?></strong>
                    </p>
                    <?php if (!empty($lead['agence_actuelle'])): ?>
                        <p class="mt-1">Agence actuelle : <strong><?php echo sanitize((string) $lead['agence_actuelle']); ?></strong></p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="rounded-xl border bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Historique</h3>
                <span class="text-xs text-gray-400"><?php echo count($interactions); ?> interaction(s)</span>
            </div>

            <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=add_note" class="mb-6 rounded-xl bg-gray-50 p-4">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="grid gap-3 md:grid-cols-4">
                    <select name="type_interaction" class="rounded border px-2 py-2 text-sm">
                        <?php foreach (['note', 'appel_sortant', 'email_envoye', 'relance', 'changement_statut'] as $interactionType): ?>
                            <option value="<?php echo $interactionType; ?>"><?php echo ucfirst(str_replace('_', ' ', $interactionType)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea name="description" required class="rounded border px-3 py-2 text-sm md:col-span-2" placeholder="Ajouter une note..."></textarea>
                    <button class="rounded bg-primary px-4 py-2 text-sm font-semibold text-white">Ajouter</button>
                </div>
            </form>

            <div class="relative pl-6">
                <div class="absolute left-2 top-0 h-full w-px bg-gray-200"></div>
                <?php foreach ($interactions as $interaction):
                    $type = (string) ($interaction['type_interaction'] ?? 'note');
                    $dotColor = $timelineColors[$type] ?? 'bg-gray-400';
                    $agentDisplay = trim((string) ($interaction['agent_prenom'] ?? '') . ' ' . (string) ($interaction['agent_nom'] ?? ''));
                    ?>
                    <div class="relative mb-5">
                        <span class="absolute -left-[23px] top-1 h-3 w-3 rounded-full <?php echo $dotColor; ?>"></span>
                        <p class="text-xs text-gray-400"><?php echo formatDateRelative((string) ($interaction['created_at'] ?? '')); ?></p>
                        <span class="mt-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600"><?php echo sanitize($type); ?></span>
                        <p class="mt-1 text-sm text-gray-700"><?php echo sanitize((string) ($interaction['description'] ?? '')); ?></p>
                        <?php if ($agentDisplay !== ''): ?>
                            <p class="text-xs text-gray-500">Agent : <?php echo sanitize($agentDisplay); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="space-y-6 lg:col-span-1">
        <section class="rounded-xl border bg-white p-6 text-center">
            <h3 class="text-lg font-semibold">Score lead</h3>
            <div class="mx-auto mt-4 h-36 w-36">
                <svg viewBox="0 0 120 120" class="h-36 w-36 -rotate-90">
                    <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="10" fill="none"></circle>
                    <circle
                        cx="60"
                        cy="60"
                        r="50"
                        stroke="<?php echo $scoreUi['stroke']; ?>"
                        stroke-width="10"
                        fill="none"
                        stroke-linecap="round"
                        stroke-dasharray="314"
                        stroke-dashoffset="<?php echo 314 - (min(100, max(0, $score)) / 100) * 314; ?>"
                    ></circle>
                </svg>
                <div class="-mt-24 text-3xl font-extrabold text-gray-900"><?php echo $score; ?></div>
            </div>
            <p class="mt-3 text-sm text-gray-500">Lead <?php echo sanitize($scoreUi['label']); ?></p>

            <div class="mt-4 space-y-1 text-left text-xs text-gray-600">
                <?php foreach ($scoreBreakdown as $criterion): ?>
                    <p>
                        <?php echo $criterion['ok'] ? '✓' : '✗'; ?>
                        <?php echo sanitize($criterion['label']); ?>
                        <?php echo $criterion['ok'] ? '+' . (int) $criterion['pts'] . 'pts' : '0pt'; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="rounded-xl border bg-white p-6">
            <h3 class="mb-3 text-lg font-semibold">Agent assigné</h3>
            <?php if (!empty($lead['agent_id'])): ?>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary font-bold text-white">
                        <?php echo strtoupper(substr((string) ($lead['agent_prenom'] ?? 'A'), 0, 1)); ?>
                    </div>
                    <div>
                        <p class="font-semibold"><?php echo sanitize(trim((string) ($lead['agent_prenom'] ?? '') . ' ' . (string) ($lead['agent_nom'] ?? ''))); ?></p>
                        <p class="text-xs text-gray-500"><?php echo sanitize((string) ($lead['agent_email'] ?? '')); ?></p>
                        <p class="text-xs text-gray-500"><?php echo sanitize((string) ($lead['agent_telephone'] ?? '')); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500">Aucun agent assigné</p>
            <?php endif; ?>

            <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=assign" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <select name="agent_assigne" class="w-full rounded border px-3 py-2 text-sm">
                    <option value="0">Non assigné</option>
                    <?php foreach ($agents as $agent):
                        $agentName = trim((string) ($agent['prenom'] ?? '') . ' ' . (string) ($agent['nom'] ?? ''));
                        ?>
                        <option value="<?php echo (int) $agent['id']; ?>" <?php echo (int) ($lead['agent_assigne'] ?? 0) === (int) $agent['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($agentName); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="mt-2 w-full rounded bg-primary px-3 py-2 text-sm font-semibold text-white">
                    <?php echo !empty($lead['agent_id']) ? 'Changer' : 'Assigner'; ?>
                </button>
            </form>
        </section>

        <section class="rounded-xl border bg-white p-6">
            <h3 class="mb-3 text-lg font-semibold">RDV</h3>
            <?php if (!empty($rdvs)):
                $rdv = $rdvs[0];
                ?>
                <p class="text-sm">
                    <strong>Date :</strong> <?php echo sanitize((string) ($rdv['date_souhaitee'] ?? '-')); ?>
                    • <?php echo sanitize((string) ($rdv['creneau'] ?? '-')); ?>
                </p>
                <p class="text-sm"><strong>Statut :</strong> <?php echo sanitize((string) ($rdv['statut'] ?? '')); ?></p>
                <?php if (!empty($rdv['message'])): ?>
                    <p class="mt-1 text-sm text-gray-500"><?php echo sanitize((string) $rdv['message']); ?></p>
                <?php endif; ?>
                <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=rdv_done" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="w-full rounded bg-green-600 px-3 py-2 text-sm font-semibold text-white">Marquer comme effectué</button>
                </form>
            <?php else: ?>
                <p class="text-sm text-gray-500">Aucun RDV</p>
                <form method="POST" action="lead.php?id=<?php echo $id; ?>&action=create_rdv" class="mt-3 space-y-2">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="nom" placeholder="Nom" class="w-full rounded border px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($lead['nom'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="email" placeholder="Email" class="w-full rounded border px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($lead['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="telephone" placeholder="Téléphone" class="w-full rounded border px-3 py-2 text-sm" value="<?php echo htmlspecialchars((string) ($lead['telephone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="date" name="date_souhaitee" class="w-full rounded border px-3 py-2 text-sm">
                    <select name="creneau" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="matin">Matin</option>
                        <option value="apres_midi">Après-midi</option>
                        <option value="soir">Soir</option>
                    </select>
                    <textarea name="message" class="w-full rounded border px-3 py-2 text-sm" placeholder="Message (optionnel)"></textarea>
                    <button class="w-full rounded bg-primary px-3 py-2 text-sm font-semibold text-white">Créer un RDV</button>
                </form>
            <?php endif; ?>
        </section>

        <section class="rounded-xl border bg-gray-50 p-4 text-xs text-gray-500">
            <p><strong>IP :</strong> <?php echo sanitize((string) ($lead['ip_address'] ?? '-')); ?></p>
            <p class="mt-1"><strong>User Agent :</strong> <?php echo sanitize(substr((string) ($lead['user_agent'] ?? '-'), 0, 120)); ?></p>
            <p class="mt-1">
                <strong>Source :</strong>
                <?php echo sanitize((string) ($lead['utm_source'] ?? '-')); ?> /
                <?php echo sanitize((string) ($lead['utm_medium'] ?? '-')); ?> /
                <?php echo sanitize((string) ($lead['utm_campaign'] ?? '-')); ?>
            </p>
            <p class="mt-1"><strong>Première visite :</strong> <?php echo sanitize((string) ($lead['created_at'] ?? '-')); ?></p>
            <p class="mt-1"><strong>Dernière activité :</strong> <?php echo sanitize($lastActivity !== '' ? $lastActivity : '-'); ?></p>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
