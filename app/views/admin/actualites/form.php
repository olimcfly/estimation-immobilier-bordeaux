<div class="admin-page-header">
  <div>
    <a href="/admin/actualites" class="admin-back-link"><i class="fas fa-arrow-left"></i> Retour aux actualités</a>
    <h1 class="admin-page-title"><?= e($submitLabel) ?></h1>
  </div>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e((string) $message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e((string) $error) ?></div><?php endif; ?>

<?php foreach ($errors as $err): ?>
  <div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e((string) $err) ?></div>
<?php endforeach; ?>

<form method="post" action="<?= e($action) ?>">
  <div class="admin-form-grid">
    <!-- Main Content Column -->
    <div class="admin-form-main">
      <div class="admin-card">
        <div class="admin-card-header"><h2>Contenu</h2></div>
        <div class="admin-card-body">
          <div class="admin-form-group">
            <label class="admin-label">Titre</label>
            <input type="text" name="title" class="admin-input" value="<?= e((string) ($actualite['title'] ?? '')) ?>" required>
          </div>

          <div class="admin-form-group">
            <label class="admin-label">Slug URL</label>
            <input type="text" name="slug" class="admin-input" value="<?= e((string) ($actualite['slug'] ?? '')) ?>" placeholder="Généré automatiquement si vide">
          </div>

          <div class="admin-form-group">
            <label class="admin-label">Extrait (chapô)</label>
            <textarea name="excerpt" class="admin-textarea" rows="3"><?= e((string) ($actualite['excerpt'] ?? '')) ?></textarea>
          </div>

          <div class="admin-form-group">
            <label class="admin-label">Contenu HTML</label>
            <textarea name="content" class="admin-textarea admin-textarea-code" rows="20" required><?= e((string) ($actualite['content'] ?? '')) ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar Column -->
    <div class="admin-form-sidebar">
      <div class="admin-card">
        <div class="admin-card-header"><h2>Publication</h2></div>
        <div class="admin-card-body">
          <div class="admin-form-group">
            <label class="admin-label">Statut</label>
            <select name="status" class="admin-select">
              <option value="draft" <?= (($actualite['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
              <option value="published" <?= (($actualite['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
            </select>
          </div>

          <input type="hidden" name="generated_by" value="<?= e((string) ($actualite['generated_by'] ?? 'manual')) ?>">
          <input type="hidden" name="source_query" value="<?= e((string) ($actualite['source_query'] ?? '')) ?>">
          <input type="hidden" name="source_results" value="<?= e((string) ($actualite['source_results'] ?? '')) ?>">

          <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
            <i class="fas fa-save"></i> <?= e($submitLabel) ?>
          </button>
        </div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header"><h2>SEO</h2></div>
        <div class="admin-card-body">
          <div class="admin-form-group">
            <label class="admin-label">Méta titre</label>
            <input type="text" name="meta_title" class="admin-input" value="<?= e((string) ($actualite['meta_title'] ?? '')) ?>" placeholder="Titre SEO">
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Méta description</label>
            <textarea name="meta_description" class="admin-textarea" rows="3" placeholder="Description pour les moteurs de recherche"><?= e((string) ($actualite['meta_description'] ?? '')) ?></textarea>
          </div>
        </div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header"><h2>Image</h2></div>
        <div class="admin-card-body">
          <?php if (!empty($actualite['image_url'])): ?>
            <div class="admin-image-preview">
              <img src="<?= e((string) $actualite['image_url']) ?>" alt="Preview">
            </div>
          <?php endif; ?>
          <div class="admin-form-group">
            <label class="admin-label">URL de l'image</label>
            <input type="text" name="image_url" class="admin-input" value="<?= e((string) ($actualite['image_url'] ?? '')) ?>" placeholder="/assets/images/ai-generated/...">
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<style>
  .admin-back-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--admin-muted); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; }
  .admin-back-link:hover { color: var(--admin-primary); }
  .admin-page-header { margin-bottom: 1.5rem; }
  .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; }
  .admin-form-grid { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start; }
  .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); margin-bottom: 1.5rem; overflow: hidden; }
  .admin-card-header { padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--admin-border); }
  .admin-card-header h2 { font-size: 0.9rem; font-weight: 600; margin: 0; }
  .admin-card-body { padding: 1.25rem; }
  .admin-form-group { margin-bottom: 1rem; }
  .admin-form-group:last-child { margin-bottom: 0; }
  .admin-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-text); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.03em; }
  .admin-input, .admin-select, .admin-textarea { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; background: #fff; }
  .admin-input:focus, .admin-select:focus, .admin-textarea:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1); }
  .admin-textarea-code { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 0.85rem; line-height: 1.6; }
  .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
  .admin-btn-primary { background: var(--admin-primary); color: #fff; }
  .admin-btn-primary:hover { opacity: 0.9; }
  .admin-image-preview { margin-bottom: 1rem; border-radius: 6px; overflow: hidden; }
  .admin-image-preview img { width: 100%; height: auto; display: block; }
  .admin-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
  .admin-alert-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
  .admin-alert-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }

  @media (max-width: 1024px) {
    .admin-form-grid { grid-template-columns: 1fr; }
  }
</style>
