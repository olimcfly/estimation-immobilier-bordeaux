<?php $page_title = 'Estimation Immobilière Bordeaux | Évaluez Votre Bien en 1 Minute'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- ============================================ -->
<!-- HERO ULTRA-PREMIUM -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <!-- COLONNE 1: HEADLINE + COPY -->
    <div>
      <!-- BADGE -->
      <p class="eyebrow">
        <i class="fas fa-certificate"></i> Estimation certifiée • Données 2024
      </p>

      <!-- HEADLINE H1 -->
      <h1>Découvrez la vraie valeur de votre bien immobilier à Bordeaux</h1>

      <!-- SUBHEADLINE -->
      <p class="lead">
        En 60 secondes, obtenez une fourchette de prix précise basée sur les données réelles du marché bordelais. Aucun engagement, 100% gratuit.
      </p>

      <!-- TRUST INDICATORS -->
      <ul class="trust-list">
        <li>
          <i class="fas fa-users"></i> 
          <strong>2 847 estimations</strong> réalisées depuis 2023
        </li>
        <li>
          <i class="fas fa-star"></i> 
          <strong>4.8/5</strong> note moyenne des utilisateurs
        </li>
        <li>
          <i class="fas fa-shield-alt"></i> 
          <strong>Données sécurisées</strong> • RGPD conforme
        </li>
      </ul>

      <!-- SOCIAL PROOF -->
      <div style="margin-top: 2rem; padding: 1.2rem; background: rgba(139, 21, 56, 0.04); border-radius: 12px; border-left: 3px solid var(--primary);">
        <p style="margin: 0 0 0.5rem 0; font-size: 0.85rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
          <i class="fas fa-quote-left"></i> Témoignage client
        </p>
        <p style="margin: 0; font-style: italic; color: var(--text); line-height: 1.6;">
          "L'estimation était très proche de l'offre reçue. Recommandé pour avoir un avis fiable avant de vendre !"
        </p>
        <p style="margin: 0.8rem 0 0; font-size: 0.85rem; color: var(--muted); font-weight: 600;">
          — Marie D. • Bordeaux Chartrons
        </p>
      </div>

      <!-- CTA BUTTONS -->
      <div class="hero-actions">
        <a href="#form-estimation" class="btn btn-primary">
          <i class="fas fa-bolt"></i> Estimer gratuitement
        </a>
        <a href="#how-it-works" class="btn btn-ghost">
          <i class="fas fa-info-circle"></i> Comment ça marche
        </a>
      </div>
    </div>

    <!-- COLONNE 2: FORMULAIRE HERO -->
    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2>
          <i class="fas fa-calculator"></i> Estimation gratuite
        </h2>
        <p class="muted">Remplissez les infos de votre bien pour obtenir votre fourchette de prix.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid">
        <!-- LIGNE 1: LOCALISATION -->
        <div class="form-row">
          <label for="city">
            <span><i class="fas fa-map-marker-alt"></i> Ville</span>
            <input 
              type="text" 
              id="city" 
              name="city" 
              placeholder="Bordeaux, Talence, Floirac..." 
              required
              autocomplete="off"
            >
          </label>

          <label for="postal_code">
            <span><i class="fas fa-envelope"></i> Code postal</span>
            <input 
              type="text" 
              id="postal_code" 
              name="postal_code" 
              placeholder="33000" 
              maxlength="5"
              required
            >
          </label>
        </div>

        <!-- LIGNE 2: TYPE & SURFACE -->
        <div class="form-row">
          <label for="property_type">
            <span><i class="fas fa-home"></i> Type de bien</span>
            <select id="property_type" name="property_type" required>
              <option value="">-- Sélectionner --</option>
              <option value="apartment">Appartement</option>
              <option value="house">Maison / Villa</option>
              <option value="studio">Studio</option>
              <option value="loft">Loft</option>
              <option value="townhouse">Maison de ville</option>
            </select>
          </label>

          <label for="surface">
            <span><i class="fas fa-ruler-combined"></i> Surface (m²)</span>
            <input 
              type="number" 
              id="surface" 
              name="surface" 
              min="10" 
              max="500" 
              step="0.1"
              placeholder="85" 
              required
            >
          </label>
        </div>

        <!-- LIGNE 3: PIÈCES & ANNÉE -->
        <div class="form-row">
          <label for="rooms">
            <span><i class="fas fa-door-open"></i> Nombre de pièces</span>
            <input 
              type="number" 
              id="rooms" 
              name="rooms" 
              min="1" 
              max="10"
              placeholder="3" 
              required
            >
          </label>

          <label for="year_built">
            <span><i class="fas fa-calendar"></i> Année construction</span>
            <input 
              type="number" 
              id="year_built" 
              name="year_built" 
              min="1850" 
              max="2024"
              placeholder="2005" 
              required
            >
          </label>
        </div>

        <!-- LIGNE 4: ÉTAGE & ÉTAT -->
        <div class="form-row">
          <label for="floor">
            <span><i class="fas fa-building"></i> Étage</span>
            <select id="floor" name="floor" required>
              <option value="">-- Sélectionner --</option>
              <option value="0">Rez-de-chaussée</option>
              <option value="1">1er étage</option>
              <option value="2">2e étage</option>
              <option value="3">3e étage</option>
              <option value="4">4e étage</option>
              <option value="5plus">5+ étages</option>
            </select>
          </label>

          <label for="condition">
            <span><i class="fas fa-tools"></i> État général</span>
            <select id="condition" name="condition" required>
              <option value="">-- Sélectionner --</option>
              <option value="excellent">Excellent (neuf/rénové)</option>
              <option value="good">Bon (entretenu)</option>
              <option value="fair">Moyen (travaux à prévoir)</option>
              <option value="poor">Mauvais (gros travaux)</option>
            </select>
          </label>
        </div>

        <!-- LIGNE 5: CHAMBRES & SALLE BAIN -->
        <div class="form-row">
          <label for="bedrooms">
            <span><i class="fas fa-bed"></i> Chambres</span>
            <input 
              type="number" 
              id="bedrooms" 
              name="bedrooms" 
              min="0" 
              max="10"
              placeholder="2" 
              required
            >
          </label>

          <label for="bathrooms">
            <span><i class="fas fa-bath"></i> Salles de bain</span>
            <input 
              type="number" 
              id="bathrooms" 
              name="bathrooms" 
              min="0" 
              max="5"
              placeholder="1" 
              required
            >
          </label>
        </div>

        <!-- BOUTON SOUMISSION -->
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 1rem; padding: 1rem;">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <!-- REASSURANCE -->
        <p class="form-footer" style="text-align: center; margin: 1rem 0 0; font-size: 0.8rem;">
          <i class="fas fa-check-circle"></i> 100% gratuit • 
          <i class="fas fa-zap"></i> Résultat en 1 min • 
          <i class="fas fa-lock"></i> Sécurisé & confidentiel
        </p>
      </form>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- COMMENT ÇA MARCHE (HOW IT WORKS) -->
