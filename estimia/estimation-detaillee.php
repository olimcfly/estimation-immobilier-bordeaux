<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['estimation_id']) || (int) $_SESSION['estimation_id'] <= 0) {
    redirect('index.php');
}

$estimationId = (int) $_SESSION['estimation_id'];
$pdo = Database::getConnection();

$stmtEstimation = $pdo->prepare('SELECT * FROM estimations WHERE id = :id LIMIT 1');
$stmtEstimation->execute(['id' => $estimationId]);
$estimation = $stmtEstimation->fetch();

if (!$estimation) {
    redirect('index.php');
}

$csrfToken = generateCSRFToken();
$errorMessage = '';

$formData = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'nb_pieces' => '',
    'nb_chambres' => '',
    'etage' => '',
    'annee_construction_range' => '',
    'etat_general' => '',
    'type_chauffage' => '',
    'dpe' => 'non_renseigne',
    'balcon' => '0',
    'terrasse' => '0',
    'jardin' => '0',
    'surface_terrain' => '',
    'parking' => '0',
    'garage' => '0',
    'piscine' => '0',
    'cave' => '0',
    'projet' => 'estimer_seulement',
    'delai_vente' => 'pas_presse',
    'deja_en_vente' => '0',
    'agence_actuelle' => '',
];

