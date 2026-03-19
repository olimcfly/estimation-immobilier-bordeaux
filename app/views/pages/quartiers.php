<?php
$page_title = 'Quartiers de Bordeaux - Estimation Immobilière Bordeaux | Guide Détaillé';

$quartiers = [
    [
        'nom' => 'Chartrons',
        'description' => "Quartier emblématique du nord de Bordeaux, les Chartrons séduisent par leur charme historique, leurs antiquaires, galeries d'art et ambiance village. Très prisé des familles et jeunes actifs.",
        'prix_m2' => 5200,
        'prix_moyen' => 520000,
        'caracteristiques' => ['Patrimoine', 'Commerces', 'Marché', 'Vie culturelle'],
        'population' => '~15000 habitants',
        'transports' => 'Tram B, Bus, Pistes cyclables',
        'attractivite' => 'Très haute',
        'coords' => '44.8530,-0.5700',
        'tendance' => '+4.8%',
    ],
    [
        'nom' => 'Saint-Pierre',
        'description' => "Cœur historique de Bordeaux, quartier piéton animé avec restaurants, bars et boutiques. Architecture XVIIIe siècle remarquable. Idéal pour les amoureux du centre-ville.",
        'prix_m2' => 5800,
        'prix_moyen' => 480000,
        'caracteristiques' => ['Centre historique', 'Piéton', 'Gastronomie', 'Vie nocturne'],
        'population' => '~8000 habitants',
        'transports' => 'Tram A/B, Bus, Piéton',
        'attractivite' => 'Très haute',
        'coords' => '44.8378,-0.5717',
        'tendance' => '+3.5%',
    ],
    [
        'nom' => 'Saint-Michel',
        'description' => "Quartier populaire et cosmopolite autour de la basilique et de son marché. Ambiance multiculturelle, prix encore accessibles et forte dynamique de rénovation urbaine.",
        'prix_m2' => 4200,
        'prix_moyen' => 350000,
        'caracteristiques' => ['Multiculturel', 'Marché', 'Patrimoine', 'Dynamique'],
        'population' => '~12000 habitants',
        'transports' => 'Tram A, Bus, Gare Saint-Jean proche',
        'attractivite' => 'Haute',
        'coords' => '44.8330,-0.5650',
        'tendance' => '+6.2%',
    ],
    [
        'nom' => 'Caudéran',
        'description' => "Quartier résidentiel prisé des familles, Caudéran offre de belles maisons avec jardins, un cadre de vie paisible et d'excellentes écoles. L'un des secteurs les plus recherchés.",
        'prix_m2' => 4900,
        'prix_moyen' => 580000,
        'caracteristiques' => ['Résidentiel', 'Familles', 'Espaces verts', 'Écoles'],
        'population' => '~42000 habitants',
        'transports' => 'Tram A/D, Bus, Accès rocade',
        'attractivite' => 'Très haute',
        'coords' => '44.8500,-0.6100',
        'tendance' => '+3.1%',
    ],
    [
        'nom' => 'Bastide',
        'description' => "Rive droite en pleine transformation, la Bastide offre des vues imprenables sur la ville, des projets urbains ambitieux et des prix encore attractifs. Fort potentiel de plus-value.",
        'prix_m2' => 4100,
        'prix_moyen' => 380000,
        'caracteristiques' => ['Rive droite', 'Renouveau urbain', 'Vue Garonne', 'Investissement'],
        'population' => '~18000 habitants',
        'transports' => 'Tram A, Bus, Pont de Pierre',
        'attractivite' => 'Haute',
        'coords' => '44.8400,-0.5550',
        'tendance' => '+7.3%',
    ],
    [
        'nom' => 'Mériadeck',
        'description' => "Quartier d'affaires et administratif de Bordeaux, Mériadeck offre des appartements spacieux à des prix raisonnables. Proche du centre, bien desservi et en cours de réhabilitation.",
        'prix_m2' => 4400,
        'prix_moyen' => 390000,
        'caracteristiques' => ['Affaires', 'Spacieux', 'Central', 'Services'],
        'population' => '~10000 habitants',
        'transports' => 'Tram A/B, Bus, Gare proche',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.8370,-0.5830',
        'tendance' => '+2.8%',
    ],
];
?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="eyebrow">
        <i class="fas fa-map-marked-alt"></i> Quartiers de Bordeaux
      </p>
      <h1>Explorez les quartiers de Bordeaux</h1>
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
        title="Carte des quartiers de Bordeaux"
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
      <h2>Ambiances et paysages de Bordeaux</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--space-4);">
      <!-- Chartrons -->
      <figure style="margin: 0;">
        <div style="position: relative; overflow: hidden; border-radius: var(--radius-xl); height: 240px; background: var(--bg-alt);">
          <img
            src="https://images.unsplash.com/photo-1560969184-10fe8719e047?auto=format&fit=crop&w=500&q=80"
            alt="Quartier des Chartrons à Bordeaux"
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
            alt="Quartier Saint-Pierre Bordeaux"
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
            alt="Quartier Saint-Michel Bordeaux"
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
          Oui, de 4 100 €/m² (Bastide) à 5 800 €/m² (Saint-Pierre). L'écart reflète la centralité, le patrimoine architectural et la demande. Bordeaux reste attractif comparé aux métropoles similaires.
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
