</main>

<!-- ================================================ -->
<!-- FOOTER PRO -->
<!-- ================================================ -->

<!-- PRE-FOOTER CTA -->
<section class="footer-cta-band">
  <div class="container">
    <div class="footer-cta-inner">
      <div class="footer-cta-text">
        <h3>Estimez votre bien immobilier gratuitement</h3>
        <p>Algorithme IA + expertise locale pour une estimation fiable en quelques minutes.</p>
      </div>
      <a href="/#form-estimation" class="btn-footer-cta">
        <i class="fas fa-chart-line"></i> Estimer mon bien
      </a>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="container">

    <!-- FOOTER MAIN -->
    <div class="footer-grid">

      <!-- COL 1: BRAND -->
      <div class="footer-column footer-col-brand">
        <a href="/" class="footer-logo-link">
          <span class="footer-logo-icon"><i class="fas fa-home"></i></span>
          <span class="footer-logo-text">Estimation Immobilier <strong>Bordeaux</strong></span>
        </a>
        <p class="footer-desc">
          Votre partenaire de confiance pour l'estimation immobilière sur Bordeaux et la métropole bordelaise depuis 2020.
        </p>
        <div class="footer-social">
          <a href="https://facebook.com/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://instagram.com/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="https://linkedin.com/company/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://twitter.com/estimation_bdx" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="social-icon"><i class="fab fa-x-twitter"></i></a>
        </div>
      </div>

      <!-- COL 2: SERVICES -->
      <div class="footer-column">
        <h4 class="footer-heading">Services</h4>
        <ul class="footer-links">
          <li><a href="/#form-estimation">Estimation en ligne</a></li>
          <li><a href="/processus-estimation">Notre processus</a></li>
          <li><a href="/quartiers">Quartiers de Bordeaux</a></li>
          <li><a href="/#how-it-works">Comment ça marche</a></li>
          <li><a href="/#example-result">Voir un exemple</a></li>
        </ul>
      </div>

      <!-- COL 3: RESSOURCES -->
      <div class="footer-column">
        <h4 class="footer-heading">Ressources</h4>
        <ul class="footer-links">
          <li><a href="/blog">Blog & actualités</a></li>
          <li><a href="/guides">Guides immobiliers</a></li>
          <li><a href="/#faq">FAQ</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
        </ul>
      </div>

      <!-- COL 4: ENTREPRISE -->
      <div class="footer-column">
        <h4 class="footer-heading">Entreprise</h4>
        <ul class="footer-links">
          <li><a href="/a-propos">À propos</a></li>
          <li><a href="/contact">Contact</a></li>
          <li><a href="/mentions-legales">Mentions légales</a></li>
          <li><a href="/politique-confidentialite">Confidentialité</a></li>
          <li><a href="/conditions-utilisation">CGU</a></li>
        </ul>
      </div>

      <!-- COL 5: CONTACT -->
      <div class="footer-column">
        <h4 class="footer-heading">Nous contacter</h4>
        <ul class="footer-contact">
          <li>
            <i class="fas fa-map-marker-alt"></i>
            <span>Bordeaux, 33000<br>Nouvelle-Aquitaine</span>
          </li>
          <li>
            <a href="tel:+33556000000">
              <i class="fas fa-phone"></i>
              <span>05 56 00 00 00</span>
            </a>
          </li>
          <li>
            <a href="mailto:contact@estimation-immobilier-bordeaux.fr">
              <i class="fas fa-envelope"></i>
              <span>contact@estimation-immobilier-bordeaux.fr</span>
            </a>
          </li>
        </ul>
      </div>

    </div>

    <!-- NEWSLETTER -->
    <div class="footer-newsletter-band">
      <div class="footer-newsletter-text">
        <i class="fas fa-envelope-open-text"></i>
        <div>
          <strong>Restez informé</strong>
          <span>Recevez nos analyses du marché bordelais et nos conseils immobiliers.</span>
        </div>
      </div>
      <form class="footer-newsletter-form" method="POST" action="/api/newsletter">
        <input type="email" name="email" placeholder="Votre adresse email" required aria-label="Email pour newsletter">
        <button type="submit">S'inscrire</button>
      </form>
    </div>

    <!-- FOOTER BOTTOM -->
    <div class="footer-bottom">
      <div class="footer-bottom-left">
        <p>&copy; 2026 Estimation Immobilier Bordeaux &mdash; SAS OCDM Agency. Tous droits réservés.</p>
      </div>
      <div class="footer-bottom-right">
        <div class="footer-trust">
          <span class="trust-badge"><i class="fas fa-lock"></i> SSL</span>
          <span class="trust-badge"><i class="fas fa-shield-alt"></i> RGPD</span>
          <span class="trust-badge"><i class="fas fa-check-circle"></i> Vérifié</span>
        </div>
        <a href="#top" class="back-to-top" aria-label="Retour en haut">
          <i class="fas fa-chevron-up"></i>
        </a>
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
