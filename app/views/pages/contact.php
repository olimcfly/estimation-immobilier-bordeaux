<?php if (!empty($success_message)): ?>
<section class="section">
  <div class="container">
    <div class="card" style="border-left: 4px solid #2e7d32;">
      <p><strong><?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?></strong></p>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- HERO PAGE -->
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-envelope"></i> Nous contacter</p>
      <h1>Parlons de votre projet immobilier</h1>
      <p class="lead">Besoin d'un avis expert avant de vendre ? Questions sur une estimation ? Notre équipe bordelaise vous répond rapidement et sans engagement.</p>
    </div>
  </div>
</section>

<!-- CONTACT INFO & FORM -->
<section class="section">
  <div class="container">
    <div class="contact-layout">
      <!-- COORDONNÉES & HORAIRES -->
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

        <div class="info-block">
          <p class="info-label"><i class="fas fa-map-pin"></i> Adresse</p>
          <p class="info-value">12 Quai des Chartrons</p>
          <p class="info-value">33000 Bordeaux</p>
          <p class="info-desc">Quartier historique du Vieux Bordeaux</p>
        </div>

        <div class="info-block">
          <p class="info-label"><i class="fas fa-clock"></i> Horaires d'ouverture</p>
          <ul class="hours-list">
            <li><span>Lundi - Vendredi</span> <strong>9h - 19h</strong></li>
            <li><span>Samedi</span> <strong>10h - 17h</strong></li>
            <li><span>Dimanche</span> <strong>Fermé</strong></li>
          </ul>
        </div>
      </article>

      <!-- FORMULAIRE CONTACT -->
      <article class="card contact-form-card">
        <h2><i class="fas fa-comment-dots"></i> Envoyez-nous un message</h2>
        <p class="form-intro">Remplissez ce formulaire et nous vous recontacterons rapidement pour discuter de votre projet.</p>

        <form class="form-grid form-contact" action="/contact" method="post">
          <div class="form-row">
            <label for="nom">
              <span>Nom complet *</span>
              <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required>
            </label>

            <label for="email">
              <span>Email *</span>
              <input type="email" id="email" name="email" placeholder="jean@email.com" required>
            </label>
          </div>

          <label for="telephone">
            <span>Téléphone *</span>
            <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" required>
          </label>

          <label for="sujet">
            <span>Sujet de votre demande *</span>
            <select id="sujet" name="sujet" required>
              <option value="">-- Sélectionner --</option>
              <option value="estimation">Estimer mon bien</option>
              <option value="vente">Conseil pour ma vente</option>
              <option value="question">Question technique</option>
              <option value="accompagnement">Accompagnement personnalisé</option>
              <option value="autre">Autre demande</option>
            </select>
          </label>

          <label for="ville">
            <span>Localisation du bien (si applicable)</span>
            <input type="text" id="ville" name="ville" placeholder="Ex : Bordeaux, Talence, Pessac">
          </label>

          <label for="message">
            <span>Votre message *</span>
            <textarea id="message" name="message" placeholder="Décrivez votre situation ou vos questions..." rows="5" required></textarea>
          </label>

          <div class="form-checkbox">
            <input type="checkbox" id="consentement" name="consentement" required>
            <label for="consentement">
              J'accepte que mes données soient utilisées pour répondre à ma demande. <a href="/mentions-legales">Politique de confidentialité</a>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-paper-plane"></i> Envoyer le message
          </button>

          <p class="form-footer">
            <i class="fas fa-lock"></i> Vos données sont sécurisées et confidentielles.
          </p>
        </form>
      </article>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Nos services</p>
      <h2>Tout ce que nous pouvons faire pour vous</h2>
    </div>

    <div class="services-grid">
      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-calculator"></i>
        </div>
        <h3>Estimation gratuite</h3>
        <p>Obtenez une première évaluation de votre bien en moins d'une minute, 100% gratuit et sans engagement.</p>
        <a href="/#simulateur" class="service-link">Estimer mon bien <i class="fas fa-arrow-right"></i></a>
      </article>

      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-user-tie"></i>
        </div>
        <h3>Accompagnement vendeur</h3>
        <p>Un expert vous conseille pour optimiser votre stratégie de vente et maximiser le prix de votre propriété.</p>
        <a href="#contact-form" class="service-link">Demander un conseil <i class="fas fa-arrow-right"></i></a>
      </article>

      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <h3>Analyse de marché</h3>
        <p>Recevez une analyse détaillée du marché immobilier de votre quartier et des tendances actuelles.</p>
        <a href="#contact-form" class="service-link">Demander l'analyse <i class="fas fa-arrow-right"></i></a>
      </article>

      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-handshake"></i>
        </div>
        <h3>Suivi de projet</h3>
        <p>Nous vous accompagnons jusqu'à la signature pour sécuriser votre transaction et atteindre vos objectifs.</p>
        <a href="#contact-form" class="service-link">Activer le suivi <i class="fas fa-arrow-right"></i></a>
      </article>

      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-video"></i>
        </div>
        <h3>Consultation vidéo</h3>
        <p>Préférez un appel vidéo ? Programmez une consultation avec un de nos experts à votre convenance.</p>
        <a href="#contact-form" class="service-link">Programmer un appel <i class="fas fa-arrow-right"></i></a>
      </article>

      <article class="card service-card">
        <div class="service-icon">
          <i class="fas fa-book"></i>
        </div>
        <h3>Guides & ressources</h3>
        <p>Accédez à nos guides complets pour bien préparer votre vente immobilière à Bordeaux.</p>
        <a href="/ressources" class="service-link">Voir les ressources <i class="fas fa-arrow-right"></i></a>
      </article>
    </div>
  </div>
