<?php
declare(strict_types=1);
$currentPage = 'estimation';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estimation immobilière à Bordeaux gratuite | DVF + IA</title>
  <meta name="description" content="Estimez gratuitement votre bien immobilier à Bordeaux en 30 secondes avec notre modèle basé sur les données DVF et l'IA.">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="/pages/estimation.php">
  <style>
    :root{--blue:#1e40af;--blue-h:#1d4ed8;--gray:#f3f4f6;--white:#fff;--text:#0f172a}
    *{box-sizing:border-box} body{margin:0;font-family:Inter,Arial,sans-serif;color:var(--text);background:var(--white);line-height:1.5}
    .container{max-width:1120px;margin:0 auto;padding:0 1rem}.top{background:var(--white);border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:9}
    .row{display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:.8rem 0}.logo{font-weight:800;color:var(--blue);text-decoration:none}
    nav a{color:#1f2937;text-decoration:none;margin-left:1rem;font-size:.95rem}
    .hero{background:linear-gradient(120deg,var(--blue),#2563eb);color:#fff;padding:3rem 0}
    h1{font-size:2rem;line-height:1.2;margin:0}.subtitle{opacity:.95;max-width:760px}
    .card{background:#fff;color:var(--text);border-radius:16px;padding:1rem;margin-top:1.2rem;box-shadow:0 10px 30px rgba(15,23,42,.1)}
    .grid{display:grid;gap:.8rem}.g2{grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
    label{font-size:.9rem;font-weight:600;display:block}.field{margin-top:.25rem;width:100%;padding:.72rem;border:1px solid #d1d5db;border-radius:10px}
    .btn{background:var(--blue);border:none;color:#fff;padding:.86rem 1.2rem;border-radius:10px;font-weight:700;cursor:pointer}
    .btn:hover{background:var(--blue-h)}
    .tiny{font-size:.82rem;color:#475569}.section{padding:2.5rem 0}.section.gray{background:var(--gray)}
    h2{font-size:1.5rem;margin:0 0 .9rem}.steps,.benefits,.testis,.faq{display:grid;gap:1rem;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
    .tile{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:1rem}
    .stars{color:#f59e0b}.muted{color:#64748b}
    footer{background:#0f172a;color:#cbd5e1;padding:2rem 0;margin-top:2rem} footer a{color:#93c5fd;text-decoration:none}
    .cta-end{background:#eff6ff;border-left:4px solid var(--blue);padding:1rem;border-radius:10px}
    @media (max-width:720px){h1{font-size:1.6rem}nav{display:none}}
  </style>
</head>
<body>
<header class="top">
  <div class="container row">
    <a class="logo" href="/pages/estimation.php">🏠 Installateur de Estimateur Immobilier avec base DVF</a>
    <nav>
      <a href="/pages/estimation.php">Estimation</a>
      <a href="/pages/prix-m2-bordeaux.php">Prix m² Bordeaux</a>
      <a href="/pages/faq.php">FAQ</a>
      <a href="/pages/contact.php">Contact</a>
    </nav>
  </div>
</header>
<main>
  <section class="hero">
    <div class="container">
      <h1>Estimez gratuitement votre bien immobilier à Bordeaux</h1>
      <p class="subtitle">Obtenez une estimation précise en 30 secondes, basée sur les données DVF et l'IA.</p>
      <form class="card" id="estimateForm" action="/pages/resultat-estimation.php" method="get" novalidate>
        <div class="grid g2">
          <p><label for="type">Type de bien</label><select id="type" name="type" class="field" required><option value="">Choisir…</option><option>Appartement</option><option>Maison</option></select></p>
          <p><label for="address">Adresse</label><input id="address" name="address" class="field" placeholder="Ex : 12 rue Sainte-Catherine, Bordeaux" autocomplete="street-address" required></p>
          <p><label for="surface">Surface (m²)</label><input id="surface" name="surface" type="number" min="9" class="field" required></p>
          <p><label for="rooms">Nombre de pièces</label><input id="rooms" name="rooms" type="number" min="1" class="field" required></p>
          <p><label for="floor">Étage</label><input id="floor" name="floor" type="text" class="field" placeholder="RDC, 1, 2..."></p>
          <p><label for="condition">État du bien</label><select id="condition" name="condition" class="field" required><option value="">Choisir…</option><option>À rénover</option><option>Bon état</option><option>Rénové</option><option>Neuf</option></select></p>
          <p style="grid-column:1/-1"><label for="email">Email (rapport détaillé)</label><input id="email" name="email" type="email" class="field" placeholder="vous@email.com" required></p>
        </div>
        <button class="btn" type="submit">Estimer mon bien</button>
        <p class="tiny">Vos données sont sécurisées et ne seront pas partagées.</p>
        <p class="tiny">Transparence : le résultat fourni est une estimation automatisée, non une expertise notariale ou bancaire.</p>
      </form>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <h2>Comment ça marche ?</h2>
      <div class="steps">
        <article class="tile"><h3>📊 Données DVF</h3><p>Nous analysons les transactions immobilières réelles enregistrées à Bordeaux et alentours.</p></article>
        <article class="tile"><h3>🤖 Intelligence artificielle</h3><p>Notre modèle ajuste les prix selon les caractéristiques de votre bien et les tendances de marché.</p></article>
        <article class="tile"><h3>📩 Rapport instantané</h3><p>Vous recevez une fourchette de prix claire et des recommandations pour votre projet immobilier.</p></article>
      </div>
      <p class="muted">Notre algorithme analyse les transactions réelles (DVF) et les tendances du marché pour vous fournir une estimation fiable.</p>
    </div>
  </section>

  <section class="section gray">
    <div class="container">
      <h2>Pourquoi nous choisir ?</h2>
      <div class="benefits">
        <div class="tile">🔒 <strong>Confidentialité</strong><p>Données hébergées en UE, collecte minimale et contrôles RGPD.</p></div>
        <div class="tile">⏱️ <strong>Rapide</strong><p>Un premier résultat en moins de 30 secondes.</p></div>
        <div class="tile">📈 <strong>Précis</strong><p>Base DVF + variables locales + ajustements IA.</p></div>
        <div class="tile">💰 <strong>Gratuit</strong><p>Estimation sans engagement et sans frais cachés.</p></div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <h2>Témoignages clients</h2>
      <div class="testis">
        <article class="tile"><p class="stars">★★★★★</p><p>"Estimation cohérente avec le prix final de vente."</p><p class="muted">— Claire M., Bordeaux Caudéran</p></article>
        <article class="tile"><p class="stars">★★★★★</p><p>"Simple, rapide et rassurant avant de contacter une agence."</p><p class="muted">— Julien R., Bordeaux Centre</p></article>
        <article class="tile"><p class="stars">★★★★☆</p><p>"Le rapport m'a aidée à fixer un prix réaliste."</p><p class="muted">— Sarah T., Talence</p></article>
      </div>
    </div>
  </section>

  <section class="section gray">
    <div class="container">
      <h2>FAQ rapide</h2>
      <div class="faq">
        <article class="tile"><h3>Le service est-il vraiment gratuit ?</h3><p>Oui, l'estimation en ligne est 100% gratuite et sans obligation d'achat.</p></article>
        <article class="tile"><h3>Le résultat est-il une expertise officielle ?</h3><p>Non. Il s'agit d'une estimation automatique indicative basée sur des données de marché.</p></article>
        <article class="tile"><h3>Comment sont traitées mes données ?</h3><p>Conformément au RGPD : finalité déterminée, durée limitée, et droits d'accès/suppression.</p></article>
      </div>
      <p class="cta-end"><strong>Besoin d'une estimation plus précise ?</strong> <a href="/pages/contact.php">Prenez rendez-vous avec un expert</a>.</p>
    </div>
  </section>
</main>
<footer>
  <div class="container">
    <p>© <?= date('Y'); ?> Estimateur Immobilier Bordeaux — Données DVF, IA et transparence utilisateur.</p>
    <p><a href="/pages/mentions-legales.php">Mentions légales</a> · <a href="/pages/politique-confidentialite.php">Politique de confidentialité</a> · <a href="/pages/cgu.php">CGU</a> · <a href="/pages/cookies.php">Cookies</a></p>
  </div>
</footer>
<script>
  document.getElementById('estimateForm').addEventListener('submit', function(e){
    const required=[...this.querySelectorAll('[required]')];
    const invalid=required.find(i=>!i.value.trim());
    if(invalid){e.preventDefault();invalid.focus();alert('Merci de compléter tous les champs obligatoires.');}
  });
  // Placeholder: intégration Google Maps Places Autocomplete à brancher avec votre clé API.
</script>
</body>
</html>
