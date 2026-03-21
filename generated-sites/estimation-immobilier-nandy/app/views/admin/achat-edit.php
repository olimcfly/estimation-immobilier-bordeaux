<style>
  .admin-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .admin-page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .admin-page-header h1 i { color: var(--admin-primary); }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--admin-primary);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
  }

  .back-link:hover { text-decoration: underline; }

  .form-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    max-width: 900px;
  }

  .form-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--admin-text);
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--admin-primary);
    margin-bottom: 1rem;
    margin-top: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-section-title:first-child { margin-top: 0; }
  .form-section-title i { color: var(--admin-primary); font-size: 0.9rem; }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
  }

  .form-group.full-width { grid-column: 1 / -1; }
  .form-group.span-2 { grid-column: span 2; }

  .form-group label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-text);
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.88rem;
    font-family: inherit;
    color: var(--admin-text);
    background: #fff;
    transition: border-color 0.15s;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px var(--admin-primary-light);
  }

  .form-group textarea { resize: vertical; min-height: 80px; }

  .form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
  }

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.25rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 500;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-primary:hover { opacity: 0.9; }

  .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.25rem;
    background: #fff;
    color: var(--admin-text);
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-secondary:hover { background: #f8fafc; }

  .admin-alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.88rem;
  }

  @media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr 1fr; }
    .form-group.span-2 { grid-column: 1 / -1; }
  }

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
  }
</style>

<?php
  $a = $achat ?? [];
  $isEdit = !empty($a['id']);
  $errs = $errors ?? [];
  $parts = $partenaires ?? [];
  $sLabels = $statutLabels ?? [];
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-shopping-cart"></i> <?= $isEdit ? 'Modifier Achat' : 'Nouvel Achat' ?></h1>
</div>

<a href="/admin/achats" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux achats</a>

