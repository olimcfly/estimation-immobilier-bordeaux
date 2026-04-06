<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$csrfToken = generateCSRFToken();

$villeParam = sanitize($_GET['ville'] ?? '');
$villeLanding = '';
$prixM2Ville = null;

if ($villeParam !== '') {
    $villeLanding = ucfirst(strtolower(str_replace('-', ' ', $villeParam)));

    try {
        $pdo = Database::getConnection();
        $stmtVille = $pdo->prepare('SELECT ville, prix_m2_appartement FROM villes_prix WHERE LOWER(ville) = LOWER(:ville) LIMIT 1');
        $stmtVille->execute(['ville' => $villeLanding]);
        $villeData = $stmtVille->fetch();

        if ($villeData) {
            $villeLanding = (string) $villeData['ville'];
            $prixM2Ville = (int) ($villeData['prix_m2_appartement'] ?? 0);
        }
    } catch (Throwable $e) {
        $prixM2Ville = null;
    }
}

if ($villeLanding !== '') {
    $pageTitle = 'Estimation Immobilière Gratuite à ' . $villeLanding;
    $pageDescription = 'Estimez gratuitement votre bien immobilier à ' . $villeLanding . '. Résultat instantané, précis et sans engagement.';
    $heroTitle = 'Combien vaut votre bien à ' . $villeLanding . ' ?';
} else {
    $pageTitle = 'Estimation Immobilière Gratuite en 30 Secondes';
    $pageDescription = 'Estimez gratuitement la valeur de votre bien à ' . siteConfig('city', 'votre zone') . ' et alentours. Résultat instantané, précis et sans engagement.';
    $heroTitle = 'Estimez votre bien à ' . siteConfig('city', 'votre ville') . ' et alentours';
}

