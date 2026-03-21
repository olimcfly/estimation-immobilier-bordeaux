<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">Actualites</h1>
    <p class="admin-page-desc">Gerez les actualites immobilieres. Recherchez via Perplexity, generez avec IA ou creez manuellement.</p>
  </div>
  <a href="/admin/actualites/create" class="admin-btn admin-btn-primary"><i class="fas fa-plus"></i> Nouvelle actualite</a>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<!-- AI Generation Panel -->
<div class="admin-card ai-panel">
  <div class="admin-card-header">
    <h2><i class="fas fa-robot"></i> Generation automatique IA</h2>
    <span class="admin-badge" style="background: rgba(139,21,56,0.1); color: var(--admin-primary);">Pipeline complet</span>
  </div>
  <div class="admin-card-body">
    <div class="ai-pipeline-steps">
      <div class="ai-step"><span class="ai-step-num">1</span> Recherche Perplexity</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">2</span> Selection OpenAI</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">3</span> Redaction article</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">4</span> Image IA</div>
    </div>

    <form method="post" action="/admin/actualites/generate" class="admin-form-inline" id="form-generate">
      <div class="admin-form-group" style="flex:1;">
        <input type="text" name="query" class="admin-input" placeholder="Theme de recherche (optionnel, ex: prix immobilier Nandy 2026)" value="">
      </div>
      <button type="submit" class="admin-btn admin-btn-primary" id="btn-generate">
        <i class="fas fa-magic"></i> Generer un article complet
      </button>
    </form>

    <div style="margin-top: 1rem; border-top: 1px solid var(--admin-border); padding-top: 1rem;">
      <form method="post" action="/admin/actualites/search" class="admin-form-inline" id="form-search">
        <div class="admin-form-group" style="flex:1;">
          <input type="text" name="query" class="admin-input" placeholder="Rechercher des idees d'articles (Perplexity)...">
        </div>
        <button type="submit" class="admin-btn admin-btn-secondary" id="btn-search">
          <i class="fas fa-search"></i> Rechercher idees
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Stats Bar -->
<?php
  $countPublished = 0;
  $countDraft = 0;
  $countAi = 0;
  $countCron = 0;
  foreach ($actualites as $a) {
      if (($a['status'] ?? '') === 'published') $countPublished++;
      else $countDraft++;
      if (($a['generated_by'] ?? '') === 'ai') $countAi++;
      if (($a['generated_by'] ?? '') === 'cron') $countCron++;
  }
?>
<div class="stats-bar">
  <div class="stat-item">
    <span class="stat-value"><?= count($actualites) ?></span>
    <span class="stat-label">Total</span>
  </div>
  <div class="stat-item stat-success">
    <span class="stat-value"><?= $countPublished ?></span>
    <span class="stat-label">Publies</span>
  </div>
  <div class="stat-item stat-warning">
    <span class="stat-value"><?= $countDraft ?></span>
    <span class="stat-label">Brouillons</span>
  </div>
  <div class="stat-item stat-ai">
    <span class="stat-value"><?= $countAi + $countCron ?></span>
    <span class="stat-label">Generes IA</span>
  </div>
</div>

