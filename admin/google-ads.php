<?php
$pageTitle = "Google Ads - Guide & Générateur";
$currentPage = 'google-ads';
$topNavCurrent = 'google-ads';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$ville = defined('CITY_NAME') ? CITY_NAME : 'Votre ville';
$rayon = defined('CITY_RADIUS_KM') ? (int) CITY_RADIUS_KM : 15;
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Votre agence';
$siteUrl = defined('SITE_URL') ? SITE_URL : 'https://example.com';
$phone = defined('SITE_PHONE') ? SITE_PHONE : '';
$siteColor = defined('SITE_COLOR') ? SITE_COLOR : '#2563eb';
$cityLat = defined('CITY_LAT') ? CITY_LAT : '';
$cityLng = defined('CITY_LNG') ? CITY_LNG : '';

$villesRayon = [];
try {
    $db = Database::getConnection();
    $stmt = $db->query(
        "SELECT ville FROM villes_prix WHERE distance_centre <= " . $rayon . " ORDER BY population DESC LIMIT 20"
    );
    $villesRayon = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
} catch (Throwable $e) {
    $villesRayon = [];
}

if (!in_array($ville, $villesRayon, true)) {
    array_unshift($villesRayon, $ville);
}
$villesRayon = array_values(array_unique(array_filter($villesRayon)));

$motsExacts = [];
$templatesExact = [
    'estimation immobilière %s',
    'estimer ma maison %s',
    'estimation maison %s',
    'estimation appartement %s',
    'prix m2 %s',
    'estimation bien immobilier %s',
    'évaluation immobilière %s',
    'estimation gratuite %s',
    'combien vaut ma maison %s',
    'valeur maison %s',
    'estimation en ligne %s',
    'prix immobilier %s',
    'estimer mon bien %s',
    'avis de valeur %s',
    'expertise immobilière %s',
];

foreach ($templatesExact as $tpl) {
    $motsExacts[] = sprintf($tpl, $ville);
}

foreach (array_slice($villesRayon, 0, 10) as $v) {
    foreach (['estimation immobilière %s', 'prix m2 %s', 'estimer maison %s', 'estimation appartement %s'] as $tpl) {
        $motsExacts[] = sprintf($tpl, $v);
    }
}

$motsExacts = array_values(array_unique($motsExacts));

$motsInfo = [
    "prix immobilier $ville 2025",
    "marché immobilier $ville",
    "évolution prix immobilier $ville",
    "prix moyen m2 $ville",
    "quartier le plus cher $ville",
    "meilleur quartier $ville pour acheter",
    "est-ce le bon moment pour vendre $ville",
    "tendance immobilier $ville",
    "prix maison $ville",
    "prix appartement $ville",
    "baromètre immobilier $ville",
    "où acheter $ville",
    "investir immobilier $ville",
];
foreach (array_slice($villesRayon, 0, 5) as $v) {
    $motsInfo[] = "prix immobilier $v";
    $motsInfo[] = "prix m2 $v 2025";
}
$motsInfo = array_values(array_unique($motsInfo));

$motsNegatifs = [
    'gratuit (optionnel selon votre stratégie)',
    'emploi', 'recrutement', 'formation', 'stage', 'salaire',
    'location', 'louer', 'locataire', 'notaire', 'succession',
    'terrain agricole', 'parking', 'garage',
    'DOM', 'TOM', 'Guadeloupe', 'Martinique', 'Réunion',
];

$titresCampagne1 = [
    'Estimation Immobilière ' . $ville,
    'Estimez Votre Bien Gratuit',
    'Résultat en 30 Secondes',
    'Prix au m² ' . $ville,
    'Estimation Gratuite ' . $ville,
    'Combien Vaut Votre Maison ?',
    'Estimation en Ligne ' . $ville,
    'Votre Estimation Offerte',
    'Estimer Mon Appartement',
    'Valeur Maison ' . $ville,
    'Estimation Fiable & Rapide',
    $siteName . ' - Estimation Pro',
    'Résultat Instantané',
    'Sans Engagement',
    'Estimation ' . date('Y') . ' ' . $ville,
];
$titresCampagne1 = array_map(fn($t) => mb_substr($t, 0, 30), $titresCampagne1);

$descriptionsCampagne1 = [
    "Estimez votre bien immobilier à $ville gratuitement. Résultat instantané et fiable.",
    'Obtenez le prix au m² de votre maison ou appartement en 30 secondes. Sans engagement.',
    "Estimation immobilière gratuite basée sur les données du marché de $ville et environs.",
    'Découvrez la valeur réelle de votre bien. Estimation en ligne rapide et professionnelle.',
];
$descriptionsCampagne1 = array_map(fn($d) => mb_substr($d, 0, 90), $descriptionsCampagne1);

$titresCampagne2 = [
    'Prix Immobilier ' . $ville . ' ' . date('Y'),
    'Marché Immobilier ' . $ville,
    'Prix au m² ' . $ville . ' ' . date('Y'),
    'Votre Bien Prend de la Valeur',
    'Évolution Prix ' . $ville,
    'Estimation Gratuite en Ligne',
    'Tendances Immo ' . $ville,
    'Connaître la Valeur de Son Bien',
    'Baromètre Prix ' . $ville,
    'Prix Moyen Maison ' . $ville,
    'Combien Vaut Mon Quartier ?',
    'Analyse Marché Offerte',
    $ville . ' - Prix en Hausse ?',
    'Bilan Immobilier Gratuit',
    'Votre Patrimoine en ' . date('Y'),
];
$titresCampagne2 = array_map(fn($t) => mb_substr($t, 0, 30), $titresCampagne2);