include __DIR__ . '/includes/header.php';
?>
<section id="formulaire" class="relative flex min-h-screen items-center justify-center bg-gradient-to-b from-gray-50 to-white">
    <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-blue-500/5 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto w-full max-w-xl px-6 py-24">
        <div class="animate-fade-in-up inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-1.5 text-sm font-medium text-primary">
            <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
            <span>Estimation gratuite en 30 secondes</span>
        </div>

        <h1 class="animate-fade-in-up animation-delay-100 mt-6 text-center text-4xl font-black leading-tight text-gray-900 md:text-5xl">
            <?php if ($villeLanding !== ''): ?>
                <?php echo htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?>
            <?php else: ?>
                <?php echo htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?><br>
                <span class="bg-gradient-to-r from-primary to-indigo-600 bg-clip-text text-transparent">dans un rayon de <?php echo (int) siteConfig('radius', 30); ?> km</span>
            <?php endif; ?>
        </h1>

        <p class="animate-fade-in-up animation-delay-200 mx-auto mt-4 max-w-md text-center text-lg text-gray-500">
            Renseignez 4 informations simples et obtenez une estimation fiable instantanément.
        </p>

        <form method="POST" action="resultat.php" id="estimationForm" class="animate-fade-in-up animation-delay-300 mt-10 rounded-3xl border border-gray-100 bg-white p-8 shadow-2xl md:p-10">
            <div class="mb-5">
                <label for="adresse" class="mb-2 block text-sm font-semibold text-gray-700">Adresse ou ville</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                    <input
                        id="adresseAutocomplete"
                        type="text"
                        name="adresse"
                        required
                        autocomplete="off"
                        value="<?php echo htmlspecialchars($villeLanding !== '' ? $villeLanding : '', ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Tapez votre adresse complète..."
                        class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-4 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10"
                    >
                    <input type="hidden" name="adresse_complete" id="adresseComplete">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="ville" id="villeDetectee">
                    <input type="hidden" name="code_postal" id="codePostal">
                    <input type="hidden" name="departement" id="departement">
                    <input type="hidden" name="place_id" id="placeId">
                    <div id="adresseConfirmation" class="mt-1 hidden text-sm text-green-600"></div>
                </div>
            </div>

            <div class="mb-5">
                <label for="type_bien" class="mb-2 block text-sm font-semibold text-gray-700">Type de bien</label>
                <div class="relative">
                    <i data-lucide="home" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                    <select
                        id="type_bien"
                        name="type_bien"
                        required
                        class="form-input w-full appearance-none rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-10 text-gray-800 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10"
                    >
                        <option value="" disabled selected>Choisissez...</option>
                        <option value="appartement">🏢 Appartement</option>
                        <option value="maison">🏠 Maison</option>
                        <option value="studio">🏠 Studio</option>
                        <option value="terrain">🌍 Terrain</option>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label for="surface" class="mb-2 block text-sm font-semibold text-gray-700">Surface</label>
                <div class="relative">
                    <i data-lucide="maximize" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                    <input
                        id="surface"
                        type="number"
                        name="surface"
                        required
                        min="5"
                        max="10000"
                        placeholder="Ex: 75"
                        class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-14 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10"
                    >
                    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 font-medium text-gray-400">m²</span>
                </div>
            </div>

            <div class="mb-8">
                <label for="budget" class="mb-2 block text-sm font-semibold text-gray-700">Selon vous, combien vaut votre bien ?</label>
                <div class="relative">
                    <i data-lucide="euro" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                    <select
                        id="budget"
                        name="budget"
                        required
                        class="form-input w-full appearance-none rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-10 text-gray-800 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10"
                    >
                        <option value="" disabled selected>Sélectionnez une fourchette</option>
                        <option value="moins_150k">Moins de 150 000 €</option>
                        <option value="150k_300k">150 000 € - 300 000 €</option>
                        <option value="300k_500k">300 000 € - 500 000 €</option>
                        <option value="plus_500k">Plus de 500 000 €</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-blue-500/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-blue-500/30"
            >
                Obtenir mon estimation
                <span aria-hidden="true">→</span>
            </button>

            <p class="mt-4 flex items-center justify-center gap-1 text-center text-xs text-gray-400">
                <i data-lucide="lock" class="h-3.5 w-3.5"></i>
                Gratuit, instantané et sans engagement
            </p>
        </form>

        <?php if ($villeLanding !== '' && $prixM2Ville !== null && $prixM2Ville > 0): ?>
            <div class="animate-fade-in-up animation-delay-300 mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-center text-sm text-blue-800">
                Prix moyen à <?php echo htmlspecialchars($villeLanding, ENT_QUOTES, 'UTF-8'); ?> :
                <strong><?php echo formatPrice($prixM2Ville); ?>/m²</strong>
            </div>
        <?php endif; ?>

        <div class="animate-fade-in-up animation-delay-400 mt-12 flex justify-center gap-10 md:gap-16">
            <div class="text-center">
                <p class="count-up text-2xl font-extrabold text-gray-900" data-target="12000">12 000</p>
                <p class="mt-1 text-xs uppercase tracking-wider text-gray-400">estimations</p>
            </div>
            <div class="text-center">
                <p class="count-up text-2xl font-extrabold text-gray-900" data-target="96">96</p>
                <p class="mt-1 text-xs uppercase tracking-wider text-gray-400">précision</p>
            </div>
            <div class="text-center">
                <p class="count-up text-2xl font-extrabold text-gray-900" data-target="30">30</p>
                <p class="mt-1 text-xs uppercase tracking-wider text-gray-400">résultat</p>
            </div>
        </div>
    </div>
</section>
<?php
$radius = (float) siteConfig('radius', 30);
$delta = max(0.1, $radius / 111);
?>
<script>
    window.siteBounds = {
        north: <?php echo (float) siteConfig('city_lat', 44.8378) + $delta; ?>,
        south: <?php echo (float) siteConfig('city_lat', 44.8378) - $delta; ?>,
        east: <?php echo (float) siteConfig('city_lng', -0.5792) + $delta; ?>,
        west: <?php echo (float) siteConfig('city_lng', -0.5792) - $delta; ?>,
    };
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
