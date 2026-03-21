<?php
/**
 * Landing Page Google Ads — Mot-clé : "avis de valeur gratuit nantes"
 */
$formError = $form_error ?? '';
?>

<!-- HERO -->
<section class="lp-hero">
  <div class="lp-container lp-hero-grid">

    <div class="lp-hero-content">
      <p class="lp-eyebrow">
        <i class="fas fa-file-alt"></i> Avis de valeur professionnel
      </p>

      <h1>Avis de Valeur <span class="lp-highlight">Gratuit</span> pour votre bien à Nantes</h1>

      <p class="lp-lead">
        Recevez un <strong>avis de valeur gratuit et sans engagement</strong> pour votre appartement ou maison à Nantes.
        Analyse basée sur les <strong>données réelles du marché</strong>, affinée par un expert immobilier local.
      </p>

      <ul class="lp-trust-list">
        <li><i class="fas fa-file-signature"></i> <strong>Avis de valeur détaillé</strong> — fourchette haute / basse</li>
        <li><i class="fas fa-user-check"></i> <strong>Affiné par un expert</strong> — connaissance terrain</li>
        <li><i class="fas fa-hand-holding-usd"></i> <strong>100% gratuit</strong> — aucune obligation</li>
        <li><i class="fas fa-shield-alt"></i> <strong>Confidentiel</strong> — RGPD conforme</li>
      </ul>

      <div class="lp-social-proof-mini">
        <div class="lp-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <span>4.8/5 — Service noté par nos clients de nantes</span>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="lp-form-card">
      <div class="lp-form-header">
        <h2><i class="fas fa-file-alt"></i> Demandez votre avis de valeur</h2>
        <p>Gratuit, sans engagement, résultat immédiat.</p>
      </div>

      <?php if ($formError !== ''): ?>
        <div class="lp-alert lp-alert-error">
          <i class="fas fa-exclamation-circle"></i> <?= e($formError) ?>
        </div>
      <?php endif; ?>

      <form action="/lp/submit" method="post" class="lp-form" id="lp-form">
        <input type="hidden" name="landing_slug" value="<?= e((string) ($landing_slug ?? 'avis-valeur-gratuit')) ?>">
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
              <option value="maison de ville">Maison de ville</option>
            </select>
          </div>
          <div class="lp-field">
            <label for="surface"><i class="fas fa-ruler-combined"></i> Surface (m²)</label>
            <input type="number" id="surface" name="surface" placeholder="75" min="5" max="10000">
          </div>
        </div>

        <div class="lp-field">
          <label for="ville"><i class="fas fa-map-marker-alt"></i> Localisation</label>
          <input type="text" id="ville" name="ville" value="Nantes" placeholder="Nantes, Mérignac, Talence...">
        </div>

        <button type="submit" class="lp-btn lp-btn-primary lp-btn-full">
          <i class="fas fa-file-signature"></i> Recevoir mon avis de valeur gratuit
        </button>

        <p class="lp-form-disclaimer">
          <i class="fas fa-lock"></i> Vos données sont protégées. Pas de spam, jamais.
        </p>
      </form>
    </div>
  </div>
</section>

<!-- DIFFÉRENCE ESTIMATION vs AVIS DE VALEUR -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-balance-scale"></i> Comprendre</p>
      <h2>Estimation en ligne vs Avis de Valeur professionnel</h2>
    </div>

    <div class="lp-comparison">
      <div class="lp-comparison-col">
        <h3><i class="fas fa-laptop"></i> Estimation en ligne</h3>
        <ul>
          <li><i class="fas fa-check"></i> Résultat instantané</li>
          <li><i class="fas fa-check"></i> Fourchette de prix indicative</li>
          <li><i class="fas fa-check"></i> Basé sur les données statistiques</li>
          <li><i class="fas fa-times"></i> Ne prend pas en compte l'état du bien</li>
        </ul>
      </div>
      <div class="lp-comparison-plus"><i class="fas fa-plus-circle"></i></div>
      <div class="lp-comparison-col lp-comparison-highlight">
        <h3><i class="fas fa-user-tie"></i> Avis de Valeur Expert</h3>
        <ul>
          <li><i class="fas fa-check"></i> Analyse personnalisée</li>
          <li><i class="fas fa-check"></i> Visite sur place possible</li>
          <li><i class="fas fa-check"></i> Prend en compte l'état, les travaux, l'exposition</li>
          <li><i class="fas fa-check"></i> Document professionnel utilisable</li>
        </ul>
        <p class="lp-comparison-badge">Inclus dans notre service gratuit</p>
      </div>
    </div>
  </div>
