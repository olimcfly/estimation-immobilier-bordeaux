<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isPost()) {
    redirect('index.php');
}

$csrfToken = $_POST['csrf_token'] ?? null;
if (!verifyCSRFToken(is_string($csrfToken) ? $csrfToken : null)) {
    redirect('index.php');
}

$adresse = sanitize($_POST['adresse'] ?? '');
$type_bien = sanitize($_POST['type_bien'] ?? '');
$surface = (int) ($_POST['surface'] ?? 0);
$budgetRaw = sanitize($_POST['budget'] ?? '');
$budget = $budgetRaw;
$adresseComplete = sanitize($_POST['adresse_complete'] ?? '');
$latitude = ($_POST['latitude'] ?? '') !== '' ? (float) $_POST['latitude'] : null;
$longitude = ($_POST['longitude'] ?? '') !== '' ? (float) $_POST['longitude'] : null;
$villeDetectee = sanitize($_POST['ville'] ?? '');
$codePostal = sanitize($_POST['code_postal'] ?? '');
$departement = sanitize($_POST['departement'] ?? '');
$placeId = sanitize($_POST['place_id'] ?? '');

$typesAutorises = ['appartement', 'maison', 'studio', 'terrain'];
$budgetsAutorises = ['moins_150k', '150k_300k', '300k_500k', 'plus_500k'];

if (!in_array($budget, $budgetsAutorises, true) && is_numeric($budgetRaw)) {
    $budgetValeur = (int) $budgetRaw;
    $budget = match (true) {
        $budgetValeur < 150000 => 'moins_150k',
        $budgetValeur <= 300000 => '150k_300k',
        $budgetValeur <= 500000 => '300k_500k',
        default => 'plus_500k',
    };
}

if ($surface <= 0 || $adresse === '' || !in_array($type_bien, $typesAutorises, true) || !in_array($budget, $budgetsAutorises, true)) {
    redirect('index.php');
}

$ville = '';
if ($villeDetectee !== '') {
    $ville = ucfirst(strtolower($villeDetectee));
} else {
    $parts = array_values(array_filter(array_map('trim', explode(',', $adresse))));
    $villeRaw = $parts[1] ?? $parts[0] ?? $adresse;
    $villeMot = preg_split('/\s+/', $villeRaw);
    $ville = ucfirst(strtolower((string) ($villeMot[0] ?? 'France')));
}

$estimation = calculerEstimation($surface, $type_bien, $ville);
$prix_estime = (int) $estimation['prix_estime'];
$prix_bas = (int) $estimation['prix_bas'];
$prix_haut = (int) $estimation['prix_haut'];
$prix_m2 = (int) $estimation['prix_m2'];
$tendance = (float) $estimation['tendance'];
$nb_transactions = (int) $estimation['nb_transactions'];
$fiabilite = (string) ($estimation['fiabilite'] ?? 'Élevée');
$messageZone = (string) ($estimation['message_zone'] ?? '');

$estimationId = null;
$saveWarning = '';

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('INSERT INTO estimations (
        adresse, adresse_complete, ville, type_bien, surface, budget_estimation, prix_estime, prix_bas, prix_haut, prix_m2,
        latitude, longitude, code_postal, departement, place_id, ip_address, user_agent
    ) VALUES (
        :adresse, :adresse_complete, :ville, :type_bien, :surface, :budget_estimation, :prix_estime, :prix_bas, :prix_haut, :prix_m2,
        :latitude, :longitude, :code_postal, :departement, :place_id, :ip_address, :user_agent
    )');

    $stmt->execute([
        'adresse' => $adresse,
        'adresse_complete' => $adresseComplete !== '' ? $adresseComplete : null,
        'ville' => $ville,
        'type_bien' => $type_bien,
        'surface' => $surface,
        'budget_estimation' => $budget,
        'prix_estime' => $prix_estime,
        'prix_bas' => $prix_bas,
        'prix_haut' => $prix_haut,
        'prix_m2' => $prix_m2,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'code_postal' => $codePostal !== '' ? $codePostal : null,
        'departement' => $departement !== '' ? $departement : null,
        'place_id' => $placeId !== '' ? $placeId : null,
        'ip_address' => getClientIp(),
        'user_agent' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
    ]);

    $estimationId = (int) $pdo->lastInsertId();

    $leadScore = calculerLeadScore([
        'adresse_complete' => $adresseComplete,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'telephone' => '',
        'email' => '',
        'rdv_pris' => 0,
        'projet' => '',
        'delai_vente' => '',
        'utm_source' => '',
        'budget_estimation' => $budget,
        'prix_estime' => $prix_estime,
    ]);

    envoyerNotification('new_estimation', [
        'id' => $estimationId,
        'adresse' => $adresseComplete !== '' ? $adresseComplete : $adresse,
        'type_bien' => $type_bien,
        'surface' => $surface,
        'prix_estime' => $prix_estime,
        'lead_score' => $leadScore,
    ]);

    if ($leadScore > 70) {
        envoyerNotification('hot_lead', [
            'id' => $estimationId,
            'lead_score' => $leadScore,
            'adresse' => $adresseComplete !== '' ? $adresseComplete : $adresse,
            'type_bien' => $type_bien,
            'surface' => $surface,
            'prix_estime' => $prix_estime,
        ]);
    }
} catch (Throwable $e) {
    $estimationId = 0;
    $saveWarning = 'Estimation calculée, mais impossible de sauvegarder vos données pour le moment.';
}

