<div class="container">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
      <div>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Gestion du blog</h1>
      </div>
      <a href="/admin/blog/create" class="btn">Nouvel article</a>
    </div>

    <?php if ($message !== ''): ?><p class="success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error !== ''): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>

    <section class="card">
      <h2>Générer avec IA</h2>
      <form method="post" action="/admin/blog/generate" class="form-grid">
        <label>Persona
          <select name="persona" required>
            <option>Propriétaire hésitant</option>
            <option>Propriétaire pressé</option>
            <option>Propriétaire méfiant</option>
            <option>Succession / divorce</option>
            <option>Investisseur vendeur</option>
          </select>
        </label>
        <label>Niveau de conscience
          <select name="awareness_level" required>
            <option>inconscient</option>
            <option>problème</option>
            <option>solution</option>
            <option>produit</option>
          </select>
        </label>
        <label>Sujet de l'article
          <input type="text" name="topic" placeholder="Ex: Est-ce le bon moment pour vendre à Angers ?" required>
        </label>
        <button type="submit" class="btn">Générer avec IA</button>
      </form>
    </section>

    <section class="card">
      <h2>Articles</h2>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Persona</th>
              <th>Niveau</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($articles as $article): ?>
              <tr>
                <td><?= e((string) $article['title']) ?></td>
                <td><?= e((string) $article['persona']) ?></td>
                <td><?= e((string) $article['awareness_level']) ?></td>
                <td><?= e((string) $article['status']) ?></td>
                <td>
                  <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="btn btn-small btn-ghost">Modifier</a>
                  <form method="post" action="/admin/blog/delete/<?= (int) $article['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                    <button type="submit" class="btn btn-small">Supprimer</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
</div>
