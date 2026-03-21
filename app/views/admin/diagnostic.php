<style>
  .diag-header {
    margin-bottom: 2rem;
  }

  .diag-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .diag-header p {
    color: var(--admin-muted);
    font-size: 0.9rem;
    margin-top: 0.25rem;
  }

  .diag-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .diag-stat-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .diag-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .diag-stat-icon.ok { background: rgba(34, 197, 94, 0.1); color: var(--admin-success); }
  .diag-stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--admin-warning); }
  .diag-stat-icon.error { background: rgba(239, 68, 68, 0.1); color: var(--admin-danger); }
  .diag-stat-icon.total { background: var(--admin-primary-light); color: var(--admin-primary); }

  .diag-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: var(--admin-text);
  }

  .diag-stat-label {
    font-size: 0.78rem;
    color: var(--admin-muted);
    margin-top: 2px;
  }

  .diag-checks {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .diag-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .diag-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    cursor: pointer;
    user-select: none;
    transition: background 0.15s;
  }

  .diag-card-header:hover {
    background: #f8fafc;
  }

  .diag-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .diag-status-dot.ok { background: var(--admin-success); box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15); }
  .diag-status-dot.warning { background: var(--admin-warning); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
  .diag-status-dot.error { background: var(--admin-danger); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }

  .diag-card-title {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--admin-text);
    flex: 1;
  }

  .diag-card-badge {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .diag-card-badge.ok { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
  .diag-card-badge.warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .diag-card-badge.error { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

  .diag-card-toggle {
    color: var(--admin-muted);
    font-size: 0.8rem;
    transition: transform 0.2s;
  }

  .diag-card.open .diag-card-toggle {
    transform: rotate(180deg);
  }

  .diag-card-body {
    display: none;
    padding: 1.25rem;
    background: #fafbfc;
    border-top: 1px solid var(--admin-border);
  }

  .diag-card.open .diag-card-body {
    display: block;
  }

  .diag-detail-row {
    display: flex;
    align-items: baseline;
    padding: 0.4rem 0;
    font-size: 0.88rem;
  }

  .diag-detail-row + .diag-detail-row {
    border-top: 1px solid #f1f5f9;
  }

  .diag-detail-label {
    width: 140px;
    flex-shrink: 0;
    color: var(--admin-muted);
    font-weight: 500;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .diag-detail-value {
    color: var(--admin-text);
    font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace;
    font-size: 0.85rem;
    word-break: break-all;
  }

  .diag-message {
    font-size: 0.88rem;
    color: var(--admin-text);
    line-height: 1.5;
  }

  .diag-message.error { color: var(--admin-danger); }
  .diag-message.warning { color: var(--admin-warning); }

  .diag-table-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.75rem;
  }

  .diag-table-tag {
    background: #fff;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    padding: 0.25rem 0.6rem;
    font-size: 0.8rem;
    font-family: 'SFMono-Regular', 'Consolas', monospace;
    color: var(--admin-text);
  }

  .diag-admin-list {
    margin-top: 0.75rem;
  }

  .diag-admin-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    font-size: 0.88rem;
  }

  .diag-admin-item + .diag-admin-item {
    border-top: 1px solid #f1f5f9;
  }

  .diag-admin-avatar {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: var(--admin-primary-light);
    color: var(--admin-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    flex-shrink: 0;
  }

  .diag-suggestion {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
    font-size: 0.85rem;
    color: #92400e;
  }

  .diag-suggestion i {
    margin-right: 0.4rem;
  }

  .diag-refresh {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.25rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: background 0.15s;
  }

  .diag-refresh:hover {
    background: #6b0f2d;
  }

  @media (max-width: 640px) {
    .diag-stats {
      grid-template-columns: 1fr 1fr;
    }

    .diag-detail-row {
      flex-direction: column;
      gap: 0.2rem;
    }

    .diag-detail-label {
      width: auto;
    }
  }
</style>

<div class="diag-header">
  <h1>
    <i class="fas fa-stethoscope" style="color: var(--admin-primary);"></i>
    Diagnostic syst&egrave;me
  </h1>
  <p>V&eacute;rification de l'&eacute;tat de la configuration et des services</p>
</div>

<!-- Stats Cards -->
<div class="diag-stats">
  <div class="diag-stat-card">
    <div class="diag-stat-icon total"><i class="fas fa-clipboard-check"></i></div>
    <div>
      <div class="diag-stat-value"><?= (int) $totalChecks ?></div>
      <div class="diag-stat-label">V&eacute;rifications</div>
    </div>
  </div>
  <div class="diag-stat-card">
    <div class="diag-stat-icon ok"><i class="fas fa-check-circle"></i></div>
    <div>
      <div class="diag-stat-value"><?= (int) $okCount ?></div>
      <div class="diag-stat-label">OK</div>
    </div>
  </div>
  <div class="diag-stat-card">
    <div class="diag-stat-icon warning"><i class="fas fa-exclamation-triangle"></i></div>
    <div>
      <div class="diag-stat-value"><?= (int) $warningCount ?></div>
      <div class="diag-stat-label">Avertissements</div>
    </div>
  </div>
  <div class="diag-stat-card">
    <div class="diag-stat-icon error"><i class="fas fa-times-circle"></i></div>
    <div>
      <div class="diag-stat-value"><?= (int) $errorCount ?></div>
      <div class="diag-stat-label">Erreurs</div>
    </div>
  </div>
</div>

<!-- Diagnostic Checks -->
<div class="diag-checks">

  <?php foreach ($diagnostics as $key => $check): ?>
  <div class="diag-card <?= $check['status'] !== 'ok' ? 'open' : '' ?>" data-diag="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
    <div class="diag-card-header" onclick="this.parentElement.classList.toggle('open')">
      <span class="diag-status-dot <?= $check['status'] ?>"></span>
      <span class="diag-card-title"><?= $check['label'] ?></span>
      <span class="diag-card-badge <?= $check['status'] ?>">
        <?php if ($check['status'] === 'ok'): ?>OK
        <?php elseif ($check['status'] === 'warning'): ?>Attention
        <?php else: ?>Erreur
        <?php endif; ?>
      </span>
      <i class="fas fa-chevron-down diag-card-toggle"></i>
    </div>
    <div class="diag-card-body">

      <?php if (!empty($check['message'])): ?>
        <p class="diag-message <?= $check['status'] !== 'ok' ? $check['status'] : '' ?>"><?= $check['message'] ?></p>
      <?php endif; ?>

      <?php if (!empty($check['details']) && is_array($check['details'])): ?>
        <?php foreach ($check['details'] as $detailKey => $detailValue): ?>
          <div class="diag-detail-row">
            <span class="diag-detail-label"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $detailKey)), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="diag-detail-value"><?= htmlspecialchars((string) $detailValue, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php // Tables list ?>
      <?php if ($key === 'tables' && !empty($check['list'])): ?>
        <div class="diag-table-list">
          <?php foreach ($check['list'] as $table): ?>
            <span class="diag-table-tag"><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php // Admin users list ?>
      <?php if ($key === 'admin_users'): ?>
        <?php if (!empty($check['columns'])): ?>
          <div class="diag-detail-row">
            <span class="diag-detail-label">Colonnes</span>
            <span class="diag-detail-value"><?= htmlspecialchars(implode(', ', $check['columns']), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        <?php endif; ?>

        <?php if (isset($check['has_login_code']) && !$check['has_login_code']): ?>
          <div class="diag-suggestion">
            <i class="fas fa-exclamation-triangle"></i>
            Colonne <code>login_code</code> manquante dans admin_users
          </div>
        <?php endif; ?>

        <?php if (!empty($check['admin_count'])): ?>
          <div class="diag-detail-row">
            <span class="diag-detail-label">Administrateurs</span>
            <span class="diag-detail-value"><?= (int) $check['admin_count'] ?></span>
          </div>
        <?php endif; ?>

        <?php if (!empty($check['admins'])): ?>
          <div class="diag-admin-list">
            <?php foreach ($check['admins'] as $admin): ?>
              <div class="diag-admin-item">
                <div class="diag-admin-avatar">
                  <?= strtoupper(mb_substr($admin['name'] ?? $admin['email'], 0, 1)) ?>
                </div>
                <div>
                  <strong><?= htmlspecialchars($admin['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                  <span style="color: var(--admin-muted); font-size: 0.82rem; margin-left: 0.5rem;"><?= htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php elseif (isset($check['admin_count']) && $check['admin_count'] === 0): ?>
          <div class="diag-suggestion">
            <i class="fas fa-exclamation-triangle"></i>
            Aucun administrateur &mdash; Ex&eacute;cutez <code>setup-admin.php</code>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php // PHP missing extensions ?>
      <?php if ($key === 'php' && !empty($check['details']['missing'])): ?>
        <div class="diag-suggestion">
          <i class="fas fa-exclamation-triangle"></i>
          Extensions manquantes : <code><?= htmlspecialchars($check['details']['missing'], ENT_QUOTES, 'UTF-8') ?></code>
        </div>
      <?php endif; ?>

      <?php // SMTP suggestions ?>
      <?php if (!empty($check['suggestions'])): ?>
        <?php foreach ($check['suggestions'] as $suggestion): ?>
          <div class="diag-suggestion">
            <i class="fas fa-lightbulb"></i>
            <?= htmlspecialchars($suggestion, ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </div>
  <?php endforeach; ?>

</div>

<!-- Refresh -->
<div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 1rem;">
  <a href="/admin/diagnostic" class="diag-refresh">
    <i class="fas fa-sync-alt"></i> Relancer le diagnostic
  </a>
  <span style="font-size: 0.82rem; color: var(--admin-muted);">
    Derni&egrave;re v&eacute;rification : <?= date('d/m/Y H:i:s') ?>
  </span>
</div>
