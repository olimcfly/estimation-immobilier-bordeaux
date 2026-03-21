<style>
  .dbdiag-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .dbdiag-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .dbdiag-header h1 i { color: var(--admin-primary); }

  .dbdiag-actions {
    display: flex;
    gap: 0.75rem;
  }

  .dbdiag-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.2rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: all 0.15s;
    border: 1px solid var(--admin-border);
    background: var(--admin-surface);
    color: var(--admin-text);
  }

  .dbdiag-btn:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
  }

  .dbdiag-btn-primary {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  .dbdiag-btn-primary:hover {
    background: #6b0f2d;
    color: #fff;
  }

  /* Summary cards */
  .dbdiag-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }

  @media (max-width: 768px) {
    .dbdiag-summary { grid-template-columns: repeat(2, 1fr); }
  }

  .dbdiag-stat {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    text-align: center;
  }

  .dbdiag-stat-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
  }

  .dbdiag-stat-number.green { color: #16a34a; }
  .dbdiag-stat-number.orange { color: #d97706; }
  .dbdiag-stat-number.red { color: #dc2626; }
  .dbdiag-stat-number.blue { color: #2563eb; }

  .dbdiag-stat-label {
    font-size: 0.82rem;
    color: var(--admin-muted);
    margin-top: 0.35rem;
    font-weight: 500;
  }

  /* Category sections */
  .dbdiag-category {
    margin-bottom: 2rem;
  }

  .dbdiag-category-title {
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0.75rem 0;
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--admin-primary);
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Page cards */
  .dbdiag-page {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    margin-bottom: 1rem;
    overflow: hidden;
  }

  .dbdiag-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.25rem;
    cursor: pointer;
    transition: background 0.15s;
    gap: 1rem;
  }

  .dbdiag-page-header:hover {
    background: #f8fafc;
  }

  .dbdiag-page-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
  }

  .dbdiag-page-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
  }

  .dbdiag-page-icon.ok { background: rgba(34,197,94,0.1); color: #16a34a; }
  .dbdiag-page-icon.warning { background: rgba(245,158,11,0.1); color: #d97706; }
  .dbdiag-page-icon.error { background: rgba(239,68,68,0.1); color: #dc2626; }
  .dbdiag-page-icon.no_db { background: rgba(148,163,184,0.15); color: #94a3b8; }

  .dbdiag-page-info {
    min-width: 0;
  }

  .dbdiag-page-name {
    font-weight: 600;
    font-size: 0.92rem;
    color: var(--admin-text);
  }

  .dbdiag-page-route {
    font-size: 0.78rem;
    color: var(--admin-muted);
    font-family: monospace;
  }

  .dbdiag-page-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }

  .dbdiag-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
  }

  .dbdiag-badge-ok { background: rgba(34,197,94,0.1); color: #16a34a; }
  .dbdiag-badge-warning { background: rgba(245,158,11,0.1); color: #d97706; }
  .dbdiag-badge-error { background: rgba(239,68,68,0.1); color: #dc2626; }
  .dbdiag-badge-static { background: rgba(148,163,184,0.1); color: #64748b; }

  .dbdiag-chevron {
    color: var(--admin-muted);
    transition: transform 0.2s;
    font-size: 0.8rem;
  }

  .dbdiag-page.open .dbdiag-chevron {
    transform: rotate(90deg);
  }

  /* Page detail */
  .dbdiag-page-detail {
    display: none;
    border-top: 1px solid var(--admin-border);
    padding: 1.25rem;
  }

  .dbdiag-page.open .dbdiag-page-detail {
    display: block;
  }

  .dbdiag-table-section {
    margin-bottom: 1.25rem;
  }

  .dbdiag-table-section:last-child {
    margin-bottom: 0;
  }

  .dbdiag-table-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.65rem;
  }

  .dbdiag-table-name {
    font-weight: 700;
    font-size: 0.9rem;
    font-family: monospace;
    color: var(--admin-text);
  }

  .dbdiag-table-desc {
    font-size: 0.82rem;
    color: var(--admin-muted);
  }

  .dbdiag-table-status {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
  }

  .dbdiag-table-status.ok { background: rgba(34,197,94,0.1); color: #16a34a; }
  .dbdiag-table-status.warning { background: rgba(245,158,11,0.1); color: #d97706; }
  .dbdiag-table-status.error { background: rgba(239,68,68,0.1); color: #dc2626; }

  /* Column grid */
  .dbdiag-cols {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0.4rem;
  }

  .dbdiag-col {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.65rem;
    border-radius: 4px;
    font-size: 0.82rem;
    background: #f8fafc;
  }

  .dbdiag-col-icon {
    font-size: 0.7rem;
    flex-shrink: 0;
  }

  .dbdiag-col-icon.ok { color: #16a34a; }
  .dbdiag-col-icon.missing { color: #dc2626; }

  .dbdiag-col-name {
    font-family: monospace;
    font-weight: 600;
    color: var(--admin-text);
  }

  .dbdiag-col-type {
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-left: auto;
    white-space: nowrap;
  }

  .dbdiag-col.missing {
    background: rgba(239,68,68,0.05);
    border: 1px dashed rgba(239,68,68,0.3);
  }

  /* Missing table alert */
  .dbdiag-missing-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    background: rgba(239,68,68,0.05);
    border: 1px solid rgba(239,68,68,0.15);
    border-radius: 6px;
    padding: 0.85rem 1rem;
    font-size: 0.85rem;
    color: #dc2626;
  }

  .dbdiag-missing-alert i {
    margin-top: 0.1rem;
    flex-shrink: 0;
  }

  .dbdiag-static-msg {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--admin-muted);
    padding: 0.5rem 0;
  }

  /* No DB connection alert */
  .dbdiag-no-connection {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: rgba(239,68,68,0.05);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    font-size: 0.9rem;
    color: #dc2626;
  }

  .dbdiag-no-connection i {
    font-size: 1.2rem;
    margin-top: 0.1rem;
  }
</style>

<div class="dbdiag-header">
  <h1><i class="fas fa-database"></i> Diagnostic Base de Donn&eacute;es</h1>
  <div class="dbdiag-actions">
    <a href="/admin/diagnostic/database" class="dbdiag-btn dbdiag-btn-primary"><i class="fas fa-sync-alt"></i> Rafra&icirc;chir</a>
    <a href="/admin/diagnostic" class="dbdiag-btn"><i class="fas fa-stethoscope"></i> Diagnostic syst&egrave;me</a>
    <a href="/admin/database" class="dbdiag-btn"><i class="fas fa-terminal"></i> Console SQL</a>
  </div>
</div>

<?php if (!$dbConnected): ?>
  <div class="dbdiag-no-connection">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
      <strong>Connexion &agrave; la base de donn&eacute;es impossible.</strong><br>
      V&eacute;rifiez votre fichier <strong>.env</strong> et la configuration DB dans <a href="/admin/diagnostic">le diagnostic syst&egrave;me</a>.
    </div>
  </div>
<?php endif; ?>

<!-- Summary -->
<div class="dbdiag-summary">
  <div class="dbdiag-stat">
    <div class="dbdiag-stat-number blue"><?= $totalPages ?></div>
    <div class="dbdiag-stat-label">Pages analys&eacute;es</div>
  </div>
  <div class="dbdiag-stat">
    <div class="dbdiag-stat-number green"><?= $totalOk ?></div>
    <div class="dbdiag-stat-label">Tables OK</div>
  </div>
  <div class="dbdiag-stat">
    <div class="dbdiag-stat-number orange"><?= $totalWarning ?></div>
    <div class="dbdiag-stat-label">Colonnes manquantes</div>
  </div>
  <div class="dbdiag-stat">
    <div class="dbdiag-stat-number red"><?= $totalError ?></div>
    <div class="dbdiag-stat-label">Tables absentes</div>
  </div>
</div>

<!-- Pages by category -->
<?php foreach ($categories as $categoryName => $pages): ?>
<div class="dbdiag-category">
  <div class="dbdiag-category-title">
    <?php
      $catIcons = [
        'Pages publiques' => 'fa-globe',
        'Estimation & Leads' => 'fa-calculator',
        'Newsletter' => 'fa-newspaper',
        'Blog' => 'fa-blog',
        'Actualit&eacute;s' => 'fa-rss',
        'Admin CRM' => 'fa-users-cog',
        'Achats' => 'fa-shopping-cart',
        'Partenaires' => 'fa-handshake',
        'Authentification' => 'fa-lock',
        'Landing Pages' => 'fa-bullseye',
        'Outils Admin' => 'fa-tools',
      ];
      $catIcon = $catIcons[$categoryName] ?? 'fa-folder';
    ?>
    <i class="fas <?= $catIcon ?>"></i>
    <?= $categoryName ?>
    <span style="font-size: 0.8rem; font-weight: 400; color: var(--admin-muted); margin-left: 0.5rem;">(<?= count($pages) ?> page<?= count($pages) > 1 ? 's' : '' ?>)</span>
  </div>

  <?php foreach ($pages as $pageKey => $page): ?>
    <?php
      $status = $page['status'];
      $hasTables = !empty($page['tables']);
      $statusIcon = match($status) {
        'ok' => 'fa-check-circle',
        'warning' => 'fa-exclamation-triangle',
        'error' => 'fa-times-circle',
        default => 'fa-minus-circle',
      };
      $badgeClass = match($status) {
        'ok' => 'dbdiag-badge-ok',
        'warning' => 'dbdiag-badge-warning',
        'error' => 'dbdiag-badge-error',
        default => 'dbdiag-badge-static',
      };
      $badgeText = match($status) {
        'ok' => 'OK',
        'warning' => 'Colonnes manquantes',
        'error' => 'Table(s) absente(s)',
        default => 'Statique',
      };

      // Count tables for this page
      $tableNames = $hasTables ? array_keys($page['tables']) : [];
    ?>
    <div class="dbdiag-page" id="page-<?= $pageKey ?>">
      <div class="dbdiag-page-header" onclick="this.parentElement.classList.toggle('open')">
        <div class="dbdiag-page-left">
          <div class="dbdiag-page-icon <?= $status ?>">
            <i class="fas <?= $page['icon'] ?>"></i>
          </div>
          <div class="dbdiag-page-info">
            <div class="dbdiag-page-name"><?= $page['label'] ?></div>
            <div class="dbdiag-page-route"><?= e($page['route']) ?></div>
          </div>
        </div>
        <div class="dbdiag-page-right">
          <?php if ($hasTables): ?>
            <?php foreach ($tableNames as $tn): ?>
              <span style="font-size: 0.72rem; background: #f1f5f9; padding: 0.15rem 0.5rem; border-radius: 4px; font-family: monospace; color: var(--admin-muted);"><?= e($tn) ?></span>
            <?php endforeach; ?>
          <?php endif; ?>
          <span class="dbdiag-badge <?= $badgeClass ?>">
            <i class="fas <?= $statusIcon ?>" style="font-size: 0.65rem;"></i>
            <?= $badgeText ?>
          </span>
          <i class="fas fa-chevron-right dbdiag-chevron"></i>
        </div>
      </div>

      <div class="dbdiag-page-detail">
        <?php if (!$hasTables): ?>
          <div class="dbdiag-static-msg">
            <i class="fas fa-info-circle"></i>
            <?= $page['message'] ?? 'Cette page ne n&eacute;cessite aucune table en base de donn&eacute;es.' ?>
          </div>
        <?php else: ?>
          <?php foreach ($page['tables'] as $tableName => $tableInfo): ?>
            <div class="dbdiag-table-section">
              <div class="dbdiag-table-header">
                <span class="dbdiag-table-name"><?= e($tableName) ?></span>
                <?php
                  $tStatus = $tableInfo['status'];
                  $tStatusText = match($tStatus) {
                    'ok' => 'Connect&eacute;e',
                    'warning' => 'Colonnes manquantes',
                    'error' => 'Table absente',
                    default => '',
                  };
                  $tStatusIcon = match($tStatus) {
                    'ok' => 'fa-check',
                    'warning' => 'fa-exclamation-triangle',
                    'error' => 'fa-times',
                    default => '',
                  };
                ?>
                <span class="dbdiag-table-status <?= $tStatus ?>">
                  <i class="fas <?= $tStatusIcon ?>"></i> <?= $tStatusText ?>
                </span>
                <?php if ($tableInfo['description']): ?>
                  <span class="dbdiag-table-desc">&mdash; <?= $tableInfo['description'] ?></span>
                <?php endif; ?>
              </div>

              <?php if (!$tableInfo['exists']): ?>
                <div class="dbdiag-missing-alert">
                  <i class="fas fa-exclamation-triangle"></i>
                  <div>
                    La table <strong><?= e($tableName) ?></strong> n'existe pas dans la base de donn&eacute;es.<br>
                    <strong><?= count($tableInfo['missing_columns']) ?> colonnes &agrave; cr&eacute;er :</strong>
                    <code><?= e(implode(', ', $tableInfo['missing_columns'])) ?></code><br>
                    <em>Importez <strong>database/schema.sql</strong> ou la migration correspondante.</em>
                  </div>
                </div>
              <?php else: ?>
                <?php if (!empty($tableInfo['missing_columns'])): ?>
                  <div class="dbdiag-missing-alert" style="background: rgba(245,158,11,0.05); border-color: rgba(245,158,11,0.2); color: #d97706; margin-bottom: 0.75rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong><?= count($tableInfo['missing_columns']) ?> colonne(s) manquante(s) :</strong> <code><?= e(implode(', ', $tableInfo['missing_columns'])) ?></code></span>
                  </div>
                <?php endif; ?>

                <div class="dbdiag-cols">
                  <?php foreach ($tableInfo['columns'] as $colName => $colData): ?>
                    <?php $colExists = $colData['exists']; ?>
                    <div class="dbdiag-col <?= $colExists ? '' : 'missing' ?>">
                      <i class="fas <?= $colExists ? 'fa-check-circle' : 'fa-times-circle' ?> dbdiag-col-icon <?= $colExists ? 'ok' : 'missing' ?>"></i>
                      <span class="dbdiag-col-name"><?= e($colName) ?></span>
                      <span class="dbdiag-col-type">
                        <?php if ($colExists && $colData['actual_type']): ?>
                          <?= e($colData['actual_type']) ?>
                        <?php else: ?>
                          <?= e($colData['expected']) ?>
                        <?php endif; ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>

<script>
// Auto-open pages with errors or warnings
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.dbdiag-page').forEach(function(page) {
    var badge = page.querySelector('.dbdiag-badge-error, .dbdiag-badge-warning');
    if (badge) {
      page.classList.add('open');
    }
  });
});
</script>
