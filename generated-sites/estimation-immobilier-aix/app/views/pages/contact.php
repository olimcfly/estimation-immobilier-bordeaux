<?php $page_title = 'Contact - Estimation Immobilière Aix-en-Provence | Nous Sommes Disponibles'; ?>

<!-- ============================================ -->
<!-- HERO SECTION -->
<!-- ============================================ -->
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow">
        <i class="fas fa-envelope"></i> Nous contacter
      </p>
      <h1>Parlons de votre projet immobilier</h1>
      <p class="lead">
        Besoin d'un avis expert ? Des questions sur une estimation ? Notre équipe de aix-en-provencee est disponible pour vous accompagner.
      </p>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CONTACT INFO + FORMULAIRE -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="contact-layout">
      
      <!-- GAUCHE: INFOS CONTACT -->
      <article class="card contact-info">
        <h2>
          <i class="fas fa-map-marker-alt"></i> Nos coordonnées
        </h2>

        <!-- TÉLÉPHONE -->
        <div class="info-block">
          <p class="info-label">
            <i class="fas fa-phone"></i> Téléphone
          </p>
          <p class="info-value">
            <a href="tel:+33556000000">+33 5 56 00 00 00</a>
          </p>
          <ul class="hours-list">
            <li>
              <span>Lundi - Vendredi :</span>
              <strong>9h - 19h</strong>
            </li>
            <li>
              <span>Samedi :</span>
              <strong>10h - 17h</strong>
            </li>
            <li>
              <span>Dimanche :</span>
              <strong>Fermé</strong>
            </li>
          </ul>
        </div>

        <!-- EMAIL -->
        <div class="info-block">
          <p class="info-label">
            <i class="fas fa-envelope"></i> Email
          </p>
          <p class="info-value">
            <a href="mailto:contact@estimation-immobilier-aix.fr">
              contact@estimation-immobilier-aix.fr
            </a>
          </p>
          <p class="info-desc">
            Réponse garantie en moins de 24h
          </p>
        </div>

        <!-- ADRESSE -->
        <div class="info-block">
          <p class="info-label">
            <i class="fas fa-map-marker-alt"></i> Adresse
          </p>
          <p class="info-value">
            12 Quai des Chartrons<br>
            13100 Aix-en-Provence<br>
            France
          </p>
          <p class="info-desc">
            Parking gratuit • Accès PMR
          </p>
        </div>

        <!-- RÉSEAUX SOCIAUX -->
        <div class="info-block">
          <p class="info-label">
            <i class="fas fa-share-alt"></i> Suivez-nous
          </p>
          <div style="display: flex; gap: 1rem; margin-top: 0.8rem;">
            <a href="#facebook" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(var(--primary-rgb), 0.08); border-radius: 10px; color: var(--primary); text-decoration: none; transition: all 0.2s; border: 1px solid rgba(var(--primary-rgb), 0.15);" title="Facebook">
              <i class="fab fa-facebook-f" style="font-size: 1.2rem;"></i>
            </a>
            <a href="#instagram" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(var(--primary-rgb), 0.08); border-radius: 10px; color: var(--primary); text-decoration: none; transition: all 0.2s; border: 1px solid rgba(var(--primary-rgb), 0.15);" title="Instagram">
              <i class="fab fa-instagram" style="font-size: 1.2rem;"></i>
            </a>
            <a href="#linkedin" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(var(--primary-rgb), 0.08); border-radius: 10px; color: var(--primary); text-decoration: none; transition: all 0.2s; border: 1px solid rgba(var(--primary-rgb), 0.15);" title="LinkedIn">
              <i class="fab fa-linkedin-in" style="font-size: 1.2rem;"></i>
            </a>
          </div>
        </div>
      </article>

      <!-- DROITE: FORMULAIRE CONTACT -->
      <article class="card contact-form-card">
        <h2>
          <i class="fas fa-comment-dots"></i> Envoyez-nous un message
        </h2>
        <p class="form-intro">
          Remplissez ce formulaire et nous vous recontacterons rapidement. Merci de votre intérêt !
        </p>

        <form class="form-grid form-contact" action="/contact" method="post">
          <!-- NOM -->
          <label for="nom" class="full-width">
            <span><i class="fas fa-user"></i> Nom complet *</span>
            <input 
              type="text" 
              id="nom" 
              name="nom" 
              placeholder="Jean Dupont" 
              required
            >
          </label>

          <!-- EMAIL -->
          <label for="email" class="full-width">
            <span><i class="fas fa-envelope"></i> Email *</span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              placeholder="jean@exemple.com" 
              required
            >
          </label>

          <!-- TÉLÉPHONE -->
          <label for="telephone" class="full-width">
            <span><i class="fas fa-phone"></i> Téléphone</span>
            <input 
              type="tel" 
              id="telephone" 
              name="telephone" 
              placeholder="+33 5 56 00 00 00"
            >
          </label>

          <!-- SUJET -->
          <label for="sujet" class="full-width">
            <span><i class="fas fa-list"></i> Sujet *</span>
            <select id="sujet" name="sujet" required>
              <option value="">-- Sélectionner un sujet --</option>
              <option value="estimation">Question sur une estimation</option>
              <option value="accompagnement">Demander un accompagnement</option>
              <option value="conseil">Conseil immobilier</option>
              <option value="partenariat">Partenariat / Collaboration</option>
              <option value="probleme">Signaler un problème</option>
              <option value="autre">Autre question</option>
            </select>
          </label>

          <!-- QUARTIER/VILLE -->
          <label for="quartier" class="full-width">
            <span><i class="fas fa-map-marker-alt"></i> Quartier / Ville concerné(e)</span>
            <input 
              type="text" 
              id="quartier" 
              name="quartier" 
              placeholder="Ex: Chartrons, Talence, Floirac..."
            >
          </label>

          <!-- MESSAGE -->
          <label for="message" class="full-width">
            <span><i class="fas fa-pen"></i> Votre message *</span>
            <textarea 
              id="message" 
              name="message" 
              placeholder="Décrivez votre situation, vos questions..." 
              rows="6"
              required
            ></textarea>
          </label>

          <!-- CHECKBOX RGPD -->
          <div class="form-checkbox full-width">
            <input 
              type="checkbox" 
              id="rgpd" 
              name="rgpd" 
              required
            >
            <label for="rgpd" style="margin: 0; font-weight: 500; font-size: 0.9rem; color: var(--text); cursor: pointer;">
              J'accepte la 
              <a href="/politique-confidentialite">politique de confidentialité</a> 
              et je consens à recevoir des communications de la part d'Estimation Aix-en-Provence *
            </label>
          </div>

          <!-- SUBMIT -->
          <button 
            type="submit" 
            class="btn btn-primary full-width" 
            style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; font-size: 1rem; padding: 1rem;"
          >
            <i class="fas fa-paper-plane"></i> Envoyer mon message
          </button>

          <!-- LEGAL INFO -->
          <p class="form-footer" style="text-align: center; margin-top: 1rem; font-size: 0.8rem; grid-column: 1 / -1;">
            <i class="fas fa-lock"></i> Vos données sont sécurisées. 
            <i class="fas fa-check-circle"></i> Nous vous répondrons rapidement.
          </p>
        </form>
      </article>

    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- SERVICES / MOYENS DE CONTACT -->
