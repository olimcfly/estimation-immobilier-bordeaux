<!-- À insérer dans ta section <main> -->

<!-- RÉSULTAT ESTIMATION -->
<section class="estimation-result">
  <div class="container">
    <div class="result-layout">
      <!-- GAUCHE : ESTIMATION -->
      <article class="card result-summary">
        <div class="result-header">
          <p class="eyebrow"><i class="fas fa-check-circle"></i> Estimation obtenue</p>
          <h2>Votre estimation à <?= e((string) $estimate['city']) ?></h2>
          <p class="muted">Voici la fourchette de valeur calculée pour votre bien immobilier.</p>
        </div>

        <!-- KPI GRID -->
        <div class="kpi-grid">
          <div class="kpi-box kpi-low">
            <p class="kpi-label"><i class="fas fa-arrow-down"></i> Prix basse</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> €</p>
          </div>
          <div class="kpi-box kpi-mid">
            <p class="kpi-label"><i class="fas fa-bullseye"></i> Estimation moyenne</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> €</p>
          </div>
          <div class="kpi-box kpi-high">
            <p class="kpi-label"><i class="fas fa-arrow-up"></i> Prix haute</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> €</p>
          </div>
        </div>

        <!-- PRIX AU M² -->
        <div class="result-detail">
          <p class="detail-label">Prix moyen au m²</p>
          <p class="detail-value"><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> €/m²</p>
          <p class="detail-info">Fourchette : <?= number_format((float) $estimate['per_sqm_low'], 0, ',', ' ') ?> - <?= number_format((float) $estimate['per_sqm_high'], 0, ',', ' ') ?> €/m²</p>
        </div>
      </article>

      <!-- DROITE : CTA LEAD -->
      <div class="result-cta-section">
        <article class="card lead-cta">
          <div class="cta-header">
            <p class="eyebrow"><i class="fas fa-handshake"></i> Passer à l'action</p>
            <h3>Transformez cette estimation en projet</h3>
            <p class="muted">Laissez vos coordonnées pour être accompagné par un expert et concrétiser votre vente.</p>
          </div>

          <div class="cta-benefits">
            <p class="benefit"><i class="fas fa-check"></i> Analyse personnalisée</p>
            <p class="benefit"><i class="fas fa-check"></i> Stratégie de vente</p>
            <p class="benefit"><i class="fas fa-check"></i> Accompagnement expert</p>
          </div>

          <a href="#lead-form" class="btn btn-primary btn-full">
            <i class="fas fa-phone-alt"></i> Je veux être recontacté
          </a>
        </article>
      </div>
    </div>
  </div>
</section>

<!-- FORMULAIRE LEAD -->
<section class="lead-section">
  <div class="container">
    <article class="card" id="lead-form">
      <div class="form-header">
        <p class="eyebrow"><i class="fas fa-form"></i> Finalisez votre demande</p>
        <h3>Vos coordonnées pour être rappelé</h3>
        <p class="muted">Nous vous recontacterons dans les 2 heures pour affiner votre stratégie de vente.</p>
      </div>

      <form action="/lead" method="post" class="form-grid form-lead">
        <!-- CHAMPS CACHÉS -->
        <input type="hidden" name="ville" value="<?= e((string) $estimate['city']) ?>">
        <input type="hidden" name="estimation" value="<?= e((string) $estimate['estimated_mid']) ?>">

        <!-- IDENTITÉ -->
        <div class="form-section">
          <h4><i class="fas fa-user"></i> Vos informations</h4>

          <div class="form-row">
            <label for="nom">
              <span>Nom complet *</span>
              <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required>
            </label>

            <label for="email">
              <span>Email *</span>
              <input type="email" id="email" name="email" placeholder="jean@example.com" required>
            </label>
          </div>

          <div class="form-row">
            <label for="telephone">
              <span>Téléphone *</span>
              <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" required>
            </label>

            <label for="nom_contact">
              <span>Préféré pour le contact</span>
              <select id="nom_contact" name="contact_prefere">
                <option value="">-- Choisir --</option>
                <option value="telephone">Téléphone</option>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
              </select>
            </label>
          </div>
        </div>

        <!-- PROJET -->
        <div class="form-section">
          <h4><i class="fas fa-calendar-alt"></i> Votre projet</h4>

          <div class="form-row">
            <label for="urgence">
              <span>Délai de vente *</span>
              <select id="urgence" name="urgence" required>
                <option value="">-- Sélectionner --</option>
                <option value="rapide">Rapide (moins de 3 mois)</option>
                <option value="moyen">Moyen (3-6 mois)</option>
                <option value="long">Long terme (6+ mois)</option>
              </select>
            </label>

            <label for="motivation">
              <span>Raison de la vente *</span>
              <select id="motivation" name="motivation" required>
                <option value="">-- Sélectionner --</option>
                <option value="vente">Vente classique</option>
                <option value="succession">Succession</option>
                <option value="divorce">Divorce/Séparation</option>
                <option value="investissement">Investissement</option>
                <option value="autre">Autre</option>
              </select>
            </label>
          </div>

          <label for="message" class="full-width">
            <span>Message (optionnel)</span>
            <textarea id="message" name="message" placeholder="Parlez-nous de votre projet..." rows="4" style="width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--border); border-radius: 10px; font-family: inherit; resize: vertical;"></textarea>
          </label>
        </div>

        <!-- ACTIONS -->
        <div class="form-actions">
          <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-check"></i> Enregistrer et être recontacté
          </button>
          <p class="form-legal">
            <i class="fas fa-lock"></i> Vos données sont sécurisées et confidentielles. <a href="/mentions-legales">En savoir plus</a>
          </p>
        </div>
      </form>
    </article>
  </div>
</section>
