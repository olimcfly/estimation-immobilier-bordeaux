<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Politique de confidentialité';
$pageDescription = 'Politique de confidentialité EstimIA.';

include __DIR__ . '/includes/header.php';
?>
<section class="bg-white pb-20 pt-28">
    <article class="prose prose-slate mx-auto max-w-3xl px-6">
        <h1>Politique de confidentialité</h1>

        <h2>Données collectées</h2>
        <p>
            Nous collectons les informations saisies dans les formulaires : adresse, type de bien, surface,
            coordonnées (nom, email, téléphone) et informations techniques (IP, user-agent).
        </p>

        <h2>Finalité du traitement</h2>
        <p>
            Les données sont utilisées pour fournir une estimation immobilière, organiser un rendez-vous
            avec un conseiller, et améliorer la qualité de nos services.
        </p>

        <h2>Durée de conservation</h2>
        <p>
            Les données sont conservées pendant une durée proportionnée aux finalités de traitement,
            puis archivées ou supprimées conformément aux obligations légales.
        </p>

        <h2>Droits des utilisateurs</h2>
        <p>
            Vous disposez d'un droit d'accès, de rectification, d'opposition, d'effacement,
            et de limitation du traitement de vos données.
        </p>

        <h2>Cookies</h2>
        <p>
            Des cookies peuvent être utilisés pour le fonctionnement technique du site et la mesure d'audience.
            Vous pouvez modifier vos préférences à tout moment.
        </p>

        <h2>Contact DPO</h2>
        <p>
            Pour toute question relative à la protection des données : dpo@estimia.fr
        </p>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
