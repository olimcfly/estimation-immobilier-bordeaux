<?php $page_title = 'Estimer - Formulaire d\'Estimation Immobilière Bordeaux'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<!-- ============================================ -->
<!-- HERO SECTION -->
<!-- ============================================ -->
<section class="section page-hero page-block-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow">
        <i class="fas fa-calculator"></i> Estimation gratuite
      </p>
      <h1>Découvrez la vraie valeur de votre bien</h1>
      <p class="lead">
        Formulaire simple, résultat instantané. Aucune visite obligatoire. 100% gratuit et sans engagement.
      </p>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FORMULAIRE ESTIMATION -->
<!-- ============================================ -->
<section class="section" id="form-estimation">
  <div class="container">
    <div class="estimation-form-wrapper estimation-layout">
      
      <!-- GAUCHE: INFOS BIEN -->
      <article class="card estimation-form">
        <h2 class="estimation-form-title">
          <i class="fas fa-home"></i> Parlez-nous de votre bien
        </h2>
        <p class="form-intro">
          Remplissez les informations ci-dessous. Votre estimation sera calculée en temps réel.
        </p>

        <form class="form-grid form-estimation" action="/estimation" method="post" id="estimation-form">
          
          <!-- LOCALISATION -->
          <label for="localisation" class="full-width">
            <span><i class="fas fa-map-marker-alt"></i> Localisation *</span>
            <select id="localisation" name="localisation" required onchange="updateQuartiers(this.value)">
              <option value="">-- Sélectionner une zone --</option>
              <optgroup label="Bordeaux Centre">
                <option value="chartrons">Chartrons</option>
                <option value="vieux-bordeaux">Vieux Bordeaux</option>
                <option value="pey-berland">Pey Berland</option>
                <option value="st-michel">Saint-Michel</option>
              </optgroup>
              <optgroup label="Rive Gauche">
                <option value="sainte-croix">Sainte-Croix</option>
                <option value="bacalan">Bacalan</option>
                <option value="la-bastide">La Bastide</option>
              </optgroup>
              <optgroup label="Banlieue">
                <option value="talence">Talence</option>
                <option value="floirac">Floirac</option>
                <option value="villenave">Villenave d'Ornon</option>
                <option value="bruges">Bruges</option>
              </optgroup>
              <optgroup label="Autre">
                <option value="autre">Autre quartier</option>
              </optgroup>
            </select>
          </label>

          <!-- CODE POSTAL -->
          <label for="code-postal" class="half-width">
            <span><i class="fas fa-envelope"></i> Code postal *</span>
            <input 
              type="text" 
              id="code-postal" 
              name="code_postal" 
              placeholder="33000" 
              pattern="[0-9]{5}"
              required
            >
          </label>

          <!-- TYPE DE BIEN -->
          <label for="type-bien" class="half-width">
            <span><i class="fas fa-home"></i> Type de bien *</span>
            <select id="type-bien" name="type_bien" required onchange="updateChamps(this.value)">
              <option value="">-- Sélectionner --</option>
              <option value="appartement">Appartement</option>
              <option value="maison">Maison / Villa</option>
              <option value="terrain">Terrain</option>
              <option value="local">Local commercial</option>
            </select>
          </label>

          <!-- SURFACE -->
          <label for="surface" class="half-width">
            <span><i class="fas fa-ruler"></i> Surface (m²) *</span>
            <input 
              type="number" 
              id="surface" 
              name="surface" 
              placeholder="85" 
              min="20"
              max="500"
              required
            >
          </label>

          <!-- NOMBRE DE PIÈCES -->
          <label for="pieces" class="half-width">
            <span><i class="fas fa-door-open"></i> Nombre de pièces *</span>
            <select id="pieces" name="pieces" required>
              <option value="">-- Sélectionner --</option>
              <option value="1">1 pièce</option>
              <option value="2">2 pièces</option>
              <option value="3">3 pièces</option>
              <option value="4">4 pièces</option>
              <option value="5">5 pièces</option>
              <option value="6+">6+ pièces</option>
            </select>
          </label>

          <!-- NOMBRE DE CHAMBRES -->
          <label for="chambres" class="half-width">
            <span><i class="fas fa-bed"></i> Chambres</span>
            <select id="chambres" name="chambres">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5+">5+</option>
            </select>
          </label>

          <!-- SALLES DE BAIN -->
          <label for="sdb" class="half-width">
            <span><i class="fas fa-bath"></i> Salles de bain</span>
            <select id="sdb" name="sdb">
              <option value="0">0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4+">4+</option>
            </select>
          </label>

          <!-- ANNÉE CONSTRUCTION -->
          <label for="annee" class="half-width">
            <span><i class="fas fa-calendar"></i> Année de construction *</span>
            <input 
              type="number" 
              id="annee" 
              name="annee" 
              placeholder="2005" 
              min="1800"
              max="2024"
              required
            >
          </label>

          <!-- ÉTAGE -->
          <label for="etage" class="half-width">
            <span><i class="fas fa-layer-group"></i> Étage</span>
            <select id="etage" name="etage">
              <option value="">Pas applicable</option>
              <option value="rdc">Rez-de-chaussée</option>
              <option value="1">1er étage</option>
              <option value="2">2ème étage</option>
              <option value="3">3ème étage</option>
              <option value="4+">4+ étages</option>
            </select>
          </label>

          <!-- ÉTAT GÉNÉRAL -->
          <label for="etat" class="full-width">
            <span><i class="fas fa-check-circle"></i> État général du bien *</span>
            <div class="radio-group">
              <label class="radio-option">
                <input type="radio" name="etat" value="excellent" required>
                <span><strong>Excellent</strong> - Rénové récemment, très bon état</span>
              </label>
              <label class="radio-option">
                <input type="radio" name="etat" value="bon" required>
                <span><strong>Bon</strong> - Bon état, quelques travaux possibles</span>
              </label>
              <label class="radio-option">
                <input type="radio" name="etat" value="moyen" required>
                <span><strong>Moyen</strong> - État correct, travaux à prévoir</span>
              </label>
              <label class="radio-option">
                <input type="radio" name="etat" value="mauvais" required>
                <span><strong>Mauvais</strong> - Nécessite travaux importants</span>
              </label>
            </div>
          </label>

          <!-- INFORMATIONS PERSONNELLES -->
          <h3 class="form-section-title">
            <i class="fas fa-user"></i> Vos informations (optionnel)
          </h3>

          <label for="nom" class="half-width">
            <span>Nom complet</span>
            <input 
              type="text" 
              id="nom" 
              name="nom" 
              placeholder="Jean Dupont"
            >
          </label>

          <label for="email" class="half-width">
            <span>Email</span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              placeholder="jean@exemple.com"
            >
          </label>

          <!-- CHECKBOX RGPD -->
          <div class="form-checkbox full-width">
            <input 
              type="checkbox" 
              id="rgpd-estimation" 
              name="rgpd"
            >
            <label for="rgpd-estimation" class="form-checkbox-label">
              J'accepte la 
              <a href="/politique-confidentialite">politique de confidentialité</a>
            </label>
          </div>

          <!-- SUBMIT -->
          <button 
            type="submit" 
            class="btn btn-primary full-width estimation-submit"
          >
            <i class="fas fa-bolt"></i> Voir mon estimation
          </button>

          <!-- FOOTER INFO -->
          <p class="form-footer form-footer-inline">
            <i class="fas fa-clock"></i> Résultat en 60 secondes • 
            <i class="fas fa-lock"></i> 100% sécurisé • 
            <i class="fas fa-ban"></i> Sans engagement
          </p>
        </form>
      </article>

      <!-- DROITE: AVANTAGES + ÉTAPES -->
      <aside class="estimation-sidebar">
        
        <!-- BLOC 1: AVANTAGES -->
        <article class="card">
          <h3 class="sidebar-title">
            <i class="fas fa-star" style="color: var(--accent);"></i> Avantages
          </h3>
          <ul class="benefits-list">
            <li class="benefits-item">
              <i class="fas fa-check-circle benefits-icon"></i>
              <span style="font-size: 0.9rem;"><strong>100% gratuit</strong> - Aucun frais caché</span>
            </li>
            <li class="benefits-item">
              <i class="fas fa-check-circle benefits-icon"></i>
              <span style="font-size: 0.9rem;"><strong>Rapide</strong> - Résultat en 1 minute</span>
            </li>
            <li class="benefits-item">
              <i class="fas fa-check-circle benefits-icon"></i>
              <span style="font-size: 0.9rem;"><strong>Précis</strong> - ±3% vs prix réel</span>
            </li>
            <li class="benefits-item">
              <i class="fas fa-check-circle benefits-icon"></i>
              <span style="font-size: 0.9rem;"><strong>Données temps réel</strong> - 5000+ transactions</span>
            </li>
            <li class="benefits-item">
              <i class="fas fa-check-circle benefits-icon"></i>
              <span style="font-size: 0.9rem;"><strong>Sans engagement</strong> - Aucune obligation</span>
            </li>
          </ul>
        </article>

        <!-- BLOC 2: ÉTAPES -->
        <article class="card">
          <h3 class="sidebar-title">
            <i class="fas fa-list-ol" style="color: var(--primary);"></i> Étapes
          </h3>
          <div class="steps-list">
            <div class="step-item">
              <div class="step-badge">1</div>
              <div>
                <h4>Remplir le formulaire</h4>
                <p>Infos sur votre bien</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-badge">2</div>
              <div>
                <h4>Analyser les données</h4>
                <p>Notre moteur calcule</p>
              </div>
            </div>
            <div class="step-item">
              <div class="step-badge">3</div>
              <div>
                <h4>Recevoir l'estimation</h4>
                <p>Fourchette de prix détaillée</p>
              </div>
            </div>
          </div>
        </article>

        <!-- BLOC 3: STATISTIQUES -->
        <article class="card">
          <h3 class="sidebar-title">
            <i class="fas fa-chart-bar" style="color: var(--accent);"></i> Nos chiffres
          </h3>
          <div class="stats-grid">
            <div>
              <p class="stat-value">3 847</p>
              <p class="stat-label">Estimations</p>
            </div>
            <div>
              <p class="stat-value">4.8/5</p>
              <p class="stat-label">Satisfaction</p>
            </div>
            <div>
              <p class="stat-value">97%</p>
              <p class="stat-label">Précision</p>
            </div>
            <div>
              <p class="stat-value">60 sec</p>
              <p class="stat-label">Résultat</p>
            </div>
          </div>
        </article>

      </aside>

    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- RÉSULTAT ESTIMATION (EXEMPLE) -->