$_SESSION['estimation_id'] = $estimationId;
$_SESSION['estimation_result'] = [
    'adresse' => $adresse,
    'ville' => $ville,
    'type_bien' => $type_bien,
    'surface' => $surface,
    'budget' => $budget,
    'adresse_complete' => $adresseComplete,
    'latitude' => $latitude,
    'longitude' => $longitude,
    'code_postal' => $codePostal,
    'departement' => $departement,
    'place_id' => $placeId,
    'prix_estime' => $prix_estime,
    'prix_bas' => $prix_bas,
    'prix_haut' => $prix_haut,
    'prix_m2' => $prix_m2,
    'tendance' => $tendance,
    'nb_transactions' => $nb_transactions,
];

$pageTitle = 'Résultat - Estimation à ' . sanitize($ville);
$pageDescription = 'Consultez instantanément la valeur estimée de votre bien immobilier à ' . sanitize($ville) . '.';

include __DIR__ . '/includes/header.php';
?>
<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white pb-20 pt-28">
    <div class="mx-auto max-w-2xl px-6">
        <div class="animate-fade-in-up text-center">
            <?php if ($saveWarning !== ''): ?>
                <div class="mx-auto mb-4 max-w-xl rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <?php echo htmlspecialchars($saveWarning, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-1.5 text-green-700">
                <i data-lucide="check" class="h-4 w-4"></i>
                <span>Estimation terminée</span>
            </div>
            <h1 class="mt-4 text-2xl font-extrabold md:text-3xl">
                Votre estimation à <span class="text-primary"><?php echo sanitize($ville); ?></span>
            </h1>
            <p class="mt-2 text-gray-500">
                <?php echo strtoupper($type_bien); ?> • <?php echo (int) $surface; ?> m²
            </p>
        </div>

        <div class="animate-fade-in-up animation-delay-100 relative mt-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-600 to-indigo-700 p-10 text-center text-white">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/5"></div>

            <div class="relative z-10">
                <p class="text-sm uppercase tracking-wider text-white/70">Valeur estimée de votre bien</p>
                <p class="mt-3 text-5xl font-black md:text-6xl"><?php echo formatPrice($prix_estime); ?></p>
                <p class="mt-3 text-lg text-white/60">
                    Entre <?php echo formatPrice($prix_bas); ?> et <?php echo formatPrice($prix_haut); ?>
                </p>
            </div>
        </div>

        <div class="animate-fade-in-up animation-delay-200 mt-6 grid grid-cols-2 gap-4">
            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50">
                    <i data-lucide="bar-chart-3" class="h-5 w-5 text-primary"></i>
                </div>
                <p class="mt-3 text-xl font-bold text-gray-900"><?php echo formatPrice($prix_m2); ?></p>
                <p class="mt-0.5 text-sm text-gray-500">Prix au m²</p>
            </article>

            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50">
                    <i data-lucide="trending-up" class="h-5 w-5 text-green-600"></i>
                </div>
                <p class="mt-3 text-xl font-bold text-green-600">+<?php echo number_format($tendance, 2, ',', ''); ?>% sur 1 an</p>
                <p class="mt-0.5 text-sm text-gray-500">Tendance du marché</p>
            </article>

            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50">
                    <i data-lucide="shield" class="h-5 w-5 text-amber-600"></i>
                </div>
                <p class="mt-3 text-xl font-bold text-gray-900"><?php echo sanitize($fiabilite); ?></p>
                <p class="mt-0.5 text-sm text-gray-500">Indice de fiabilité</p>
                <div class="mt-2 h-1.5 rounded-full bg-gray-200">
                    <div class="h-full <?php echo $fiabilite === 'Faible' ? 'w-[45%] bg-amber-500' : 'w-[92%] bg-green-500'; ?> rounded-full"></div>
                </div>
                <?php if ($messageZone !== ''): ?><p class="mt-2 text-xs text-amber-600"><?php echo sanitize($messageZone); ?></p><?php endif; ?>
            </article>

            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50">
                    <i data-lucide="file-text" class="h-5 w-5 text-purple-600"></i>
                </div>
                <p class="mt-3 text-xl font-bold text-gray-900"><?php echo number_format($nb_transactions, 0, ',', ' '); ?> ventes</p>
                <p class="mt-0.5 text-sm text-gray-500">Transactions récentes</p>
            </article>
        </div>

        <div class="animate-fade-in-up animation-delay-300 mt-10 rounded-3xl border border-gray-100 bg-white p-8 text-center shadow-lg">
            <div class="text-4xl">🎯</div>
            <h2 class="mt-3 text-xl font-bold">Affinez votre estimation avec un expert</h2>
            <p class="mx-auto mt-2 max-w-md text-gray-500">
                Nos conseillers immobiliers vous accompagnent gratuitement pour une évaluation précise de votre bien.
            </p>

            <div class="mt-6 flex flex-col justify-center gap-3 md:flex-row">
                <a href="estimation-detaillee.php" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-primary bg-white px-8 py-3.5 font-semibold text-primary transition-all hover:bg-primary/5">
                    <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                    Affiner mon estimation
                </a>

                <a href="rdv.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-8 py-3.5 font-semibold text-white shadow-lg shadow-blue-500/25 transition-all hover:-translate-y-0.5 hover:shadow-xl">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                    Prendre rendez-vous
                </a>

                <a href="index.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-100 px-8 py-3.5 font-semibold text-gray-700 transition-all hover:bg-gray-200">
                    Nouvelle estimation
                </a>
            </div>
        </div>

        <div class="animate-fade-in-up animation-delay-400 mt-8 text-center">
            <p class="mx-auto max-w-lg text-xs text-gray-400">
                * Cette estimation est donnée à titre indicatif et ne constitue pas une évaluation officielle.
                Elle est basée sur les données du marché immobilier local.
            </p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
