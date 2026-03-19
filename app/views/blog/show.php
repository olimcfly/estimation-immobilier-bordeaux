<section class="section">
  <div class="container article-container">
    <p class="eyebrow"><?= e((string) $article['persona']) ?> • <?= e((string) $article['awareness_level']) ?></p>
    <h1><?= e((string) $article['title']) ?></h1>
    <p class="muted"><?= e((string) $article['meta_description']) ?></p>

    <article class="card article-content">
      <?= (string) $article['content'] ?>
    </article>

    <section class="card cta-card">
      <h2>Besoin d'un prix de vente réaliste et défendable ?</h2>
      <p class="muted">Profitez de notre simulateur pour obtenir une fourchette fiable adaptée à Bordeaux.</p>
      <a href="/estimation" class="btn">Demander mon estimation</a>
    </section>
  </div>
</section>
