<section class="section">
  <div class="container">
    <p class="eyebrow">Blog immobilier Nandy</p>
    <h1>Conseils pour vendre votre bien au meilleur prix</h1>
    <p class="lead">Découvrez nos analyses locales, guides pratiques et stratégies de vente adaptées à votre profil vendeur.</p>

    <div class="blog-grid">
      <?php if (empty($articles)): ?>
        <article class="card">
          <h2>Aucun article publié pour le moment</h2>
          <p class="muted">Revenez prochainement pour lire nos derniers conseils immobiliers.</p>
        </article>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <article class="card blog-card">
            <p class="badge"><?= e((string) $article['persona']) ?> • <?= e((string) $article['awareness_level']) ?></p>
            <h2><?= e((string) $article['title']) ?></h2>
            <p class="muted"><?= e((string) $article['meta_description']) ?></p>
            <a class="btn btn-small" href="/blog/<?= e((string) $article['slug']) ?>">Lire l'article</a>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <section class="card cta-card">
      <h2>Vous voulez connaître la valeur réelle de votre bien ?</h2>
      <p class="muted">Obtenez une estimation précise en moins de 2 minutes.</p>
      <a class="btn" href="/estimation">Lancer mon estimation</a>
    </section>
  </div>
</section>
