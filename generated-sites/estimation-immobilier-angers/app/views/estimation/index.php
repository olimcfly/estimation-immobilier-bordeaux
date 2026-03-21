<?php $page_title = 'Estimation Immobilière Angers - Avis de Valeur Indicatif Gratuit'; ?>

<!-- ============================================ -->
<!-- HERO + FORMULAIRE -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      <p class="eyebrow"><i class="fas fa-chart-line"></i> Avis de valeur indicatif en ligne</p>
      <h1>Estimez votre bien immobilier à Angers</h1>
      <p class="lead">
        Obtenez une fourchette de prix indicative en quelques secondes.
        Seulement 3 informations nécessaires.
      </p>

      <ul class="trust-list">
        <li><i class="fas fa-bolt"></i> <strong>Résultat instantané</strong></li>
        <li><i class="fas fa-hand-holding-usd"></i> <strong>100% gratuit</strong></li>
        <li><i class="fas fa-shield-alt"></i> <strong>Sans inscription</strong></li>
      </ul>
    </div>

    <!-- FORMULAIRE 3 CHAMPS -->
    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2><i class="fas fa-calculator"></i> Estimation gratuite</h2>
        <p class="muted">3 informations suffisent pour votre fourchette de prix.</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div style="padding: 1rem; margin-bottom: 1rem; background: rgba(var(--warning-rgb), 0.1); border: 1px solid var(--warning); border-radius: 8px;">
          <?php foreach ($errors as $error): ?>
            <p style="margin: 0; color: var(--danger); font-size: 0.9rem;"><i class="fas fa-exclamation-circle"></i> <?= e((string) $error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="/estimation" method="post" class="form-grid">
        <!-- VILLE -->
        <div class="form-row">
          <label for="ville">
            <span><i class="fas fa-map-marker-alt"></i> Ville</span>
            <input
              type="text"
              id="ville"
              name="ville"
              placeholder="Angers, Talence, Mérignac..."
              required
              autocomplete="off"
            >
          </label>
        </div>

        <!-- TYPE & SURFACE -->
        <div class="form-row">
          <label for="type">
            <span><i class="fas fa-building"></i> Type de bien</span>
            <select id="type" name="type" required>
              <option value="">-- Sélectionner --</option>
              <option value="appartement">Appartement</option>
              <option value="maison">Maison</option>
              <option value="studio">Studio</option>
              <option value="loft">Loft</option>
              <option value="maison de ville">Maison de ville</option>
            </select>
          </label>

          <label for="surface">
            <span><i class="fas fa-ruler-combined"></i> Surface (m²)</span>
            <input
              type="number"
              id="surface"
              name="surface"
              min="5"
              max="10000"
              step="1"
              placeholder="85"
              required
            >
          </label>
        </div>

        <!-- PIÈCES -->
        <div class="form-row">
          <label for="pieces">
            <span><i class="fas fa-door-open"></i> Nombre de pièces</span>
            <input
              type="number"
              id="pieces"
              name="pieces"
              min="1"
              max="50"
              placeholder="3"
              required
            >
          </label>
        </div>

        <!-- BOUTON -->
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 1rem; padding: 1rem;">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <p class="form-footer" style="text-align: center; margin: 1rem 0 0; font-size: 0.8rem; color: var(--muted);">
          <i class="fas fa-check-circle"></i> 100% gratuit •
          <i class="fas fa-clock"></i> Résultat en 1 min •
          <i class="fas fa-lock"></i> Données sécurisées
        </p>
      </form>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- COMPRENDRE: ESTIMATION vs AVIS DE VALEUR -->
<!-- ============================================ -->
<section class="section section-alt" id="comprendre">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow"><i class="fas fa-gavel"></i> Ce qu'il faut savoir</p>
      <h2>Estimation en ligne vs. Avis de valeur réalisé par un conseiller immobilier</h2>
    </div>

    <div class="card" style="margin-top: 1.5rem; padding: 1.5rem 2rem; background: rgba(var(--primary-rgb), 0.04); border-left: 4px solid var(--primary);">
      <p style="margin: 0; font-size: 0.95rem; line-height: 1.7;">
        <i class="fas fa-exclamation-triangle" style="color: var(--primary);"></i>
        <strong>Important :</strong> Les outils en ligne (y compris le nôtre) fournissent des <strong>estimations statistiques</strong> à partir de données de marché.
        Pour affiner le prix de vente de votre bien, l'idéal est de compléter cette première estimation par un <strong>avis de valeur</strong> réalisé par un conseiller immobilier
        qui se déplace chez vous et analyse votre bien dans le détail.
      </p>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- PROCESSUS -->
<!-- ============================================ -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow"><i class="fas fa-bolt"></i> Notre méthode</p>
      <h2>3 étapes simples pour estimer votre bien</h2>
    </div>
    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">01</div>
        <h3>Renseignez votre bien</h3>
        <p>Ville, type, surface et nombre de pièces suffisent pour lancer l'estimation.</p>
      </article>
      <article class="card step-card">
        <div class="step-number">02</div>
        <h3>Analyse des données</h3>
        <p>Notre moteur analyse les transactions récentes dans votre secteur.</p>
      </article>
      <article class="card step-card">
        <div class="step-number">03</div>
        <h3>Recevez l'estimation</h3>
        <p>Une fourchette de prix détaillée avec prix au m² et analyse du marché.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ -->
<!-- ============================================ -->
<section class="section section-alt" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow"><i class="fas fa-question-circle"></i> Questions fréquentes</p>
      <h2>FAQ Estimation</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation est-elle gratuite ?</h3>
        <p>Oui, 100% gratuite et sans engagement. Aucune inscription nécessaire.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Qu'est-ce qu'un avis de valeur ?</h3>
        <p>C'est un document rédigé par un <strong>professionnel de l'immobilier</strong> (conseiller ou agent immobilier) après visite du bien. Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Pourquoi les outils en ligne ne suffisent-ils pas ?</h3>
        <p>Les outils en ligne utilisent des <strong>statistiques générales</strong> (prix au m², tendances, historique des ventes). Ils ne voient pas l'état réel du bien, les travaux, la luminosité, la vue ou le voisinage. Seul un professionnel qui se rend sur place peut intégrer ces critères dans un avis de valeur.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="cta-final card">
      <p class="eyebrow"><i class="fas fa-calculator"></i> Prêt ?</p>
      <h2>Obtenez votre fourchette de prix en 30 secondes</h2>
      <p class="lead">3 informations. Gratuit. Sans engagement.</p>
      <a href="#form-estimation" class="btn btn-primary" style="display: inline-flex; gap: 0.5rem;">
        <i class="fas fa-bolt"></i> Estimer mon bien gratuitement
      </a>
    </div>
  </div>
</section>