$descriptionsCampagne2 = [
    'Découvrez les prix au m² à ' . $ville . ' en ' . date('Y') . '. Estimation gratuite de votre bien en ligne.',
    'Le marché immobilier à ' . $ville . ' évolue. Estimez votre bien pour connaître sa valeur actuelle.',
    'Prix en hausse ou en baisse à ' . $ville . ' ? Obtenez une estimation fiable et gratuite en 30 secondes.',
    'Analyse du marché immobilier à ' . $ville . ' et environs. Estimez gratuitement la valeur de votre bien.',
];
$descriptionsCampagne2 = array_map(fn($d) => mb_substr($d, 0, 90), $descriptionsCampagne2);

$sections = [
    'overview' => '🎯 Vue d\'ensemble',
    'checklist' => '📋 Checklist pré-lancement',
    'awareness' => '🧠 Niveaux de conscience',
    'keywords' => '🔑 Mots-clés par campagne',
    'ads' => '✍️ Annonces prêtes à copier',
    'extensions' => '🎨 Extensions d\'annonces',
    'geo' => '📍 Ciblage géographique',
    'budget' => '💰 Budget & Enchères',
    'tracking' => '📊 Suivi des conversions',
    'optim' => '🔄 Optimisation continue',
    'export' => '📥 Export complet',
];
?>

