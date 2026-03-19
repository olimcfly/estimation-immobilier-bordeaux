<section class="section">
  <div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost">← Retour CMS</a>
    <h1><?= e($submitLabel) ?></h1>

    <?php foreach ($errors as $err): ?>
      <p class="alert"><?= e((string) $err) ?></p>
    <?php endforeach; ?>

    <form method="post" action="<?= e($action) ?>" class="card form-grid">
      <label>Titre
        <input type="text" name="title" value="<?= e((string) ($article['title'] ?? '')) ?>" required>
      </label>

      <label>Slug
        <input type="text" name="slug" value="<?= e((string) ($article['slug'] ?? '')) ?>" required>
      </label>

      <label>Méta titre
        <input type="text" name="meta_title" value="<?= e((string) ($article['meta_title'] ?? '')) ?>" required>
      </label>

      <label>Méta description
        <textarea name="meta_description" rows="3" required><?= e((string) ($article['meta_description'] ?? '')) ?></textarea>
      </label>

      <label>Persona
        <input type="text" name="persona" value="<?= e((string) ($article['persona'] ?? '')) ?>" required>
      </label>

      <label>Niveau de conscience
        <input type="text" name="awareness_level" value="<?= e((string) ($article['awareness_level'] ?? '')) ?>" required>
      </label>

      <label>Contenu HTML
        <textarea name="content" rows="16" required><?= e((string) ($article['content'] ?? '')) ?></textarea>
      </label>

      <label>Statut
        <select name="status" required>
          <option value="draft" <?= (($article['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
          <option value="published" <?= (($article['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
        </select>
      </label>

      <button class="btn" type="submit"><?= e($submitLabel) ?></button>
    </form>
  </div>
</section>
