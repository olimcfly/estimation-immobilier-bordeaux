<?php $page_title = 'Newsletter - Estimation Immobilière Bordeaux | Conseils & Tendances'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-envelope-open-text"></i> Newsletter</p>
      <h1>Recevez nos conseils immobiliers à Bordeaux</h1>
      <p class="lead">
        Chaque semaine, profitez d'analyses du marché local, de conseils de vente et d'alertes sur les tendances des prix.
      </p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width: 840px;">
    <article class="card" style="padding: 2rem;">
      <h2><i class="fas fa-paper-plane"></i> Inscription rapide</h2>
      <p>
        Laissez votre email pour recevoir notre newsletter. Aucune publicité inutile : uniquement du contenu utile
        pour mieux estimer, vendre ou acheter à Bordeaux.
      </p>

      <form class="form-grid" action="#" method="post" style="margin-top: 1rem;">
        <label for="newsletter_email" class="full-width">
          <span><i class="fas fa-envelope"></i> Email *</span>
          <input type="email" id="newsletter_email" name="newsletter_email" placeholder="vous@exemple.com" required>
        </label>

        <div class="form-checkbox full-width">
          <input type="checkbox" id="newsletter_rgpd" name="newsletter_rgpd" required>
          <label for="newsletter_rgpd" style="margin: 0; font-weight: 500; font-size: 0.9rem; color: var(--text); cursor: pointer;">
            J'accepte de recevoir la newsletter et je peux me désinscrire à tout moment.
          </label>
        </div>

        <button type="submit" class="btn btn-primary full-width">
          <i class="fas fa-check-circle"></i> Je m'abonne
        </button>
      </form>
    </article>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
