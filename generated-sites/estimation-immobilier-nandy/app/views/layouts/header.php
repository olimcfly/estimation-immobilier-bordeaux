<!DOCTYPE html>
<html lang="fr">
<head>
  <?php
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $canonicalPath = (string) parse_url($requestUri, PHP_URL_PATH);
    $canonicalPath = $canonicalPath !== '' ? $canonicalPath : '/';
    if ($canonicalPath !== '/') {
        $canonicalPath = rtrim($canonicalPath, '/');
    }

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $canonicalUrl = $scheme . '://' . $host . $canonicalPath;
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars((string) ($meta_description ?? 'Estimation immobilier Nandy - Obtenez votre avis de valeur immobilier gratuit. Données réelles du marché de nandy, résultat en 60 secondes.'), ENT_QUOTES, 'UTF-8') ?>">
  <meta name="theme-color" content="#8B1538">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="canonical" href="<?= e($canonicalUrl) ?>">
  <title><?= isset($page_title) ? $page_title : 'Estimation Immobilier Nandy' ?></title>

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Estimation Immobilier Nandy' ?>">
  <meta property="og:description" content="<?= htmlspecialchars((string) ($meta_description ?? 'Obtenez votre avis de valeur immobilier gratuit à Nandy. Résultat en 60 secondes.'), ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="Estimation Immobilier Nandy">
  <meta property="og:image" content="https://estimation-immobilier-nandy.fr/assets/images/og-estimation-nandy.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Estimation Immobilier Nandy' ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars((string) ($meta_description ?? 'Avis de valeur immobilier gratuit à Nandy. Résultat en 60 secondes.'), ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:image" content="https://estimation-immobilier-nandy.fr/assets/images/og-estimation-nandy.png">

  <!-- Schema.org JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "RealEstateAgent",
    "name": "Estimation Immobilier Nandy",
    "description": "Avis de valeur et estimation immobilière gratuite à Nandy et en Seine-et-Marne.",
    "url": "https://estimation-immobilier-nandy.fr",
    "telephone": "+33164000000",
    "email": "contact@estimation-immobilier-nandy.fr",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Nandy",
      "addressRegion": "Île-de-France",
      "postalCode": "77176",
      "addressCountry": "FR"
    },
    "areaServed": {
      "@type": "City",
      "name": "Nandy"
    },
    "priceRange": "Gratuit"
  }
  </script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS Principal -->
  <link rel="stylesheet" href="/assets/css/app.css">

  <!-- CSS Header Personnalisé -->
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
      --neutral: <?= e((string) ($colors['neutral'] ?? '#000000')) ?>;
      --bg-rgb: <?= e((string) ($rgbColors['bg'] ?? '250, 249, 247')) ?>;
      --border-rgb: <?= e((string) ($rgbColors['border'] ?? '232, 223, 215')) ?>;
      --primary-rgb: <?= e((string) ($rgbColors['primary'] ?? '139, 21, 56')) ?>;
      --accent-rgb: <?= e((string) ($rgbColors['accent'] ?? '212, 175, 55')) ?>;
      --success-rgb: <?= e((string) ($rgbColors['success'] ?? '34, 197, 94')) ?>;
      --warning-rgb: <?= e((string) ($rgbColors['warning'] ?? '249, 115, 22')) ?>;
      --neutral-rgb: <?= e((string) ($rgbColors['neutral'] ?? '0, 0, 0')) ?>;
    }

    /* HEADER PREMIUM */
    .site-header {
      position: sticky;
      top: 0;
      z-index: 999;
      backdrop-filter: blur(12px);
      background: rgba(var(--bg-rgb), 0.95);
      border-bottom: 1px solid rgba(var(--border-rgb), 0.6);
      box-shadow: 0 2px 8px rgba(var(--neutral-rgb), 0.04);
    }

    .header-container {
      width: min(1200px, calc(100% - 2.5rem));
      margin-inline: auto;
      padding: 1rem 0;
    }

    .header-wrapper {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 2rem;
    }

    /* LOGO/BRAND */
    .brand {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      text-decoration: none;
      margin: 0;
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 1.4rem;
      letter-spacing: -0.02em;
      flex-shrink: 0;
      min-width: 200px;
    }

    .brand-icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), #C41E3A);
      border-radius: 10px;
      color: #fff;
      font-size: 1.2rem;
      box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
    }

    .brand span {
      color: var(--primary);
    }

    /* NAVIGATION PRINCIPALE */
    .top-nav {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      flex: 1;
    }

    .nav-item {
      position: relative;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.8rem 0.9rem;
      text-decoration: none;
      color: var(--muted);
      font-weight: 500;
      font-size: 0.95rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .nav-link:hover {
      color: var(--primary);
      background: rgba(var(--primary-rgb), 0.05);
    }

    .nav-link.active {
      color: var(--primary);
      background: rgba(var(--primary-rgb), 0.08);
      font-weight: 600;
    }

    .nav-link i {
      font-size: 0.9rem;
    }

    /* DROPDOWN MENU */
    .has-dropdown {
      position: relative;
    }

    .has-dropdown > .nav-link::after {
      content: '';
      display: inline-block;
      width: 0.4rem;
      height: 0.4rem;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      transform: rotate(45deg);
      margin-left: 0.4rem;
      transition: transform 0.2s ease;
    }

    .has-dropdown:hover > .nav-link::after {
      transform: rotate(-135deg);
    }

    .dropdown-menu {
      position: absolute;
      top: calc(100% + 0.5rem);
      left: 0;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(var(--neutral-rgb), 0.1);
      min-width: 220px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s ease;
      list-style: none;
      margin: 0;
      padding: 0.5rem 0;
      z-index: 1000;
    }

    .has-dropdown:hover .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-menu li {
      margin: 0;
    }

    .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      padding: 0.75rem 1.5rem;
      color: var(--text);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .dropdown-menu a:hover {
      background: rgba(var(--primary-rgb), 0.05);
      border-left-color: var(--primary);
      color: var(--primary);
      padding-left: 1.8rem;
    }

    .dropdown-menu i {
      width: 18px;
      text-align: center;
      color: var(--primary);
    }

    /* CTA & SEARCH */
    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-shrink: 0;
    }

    .search-wrapper {
      position: relative;
      display: none;
    }

    .search-input {
      padding: 0.6rem 1rem 0.6rem 2.5rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.9rem;
      width: 200px;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08);
    }

    .search-icon {
      position: absolute;
      left: 0.8rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      pointer-events: none;
    }

    .btn-cta {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.8rem 1.6rem;
      background: linear-gradient(135deg, var(--primary), #C41E3A);
      color: #fff;
      text-decoration: none;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
      white-space: nowrap;
    }

    .btn-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.3);
      background: linear-gradient(135deg, var(--primary-dark), #a01833);
    }

    .btn-cta i {
      font-size: 1rem;
    }

    /* TOGGLE MOBILE */
    .menu-toggle {
      display: none;
      flex-direction: column;
      gap: 0.4rem;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      z-index: 999;
    }

    .menu-toggle span {
      width: 24px;
      height: 2px;
      background: var(--text);
      border-radius: 2px;
      transition: all 0.3s ease;
    }

    .menu-toggle.active span:nth-child(1) {
      transform: rotate(45deg) translate(4px, 4px);
    }

    .menu-toggle.active span:nth-child(2) {
      opacity: 0;
    }

    .menu-toggle.active span:nth-child(3) {
      transform: rotate(-45deg) translate(4px, -4px);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .top-nav {
        gap: 0;
      }

      .nav-link {
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
      }

      .search-wrapper {
        display: none !important;
      }

      .header-wrapper {
        gap: 1rem;
      }
    }

    @media (max-width: 768px) {
      .menu-toggle {
        display: flex;
      }

      .top-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--surface);
        flex-direction: column;
        gap: 0;
        padding-top: 70px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
        z-index: 998;
      }

      .top-nav.active {
        transform: translateX(0);
      }

      .top-nav > .nav-item {
        width: 100%;
        border-bottom: 1px solid var(--border);
      }

      .nav-link {
        padding: 1rem 1.5rem;
        border-radius: 0;
        justify-content: space-between;
        width: 100%;
        font-size: 1.05rem;
      }

      /* Disable hover-based dropdown on mobile */
      .has-dropdown:hover .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
      }

      .dropdown-menu {
        position: static;
        opacity: 0;
        visibility: hidden;
        max-height: 0;
        overflow: hidden;
        box-shadow: none;
        border: none;
        border-radius: 0;
        background: rgba(var(--primary-rgb), 0.04);
        transform: none;
        transition: max-height 0.3s ease, opacity 0.2s ease, visibility 0.2s ease;
        padding: 0;
      }

      .has-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        max-height: 500px;
        padding: 0.5rem 0;
      }

      /* Rotate arrow when dropdown is open */
      .has-dropdown.active > .nav-link::after {
        transform: rotate(-135deg);
      }

      .dropdown-menu a {
        padding-left: 3rem;
      }

      .dropdown-menu a:hover {
        padding-left: 3rem;
      }

      .header-actions {
        gap: 0.5rem;
      }

      .btn-cta {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
      }

      .brand {
        font-size: 1rem;
      }

      .brand-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
      }
    }

    @media (max-width: 480px) {
      .header-container {
        padding: 0.8rem 0;
      }

      .header-wrapper {
        gap: 0.8rem;
      }

      .brand {
        font-size: 0.85rem;
        gap: 0.4rem;
      }

      .brand-icon {
        width: 32px;
        height: 32px;
      }

      .btn-cta {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
      }

      .btn-cta span {
        display: none;
      }
    }

    /* Header CTA button */
    .btn-header-cta {
      padding: 0.9rem 2rem;
      font-size: 1rem;
      flex-shrink: 0;
      min-width: 200px;
      text-align: center;
    }

    .nav-cta-mobile {
      display: none;
    }

    @media (max-width: 768px) {
      .btn-header-cta {
        display: none;
      }

      .nav-cta-mobile {
        display: block;
        padding: 1.5rem;
      }

      .nav-cta-mobile a {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary), #C41E3A);
        color: #fff;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.25);
      }
    }
  </style>