</section>

<!-- 3 ÉTAPES -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-list-ol"></i> Comment ça marche</p>
      <h2>Recevez votre avis de valeur en 3 étapes</h2>
    </div>

    <div class="lp-steps">
      <div class="lp-step">
        <div class="lp-step-number">1</div>
        <h3>Décrivez votre bien</h3>
        <p>Remplissez le formulaire avec les informations de base : type, surface, localisation.</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">2</div>
        <h3>Estimation instantanée</h3>
        <p>Recevez une première fourchette de prix basée sur les données du marché de nantes.</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">3</div>
        <h3>Avis de valeur expert</h3>
        <p>Un professionnel vous contacte pour affiner l'estimation et vous remettre un avis de valeur complet.</p>
      </div>
    </div>
  </div>
</section>

<!-- TÉMOIGNAGES -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-users"></i> Témoignages</p>
      <h2>Ils ont reçu leur avis de valeur gratuit</h2>
    </div>

    <div class="lp-stats-row">
      <div class="lp-stat">
        <span class="lp-stat-number">2 400+</span>
        <span class="lp-stat-label">Avis de valeur délivrés</span>
      </div>
      <div class="lp-stat">
        <span class="lp-stat-number">4.8/5</span>
        <span class="lp-stat-label">Satisfaction client</span>
      </div>
      <div class="lp-stat">
        <span class="lp-stat-number">24h</span>
        <span class="lp-stat-label">Délai de rappel expert</span>
      </div>
    </div>

    <div class="lp-testimonials">
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"J'avais besoin d'un avis de valeur pour une succession. Le service a été rapide et l'expert très compétent. Le document m'a été très utile auprès du notaire."</p>
        <p class="lp-testimonial-author"><strong>Catherine V.</strong> — Nantes Bastide</p>
      </div>
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"Service gratuit et de qualité. L'avis de valeur m'a permis de négocier avec l'acheteur en toute sérénité."</p>
        <p class="lp-testimonial-author"><strong>Julien T.</strong> — Mérignac</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-question-circle"></i> FAQ</p>
      <h2>Questions sur l'avis de valeur</h2>
    </div>

    <div class="lp-faq">
      <details class="lp-faq-item" open>
        <summary>Quelle est la différence entre une estimation et un avis de valeur ?</summary>
        <p>L'estimation en ligne donne une fourchette indicative. L'avis de valeur est un document plus détaillé, réalisé ou affiné par un professionnel, qui prend en compte les spécificités de votre bien (état, travaux, exposition, etc.).</p>
      </details>
      <details class="lp-faq-item">
        <summary>L'avis de valeur a-t-il une valeur juridique ?</summary>
        <p>L'avis de valeur est un document informatif rédigé par un professionnel. Il n'a pas de valeur juridique au sens strict, mais il est reconnu et utilisé par les notaires, les banques et les tribunaux comme référence indicative.</p>
      </details>
      <details class="lp-faq-item">
        <summary>Puis-je utiliser l'avis de valeur pour un divorce ou une succession ?</summary>
        <p>Oui, l'avis de valeur est fréquemment utilisé dans le cadre de successions, divorces ou partages. Il permet d'avoir une base objective de la valeur du bien.</p>
      </details>
      <details class="lp-faq-item">
        <summary>L'expert se déplace-t-il ?</summary>
        <p>Oui, si nécessaire. Après l'estimation en ligne, l'expert peut organiser une visite sur place pour affiner son analyse et prendre en compte tous les détails de votre bien.</p>
      </details>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="lp-section lp-cta-section">
  <div class="lp-container lp-cta-content">
    <h2>Obtenez votre avis de valeur gratuit maintenant</h2>
    <p>Sans engagement. Un expert de nantes affine votre estimation sous 24h.</p>
    <a href="#lp-form" class="lp-btn lp-btn-primary lp-btn-large">
      <i class="fas fa-file-signature"></i> Demander mon avis de valeur gratuit
    </a>
  </div>
</section>