<!-- ============================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-headset"></i> Autres moyens de nous contacter
      </p>
      <h2>Choisissez le canal qui vous convient</h2>
    </div>

    <div class="service-grid">
      <!-- MOYEN 1: TÉLÉPHONE -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-phone"></i> Par téléphone
        </h3>
        <p>
          Parlez directement à un expert. Réponses immédiates à vos questions. 
          Disponible du lundi au samedi, 9h-19h.
        </p>
        <a href="tel:+33556000000" class="btn btn-small" style="margin-top: 1rem;">
          <i class="fas fa-phone"></i> Appeler maintenant
        </a>
      </article>

      <!-- MOYEN 2: EMAIL -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-envelope"></i> Par email
        </h3>
        <p>
          Envoyez-nous un email détaillé. Notre équipe vous répond en moins de 24h. 
          Idéal pour les questions complexes.
        </p>
        <a href="mailto:contact@estimation-immobilier-aix.fr" class="btn btn-small" style="margin-top: 1rem;">
          <i class="fas fa-envelope"></i> Envoyer un email
        </a>
      </article>

      <!-- MOYEN 3: FORMULAIRE -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-comment-dots"></i> Via le formulaire
        </h3>
        <p>
          Remplissez le formulaire ci-dessus. Nous traiterons votre demande 
          en priorité et vous recontacterons rapidement.
        </p>
        <a href="#form-contact" class="btn btn-small" style="margin-top: 1rem;">
          <i class="fas fa-arrow-down"></i> Aller au formulaire
        </a>
      </article>

      <!-- MOYEN 4: CHAT DIRECT -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-comments"></i> Chat en direct
        </h3>
        <p>
          Discussions instantanées avec nos experts (disponible du lundi 
          au vendredi, 9h-18h). Support immédiat pour vos questions.
        </p>
        <button class="btn btn-small" style="margin-top: 1rem; cursor: pointer;">
          <i class="fas fa-comments"></i> Ouvrir le chat
        </button>
      </article>

      <!-- MOYEN 5: RÉSEAUX SOCIAUX -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-share-alt"></i> Sur les réseaux sociaux
        </h3>
        <p>
          Suivez-nous sur Facebook, Instagram et LinkedIn. Actualités, 
          conseils immobiliers et réponses à vos questions.
        </p>
        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
          <a href="#facebook" class="btn btn-small" style="flex: 1; justify-content: center;">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#instagram" class="btn btn-small" style="flex: 1; justify-content: center;">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#linkedin" class="btn btn-small" style="flex: 1; justify-content: center;">
            <i class="fab fa-linkedin-in"></i>
          </a>
        </div>
      </article>

      <!-- MOYEN 6: RENDEZ-VOUS -->
      <article class="card service-additional">
        <h3>
          <i class="fas fa-calendar"></i> Prendre rendez-vous
        </h3>
        <p>
          Préférez une consultation en personne ? Prenez rendez-vous 
          avec nos experts au siège à Aix-en-Provence.
        </p>
        <button class="btn btn-small" style="margin-top: 1rem; cursor: pointer;">
          <i class="fas fa-calendar"></i> Prendre RDV
        </button>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ CONTACT -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-question-circle"></i> Questions fréquentes
      </p>
      <h2>Réponses rapides</h2>
    </div>

    <div class="faq-grid">
      <!-- FAQ 1 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel est le meilleur moyen pour me contacter?
        </h3>
        <p>
          Cela dépend de votre besoin. Pour une question rapide, appelez-nous. 
          Pour une demande détaillée, utilisez le formulaire ou l'email. Le chat est parfait pour les urgences.
        </p>
      </article>

      <!-- FAQ 2 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Combien de temps pour obtenir une réponse?
        </h3>
        <p>
          Par téléphone : immédiat. Par email/formulaire : moins de 24h. 
          Par chat : réponse en quelques minutes (heures de bureau).
        </p>
      </article>

      <!-- FAQ 3 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Êtes-vous disponibles le week-end?
        </h3>
        <p>
          Le samedi de 10h à 17h. Le dimanche fermé. Pour les urgences en dehors des heures, 
          utilisez le formulaire et nous vous répondrons dès lundi.
        </p>
      </article>

      <!-- FAQ 4 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Puis-je visiter vos bureaux?
        </h3>
        <p>
          Oui ! Sur rendez-vous. 12 Quai des Chartrons, Aix-en-Provence. 
          Parking gratuit et accès PMR disponibles.
        </p>
      </article>

      <!-- FAQ 5 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Mes données sont-elles sécurisées?
        </h3>
        <p>
          Absolument. Chiffrement SSL/TLS, RGPD conforme. Vos données ne sont jamais vendues. 
          Nous respectons votre confidentialité.
        </p>
      </article>

      <!-- FAQ 6 -->
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Puis-je demander un rappel?
        </h3>
        <p>
          Oui, indiquez-le dans le formulaire. Nous vous recontacterons au moment qui vous convient. 
          Vous pouvez aussi nous appeler directement.
        </p>
      </article>
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
        <i class="fas fa-hands-helping"></i> Pas encore estimé?
      </p>
      <h2>Commencez par une estimation gratuite</h2>
      <p class="lead">
        Avant de nous contacter, découvrez la fourchette de prix de votre bien. 
        100% gratuit, résultat en 1 minute.
      </p>
      <a href="/#form-estimation" class="btn btn-primary">
        <i class="fas fa-calculator"></i> Estimer mon bien
      </a>
    </div>
  </div>
</section>
