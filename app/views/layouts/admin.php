<?php
/**
 * Admin CRM Layout - Sidebar + Content area
 * Variables: $page_title, $admin_page, $pageContent
 */
$currentPage = $admin_page ?? '';
$adminName = $_SESSION['admin_user_name'] ?? 'Admin';
$adminEmail = $_SESSION['admin_user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= htmlspecialchars($page_title ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --admin-sidebar-w: 260px;
      --admin-bg: #f1f5f9;
      --admin-sidebar-bg: #0f172a;
      --admin-sidebar-hover: #1e293b;
      --admin-sidebar-active: #8B1538;
      --admin-sidebar-text: #94a3b8;
      --admin-sidebar-text-active: #ffffff;
      --admin-topbar-bg: #ffffff;
      --admin-topbar-h: 60px;
      --admin-text: #1e293b;
      --admin-muted: #64748b;
      --admin-border: #e2e8f0;
      --admin-primary: #8B1538;
      --admin-primary-light: rgba(139, 21, 56, 0.1);
      --admin-success: #22c55e;
      --admin-warning: #f59e0b;
      --admin-danger: #ef4444;
      --admin-info: #3b82f6;
      --admin-surface: #ffffff;
      --admin-radius: 8px;
    }

    body {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--admin-bg);
      color: var(--admin-text);
      min-height: 100vh;
      display: flex;
    }

    /* ========================= */
    /* SIDEBAR                   */
    /* ========================= */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: var(--admin-sidebar-w);
      background: var(--admin-sidebar-bg);
      display: flex;
      flex-direction: column;
      z-index: 100;
      transition: transform 0.3s ease;
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      text-decoration: none;
    }

    .sidebar-brand-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--admin-primary), #C41E3A);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .sidebar-brand-text {
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
      line-height: 1.2;
    }

    .sidebar-brand-text small {
      display: block;
      color: var(--admin-sidebar-text);
      font-weight: 400;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 2px;
    }

    .sidebar-nav {
      flex: 1;
      padding: 1rem 0;
      overflow-y: auto;
    }

    .sidebar-section {
      padding: 0 1rem;
      margin-bottom: 0.5rem;
    }

    .sidebar-section-title {
      color: var(--admin-sidebar-text);
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 0.75rem 0.75rem 0.5rem;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0.75rem;
      color: var(--admin-sidebar-text);
      text-decoration: none;
      border-radius: 6px;
      font-size: 0.88rem;
      font-weight: 500;
      transition: all 0.15s ease;
      margin-bottom: 2px;
    }

    .sidebar-link:hover {
      background: var(--admin-sidebar-hover);
      color: #fff;
    }

    .sidebar-link.active {
      background: var(--admin-sidebar-active);
      color: #fff;
    }

    .sidebar-link i {
      width: 20px;
      text-align: center;
      font-size: 0.9rem;
    }

    .sidebar-link .badge {
      margin-left: auto;
      background: var(--admin-primary);
      color: #fff;
      font-size: 0.7rem;
      font-weight: 600;
      padding: 2px 7px;
      border-radius: 10px;
    }

    .sidebar-link.active .badge {
      background: rgba(255,255,255,0.2);
    }

    .sidebar-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(255,255,255,0.08);
    }

    .sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .sidebar-user-avatar {
      width: 34px;
      height: 34px;
      background: var(--admin-sidebar-hover);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--admin-sidebar-text);
      font-size: 0.85rem;
    }

    .sidebar-user-info {
      flex: 1;
      min-width: 0;
    }

    .sidebar-user-name {
      color: #fff;
      font-size: 0.85rem;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar-user-role {
      color: var(--admin-sidebar-text);
      font-size: 0.7rem;
    }

    /* ========================= */
    /* MAIN CONTENT              */
    /* ========================= */
    .admin-main {
      margin-left: var(--admin-sidebar-w);
      flex: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .admin-topbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: var(--admin-topbar-bg);
      height: var(--admin-topbar-h);
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .topbar-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      color: var(--admin-muted);
      font-size: 1.2rem;
    }

    .topbar-breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      color: var(--admin-muted);
    }

    .topbar-breadcrumb a {
      color: var(--admin-muted);
      text-decoration: none;
    }

    .topbar-breadcrumb a:hover {
      color: var(--admin-primary);
    }

    .topbar-breadcrumb .separator {
      color: var(--admin-border);
    }

    .topbar-breadcrumb .current {
      color: var(--admin-text);
      font-weight: 600;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .topbar-link {
      color: var(--admin-muted);
      text-decoration: none;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      transition: color 0.15s;
    }

    .topbar-link:hover {
      color: var(--admin-primary);
    }

    .admin-content {
      flex: 1;
      padding: 2rem;
    }

    /* ========================= */
    /* MOBILE OVERLAY            */
    /* ========================= */
    .admin-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 99;
    }

    /* ========================= */
    /* RESPONSIVE                */
    /* ========================= */
    @media (max-width: 1024px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.open {
        transform: translateX(0);
      }

      .admin-overlay.open {
        display: block;
      }

      .admin-main {
        margin-left: 0;
      }

      .topbar-toggle {
        display: block;
      }

      .admin-content {
        padding: 1.5rem 1rem;
      }
    }

    @media (max-width: 640px) {
      .admin-content {
        padding: 1rem 0.75rem;
      }
    }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="admin-sidebar" id="adminSidebar">
  <a href="/admin/leads" class="sidebar-brand">
    <div class="sidebar-brand-icon"><i class="fas fa-building"></i></div>
    <div class="sidebar-brand-text">
      EIB Admin
      <small>Back-office CRM</small>
    </div>
  </a>

  <nav class="sidebar-nav">
    <div class="sidebar-section">
      <div class="sidebar-section-title">Principal</div>
      <a href="/admin/dashboard" class="sidebar-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i> Tableau de Bord
      </a>
      <a href="/admin/leads" class="sidebar-link <?= $currentPage === 'leads' ? 'active' : '' ?>">
        <i class="fas fa-users"></i> Leads
        <?php if (!empty($leadCount)): ?>
          <span class="badge"><?= (int) $leadCount ?></span>
        <?php endif; ?>
      </a>
      <a href="/admin/funnel" class="sidebar-link <?= $currentPage === 'funnel' ? 'active' : '' ?>">
        <i class="fas fa-filter"></i> Entonnoir
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-title">Commercial</div>
      <a href="/admin/portfolio" class="sidebar-link <?= $currentPage === 'portfolio' ? 'active' : '' ?>">
        <i class="fas fa-briefcase"></i> Portefeuille
      </a>
      <a href="/admin/partenaires" class="sidebar-link <?= $currentPage === 'partenaires' ? 'active' : '' ?>">
        <i class="fas fa-handshake"></i> Partenaires
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-title">Contenu</div>
      <a href="/blog" class="sidebar-link <?= $currentPage === 'blog' ? 'active' : '' ?>">
        <i class="fas fa-newspaper"></i> Articles
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-title">Communication</div>
      <a href="/admin/emails" class="sidebar-link <?= $currentPage === 'emails' ? 'active' : '' ?>">
        <i class="fas fa-envelope"></i> Emails
      </a>
      <a href="/admin/sequences" class="sidebar-link <?= $currentPage === 'sequences' ? 'active' : '' ?>">
        <i class="fas fa-project-diagram"></i> S&eacute;quences
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-title">Outils</div>
      <a href="/admin/database" class="sidebar-link <?= $currentPage === 'database' ? 'active' : '' ?>">
        <i class="fas fa-database"></i> Base de donn&eacute;es
      </a>
      <a href="/admin/diagnostic" class="sidebar-link <?= $currentPage === 'diagnostic' ? 'active' : '' ?>">
        <i class="fas fa-stethoscope"></i> Diagnostic
      </a>
      <a href="/" class="sidebar-link" target="_blank">
        <i class="fas fa-external-link-alt"></i> Voir le site
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar"><i class="fas fa-user"></i></div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="sidebar-user-role">Administrateur</div>
      </div>
    </div>
  </div>
</aside>

<!-- OVERLAY MOBILE -->
<div class="admin-overlay" id="adminOverlay"></div>

<!-- MAIN -->
<div class="admin-main">
  <header class="admin-topbar">
    <div class="topbar-left">
      <button class="topbar-toggle" id="sidebarToggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
      <div class="topbar-breadcrumb">
        <a href="/admin/leads">Admin</a>
        <span class="separator">/</span>
        <span class="current"><?= htmlspecialchars($breadcrumb ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    </div>
    <div class="topbar-right">
      <a href="/" class="topbar-link" target="_blank"><i class="fas fa-external-link-alt"></i> Site</a>
    </div>
  </header>

  <div class="admin-content">
    <?= $pageContent ?? '' ?>
  </div>
</div>

<script>
(function() {
  var toggle = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('adminSidebar');
  var overlay = document.getElementById('adminOverlay');

  if (!toggle || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('open');
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
  }

  toggle.addEventListener('click', function() {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);
})();
</script>
</body>
</html>