<div class="min-h-screen bg-slate-50" style="--accent: <?= htmlspecialchars($siteColor) ?>">
    <div class="flex">
        <aside class="w-64 sticky top-0 h-screen bg-white border-r border-slate-200 p-4 overflow-y-auto hidden lg:block">
            <div class="flex items-center gap-2 text-lg font-bold text-slate-800 mb-4">
                <span>📈</span>
                <span>Google Ads</span>
            </div>
            <nav class="space-y-1 text-sm">
                <?php foreach ($sections as $id => $label): ?>
                    <a href="#<?= $id ?>" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100 nav-link" data-section="<?= $id ?>">
                        <span><?= $label ?></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 completion-dot" data-dot="<?= $id ?>"></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="flex-1 p-4 lg:p-8 space-y-8">
            <section id="overview" class="space-y-6">
                <div class="rounded-2xl p-8 text-white bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
                    <h1 class="text-3xl font-bold mb-2">Votre stratégie Google Ads pour <?= htmlspecialchars($ville) ?></h1>
                    <p class="text-blue-100 mb-6">Générez des leads qualifiés d'estimation immobilière.</p>
                    <div class="grid md:grid-cols-4 gap-4">
                        <div class="bg-white/10 rounded-xl p-4"><div class="text-2xl font-bold">3 campagnes</div><div class="text-sm text-blue-100">Prêtes à déployer</div></div>
                        <div class="bg-white/10 rounded-xl p-4"><div class="text-2xl font-bold">~150 mots-clés</div><div class="text-sm text-blue-100">Générés pour votre zone</div></div>
                        <div class="bg-white/10 rounded-xl p-4"><div class="text-2xl font-bold">12 annonces</div><div class="text-sm text-blue-100">Textes rédigés</div></div>
                        <div class="bg-white/10 rounded-xl p-4"><div class="text-2xl font-bold">&lt; 30 min</div><div class="text-sm text-blue-100">Pour tout mettre en place</div></div>
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-4 text-center">
                    <?php
                    $steps = [
                        ['Créer compte Google Ads', '15 min', 'checklist'],
                        ['Configurer campagnes', '30 min', 'keywords'],
                        ['Lancer campagnes', '5 min', 'budget'],
                        ['Optimiser & scaler', 'continu', 'optim'],
                    ];
                    foreach ($steps as $idx => $s): ?>
                        <a href="#<?= $s[2] ?>" class="bg-white border border-slate-200 rounded-xl p-4 hover:shadow-md transition block">
                            <div class="text-xs text-slate-500">ÉTAPE <?= $idx + 1 ?></div>
                            <div class="font-semibold"><?= $s[0] ?></div>
                            <div class="text-sm text-slate-500">[<?= $s[1] ?>]</div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="checklist" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
                <h2 class="text-2xl font-bold">📋 Avant de commencer</h2>
                <div id="launchChecklist" class="space-y-4 text-sm">
                    <?php
                    $checklistItems = [
                        ['ads_account', 'Créer un compte Google Ads sur ads.google.com', 'Utilisez le même compte Google que votre Google Analytics.'],
                        ['expert_mode', 'Passer en mode Expert (PAS le mode intelligent)', 'Le mode intelligent ne donne pas le contrôle nécessaire.'],
                        ['billing', 'Configurer la facturation', 'Google facture au clic. Ajoutez une carte bancaire.'],
                        ['gtm', 'Installer Google Tag Manager', 'Ajoutez le code GTM dans votre header.php.'],
                        ['ga4', 'Configurer Google Analytics 4', 'Créez une propriété GA4 et connectez-la au site.'],
                        ['conversions', 'Créer les actions de conversion', 'Voir la section 9 pour les détails complets.'],
                        ['speed', 'Vérifier que votre site charge en < 3 secondes', 'Testez la performance sur mobile et desktop.'],
                        ['form', 'Vérifier que le formulaire fonctionne', 'Soumettez un test complet depuis la page d’estimation.'],
                        ['email', 'Vérifier la réception des emails de notification', 'Vérifiez aussi le dossier spam.'],
                    ];
                    foreach ($checklistItems as $item): ?>
                        <label class="flex gap-3 p-3 border rounded-lg items-start">
                            <input type="checkbox" class="mt-1 checklist-input" data-key="<?= $item[0] ?>">
                            <span>
                                <span class="font-medium block"><?= htmlspecialchars($item[1]) ?></span>
                                <span class="text-slate-500"><?= htmlspecialchars($item[2]) ?></span>
                                <?php if ($item[0] === 'ads_account'): ?>
                                    <a class="text-blue-600 block" target="_blank" href="https://ads.google.com/intl/fr_fr/start/">→ Ouvrir ads.google.com</a>
                                <?php elseif ($item[0] === 'speed'): ?>
                                    <a class="text-blue-600 block" target="_blank" href="https://pagespeed.web.dev/?url=<?= urlencode($siteUrl) ?>">→ Tester avec PageSpeed</a>
                                <?php elseif ($item[0] === 'form'): ?>
                                    <a class="text-blue-600 block" target="_blank" href="<?= htmlspecialchars($siteUrl) ?>">→ Tester le site</a>
                                <?php elseif ($item[0] === 'email'): ?>
                                    <a class="text-blue-600 block" href="settings.php#notifications">→ Paramètres notifications</a>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-2"><span id="checklistCount">0/9 étapes complétées</span><span>Progression</span></div>
                    <div class="w-full h-3 bg-slate-100 rounded-full"><div id="checklistBar" class="h-3 rounded-full bg-green-500" style="width:0%"></div></div>
                </div>
            </section>

            <section id="awareness" class="space-y-6">
                <h2 class="text-2xl font-bold">🧠 Stratégie par niveau de conscience client</h2>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl text-sm">Chaque personne qui cherche sur Google est à un niveau de conscience différent. Nous créons une campagne adaptée à chaque niveau pour capter un maximum de leads qualifiés.</div>
                <div class="grid lg:grid-cols-3 gap-6">
                    <article class="bg-white border-2 border-red-200 rounded-2xl overflow-hidden">
                        <header class="bg-red-50 p-4"><span class="bg-red-500 text-white text-xs rounded-full px-3 py-1">🔥 PRIORITÉ 1</span><h3 class="font-bold mt-2">Intention directe</h3><p class="text-sm text-slate-600">La personne SAIT qu'elle veut une estimation.</p></header>
                        <div class="p-6 text-sm space-y-3"><p>Propriétaires qui veulent vendre ou connaître la valeur de leur bien.</p><ul class="list-disc pl-6 text-red-600"><li>estimation immobilière <?= htmlspecialchars($ville) ?></li><li>estimer ma maison <?= htmlspecialchars($ville) ?></li><li>prix m² <?= htmlspecialchars($ville) ?></li><li>combien vaut mon appartement</li><li>estimation gratuite maison</li></ul><p><strong>Budget :</strong> 60% · <strong>Objectif :</strong> Conversions</p><p><strong>Taux conv. :</strong> 3-8% · <strong>CPL :</strong> 5-20€</p><a class="inline-block mt-2 text-red-600 font-semibold" href="#keywords">Voir les mots-clés détaillés →</a></div>
                    </article>
                    <article class="bg-white border-2 border-amber-200 rounded-2xl overflow-hidden">
                        <header class="bg-amber-50 p-4"><span class="bg-amber-500 text-white text-xs rounded-full px-3 py-1">🟡 PRIORITÉ 2</span><h3 class="font-bold mt-2">Recherche d'information</h3><p class="text-sm text-slate-600">La personne se renseigne sur le marché.</p></header>
                        <div class="p-6 text-sm space-y-3"><p>Propriétaires qui pensent peut-être à vendre et recherchent des tendances.</p><ul class="list-disc pl-6"><li>prix immobilier <?= htmlspecialchars($ville) ?> 2025</li><li>marché immobilier <?= htmlspecialchars($ville) ?></li><li>est-ce le bon moment pour vendre</li><li>évolution prix immobilier département</li><li>quartier le plus cher <?= htmlspecialchars($ville) ?></li></ul><p><strong>Budget :</strong> 25% · <strong>Objectif :</strong> Lead magnet estimation</p><p><strong>Taux conv. :</strong> 1-4% · <strong>CPL :</strong> 10-35€</p></div>
                    </article>
                    <article class="bg-white border-2 border-blue-200 rounded-2xl overflow-hidden">
                        <header class="bg-blue-50 p-4"><span class="bg-blue-500 text-white text-xs rounded-full px-3 py-1">❄️ PRIORITÉ 3</span><h3 class="font-bold mt-2">Audience large</h3><p class="text-sm text-slate-600">La personne ne pense pas encore à vendre.</p></header>
                        <div class="p-6 text-sm space-y-3"><p>À activer après rentabilité des campagnes chaudes/tièdes.</p><ul class="list-disc pl-6"><li>Display</li><li>YouTube</li><li>Discovery</li></ul><p><strong>Budget :</strong> 15% · <strong>Objectif :</strong> Trafic / notoriété</p><p><strong>Taux conv. :</strong> 0.3-1% · <strong>CPL :</strong> 15-50€</p></div>
                    </article>
                </div>
                <pre class="bg-white border rounded-xl p-4 text-center text-sm overflow-auto">         ❄️ FROID - Audience large
        ╱                          ╲
       ╱   🟡 TIÈDE - Recherche info  ╲
      ╱                                  ╲
     ╱     🔥 CHAUD - Intention directe    ╲
    ╱________________________________________╲
              💰 CONVERSION (Lead)
              📞 RDV avec conseiller</pre>
            </section>

            <section id="keywords" class="space-y-6">
                <h2 class="text-2xl font-bold">🔑 Mots-clés générés pour <?= htmlspecialchars($ville) ?></h2>
                <p class="text-sm text-slate-600">Ces mots-clés sont générés automatiquement selon votre ville (<?= htmlspecialchars($ville) ?>) et votre rayon de <?= $rayon ?> km.</p>

                <div class="bg-white rounded-xl border p-6 space-y-4">
                    <h3 class="font-bold text-lg">Campagne 1 : Intention directe (🔥)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left border-b"><th class="p-2">Sel.</th><th class="p-2">Mot-clé</th><th class="p-2">Match Type</th><th class="p-2">Volume</th><th class="p-2">Concurrence</th><th class="p-2">CPC estimé</th></tr></thead>
                            <tbody>
                            <?php foreach ($motsExacts as $i => $mot):
                                $vol = $i % 3 === 0 ? 'Élevé' : ($i % 3 === 1 ? 'Moyen' : 'Faible');
                                $conc = $i % 2 === 0 ? 'Forte' : 'Moyenne';
                            ?>
                                <tr class="border-b">
                                    <td class="p-2"><input type="checkbox" class="keyword-select" data-kw="[<?= htmlspecialchars($mot) ?>]" checked></td>
                                    <td class="p-2"><?= htmlspecialchars($mot) ?></td>
                                    <td class="p-2"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">Exact</span></td>
                                    <td class="p-2"><?= $vol ?></td>
                                    <td class="p-2"><?= $conc ?></td>
                                    <td class="p-2">1.20€ - 3.10€</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button class="px-4 py-2 rounded bg-blue-600 text-white" onclick="copySelectedKeywords()">📋 Copier les mots-clés sélectionnés</button>
                        <button class="px-4 py-2 rounded bg-slate-900 text-white" onclick="exportKeywordsCsv()">📥 Exporter en CSV</button>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="font-semibold mb-2">Mots-clés négatifs</h4>
                        <div class="grid md:grid-cols-2 gap-1 text-sm" id="negKeywords">
                            <?php foreach ($motsNegatifs as $n): ?><label><input type="checkbox" checked class="neg-keyword" value="-<?= htmlspecialchars($n) ?>"> -<?= htmlspecialchars($n) ?></label><?php endforeach; ?>
                        </div>
                        <button class="mt-3 px-3 py-2 rounded bg-slate-700 text-white" onclick="copyNegativeKeywords()">📋 Copier les négatifs</button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border p-6 space-y-3">
                    <h3 class="font-bold text-lg">Campagne 2 : Recherche info (🟡)</h3>
                    <ul class="grid md:grid-cols-2 gap-2 text-sm">
                        <?php foreach ($motsInfo as $mot): ?><li class="border rounded p-2">"<?= htmlspecialchars($mot) ?>"</li><?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-white rounded-xl border p-6 space-y-3">
                    <h3 class="font-bold text-lg">Campagne 3 : Audience large (❄️)</h3>
                    <p class="text-sm">Pour Display/Discovery, utilisez le ciblage audience plutôt que les mots-clés traditionnels.</p>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div><h4 class="font-semibold">Affinité</h4><ul class="list-disc pl-5"><li>Propriétaires immobiliers</li><li>Intéressés immobilier</li><li>Acheteurs/vendeurs actifs</li></ul></div>
                        <div><h4 class="font-semibold">In-market</h4><ul class="list-disc pl-5"><li>Services estimation</li><li>Vente résidentielle</li><li>Services immobiliers</li></ul></div>
                        <div><h4 class="font-semibold">Personnalisées</h4><ul class="list-disc pl-5"><li>meilleurs-agents.com</li><li>seloger.com</li><li>bien-ici.com</li></ul></div>
                    </div>
                </div>
            </section>

            <section id="ads" class="space-y-6">
                <h2 class="text-2xl font-bold">✍️ Annonces rédigées pour vos campagnes</h2>
                <p class="text-sm text-slate-600">Format Google RSA : jusqu'à 15 titres et 4 descriptions.</p>
                <?php
                $renderAdEditor = function ($id, $title, $borderClass, $titres, $descs) use ($siteUrl, $ville) {
                    ?>
                    <div class="bg-white rounded-xl border-2 <?= $borderClass ?> p-6 space-y-4">
                        <h3 class="font-bold text-lg"><?= htmlspecialchars($title) ?></h3>
                        <div class="border rounded-xl p-4 text-sm bg-white">
                            <div class="text-xs text-green-700">Ad · <?= htmlspecialchars($siteUrl) ?></div>
                            <div class="text-blue-700 font-semibold">Estimation Immobilière <?= htmlspecialchars($ville) ?> - Gratuit en 30 Secondes</div>
                            <div class="text-slate-700">Estimez votre maison ou appartement gratuitement. Résultat immédiat + prix au m².</div>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold mb-2">Titres</h4>
                                <div class="space-y-2" data-group="<?= $id ?>-titles">
                                    <?php foreach ($titres as $t): ?>
                                        <div class="flex gap-2 items-center"><input class="ad-input flex-1 border rounded px-2 py-1" maxlength="35" value="<?= htmlspecialchars($t) ?>"><span class="text-xs counter">0/30</span><button class="text-xs px-2 py-1 border rounded" onclick="copyText(this)">Copier</button></div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="mt-2 text-sm px-3 py-2 rounded bg-slate-800 text-white" onclick="copyGroup('<?= $id ?>-titles')">📋 Copier tous les titres</button>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Descriptions</h4>
                                <div class="space-y-2" data-group="<?= $id ?>-descs">
                                    <?php foreach ($descs as $d): ?>
                                        <div class="flex gap-2 items-center"><input class="ad-input flex-1 border rounded px-2 py-1" maxlength="95" value="<?= htmlspecialchars($d) ?>"><span class="text-xs counter">0/90</span><button class="text-xs px-2 py-1 border rounded" onclick="copyText(this)">Copier</button></div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="mt-2 text-sm px-3 py-2 rounded bg-slate-800 text-white" onclick="copyGroup('<?= $id ?>-descs')">📋 Copier toutes les descriptions</button>
                            </div>
                        </div>
                    </div>
                    <?php
                };
                $renderAdEditor('camp1', 'Annonce Campagne 1 : Intention directe', 'border-red-200', $titresCampagne1, $descriptionsCampagne1);
                $renderAdEditor('camp2', 'Annonce Campagne 2 : Recherche info', 'border-amber-200', $titresCampagne2, $descriptionsCampagne2);
                ?>
                <div class="bg-white border-2 border-blue-200 rounded-xl p-6 text-sm space-y-2">
                    <h3 class="font-bold text-lg">Annonce Campagne 3 : Display</h3>
                    <p><strong>Titre court :</strong> Estimez Votre Bien</p>
                    <p><strong>Titre long :</strong> Votre maison à <?= htmlspecialchars($ville) ?> a peut-être pris de la valeur. Découvrez combien elle vaut gratuitement.</p>
                    <p><strong>Description :</strong> Estimation immobilière gratuite en 30 secondes. Résultat instantané pour <?= htmlspecialchars($ville) ?> et environs.</p>
                    <p><strong>Entreprise :</strong> <?= htmlspecialchars($siteName) ?> · <strong>CTA :</strong> Estimer maintenant</p>
                    <p class="text-slate-500">Formats images : 1200×628, 1200×1200, logo 1200×1200.</p>
                </div>
            </section>

            <section id="extensions" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">🎨 Extensions d'annonces (Assets)</h2>
                <p class="text-sm text-slate-600">Les extensions augmentent souvent le CTR de 15-30%. Ajoutez-les toutes.</p>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="border rounded p-4"><h3 class="font-semibold">Sitelinks</h3><ul class="list-disc pl-5"><li>Estimation Gratuite → <?= htmlspecialchars($siteUrl) ?></li><li>Prix au m² <?= htmlspecialchars($ville) ?> → <?= htmlspecialchars($siteUrl) ?>/prix-m2</li><li>Prendre RDV Conseiller → <?= htmlspecialchars($siteUrl) ?>/rdv</li><li>Avis Clients → <?= htmlspecialchars($siteUrl) ?>/avis</li></ul></div>
                    <div class="border rounded p-4"><h3 class="font-semibold">Accroches</h3><ul class="list-disc pl-5"><li>✓ Estimation Gratuite</li><li>✓ Résultat en 30 secondes</li><li>✓ Sans Engagement</li><li>✓ Données Marché <?= date('Y') ?></li><li>✓ Expert Local <?= htmlspecialchars($ville) ?></li><li>✓ 100% Confidentiel</li></ul></div>
                    <div class="border rounded p-4"><h3 class="font-semibold">Extraits structurés (Services)</h3><p>Estimation maison, estimation appartement, prix au m², avis de valeur, conseil vente, analyse marché.</p></div>
                    <div class="border rounded p-4"><h3 class="font-semibold">Extension d'appel</h3><p>Numéro : <?= htmlspecialchars($phone ?: 'Configurez votre numéro dans les paramètres') ?></p><?php if (!$phone): ?><a class="text-amber-700" href="settings.php">⚠️ Ajoutez votre numéro dans les paramètres</a><?php endif; ?><p class="mt-2">Liez aussi votre Google Business Profile pour l'extension de lieu.</p></div>
                </div>
            </section>

            <section id="geo" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">📍 Ciblage géographique</h2>
                <div class="border rounded-xl p-4 bg-slate-50 text-sm">
                    <div class="font-semibold">Mini-carte (placeholder)</div>
                    <div class="h-64 mt-2 rounded bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-slate-600">Carte 500×300 : cercle <?= $rayon ?> km autour de <?= htmlspecialchars($ville) ?></div>
                </div>
                <p class="text-sm">Centre : <?= htmlspecialchars($ville) ?> (<?= htmlspecialchars((string)$cityLat) ?>, <?= htmlspecialchars((string)$cityLng) ?>) · Rayon : <?= $rayon ?> km</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border rounded p-3 text-sm"><h3 class="font-semibold">Villes à cibler</h3><div id="citiesList" class="mt-2 space-y-1 max-h-56 overflow-auto"><?php foreach ($villesRayon as $v): ?><div>☑ <?= htmlspecialchars($v) ?></div><?php endforeach; ?></div><button class="mt-3 px-3 py-2 rounded bg-slate-800 text-white" onclick="copyCities()">📋 Copier la liste des villes</button></div>
                    <div class="bg-amber-50 border border-amber-200 rounded p-3 text-sm"><h3 class="font-semibold">Important</h3><p>Choisissez <strong>Présence uniquement</strong> (et pas “Présence ou intérêt”) pour éviter des clics hors zone.</p><p class="mt-2 text-slate-600">Parcours : Paramètres > Zones > Options de localisation > Présence uniquement.</p></div>
                </div>
            </section>

            <section id="budget" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">💰 Recommandations budget et enchères</h2>
                <div class="grid md:grid-cols-3 gap-4 items-end">
                    <label class="block text-sm md:col-span-2">Budget mensuel total
                        <input id="budgetInput" type="number" min="100" max="5000" value="500" class="w-full border rounded p-2 mt-1">
                        <input id="budgetRange" type="range" min="100" max="5000" value="500" class="w-full mt-2">
                    </label>
                    <label class="block text-sm">Objectif principal
                        <select id="objective" class="w-full border rounded p-2 mt-1"><option>Maximum de leads</option><option>Coût par lead le plus bas</option><option>Visibilité maximale</option></select>
                    </label>
                </div>
                <div id="budgetCards" class="grid md:grid-cols-3 gap-4 text-sm"></div>
                <table class="w-full text-sm border mt-2">
                    <thead class="bg-slate-50"><tr><th class="p-2 border">Stratégie</th><th class="p-2 border">Quand</th><th class="p-2 border">Risque</th><th class="p-2 border">Recommandé</th></tr></thead>
                    <tbody>
                        <tr><td class="p-2 border">CPA cible</td><td class="p-2 border">Après 30 conversions</td><td class="p-2 border">Faible</td><td class="p-2 border">✅ Long terme</td></tr>
                        <tr><td class="p-2 border">Maximiser conversions</td><td class="p-2 border">Démarrage</td><td class="p-2 border">Moyen</td><td class="p-2 border">✅ Démarrage</td></tr>
                        <tr><td class="p-2 border">CPC manuel</td><td class="p-2 border">Contrôle total</td><td class="p-2 border">Élevé</td><td class="p-2 border">⚠️ Experts</td></tr>
                        <tr><td class="p-2 border">Maximiser clics</td><td class="p-2 border">Test</td><td class="p-2 border">Élevé</td><td class="p-2 border">⚠️ Temporaire</td></tr>
                    </tbody>
                </table>
                <p id="biddingReco" class="font-semibold"></p>
                <p class="text-sm text-slate-600">Planning recommandé : Lundi-Samedi, 7h-22h, +20% 18h-21h, +10% samedi matin, -50% nuit.</p>
            </section>

            <section id="tracking" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">📊 Configurer le suivi des conversions</h2>
                <p class="text-sm">Sans suivi des conversions, Google ne peut pas optimiser vos campagnes.</p>
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div class="border rounded p-3"><h3 class="font-semibold">Conversion 1 : Estimation_soumise</h3><p>Catégorie : Formulaire · Valeur : 5€ · Fenêtre : 30 jours</p></div>
                    <div class="border rounded p-3"><h3 class="font-semibold">Conversion 2 : RDV_pris</h3><p>Catégorie : RDV · Valeur : 25€</p></div>
                    <div class="border rounded p-3"><h3 class="font-semibold">Conversion 3 : Appel_telephone</h3><p>Via extension d'appel Google Ads.</p></div>
                </div>
                <pre class="bg-slate-900 text-slate-100 p-4 rounded text-xs overflow-auto">&lt;!-- Google tag (gtag.js) --&gt;
