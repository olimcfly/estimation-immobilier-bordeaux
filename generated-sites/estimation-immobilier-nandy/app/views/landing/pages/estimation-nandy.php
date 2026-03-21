<?php
/**
 * Landing Page Google Ads — Mot-clé : "estimation immobilière nandy"
 *
 * Structure optimisée Quality Score :
 *  1. Hero + Formulaire (above the fold)
 *  2. Bénéfices / Proposition de valeur
 *  3. Comment ça marche (3 étapes)
 *  4. Preuve sociale (témoignages + chiffres)
 *  5. FAQ (objections)
 *  6. CTA final
 */
$formError = $form_error ?? '';
?>

<!-- ═══════════════════════════════════════════ -->
<!-- HERO : TITRE + FORMULAIRE                   -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-hero">
  <div class="lp-container lp-hero-grid">

    <!-- Colonne gauche : Message -->
    <div class="lp-hero-content">
      <p class="lp-eyebrow">
        <i class="fas fa-chart-line"></i> Estimation immobilière en ligne
      </p>

      <h1>Estimation Immobilière <span class="lp-highlight">Nandy</span> — Gratuite en 60 secondes</h1>

      <p class="lp-lead">
        Obtenez une <strong>fourchette de prix fiable</strong> pour votre bien immobilier à Nandy.
        Basé sur les données réelles du marché de nandy. <strong>100% gratuit</strong>, sans engagement.
      </p>

      <ul class="lp-trust-list">
        <li><i class="fas fa-bolt"></i> <strong>Résultat immédiat</strong> — en 60 secondes</li>
        <li><i class="fas fa-hand-holding-usd"></i> <strong>100% gratuit</strong> — aucun frais caché</li>
        <li><i class="fas fa-shield-alt"></i> <strong>Données sécurisées</strong> — RGPD conforme</li>
        <li><i class="fas fa-chart-bar"></i> <strong>Marché actuel</strong> — prix réels Nandy 2024</li>
      </ul>

      <!-- Social proof compact -->
      <div class="lp-social-proof-mini">
        <div class="lp-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <span>4.8/5 — Plus de <strong>2 400 estimations</strong> réalisées à Nandy</span>
      </div>
    </div>

    <!-- Colonne droite : Formulaire -->
    <div class="lp-form-card">
      <div class="lp-form-header">
        <h2><i class="fas fa-calculator"></i> Votre estimation gratuite</h2>
        <p>Remplissez ce formulaire pour recevoir votre avis de valeur.</p>
      </div>

      <?php if ($formError !== ''): ?>
        <div class="lp-alert lp-alert-error">
          <i class="fas fa-exclamation-circle"></i> <?= e($formError) ?>
        </div>
      <?php endif; ?>

      <form action="/lp/submit" method="post" class="lp-form" id="lp-form">
        <input type="hidden" name="landing_slug" value="<?= e((string) ($landing_slug ?? 'estimation-nandy')) ?>">
        <input type="hidden" name="utm_source" value="">
        <input type="hidden" name="utm_medium" value="">
        <input type="hidden" name="utm_campaign" value="">
        <input type="hidden" name="utm_term" value="">
        <input type="hidden" name="utm_content" value="">
        <input type="hidden" name="gclid" value="">
        <?= \App\Services\UtmTrackingService::hiddenFields() ?>

        <div class="lp-field">
          <label for="nom"><i class="fas fa-user"></i> Votre nom</label>
          <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required minlength="2" maxlength="120">
        </div>

        <div class="lp-field">
          <label for="email"><i class="fas fa-envelope"></i> Votre email</label>
          <input type="email" id="email" name="email" placeholder="jean@example.com" required>
        </div>

        <div class="lp-field">
          <label for="telephone"><i class="fas fa-phone"></i> Votre téléphone</label>
          <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" required minlength="6" maxlength="30">
        </div>

        <div class="lp-field-row">
          <div class="lp-field">
            <label for="type_bien"><i class="fas fa-home"></i> Type de bien</label>
            <select id="type_bien" name="type_bien">
              <option value="appartement">Appartement</option>
              <option value="maison">Maison</option>
              <option value="studio">Studio</option>
              <option value="loft">Loft</option>
            </select>
          </div>
          <div class="lp-field">
            <label for="surface"><i class="fas fa-ruler-combined"></i> Surface (m²)</label>
            <input type="number" id="surface" name="surface" placeholder="75" min="5" max="10000">
          </div>
        </div>

        <div class="lp-field">
          <label for="ville"><i class="fas fa-map-marker-alt"></i> Ville / Quartier</label>
          <input type="text" id="ville" name="ville" value="Nandy" placeholder="Nandy, Chartrons, Caudéran...">
        </div>

        <button type="submit" class="lp-btn lp-btn-primary lp-btn-full">
          <i class="fas fa-paper-plane"></i> Obtenir mon estimation gratuite
        </button>

        <p class="lp-form-disclaimer">
          <i class="fas fa-lock"></i> Vos données sont protégées et ne seront jamais partagées.
          <a href="/politique-confidentialite" target="_blank">Politique de confidentialité</a>
        </p>
      </form>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- BÉNÉFICES                                    -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-award"></i> Pourquoi nous choisir</p>
      <h2>L'estimation immobilière la plus fiable de <span class="lp-highlight">Nandy</span></h2>
    </div>

    <div class="lp-benefits-grid">
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-database"></i></div>
        <h3>Données du marché réel</h3>
        <p>Notre algorithme analyse les transactions récentes et les prix au m² actuels de chaque quartier de nandy.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-clock"></i></div>
        <h3>Résultat en 60 secondes</h3>
        <p>Pas d'attente, pas de rendez-vous. Remplissez le formulaire et recevez votre fourchette de prix instantanément.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-user-tie"></i></div>
        <h3>Suivi par un expert</h3>
        <p>Un conseiller immobilier vous rappelle pour affiner l'estimation et vous accompagner dans votre projet.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-euro-sign"></i></div>
        <h3>100% gratuit</h3>
        <p>Aucun frais, aucun engagement. Notre service d'estimation est entièrement gratuit pour les propriétaires.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- COMMENT ÇA MARCHE (3 étapes)                -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-list-ol"></i> Simple et rapide</p>
      <h2>Comment obtenir votre estimation ?</h2>
    </div>

    <div class="lp-steps">
      <div class="lp-step">
        <div class="lp-step-number">1</div>
        <h3>Remplissez le formulaire</h3>
        <p>Indiquez vos coordonnées et les caractéristiques de votre bien (type, surface, localisation).</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">2</div>
        <h3>Recevez votre estimation</h3>
        <p>Notre algorithme calcule une fourchette de prix basée sur les données réelles du marché de nandy.</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">3</div>
        <h3>Un expert vous rappelle</h3>
        <p>Un conseiller vous contacte pour affiner l'estimation et répondre à toutes vos questions.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- PREUVE SOCIALE                               -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-users"></i> Ils nous font confiance</p>
      <h2>Ce que disent nos clients à Nandy</h2>
    </div>

    <!-- Chiffres clés -->
    <div class="lp-stats-row">
      <div class="lp-stat">
        <span class="lp-stat-number">2 400+</span>
        <span class="lp-stat-label">Estimations réalisées</span>
      </div>
      <div class="lp-stat">
        <span class="lp-stat-number">4.8/5</span>
        <span class="lp-stat-label">Note de satisfaction</span>
      </div>
      <div class="lp-stat">
        <span class="lp-stat-number">60 sec</span>
        <span class="lp-stat-label">Temps de réponse</span>
      </div>
      <div class="lp-stat">
        <span class="lp-stat-number">100%</span>
        <span class="lp-stat-label">Gratuit</span>
      </div>
    </div>

    <!-- Témoignages -->
    <div class="lp-testimonials">
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"L'estimation était très proche du prix final de vente. Le conseiller m'a rappelé le jour même et m'a accompagné jusqu'à la signature. Très professionnel."</p>
        <p class="lp-testimonial-author"><strong>Marie D.</strong> — Nandy Chartrons</p>
      </div>
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"Simple, rapide et gratuit. J'ai obtenu une fourchette de prix en moins d'une minute. L'avis de valeur m'a aidé à fixer le bon prix pour mon appartement."</p>
        <p class="lp-testimonial-author"><strong>Pierre L.</strong> — Nandy Saint-Pierre</p>
      </div>
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <p class="lp-testimonial-text">"Je cherchais une estimation fiable pour ma maison à Caudéran. Le service est sérieux et le rappel de l'expert m'a vraiment aidé à prendre ma décision."</p>
        <p class="lp-testimonial-author"><strong>Sophie M.</strong> — Caudéran</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- FAQ                                          -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-question-circle"></i> Questions fréquentes</p>
      <h2>Tout savoir sur l'estimation immobilière à Nandy</h2>
    </div>

    <div class="lp-faq">
      <details class="lp-faq-item" open>
        <summary>L'estimation est-elle vraiment gratuite ?</summary>
        <p>Oui, notre service d'estimation en ligne est 100% gratuit et sans engagement. Vous n'avez rien à payer, ni maintenant ni plus tard.</p>
      </details>
      <details class="lp-faq-item">
        <summary>Comment est calculée l'estimation ?</summary>
        <p>Notre algorithme analyse les transactions immobilières récentes à Nandy, les prix au m² par quartier, le type de bien et sa surface pour vous fournir une fourchette de prix réaliste.</p>
      </details>
      <details class="lp-faq-item">
        <summary>Combien de temps faut-il pour recevoir le résultat ?</summary>
        <p>Le résultat est quasi-instantané. Dès que vous soumettez le formulaire, notre système calcule votre estimation et un expert vous recontacte sous 24h pour l'affiner.</p>
      </details>
      <details class="lp-faq-item">
        <summary>Mes données sont-elles protégées ?</summary>
        <p>Absolument. Vos données personnelles sont protégées conformément au RGPD. Elles ne sont jamais partagées avec des tiers et sont utilisées uniquement pour vous fournir votre estimation.</p>
      </details>
      <details class="lp-faq-item">
        <summary>L'estimation est-elle fiable ?</summary>
        <p>Notre estimation est indicative et basée sur les données réelles du marché. Un expert immobilier vous rappelle ensuite pour affiner la valeur en tenant compte des spécificités de votre bien (état, étage, vue, etc.).</p>
      </details>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════ -->
<!-- CTA FINAL                                    -->
<!-- ═══════════════════════════════════════════ -->
<section class="lp-section lp-cta-section">
  <div class="lp-container lp-cta-content">
    <h2>Prêt à connaître la valeur de votre bien ?</h2>
    <p>Obtenez votre estimation immobilière gratuite en 60 secondes. Sans engagement.</p>
    <a href="#lp-form" class="lp-btn lp-btn-primary lp-btn-large">
      <i class="fas fa-paper-plane"></i> Estimer mon bien gratuitement
    </a>
    <p class="lp-cta-sub">Plus de 2 400 estimations réalisées à Nandy</p>
  </div>
</section>
