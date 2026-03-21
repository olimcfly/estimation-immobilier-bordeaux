<?php
/**
 * Landing Page Google Ads — Mot-clé : "vendre maison nantes"
 */
$formError = $form_error ?? '';
?>

<!-- HERO -->
<section class="lp-hero">
  <div class="lp-container lp-hero-grid">

    <div class="lp-hero-content">
      <p class="lp-eyebrow">
        <i class="fas fa-home"></i> Vente immobilière à Nantes
      </p>

      <h1>Vendez votre maison à <span class="lp-highlight">Nantes</span> au meilleur prix</h1>

      <p class="lp-lead">
        Vous envisagez de <strong>vendre votre maison à Nantes</strong> ?
        Commencez par connaître sa valeur réelle grâce à notre estimation gratuite.
        Un expert vous accompagne ensuite pour <strong>optimiser votre prix de vente</strong>.
      </p>

      <ul class="lp-trust-list">
        <li><i class="fas fa-chart-line"></i> <strong>Prix de vente optimal</strong> — basé sur le marché actuel</li>
        <li><i class="fas fa-user-tie"></i> <strong>Accompagnement expert</strong> — rappel sous 24h</li>
        <li><i class="fas fa-hand-holding-usd"></i> <strong>Estimation gratuite</strong> — sans engagement</li>
        <li><i class="fas fa-map-marked-alt"></i> <strong>Connaissance locale</strong> — Nantes et Loire-Atlantique</li>
      </ul>

      <div class="lp-social-proof-mini">
        <div class="lp-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <span>4.8/5 — Des centaines de propriétaires de nantes nous font confiance</span>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="lp-form-card">
      <div class="lp-form-header">
        <h2><i class="fas fa-home"></i> Estimez votre maison</h2>
        <p>Recevez gratuitement la valeur de votre maison à Nantes.</p>
      </div>

      <?php if ($formError !== ''): ?>
        <div class="lp-alert lp-alert-error">
          <i class="fas fa-exclamation-circle"></i> <?= e($formError) ?>
        </div>
      <?php endif; ?>

      <form action="/lp/submit" method="post" class="lp-form" id="lp-form">
        <input type="hidden" name="landing_slug" value="<?= e((string) ($landing_slug ?? 'vendre-maison-nantes')) ?>">
        <input type="hidden" name="type_bien" value="maison">
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

        <div class="lp-field">
          <label for="surface"><i class="fas fa-ruler-combined"></i> Surface de la maison (m²)</label>
          <input type="number" id="surface" name="surface" placeholder="120" min="5" max="10000">
        </div>

        <div class="lp-field">
          <label for="ville"><i class="fas fa-map-marker-alt"></i> Quartier / Ville</label>
          <input type="text" id="ville" name="ville" value="Nantes" placeholder="Caudéran, Talence, Pessac...">
        </div>

        <button type="submit" class="lp-btn lp-btn-primary lp-btn-full">
          <i class="fas fa-paper-plane"></i> Estimer ma maison gratuitement
        </button>

        <p class="lp-form-disclaimer">
          <i class="fas fa-lock"></i> Vos données sont protégées et ne seront jamais partagées.
        </p>
      </form>
    </div>
  </div>
</section>

<!-- BÉNÉFICES -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-key"></i> Vendez en toute confiance</p>
      <h2>Pourquoi estimer votre maison avant de vendre ?</h2>
    </div>

    <div class="lp-benefits-grid">
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-bullseye"></i></div>
        <h3>Fixez le bon prix dès le départ</h3>
        <p>Un bien surévalué reste trop longtemps sur le marché. Un bien sous-évalué vous fait perdre de l'argent. L'estimation vous donne le prix juste.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-tachometer-alt"></i></div>
        <h3>Vendez plus rapidement</h3>
        <p>Un prix bien positionné attire les acheteurs sérieux dès les premières semaines de mise en vente.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-handshake"></i></div>
        <h3>Négociez en position de force</h3>
        <p>Avec une estimation documentée, vous avez des arguments solides face aux offres d'achat.</p>
      </div>
      <div class="lp-benefit-card">
        <div class="lp-benefit-icon"><i class="fas fa-map-pin"></i></div>
        <h3>Expertise locale Nantes</h3>
        <p>Nous connaissons chaque quartier de Nantes : Chartrons, Caudéran, Saint-Pierre, Bastide, Mériadeck...</p>
      </div>
    </div>
  </div>
