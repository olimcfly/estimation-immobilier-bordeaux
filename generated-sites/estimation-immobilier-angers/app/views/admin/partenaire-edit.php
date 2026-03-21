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
    max-width: 700px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
  }

  .form-group.full-width { grid-column: 1 / -1; }

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

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
  }
</style>

<?php
  $p = $partenaire ?? [];
  $isEdit = !empty($p['id']);
  $errs = $errors ?? [];
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-handshake"></i> <?= $isEdit ? 'Modifier Partenaire' : 'Nouveau Partenaire' ?></h1>
</div>

<a href="/admin/partenaires" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux partenaires</a>

<?php if (!empty($errs)): ?>
  <div class="admin-alert-error">
    <?php foreach ($errs as $err): ?>
      <div><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="/admin/partenaires/save">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
    <?php endif; ?>

    <div class="form-grid">
      <div class="form-group">
        <label for="nom">Nom *</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars((string)($p['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form-group">
        <label for="entreprise">Entreprise</label>
        <input type="text" id="entreprise" name="entreprise" value="<?= htmlspecialchars((string)($p['entreprise'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars((string)($p['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form-group">
        <label for="telephone">T&eacute;l&eacute;phone</label>
        <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars((string)($p['telephone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-group">
        <label for="specialite">Sp&eacute;cialit&eacute;</label>
        <input type="text" id="specialite" name="specialite" value="<?= htmlspecialchars((string)($p['specialite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Appartements luxe, Maisons, Viager...">
      </div>

      <div class="form-group">
        <label for="zone_geographique">Zone G&eacute;ographique</label>
        <input type="text" id="zone_geographique" name="zone_geographique" value="<?= htmlspecialchars((string)($p['zone_geographique'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Angers Centre, Rive Droite...">
      </div>

      <div class="form-group">
        <label for="commission_defaut">Commission par d&eacute;faut (%)</label>
        <input type="number" id="commission_defaut" name="commission_defaut" value="<?= htmlspecialchars((string)($p['commission_defaut'] ?? '3.00'), ENT_QUOTES, 'UTF-8') ?>" step="0.1" min="0" max="100">
      </div>

      <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
          <option value="actif" <?= ($p['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
          <option value="inactif" <?= ($p['statut'] ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
          <option value="prospect" <?= ($p['statut'] ?? '') === 'prospect' ? 'selected' : '' ?>>Prospect</option>
        </select>
      </div>

      <div class="form-group full-width">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars((string)($p['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
      <a href="/admin/partenaires" class="btn-secondary">Annuler</a>
    </div>
  </form>
</div>
