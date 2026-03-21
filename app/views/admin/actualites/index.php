<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">Actualités</h1>
    <p class="admin-page-desc">Gérez les actualités immobilières. Recherchez via Perplexity, générez avec IA ou créez manuellement.</p>
  </div>
  <a href="/admin/actualites/create" class="admin-btn admin-btn-primary"><i class="fas fa-plus"></i> Nouvelle actualité</a>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<!-- AI Generation Panel -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-robot"></i> Génération automatique IA</h2>
  </div>
  <div class="admin-card-body">
    <p style="margin-bottom: 1rem; color: var(--admin-muted); font-size: 0.9rem;">
      Recherche Perplexity + rédaction OpenAI + génération d'image automatique. Un article complet en un clic.
    </p>
    <form method="post" action="/admin/actualites/generate" class="admin-form-inline">
      <div class="admin-form-group" style="flex:1;">
        <input type="text" name="query" class="admin-input" placeholder="Thème de recherche (optionnel, ex: prix immobilier Bordeaux 2026)" value="">
      </div>
      <button type="submit" class="admin-btn admin-btn-primary">
        <i class="fas fa-magic"></i> Générer un article complet
      </button>
    </form>

    <div style="margin-top: 1rem; border-top: 1px solid var(--admin-border); padding-top: 1rem;">
      <form method="post" action="/admin/actualites/search" class="admin-form-inline">
        <div class="admin-form-group" style="flex:1;">
          <input type="text" name="query" class="admin-input" placeholder="Rechercher des idées d'articles (Perplexity)...">
        </div>
        <button type="submit" class="admin-btn admin-btn-secondary">
          <i class="fas fa-search"></i> Rechercher idées
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Articles List -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-newspaper"></i> Actualités publiées</h2>
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
            <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--admin-muted);">Aucune actualité. Cliquez sur "Générer" pour commencer.</td></tr>
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
                    <span class="admin-status admin-status-success">Publié</span>
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
                    <form method="post" action="/admin/actualites/delete/<?= (int) $actu['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cette actualité ?');">
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
<?php if (!empty($cronLogs)): ?>
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-history"></i> Historique des générations automatiques</h2>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Requête</th>
            <th>Articles trouvés</th>
            <th>Statut</th>
            <th>Erreur</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cronLogs as $log): ?>
            <tr>
              <td style="font-size: 0.85rem;"><?= e(date('d/m/Y H:i', strtotime((string) $log['created_at']))) ?></td>
              <td style="font-size: 0.85rem;"><?= e(mb_substr((string) $log['query_used'], 0, 60)) ?></td>
              <td><?= (int) $log['articles_found'] ?></td>
              <td>
                <?php if ($log['status'] === 'success'): ?>
                  <span class="admin-status admin-status-success">OK</span>
                <?php else: ?>
                  <span class="admin-status admin-status-danger">Erreur</span>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.8rem; color: var(--admin-danger);"><?= e((string) ($log['error_message'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

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
  .admin-btn-ghost:hover { color: var(--admin-primary); background: var(--admin-primary-light); }
  .admin-btn-danger { background: transparent; color: var(--admin-danger); padding: 0.4rem 0.6rem; }
  .admin-btn-danger:hover { background: rgba(239, 68, 68, 0.1); }
  .admin-btn-sm { padding: 0.35rem 0.5rem; font-size: 0.8rem; }
  .admin-form-inline { display: flex; gap: 0.75rem; align-items: flex-end; }
  .admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; }
  .admin-input:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px var(--admin-primary-light); }
  .admin-table-responsive { overflow-x: auto; }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); background: var(--admin-bg); }
  .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-border); font-size: 0.9rem; }
  .admin-table tbody tr:hover { background: rgba(0,0,0,0.02); }
  .admin-status { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
  .admin-status-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
  .admin-status-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .admin-status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
  .admin-badge { background: var(--admin-bg); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--admin-muted); font-weight: 600; }
  .admin-actions { display: flex; gap: 0.25rem; }
  .admin-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
  .admin-alert-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
  .admin-alert-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }

  @media (max-width: 768px) {
    .admin-form-inline { flex-direction: column; }
    .admin-page-header { flex-direction: column; }
  }
</style>