<?php if (!empty($errs)): ?>
  <div class="admin-alert-error">
    <?php foreach ($errs as $err): ?>
      <div><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="/admin/achats/save">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
    <?php endif; ?>

    <!-- ACHETEUR -->
    <div class="form-section-title"><i class="fas fa-user"></i> Acheteur</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="nom_acheteur">Nom *</label>
        <input type="text" id="nom_acheteur" name="nom_acheteur" value="<?= htmlspecialchars((string)($a['nom_acheteur'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form-group">
        <label for="email_acheteur">Email</label>
        <input type="email" id="email_acheteur" name="email_acheteur" value="<?= htmlspecialchars((string)($a['email_acheteur'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="telephone_acheteur">Telephone</label>
        <input type="text" id="telephone_acheteur" name="telephone_acheteur" value="<?= htmlspecialchars((string)($a['telephone_acheteur'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>
    </div>

    <!-- BIEN RECHERCHE -->
    <div class="form-section-title"><i class="fas fa-home"></i> Bien</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="type_bien">Type de bien</label>
        <select id="type_bien" name="type_bien">
          <option value="">-- Choisir --</option>
          <?php
            $typesBien = ['Appartement', 'Maison', 'Loft', 'Duplex', 'Studio', 'Terrain', 'Local commercial', 'Immeuble', 'Parking'];
            foreach ($typesBien as $t):
          ?>
            <option value="<?= $t ?>" <?= ($a['type_bien'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="surface_m2">Surface (m&sup2;)</label>
        <input type="number" id="surface_m2" name="surface_m2" value="<?= htmlspecialchars((string)($a['surface_m2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label for="pieces">Pieces</label>
        <input type="number" id="pieces" name="pieces" value="<?= htmlspecialchars((string)($a['pieces'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" min="1">
      </div>

      <div class="form-group span-2">
        <label for="adresse_bien">Adresse du bien</label>
        <input type="text" id="adresse_bien" name="adresse_bien" value="<?= htmlspecialchars((string)($a['adresse_bien'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: 12 rue Sainte-Catherine">
      </div>

      <div class="form-group">
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" value="<?= htmlspecialchars((string)($a['ville'] ?? 'Nandy'), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="quartier">Quartier</label>
        <input type="text" id="quartier" name="quartier" value="<?= htmlspecialchars((string)($a['quartier'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Chartrons, Saint-Pierre...">
      </div>
    </div>

    <!-- FINANCIER -->
    <div class="form-section-title"><i class="fas fa-euro-sign"></i> Financier</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="prix_achat">Prix d'achat (&euro;)</label>
        <input type="number" id="prix_achat" name="prix_achat" value="<?= htmlspecialchars((string)($a['prix_achat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label for="prix_estime">Prix estime (&euro;)</label>
        <input type="number" id="prix_estime" name="prix_estime" value="<?= htmlspecialchars((string)($a['prix_estime'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label for="type_financement">Financement</label>
        <select id="type_financement" name="type_financement">
          <option value="credit" <?= ($a['type_financement'] ?? 'credit') === 'credit' ? 'selected' : '' ?>>Credit</option>
          <option value="comptant" <?= ($a['type_financement'] ?? '') === 'comptant' ? 'selected' : '' ?>>Comptant</option>
          <option value="mixte" <?= ($a['type_financement'] ?? '') === 'mixte' ? 'selected' : '' ?>>Mixte</option>
        </select>
      </div>

      <div class="form-group">
        <label for="montant_pret">Montant du pret (&euro;)</label>
        <input type="number" id="montant_pret" name="montant_pret" value="<?= htmlspecialchars((string)($a['montant_pret'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>

      <div class="form-group">
        <label for="apport_personnel">Apport personnel (&euro;)</label>
        <input type="number" id="apport_personnel" name="apport_personnel" value="<?= htmlspecialchars((string)($a['apport_personnel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>
    </div>

    <!-- SUIVI -->
    <div class="form-section-title"><i class="fas fa-tasks"></i> Suivi</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
          <?php foreach ($sLabels as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($a['statut'] ?? 'prospect') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="score">Score</label>
        <select id="score" name="score">
          <option value="froid" <?= ($a['score'] ?? 'froid') === 'froid' ? 'selected' : '' ?>>Froid</option>
          <option value="tiede" <?= ($a['score'] ?? '') === 'tiede' ? 'selected' : '' ?>>Tiede</option>
          <option value="chaud" <?= ($a['score'] ?? '') === 'chaud' ? 'selected' : '' ?>>Chaud</option>
        </select>
      </div>

      <div class="form-group">
        <label for="partenaire_id">Partenaire</label>
        <select id="partenaire_id" name="partenaire_id">
          <option value="">-- Aucun --</option>
          <?php foreach ($parts as $p): ?>
            <option value="<?= (int)$p['id'] ?>" <?= ((int)($a['partenaire_id'] ?? 0)) === (int)$p['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string)$p['nom'], ENT_QUOTES, 'UTF-8') ?><?= !empty($p['entreprise']) ? ' (' . htmlspecialchars((string)$p['entreprise'], ENT_QUOTES, 'UTF-8') . ')' : '' ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="commission_taux">Commission (%)</label>
        <input type="number" id="commission_taux" name="commission_taux" value="<?= htmlspecialchars((string)($a['commission_taux'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.1" min="0" max="20">
      </div>

      <div class="form-group">
        <label for="commission_montant">Commission montant (&euro;)</label>
        <input type="number" id="commission_montant" name="commission_montant" value="<?= htmlspecialchars((string)($a['commission_montant'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" step="0.01" min="0">
      </div>
    </div>

    <!-- DATES -->
    <div class="form-section-title"><i class="fas fa-calendar-alt"></i> Dates cles</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="date_premiere_visite">Premiere visite</label>
        <input type="date" id="date_premiere_visite" name="date_premiere_visite" value="<?= htmlspecialchars((string)($a['date_premiere_visite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="date_offre">Date offre</label>
        <input type="date" id="date_offre" name="date_offre" value="<?= htmlspecialchars((string)($a['date_offre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="date_compromis">Date compromis</label>
        <input type="date" id="date_compromis" name="date_compromis" value="<?= htmlspecialchars((string)($a['date_compromis'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="date_acte">Date acte</label>
        <input type="date" id="date_acte" name="date_acte" value="<?= htmlspecialchars((string)($a['date_acte'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>
    </div>

    <!-- NOTES -->
    <div class="form-section-title"><i class="fas fa-sticky-note"></i> Notes</div>
    <div class="form-grid">
      <div class="form-group full-width">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4"><?= htmlspecialchars((string)($a['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
    </div>

    <input type="hidden" name="lead_id" value="<?= htmlspecialchars((string)($a['lead_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <div class="form-actions">
      <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
      <a href="/admin/achats" class="btn-secondary">Annuler</a>
    </div>
  </form>
</div>