<!-- ============================================ -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-bolt"></i> Processus simple & rapide
      </p>
      <h2>Comment fonctionne notre estimation ?</h2>
    </div>

    <div class="steps-grid">
      <!-- ÉTAPE 1 -->
      <article class="card step-card">
        <div class="step-icon">
          <i class="fas fa-edit"></i>
        </div>
        <div class="step-number">1</div>
        <h3>Remplissez le formulaire</h3>
        <p>Vous entrez les caractéristiques essentielles de votre bien : localisation, type, surface, pièces, état.</p>
      </article>

      <!-- ÉTAPE 2 -->
      <article class="card step-card">
        <div class="step-icon">
          <i class="fas fa-database"></i>
        </div>
        <div class="step-number">2</div>
        <h3>Analyse des données</h3>
        <p>Notre moteur analyse les références réelles de transactions dans votre quartier pour une évaluation précise.</p>
      </article>

      <!-- ÉTAPE 3 -->
      <article class="card step-card">
        <div class="step-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <div class="step-number">3</div>
        <h3>Recevez l'estimation</h3>
        <p>Vous obtenez une fourchette de prix, l'analyse comparative et des insights sur votre marché local.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- AVANTAGES CLÉS -->
<!-- ============================================ -->
<section class="section section-alt" id="benefits">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-crown"></i> Pourquoi nous choisir
      </p>
      <h2>L'estimation immobilière fiable</h2>
    </div>

    <div class="features-grid">
      <!-- AVANTAGE 1 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-database"></i>
        </div>
        <h3>Données actualisées</h3>
        <p>Base de données de 5000+ transactions récentes en Gironde. Marché temps réel, pas de données obsolètes.</p>
      </article>

      <!-- AVANTAGE 2 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-robot"></i>
        </div>
        <h3>Algorithme intelligent</h3>
        <p>Machine learning entraîné sur les tendances bordelaises. Précision ±5% en conditions normales.</p>
      </article>

      <!-- AVANTAGE 3 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-clock"></i>
        </div>
        <h3>Résultat immédiat</h3>
        <p>Pas d'attente, pas de formulaire complexe. Estimation complète en moins de 60 secondes.</p>
      </article>

      <!-- AVANTAGE 4 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3>100% confidentiel</h3>
        <p>Vos données ne sont jamais vendues. RGPD conforme. Chiffrement SSL/TLS de bout en bout.</p>
      </article>

      <!-- AVANTAGE 5 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-handshake"></i>
        </div>
        <h3>Support expert</h3>
        <p>Experts immobiliers locaux disponibles pour clarifier votre estimation et vous conseiller.</p>
      </article>

      <!-- AVANTAGE 6 -->
      <article class="card feature-card">
        <div class="feature-icon">
          <i class="fas fa-star"></i>
        </div>
        <h3>Gratuit & sans engagement</h3>
        <p>Estimation complète 100% gratuite. Aucune obligation d'être recontacté ou d'avancer.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- EXEMPLE DE RÉSULTAT -->