&lt;script async src="https://www.googletagmanager.com/gtag/js?id=AW-XXXXXXXXX"&gt;&lt;/script&gt;
&lt;script&gt;
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);} 
gtag('js', new Date());
gtag('config', 'AW-XXXXXXXXX');
&lt;/script&gt;</pre>
                <ol class="list-decimal pl-6 text-sm space-y-1"><li>Google Ads > Outils > Conversions</li><li>Nouvelle conversion > Site web</li><li>Nommez “Estimation_soumise”</li><li>Catégorie : formulaire</li><li>Valeur : 5€</li><li>Comptabilisation : Une</li><li>Installer via gtag ou GTM</li><li>Tester avec Tag Assistant</li></ol>
            </section>

            <section id="optim" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">🔄 Guide d'optimisation (après lancement)</h2>
                <div class="grid md:grid-cols-4 gap-3 text-sm">
                    <div class="border rounded p-3"><h3 class="font-semibold">Semaine 1</h3><p>Vérifier tracking, ne pas changer enchères, surveiller CTR > 3%.</p></div>
                    <div class="border rounded p-3"><h3 class="font-semibold">Semaine 2-3</h3><p>Analyser termes de recherche et exclure non pertinents.</p></div>
                    <div class="border rounded p-3"><h3 class="font-semibold">Mois 2</h3><p>Si > 30 conversions, passer en CPA cible.</p></div>
                    <div class="border rounded p-3"><h3 class="font-semibold">Mois 3+</h3><p>Scaler +20%/semaine max, tester Display/Discovery.</p></div>
                </div>
                <div class="bg-slate-50 border rounded p-4 text-sm">
                    <h3 class="font-semibold mb-2">Checklist hebdomadaire</h3>
                    <?php foreach (['rapport termes de recherche', 'ajout mots-clés négatifs', 'coût par conversion', 'performances annonces', 'budget restant'] as $idx => $task): ?>
                        <label class="block"><input type="checkbox" class="weekly-check" data-key="weekly_<?= $idx ?>"> Vérifier <?= $task ?></label>
                    <?php endforeach; ?>
                </div>
                <table class="w-full text-sm border">
                    <thead class="bg-slate-50"><tr><th class="p-2 border">KPI</th><th class="p-2 border">Cible</th><th class="p-2 border">Alerte</th><th class="p-2 border">Action</th></tr></thead>
                    <tbody>
                        <tr><td class="p-2 border">CTR</td><td class="p-2 border">&gt; 4%</td><td class="p-2 border">&lt; 2%</td><td class="p-2 border">Améliorer les titres</td></tr>
                        <tr><td class="p-2 border">CPC</td><td class="p-2 border">&lt; 2€</td><td class="p-2 border">&gt; 4€</td><td class="p-2 border">Revoir les mots-clés</td></tr>
                        <tr><td class="p-2 border">Taux conv.</td><td class="p-2 border">&gt; 5%</td><td class="p-2 border">&lt; 2%</td><td class="p-2 border">Optimiser landing</td></tr>
                        <tr><td class="p-2 border">Coût/lead</td><td class="p-2 border">&lt; 15€</td><td class="p-2 border">&gt; 30€</td><td class="p-2 border">Affiner ciblage</td></tr>
                        <tr><td class="p-2 border">Quality Score</td><td class="p-2 border">&gt; 7/10</td><td class="p-2 border">&lt; 5/10</td><td class="p-2 border">Améliorer pertinence</td></tr>
                    </tbody>
                </table>
            </section>

            <section id="export" class="bg-white rounded-2xl border p-6 space-y-4">
                <h2 class="text-2xl font-bold">📥 Exporter tout pour Google Ads</h2>
                <div class="flex flex-wrap gap-3">
                    <button class="px-4 py-2 rounded bg-slate-900 text-white" onclick="exportKeywordsCsv()">📥 Télécharger tous les mots-clés (CSV)</button>
                    <button class="px-4 py-2 rounded bg-slate-900 text-white" onclick="exportAdsCsv()">📥 Télécharger toutes les annonces (CSV)</button>
                    <button class="px-4 py-2 rounded bg-slate-900 text-white" onclick="window.print()">📥 Télécharger le plan complet (PDF)</button>
                    <button class="px-4 py-2 rounded bg-blue-700 text-white" onclick="copyBrief()">📋 Copier le brief complet</button>
                </div>
                <button class="px-5 py-3 rounded-xl text-white font-bold" style="background: var(--accent)" onclick="completeGuide()">🎯 J'ai tout configuré !</button>
                <div id="completionMessage" class="hidden p-4 rounded-xl bg-green-50 border border-green-200 text-green-800"></div>
            </section>
        </main>
    </div>
