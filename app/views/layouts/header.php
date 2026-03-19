<!DOCTYPE html>
<html lang="fr">
<head>
<?php
  $colors = $config['colors'] ?? [];
  $rgbColors = $config['rgb_colors'] ?? [];
?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Estimation immobilière Bordeaux - Évaluez votre bien gratuitement et découvrez nos guides immobiliers.">
  <meta name="theme-color" content="<?= e((string) ($colors['primary'] ?? '#8B1538')) ?>">
  <title><?= isset($page_title) ? $page_title : 'Estimation Immobilière Bordeaux' ?></title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS Principal -->
  <link rel="stylesheet" href="/public/assets/css/app.css">

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
    .nav-main {
      display: flex;
      align-items: center;
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
      padding: 0.8rem 1.2rem;
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
    .nav-dropdown {
      position: relative;
    }

    .dropdown-toggle::after {
      content: '';
      display: inline-block;
      width: 0.4rem;
      height: 0.4rem;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      transform: rotate(-45deg);
      margin-left: 0.4rem;
      transition: transform 0.2s ease;
    }

    .nav-dropdown:hover .dropdown-toggle::after {
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

    .nav-dropdown:hover .dropdown-menu {
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
    }

    .menu-toggle span {
      width: 24px;
      height: 2px;
      background: var(--text);
      border-radius: 2px;
      transition: all 0.3s ease;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .nav-main {
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

      .nav-main {
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        background: var(--surface);
        flex-direction: column;
        gap: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        border-bottom: 1px solid var(--border);
      }

      .nav-main.active {
        max-height: 500px;
      }

      .nav-item {
        width: 100%;
      }

      .nav-link {
        padding: 1rem 1.5rem;
        border-radius: 0;
        justify-content: space-between;
      }

      .dropdown-menu {
        position: static;
        opacity: 0;
        visibility: hidden;
        max-height: 0;
        overflow: hidden;
        box-shadow: none;
        border: none;
        background: rgba(var(--primary-rgb), 0.04);
        transform: none;
        transition: all 0.2s ease;
      }

      .nav-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        max-height: 400px;
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
        font-size: 1.2rem;
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
        font-size: 1.1rem;
        gap: 0.5rem;
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
  </style>
</head>
<body>

<!-- ============================= -->
<!-- HEADER PREMIUM -->
<!-- ============================= -->
<header class="site-header">
  <div class="header-container">
    <div class="header-wrapper">
      <!-- LOGO -->
      <a href="/" class="brand">
        <div class="brand-icon">
          <i class="fas fa-home"></i>
        </div>
        <span>Estim</span>ation
      </a>

      <!-- NAVIGATION PRINCIPALE -->
      <nav class="nav-main" id="navMain">
        <!-- ESTIMATION -->
        <div class="nav-item nav-dropdown">
          <a href="/" class="nav-link dropdown-toggle">
            <i class="fas fa-calculator"></i> Estimation
          </a>
          <ul class="dropdown-menu">
            <li><a href="/#form-estimation"><i class="fas fa-zap"></i> Estimer mon bien</a></li>
            <li><a href="/#example-result"><i class="fas fa-chart-bar"></i> Voir un exemple</a></li>
            <li><a href="/#how-it-works"><i class="fas fa-cogs"></i> Comment ça marche</a></li>
            <li><a href="/faq"><i class="fas fa-question-circle"></i> FAQ Estimation</a></li>
          </ul>
        </div>

        <!-- BLOG -->
        <div class="nav-item nav-dropdown">
          <a href="/blog" class="nav-link dropdown-toggle">
            <i class="fas fa-book-open"></i> Blog
          </a>
          <ul class="dropdown-menu">
            <li><a href="/blog"><i class="fas fa-newspaper"></i> Tous les articles</a></li>
            <li><a href="/blog?cat=vendre"><i class="fas fa-door-open"></i> Vendre son bien</a></li>
            <li><a href="/blog?cat=marche"><i class="fas fa-chart-line"></i> Marché immobilier</a></li>
            <li><a href="/blog?cat=conseil"><i class="fas fa-lightbulb"></i> Conseils & astuces</a></li>
            <li><a href="/blog?cat=legal"><i class="fas fa-gavel"></i> Aspect juridique</a></li>
          </ul>
        </div>

        <!-- SERVICES -->
        <div class="nav-item nav-dropdown">
          <a href="/services" class="nav-link dropdown-toggle">
            <i class="fas fa-briefcase"></i> Services
          </a>
          <ul class="dropdown-menu">
            <li><a href="/services#estimation"><i class="fas fa-calculator"></i> Estimation détaillée</a></li>
            <li><a href="/services#accompagnement"><i class="fas fa-handshake"></i> Accompagnement</a></li>
            <li><a href="/services#conseil"><i class="fas fa-user-tie"></i> Conseil immobilier</a></li>
            <li><a href="/services#marketing"><i class="fas fa-bullhorn"></i> Marketing immobilier</a></li>
          </ul>
        </div>

        <!-- À PROPOS -->
        <div class="nav-item">
          <a href="/about" class="nav-link">
            <i class="fas fa-info-circle"></i> À propos
          </a>
        </div>

        <!-- CONTACT -->
        <div class="nav-item">
          <a href="/contact" class="nav-link">
            <i class="fas fa-envelope"></i> Contact
          </a>
        </div>

        <!-- RESSOURCES -->
        <div class="nav-item nav-dropdown">
          <a href="#" class="nav-link dropdown-toggle">
            <i class="fas fa-graduation-cap"></i> Ressources
          </a>
          <ul class="dropdown-menu">
            <li><a href="/guides"><i class="fas fa-map"></i> Guides complets</a></li>
            <li><a href="/tools/calculatrice"><i class="fas fa-calculator"></i> Calculatrice prix</a></li>
            <li><a href="/quartiers"><i class="fas fa-map-marker-alt"></i> Quartiers Bordeaux</a></li>
            <li><a href="/podcast"><i class="fas fa-podcast"></i> Podcast immobilier</a></li>
            <li><a href="/newsletter"><i class="fas fa-envelope-open"></i> Newsletter</a></li>
          </ul>
        </div>
      </nav>

      <!-- ACTIONS DROITE -->
      <div class="header-actions">
        <!-- SEARCH (optionnel) -->
        <!-- <div class="search-wrapper">
          <i class="fas fa-search search-icon"></i>
          <input type="text" class="search-input" placeholder="Chercher...">
        </div> -->

        <!-- CTA PRINCIPAL -->
        <a href="/#form-estimation" class="btn-cta">
          <i class="fas fa-bolt"></i>
          <span>Estimer</span>
        </a>

        <!-- MENU TOGGLE MOBILE -->
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </div>
</header>

<main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const navMain = document.getElementById('navMain');
    const navDropdowns = document.querySelectorAll('.nav-dropdown');

    // Toggle menu mobile
    menuToggle.addEventListener('click', function() {
      navMain.classList.toggle('active');
    });

    // Fermer menu au clic sur un lien
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        // Ne pas fermer si c'est un dropdown
        if (!this.parentElement.classList.contains('nav-dropdown')) {
          navMain.classList.remove('active');
        }
      });
    });

    // Gestion des dropdowns en mobile
    navDropdowns.forEach(dropdown => {
      const toggle = dropdown.querySelector('.nav-link');
      toggle.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          dropdown.classList.toggle('active');
          e.stopPropagation();
        }
      });
    });

    // Fermer les dropdowns au clic extérieur
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.nav-dropdown')) {
        navDropdowns.forEach(d => d.classList.remove('active'));
      }
    });
  });
</script>