<!-- ============================================ -->
<section class="section section-alt" id="example-result">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-eye"></i> À quoi ressemble une estimation?
      </p>
      <h2>Exemple de résultat détaillé</h2>
    </div>

    <article class="card">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        
        <!-- GAUCHE: BIEN ESTIMÉ -->
        <div>
          <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem;">Bien estimé</h3>
          <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <p style="margin: 0 0 0.5rem; color: var(--muted); font-size: 0.85rem; text-transform: uppercase;">Localisation</p>
            <p style="margin: 0 0 1rem; font-weight: 600; font-size: 1rem;">Chartrons, Bordeaux</p>
            
            <p style="margin: 0 0 0.5rem; color: var(--muted); font-size: 0.85rem; text-transform: uppercase;">Caractéristiques</p>
            <ul style="margin: 0 0 1rem; padding-left: 1.5rem; font-size: 0.9rem;">
              <li>Type: Appartement</li>
              <li>Surface: 85 m²</li>
              <li>Pièces: 3 (2 chambres)</li>
              <li>Année: 2005</li>
              <li>État: Bon</li>
              <li>Étage: 2ème</li>
            </ul>
          </div>
        </div>

        <!-- DROITE: RÉSULTAT ESTIMATION -->
        <div>
          <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem;">Résultat de l'estimation</h3>
          <div style="background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.05), rgba(var(--accent-rgb), 0.03)); border: 2px solid var(--accent); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- FOURCHETTE PRIX -->
            <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
              <p style="margin: 0 0 0.8rem; color: var(--muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Fourchette de prix</p>
              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.8rem; text-align: center;">
                <div>
                  <p style="margin: 0; color: var(--muted); font-size: 0.8rem;">Min</p>
                  <p style="margin: 0.3rem 0 0; font-weight: 700; font-size: 1.1rem; color: #e24b4a;">280 000 €</p>
                </div>
                <div style="border-left: 1px solid var(--border); border-right: 1px solid var(--border);">
                  <p style="margin: 0; color: var(--muted); font-size: 0.8rem;">Médian</p>
                  <p style="margin: 0.3rem 0 0; font-weight: 700; font-size: 1.1rem; color: var(--primary);">320 000 €</p>
                </div>
                <div>
                  <p style="margin: 0; color: var(--muted); font-size: 0.8rem;">Max</p>
                  <p style="margin: 0.3rem 0 0; font-weight: 700; font-size: 1.1rem; color: #22c55e;">360 000 €</p>
                </div>
              </div>
            </div>

            <!-- PRIX AU M² -->
            <div style="margin-bottom: 1rem;">
              <p style="margin: 0 0 0.5rem; color: var(--muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Prix au m²</p>
              <p style="margin: 0; font-weight: 700; font-size: 1rem;">
                <span style="color: var(--primary);">3 765 €</span>
                <span style="color: var(--muted); font-size: 0.9rem;"> (médian)</span>
              </p>
            </div>

            <!-- ANALYSE -->
            <div style="background: rgba(var(--primary-rgb), 0.05); border-radius: 8px; padding: 1rem; font-size: 0.9rem;">
              <p style="margin: 0 0 0.5rem; font-weight: 600; color: var(--primary);">✓ Analyse positive</p>
              <p style="margin: 0; color: var(--muted); font-size: 0.85rem;">Quartier Chartrons dynamique, prix au m² 12% au-dessus de la moyenne bordelaise, bon potentiel de vente.</p>
            </div>

          </div>
        </div>

      </div>

      <!-- BOUTONS -->
      <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: center;">
        <a href="/estimation/exemple" class="btn btn-primary">
          <i class="fas fa-eye"></i> Voir plus d'exemples
        </a>
        <a href="#form-estimation" class="btn btn-ghost">
          <i class="fas fa-arrow-up"></i> Estimer mon bien
        </a>
      </div>
    </article>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ ESTIMATION -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-question-circle"></i> Questions rapides
      </p>
      <h2>FAQ Estimation</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Quelle est la précision de l'estimation?</h3>
        <p>Précision moyenne ±3% vs prix de vente final. Basée sur 5000+ transactions réelles à Bordeaux.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Faut-il une visite pour estimer?</h3>
        <p>Non. Notre estimation est basée sur les données que vous fournissez. Une visite peut améliorer la précision si vous le souhaitez.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je modifier mon estimation?</h3>
        <p>Oui, autant de fois que vous le souhaitez. Vous pouvez ajuster les paramètres et voir les résultats en temps réel.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Comment utiliser cette estimation?</h3>
        <p>Vous pouvez l'utiliser comme base pour fixer le prix de vente, négocier avec des acheteurs, ou préparer un dossier immobilier.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je vendre sans agence?</h3>
        <p>Oui. L'estimation vous donne une fourchette de prix pour vendre en direct. Un conseil expert peut vous aider.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je être accompagné après?</h3>
        <p>Oui. Laissez vos coordonnées dans le formulaire pour être contacté par un expert. C'est 100% gratuit.</p>
      </article>
    </div>

    <div style="text-align: center; margin-top: 2rem;">
      <a href="/faq" class="btn btn-primary">
        <i class="fas fa-question-circle"></i> Voir toutes les questions
      </a>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="cta-final card">
      <p class="eyebrow">
        <i class="fas fa-bolt"></i> Prêt à découvrir la valeur?
      </p>
      <h2>Commencez votre estimation maintenant</h2>
      <p class="lead">
        Formulaire simple, résultat instantané. Aucun frais, aucun engagement.
      </p>
      <a href="#form-estimation" class="btn btn-primary" style="display: inline-flex; gap: 0.5rem;">
        <i class="fas fa-calculator"></i> Estimer mon bien gratuitement
      </a>
    </div>
  </div>
</section>

<?php require 'app/views/layouts/footer.php'; ?>

<script>
// Fonction pour mettre à jour les champs selon le type de bien
function updateChamps(typeBien) {
  const etagField = document.getElementById('etage');
  const chambreField = document.getElementById('chambres');
  
  if (typeBien === 'terrain') {
    etagField.disabled = true;
    chambreField.disabled = true;
  } else {
    etagField.disabled = false;
    chambreField.disabled = false;
  }
}

// Fonction pour mettre à jour les quartiers
function updateQuartiers(zone) {
  console.log('Zone sélectionnée:', zone);
}

// Form submission
document.getElementById('estimation-form')?.addEventListener('submit', function(e) {
  e.preventDefault();
  // Redirection vers page résultat avec données
  const formData = new FormData(this);
  const params = new URLSearchParams(formData);
  window.location.href = '/estimation/result?' + params.toString();
});
</script>
