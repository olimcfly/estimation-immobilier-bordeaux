<?php
$createdAt = null;
if (!empty($article['created_at'])) {
    try {
        $createdAt = (new DateTimeImmutable((string) $article['created_at']))->format(DATE_ATOM);
    } catch (Exception) {
        $createdAt = null;
    }
}

$baseUrl = App\Core\Config::get('app.base_url', '');
$articlePath = '/blog/' . rawurlencode((string) $article['slug']);
$articleUrl = $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . $articlePath : $articlePath;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => (string) $article['title'],
    'description' => (string) $article['meta_description'],
    'datePublished' => $createdAt,
    'author' => [
        '@type' => 'Organization',
        'name' => 'Estimation Immobilière Nantes',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Estimation Immobilière Nantes',
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $articleUrl,
    ],
    'url' => $articleUrl,
];

$jsonLd = array_filter($jsonLd, static fn (mixed $value): bool => $value !== null && $value !== '');
?>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

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
      <p class="muted">Profitez de notre simulateur pour obtenir une fourchette fiable adaptée à Nantes.</p>
      <a href="/estimation" class="btn">Demander mon estimation</a>
    </section>
  </div>
</section>
