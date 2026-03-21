<!DOCTYPE html>
<html lang="fr">
<head>
  <?php
    $siteConfig = getSiteConfig();
    $colors = $siteConfig['colors'] ?? [];
    $rgbColors = $siteConfig['rgb_colors'] ?? [];
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e((string) ($meta_description ?? '')) ?>">
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="<?= e((string) ($colors['primary'] ?? '#8B1538')) ?>">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <title><?= e((string) ($page_title ?? 'Estimation Immobilier Angers')) ?></title>

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e((string) ($page_title ?? '')) ?>">
  <meta property="og:description" content="<?= e((string) ($meta_description ?? '')) ?>">
  <meta property="og:locale" content="fr_FR">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Landing Page CSS -->
  <link rel="stylesheet" href="/assets/css/landing.css">

  <style>
    :root {
      --bg: <?= e((string) ($colors['bg'] ?? '#faf9f7')) ?>;
      --surface: <?= e((string) ($colors['surface'] ?? '#ffffff')) ?>;
      --text: <?= e((string) ($colors['text'] ?? '#1a1410')) ?>;
      --muted: <?= e((string) ($colors['muted'] ?? '#6b6459')) ?>;
      --primary: <?= e((string) ($colors['primary'] ?? '#8B1538')) ?>;
      --primary-dark: <?= e((string) ($colors['primary_dark'] ?? '#6b0f2d')) ?>;
      --accent: <?= e((string) ($colors['accent'] ?? '#D4AF37')) ?>;
      --accent-light: <?= e((string) ($colors['accent_light'] ?? '#E8C547')) ?>;
      --border: <?= e((string) ($colors['border'] ?? '#e8dfd7')) ?>;
      --success: <?= e((string) ($colors['success'] ?? '#22c55e')) ?>;
      --warning: <?= e((string) ($colors['warning'] ?? '#f97316')) ?>;
      --danger: <?= e((string) ($colors['danger'] ?? '#e24b4a')) ?>;
      --info: <?= e((string) ($colors['info'] ?? '#3b82f6')) ?>;
      --primary-rgb: <?= e((string) ($rgbColors['primary'] ?? '139, 21, 56')) ?>;
      --accent-rgb: <?= e((string) ($rgbColors['accent'] ?? '212, 175, 55')) ?>;
      --success-rgb: <?= e((string) ($rgbColors['success'] ?? '34, 197, 94')) ?>;
      --border-rgb: <?= e((string) ($rgbColors['border'] ?? '232, 223, 215')) ?>;
    }
  </style>

  <!-- Google Ads Conversion Tracking (placeholder — replace with your IDs) -->
  <!-- Google tag (gtag.js) -->
  <!--
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-XXXXXXXXX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'AW-XXXXXXXXX');
  </script>
  -->
</head>
<body class="lp-body">

  <?php
    // Include the specific landing page content
    $landingViewPath = __DIR__ . '/../' . ($landing_view ?? 'landing/pages/estimation-angers') . '.php';
    if (is_file($landingViewPath)) {
        include $landingViewPath;
    }
  ?>

  <!-- Minimal footer — required for Google Ads policy -->
  <footer class="lp-footer">
    <div class="lp-container">
      <div class="lp-footer-inner">
        <div class="lp-footer-brand">
          <span class="lp-logo-icon"><i class="fas fa-home"></i></span>
          <span class="lp-logo-text">Estimation Immobilier <strong>Angers</strong></span>
        </div>
        <div class="lp-footer-links">
          <a href="/mentions-legales" target="_blank" rel="noopener">Mentions légales</a>
          <a href="/politique-confidentialite" target="_blank" rel="noopener">Confidentialité</a>
          <a href="/rgpd" target="_blank" rel="noopener">RGPD</a>
        </div>
        <p class="lp-footer-copy">&copy; <?= date('Y') ?> Estimation Immobilier Angers. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <!-- Google Ads conversion event (fire on thank-you page) -->
  <script>
    // UTM parameter persistence in sessionStorage
    (function() {
      var params = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid'];
      var search = new URLSearchParams(window.location.search);
      params.forEach(function(p) {
        var val = search.get(p);
        if (val) sessionStorage.setItem(p, val);
      });

      // Auto-fill hidden UTM fields in forms
      document.querySelectorAll('form').forEach(function(form) {
        params.forEach(function(p) {
          var stored = sessionStorage.getItem(p);
          if (stored) {
            var input = form.querySelector('input[name="' + p + '"]');
            if (input) input.value = stored;
          }
        });
      });
    })();
  </script>

</body>
</html>
