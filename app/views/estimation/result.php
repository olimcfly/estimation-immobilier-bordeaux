<!-- À insérer dans ta section <main> -->

<!-- RÉSULTAT ESTIMATION -->
<section class="estimation-result">
  <div class="container">
    <div class="result-layout">
      <!-- GAUCHE : ESTIMATION -->
      <article class="card result-summary">
        <div class="result-header">
          <p class="eyebrow"><i class="fas fa-check-circle"></i> Estimation obtenue</p>
          <h2>Votre estimation à <?= e((string) $estimate['city']) ?></h2>
          <p class="muted">Voici la fourchette de valeur calculée pour votre bien immobilier.</p>
        </div>

        <!-- KPI GRID -->
        <div class="kpi-grid">
          <div class="kpi-box kpi-low">
            <p class="kpi-label"><i class="fas fa-arrow-down"></i> Prix basse</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> €</p>
          </div>
          <div class="kpi-box kpi-mid">
            <p class="kpi-label"><i class="fas fa-bullseye"></i> Estimation moyenne</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> €</p>
          </div>
          <div class="kpi-box kpi-high">
            <p class="kpi-label"><i class="fas fa-arrow-up"></i> Prix haute</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> €</p>
          </div>
        </div>

        <!-- PRIX AU M² -->
        <div class="result-detail">
          <p class="detail-label">Prix moyen au m²</p>
          <p class="detail-value"><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> €/m²</p>
          <p class="detail-info">Fourchette : <?= number_format((float) $estimate['per_sqm_low'], 0, ',', ' ') ?> - <?= number_format((float) $estimate['per_sqm_high'], 0, ',', ' ') ?> €/m²</p>
        </div>
      </article>

      <!-- DROITE : CTA LEAD -->
      <div class="result-cta-section">
        <article class="card lead-cta">
          <div class="cta-header">
            <p class="eyebrow"><i class="fas fa-handshake"></i> Passer à l'action</p>
            <h3>Transformez cette estimation en projet</h3>
            <p class="muted">Laissez vos coordonnées pour être accompagné par un expert et concrétiser votre vente.</p>
          </div>

          <div class="cta-benefits">
            <p class="benefit"><i class="fas fa-check"></i> Analyse personnalisée</p>
            <p class="benefit"><i class="fas fa-check"></i> Stratégie de vente</p>
            <p class="benefit"><i class="fas fa-check"></i> Accompagnement expert</p>
          </div>

          <a href="#lead-form" class="btn btn-full">
            <i class="fas fa-phone-alt"></i> Je veux être recontacté
          </a>
        </article>
      </div>
    </div>
  </div>
</section>

<section class="card">
  <h3>Recevoir un accompagnement</h3>
  <form action="/lead" method="post">
    <input type="hidden" name="ville" value="<?= e((string) $estimate['city']) ?>">
    <input type="hidden" name="estimation" value="<?= e((string) $estimate['estimated_mid']) ?>">

    <label>Nom
      <input type="text" name="nom" required>
    </label>

    <label>Email
      <input type="email" name="email" required>
    </label>

    <label>Téléphone
      <input type="text" name="telephone" required>
    </label>

    <label>Adresse du bien
      <input type="text" name="adresse" required>
    </label>

    <label>Urgence
      <select name="urgence" required>
        <option value="rapide">Rapide</option>
        <option value="moyen">Moyen</option>
        <option value="long">Long terme</option>
      </select>
    </label>

    <label>Motivation
      <select name="motivation" required>
        <option value="vente">Vente</option>
        <option value="succession">Succession</option>
        <option value="divorce">Divorce</option>
        <option value="investissement">Investissement</option>
      </select>
    </label>

    <label>Notes
      <textarea name="notes" rows="4" placeholder="Ajoutez un contexte utile (travaux, disponibilité, contraintes, etc.)"></textarea>
    </label>

    <button type="submit">Enregistrer mon lead</button>
  </form>
</section>