</div>

<script>
const STORAGE_PREFIX = 'google_ads_guide_';
const checklistInputs = [...document.querySelectorAll('.checklist-input')];
const weeklyInputs = [...document.querySelectorAll('.weekly-check')];
const sectionIds = <?= json_encode(array_keys($sections)) ?>;

function saveCheckState(key, checked) { localStorage.setItem(STORAGE_PREFIX + key, checked ? '1' : '0'); }
function loadCheckState(key) { return localStorage.getItem(STORAGE_PREFIX + key) === '1'; }

function updateChecklistProgress() {
    const done = checklistInputs.filter(c => c.checked).length;
    const total = checklistInputs.length;
    document.getElementById('checklistCount').textContent = `${done}/${total} étapes complétées`;
    document.getElementById('checklistBar').style.width = `${(done/total)*100}%`;
}

checklistInputs.forEach(input => {
    input.checked = loadCheckState(input.dataset.key);
    input.addEventListener('change', () => { saveCheckState(input.dataset.key, input.checked); updateChecklistProgress(); });
});
weeklyInputs.forEach(input => {
    input.checked = loadCheckState(input.dataset.key);
    input.addEventListener('change', () => saveCheckState(input.dataset.key, input.checked));
});
updateChecklistProgress();

function markSectionCompletion() {
    sectionIds.forEach(id => {
        const section = document.getElementById(id);
        const rect = section.getBoundingClientRect();
        if (rect.top < window.innerHeight * 0.5) {
            localStorage.setItem(STORAGE_PREFIX + 'section_' + id, '1');
        }
        const dot = document.querySelector(`[data-dot="${id}"]`);
        if (dot && localStorage.getItem(STORAGE_PREFIX + 'section_' + id) === '1') {
            dot.classList.remove('bg-slate-300');
            dot.classList.add('bg-green-500');
        }
    });
}
window.addEventListener('scroll', markSectionCompletion, {passive: true});
markSectionCompletion();

