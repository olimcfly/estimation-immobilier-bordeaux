<section class="hero">
  <div class="container hero-grid">
    <div>
      <p class="eyebrow">Estimation immobilière à Bordeaux</p>
      <h1>Vendez au meilleur prix avec une estimation claire, rapide et professionnelle.</h1>
      <p class="lead">Notre moteur combine données locales, tendances de marché et critères de votre bien pour fournir une fourchette de valeur fiable en quelques secondes.</p>
      <div class="hero-actions">
        <a class="btn" href="#simulateur">Lancer mon estimation</a>
        <a class="btn btn-ghost" href="#avantages">Découvrir nos atouts</a>
      </div>
      <ul class="trust-list">
        <li>+2 000 estimations réalisées</li>
        <li>Méthodologie orientée marché local</li>
        <li>Accompagnement personnalisé</li>
      </ul>
    </div>
    <aside class="hero-panel card" id="simulateur">
      <h2>Simuler la valeur de votre bien</h2>
      <p class="muted">Remplissez le formulaire pour obtenir une première estimation immédiate.</p>

      <?php if (!empty($errors ?? [])): ?>
        <div class="alert">
          <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="/estimation" method="post" class="form-grid">
        <label>Ville
          <input type="text" name="ville" placeholder="Ex : Bordeaux" required>
        </label>

        <label>Type de bien
          <select name="type_bien" required>
            <option value="Appartement">Appartement</option>
            <option value="Maison">Maison</option>
          </select>
        </label>

        <label>Surface (m²)
          <input type="number" name="surface" min="5" step="0.1" placeholder="Ex : 82" required>
        </label>

        <label>Pièces
          <input type="number" name="pieces" min="1" placeholder="Ex : 4" required>
        </label>

        <button type="submit" class="btn">Obtenir une estimation</button>
      </form>
    </aside>
  </div>
</section>

<section class="section" id="avantages">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Pourquoi nous choisir</p>
      <h2>Une home page premium mérite un service premium.</h2>
    </div>
    <div class="features-grid">
      <article class="card feature-card">
        <h3>Données locales fines</h3>
        <p>Nous nous appuyons sur des références de transactions en Gironde pour une évaluation réaliste de votre bien.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card feature-card">
        <h3>Résultat immédiat</h3>
        <p>En moins d'une minute, obtenez une fourchette de prix basse, moyenne et haute avec prix au m².</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card feature-card">
        <h3>Passage à l'action</h3>
        <p>Recevez ensuite un accompagnement sur-mesure pour vendre rapidement et sécuriser votre projet.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
    </div>
  </div>
</section>

<section class="section section-alt" id="process">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Notre méthode</p>
      <h2>3 étapes simples pour démarrer.</h2>
    </div>
    <div class="steps-grid">
      <article class="card">
        <p class="step-number">01</p>
        <h3>Renseignez votre bien</h3>
        <p>Ville, type, surface et nombre de pièces suffisent pour lancer la simulation.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card">
        <p class="step-number">02</p>
        <h3>Recevez l'estimation</h3>
        <p>Une fourchette de prix cohérente avec la dynamique actuelle du marché bordelais.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card">
        <p class="step-number">03</p>
        <h3>Activez l'accompagnement</h3>
        <p>Laissez vos coordonnées et avancez avec des conseils d'expert pour concrétiser votre vente.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
    </div>
  </div>
</section>

<section class="section" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">FAQ</p>
      <h2>Questions fréquentes</h2>
    </div>
    <div class="faq-grid">
      <article class="card">
        <h3>L'estimation est-elle gratuite ?</h3>
        <p>Oui, la simulation en ligne est gratuite et sans engagement.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card">
        <h3>En combien de temps j'obtiens mon résultat ?</h3>
        <p>Le résultat est généré immédiatement après validation du formulaire.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
      <article class="card">
        <h3>Puis-je être recontacté ensuite ?</h3>
        <p>Oui, si vous le souhaitez, vous pouvez laisser vos coordonnées pour être accompagné.</p>
        <a class="btn btn-small" href="#simulateur">Estimer mon bien</a>
      </article>
    </div>
  </div>
</section>