if (isPost()) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        redirect('index.php');
    }

    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = sanitize($_POST[$key] ?? $defaultValue);
    }

    if ($formData['nom'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || $formData['telephone'] === '') {
        $errorMessage = 'Veuillez renseigner correctement les champs obligatoires.';
    } else {
        $anneeMap = [
            'avant_1950' => 1940,
            '1950_1970' => 1960,
            '1970_1990' => 1980,
            '1990_2000' => 1995,
            '2000_2010' => 2005,
            '2010_2020' => 2015,
            'apres_2020' => (int) date('Y') - 1,
        ];

        $etageRaw = $formData['etage'];
        $maisonIndividuelle = $etageRaw === 'maison';
        $etage = match ($etageRaw) {
            'rdc' => 0,
            'maison' => null,
            '10+' => 10,
            default => is_numeric($etageRaw) ? (int) $etageRaw : null,
        };
        $nbEtagesImmeuble = $etage !== null && $etage >= 0 ? max($etage, 6) : null;

        $details = [
            'nom' => $formData['nom'],
            'prenom' => $formData['prenom'],
            'email' => $formData['email'],
            'telephone' => $formData['telephone'],
            'nb_pieces' => (int) ($formData['nb_pieces'] !== '' ? $formData['nb_pieces'] : 0),
            'nb_chambres' => (int) ($formData['nb_chambres'] !== '' ? $formData['nb_chambres'] : 0),
            'etage' => $etage,
            'nb_etages_immeuble' => $nbEtagesImmeuble,
            'annee_construction' => $anneeMap[$formData['annee_construction_range']] ?? null,
            'etat_general' => $formData['etat_general'] !== '' ? $formData['etat_general'] : null,
            'type_chauffage' => $formData['type_chauffage'] !== '' ? $formData['type_chauffage'] : null,
            'dpe' => $formData['dpe'] !== '' ? $formData['dpe'] : 'non_renseigne',
            'balcon' => (int) ($formData['balcon'] === '1'),
            'terrasse' => (int) ($formData['terrasse'] === '1'),
            'jardin' => (int) ($formData['jardin'] === '1'),
            'surface_terrain' => $formData['surface_terrain'] !== '' ? (int) $formData['surface_terrain'] : null,
            'parking' => (int) ($formData['parking'] === '1'),
            'garage' => (int) ($formData['garage'] === '1'),
            'piscine' => (int) ($formData['piscine'] === '1'),
            'cave' => (int) ($formData['cave'] === '1'),
            'projet' => $formData['projet'],
            'delai_vente' => $formData['delai_vente'],
            'deja_en_vente' => (int) ($formData['deja_en_vente'] === '1'),
            'agence_actuelle' => $formData['agence_actuelle'] !== '' ? $formData['agence_actuelle'] : null,
            'maison_individuelle' => $maisonIndividuelle,
        ];

        $estimationDetaillee = calculerEstimationDetaillee($estimation, $details);

        $leadScore = calculerLeadScore(array_merge($estimation, $details, [
            'rdv_pris' => $estimation['rdv_pris'] ?? 0,
            'utm_source' => $estimation['utm_source'] ?? '',
            'prix_estime' => $estimationDetaillee['prix_estime'],
            'lead_detaille' => true,
        ]));

        $pdo->beginTransaction();

        try {
            $insert = $pdo->prepare('INSERT INTO leads_detailles (
                estimation_id, nom, prenom, email, telephone, est_proprietaire,
                nb_pieces, nb_chambres, etage, nb_etages_immeuble, annee_construction,
                etat_general, type_chauffage, dpe, balcon, terrasse, jardin, surface_terrain,
                parking, garage, piscine, cave, projet, delai_vente, deja_en_vente,
                agence_actuelle, prix_estime_detaille, prix_bas_detaille, prix_haut_detaille
            ) VALUES (
                :estimation_id, :nom, :prenom, :email, :telephone, 1,
                :nb_pieces, :nb_chambres, :etage, :nb_etages_immeuble, :annee_construction,
                :etat_general, :type_chauffage, :dpe, :balcon, :terrasse, :jardin, :surface_terrain,
                :parking, :garage, :piscine, :cave, :projet, :delai_vente, :deja_en_vente,
                :agence_actuelle, :prix_estime_detaille, :prix_bas_detaille, :prix_haut_detaille
            )');

            $insert->execute([
                'estimation_id' => $estimationId,
                'nom' => $details['nom'],
                'prenom' => $details['prenom'] ?: null,
                'email' => $details['email'],
                'telephone' => $details['telephone'],
                'nb_pieces' => $details['nb_pieces'] ?: null,
                'nb_chambres' => $details['nb_chambres'] ?: null,
                'etage' => $details['etage'],
                'nb_etages_immeuble' => $details['nb_etages_immeuble'],
                'annee_construction' => $details['annee_construction'],
                'etat_general' => $details['etat_general'],
                'type_chauffage' => $details['type_chauffage'],
                'dpe' => $details['dpe'],
                'balcon' => $details['balcon'],
                'terrasse' => $details['terrasse'],
                'jardin' => $details['jardin'],
                'surface_terrain' => $details['surface_terrain'],
                'parking' => $details['parking'],
                'garage' => $details['garage'],
                'piscine' => $details['piscine'],
                'cave' => $details['cave'],
                'projet' => $details['projet'],
                'delai_vente' => $details['delai_vente'],
                'deja_en_vente' => $details['deja_en_vente'],
                'agence_actuelle' => $details['agence_actuelle'],
                'prix_estime_detaille' => $estimationDetaillee['prix_estime'],
                'prix_bas_detaille' => $estimationDetaillee['prix_bas'],
                'prix_haut_detaille' => $estimationDetaillee['prix_haut'],
            ]);

            $updateEstimation = $pdo->prepare('UPDATE estimations
                SET lead_type = :lead_type,
                    lead_score = :lead_score,
                    lead_statut = :lead_statut,
                    nom = :nom,
                    email = :email,
                    telephone = :telephone
                WHERE id = :id');

            $updateEstimation->execute([
                'lead_type' => 'estimation_detaillee',
                'lead_score' => $leadScore,
                'lead_statut' => 'qualifie',
                'nom' => $details['nom'],
                'email' => $details['email'],
                'telephone' => $details['telephone'],
                'id' => $estimationId,
            ]);

            $insertInteraction = $pdo->prepare('INSERT INTO lead_interactions (estimation_id, type_interaction, description, agent_id)
                VALUES (:estimation_id, :type_interaction, :description, :agent_id)');

            $insertInteraction->execute([
                'estimation_id' => $estimationId,
                'type_interaction' => 'creation',
                'description' => 'Estimation détaillée soumise',
                'agent_id' => $estimation['agent_assigne'] ?? null,
            ]);

            $pdo->commit();

            $_SESSION['resultat_detaille'] = $estimationDetaillee;
            redirect('resultat-detaille.php');
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errorMessage = 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.';
        }
    }
}

$pageTitle = 'Estimation Détaillée';
$pageDescription = 'Affinez votre estimation immobilière avec davantage de détails sur votre bien.';

include __DIR__ . '/includes/header.php';
?>
<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white pb-20 pt-28">
    <div class="mx-auto max-w-3xl px-6">
        <div>
            <p class="text-sm text-gray-400">Estimation simple <span class="mx-2">›</span> Estimation détaillée (étape 2)</p>
            <div class="mt-3 inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-sm font-medium text-primary">Affinez votre estimation</div>
            <h1 class="mt-4 text-2xl font-extrabold text-gray-900">Détaillez votre bien</h1>
            <p class="mt-2 text-gray-500">Plus d'informations = estimation plus précise</p>
        </div>

        <div class="mt-6 flex flex-col justify-between gap-4 rounded-xl border border-primary/10 bg-primary/5 p-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold text-gray-800"><?php echo sanitize((string) ($estimation['adresse'] ?? '-')); ?></p>
                <p class="text-sm text-gray-600"><?php echo ucfirst((string) ($estimation['type_bien'] ?? '')); ?> • <?php echo (int) ($estimation['surface'] ?? 0); ?> m²</p>
            </div>
            <p class="text-lg font-bold text-primary"><?php echo formatPrice((int) ($estimation['prix_estime'] ?? 0)); ?></p>
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-8 space-y-8">
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 flex items-center gap-2 border-b pb-3 font-semibold"><i data-lucide="user" class="h-5 w-5"></i>Vos coordonnées</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <input class="form-input rounded-xl border-2 border-gray-200 px-4 py-3" name="nom" placeholder="Nom *" required value="<?php echo htmlspecialchars($formData['nom'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input class="form-input rounded-xl border-2 border-gray-200 px-4 py-3" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($formData['prenom'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="email" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3" name="email" placeholder="Email *" required value="<?php echo htmlspecialchars($formData['email'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="tel" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3" name="telephone" placeholder="Téléphone *" required value="<?php echo htmlspecialchars($formData['telephone'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 flex items-center gap-2 border-b pb-3 font-semibold"><i data-lucide="home" class="h-5 w-5"></i>Caractéristiques du bien</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <select name="nb_pieces" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">Nombre de pièces</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option value="8">8+</option></select>
                    <select name="nb_chambres" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">Nombre de chambres</option><option>0</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option value="6">6+</option></select>
                    <select name="etage" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">Étage</option><option value="rdc">RDC</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option value="10+">10+</option><option value="maison">Maison individuelle</option></select>
                    <select name="annee_construction_range" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">Année de construction</option><option value="avant_1950">Avant 1950</option><option value="1950_1970">1950-1970</option><option value="1970_1990">1970-1990</option><option value="1990_2000">1990-2000</option><option value="2000_2010">2000-2010</option><option value="2010_2020">2010-2020</option><option value="apres_2020">Après 2020</option></select>
                    <select name="etat_general" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">État général</option><option value="neuf">Neuf</option><option value="tres_bon">Très bon</option><option value="bon">Bon</option><option value="a_rafraichir">À rafraîchir</option><option value="a_renover">À rénover</option></select>
                    <select name="type_chauffage" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="">Type de chauffage</option><option value="individuel_gaz">Individuel gaz</option><option value="individuel_electrique">Individuel électrique</option><option value="collectif">Collectif</option><option value="pompe_chaleur">Pompe à chaleur</option><option value="autre">Autre</option></select>
                    <select name="dpe" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3 md:col-span-2"><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option><option value="G">G</option><option value="non_renseigne">Je ne sais pas</option></select>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 flex items-center gap-2 border-b pb-3 font-semibold"><i data-lucide="trees" class="h-5 w-5"></i>Extérieurs et annexes</h3>
                <div class="grid gap-4 md:grid-cols-3">
                    <?php
                    $toggles = [
                        'balcon' => '🏗 Balcon',
                        'terrasse' => '☀️ Terrasse',
                        'jardin' => '🌳 Jardin',
                        'parking' => '🅿️ Parking',
                        'garage' => '🚗 Garage',
                        'piscine' => '🏊 Piscine',
                        'cave' => '📦 Cave',
                    ];
                    foreach ($toggles as $name => $label):
                        $active = $formData[$name] === '1';
                    ?>
                        <button type="button" data-toggle-target="<?php echo $name; ?>" class="toggle-card relative rounded-xl border-2 p-4 text-center <?php echo $active ? 'border-primary bg-primary/5' : 'border-gray-200 bg-gray-50'; ?>">
                            <span class="text-2xl"><?php echo explode(' ', $label)[0]; ?></span>
                            <p class="mt-2 text-sm font-medium"><?php echo substr($label, strlen(explode(' ', $label)[0]) + 1); ?></p>
                            <span class="absolute right-2 top-2 <?php echo $active ? '' : 'hidden'; ?>" data-check="<?php echo $name; ?>">✓</span>
                        </button>
                        <input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $active ? '1' : '0'; ?>">
                    <?php endforeach; ?>
                </div>

                <div id="surfaceTerrainWrapper" class="mt-4 <?php echo $formData['jardin'] === '1' ? '' : 'hidden'; ?>">
                    <input type="number" name="surface_terrain" placeholder="Surface du terrain en m²" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3" value="<?php echo htmlspecialchars($formData['surface_terrain'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-5 flex items-center gap-2 border-b pb-3 font-semibold"><i data-lucide="target" class="h-5 w-5"></i>Votre projet</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <select name="projet" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="vendre">Vendre</option><option value="estimer_seulement">Juste estimer</option><option value="succession">Succession</option><option value="divorce">Divorce</option><option value="investissement">Investissement</option><option value="autre">Autre</option></select>
                    <select name="delai_vente" class="form-input rounded-xl border-2 border-gray-200 px-4 py-3"><option value="urgent">Urgent &lt;1 mois</option><option value="3_mois">Dans 3 mois</option><option value="6_mois">Dans 6 mois</option><option value="1_an">Dans 1 an</option><option value="pas_presse">Pas pressé</option></select>

                    <div class="md:col-span-2">
                        <div class="inline-flex rounded-xl bg-gray-100 p-1">
                            <button type="button" id="btnDejaOui" class="rounded-lg px-4 py-2 text-sm font-semibold <?php echo $formData['deja_en_vente'] === '1' ? 'bg-primary text-white' : 'text-gray-600'; ?>">Déjà en vente : Oui</button>
                            <button type="button" id="btnDejaNon" class="rounded-lg px-4 py-2 text-sm font-semibold <?php echo $formData['deja_en_vente'] === '0' ? 'bg-white text-gray-800' : 'text-gray-600'; ?>">Non</button>
                        </div>
                        <input type="hidden" name="deja_en_vente" id="deja_en_vente" value="<?php echo htmlspecialchars($formData['deja_en_vente'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div id="agenceWrapper" class="md:col-span-2 <?php echo $formData['deja_en_vente'] === '1' ? '' : 'hidden'; ?>">
                        <input name="agence_actuelle" placeholder="Agence actuelle" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3" value="<?php echo htmlspecialchars($formData['agence_actuelle'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </section>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="estimation_id" value="<?php echo $estimationId; ?>">

            <button type="submit" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-blue-500/25 transition-all hover:-translate-y-0.5 hover:shadow-xl">
                Obtenir mon estimation détaillée →
            </button>

            <p class="mt-4 text-center text-xs text-gray-400">Les champs marqués * sont obligatoires. Vos données restent confidentielles.</p>
        </form>
    </div>
</section>
<script>
    document.querySelectorAll('.toggle-card').forEach((card) => {
        card.addEventListener('click', () => {
            const target = card.dataset.toggleTarget;
            const input = document.getElementById(target);
            if (!input) return;

            const isActive = input.value === '1';
            input.value = isActive ? '0' : '1';
            card.classList.toggle('border-primary', !isActive);
            card.classList.toggle('bg-primary/5', !isActive);
            card.classList.toggle('border-gray-200', isActive);
            card.classList.toggle('bg-gray-50', isActive);

            const check = card.querySelector(`[data-check="${target}"]`);
            if (check) check.classList.toggle('hidden', isActive);

            if (target === 'jardin') {
                document.getElementById('surfaceTerrainWrapper')?.classList.toggle('hidden', isActive);
            }
        });
    });

    const btnOui = document.getElementById('btnDejaOui');
    const btnNon = document.getElementById('btnDejaNon');
    const inputDeja = document.getElementById('deja_en_vente');
    const agence = document.getElementById('agenceWrapper');

    btnOui?.addEventListener('click', () => {
        inputDeja.value = '1';
        btnOui.classList.add('bg-primary', 'text-white');
        btnNon.classList.remove('bg-white', 'text-gray-800');
        agence?.classList.remove('hidden');
    });

    btnNon?.addEventListener('click', () => {
        inputDeja.value = '0';
        btnNon.classList.add('bg-white', 'text-gray-800');
        btnOui.classList.remove('bg-primary', 'text-white');
        agence?.classList.add('hidden');
    });
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
