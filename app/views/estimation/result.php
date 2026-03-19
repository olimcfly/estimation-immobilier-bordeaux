<section class="result-layout">
  <article class="card result-summary">
    <p class="eyebrow">Estimation obtenue</p>
    <h2>Votre estimation à <?= e((string) $estimate['city']) ?></h2>
    <p class="muted">Voici la fourchette de valeur calculée pour votre bien.</p>

    <div class="grid-3">
      <div class="result-kpi">
        <strong>Basse</strong>
        <p><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> €</p>
      </div>
      <div class="result-kpi">
        <strong>Moyenne</strong>
        <p><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> €</p>
      </div>
      <div class="result-kpi">
        <strong>Haute</strong>
        <p><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> €</p>
      </div>
    </div>

    <p class="result-price">Prix moyen au m² : <strong><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> €/m²</strong></p>
  </article>

  <article class="card lead-cta">
    <p class="eyebrow">Passer à l'action</p>
    <h3>Activez votre accompagnement vendeur</h3>
    <p class="muted">Laissez vos coordonnées pour être rappelé et transformer cette estimation en projet concret.</p>
    <a href="#lead-form" class="btn">Je veux être recontacté</a>
  </article>
</section>

<section class="card" id="lead-form">
  <h3>Laisser mes coordonnées</h3>
  <form action="/lead" method="post" class="form-grid">
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

    <button type="submit" class="btn">Enregistrer mon lead</button>
  </form>
</section>
