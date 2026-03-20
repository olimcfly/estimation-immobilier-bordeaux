</main>

<!-- ================================================ -->
<!-- FOOTER PREMIUM -->
<!-- ================================================ -->
<footer class="site-footer">
  <div class="footer-container">

    <!-- FOOTER MAIN CONTENT -->
    <div class="footer-content">
      <div class="footer-grid">

        <!-- COLONNE 1: BRAND & NEWSLETTER -->
        <div class="footer-column footer-brand">
          <div class="footer-logo">
            <div class="footer-icon">
              <i class="fas fa-home"></i>
            </div>
            <h3 class="footer-brand-name">
              Estimation Immobilier <span>Bordeaux</span>
            </h3>
          </div>

          <p class="footer-tagline">
            Estimez votre bien immobilier gratuitement à Bordeaux et ses alentours. Algorithme IA + expertise locale.
          </p>

          <!-- Newsletter Signup -->
          <div class="footer-newsletter">
            <h4 class="footer-section-title">Rester informé</h4>
            <form class="newsletter-form" method="POST" action="/api/newsletter">
              <input
                type="email"
                name="email"
                placeholder="Votre email..."
                required
                class="newsletter-input"
                aria-label="Email pour newsletter"
              >
              <button type="submit" class="newsletter-btn" aria-label="S'inscrire à la newsletter">
                <i class="fas fa-paper-plane"></i>
              </button>
            </form>
            <p class="newsletter-info">
              <i class="fas fa-lock"></i> Aucun spam, désinscription facile
            </p>
          </div>

          <!-- Réseaux Sociaux -->
          <div class="footer-social">
            <a href="https://facebook.com/estimation-bordeaux"
               class="social-link"
               title="Suivez-nous sur Facebook"
               target="_blank"
               rel="noopener noreferrer">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://instagram.com/estimation-bordeaux"
               class="social-link"
               title="Suivez-nous sur Instagram"
               target="_blank"
               rel="noopener noreferrer">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="https://linkedin.com/company/estimation-bordeaux"
               class="social-link"
               title="Suivez-nous sur LinkedIn"
               target="_blank"
               rel="noopener noreferrer">
              <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://twitter.com/estimation_bdx"
               class="social-link"
               title="Suivez-nous sur Twitter"
               target="_blank"
               rel="noopener noreferrer">
              <i class="fab fa-twitter"></i>
            </a>
          </div>
        </div>

        <!-- COLONNE 2: ESTIMATION & RESSOURCES -->
        <div class="footer-column">
          <h4 class="footer-section-title">
            <i class="fas fa-calculator"></i> Estimation
          </h4>
          <ul class="footer-links">
            <li><a href="/#form-estimation">Estimer mon bien</a></li>
            <li><a href="/#example-result">Voir un exemple</a></li>
            <li><a href="/processus-estimation">Notre processus</a></li>
            <li><a href="/#how-it-works">Comment ça marche</a></li>
            <li><a href="/quartiers">Nos quartiers</a></li>
          </ul>

          <h4 class="footer-section-title" style="margin-top: var(--space-6);">
            <i class="fas fa-book-open"></i> Ressources
          </h4>
          <ul class="footer-links">
            <li><a href="/guides">Guides complets</a></li>
            <li><a href="/podcast">Podcast immobilier</a></li>
            <li><a href="/newsletter">Newsletter</a></li>
            <li><a href="/blog">Blog & articles</a></li>
          </ul>
        </div>

        <!-- COLONNE 3: ENTREPRISE & SUPPORT -->
        <div class="footer-column">
          <h4 class="footer-section-title">
            <i class="fas fa-building"></i> Entreprise
          </h4>
          <ul class="footer-links">
            <li><a href="/a-propos">À propos de nous</a></li>
            <li><a href="/contact">Nous contacter</a></li>
            <li><a href="tel:+33556000000">+33 5 56 00 00 00</a></li>
            <li><a href="mailto:contact@estimation-immobilier-bordeaux.fr">contact@estimation-immobilier-bordeaux.fr</a></li>
            <li><a href="/a-propos">Notre équipe</a></li>
          </ul>

          <h4 class="footer-section-title" style="margin-top: var(--space-6);">
            <i class="fas fa-headset"></i> Support
          </h4>
          <ul class="footer-links">
            <li><a href="/#faq">Questions fréquentes</a></li>
            <li><a href="/contact">Formulaire de contact</a></li>
            <li><a href="/mentions-legales#help">Centre d'aide</a></li>
            <li><a href="/mentions-legales#bugs">Signaler un bug</a></li>
          </ul>
        </div>

        <!-- COLONNE 4: LEGAL & CONFORMITÉ -->
        <div class="footer-column">
          <h4 class="footer-section-title">
            <i class="fas fa-shield-alt"></i> Conformité
          </h4>
          <ul class="footer-links">
            <li><a href="/mentions-legales">Mentions légales</a></li>
            <li><a href="/politique-confidentialite">Politique de confidentialité</a></li>
            <li><a href="/conditions-utilisation">Conditions d'utilisation</a></li>
            <li><a href="/rgpd">Politique de cookies</a></li>
            <li><a href="/rgpd">Conformité RGPD</a></li>
          </ul>

          <h4 class="footer-section-title" style="margin-top: var(--space-6);">
            <i class="fas fa-certificate"></i> Certifications
          </h4>
          <div class="footer-badges">
            <span class="badge">
              <i class="fas fa-lock"></i> SSL Sécurisé
            </span>
            <span class="badge">
              <i class="fas fa-shield-alt"></i> RGPD
            </span>
            <span class="badge">
              <i class="fas fa-check-circle"></i> Vérifiée
            </span>
          </div>
        </div>

      </div>
    </div>

    <!-- FOOTER BOTTOM -->
    <div class="footer-bottom">
      <div class="footer-bottom-container">
        <p class="footer-copyright">
          &copy; 2026 Estimation Immobilier Bordeaux. Tous droits réservés. SAS OCDM Agency.
        </p>
        <div class="footer-bottom-links">
          <a href="#top" class="back-to-top">
            <i class="fas fa-arrow-up"></i> Retour en haut
          </a>
        </div>
      </div>
    </div>

  </div>
</footer>

<script>
  // Property image alt text
  document.querySelectorAll('img[data-address][data-bedrooms]').forEach((propertyImage) => {
    const address = (propertyImage.dataset.address || '').trim();
    const bedrooms = (propertyImage.dataset.bedrooms || '').trim();
    if (!address || !bedrooms) return;
    propertyImage.alt = `${address} - ${bedrooms} pièces`;
  });

  // Mobile menu toggle
  (function() {
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.top-nav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function() {
      nav.classList.toggle('active');
      toggle.setAttribute('aria-expanded', nav.classList.contains('active'));
    });

    // Mobile dropdown toggles (touch-friendly)
    document.querySelectorAll('.has-dropdown > .nav-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          const parent = this.parentElement;
          document.querySelectorAll('.has-dropdown').forEach(function(d) {
            if (d !== parent) d.classList.remove('active');
          });
          parent.classList.toggle('active');
        }
      });
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        nav.classList.remove('active');
        document.querySelectorAll('.has-dropdown').forEach(function(d) {
          d.classList.remove('active');
        });
      }
    });
  })();
</script>

</body>
</html>
