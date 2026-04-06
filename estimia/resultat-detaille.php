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

$stmt = $pdo->prepare('SELECT e.*, ld.*,
    e.prix_estime AS simple_prix_estime,
    e.prix_bas AS simple_prix_bas,
    e.prix_haut AS simple_prix_haut,
    ld.prix_estime_detaille,
    ld.prix_bas_detaille,
    ld.prix_haut_detaille
    FROM estimations e
    INNER JOIN leads_detailles ld ON ld.estimation_id = e.id
    WHERE e.id = :id
    ORDER BY ld.created_at DESC
    LIMIT 1');
$stmt->execute(['id' => $estimationId]);
$data = $stmt->fetch();

if (!$data) {
    redirect('estimation-detaillee.php');
}

$details = [
    'etat_general' => $data['etat_general'] ?? null,
    'dpe' => $data['dpe'] ?? 'non_renseigne',
    'balcon' => (int) ($data['balcon'] ?? 0),
    'terrasse' => (int) ($data['terrasse'] ?? 0),
    'jardin' => (int) ($data['jardin'] ?? 0),
    'piscine' => (int) ($data['piscine'] ?? 0),
    'parking' => (int) ($data['parking'] ?? 0),
    'garage' => (int) ($data['garage'] ?? 0),
    'cave' => (int) ($data['cave'] ?? 0),
    'etage' => $data['etage'] ?? null,
    'nb_etages_immeuble' => $data['nb_etages_immeuble'] ?? null,
    'annee_construction' => $data['annee_construction'] ?? null,
    'maison_individuelle' => ($data['etage'] ?? null) === null && ($data['type_bien'] ?? '') === 'maison',
];

$detailCalc = calculerEstimationDetaillee($data, $details);

$update = $pdo->prepare('UPDATE leads_detailles
    SET prix_estime_detaille = :prix_estime,
        prix_bas_detaille = :prix_bas,
        prix_haut_detaille = :prix_haut
    WHERE id = :lead_id');
$update->execute([
    'prix_estime' => $detailCalc['prix_estime'],
    'prix_bas' => $detailCalc['prix_bas'],
    'prix_haut' => $detailCalc['prix_haut'],
    'lead_id' => $data['id'],
]);

$prixSimple = (int) ($data['simple_prix_estime'] ?? 0);
$prixSimpleBas = (int) ($data['simple_prix_bas'] ?? 0);
$prixSimpleHaut = (int) ($data['simple_prix_haut'] ?? 0);
$prixDetail = (int) $detailCalc['prix_estime'];
$prixDetailBas = (int) $detailCalc['prix_bas'];
$prixDetailHaut = (int) $detailCalc['prix_haut'];

$deltaPercent = $prixSimple > 0 ? round((($prixDetail - $prixSimple) / $prixSimple) * 100, 1) : 0;
$deltaPrefix = $deltaPercent >= 0 ? '+' : '';

$factors = [];
$addFactor = static function (array &$factors, string $icon, string $label, float $impact): void {
    $color = 'text-gray-500';
    $arrow = 'minus';

    if ($impact > 0) {
        $color = 'text-green-600';
        $arrow = 'arrow-up';
    } elseif ($impact < 0) {
        $color = 'text-red-600';
        $arrow = 'arrow-down';
    }

    $factors[] = [
        'icon' => $icon,
        'label' => $label,
        'impact' => ($impact > 0 ? '+' : '') . number_format($impact, 0, ',', '') . '%',
        'color' => $color,
        'arrow' => $arrow,
    ];
};

$etatImpacts = ['neuf' => 15, 'tres_bon' => 8, 'bon' => 0, 'a_rafraichir' => -10, 'a_renover' => -20];
$etatLabel = ['neuf' => 'État : Neuf', 'tres_bon' => 'État : Très bon', 'bon' => 'État : Bon', 'a_rafraichir' => 'État : À rafraîchir', 'a_renover' => 'État : À rénover'];
$etat = (string) ($details['etat_general'] ?? 'bon');
$addFactor($factors, 'home', $etatLabel[$etat] ?? 'État : Non renseigné', (float) ($etatImpacts[$etat] ?? 0));

$dpe = (string) ($details['dpe'] ?? 'non_renseigne');
$dpeImpact = in_array($dpe, ['A', 'B'], true) ? 5 : (in_array($dpe, ['F', 'G'], true) ? -12 : ($dpe === 'E' ? -5 : 0));
$addFactor($factors, 'leaf', 'DPE : ' . $dpe, (float) $dpeImpact);

$addFactor($factors, 'sun', !empty($details['terrasse']) ? 'Terrasse' : 'Pas de terrasse', !empty($details['terrasse']) ? 5 : 0);
$addFactor($factors, 'car', !empty($details['parking']) ? 'Parking' : 'Pas de parking', !empty($details['parking']) ? 5 : 0);
$addFactor($factors, 'waves', !empty($details['piscine']) ? 'Piscine' : 'Pas de piscine', !empty($details['piscine']) ? 10 : 0);

$etage = $details['etage'] !== null ? (int) $details['etage'] : null;
$etageImpact = 0;
$etageLabel = 'Étage : Maison';
if ($etage !== null) {
    if ($etage === 0) {
        $etageImpact = -5;
        $etageLabel = 'Étage : RDC';
    } elseif ($etage === 1) {
        $etageImpact = -2;
        $etageLabel = 'Étage : 1er';
    } elseif ($etage > 3) {
        $etageImpact = -3;
        $etageLabel = 'Étage : ' . $etage;
    } else {
        $etageLabel = 'Étage : ' . $etage;
    }
}
$addFactor($factors, 'building-2', $etageLabel, (float) $etageImpact);

$annee = (int) ($details['annee_construction'] ?? 0);
$constructionImpact = 0;
if ($annee > 0) {
    $age = (int) date('Y') - $annee;
    if ($age < 5) {
        $constructionImpact = 5;
    } elseif ($age > 50) {
        $constructionImpact = -3;
    }
}
$addFactor($factors, 'calendar-clock', $annee > 0 ? 'Construction : ' . $annee : 'Construction : NC', (float) $constructionImpact);

$pageTitle = 'Estimation Détaillée - Résultat';
$pageDescription = 'Résultat premium de votre estimation détaillée immobilière.';

include __DIR__ . '/includes/header.php';
?>
<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white pb-20 pt-28">
    <div class="mx-auto max-w-4xl px-6">
        <div class="animate-fade-in-up text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-1.5 text-amber-700">
                <span>⭐</span>
                <span>Estimation détaillée premium</span>
            </div>
            <h1 class="mt-4 text-3xl font-extrabold text-gray-900">Votre estimation précise</h1>
            <p class="mt-2 text-gray-500"><?php echo sanitize((string) ($data['adresse_complete'] ?? $data['adresse'] ?? 'Adresse non renseignée')); ?></p>
        </div>

        <div class="animate-fade-in-up animation-delay-100 relative mt-8 overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-indigo-600 to-purple-700 p-12 text-center text-white shadow-2xl">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/5"></div>

            <p class="text-sm uppercase tracking-wider text-white/70">Valeur détaillée de votre bien</p>
            <p class="mt-3 text-5xl font-black md:text-6xl"><?php echo formatPrice($prixDetail); ?></p>
            <p class="mt-3 text-lg text-white/80">Entre <?php echo formatPrice($prixDetailBas); ?> et <?php echo formatPrice($prixDetailHaut); ?></p>

            <div class="mx-auto mt-6 max-w-sm">
                <div class="mb-2 flex justify-between text-sm text-white/90">
                    <span>Fiabilité</span>
                    <span>94%</span>
                </div>
                <div class="h-2 rounded-full bg-white/20">
                    <div class="h-full w-[94%] rounded-full bg-emerald-300"></div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold">Comparaison des estimations</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Estimation simple</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo formatPrice($prixSimple); ?></p>
                    <p class="mt-1 text-sm text-gray-500"><?php echo formatPrice($prixSimpleBas); ?> - <?php echo formatPrice($prixSimpleHaut); ?></p>
                </div>
                <div class="rounded-xl border border-primary/20 bg-primary/5 p-4">
                    <p class="text-sm text-primary">Estimation détaillée</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo formatPrice($prixDetail); ?></p>
                    <p class="mt-1 text-sm text-gray-500"><?php echo formatPrice($prixDetailBas); ?> - <?php echo formatPrice($prixDetailHaut); ?></p>
                    <span class="mt-2 inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">✓ Plus précise</span>
                </div>
            </div>
            <p class="mt-4 text-sm font-semibold text-gray-700">Ajustement : <?php echo $deltaPrefix . number_format($deltaPercent, 1, ',', ''); ?>%</p>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <?php foreach ($factors as $factor): ?>
                <article class="flex items-center justify-between rounded-xl border bg-white p-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="<?php echo $factor['icon']; ?>" class="h-5 w-5 text-gray-500"></i>
                        <span class="text-sm font-medium text-gray-700"><?php echo $factor['label']; ?></span>
                    </div>
                    <div class="flex items-center gap-1 <?php echo $factor['color']; ?>">
                        <i data-lucide="<?php echo $factor['arrow']; ?>" class="h-4 w-4"></i>
                        <span class="text-sm font-semibold"><?php echo $factor['impact']; ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-3xl border bg-gradient-to-r from-gray-50 to-blue-50 p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-lg font-bold text-white">MD</div>
                    <div>
                        <p class="font-semibold text-gray-900">Un expert immobilier local peut valider cette estimation</p>
                        <p class="mt-1 text-sm text-gray-500">Gratuit, sans engagement, sous 24h</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="rdv.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-7 py-3.5 font-semibold text-white shadow-lg shadow-blue-500/25 hover:-translate-y-0.5 transition-all">
                        <i data-lucide="calendar" class="h-4 w-4"></i>
                        Prendre rendez-vous
                    </a>
                    <button disabled class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-primary px-7 py-3.5 font-semibold text-primary opacity-60 cursor-not-allowed">
                        <i data-lucide="download" class="h-4 w-4"></i>
                        Télécharger le rapport
                    </button>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-xs text-gray-400">
            * Cette estimation détaillée est plus fiable grâce aux informations complémentaires, mais reste indicative
            et ne constitue pas une expertise immobilière officielle.
        </p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
