#!/usr/bin/env bash
set -euo pipefail

mkdir -p app/views/layouts app/views/pages app/controllers routes public/assets/css

create_file_if_missing() {
  local file="$1"
  local content="$2"

  if [[ ! -f "$file" ]]; then
    printf "%s\n" "$content" > "$file"
    echo "Créé: $file"
  else
    echo "Déjà présent: $file"
  fi
}

create_file_if_missing "app/views/layouts/header.php" '<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? "Estimation Immobilier Bordeaux", ENT_QUOTES, "UTF-8") ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main>'

create_file_if_missing "app/views/layouts/footer.php" '</main>
</body>
</html>
EOH
echo -e "${GREEN}✓ footer.php mis à jour${NC}"
echo ""

# 5. CRÉER LES PAGES MANQUANTES
echo -e "${YELLOW}[5/8] Création des pages manquantes...${NC}"

# Page HOME
if [ ! -f "app/views/pages/home.php" ]; then
    cat > app/views/pages/home.php << 'EOH'
<?php $page_title = 'Estimation Immobilière Bordeaux - Évaluez Votre Bien'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      <p class="eyebrow">✓ Estimation immobilière à Bordeaux</p>
      <h1>Vendez au meilleur prix avec une évaluation fiable et professionnelle</h1>
      <p class="lead">Notre moteur analyse les données locales, les tendances du marché et les caractéristiques de votre bien pour fournir une fourchette de valeur précise en quelques secondes.</p>
      
      <ul class="trust-list">
        <li><i class="fas fa-check-circle"></i> +2 000 estimations réalisées</li>
        <li><i class="fas fa-check-circle"></i> Méthodologie marché local</li>
        <li><i class="fas fa-check-circle"></i> Accompagnement personnalisé</li>
      </ul>

      <div class="hero-actions">
        <a class="btn" href="#simulateur">Lancer mon estimation</a>
      </div>
    </div>

    <!-- FORMULAIRE HERO -->
    <aside class="hero-panel card" id="simulateur">
      <div class="panel-header">
        <h2>Simuler la valeur de votre bien</h2>
        <p class="muted">Remplissez le formulaire pour obtenir votre estimation immédiate.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid">
        <div class="form-row">
          <label for="ville">
            <span><i class="fas fa-map-marker-alt"></i> Ville</span>
            <input type="text" id="ville" name="ville" placeholder="Ex : Bordeaux" required>
          </label>

          <label for="type">
            <span><i class="fas fa-home"></i> Type de bien</span>
            <select id="type" name="type" required>
              <option value="">-- Sélectionner --</option>
              <option value="Appartement">Appartement</option>
              <option value="Maison">Maison</option>
              <option value="Studio">Studio</option>
            </select>
          </label>
        </div>

        <div class="form-row">
          <label for="surface">
            <span><i class="fas fa-ruler-combined"></i> Surface (m²)</span>
            <input type="number" id="surface" name="surface" min="5" step="0.1" placeholder="Ex : 85" required>
          </label>

          <label for="pieces">
            <span><i class="fas fa-door-open"></i> Pièces</span>
            <input type="number" id="pieces" name="pieces" min="1" placeholder="Ex : 3" required>
          </label>
        </div>

        <button type="submit" class="btn btn-primary">Obtenir mon estimation gratuite</button>
        <p class="form-footer">✓ 100% gratuit • ✓ Résultat immédiat • ✓ Sans engagement</p>
      </form>
    </aside>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
EOH
    echo -e "${GREEN}✓ home.php créé${NC}"
else
    echo -e "${GREEN}✓ home.php existe${NC}"
fi

# Page ABOUT
if [ ! -f "app/views/pages/about.php" ]; then
    cat > app/views/pages/about.php << 'EOH'