</head>
<body>

<!-- ============================= -->
<!-- HEADER PREMIUM -->
<!-- ============================= -->
<header class="site-header">
  <div class="container nav-wrapper">
    <a href="/" class="brand">Estimation Immobilier <span>Nandy</span></a>

    <button class="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <nav class="top-nav" aria-label="Navigation principale">
      <div class="nav-item has-dropdown">
        <a href="/estimation" class="nav-link">Estimation</a>
        <ul class="dropdown-menu" aria-label="Sous-menu estimation">
          <li><a href="/estimation#form-estimation">Estimer mon bien</a></li>
          <li><a href="/estimation#example-result">Voir un exemple</a></li>
          <li><a href="/estimation#how-it-works">Comment ça marche</a></li>
          <li><a href="/estimation#faq">FAQ Estimation</a></li>
        </ul>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/blog" class="nav-link">Blog</a>
        <ul class="dropdown-menu" aria-label="Sous-menu blog">
          <li><a href="/blog">Tous les articles</a></li>
          <li><a href="/blog?cat=vendre">Vendre son bien</a></li>
          <li><a href="/blog?cat=marche">Marché immobilier</a></li>
          <li><a href="/blog?cat=conseil">Conseils &amp; astuces</a></li>
          <li><a href="/blog?cat=legal">Aspect juridique</a></li>
        </ul>
      </div>

      <div class="nav-item">
        <a href="/actualites" class="nav-link">Actualités</a>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/services" class="nav-link">Services</a>
        <ul class="dropdown-menu" aria-label="Sous-menu services">
          <li><a href="/services#estimation-detaillee">Estimation détaillée</a></li>
          <li><a href="/services#accompagnement">Accompagnement</a></li>
          <li><a href="/services#conseil-immobilier">Conseil immobilier</a></li>
          <li><a href="/services#marketing-immobilier">Marketing immobilier</a></li>
        </ul>
      </div>

      <div class="nav-item">
        <a href="/a-propos" class="nav-link">À propos</a>
      </div>
      <div class="nav-item">
        <a href="/contact" class="nav-link">Contact</a>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/guides" class="nav-link">Ressources</a>
        <ul class="dropdown-menu" aria-label="Sous-menu ressources">
          <li><a href="/guides">Guides complets</a></li>
          <li><a href="/tools/calculatrice">Calculatrice prix</a></li>
          <li><a href="/quartiers">Quartiers Nandy</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
        </ul>
      </div>

      <div class="nav-cta-mobile">
        <a href="/estimation#form-estimation">Estimer mon bien</a>
      </div>
    </nav>

    <a href="/estimation#form-estimation" class="btn btn-header-cta">Estimer mon bien</a>
  </div>
</header>

<main>
