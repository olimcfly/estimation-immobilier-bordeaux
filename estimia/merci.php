<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pageTitle = 'Merci !';
$pageDescription = 'Merci pour votre demande. Un conseiller immobilier EstimIA vous contacte rapidement.';

$hasEstimation = isset($_SESSION['estimation_id']) && (int) $_SESSION['estimation_id'] > 0;
$estimationData = $_SESSION['estimation_result'] ?? [];

$adresse = sanitize($estimationData['adresse'] ?? 'Non renseignée');
$typeBien = sanitize($estimationData['type_bien'] ?? 'Non renseigné');
$surface = (int) ($estimationData['surface'] ?? 0);
$prixEstime = (int) ($estimationData['prix_estime'] ?? 0);

include __DIR__ . '/includes/header.php';
?>
<section class="flex min-h-screen items-center pb-20 pt-28">
    <div class="mx-auto max-w-lg px-6 text-center">
        <div class="animate-fade-in-up mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
            <i data-lucide="check" class="h-10 w-10 text-green-600"></i>
        </div>

        <h1 class="animate-fade-in-up animation-delay-100 mt-6 text-3xl font-extrabold text-gray-900">
            Merci pour votre confiance !
        </h1>

        <p class="animate-fade-in-up animation-delay-200 mt-4 text-lg text-gray-500">
            Un conseiller immobilier vous recontactera dans les 24 heures pour affiner votre estimation.
        </p>

        <?php if ($hasEstimation): ?>
            <div class="animate-fade-in-up animation-delay-300 mt-8 rounded-2xl bg-gray-50 p-6 text-left">
                <p class="text-sm font-semibold uppercase text-gray-500">Récapitulatif</p>
                <ul class="mt-3">
                    <li class="flex justify-between border-b border-gray-100 py-2">
                        <span class="text-gray-500">Adresse</span>
                        <span class="font-semibold text-gray-900"><?php echo $adresse; ?></span>
                    </li>
                    <li class="flex justify-between border-b border-gray-100 py-2">
                        <span class="text-gray-500">Type</span>
                        <span class="font-semibold text-gray-900"><?php echo ucfirst($typeBien); ?></span>
                    </li>
                    <li class="flex justify-between border-b border-gray-100 py-2">
                        <span class="text-gray-500">Surface</span>
                        <span class="font-semibold text-gray-900"><?php echo $surface; ?> m²</span>
                    </li>
                    <li class="flex justify-between border-b border-gray-100 py-2">
                        <span class="text-gray-500">Estimation</span>
                        <span class="font-semibold text-gray-900"><?php echo formatPrice($prixEstime); ?></span>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="animate-fade-in-up animation-delay-400 mt-8 rounded-2xl bg-blue-50 p-6 text-left">
            <h3 class="font-semibold text-primary">Prochaines étapes</h3>
            <ol class="ml-5 mt-3 list-decimal space-y-2 text-sm text-gray-600">
                <li>Notre expert analyse votre dossier</li>
                <li>Il vous contacte pour un échange personnalisé</li>
                <li>Vous recevez une estimation détaillée par email</li>
            </ol>
        </div>

        <div class="animate-fade-in-up mt-8">
            <a href="index.php" class="inline-flex rounded-xl bg-gray-100 px-8 py-3 font-semibold text-gray-700 transition-all hover:bg-gray-200">
                Faire une nouvelle estimation
            </a>
        </div>
    </div>
</section>
<?php
unset($_SESSION['estimation_id']);
include __DIR__ . '/includes/footer.php';
