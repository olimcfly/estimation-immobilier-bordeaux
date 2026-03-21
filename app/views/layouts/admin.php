<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Admin - Estimation Immobilier Bordeaux' ?></title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- FontAwesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- CSS Principal -->
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <style>
    :root {
      --admin-sidebar-width: 260px;
      --admin-header-height: 60px;
      --admin-bg: #f4f1ed;
      --admin-sidebar-bg: #1a1410;
      --admin-sidebar-text: #c8c0b8;
      --admin-sidebar-hover: rgba(255,255,255,0.08);
      --admin-sidebar-active: rgba(139, 21, 56, 0.4);
      --admin-primary: #8B1538;
      --admin-accent: #D4AF37;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; height: 100%; }

    body {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      color: #1a1410;
      background: var(--admin-bg);
      line-height: 1.6;
      display: flex;
      min-height: 100vh;
    }

    /* ================================ */
    /* SIDEBAR                          */
    /* ================================ */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: var(--admin-sidebar-width);
      background: var(--admin-sidebar-bg);
      color: var(--admin-sidebar-text);
      display: flex;
      flex-direction: column;
      z-index: 1000;
      transition: transform 0.3s ease;
      overflow-y: auto;
    }

    .admin-sidebar-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1.25rem 1.5rem;
      text-decoration: none;
      color: #fff;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      flex-shrink: 0;
    }

    .admin-sidebar-brand-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--admin-primary), #C41E3A);
      border-radius: 8px;
      color: #fff;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .admin-sidebar-brand-text {
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 1rem;
      line-height: 1.2;
    }

    .admin-sidebar-brand-text small {
      display: block;
      font-family: 'DM Sans', sans-serif;
      font-weight: 400;
      font-size: 0.7rem;
      color: var(--admin-sidebar-text);
      opacity: 0.7;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-top: 2px;
    }

    /* Sidebar navigation */
    .admin-sidebar-nav {
      flex: 1;
      padding: 1rem 0;
    }

    .admin-sidebar-section {
      padding: 0.5rem 1.5rem 0.4rem;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,0.3);
      margin-top: 0.5rem;
    }

    .admin-sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.7rem 1.5rem;
      color: var(--admin-sidebar-text);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      border-left: 3px solid transparent;
      transition: all 0.15s ease;
    }

    .admin-sidebar-link:hover {
      background: var(--admin-sidebar-hover);
      color: #fff;
    }

    .admin-sidebar-link.active {
      background: var(--admin-sidebar-active);
      color: #fff;
      border-left-color: var(--admin-primary);
    }

    .admin-sidebar-link i {
      width: 20px;
      text-align: center;
      font-size: 0.95rem;
      opacity: 0.8;
    }

    .admin-sidebar-link.active i {
      opacity: 1;
      color: var(--admin-accent);
    }

    .admin-sidebar-link .badge {
      margin-left: auto;
      background: var(--admin-primary);
      color: #fff;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 0.15rem 0.5rem;
      border-radius: 10px;
      min-width: 20px;
      text-align: center;
    }

    /* Sidebar footer */
    .admin-sidebar-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(255,255,255,0.08);
      flex-shrink: 0;
    }

    .admin-sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 0.75rem;
    }

    .admin-sidebar-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--admin-primary), #C41E3A);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.8rem;
      font-weight: 700;
      flex-shrink: 0;
    }

    .admin-sidebar-user-info {
      overflow: hidden;
    }

    .admin-sidebar-user-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-sidebar-user-email {
      font-size: 0.7rem;
      color: var(--admin-sidebar-text);
      opacity: 0.7;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-sidebar-logout {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      color: var(--admin-sidebar-text);
      text-decoration: none;
      font-size: 0.8rem;
      border-radius: 6px;
      transition: all 0.15s ease;
    }

    .admin-sidebar-logout:hover {
      background: rgba(226, 75, 74, 0.15);
      color: #e24b4a;
    }

    /* ================================ */
    /* MAIN CONTENT AREA                */
    /* ================================ */
    .admin-main {
      flex: 1;
      margin-left: var(--admin-sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Top bar */
    .admin-topbar {
      position: sticky;
      top: 0;
      z-index: 500;
      height: var(--admin-header-height);
      background: rgba(244, 241, 237, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #e8dfd7;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
    }

    .admin-topbar-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-topbar-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #1a1410;
    }

    .admin-topbar-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-topbar-link {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 0.9rem;
      color: #6b6459;
      text-decoration: none;
      font-size: 0.85rem;
      border-radius: 6px;
      transition: all 0.15s ease;
    }

    .admin-topbar-link:hover {
      background: rgba(139, 21, 56, 0.06);
      color: var(--admin-primary);
    }

    /* Mobile toggle */
    .admin-sidebar-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      color: #1a1410;
      font-size: 1.25rem;
    }

    /* Admin content */
    .admin-content {
      flex: 1;
      padding: 2rem;
    }

    .admin-content .container {
      width: min(1120px, 100%);
      margin-inline: auto;
    }

    /* Mobile overlay */
    .admin-sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }

    /* ================================ */
    /* RESPONSIVE                       */
    /* ================================ */
    @media (max-width: 1024px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.open {
        transform: translateX(0);
      }

      .admin-sidebar-overlay.open {
        display: block;
      }

      .admin-main {
        margin-left: 0;
      }

      .admin-sidebar-toggle {
        display: block;
      }

      .admin-content {
        padding: 1.25rem;
      }
    }

    @media (max-width: 640px) {
      .admin-topbar {
        padding: 0 1rem;
      }

      .admin-content {
        padding: 1rem;
      }

      .admin-topbar-title {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="admin-sidebar-overlay" id="sidebar-overlay"></div>

<!-- SIDEBAR -->
<aside class="admin-sidebar" id="admin-sidebar">
  <a href="/admin" class="admin-sidebar-brand">
    <span class="admin-sidebar-brand-icon"><i class="fas fa-home"></i></span>
    <span class="admin-sidebar-brand-text">
      Estimation Bordeaux
      <small>Administration</small>
    </span>
  </a>

  <nav class="admin-sidebar-nav">
    <div class="admin-sidebar-section">Principal</div>
    <a href="/admin" class="admin-sidebar-link <?= ($admin_page ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="fas fa-tachometer-alt"></i> Tableau de bord
    </a>
    <a href="/admin/leads" class="admin-sidebar-link <?= ($admin_page ?? '') === 'leads' ? 'active' : '' ?>">
      <i class="fas fa-users"></i> Leads
    </a>
    <a href="/admin/partenaires" class="admin-sidebar-link <?= ($admin_page ?? '') === 'partenaires' ? 'active' : '' ?>">
      <i class="fas fa-handshake"></i> Partenaires
    </a>

    <div class="admin-sidebar-section">Contenu</div>
    <a href="/admin/blog" class="admin-sidebar-link <?= ($admin_page ?? '') === 'blog' ? 'active' : '' ?>">
      <i class="fas fa-pen-fancy"></i> Articles Blog
    </a>
    <a href="/admin/actualites" class="admin-sidebar-link <?= ($admin_page ?? '') === 'actualites' ? 'active' : '' ?>">
      <i class="fas fa-newspaper"></i> Actualités
    </a>
    <a href="/admin/images" class="admin-sidebar-link <?= ($admin_page ?? '') === 'images' ? 'active' : '' ?>">
    <a href="/admin/leads" class="admin-sidebar-link <?= $currentPage === 'leads' ? 'active' : '' ?>">
      <i class="fas fa-users"></i> Leads
      <?php if (!empty($leadCount)): ?>
        <span class="badge"><?= (int) $leadCount ?></span>
      <?php endif; ?>
    </a>
    <a href="/admin/leads?score=chaud" class="admin-sidebar-link <?= $currentPage === 'leads-chaud' ? 'active' : '' ?>">
      <i class="fas fa-fire" style="color: #ef4444;"></i> Leads Chauds
    </a>

    <div class="admin-sidebar-section">Contenu</div>
    <a href="/admin/blog" class="admin-sidebar-link <?= $currentPage === 'blog' ? 'active' : '' ?>">
      <i class="fas fa-pen-fancy"></i> Articles Blog
    </a>
    <a href="/admin/actualites" class="admin-sidebar-link <?= $currentPage === 'actualites' ? 'active' : '' ?>">
      <i class="fas fa-newspaper"></i> Actualités
    </a>
    <a href="/admin/images" class="admin-sidebar-link <?= $currentPage === 'images' ? 'active' : '' ?>">
      <i class="fas fa-image"></i> Images IA
    </a>

    <div class="admin-sidebar-section">Outils</div>
    <a href="/admin/api-management" class="admin-sidebar-link <?= ($admin_page ?? '') === 'api-management' ? 'active' : '' ?>">
      <i class="fas fa-key"></i> API
    </a>
    <a href="/admin/diagnostic" class="admin-sidebar-link <?= ($admin_page ?? '') === 'diagnostic' ? 'active' : '' ?>">
      <i class="fas fa-stethoscope"></i> Diagnostic
    </a>
    <a href="/admin/test-smtp" class="admin-sidebar-link <?= ($admin_page ?? '') === 'smtp' ? 'active' : '' ?>">
      <i class="fas fa-envelope"></i> SMTP
    <a href="/admin/diagnostic" class="admin-sidebar-link <?= $currentPage === 'diagnostic' ? 'active' : '' ?>" target="_blank">
      <i class="fas fa-stethoscope"></i> Diagnostic DB
    </a>
    <a href="/admin/test-smtp" class="admin-sidebar-link <?= $currentPage === 'smtp' ? 'active' : '' ?>" target="_blank">
      <i class="fas fa-envelope"></i> Test SMTP
    </a>
    <a href="/" class="admin-sidebar-link" target="_blank">
      <i class="fas fa-external-link-alt"></i> Voir le site
    </a>
  </nav>

  <div class="admin-sidebar-footer">
    <div class="admin-sidebar-user">
      <div class="admin-sidebar-avatar">
        <?= strtoupper(mb_substr((string) ($_SESSION['admin_user_name'] ?? 'A'), 0, 1)) ?>
      </div>
      <div class="admin-sidebar-user-info">
        <div class="admin-sidebar-user-name"><?= htmlspecialchars((string) ($_SESSION['admin_user_name'] ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="admin-sidebar-user-email"><?= htmlspecialchars((string) ($_SESSION['admin_user_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>
    <a href="/admin/logout" class="admin-sidebar-logout">
      <i class="fas fa-sign-out-alt"></i> Deconnexion
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="admin-main">
  <header class="admin-topbar">
    <div class="admin-topbar-left">
      <button class="admin-sidebar-toggle" id="sidebar-toggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar-title"><?= htmlspecialchars((string) ($admin_page_title ?? 'Administration'), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="admin-topbar-right">
      <a href="/" class="admin-topbar-link" target="_blank">
        <i class="fas fa-external-link-alt"></i> <span>Voir le site</span>
      </a>
    </div>
  </header>

  <div class="admin-content">
    %%ADMIN_CONTENT%%
  </div>
</div>

<script>
(function() {
  var toggle = document.getElementById('sidebar-toggle');
  var sidebar = document.getElementById('admin-sidebar');
  var overlay = document.getElementById('sidebar-overlay');

  if (!toggle || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', function() {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

  window.addEventListener('resize', function() {
    if (window.innerWidth > 1024) closeSidebar();
  });
})();
</script>

</body>
</html>
