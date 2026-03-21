<?php $page_title = 'Résultat de votre estimation - Avis de Valeur Indicatif'; ?>

<!-- ============================================ -->
<!-- RÉSULTAT ESTIMATION - FOURCHETTE 3 PRIX -->
<!-- ============================================ -->
<section class="estimation-result">
  <div class="container">

    <!-- EN-TÊTE RÉSULTAT -->
    <div class="section-heading" style="margin-bottom: 2rem;">
      <p class="eyebrow"><i class="fas fa-chart-bar"></i> Estimation indicative obtenue</p>
      <h1>Votre fourchette de prix à <?= e((string) $estimate['city']) ?></h1>
      <p class="muted" style="max-width: 700px; margin: 0.5rem auto 0;">
        Voici une estimation statistique basée sur les données du marché pour votre
        <strong><?= e((string) $estimate['property_type']) ?></strong> de
        <strong><?= number_format((float) $estimate['surface'], 0, ',', ' ') ?> m²</strong>.
      </p>
    </div>

    <!-- FOURCHETTE 3 PRIX -->
    <div class="result-layout">
      <article class="card result-summary">
        <div class="kpi-grid">
          <div class="kpi-box kpi-low">
            <p class="kpi-label"><i class="fas fa-arrow-down"></i> Estimation basse</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_low'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
          <div class="kpi-box kpi-mid">
            <p class="kpi-label"><i class="fas fa-bullseye"></i> Estimation moyenne</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
          <div class="kpi-box kpi-high">
            <p class="kpi-label"><i class="fas fa-arrow-up"></i> Estimation haute</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_high'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
        </div>

        <!-- AVERTISSEMENT STATISTIQUE -->
        <div style="margin-top: 1.5rem; padding: 1rem 1.5rem; background: rgba(var(--warning-rgb), 0.08); border-radius: 10px; border-left: 4px solid var(--warning);">
          <p style="margin: 0; font-size: 0.9rem; color: var(--text); line-height: 1.6;">
            <i class="fas fa-info-circle" style="color: var(--warning);"></i>
            <strong>Estimation indicative :</strong> Ces chiffres sont basés sur des <strong>données statistiques</strong> du marché immobilier.
            Ils donnent une indication, mais ne remplacent pas un Avis de Valeur professionnel.
          </p>
        </div>
      </article>
    </div>

  </div>
</section>

