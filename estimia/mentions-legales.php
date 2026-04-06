<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Mentions légales';
$pageDescription = 'Mentions légales du site EstimIA.';

include __DIR__ . '/includes/header.php';
?>
<section class="bg-white pb-20 pt-28">
    <article class="prose prose-slate mx-auto max-w-3xl px-6">
        <h1>Mentions légales</h1>

        <h2>Éditeur du site</h2>
        <p>
            EstimIA (placeholder) – société en cours de constitution.<br>
            Contact : contact@estimia.fr
        </p>

        <h2>Hébergeur</h2>
        <p>
            O2Switch – 222 Boulevard Gustave Flaubert, 63000 Clermont-Ferrand, France.
        </p>

        <h2>RGPD & données personnelles</h2>
        <p>
            Les données collectées via les formulaires sont utilisées uniquement pour traiter les demandes d'estimation
            et de rendez-vous. Elles sont conservées de manière sécurisée et ne sont pas revendues à des tiers.
        </p>

        <h2>Cookies</h2>
        <p>
            Le site peut utiliser des cookies techniques et de mesure d'audience. Un bandeau de gestion du consentement
            sera affiché lors de la mise en production.
        </p>

        <h2>Droit d'accès et de rectification</h2>
        <p>
            Conformément à la réglementation en vigueur, vous pouvez demander l'accès, la rectification ou la suppression
            de vos données en écrivant à : contact@estimia.fr.
        </p>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