<!-- ============================================ -->
<section class="section" id="example-result">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-eye"></i> Voici ce que vous recevrez
      </p>
      <h2>Exemple d'estimation détaillée</h2>
    </div>

    <div class="result-layout">
      <!-- RÉSUMÉ PRINCIPAL -->
      <div class="result-summary card">
        <div class="result-header">
          <p class="eyebrow">
            <i class="fas fa-check-circle"></i> Estimation pour
          </p>
          <h2>T3 • Bordeaux Chartrons</h2>
          <p>85 m² • Année 2005 • État bon</p>
        </div>

        <div class="kpi-grid">
          <div class="kpi-box kpi-low">
            <p class="kpi-label"><i class="fas fa-arrow-down"></i> Prix minimum</p>
            <p class="kpi-value">290 K€</p>
          </div>

          <div class="kpi-box kpi-mid">
            <p class="kpi-label"><i class="fas fa-target"></i> Estimation centrale</p>
            <p class="kpi-value">315 K€</p>
          </div>

          <div class="kpi-box kpi-high">
            <p class="kpi-label"><i class="fas fa-arrow-up"></i> Prix maximum</p>
            <p class="kpi-value">340 K€</p>
          </div>
        </div>

        <div class="result-detail">
          <p class="detail-label"><i class="fas fa-coins"></i> Prix au m²</p>
          <p class="detail-value">€3 700</p>
          <p class="detail-info">Moyenne pour votre quartier : €3 500 - €4 100</p>
        </div>
      </div>

      <!-- INSIGHTS -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--primary);">
          <h3 style="margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; color: var(--primary);">
            <i class="fas fa-chart-line"></i> Tendance du marché
          </h3>
          <p style="margin: 0; color: var(--text); font-weight: 600;">
            <i class="fas fa-arrow-up" style="color: #22c55e;"></i> 
            <span style="color: #22c55e;">Marché haussier</span>
          </p>
          <p style="margin: 0.5rem 0 0; color: var(--muted); font-size: 0.9rem;">
            Prix en hausse de +3.2% sur les 12 derniers mois dans ce secteur.
          </p>
        </div>

        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--accent);">
          <h3 style="margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; color: var(--primary);">
            <i class="fas fa-info-circle"></i> Facteurs clés
          </h3>
          <ul style="margin: 0; padding-left: 1.5rem; list-style: none;">
            <li style="margin-bottom: 0.5rem; color: var(--text);">
              <span style="color: var(--primary); font-weight: 700;">✓</span> Localisation prime (Chartrons)
            </li>
            <li style="margin-bottom: 0.5rem; color: var(--text);">
              <span style="color: var(--primary); font-weight: 700;">✓</span> Bien entretenu (+5%)
            </li>
            <li style="color: var(--text);">
              <span style="color: var(--primary); font-weight: 700;">•</span> Absence de parking (-3%)
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ SECTION -->
<!-- ============================================ -->
<section class="section" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-comments"></i> Questions fréquentes
      </p>
      <h2>Vos réponses en un coup d'œil</h2>
    </div>

    <div class="faq-grid">
      <!-- FAQ 1 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation est-elle gratuite ?</h3>
        <p>Oui, 100% gratuit et sans engagement. Utilisez-la autant que vous le souhaitez pour explorer différents scénarios.</p>
      </article>

      <!-- FAQ 2 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Combien de temps faut-il ?</h3>
        <p>Estimation immédiate après validation. Vous avez votre fourchette de prix en moins de 60 secondes.</p>
      </article>

      <!-- FAQ 3 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> À quelle précision m'attendre ?</h3>
        <p>Précision moyenne ±5% basée sur les données réelles de transactions. Peut varier selon l'état du bien et la localisation exacte.</p>
      </article>

      <!-- FAQ 4 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Mes données sont-elles sécurisées ?</h3>
        <p>Oui. Chiffrement SSL/TLS, stockage sécurisé, et conformité RGPD. Vos données ne sont jamais vendues à des tiers.</p>
      </article>

      <!-- FAQ 5 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Serai-je harcelé par des appels commerciaux ?</h3>
        <p>Non. Nous respectons votre choix. Si vous laissez vos coordonnées, vous avez le contrôle total. Désinscription possible à tout moment.</p>
      </article>

      <!-- FAQ 6 -->
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je faire plusieurs estimations ?</h3>
        <p>Oui ! Estimez votre bien, celui d'un ami, explorez différents scénarios. Aucune limite, c'est 100% gratuit.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL HAUTE CONVERSION -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="card" style="padding: 3rem; background: linear-gradient(135deg, rgba(139, 21, 56, 0.05), rgba(212, 175, 55, 0.03)); border: 2px solid var(--accent); text-align: center;">
      <p class="eyebrow" style="margin-bottom: 1rem;">
        <i class="fas fa-rocket"></i> Plus attendre pour connaître la valeur
      </p>
      <h2 style="margin-bottom: 1rem; font-size: 2rem;">
        Estimez votre bien en 60 secondes
      </h2>
      <p class="lead" style="max-width: 600px; margin: 0 auto 2rem;">
        Obtenez une fourchette de prix précise basée sur les données réelles du marché bordelais. 100% gratuit, sans engagement, confidentiel.
      </p>
      <a href="#form-estimation" class="btn btn-primary" style="display: inline-flex; font-size: 1.1rem; padding: 1.2rem 2rem;">
        <i class="fas fa-calculator"></i> Lancer mon estimation gratuite
      </a>
      <p style="margin-top: 1.5rem; font-size: 0.85rem; color: var(--muted);">
        <i class="fas fa-clock"></i> Résultat en 1 minute • 
        <i class="fas fa-lock"></i> Données sécurisées • 
        <i class="fas fa-check-circle"></i> Sans engagement
      </p>
    </div>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