<?php $page_title = 'À Propos - Estimation Immobilier Bordeaux'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- HERO PAGE -->
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-info-circle"></i> À propos de nous</p>
      <h1>Une équipe bordelaise dédiée à la réussite de votre vente immobilière</h1>
      <p class="lead">Estimation Immobilier Bordeaux est né de l'envie de rendre l'estimation immobilière plus claire, plus rapide et plus fiable pour les propriétaires girondins.</p>
    </div>
  </div>
</section>

<!-- MISSION & EXPERTISE -->
<section class="section">
  <div class="container">
    <div class="about-grid">
      <article class="card about-card">
        <div class="about-icon">
          <i class="fas fa-bullseye"></i>
        </div>
        <h2>Notre mission</h2>
        <p>Rendre accessible une première estimation réaliste en quelques clics, puis proposer un accompagnement premium selon votre objectif de vente.</p>
      </article>

      <article class="card about-card">
        <div class="about-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <h2>Notre expertise</h2>
        <p>Nous nous appuyons sur la dynamique des quartiers bordelais, l'analyse approfondie du marché immobilier local et les méthodes d'évaluation utilisées par les professionnels.</p>
      </article>
    </div>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
EOH
    echo -e "${GREEN}✓ about.php créé${NC}"
else
    echo -e "${GREEN}✓ about.php existe${NC}"
fi

# Page SERVICES
if [ ! -f "app/views/pages/services.php" ]; then
    cat > app/views/pages/services.php << 'EOH'
<?php $page_title = 'Services - Estimation Immobilier Bordeaux'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- HERO PAGE -->
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-briefcase"></i> Nos services</p>
      <h1>Un accompagnement immobilier complet, de l'estimation à la signature</h1>
      <p class="lead">Nous combinons technologie, expertise locale et suivi humain pour sécuriser votre projet immobilier à Bordeaux et en Gironde.</p>
    </div>
  </div>
</section>

<!-- SERVICES PRINCIPAUX -->
<section class="section">
  <div class="container">
    <div class="cards-stack">
      <article class="card service-card">
        <div class="service-header">
          <div class="service-icon-large">
            <i class="fas fa-calculator"></i>
          </div>
          <div>
            <h2>Estimation intelligente</h2>
            <p class="service-subtitle">Découvrez la vraie valeur de votre bien</p>
          </div>
        </div>
        <p>Notre simulateur valorise votre bien à partir des tendances locales réelles, du type de logement et de ses caractéristiques clés.</p>
      </article>
    </div>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
EOH
    echo -e "${GREEN}✓ services.php créé${NC}"
else
    echo -e "${GREEN}✓ services.php existe${NC}"
fi

# Page CONTACT
if [ ! -f "app/views/pages/contact.php" ]; then
    cat > app/views/pages/contact.php << 'EOH'
<?php $page_title = 'Contact - Estimation Immobilier Bordeaux'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- HERO PAGE -->
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-envelope"></i> Nous contacter</p>
      <h1>Parlons de votre projet immobilier</h1>
      <p class="lead">Besoin d'un avis expert avant de vendre ? Notre équipe bordelaise vous répond rapidement et sans engagement.</p>
    </div>
  </div>
</section>

<!-- CONTACT INFO & FORM -->
<section class="section">
  <div class="container">
    <div class="contact-layout">
      <article class="card contact-info">
        <h2><i class="fas fa-map-marker-alt"></i> Nos coordonnées</h2>
        
        <div class="info-block">
          <p class="info-label"><i class="fas fa-phone"></i> Téléphone</p>
          <p class="info-value"><a href="tel:+33556000000">+33 5 56 00 00 00</a></p>
          <p class="info-desc">Du lundi au vendredi, 9h à 19h</p>
        </div>

        <div class="info-block">
          <p class="info-label"><i class="fas fa-envelope"></i> Email</p>
          <p class="info-value"><a href="mailto:contact@estimation-bordeaux.fr">contact@estimation-bordeaux.fr</a></p>
          <p class="info-desc">Réponse en moins de 24h</p>
        </div>
      </article>

      <article class="card contact-form-card">
        <h2><i class="fas fa-comment-dots"></i> Envoyez-nous un message</h2>
        <p class="form-intro">Remplissez ce formulaire et nous vous recontacterons rapidement.</p>

        <form class="form-grid form-contact" action="/contact" method="post">
          <label for="nom">
            <span>Nom complet *</span>
            <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required>
          </label>

          <label for="email">
            <span>Email *</span>
            <input type="email" id="email" name="email" placeholder="jean@email.com" required>
          </label>

          <label for="message">
            <span>Votre message *</span>
            <textarea id="message" name="message" placeholder="Décrivez votre situation..." rows="5" required></textarea>
          </label>

          <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-paper-plane"></i> Envoyer le message
          </button>
        </form>
      </article>
    </div>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>
