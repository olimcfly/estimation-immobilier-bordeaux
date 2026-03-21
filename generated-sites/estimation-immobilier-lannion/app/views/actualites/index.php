<section class="section">
  <div class="container">
    <p class="eyebrow">Actualités immobilières</p>
    <h1>L'actualité immobilière à Lannion et en Côtes-d'Armor</h1>
    <p class="lead">Restez informé des dernières tendances du marché, des évolutions de prix et des projets qui transforment l'immobilier de lannion.</p>

    <div class="blog-grid">
      <?php if (empty($actualites)): ?>
        <article class="card">
          <h2>Aucune actualité publiée pour le moment</h2>
          <p class="muted">Revenez prochainement pour lire nos dernières analyses du marché immobilier de lannion.</p>
        </article>
      <?php else: ?>
        <?php foreach ($actualites as $actu): ?>
          <article class="card blog-card actualite-card">
            <?php if (!empty($actu['image_url'])): ?>
              <div class="actualite-image">
                <img src="<?= e((string) $actu['image_url']) ?>" alt="<?= e((string) $actu['title']) ?>" loading="lazy">
              </div>
            <?php endif; ?>
            <div class="actualite-content">
              <div class="actualite-meta">
                <span class="badge badge-actu">
                  <i class="fas fa-newspaper"></i> Actualité
                </span>
                <?php if (!empty($actu['published_at'])): ?>
                  <time class="muted" datetime="<?= e((string) $actu['published_at']) ?>">
                    <?= e(date('d/m/Y', strtotime((string) $actu['published_at']))) ?>
                  </time>
                <?php endif; ?>
              </div>
              <h2><?= e((string) $actu['title']) ?></h2>
              <?php if (!empty($actu['excerpt'])): ?>
                <p class="muted"><?= e((string) $actu['excerpt']) ?></p>
              <?php endif; ?>
              <a class="btn btn-small" href="/actualites/<?= e((string) $actu['slug']) ?>">Lire la suite</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Pagination des actualités">
        <?php if ($page > 1): ?>
          <a href="/actualites?page=<?= $page - 1 ?>" class="btn btn-small btn-ghost">&larr; Précédent</a>
        <?php endif; ?>
        <span class="pagination-info">Page <?= $page ?> sur <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
          <a href="/actualites?page=<?= $page + 1 ?>" class="btn btn-small btn-ghost">Suivant &rarr;</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

    <section class="card cta-card">
      <h2>Vous souhaitez connaître la valeur de votre bien ?</h2>
      <p class="muted">Profitez des données du marché pour obtenir une estimation précise et gratuite.</p>
      <a class="btn" href="/estimation">Estimer mon bien</a>
    </section>
  </div>
</section>

<style>
  .actualite-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .actualite-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
    margin: -1.5rem -1.5rem 1rem -1.5rem;
    width: calc(100% + 3rem);
  }
  .actualite-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  .actualite-card:hover .actualite-image img {
    transform: scale(1.05);
  }
  .actualite-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
  }
  .badge-actu {
    background: rgba(var(--primary-rgb), 0.1);
    color: var(--primary);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }
  .pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin: 2rem 0;
  }
  .pagination-info {
    color: var(--muted);
    font-size: 0.9rem;
  }
</style>
