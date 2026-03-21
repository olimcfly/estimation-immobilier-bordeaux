<style>
  .edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .edit-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .edit-header h1 i { color: var(--admin-primary); }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid var(--admin-border);
    background: #fff;
    color: var(--admin-text);
    transition: all 0.15s;
  }

  .btn:hover { background: #f8fafc; }
  .btn-primary { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }
  .btn-primary:hover { opacity: 0.9; background: var(--admin-primary); }

  .form-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
    margin-bottom: 1.5rem;
  }

  .form-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-card-header i { color: var(--admin-primary); font-size: 0.9rem; }

  .form-card-body { padding: 1.25rem; }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
  }

  .form-group label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--admin-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 0.6rem 0.75rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.85rem;
    color: var(--admin-text);
    background: #fff;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .form-group textarea {
    min-height: 80px;
    resize: vertical;
  }

  .form-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--admin-border);
  }

  .admin-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
  }

  .info-row {
    display: flex;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .info-row:last-child { border-bottom: none; }

  .info-label {
    width: 140px;
    flex-shrink: 0;
    font-size: 0.8rem;
    color: var(--admin-muted);
    font-weight: 500;
  }

  .info-value {
    flex: 1;
    font-size: 0.9rem;
    color: var(--admin-text);
  }

  @media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .edit-header { flex-direction: column; align-items: flex-start; }
  }
</style>

<?php
  $lead = $lead ?? [];
  $partenaires = $partenaires ?? [];
  $errors = $errors ?? [];
  $leadId = (int) ($lead['id'] ?? 0);

  $statutLabels = [
    'nouveau' => 'Nouveau',
    'contacte' => 'Contact&eacute;',
    'rdv_pris' => 'RDV Pris',
    'visite_realisee' => 'Visite R&eacute;alis&eacute;e',
    'mandat_simple' => 'Mandat Simple',
    'mandat_exclusif' => 'Mandat Exclusif',
    'compromis_vente' => 'Compromis',
    'signe' => 'Sign&eacute;',
    'co_signature_partenaire' => 'Co-signature',
    'assigne_autre' => 'Assign&eacute;',
  ];
?>

<!-- HEADER -->
<div class="edit-header">
  <h1><i class="fas fa-edit"></i> Modifier Lead #<?= $leadId ?></h1>
  <div>
    <a href="/admin/leads/detail?id=<?= $leadId ?>" class="btn"><i class="fas fa-arrow-left"></i> Retour</a>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <?php foreach ($errors as $error): ?>
    <div class="admin-alert"><i class="fas fa-exclamation-triangle"></i> <?= e((string) $error) ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<form method="POST" action="/admin/leads/update">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="id" value="<?= $leadId ?>">

  <!-- Read-only contact info -->
  <div class="form-card">
    <div class="form-card-header"><i class="fas fa-address-card"></i> Informations Contact (lecture seule)</div>
    <div class="form-card-body">
      <div class="info-row">
        <div class="info-label">Nom</div>
        <div class="info-value"><?= !empty($lead['nom']) ? e((string) $lead['nom']) : 'Anonyme' ?></div>
      </div>
      <div class="info-row">
        <div class="info-label">Email</div>
        <div class="info-value"><?= !empty($lead['email']) ? e((string) $lead['email']) : '-' ?></div>
      </div>
      <div class="info-row">
        <div class="info-label">T&eacute;l&eacute;phone</div>
        <div class="info-value"><?= !empty($lead['telephone']) ? e((string) $lead['telephone']) : '-' ?></div>
      </div>
      <div class="info-row">
        <div class="info-label">Ville</div>
        <div class="info-value"><?= e((string) ($lead['ville'] ?? '')) ?></div>
      </div>
      <div class="info-row">
        <div class="info-label">Estimation</div>
        <div class="info-value"><strong><?= number_format((float) ($lead['estimation'] ?? 0), 0, ',', ' ') ?> &euro;</strong></div>
      </div>
    </div>
  </div>

  <!-- Editable pipeline fields -->
  <div class="form-card">
    <div class="form-card-header"><i class="fas fa-tasks"></i> Pipeline &amp; Gestion</div>
    <div class="form-card-body">
      <div class="form-grid">
        <div class="form-group">
          <label for="statut">Statut</label>
          <select name="statut" id="statut">
            <?php foreach ($statutLabels as $sKey => $sLabel): ?>
              <option value="<?= $sKey ?>" <?= ($lead['statut'] ?? 'nouveau') === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="score">Score</label>
          <select name="score" id="score">
            <option value="chaud" <?= ($lead['score'] ?? '') === 'chaud' ? 'selected' : '' ?>>Chaud</option>
            <option value="tiede" <?= ($lead['score'] ?? '') === 'tiede' ? 'selected' : '' ?>>Ti&egrave;de</option>
            <option value="froid" <?= ($lead['score'] ?? '') === 'froid' ? 'selected' : '' ?>>Froid</option>
          </select>
        </div>

        <div class="form-group">
          <label for="assigne_a">Assign&eacute; &agrave;</label>
          <input type="text" name="assigne_a" id="assigne_a" value="<?= e((string) ($lead['assigne_a'] ?? '')) ?>" placeholder="Nom de l'agent...">
        </div>

        <div class="form-group">
          <label for="partenaire_id">Partenaire</label>
          <select name="partenaire_id" id="partenaire_id">
            <option value="">-- Aucun --</option>
            <?php foreach ($partenaires as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= ((int) ($lead['partenaire_id'] ?? 0)) === (int) $p['id'] ? 'selected' : '' ?>>
                <?= e((string) $p['nom']) ?><?= !empty($p['entreprise']) ? ' (' . e((string) $p['entreprise']) . ')' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="commission_taux">Commission (%)</label>
          <input type="number" step="0.01" name="commission_taux" id="commission_taux" value="<?= e((string) ($lead['commission_taux'] ?? '')) ?>" placeholder="3.00">
        </div>

        <div class="form-group">
          <label for="commission_montant">Commission (&euro;)</label>
          <input type="number" step="0.01" name="commission_montant" id="commission_montant" value="<?= e((string) ($lead['commission_montant'] ?? '')) ?>" placeholder="0.00">
        </div>

        <div class="form-group">
          <label for="prix_vente">Prix de vente (&euro;)</label>
          <input type="number" step="0.01" name="prix_vente" id="prix_vente" value="<?= e((string) ($lead['prix_vente'] ?? '')) ?>" placeholder="0.00">
        </div>
      </div>
    </div>
  </div>

  <!-- Dates -->
  <div class="form-card">
    <div class="form-card-header"><i class="fas fa-calendar-alt"></i> Dates</div>
    <div class="form-card-body">
      <div class="form-grid">
        <div class="form-group">
          <label for="date_mandat">Date mandat</label>
          <input type="date" name="date_mandat" id="date_mandat" value="<?= e((string) ($lead['date_mandat'] ?? '')) ?>">
        </div>

        <div class="form-group">
          <label for="date_compromis">Date compromis</label>
          <input type="date" name="date_compromis" id="date_compromis" value="<?= e((string) ($lead['date_compromis'] ?? '')) ?>">
        </div>

        <div class="form-group">
          <label for="date_signature">Date signature</label>
          <input type="date" name="date_signature" id="date_signature" value="<?= e((string) ($lead['date_signature'] ?? '')) ?>">
        </div>
      </div>

      <div class="form-actions">
        <a href="/admin/leads/detail?id=<?= $leadId ?>" class="btn">Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
      </div>
    </div>
  </div>
</form>
