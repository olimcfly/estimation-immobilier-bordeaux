<?php $page_title = 'Estimation Immobilière Bordeaux - Évaluez Votre Bien'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      <p class="eyebrow">✓ Estimation immobilière à Bordeaux</p>
      <h1>Vendez au meilleur prix avec une évaluation fiable et professionnelle</h1>
      <p class="lead">Notre moteur analyse les données locales, les tendances du marché et les caractéristiques de votre bien pour fournir une fourchette de valeur précise en quelques secondes.</p>
      
      <ul class="trust-list">
        <li><i class="fas fa-check-circle"></i> +2 000 estimations réalisées</li>
        <li><i class="fas fa-check-circle"></i> Méthodologie marché local</li>
        <li><i class="fas fa-check-circle"></i> Accompagnement personnalisé</li>
      </ul>

      <div class="hero-actions">
        <a class="btn" href="#simulateur">Lancer mon estimation</a>
      </div>
    </div>

    <!-- FORMULAIRE HERO -->
    <aside class="hero-panel card" id="simulateur">
      <div class="panel-header">
        <h2>Simuler la valeur de votre bien</h2>
        <p class="muted">Remplissez le formulaire pour obtenir votre estimation immédiate.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid">
        <div class="form-row">
          <label for="ville">
            <span><i class="fas fa-map-marker-alt"></i> Ville</span>
            <input type="text" id="ville" name="ville" placeholder="Ex : Bordeaux" required>
          </label>

          <label for="type">
            <span><i class="fas fa-home"></i> Type de bien</span>
            <select id="type" name="type" required>
              <option value="">-- Sélectionner --</option>
              <option value="Appartement">Appartement</option>
              <option value="Maison">Maison</option>
              <option value="Studio">Studio</option>
            </select>
          </label>
        </div>

        <div class="form-row">
          <label for="surface">
            <span><i class="fas fa-ruler-combined"></i> Surface (m²)</span>
            <input type="number" id="surface" name="surface" min="5" step="0.1" placeholder="Ex : 85" required>
          </label>

          <label for="pieces">
            <span><i class="fas fa-door-open"></i> Pièces</span>
            <input type="number" id="pieces" name="pieces" min="1" placeholder="Ex : 3" required>
          </label>
        </div>

        <button type="submit" class="btn btn-primary">Obtenir mon estimation gratuite</button>
        <p class="form-footer">✓ 100% gratuit • ✓ Résultat immédiat • ✓ Sans engagement</p>
      </form>
    </aside>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