</section>

<!-- 3 ÉTAPES -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-list-ol"></i> Processus simple</p>
      <h2>3 étapes pour vendre votre maison au bon prix</h2>
    </div>

    <div class="lp-steps">
      <div class="lp-step">
        <div class="lp-step-number">1</div>
        <h3>Demandez votre estimation</h3>
        <p>Remplissez le formulaire avec les informations de base de votre maison. C'est gratuit et sans engagement.</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">2</div>
        <h3>Recevez votre prix de vente</h3>
        <p>Obtenez une fourchette de prix basée sur les ventes récentes dans votre quartier.</p>
      </div>
      <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="lp-step">
        <div class="lp-step-number">3</div>
        <h3>Vendez avec un expert</h3>
        <p>Un conseiller immobilier vous accompagne pour optimiser la vente et obtenir le meilleur prix.</p>
      </div>
    </div>
  </div>
</section>

<!-- TÉMOIGNAGES -->
<section class="lp-section">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-users"></i> Témoignages</p>
      <h2>Ils ont vendu leur maison à Nantes avec nous</h2>
    </div>

    <div class="lp-testimonials">
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"Nous avons vendu notre maison à Caudéran en 3 semaines grâce à l'estimation qui nous a permis de fixer le bon prix dès le départ."</p>
        <p class="lp-testimonial-author"><strong>François et Claire B.</strong> — Caudéran</p>
      </div>
      <div class="lp-testimonial">
        <div class="lp-testimonial-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
        </div>
        <p class="lp-testimonial-text">"Le conseiller a été très réactif. Il m'a rappelé dans l'heure et m'a accompagné tout au long de la vente. Je recommande vivement."</p>
        <p class="lp-testimonial-author"><strong>Alain R.</strong> — Pessac</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="lp-section lp-section-alt">
  <div class="lp-container">
    <div class="lp-section-heading">
      <p class="lp-eyebrow"><i class="fas fa-question-circle"></i> FAQ</p>
      <h2>Questions sur la vente de votre maison</h2>
    </div>

    <div class="lp-faq">
      <details class="lp-faq-item" open>
        <summary>Quel est le prix moyen d'une maison à Nantes ?</summary>
        <p>Le prix moyen d'une maison à Nantes varie entre 3 500 et 6 000 €/m² selon le quartier. Caudéran et les Chartrons sont parmi les quartiers les plus prisés, tandis que la Bastide offre encore des opportunités intéressantes.</p>
      </details>
      <details class="lp-faq-item">
        <summary>Combien de temps faut-il pour vendre une maison à Nantes ?</summary>
        <p>En moyenne, une maison correctement estimée se vend entre 1 et 3 mois à Nantes. Un prix juste dès le départ est la clé pour une vente rapide.</p>
      </details>
      <details class="lp-faq-item">
        <summary>L'estimation engage-t-elle à vendre ?</summary>
        <p>Non, l'estimation est totalement gratuite et sans engagement. Vous êtes libre de l'utiliser comme simple information pour votre projet.</p>
      </details>
      <details class="lp-faq-item">
        <summary>L'expert peut-il se déplacer chez moi ?</summary>
        <p>Oui, après l'estimation en ligne, un expert peut se rendre sur place pour affiner la valeur en tenant compte de l'état du bien, des travaux réalisés, de la vue, etc.</p>
      </details>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="lp-section lp-cta-section">
  <div class="lp-container lp-cta-content">
    <h2>Vendez votre maison au meilleur prix</h2>
    <p>Commencez par une estimation gratuite. Un expert de nantes vous accompagne ensuite.</p>
    <a href="#lp-form" class="lp-btn lp-btn-primary lp-btn-large">
      <i class="fas fa-home"></i> Estimer ma maison gratuitement
    </a>
  </div>
</section>