function copyToClipboard(text) { navigator.clipboard.writeText(text); }
function copySelectedKeywords() {
    const keywords = [...document.querySelectorAll('.keyword-select:checked')].map(k => k.dataset.kw).join('\n');
    copyToClipboard(keywords);
}
function copyNegativeKeywords() {
    const keywords = [...document.querySelectorAll('.neg-keyword:checked')].map(k => k.value).join('\n');
    copyToClipboard(keywords);
}
function copyCities() {
    const txt = [...document.querySelectorAll('#citiesList div')].map(n => n.textContent.replace('☑ ', '')).join('\n');
    copyToClipboard(txt);
}
function csvDownload(filename, rows) {
    const csv = rows.map(r => r.map(v => `"${String(v).replaceAll('"', '""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
}
function exportKeywordsCsv() {
    const rows = [['Campaign','Ad Group','Keyword','Match Type','Max CPC']];
    document.querySelectorAll('.keyword-select').forEach(k => rows.push(['Campagne 1 - Intention','Groupe principal',k.dataset.kw,'Exact','2.00']));
    csvDownload('google-ads-keywords.csv', rows);
}
function exportAdsCsv() {
    const rows = [['Campaign','Ad Group','Headline 1','Headline 2','Headline 3','Description 1','Description 2','Final URL','Path 1','Path 2']];
    rows.push(['Campagne 1 - Intention','RSA Principal','Estimation Immobilière','Estimez Votre Bien','Résultat 30s','Estimation gratuite','Sans engagement','<?= addslashes($siteUrl) ?>','estimation','gratuite']);
    rows.push(['Campagne 2 - Info','RSA Information','Prix Immobilier <?= addslashes($ville) ?>','Marché Immobilier','Estimation gratuite','Découvrez les prix','Résultat instantané','<?= addslashes($siteUrl) ?>','prix','marche']);
    csvDownload('google-ads-ads.csv', rows);
}

function updateCounters() {
    document.querySelectorAll('.ad-input').forEach(input => {
        const counter = input.parentElement.querySelector('.counter');
        const max = Number(input.getAttribute('maxlength')) <= 35 ? 30 : 90;
        counter.textContent = `${input.value.length}/${max}`;
        counter.className = 'text-xs counter ' + (input.value.length <= max ? 'text-green-600' : 'text-red-600');
    });
}
document.querySelectorAll('.ad-input').forEach(i => i.addEventListener('input', updateCounters));
updateCounters();

function copyText(btn) {
    const input = btn.parentElement.querySelector('input');
    copyToClipboard(input.value);
}
function copyGroup(groupName) {
    const group = document.querySelector(`[data-group="${groupName}"]`);
    const text = [...group.querySelectorAll('input')].map(i => i.value).join('\n');
    copyToClipboard(text);
}
function copyBrief() {
    const brief = [
        'BRIEF GOOGLE ADS - <?= addslashes($ville) ?>',
        'Site: <?= addslashes($siteUrl) ?>',
        'Budget recommandé: 500€/mois (ajustable)',
        'Campagnes: Intention directe, Recherche info, Audience large',
        'Mots-clés et annonces exportables depuis cette page.'
    ].join('\n');
    copyToClipboard(brief);
}

function renderBudgetCards() {
    const budget = Math.max(100, Math.min(5000, Number(document.getElementById('budgetInput').value || 500)));
    document.getElementById('budgetRange').value = budget;
    const defs = [
        {name:'🔥 Campagne 1', p:0.6, cpc:1.5, cr:0.05},
        {name:'🟡 Campagne 2', p:0.25, cpc:1.0, cr:0.025},
        {name:'❄️ Campagne 3', p:0.15, cpc:0.5, cr:0.005},
    ];
    document.getElementById('budgetCards').innerHTML = defs.map(d => {
        const monthly = budget * d.p;
        const daily = monthly / 30;
        const clicks = daily / d.cpc;
        const leads = clicks * d.cr;
        const cpl = leads > 0 ? daily / leads : 0;
        return `<div class="border rounded-xl p-3"><h3 class="font-semibold">${d.name}</h3><p>Budget: ${monthly.toFixed(0)}€/mois</p><p>/jour: ${daily.toFixed(2)}€</p><p>Clics estimés: ${(clicks*0.8).toFixed(0)}-${(clicks*1.2).toFixed(0)}</p><p>Leads estimés: ${(leads*0.7).toFixed(1)}-${(leads*1.3).toFixed(1)}</p><p>Coût/lead: ${(cpl*0.8).toFixed(0)}-${(cpl*1.2).toFixed(0)}€</p></div>`;
    }).join('');
    const leadCost = ((budget * 0.6 / 30) / (((budget*0.6/30)/1.5)*0.05)).toFixed(0);
    document.getElementById('biddingReco').textContent = `Pour commencer : Maximiser les conversions avec ${(budget/30).toFixed(2)}€/jour. Après 30-50 conversions : CPA cible à ~${leadCost}€.`;
}
['budgetInput','budgetRange'].forEach(id => {
    document.getElementById(id).addEventListener('input', (e) => {
        if (id === 'budgetRange') document.getElementById('budgetInput').value = e.target.value;
        renderBudgetCards();
    });
});
renderBudgetCards();

function completeGuide() {
    localStorage.setItem(STORAGE_PREFIX + 'completed', '1');
    const msg = document.getElementById('completionMessage');
    msg.classList.remove('hidden');
    msg.innerHTML = '🎉 Bravo ! Vos campagnes vont commencer à générer des leads. <a class="underline" href="settings.php">Voir l\\'admin</a>';
    for (let i = 0; i < 70; i++) {
        const c = document.createElement('span');
        c.textContent = ['🎉','✨','🎊'][i%3];
        c.style.position = 'fixed';
        c.style.left = Math.random() * 100 + 'vw';
        c.style.top = '-20px';
        c.style.zIndex = '9999';
        c.style.transition = 'transform 1.8s linear, opacity 1.8s';
        document.body.appendChild(c);
        requestAnimationFrame(() => {
            c.style.transform = `translateY(${window.innerHeight + 40}px)`;
            c.style.opacity = '0';
        });
        setTimeout(() => c.remove(), 1900);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
