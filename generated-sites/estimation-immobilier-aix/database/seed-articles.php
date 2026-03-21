#!/usr/bin/env php
<?php

/**
 * Seed script: insert 3 blog articles into the database.
 *
 * Usage: php database/seed-articles.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;
use App\Core\Config;

echo "=== Seed : insertion de 3 articles blog ===\n\n";

try {
    $pdo = Database::connection();
    echo "Connexion OK\n\n";
} catch (\Throwable $e) {
    echo "ECHEC connexion : " . $e->getMessage() . "\n";
    exit(1);
}

$websiteId = (int) Config::get('website.id', 1);

$articles = [
    [
        'title' => 'Estimation immobilière à Aix-en-Provence : comment obtenir le prix juste en 2026',
        'slug' => 'estimation-immobiliere-aix-prix-juste-2026',
        'meta_title' => 'Estimation immobilière Aix-en-Provence 2026 : obtenir le prix juste',
        'meta_description' => 'Découvrez les méthodes fiables pour estimer votre bien immobilier à Aix-en-Provence en 2026. Prix au m², facteurs de valorisation et erreurs à éviter.',
        'persona' => 'Propriétaire hésitant',
        'awareness_level' => 'solution',
        'status' => 'published',
        'content' => <<<'HTML'
<h2>Pourquoi une estimation précise est essentielle à Aix-en-Provence</h2>
<p>Le marché immobilier de aix-en-provence reste l'un des plus dynamiques de France, mais aussi l'un des plus exigeants. En 2026, le prix moyen au m² à Aix-en-Provence se situe autour de <strong>4 800 €</strong>, avec des écarts considérables selon les quartiers. Une estimation trop haute fait fuir les acheteurs ; trop basse, vous perdez de l'argent.</p>

<h2>Les 3 méthodes d'estimation utilisées par les professionnels</h2>

<h3>1. La méthode comparative</h3>
<p>C'est la méthode la plus courante. Elle consiste à comparer votre bien avec des ventes récentes similaires dans le même quartier. À Aix-en-Provence, les données DVF (Demandes de Valeurs Foncières) permettent d'accéder aux transactions réelles des 5 dernières années.</p>

<h3>2. La méthode par capitalisation</h3>
<p>Utilisée principalement pour les biens locatifs, elle évalue la valeur du bien en fonction des revenus locatifs qu'il génère. Un appartement T2 aux Chartrons loué 850 €/mois avec un rendement attendu de 4 % sera valorisé autour de 255 000 €.</p>

<h3>3. La méthode par le coût de remplacement</h3>
<p>Plus rare, elle calcule combien coûterait la reconstruction du bien à neuf, moins la vétusté. Elle est pertinente pour les maisons atypiques ou les biens de caractère dans des quartiers comme Saint-Pierre.</p>

<h2>Les facteurs qui font varier le prix à Aix-en-Provence</h2>
<ul>
<li><strong>Le quartier</strong> : les Chartrons, le Triangle d'Or et Saint-Pierre affichent les prix les plus élevés (6 000-8 000 €/m²)</li>
<li><strong>L'étage et la luminosité</strong> : un dernier étage avec ascenseur gagne 10-15 % par rapport au rez-de-chaussée</li>
<li><strong>Le DPE</strong> : un bien classé F ou G subit une décote de 10 à 20 % depuis les nouvelles réglementations</li>
<li><strong>L'extérieur</strong> : terrasse, balcon ou jardin ajoutent 5 à 15 % de valeur selon la surface</li>
<li><strong>Le stationnement</strong> : un parking en centre-ville peut valoir 25 000 à 40 000 € seul</li>
</ul>

<h2>Les erreurs classiques à éviter</h2>
<p>La première erreur est de se fier uniquement aux estimations en ligne sans les croiser. La seconde est de surévaluer les travaux réalisés : une cuisine refaite il y a 10 ans n'apporte plus de plus-value significative. Enfin, ne confondez pas prix affiché et prix de vente réel — à Aix-en-Provence, la marge de négociation tourne autour de 3 à 5 %.</p>

<h2>FAQ</h2>
<h3>Combien coûte une estimation immobilière à Aix-en-Provence ?</h3>
<p>Une estimation en ligne est gratuite. Un avis de valeur par un agent immobilier est également gratuit et sans engagement. Seule l'expertise par un expert agréé (obligatoire dans certains cas juridiques) est payante, entre 250 et 500 €.</p>

<h3>En combien de temps peut-on vendre à Aix-en-Provence ?</h3>
<p>Le délai moyen de vente à Aix-en-Provence est de 60 à 90 jours pour un bien correctement estimé. Un bien surévalué peut rester plus de 6 mois sur le marché.</p>

<p><strong>Estimez votre bien gratuitement</strong> : notre outil utilise les données réelles du marché de aix-en-provence pour vous fournir une fourchette fiable en moins de 2 minutes. <a href="/estimation">Lancer mon estimation →</a></p>
HTML,
    ],
    [
        'title' => 'Vendre dans les Chartrons : guide complet du quartier le plus prisé de Aix-en-Provence',
        'slug' => 'vendre-chartrons-guide-quartier-aix',
        'meta_title' => 'Vendre aux Chartrons Aix-en-Provence : prix, conseils et stratégie 2026',
        'meta_description' => 'Guide complet pour vendre votre bien dans le quartier des Chartrons à Aix-en-Provence. Prix au m², profil des acheteurs, délais de vente et conseils de mise en valeur.',
        'persona' => 'Propriétaire pressé',
        'awareness_level' => 'produit',
        'status' => 'published',
        'content' => <<<'HTML'
<h2>Les Chartrons : un quartier en or pour les vendeurs</h2>
<p>Ancien quartier des négociants en vin, les Chartrons sont devenus l'adresse la plus recherchée de Aix-en-Provence. Avec ses rues pavées, ses boutiques indépendantes et sa proximité avec les quais, le quartier attire familles aisées, jeunes cadres et investisseurs. Le prix moyen au m² y atteint <strong>5 500 à 7 000 €</strong> selon l'emplacement exact.</p>

<h2>Qui sont les acheteurs aux Chartrons ?</h2>
<ul>
<li><strong>Jeunes couples CSP+</strong> (30-40 ans) cherchant un T3/T4 avec cachet, budget 400 000 – 550 000 €</li>
<li><strong>Familles</strong> en quête de maisons de ville ou grands appartements, budget 600 000 – 900 000 €</li>
<li><strong>Investisseurs</strong> visant la location meublée haut de gamme ou la location saisonnière</li>
<li><strong>Parisiens en mobilité</strong> séduits par la LGV (2h Paris-Aix-en-Provence) et le cadre de vie</li>
</ul>

<h2>Les atouts à mettre en avant pour vendre vite</h2>

<h3>L'authenticité du bâti</h3>
<p>Les échoppes de aix-en-provencees, les immeubles en pierre blonde, les parquets anciens et les moulures sont des arguments de vente majeurs. Ne les cachez pas sous des faux plafonds ou du placo — les acheteurs veulent du « vrai Aix-en-Provence ».</p>

<h3>La vie de quartier</h3>
<p>Le marché des Chartrons (dimanche matin), la rue Notre-Dame avec ses antiquaires, les quais pour le jogging et le vélo… Mentionnez ces éléments dans votre annonce, ils font partie du produit.</p>

<h3>Les transports</h3>
<p>La ligne C du tramway dessert le quartier. L'accès à la rocade est rapide via les quais. Précisez les distances : 10 min à pied du Grand Théâtre, 15 min en tram de la gare Saint-Jean.</p>

<h2>Stratégie de prix : ne surjouez pas</h2>
<p>Paradoxalement, les biens surévalués aux Chartrons mettent plus longtemps à se vendre. Les acheteurs connaissent le marché. Notre recommandation : estimez au prix juste et créez une urgence d'achat. Un bien bien positionné aux Chartrons se vend en <strong>30 à 45 jours</strong>.</p>

<h2>FAQ</h2>
<h3>Faut-il faire des travaux avant de vendre aux Chartrons ?</h3>
<p>Pour un bien en bon état, un simple rafraîchissement (peinture blanche, nettoyage des parquets) suffit. Pour un bien à rénover, mieux vaut vendre en l'état — les acheteurs des Chartrons aiment personnaliser et le coût des travaux ne sera pas entièrement récupéré dans le prix.</p>

<h3>Quel est le meilleur moment pour vendre aux Chartrons ?</h3>
<p>Le printemps (mars-juin) reste la meilleure période. Les familles cherchent à s'installer avant la rentrée. Septembre-octobre offre aussi un bon pic d'activité.</p>

<p><strong>Estimez la valeur de votre bien aux Chartrons</strong> en quelques clics. Notre algorithme intègre les données spécifiques du quartier. <a href="/estimation">Obtenir mon estimation gratuite →</a></p>
HTML,
    ],
    [
        'title' => 'DPE et valeur immobilière : ce que tout propriétaire de aix-en-provence doit savoir',
        'slug' => 'dpe-valeur-immobiliere-aix-proprietaire',
        'meta_title' => 'DPE Aix-en-Provence : impact sur la valeur de votre bien immobilier',
        'meta_description' => 'Comment le DPE influence le prix de vente de votre bien à Aix-en-Provence. Décotes par classe énergétique, aides à la rénovation et stratégies pour vendre malgré un mauvais DPE.',
        'persona' => 'Propriétaire méfiant',
        'awareness_level' => 'problème',
        'status' => 'published',
        'content' => <<<'HTML'
<h2>Le DPE est devenu un critère décisif à Aix-en-Provence</h2>
<p>Depuis les réformes réglementaires, le Diagnostic de Performance Énergétique (DPE) n'est plus un simple document administratif. Il conditionne directement la valeur de votre bien, sa capacité à être loué, et la vitesse à laquelle il se vendra. À Aix-en-Provence, où le parc immobilier ancien est majoritaire, c'est un sujet qui concerne <strong>plus de 40 % des propriétaires</strong>.</p>

<h2>L'impact concret du DPE sur les prix à Aix-en-Provence</h2>
<p>Les données du marché de aix-en-provence montrent des écarts significatifs :</p>
<ul>
<li><strong>Classe A-B</strong> : prime de +6 à +10 % par rapport au prix moyen du quartier</li>
<li><strong>Classe C-D</strong> : prix dans la moyenne du marché, aucune décote</li>
<li><strong>Classe E</strong> : décote de -5 à -8 %, les acheteurs négocient systématiquement</li>
<li><strong>Classe F</strong> : décote de -10 à -15 %, interdiction de location depuis 2025</li>
<li><strong>Classe G</strong> : décote de -15 à -25 %, considéré comme « passoire thermique »</li>
</ul>

<h3>Un exemple concret</h3>
<p>Un appartement T3 de 70 m² à Caudéran, classé D, se vend autour de 310 000 €. Le même bien classé F se négocie entre 265 000 et 280 000 € — soit 30 000 à 45 000 € de différence. Mais attention : les travaux d'amélioration énergétique coûtent souvent moins que cette décote.</p>

<h2>Faut-il rénover avant de vendre ?</h2>

<h3>Quand la rénovation est rentable</h3>
<p>Si votre bien est classé F ou G et que les travaux pour passer en D ou E coûtent moins de 15 000 €, c'est presque toujours rentable. Les interventions les plus efficaces à Aix-en-Provence :</p>
<ul>
<li><strong>Isolation des combles</strong> : 2 000 à 5 000 €, gain de 1 à 2 classes</li>
<li><strong>Remplacement des fenêtres</strong> : 5 000 à 10 000 € pour un T3, gain significatif en confort et DPE</li>
<li><strong>Changement de chaudière</strong> : 3 000 à 8 000 €, passage du fioul/gaz ancien vers une pompe à chaleur</li>
</ul>

<h3>Quand il vaut mieux vendre en l'état</h3>
<p>Si le bien nécessite une rénovation globale (toiture, façade, réseaux), vendez en l'état en ajustant le prix. Les investisseurs et les primo-accédants bricoleurs cherchent ce type de biens à Aix-en-Provence, surtout dans les quartiers en devenir comme la Bastide ou Saint-Michel.</p>

<h2>Les aides disponibles en Bouches-du-Rhône</h2>
<ul>
<li><strong>MaPrimeRénov'</strong> : jusqu'à 20 000 € selon les revenus et les travaux</li>
<li><strong>Éco-PTZ</strong> : prêt à taux zéro jusqu'à 50 000 € pour la rénovation énergétique</li>
<li><strong>Aides de Aix-en-Provence Métropole</strong> : subventions complémentaires pour les copropriétés</li>
<li><strong>CEE (Certificats d'Économie d'Énergie)</strong> : primes versées par les fournisseurs d'énergie</li>
</ul>

<h2>FAQ</h2>
<h3>Mon DPE est-il encore valable ?</h3>
<p>Les DPE réalisés avant le 1er juillet 2021 avec l'ancienne méthode ne sont plus valables. Si votre DPE date d'avant cette date, faites-le refaire avant de mettre en vente — le nouveau calcul pourrait d'ailleurs vous être favorable.</p>

<h3>Peut-on vendre un bien classé G ?</h3>
<p>Oui, la vente reste possible quelle que soit la classe DPE. Seule la <strong>mise en location</strong> est restreinte pour les classes F et G. Mais attendez-vous à une forte négociation de la part des acheteurs.</p>

<p><strong>Quel impact le DPE a-t-il sur votre bien ?</strong> Estimez sa valeur actuelle et découvrez le potentiel de valorisation. <a href="/estimation">Estimer mon bien maintenant →</a></p>
HTML,
    ],
];

$inserted = 0;

$sql = 'INSERT INTO articles (website_id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at)
        VALUES (:website_id, :title, :slug, :content, :meta_title, :meta_description, :persona, :awareness_level, :status, NOW())';

$stmt = $pdo->prepare($sql);

foreach ($articles as $i => $article) {
    echo "  " . ($i + 1) . ". {$article['title']}... ";

    // Check if slug already exists
    $check = $pdo->prepare('SELECT id FROM articles WHERE website_id = :wid AND slug = :slug LIMIT 1');
    $check->execute([':wid' => $websiteId, ':slug' => $article['slug']]);

    if ($check->fetch()) {
        echo "existe déjà, ignoré\n";
        continue;
    }

    try {
        $stmt->execute([
            ':website_id' => $websiteId,
            ':title' => $article['title'],
            ':slug' => $article['slug'],
            ':content' => $article['content'],
            ':meta_title' => $article['meta_title'],
            ':meta_description' => $article['meta_description'],
            ':persona' => $article['persona'],
            ':awareness_level' => $article['awareness_level'],
            ':status' => $article['status'],
        ]);
        echo "OK (ID: " . $pdo->lastInsertId() . ")\n";
        $inserted++;
    } catch (\PDOException $e) {
        echo "ERREUR - " . $e->getMessage() . "\n";
    }
}

echo "\n=== Résultat : {$inserted} article(s) inséré(s) ===\n";