</section>

<!-- RESPONSE TIME -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Réactivité garantie</p>
      <h2>Nous nous engageons à vous répondre rapidement</h2>
    </div>

    <div class="response-grid">
      <div class="response-card card">
        <div class="response-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <p class="response-time"><strong>Moins de 24h</strong></p>
        <p class="response-label">Par email</p>
        <p class="response-desc">Nous répondons à tous les emails en moins d'une journée ouvrable.</p>
      </div>

      <div class="response-card card">
        <div class="response-icon">
          <i class="fas fa-phone"></i>
        </div>
        <p class="response-time"><strong>Jour même</strong></p>
        <p class="response-label">Par téléphone</p>
        <p class="response-desc">Appelez-nous pendant les horaires d'ouverture pour une réponse immédiate.</p>
      </div>

      <div class="response-card card">
        <div class="response-icon">
          <i class="fas fa-mobile-alt"></i>
        </div>
        <p class="response-time"><strong>2-4h</strong></p>
        <p class="response-label">Par SMS</p>
        <p class="response-desc">Besoin d'une réponse rapide ? Envoyez-nous un SMS pour un contact immédiat.</p>
      </div>

      <div class="response-card card">
        <div class="response-icon">
          <i class="fas fa-comments"></i>
        </div>
        <p class="response-time"><strong>30 min</strong></p>
        <p class="response-label">Instant messaging</p>
        <p class="response-desc">Disponibilité 24/7 via WhatsApp ou Messenger pour les questions urgentes.</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ CONTACT -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Questions fréquentes</p>
      <h2>Tout ce que vous devez savoir</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Quel est le meilleur moment pour me contacter ?</h3>
        <p>Nous sommes disponibles du lundi au vendredi de 9h à 19h, et le samedi de 10h à 17h. Vous pouvez nous contacter à tout moment via email ou formulaire, nous répondons rapidement.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Faut-il un rendez-vous pour être conseillé ?</h3>
        <p>Non, vous pouvez commencer par une estimation gratuite en ligne. Si vous avez besoin d'un conseil personnalisé, nous pouvons organiser un appel ou une visite selon votre préférence.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Y a-t-il des frais pour une consultation ?</h3>
        <p>L'estimation en ligne est 100% gratuite. Notre accompagnement personnalisé peut être proposé sous différentes formes selon vos besoins. Nous discutons toujours de la tarification avant d'engager un processus.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Pouvez-vous venir visiter mon bien ?</h3>
        <p>Oui, nous pouvons organiser une visite si vous êtes intéressé par un accompagnement personnalisé. Contactez-nous pour programmer une visite adaptée à votre emploi du temps.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Comment se passe une consultation vidéo ?</h3>
        <p>Après validation de votre demande, nous vous envoyons un lien pour participer à une vidéoconférence sécurisée. Pas besoin de télécharger d'application, tout se fait via navigateur.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Que se passe-t-il après ma demande ?</h3>
        <p>Nous vous contactons rapidement pour comprendre votre situation et vos besoins. Ensuite, nous vous proposons les solutions les plus adaptées : estimation, conseil, accompagnement, etc.</p>
      </article>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="section">
  <div class="container">
    <div class="cta-contact card" id="contact-form">
      <p class="eyebrow"><i class="fas fa-lightbulb"></i> Pas encore décidé ?</p>
      <h2>Commencez par une estimation gratuite</h2>
      <p class="lead">Aucun engagement, 100% confidentiel. Découvrez la valeur de votre bien en moins d'une minute.</p>
      <a href="/#simulateur" class="btn btn-primary">Estimer mon bien gratuitement</a>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