<!-- Articles List -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-newspaper"></i> Toutes les actualites</h2>
    <span class="admin-badge"><?= count($actualites) ?> articles</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Titre</th>
            <th>Statut</th>
            <th>Source</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($actualites)): ?>
            <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--admin-muted);">Aucune actualite. Cliquez sur "Generer" pour commencer.</td></tr>
          <?php else: ?>
            <?php foreach ($actualites as $actu): ?>
              <tr>
                <td style="width: 60px;">
                  <?php if (!empty($actu['image_url'])): ?>
                    <img src="<?= e((string) $actu['image_url']) ?>" alt="" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                  <?php else: ?>
                    <span style="color: var(--admin-muted); font-size: 0.8rem;"><i class="fas fa-image"></i></span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= e((string) $actu['title']) ?></strong>
                </td>
                <td>
                  <?php if ($actu['status'] === 'published'): ?>
                    <span class="admin-status admin-status-success">Publie</span>
                  <?php else: ?>
                    <span class="admin-status admin-status-warning">Brouillon</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $genIcon = match($actu['generated_by'] ?? 'manual') {
                      'ai' => '<i class="fas fa-robot" title="IA"></i> IA',
                      'cron' => '<i class="fas fa-clock" title="Cron"></i> Auto',
                      default => '<i class="fas fa-pen" title="Manuel"></i> Manuel',
                    };
                  ?>
                  <span style="font-size: 0.85rem;"><?= $genIcon ?></span>
                </td>
                <td style="font-size: 0.85rem; color: var(--admin-muted);">
                  <?= e(date('d/m/Y', strtotime((string) ($actu['published_at'] ?? $actu['created_at'])))) ?>
                </td>
                <td>
                  <div class="admin-actions">
                    <a href="/admin/actualites/edit/<?= (int) $actu['id'] ?>" class="admin-btn admin-btn-sm admin-btn-ghost" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="/actualites/<?= e((string) $actu['slug']) ?>" class="admin-btn admin-btn-sm admin-btn-ghost" target="_blank" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
                    <form method="post" action="/admin/actualites/delete/<?= (int) $actu['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cette actualite ?');">
                      <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Cron Logs -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-history"></i> Historique des generations automatiques</h2>
    <span class="admin-badge"><?= count($cronLogs) ?> entrees</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <?php if (empty($cronLogs)): ?>
      <div style="text-align: center; padding: 2rem; color: var(--admin-muted);">
        <i class="fas fa-clock" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.3;"></i>
        Aucune generation enregistree. Utilisez le bouton "Generer" ou configurez le cron.
        <div style="margin-top: 0.75rem; font-size: 0.8rem; font-family: monospace; background: var(--admin-bg); padding: 0.5rem 1rem; border-radius: 4px; display: inline-block;">
          0 8 * * 1 php cron/generate-actualite.php
        </div>
      </div>
    <?php else: ?>
      <div class="admin-table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Requete</th>
              <th>Articles trouves</th>
              <th>Article publie</th>
              <th>Statut</th>
              <th>Erreur</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cronLogs as $log): ?>
              <tr>
                <td style="font-size: 0.85rem;"><?= e(date('d/m/Y H:i', strtotime((string) $log['created_at']))) ?></td>
                <td style="font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e((string) $log['query_used']) ?>">
                  <?= e(mb_substr((string) $log['query_used'], 0, 60)) ?>
                  <?= mb_strlen((string) $log['query_used']) > 60 ? '...' : '' ?>
                </td>
                <td style="text-align: center;"><?= (int) $log['articles_found'] ?></td>
                <td style="text-align: center;">
                  <?php if (!empty($log['article_published_id'])): ?>
                    <a href="/admin/actualites/edit/<?= (int) $log['article_published_id'] ?>" class="admin-btn admin-btn-sm admin-btn-ghost" title="Voir l'article">
                      #<?= (int) $log['article_published_id'] ?> <i class="fas fa-external-link-alt"></i>
                    </a>
                  <?php else: ?>
                    <span style="color: var(--admin-muted);">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($log['status'] === 'success'): ?>
                    <span class="admin-status admin-status-success"><i class="fas fa-check"></i> OK</span>
                  <?php else: ?>
                    <span class="admin-status admin-status-danger"><i class="fas fa-times"></i> Erreur</span>
                  <?php endif; ?>
                </td>
                <td style="font-size: 0.8rem; color: #dc2626; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e((string) ($log['error_message'] ?? '')) ?>">
                  <?= e((string) ($log['error_message'] ?? '')) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .admin-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
  .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; }
  .admin-page-desc { font-size: 0.9rem; color: var(--admin-muted); margin-top: 0.25rem; }
  .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); margin-bottom: 1.5rem; overflow: hidden; }
  .admin-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); }
  .admin-card-header h2 { font-size: 1rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
  .admin-card-body { padding: 1.5rem; }
  .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
  .admin-btn-primary { background: var(--admin-primary); color: #fff; }
  .admin-btn-primary:hover { opacity: 0.9; }
  .admin-btn-secondary { background: var(--admin-bg); color: var(--admin-text); border: 1px solid var(--admin-border); }
  .admin-btn-secondary:hover { background: var(--admin-border); }
  .admin-btn-ghost { background: transparent; color: var(--admin-muted); padding: 0.4rem 0.6rem; }
  .admin-btn-ghost:hover { color: var(--admin-primary); background: rgba(139,21,56,0.06); }
  .admin-btn-danger { background: transparent; color: #dc2626; padding: 0.4rem 0.6rem; }
  .admin-btn-danger:hover { background: rgba(239, 68, 68, 0.1); }
  .admin-btn-sm { padding: 0.35rem 0.5rem; font-size: 0.8rem; }
  .admin-btn:disabled { opacity: 0.6; cursor: not-allowed; }
  .admin-form-inline { display: flex; gap: 0.75rem; align-items: flex-end; }
  .admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; background: #fff; }
  .admin-input:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(139,21,56,0.1); }
  .admin-table-responsive { overflow-x: auto; }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); background: var(--admin-bg); }
  .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-border); font-size: 0.9rem; }
  .admin-table tbody tr:hover { background: rgba(0,0,0,0.02); }
  .admin-status { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
  .admin-status-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
  .admin-status-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .admin-status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
  .admin-badge { background: var(--admin-bg); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--admin-muted); font-weight: 600; }
  .admin-actions { display: flex; gap: 0.25rem; }
  .admin-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
  .admin-alert-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
  .admin-alert-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }

  /* AI Pipeline Steps */
  .ai-pipeline-steps { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.25rem; padding: 1rem; background: var(--admin-bg); border-radius: 8px; flex-wrap: wrap; }
  .ai-step { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--admin-text); }
  .ai-step-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: var(--admin-primary); color: #fff; font-size: 0.7rem; font-weight: 700; }
  .ai-step-arrow { color: var(--admin-muted); font-size: 0.7rem; }

  /* Stats Bar */
  .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .stat-item { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem 1.25rem; text-align: center; }
  .stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: var(--admin-text); }
  .stat-label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); margin-top: 0.15rem; }
  .stat-success .stat-value { color: #16a34a; }
  .stat-warning .stat-value { color: #d97706; }
  .stat-ai .stat-value { color: var(--admin-primary); }

  /* Loading spinner */
  .btn-loading .fa-magic, .btn-loading .fa-search { display: none; }
  .btn-loading::before {
    content: '';
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    flex-shrink: 0;
  }
  .admin-btn-secondary.btn-loading::before {
    border-color: rgba(0,0,0,0.15);
    border-top-color: var(--admin-text);
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  @media (max-width: 768px) {
    .admin-form-inline { flex-direction: column; }
    .admin-page-header { flex-direction: column; }
    .ai-pipeline-steps { gap: 0.25rem; }
    .ai-step-arrow { display: none; }
  }
</style>

<script>
(function() {
  function setupLoadingForm(formId, btnId, loadingText) {
    var form = document.getElementById(formId);
    var btn = document.getElementById(btnId);
    if (!form || !btn) return;
    form.addEventListener('submit', function() {
      btn.classList.add('btn-loading');
      btn.disabled = true;
      var textNode = btn.lastChild;
      if (textNode && textNode.nodeType === 3) {
        textNode.textContent = ' ' + loadingText;
      }
    });
  }
  setupLoadingForm('form-generate', 'btn-generate', 'Generation en cours...');
  setupLoadingForm('form-search', 'btn-search', 'Recherche en cours...');
})();
</script>