EOH
    echo -e "${GREEN}✓ contact.php créé${NC}"
else
    echo -e "${GREEN}✓ contact.php existe${NC}"
fi
echo ""

# 6. CRÉER PAGE CONTROLLER
echo -e "${YELLOW}[6/8] Création du PageController...${NC}"
if [ ! -f "app/controllers/PageController.php" ]; then
    cat > app/controllers/PageController.php << 'EOH'
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class PageController
{
    public function services(): void
    {
        View::render('pages/services');
    }

    public function aPropos(): void
    {
        View::render('pages/a_propos');
    }

    public function contact(): void
    {
        View::render('pages/contact');
    }
}
EOH
    echo -e "${GREEN}✓ PageController.php créé${NC}"
else
    echo -e "${GREEN}✓ PageController.php existe${NC}"
fi
echo ""

create_file_if_missing "app/views/pages/home.php" '<section class="section"><div class="container"><h1>Accueil</h1></div></section>'
create_file_if_missing "app/views/pages/about.php" '<section class="section"><div class="container"><h1>À propos</h1></div></section>'
create_file_if_missing "app/views/pages/services.php" '<section class="section"><div class="container"><h1>Services</h1></div></section>'
create_file_if_missing "app/views/pages/contact.php" '<section class="section"><div class="container"><h1>Contact</h1></div></section>'

declare(strict_types=1);

use App\Controllers\EstimationController;
use App\Controllers\PageController;

$router->get('/', [EstimationController::class, 'index']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/contact', [PageController::class, 'contact']);
EOH
    echo -e "${GREEN}✓ routes/web.php créé${NC}"
else
  echo "WARN: app-css-complete-final.css absent, copie CSS ignorée"
fi

# 8. AFFICHER RÉSUMÉ
echo -e "${YELLOW}[8/8] Résumé de la restructuration${NC}"
echo ""
echo -e "${GREEN}✓ Structure MVC prête !${NC}"
echo ""
echo "Structure créée :"
echo "├── app/"
echo "│   ├── views/layouts/     (header + footer)"
echo "│   ├── views/pages/       (home, about, services, contact)"
echo "│   ├── views/estimation/  (index, result, lead_saved)"
echo "│   ├── controllers/       (PageController, EstimationController)"
echo "│   ├── models/"
echo "│   └── services/"
echo "├── public/assets/css/     (app.css)"
echo "├── routes/               (web.php)"
echo "└── config/"
echo ""
echo "================================"
echo "PROCHAINES ÉTAPES"
echo "================================"
echo ""
echo "1. Copie le CSS complet dans public/assets/css/app.css :"
echo "   → app-css-complete-final.css"
echo ""
echo "2. Vérifie les routes dans routes/web.php"
echo ""
echo "3. Teste les URLs :"
echo "   → http://localhost/"
echo "   → http://localhost/estimation"
echo "   → http://localhost/a-propos"
echo "   → http://localhost/services"
echo "   → http://localhost/contact"
echo ""
echo "4. Adapte le routeur si nécessaire"
echo ""
echo -e "${GREEN}Fin de la restructuration !${NC}"
