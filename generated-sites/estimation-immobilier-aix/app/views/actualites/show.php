<?php
$publishedAt = null;
if (!empty($actualite['published_at'])) {
    try {
        $publishedAt = (new DateTimeImmutable((string) $actualite['published_at']))->format(DATE_ATOM);
    } catch (Exception) {
        $publishedAt = null;
    }
}

$baseUrl = App\Core\Config::get('app.base_url', '');
$articlePath = '/actualites/' . rawurlencode((string) $actualite['slug']);
$articleUrl = $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . $articlePath : $articlePath;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => (string) $actualite['title'],
    'description' => (string) $actualite['meta_description'],
    'datePublished' => $publishedAt,
    'author' => [
        '@type' => 'Organization',
        'name' => 'Estimation Immobilière Aix-en-Provence',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Estimation Immobilière Aix-en-Provence',
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $articleUrl,
    ],
    'url' => $articleUrl,
];

if (!empty($actualite['image_url'])) {
    $imgUrl = $actualite['image_url'];
    if (str_starts_with($imgUrl, '/')) {
        $imgUrl = rtrim((string) $baseUrl, '/') . $imgUrl;
    }
    $jsonLd['image'] = $imgUrl;
}

$jsonLd = array_filter($jsonLd, static fn (mixed $value): bool => $value !== null && $value !== '');
?>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<section class="section">
  <div class="container article-container">
    <a href="/actualites" class="back-link"><i class="fas fa-arrow-left"></i> Toutes les actualités</a>

    <div class="actualite-header">
      <span class="badge badge-actu"><i class="fas fa-newspaper"></i> Actualité</span>
      <?php if (!empty($actualite['published_at'])): ?>
        <time class="muted" datetime="<?= e((string) $actualite['published_at']) ?>">
          Publié le <?= e(date('d/m/Y', strtotime((string) $actualite['published_at']))) ?>
        </time>
      <?php endif; ?>
    </div>

    <h1><?= e((string) $actualite['title']) ?></h1>

    <?php if (!empty($actualite['excerpt'])): ?>
      <p class="lead"><?= e((string) $actualite['excerpt']) ?></p>
    <?php endif; ?>

    <?php if (!empty($actualite['image_url'])): ?>
      <div class="actualite-hero-image">
        <img src="<?= e((string) $actualite['image_url']) ?>" alt="<?= e((string) $actualite['title']) ?>" loading="lazy">
      </div>
    <?php endif; ?>

    <article class="card article-content">
      <?= (string) $actualite['content'] ?>
    </article>

    <section class="card cta-card">
      <h2>Besoin d'un prix de vente réaliste et défendable ?</h2>
      <p class="muted">Profitez de notre simulateur pour obtenir une fourchette fiable adaptée à Aix-en-Provence.</p>
      <a href="/estimation" class="btn">Demander mon estimation</a>
    </section>
  </div>
</section>

<style>
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
  }
  .back-link:hover {
    text-decoration: underline;
  }
  .actualite-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
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
  .actualite-hero-image {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    margin: 1.5rem 0;
    max-height: 400px;
  }
  .actualite-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
</style>
