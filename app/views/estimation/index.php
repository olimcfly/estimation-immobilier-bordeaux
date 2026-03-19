<?php $page_title = 'Estimation Immobilière Bordeaux - Évaluez Votre Bien'; ?>

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

        <div class="form-row">
          <label for="annee">
            <span><i class="fas fa-calendar"></i> Année construction</span>
            <input type="number" id="annee" name="annee" min="1900" max="2024" placeholder="Ex : 2005" required>
          </label>

          <label for="etage">
            <span><i class="fas fa-building"></i> Étage</span>
            <input type="number" id="etage" name="etage" min="0" placeholder="Ex : 2" required>
          </label>
        </div>

        <label for="etat" class="full-width">
          <span><i class="fas fa-tools"></i> État général</span>
          <select id="etat" name="etat" required>
            <option value="">-- Sélectionner --</option>
            <option value="Excellent">Excellent (rénové récemment)</option>
            <option value="Bon">Bon (entretenu)</option>
            <option value="Moyen">Moyen (travaux à prévoir)</option>
            <option value="Mauvais">Mauvais (travaux importants)</option>
          </select>
        </label>

        <button type="submit" class="btn btn-primary">Obtenir mon estimation gratuite</button>
        <p class="form-footer">✓ 100% gratuit • ✓ Résultat immédiat • ✓ Sans engagement</p>
      </form>
    </aside>
  </div>
</section>

<!-- AVANTAGES -->
<section class="section" id="avantages">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Pourquoi nous choisir</p>
      <h2>Un service premium pour votre projet immobilier</h2>
    </div>
    <div class="features-grid">
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <h3>Données locales fines</h3>
        <p>Nous analysons les références de transactions en Gironde pour une évaluation réaliste et actualisée de votre bien.</p>
      </article>
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-bolt"></i>
        </div>
        <h3>Résultat immédiat</h3>
        <p>En moins d'une minute, obtenez une fourchette complète avec prix au m², analyse comparative et tendances.</p>
      </article>
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-handshake"></i>
        </div>
        <h3>Accompagnement personnalisé</h3>
        <p>Recevez des conseils d'expert pour vendre rapidement et sécuriser votre projet de vente immobilière.</p>
      </article>
    </div>
  </div>
</section>

<!-- EXEMPLE RÉSULTAT -->
<section class="section section-alt" id="resultat-exemple">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Voici ce que vous recevrez</p>
      <h2>Exemple d'estimation détaillée</h2>
    </div>
    <div class="result-layout">
      <div class="result-summary card">
        <h3>Votre estimation</h3>
        <p class="result-price">
          <span class="price-range">€ 290 000 - 340 000</span>
          <span class="price-m2">€ 3 500 - 4 100 / m²</span>
        </p>
        <div class="result-kpi">
          <p class="kpi-label">Estimation moyenne</p>
          <p class="kpi-value">€ 315 000</p>
        </div>
      </div>
      <div class="result-boxes">
        <div class="result-box">
          <p class="box-label">Prix bas</p>
          <p class="box-value">€ 290 K</p>
        </div>
        <div class="result-box">
          <p class="box-label">Prix haut</p>
          <p class="box-value">€ 340 K</p>
        </div>
        <div class="result-box">
          <p class="box-label">Marché actuel</p>
          <p class="box-value">Haussier</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PROCESSUS -->
<section class="section" id="process">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Notre méthode</p>
      <h2>3 étapes simples pour démarrer</h2>
    </div>
    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">01</div>
        <h3>Renseignez votre bien</h3>
        <p>Ville, type, surface, étage et état suffisent pour lancer la simulation complète.</p>
        <div class="step-icon"><i class="fas fa-pencil-alt"></i></div>
      </article>
      <article class="card step-card">
        <div class="step-number">02</div>
        <h3>Recevez l'estimation</h3>
        <p>Une fourchette de prix avec analyse comparative et prix au m² en cohérence avec le marché.</p>
        <div class="step-icon"><i class="fas fa-file-invoice-dollar"></i></div>
      </article>
      <article class="card step-card">
        <div class="step-number">03</div>
        <h3>Activez l'accompagnement</h3>
        <p>Laissez vos coordonnées et avancez avec nos experts pour concrétiser votre vente.</p>
        <div class="step-icon"><i class="fas fa-phone-alt"></i></div>
      </article>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Questions fréquentes</p>
      <h2>On répond à vos questions</h2>
    </div>
    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation est-elle gratuite ?</h3>
        <p>Oui, la simulation en ligne est 100% gratuite et sans engagement. Vous pouvez l'utiliser autant de fois que vous le souhaitez.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Combien de temps pour le résultat ?</h3>
        <p>Le résultat est généré immédiatement après validation du formulaire. Vous recevez votre fourchette de prix en moins d'une minute.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je être recontacté ?</h3>
        <p>Oui, si vous le souhaitez, vous pouvez laisser vos coordonnées pour être accompagné par un expert dans votre projet de vente.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Quelle est la précision de l'estimation ?</h3>
        <p>Notre moteur utilise des algorithmes basés sur les données réelles de transactions en Gironde. La précision est d'environ ±5% en conditions normales.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Comment sont utilisées mes données ?</h3>
        <p>Vos données sont sécurisées et utilisées uniquement pour calculer votre estimation et vous proposer un accompagnement si vous le souhaitez.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je faire une estimation pour un projet futur ?</h3>
        <p>Bien sûr ! Vous pouvez simuler la valeur de votre bien à tout moment pour préparer votre stratégie de vente.</p>
      </article>
    </div>
  </div>
</section>
