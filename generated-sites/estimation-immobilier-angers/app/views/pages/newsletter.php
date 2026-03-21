<?php $page_title = 'Newsletter - Estimation Immobilière Angers | Conseils & Tendances'; ?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-envelope-open-text"></i> Newsletter</p>
      <h1>Recevez nos conseils immobiliers à Angers</h1>
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
        pour mieux estimer, vendre ou acheter à Angers.
      </p>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success" style="background:#e9f9ef;color:#14532d;border:1px solid #86efac;padding:0.9rem 1rem;border-radius:10px;margin:1rem 0;">
          <?= e($success_message) ?>
        </div>
      <?php endif; ?>

      <?php if (isset($error_message)): ?>
        <div class="alert alert-error" style="background:#fef2f2;color:#7f1d1d;border:1px solid #fecaca;padding:0.9rem 1rem;border-radius:10px;margin:1rem 0;">
          <?= e($error_message) ?>
        </div>
      <?php endif; ?>

      <form class="form-grid" action="/newsletter" method="post" style="margin-top: 1rem;">
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
