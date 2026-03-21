<div class="admin-page-header">
  <div>
    <a href="/admin/actualites" class="admin-back-link"><i class="fas fa-arrow-left"></i> Retour aux actualités</a>
    <h1 class="admin-page-title">Idées d'articles trouvées</h1>
    <p class="admin-page-desc">Recherche : "<?= e($query) ?>" (source: <?= e($source) ?>)</p>
  </div>
</div>

<div class="search-results-grid">
  <?php foreach ($results as $i => $idea): ?>
    <div class="admin-card idea-card">
      <div class="admin-card-body">
        <span class="idea-number">#<?= $i + 1 ?></span>
        <h3><?= e((string) ($idea['title'] ?? 'Sans titre')) ?></h3>
        <?php if (!empty($idea['summary'])): ?>
          <p class="idea-summary"><?= e((string) $idea['summary']) ?></p>
        <?php endif; ?>
        <?php if (!empty($idea['angle'])): ?>
          <p class="idea-angle"><strong>Angle :</strong> <?= e((string) $idea['angle']) ?></p>
        <?php endif; ?>
        <form method="post" action="/admin/actualites/generate" style="margin-top: 1rem;">
          <input type="hidden" name="query" value="<?= e((string) ($idea['title'] ?? '')) ?>">
          <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
            <i class="fas fa-magic"></i> Générer cet article
          </button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<style>
  .admin-back-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--admin-muted); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; }
  .admin-back-link:hover { color: var(--admin-primary); }
  .admin-page-header { margin-bottom: 1.5rem; }
  .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; }
  .admin-page-desc { font-size: 0.9rem; color: var(--admin-muted); margin-top: 0.25rem; }
  .search-results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1rem; }
  .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); overflow: hidden; }
  .admin-card-body { padding: 1.25rem; }
  .idea-card h3 { font-size: 1.05rem; font-weight: 600; margin: 0.5rem 0; }
  .idea-summary { color: var(--admin-muted); font-size: 0.9rem; line-height: 1.5; }
  .idea-angle { color: var(--admin-text); font-size: 0.85rem; margin-top: 0.5rem; }
  .idea-number { display: inline-block; background: var(--admin-primary-light); color: var(--admin-primary); font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; }
  .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
  .admin-btn-primary { background: var(--admin-primary); color: #fff; }
  .admin-btn-primary:hover { opacity: 0.9; }
  .admin-btn-sm { padding: 0.4rem 0.8rem; font-size: 0.8rem; }
</style>
