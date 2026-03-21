<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour CMS</a>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 1rem;"><?= e($submitLabel) ?></h1>

    <?php if (($message ?? '') !== ''): ?><p class="success"><?= e((string) $message) ?></p><?php endif; ?>
    <?php if (($error ?? '') !== ''): ?><p class="alert"><?= e((string) $error) ?></p><?php endif; ?>

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

    <?php if (!empty($article['id']) && !empty($revisions)): ?>
      <section class="card" style="margin-top:1.5rem;">
        <h2>Historique des révisions</h2>
        <div class="table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Version</th>
                <th>Titre</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($revisions as $revision): ?>
                <tr>
                  <td>v<?= (int) $revision['revision_number'] ?></td>
                  <td><?= e((string) $revision['title']) ?></td>
                  <td><?= e((string) $revision['status']) ?></td>
                  <td><?= e((string) $revision['created_at']) ?></td>
                  <td>
                    <form method="post" action="/admin/blog/restore/<?= (int) $article['id'] ?>/<?= (int) $revision['id'] ?>" onsubmit="return confirm('Restaurer cette version ?');">
                      <button type="submit" class="btn btn-small btn-ghost">Restaurer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
</div>
