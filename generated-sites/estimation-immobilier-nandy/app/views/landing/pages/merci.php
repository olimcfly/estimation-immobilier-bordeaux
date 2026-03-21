<?php
/**
 * Page de remerciement (Thank You page) — post-conversion.
 *
 * Cette page est cruciale pour :
 *  1. Confirmer la demande au visiteur
 *  2. Déclencher le pixel de conversion Google Ads
 *  3. Définir les prochaines étapes (réduire l'anxiété)
 */
$leadNom = $lead_nom ?? '';
$leadEmail = $lead_email ?? '';
$leadIdValue = $lead_id ?? 0;
$estimationValue = $estimation ?? 0;
?>

<!-- Google Ads Conversion Tracking — FIRE ON THIS PAGE -->
<script>
  // Replace AW-XXXXXXXXX/XXXXXX with your actual conversion ID and label
  // gtag('event', 'conversion', {'send_to': 'AW-XXXXXXXXX/XXXXXX'});

  // DataLayer push for Google Tag Manager
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    'event': 'lead_form_submit',
    'lead_id': '<?= (int) $leadIdValue ?>',
    'lead_source': 'google_ads_landing_page',
    'conversion_value': <?= $estimationValue > 0 ? (float) $estimationValue : 0 ?>
  });
</script>

<!-- CONFIRMATION -->
<section class="lp-hero lp-hero-merci">
  <div class="lp-container">
    <div class="lp-merci-content">

      <div class="lp-merci-icon">
        <i class="fas fa-check-circle"></i>
      </div>

      <h1>Merci<?= $leadNom !== '' ? ', ' . e($leadNom) : '' ?> !</h1>
      <p class="lp-lead">
        Votre demande d'estimation a bien été enregistrée.
        <?php if ($leadEmail !== ''): ?>
          Un email de confirmation a été envoyé à <strong><?= e($leadEmail) ?></strong>.
        <?php endif; ?>
      </p>

      <?php if ($estimationValue > 0): ?>
        <div class="lp-merci-estimation">
          <p class="lp-merci-estimation-label">Estimation indicative</p>
          <p class="lp-merci-estimation-value"><?= number_format((float) $estimationValue, 0, ',', ' ') ?> &euro;</p>
          <p class="lp-merci-estimation-note">Un expert affinera cette estimation lors de votre échange.</p>
        </div>
      <?php endif; ?>

      <!-- Prochaines étapes -->
      <div class="lp-merci-steps">
        <h2>Et maintenant ?</h2>
        <div class="lp-steps lp-steps-compact">
          <div class="lp-step">
            <div class="lp-step-number"><i class="fas fa-check"></i></div>
            <h3>Demande enregistrée</h3>
            <p>Votre formulaire a bien été reçu par notre équipe.</p>
          </div>
          <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
          <div class="lp-step">
            <div class="lp-step-number">2</div>
            <h3>Rappel sous 24h</h3>
            <p>Un expert immobilier vous contacte pour affiner l'estimation.</p>
          </div>
          <div class="lp-step-arrow"><i class="fas fa-chevron-right"></i></div>
          <div class="lp-step">
            <div class="lp-step-number">3</div>
            <h3>Avis de valeur complet</h3>
            <p>Vous recevez un avis de valeur détaillé pour votre bien.</p>
          </div>
        </div>
      </div>

      <!-- Rassurance -->
      <div class="lp-merci-reassurance">
        <div class="lp-merci-reassurance-item">
          <i class="fas fa-phone-alt"></i>
          <div>
            <strong>Un expert vous rappelle sous 24h</strong>
            <p>Gardez votre téléphone à portée de main.</p>
          </div>
        </div>
        <div class="lp-merci-reassurance-item">
          <i class="fas fa-shield-alt"></i>
          <div>
            <strong>Service 100% gratuit</strong>
            <p>Aucun frais, aucun engagement de votre part.</p>
          </div>
        </div>
        <div class="lp-merci-reassurance-item">
          <i class="fas fa-lock"></i>
          <div>
            <strong>Données protégées</strong>
            <p>Vos informations ne seront jamais partagées.</p>
          </div>
        </div>
      </div>

      <!-- CTA secondaire -->
      <div class="lp-merci-cta">
        <p>En attendant, découvrez nos ressources :</p>
        <div class="lp-merci-links">
          <a href="/blog" class="lp-btn lp-btn-ghost" target="_blank">
            <i class="fas fa-book"></i> Blog immobilier Nandy
          </a>
          <a href="/quartiers" class="lp-btn lp-btn-ghost" target="_blank">
            <i class="fas fa-map"></i> Guide des quartiers
          </a>
        </div>
      </div>

    </div>
  </div>
</section>