<!-- ============================================ -->
<!-- CTA: ESTIMATION PLUS PRÉCISE -->
<!-- ============================================ -->
<section class="section section-alt">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">

      <!-- COLONNE GAUCHE: POURQUOI ALLER PLUS LOIN -->
      <div>
        <p class="eyebrow" style="margin-bottom: 1rem;">
          <i class="fas fa-user-tie"></i> Aller plus loin
        </p>
        <h2 style="margin-bottom: 1rem;">Complétez avec un avis de valeur</h2>
        <p style="color: var(--muted); line-height: 1.7; margin-bottom: 1.5rem;">
          L'estimation que vous venez de recevoir est basée sur des <strong>statistiques</strong> — comme tous les outils en ligne.
          C'est une bonne première indication, mais pour fixer un <strong>prix de mise en vente réaliste</strong>, l'idéal est de la compléter par un <strong>avis de valeur</strong> réalisé par un conseiller immobilier.
        </p>

        <div style="margin-bottom: 1.5rem;">
          <h3 style="font-size: 1rem; margin-bottom: 0.8rem;">Ce qu'apporte un avis de valeur :</h3>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
              <i class="fas fa-certificate" style="color: var(--primary); margin-top: 0.2rem;"></i>
              <span>Réalisé par un <strong>conseiller immobilier</strong> connaissant votre quartier</span>
            </li>
            <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
              <i class="fas fa-eye" style="color: var(--primary); margin-top: 0.2rem;"></i>
              <span><strong>Visite physique</strong> de votre bien (état, travaux, luminosité, vue...)</span>
            </li>
            <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
              <i class="fas fa-file-alt" style="color: var(--primary); margin-top: 0.2rem;"></i>
              <span>Prend en compte l'état, les travaux, la situation, l'environnement et la <strong>demande sur le secteur</strong></span>
            </li>
            <li style="padding: 0.5rem 0; display: flex; gap: 0.5rem;">
              <i class="fas fa-bullseye" style="color: var(--primary); margin-top: 0.2rem;"></i>
              <span><strong>Base de travail</strong> pour fixer un prix de mise en vente réaliste</span>
            </li>
          </ul>
        </div>

        <div style="padding: 1rem 1.5rem; background: rgba(var(--primary-rgb), 0.04); border-radius: 10px; border-left: 4px solid var(--primary);">
          <p style="margin: 0; font-size: 0.9rem; color: var(--text); line-height: 1.6;">
            <i class="fas fa-lightbulb" style="color: var(--primary);"></i>
            <strong>Le saviez-vous ?</strong> Un avis de valeur est rédigé par un professionnel de l'immobilier après visite du bien.
            Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent.
          </p>
        </div>
      </div>

      <!-- COLONNE DROITE: FORMULAIRE CONTACT -->
      <article class="card" id="lead-form" style="border-top: 4px solid var(--primary);">
        <div class="form-header">
          <h3 style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-handshake" style="color: var(--primary);"></i>
            Demander un avis de valeur
          </h3>
          <p class="muted">Un conseiller immobilier vous recontacte pour organiser une visite et vous remettre un avis de valeur complet.</p>
        </div>

        <form action="/lead" method="post" class="form-grid form-lead">
          <!-- CHAMPS CACHÉS -->
          <input type="hidden" name="ville" value="<?= e((string) $estimate['city']) ?>">
          <input type="hidden" name="estimation" value="<?= e((string) $estimate['estimated_mid']) ?>">

          <label for="nom">
            <span><i class="fas fa-user"></i> Nom complet *</span>
            <input type="text" id="nom" name="nom" placeholder="Jean Dupont" required>
          </label>

          <label for="email">
            <span><i class="fas fa-envelope"></i> Email *</span>
            <input type="email" id="email" name="email" placeholder="jean@example.com" required>
          </label>

          <label for="telephone">
            <span><i class="fas fa-phone"></i> Téléphone *</span>
            <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" required>
          </label>

          <div class="form-row">
            <label for="urgence">
              <span>Délai souhaité *</span>
              <select id="urgence" name="urgence" required>
                <option value="">-- Sélectionner --</option>
                <option value="rapide">Rapide (< 3 mois)</option>
                <option value="moyen">Moyen (3-6 mois)</option>
                <option value="long">Pas pressé (6+ mois)</option>
              </select>
            </label>

            <label for="motivation">
              <span>Raison *</span>
              <select id="motivation" name="motivation" required>
                <option value="">-- Sélectionner --</option>
                <option value="vente">Vente</option>
                <option value="succession">Succession</option>
                <option value="divorce">Séparation</option>
                <option value="investissement">Investissement</option>
                <option value="autre">Autre</option>
              </select>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
            <i class="fas fa-certificate"></i> Demander mon avis de valeur
          </button>

          <p style="text-align: center; margin: 0.5rem 0 0; font-size: 0.8rem; color: var(--muted);">
            <i class="fas fa-lock"></i> Vos données sont confidentielles. <a href="/mentions-legales">En savoir plus</a>
          </p>
        </form>
      </article>

    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- REFAIRE UNE ESTIMATION -->
<!-- ============================================ -->
<section class="section">
  <div class="container" style="text-align: center;">
    <p style="margin-bottom: 1rem; color: var(--muted);">Les résultats ne correspondent pas ? Modifiez vos critères.</p>
    <a href="/#form-estimation" class="btn btn-ghost">
      <i class="fas fa-redo"></i> Refaire une estimation
    </a>
  </div>
</section>
