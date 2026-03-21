<?php
$page_title = 'Quartiers de Aix-en-Provence - Estimation Immobilière Aix-en-Provence | Guide Détaillé';

$quartiers = [
    [
        'nom' => 'Centre Historique / Cours Mirabeau',
        'description' => "Coeur vibrant d'Aix-en-Provence avec le célèbre Cours Mirabeau, ses fontaines, hôtels particuliers XVIIe-XVIIIe et terrasses de cafés. Architecture provençale remarquable, commerces haut de gamme.",
        'prix_m2' => 6200,
        'prix_moyen' => 520000,
        'caracteristiques' => ['Historique', 'Cours Mirabeau', 'Fontaines', 'Patrimoine'],
        'population' => '~12000 habitants',
        'transports' => 'Bus Aix en Bus, Centre piéton, Navettes',
        'attractivite' => 'Très haute',
        'coords' => '43.5263,5.4481',
        'tendance' => '+3.8%',
    ],
    [
        'nom' => 'Mazarin',
        'description' => "Quartier aristocratique au sud du Cours Mirabeau. Hôtels particuliers, rues calmes et élégantes, musée Granet. L'un des quartiers les plus prestigieux d'Aix.",
        'prix_m2' => 6500,
        'prix_moyen' => 580000,
        'caracteristiques' => ['Prestige', 'Hôtels particuliers', 'Musée Granet', 'Élégant'],
        'population' => '~6000 habitants',
        'transports' => 'Bus, Piéton, Parking Mignet',
        'attractivite' => 'Très haute',
        'coords' => '43.5240,5.4500',
        'tendance' => '+3.2%',
    ],
    [
        'nom' => 'Jas de Bouffan',
        'description' => "Quartier résidentiel à l'ouest d'Aix, lié à l'histoire de Cézanne. Centre commercial, logements variés et accès rapide à l'autoroute. Bon rapport qualité-prix.",
        'prix_m2' => 4200,
        'prix_moyen' => 380000,
        'caracteristiques' => ['Résidentiel', 'Cézanne', 'Commerces', 'Accessible'],
        'population' => '~15000 habitants',
        'transports' => 'Bus Aix en Bus, Accès A8/A51',
        'attractivite' => 'Haute',
        'coords' => '43.5250,5.4200',
        'tendance' => '+4.5%',
    ],
    [
        'nom' => 'Pont de l\'Arc / Les Milles',
        'description' => "Secteur sud dynamique entre Aix et la zone d'activités des Milles. Résidences récentes, familles et actifs. Proximité du technopôle de l'Arbois et de la gare TGV.",
        'prix_m2' => 4600,
        'prix_moyen' => 420000,
        'caracteristiques' => ['Dynamique', 'Technopôle', 'Gare TGV', 'Familles'],
        'population' => '~18000 habitants',
        'transports' => 'Bus, Gare TGV Aix, Accès autoroute',
        'attractivite' => 'Haute',
        'coords' => '43.5050,5.4300',
        'tendance' => '+5.1%',
    ],
    [
        'nom' => 'Puyricard',
        'description' => "Village provençal au nord d'Aix, réputé pour ses calissons et son cadre champêtre. Bastides, mas et villas avec piscine dans un écrin de verdure. Très prisé des familles aisées.",
        'prix_m2' => 5800,
        'prix_moyen' => 750000,
        'caracteristiques' => ['Village', 'Bastides', 'Calissons', 'Nature'],
        'population' => '~8000 habitants',
        'transports' => 'Voiture, Bus scolaire',
        'attractivite' => 'Très haute',
        'coords' => '43.5650,5.4250',
        'tendance' => '+3.5%',
    ],
    [
        'nom' => 'La Torse / Saint-Jérôme',
        'description' => "Quartier résidentiel prisé à l'est d'Aix. Proximité du parc de la Torse, villas avec vue sur la Sainte-Victoire. Cadre de vie exceptionnel entre ville et campagne.",
        'prix_m2' => 5400,
        'prix_moyen' => 620000,
        'caracteristiques' => ['Résidentiel', 'Sainte-Victoire', 'Parc', 'Villas'],
        'population' => '~10000 habitants',
        'transports' => 'Bus, Pistes cyclables',
        'attractivite' => 'Très haute',
        'coords' => '43.5300,5.4700',
        'tendance' => '+4.2%',
    ],
];
?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="eyebrow">
        <i class="fas fa-map-marked-alt"></i> Quartiers de Aix-en-Provence
      </p>
      <h1>Explorez les quartiers de Aix-en-Provence</h1>
      <p class="lead">
        Comparez les prix au m², les tendances de marché et les points forts de chaque quartier pour affiner votre estimation immobilière.
      </p>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- CARTE INTERACTIVE -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-map-pin"></i> Carte Interactive
      </p>
      <h2>Visualisez les quartiers sur la carte</h2>
    </div>

    <div class="card" style="padding: var(--space-6);">
      <p style="color: var(--text-secondary); margin-bottom: var(--space-4); font-size: var(--size-sm); display: flex; align-items: center; gap: var(--space-2);">
        <i class="fas fa-info-circle"></i> Cliquez sur un quartier pour centrer la carte et découvrir ses caractéristiques.
      </p>

      <div style="display: flex; flex-wrap: wrap; gap: var(--space-3); margin-bottom: var(--space-6);">
        <?php foreach ($quartiers as $index => $quartier): ?>
          <button
            type="button"
            class="btn btn-outline quartier-map-btn"
            data-nom="<?= htmlspecialchars($quartier['nom']); ?>"
            data-coords="<?= htmlspecialchars($quartier['coords']); ?>"
            data-zoom="15"
            data-index="<?= $index; ?>"
          >
            <i class="fas fa-location-dot"></i> <?= htmlspecialchars($quartier['nom']); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <iframe
        id="google-map-quartiers"
        title="Carte des quartiers de Aix-en-Provence"
        src="https://maps.google.com/maps?q=44.8378,-0.5792&z=13&output=embed"
        width="100%"
        height="480"
        style="border: 0; border-radius: var(--radius-xl); display: block;"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- GRILLE QUARTIERS AVEC STATS -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-chart-bar"></i> Détails par Quartier
      </p>
      <h2>Prix et caractéristiques clés</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-6);">
      <?php foreach ($quartiers as $index => $quartier): ?>
        <article class="card quartier-card" data-quartier="<?= htmlspecialchars($quartier['nom']); ?>">
          <!-- En-tête avec prix et tendance -->
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-light);">
            <div style="flex: 1;">
              <h3 style="margin: 0 0 var(--space-1) 0; font-size: var(--size-2xl);"><?= htmlspecialchars($quartier['nom']); ?></h3>
              <p style="margin: 0; font-size: var(--size-sm); color: var(--text-muted);">
                <i class="fas fa-users"></i> <?= htmlspecialchars($quartier['population']); ?>
              </p>
            </div>
            <div style="text-align: right;">
              <div style="background: linear-gradient(135deg, rgba(139, 21, 56, 0.1), rgba(212, 175, 55, 0.08)); border-radius: var(--radius-lg); padding: var(--space-3) var(--space-4);">
                <p style="margin: 0; font-weight: 700; font-size: var(--size-lg); color: var(--primary);">
                  <?= number_format((int) $quartier['prix_m2'], 0, ',', ' '); ?> €/m²
                </p>
                <p style="margin: var(--space-1) 0 0 0; font-size: var(--size-xs); color: var(--text-secondary);">
                  <i class="fas fa-arrow-trend-up"></i> <?= htmlspecialchars($quartier['tendance']); ?>
                </p>
              </div>
            </div>
          </div>

          <!-- Description -->
          <p style="color: var(--text-secondary); font-size: var(--size-sm); margin-bottom: var(--space-4); line-height: var(--line-lg);">
            <?= htmlspecialchars($quartier['description']); ?>
          </p>

          <!-- Prix moyen -->
          <div style="background: var(--bg-alt); border-radius: var(--radius-lg); padding: var(--space-3) var(--space-4); margin-bottom: var(--space-4); border-left: 4px solid var(--accent);">
            <p style="margin: 0; font-size: var(--size-xs); color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Prix moyen estimé</p>
            <p style="margin: var(--space-1) 0 0 0; font-family: var(--font-primary); font-size: var(--size-2xl); font-weight: 800; color: var(--primary);">
              <?= number_format((int) $quartier['prix_moyen'], 0, ',', ' '); ?> €
            </p>
          </div>

          <!-- Caractéristiques -->
          <div style="margin-bottom: var(--space-4);">
            <p style="font-size: var(--size-xs); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: var(--space-2);">
              <i class="fas fa-check-circle"></i> Caractéristiques
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
              <?php foreach ($quartier['caracteristiques'] as $caracteristique): ?>
                <span class="badge badge-primary">
                  <?= htmlspecialchars($caracteristique); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Infos détaillées -->
          <div style="display: grid; gap: var(--space-3); font-size: var(--size-sm); margin-bottom: var(--space-4); padding: var(--space-4) 0; border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
            <div style="display: flex; gap: var(--space-3);">
              <span style="color: var(--primary); font-weight: 600; min-width: 120px;">
                <i class="fas fa-bus"></i> Transports
              </span>
              <span style="color: var(--text-secondary);">
                <?= htmlspecialchars($quartier['transports']); ?>
              </span>
            </div>
            <div style="display: flex; gap: var(--space-3);">
              <span style="color: var(--primary); font-weight: 600; min-width: 120px;">
                <i class="fas fa-star"></i> Attractivité
              </span>
              <span style="color: var(--text-secondary);">
                <?= htmlspecialchars($quartier['attractivite']); ?>
              </span>
            </div>
          </div>

          <!-- CTA Bouton -->
          <a href="/estimation#form-estimation" class="btn btn-primary full-width">
            <i class="fas fa-calculator"></i> Estimer mon bien ici
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- COMPARATIF PRIX -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-chart-line"></i> Comparatif des Prix
      </p>
      <h2>Évolution des prix au m² par quartier</h2>
    </div>

    <div class="card" style="padding: var(--space-8); overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; font-size: var(--size-sm);">
        <thead>
          <tr style="border-bottom: 2px solid var(--border);">
            <th style="padding: var(--space-3) var(--space-4); text-align: left; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
              Quartier
            </th>
            <th style="padding: var(--space-3) var(--space-4); text-align: right; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
              Prix/m²
            </th>
            <th style="padding: var(--space-3) var(--space-4); text-align: right; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
              Prix Moyen
            </th>
            <th style="padding: var(--space-3) var(--space-4); text-align: center; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
              Tendance
            </th>
            <th style="padding: var(--space-3) var(--space-4); text-align: center; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
              Dynamisme
            </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($quartiers as $quartier):
            $prix_m2 = (int) $quartier['prix_m2'];
            $prix_moyen = (int) $quartier['prix_moyen'];
            $tendance = $quartier['tendance'];
            $dynamisme = match(true) {
              str_contains($quartier['attractivite'], 'Très haute') => '★★★★★',
              str_contains($quartier['attractivite'], 'Haute') => '★★★★',
              str_contains($quartier['attractivite'], 'Moyenne à haute') => '★★★★',
              default => '★★★'
            };
          ?>
            <tr style="border-bottom: 1px solid var(--border-light); transition: background var(--trans-fast);" onmouseover="this.style.background='var(--bg-alt)'" onmouseout="this.style.background='transparent'">
              <td style="padding: var(--space-3) var(--space-4); font-weight: 600; color: var(--text);">
                <?= htmlspecialchars($quartier['nom']); ?>
              </td>
              <td style="padding: var(--space-3) var(--space-4); text-align: right; color: var(--primary); font-weight: 700;">
                <?= number_format($prix_m2, 0, ',', ' '); ?> €
              </td>
              <td style="padding: var(--space-3) var(--space-4); text-align: right; color: var(--text-secondary);">
                <?= number_format($prix_moyen, 0, ',', ' '); ?> €
              </td>
              <td style="padding: var(--space-3) var(--space-4); text-align: center;">
                <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: var(--space-2) var(--space-3); border-radius: var(--radius-md); font-weight: 700; font-size: var(--size-xs);">
                  <?= htmlspecialchars($tendance); ?>
                </span>
              </td>
              <td style="padding: var(--space-3) var(--space-4); text-align: center; color: var(--accent); font-weight: 700; font-size: var(--size-sm);">
                <?= $dynamisme; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- GALERIE PHOTOS -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-image"></i> Galerie Visuelle
      </p>
      <h2>Ambiances et paysages de Aix-en-Provence</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--space-4);">
      <!-- Chartrons -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1560969184-10fe8719e047?auto=format&fit=crop&w=500&q=80"
            alt="Quartier des Chartrons à Aix-en-Provence"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-wine-glass-alt"></i> Chartrons
        </figcaption>
      </figure>

      <!-- Saint-Pierre -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Saint-Pierre Aix-en-Provence"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-landmark"></i> Saint-Pierre
        </figcaption>
      </figure>

      <!-- Saint-Michel -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Saint-Michel Aix-en-Provence"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-church"></i> Saint-Michel
        </figcaption>
      </figure>

      <!-- Caudéran -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=500&q=80"
            alt="Quartier résidentiel Caudéran"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-home"></i> Caudéran
        </figcaption>
      </figure>

      <!-- Bastide -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Bastide - rive droite"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-water"></i> Bastide
        </figcaption>
      </figure>

      <!-- Mériadeck -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Mériadeck"
            style="width: 100%; height: 100%; object-fit: cover; transition: transform var(--trans-base);"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
          >
          <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.4)); border-radius: var(--radius-xl);"></div>
        </div>
        <figcaption style="font-weight: 600; color: var(--text); margin-top: var(--space-2); font-size: var(--size-sm);">
          <i class="fas fa-building"></i> Mériadeck
        </figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- FAQ QUARTIERS -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-question-circle"></i> Questions Fréquentes
      </p>
      <h2>Vos questions sur les quartiers</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel est le quartier le plus dynamique ?
        </h3>
        <p>
          La Bastide affiche la tendance la plus forte (+7.3%) grâce aux projets urbains majeurs (Darwin, Euratlantique). Saint-Michel suit avec +6.2% porté par la rénovation du quartier.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel quartier pour une famille ?
        </h3>
        <p>
          Caudéran est le quartier familial par excellence avec ses maisons avec jardin, ses écoles réputées et son ambiance résidentielle calme. Les Chartrons offrent aussi un excellent cadre de vie.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Où trouver le meilleur investissement ?
        </h3>
        <p>
          La Bastide et Saint-Michel combinent des prix encore accessibles avec de fortes perspectives de plus-value grâce aux projets de rénovation urbaine en cours.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel quartier offre le meilleur rapport qualité/prix ?
        </h3>
        <p>
          Mériadeck et Saint-Michel proposent des prix au m² plus abordables tout en restant très centraux. Idéal pour les primo-accédants souhaitant rester intra-rocade.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Les prix varient-ils beaucoup d'un quartier à l'autre ?
        </h3>
        <p>
          Oui, de 4 100 €/m² (Bastide) à 5 800 €/m² (Saint-Pierre). L'écart reflète la centralité, le patrimoine architectural et la demande. Aix-en-Provence reste attractif comparé aux métropoles similaires.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Comment choisir son quartier pour vendre ?
        </h3>
        <p>
          Votre bien s'adapte à un profil de client. Utilisez notre estimation pour connaître le prix du marché, puis explorez les tendances de votre quartier pour fixer le bon prix de vente.
        </p>
      </article>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- CTA FINAL -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="cta-final card">
      <p class="eyebrow">
        <i class="fas fa-lightbulb"></i> Prêt à connaître la valeur de votre bien ?
      </p>
      <h2>Estimez votre propriété dès maintenant</h2>
      <p class="lead">
        Quel que soit votre quartier, notre outil vous donne une estimation fiable et précise en quelques secondes.
      </p>
      <a href="/estimation#form-estimation" class="btn btn-primary">
        <i class="fas fa-calculator"></i> Commencer une estimation
      </a>
    </div>
  </div>
</section>

<script>
  (function () {
    const mapIframe = document.getElementById('google-map-quartiers');
    const buttons = document.querySelectorAll('.quartier-map-btn');

    if (!mapIframe || !buttons.length) {
      return;
    }

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const coords = button.getAttribute('data-coords');
        const zoom = button.getAttribute('data-zoom') || '15';
        const nom = button.getAttribute('data-nom');

        if (!coords) {
          return;
        }

        // Update map
        mapIframe.setAttribute('src', `https://maps.google.com/maps?q=${coords}&z=${zoom}&output=embed`);

        // Update button states
        buttons.forEach((btn) => btn.classList.remove('active'));
        button.classList.add('active');

        // Smooth scroll to map
        mapIframe.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      });
    });

    // Set first button as active on load
    if (buttons.length > 0) {
      buttons[0].classList.add('active');
    }
  })();
</script>
