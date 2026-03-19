#!/bin/bash

# Script de restructuration - Estimation Immobilier Bordeaux
# Exécution: bash restructure.sh

set -e

echo "================================"
echo "RESTRUCTURATION DU PROJET"
echo "================================"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. CRÉER LES RÉPERTOIRES MANQUANTS
echo -e "${YELLOW}[1/8] Création des répertoires...${NC}"
mkdir -p app/views/layouts
mkdir -p app/views/pages
mkdir -p app/views/estimation
mkdir -p app/controllers
mkdir -p app/models
mkdir -p app/services
mkdir -p app/core
mkdir -p public/assets/css
mkdir -p public/assets/js
mkdir -p public/assets/images
mkdir -p routes
mkdir -p config
mkdir -p database
echo -e "${GREEN}✓ Répertoires créés${NC}\n"

# 2. VÉRIFIER LES FICHIERS EXISTANTS
echo -e "${YELLOW}[2/8] Vérification des fichiers existants...${NC}"

if [ -f "app/views/layouts/footer.php" ]; then
    echo -e "${GREEN}✓ footer.php existe${NC}"
else
    echo -e "${RED}✗ footer.php MANQUANT${NC}"
fi

if [ -f "app/views/estimation/index.php" ]; then
    echo -e "${GREEN}✓ estimation/index.php existe${NC}"
else
    echo -e "${RED}✗ estimation/index.php MANQUANT${NC}"
fi

if [ -f "public/assets/css/app.css" ]; then
    echo -e "${GREEN}✓ app.css existe${NC}"
else
    echo -e "${RED}✗ app.css MANQUANT${NC}"
fi
echo ""

# 3. CRÉER HEADER.PHP SI N'EXISTE PAS
echo -e "${YELLOW}[3/8] Création/vérification du header...${NC}"
if [ ! -f "app/views/layouts/header.php" ]; then
    cat > app/views/layouts/header.php << 'EOH'
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Obtenez une estimation immobilière instantanée à Bordeaux avec une interface premium et un accompagnement professionnel.">
  <title><?= isset($page_title) ? $page_title : 'Estimation Immobilière Bordeaux' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/css/app.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="container">
    <nav class="nav-wrapper">
      <a href="/" class="brand">Estimation <span>Bordeaux</span></a>
      <div class="top-nav">
        <a href="/">Accueil</a>
        <a href="/about">À propos</a>
        <a href="/services">Services</a>
        <a href="/contact">Contact</a>
        <a href="/#simulateur" class="btn btn-small">Estimer</a>
      </div>
    </nav>
  </div>
</header>

<main>
EOH
    echo -e "${GREEN}✓ header.php créé${NC}"
else
    echo -e "${GREEN}✓ header.php existe${NC}"
fi
echo ""

# 4. CRÉER/METTRE À JOUR FOOTER.PHP
echo -e "${YELLOW}[4/8] Création/mise à jour du footer...${NC}"
cat > app/views/layouts/footer.php << 'EOH'
</main>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <p class="brand-footer">Estimation <span>Bordeaux</span></p>
        <p class="muted">Accompagner les vendeurs immobiliers à Bordeaux avec des estimations fiables et professionnelles.</p>
      </div>
      <div>
        <h4>Pages</h4>
        <ul class="footer-links">
          <li><a href="/">Accueil</a></li>
          <li><a href="/about">À propos</a></li>
          <li><a href="/services">Services</a></li>
          <li><a href="/contact">Contact</a></li>
          <li><a href="/mentions-legales">Mentions légales</a></li>
        </ul>
      </div>
      <div>
        <h4>Contact</h4>
        <p class="muted"><i class="fas fa-envelope"></i> contact@estimation-bordeaux.fr</p>
        <p class="muted"><i class="fas fa-phone"></i> 05 XX XX XX XX</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="muted">&copy; 2024 Estimation Bordeaux. Tous droits réservés.</p>
    </div>
  </div>
</footer>

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

namespace App\Controllers;

class PageController {
    
    public function home() {
        require 'app/views/pages/home.php';
    }

    public function about() {
        require 'app/views/pages/about.php';
    }

    public function services() {
        require 'app/views/pages/services.php';
    }

    public function contact() {
        require 'app/views/pages/contact.php';
    }
}
EOH
    echo -e "${GREEN}✓ PageController.php créé${NC}"
else
    echo -e "${GREEN}✓ PageController.php existe${NC}"
fi
echo ""

# 7. CRÉER LES ROUTES
echo -e "${YELLOW}[7/8] Création des routes...${NC}"
if [ ! -f "routes/web.php" ]; then
    cat > routes/web.php << 'EOH'
<?php

/**
 * Routes de l'application
 * Format: $router->METHOD('path', 'Controller@method')
 */

// Pages
$router->get('/', 'PageController@home');
$router->get('/about', 'PageController@about');
$router->get('/services', 'PageController@services');
$router->get('/contact', 'PageController@contact');

// Estimation
$router->post('/estimation', 'EstimationController@store');
$router->get('/estimation/result', 'EstimationController@result');

// Contact
$router->post('/contact', 'PageController@contactSubmit');
EOH
    echo -e "${GREEN}✓ routes/web.php créé${NC}"
else
    echo -e "${GREEN}✓ routes/web.php existe${NC}"
fi
echo ""

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
echo "   → http://localhost/about"
echo "   → http://localhost/services"
echo "   → http://localhost/contact"
echo ""
echo "4. Adapte le routeur si nécessaire"
echo ""
echo -e "${GREEN}Fin de la restructuration !${NC}"
