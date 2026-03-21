<style>
  .admin-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
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

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-primary:hover { opacity: 0.9; }

  .btn-success {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.7rem 1.5rem;
    background: #22c55e;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-success:hover { opacity: 0.9; }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .stat-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .stat-icon.total { background: rgba(139,21,56,0.1); color: var(--admin-primary); }
  .stat-icon.chaud { background: rgba(239,68,68,0.1); color: #ef4444; }
  .stat-icon.signe { background: rgba(34,197,94,0.1); color: #22c55e; }
  .stat-icon.volume { background: rgba(245,158,11,0.1); color: #f59e0b; }

  .stat-info { min-width: 0; }
  .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); line-height: 1; }
  .stat-label { font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px; }

  .filters-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    align-items: center;
  }

  .filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    color: var(--admin-muted);
    background: var(--admin-surface);
    transition: all 0.15s;
  }

  .filter-btn:hover { border-color: var(--admin-primary); color: var(--admin-primary); }
  .filter-btn.active { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }

  .table-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .table-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .table-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--admin-text);
  }

  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .admin-table thead { background: #f8fafc; }

  .admin-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    border-bottom: 1px solid var(--admin-border);
  }

  .admin-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--admin-text);
  }

  .admin-table tbody tr:hover { background: #f8fafc; }

  .badge-score {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .badge-chaud { background: rgba(239,68,68,0.1); color: #dc2626; }
  .badge-tiede { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-froid { background: rgba(59,130,246,0.1); color: #2563eb; }

  .badge-statut {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    background: rgba(100,116,139,0.1);
    color: #475569;
  }

  .badge-statut.acte_signe { background: rgba(34,197,94,0.1); color: #16a34a; }
  .badge-statut.compromis, .badge-statut.financement { background: rgba(59,130,246,0.1); color: #2563eb; }
  .badge-statut.offre, .badge-statut.negociation { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-statut.annule { background: rgba(239,68,68,0.1); color: #dc2626; }

  .btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    border-radius: 5px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid var(--admin-border);
    cursor: pointer;
    transition: all 0.15s;
  }

  .btn-edit { background: #fff; color: var(--admin-text); }
  .btn-edit:hover { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }
  .btn-delete { background: #fff; color: #ef4444; border-color: #fecaca; }
  .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

  .actions-cell { display: flex; gap: 0.4rem; }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  .create-table-banner {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: var(--admin-radius);
    padding: 2rem;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .create-table-banner h2 {
    color: #92400e;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
  }

  .create-table-banner p {
    color: #78350f;
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
  }

  .flash-message {
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .flash-success { background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
  .flash-error { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }

  @media (max-width: 640px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
  }
</style>

<?php
  $allAchats = $achats ?? [];
  $s = $stats ?? [];
  $tblExists = $tableExists ?? false;
  $flash = $_SESSION['achat_flash'] ?? null;
  unset($_SESSION['achat_flash']);
  $sLabels = $statutLabels ?? [];
  $fScore = $filterScore ?? null;
  $fStatut = $filterStatut ?? null;
  $sCounts = $statutCounts ?? [];
?>

<!-- FLASH MESSAGE -->
<?php if ($flash): ?>
  <div class="flash-message flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<!-- CREATE TABLE BANNER (if table doesn't exist) -->
<?php if (!$tblExists): ?>
  <div class="create-table-banner">
    <h2><i class="fas fa-database"></i> Table "achats" non detectee</h2>
    <p>La table <strong>achats</strong> n'existe pas encore dans votre base de donnees.<br>Cliquez sur le bouton ci-dessous pour la creer automatiquement et rendre cette page fonctionnelle.</p>
    <form method="POST" action="/admin/achats/create-table" style="display:inline;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-success" onclick="return confirm('Creer la table achats dans la base de donnees ?');">
        <i class="fas fa-magic"></i> Creer la table maintenant
      </button>
    </form>
  </div>
<?php else: ?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-shopping-cart"></i> Achats</h1>
  <a href="/admin/achats/edit" class="btn-primary"><i class="fas fa-plus"></i> Nouvel Achat</a>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon total"><i class="fas fa-shopping-cart"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['total'] ?? 0) ?></div>
      <div class="stat-label">Total Achats</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon chaud"><i class="fas fa-fire"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['chauds'] ?? 0) ?></div>
      <div class="stat-label">Acheteurs Chauds</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon signe"><i class="fas fa-check-double"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['signes'] ?? 0) ?></div>
      <div class="stat-label">Actes Signes</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon volume"><i class="fas fa-euro-sign"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= number_format((float)($s['volume_signe'] ?? 0), 0, ',', ' ') ?> &euro;</div>
      <div class="stat-label">Volume Signe</div>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="filters-bar">
  <a href="/admin/achats" class="filter-btn <?= ($fScore === null && $fStatut === null) ? 'active' : '' ?>">Tous</a>
  <a href="/admin/achats?score=chaud" class="filter-btn <?= $fScore === 'chaud' ? 'active' : '' ?>"><i class="fas fa-fire"></i> Chaud</a>
  <a href="/admin/achats?score=tiede" class="filter-btn <?= $fScore === 'tiede' ? 'active' : '' ?>"><i class="fas fa-thermometer-half"></i> Tiede</a>
  <a href="/admin/achats?score=froid" class="filter-btn <?= $fScore === 'froid' ? 'active' : '' ?>"><i class="fas fa-snowflake"></i> Froid</a>
  <span style="color:var(--admin-border);">|</span>
  <?php foreach ($sLabels as $key => $label): ?>
    <a href="/admin/achats?statut=<?= $key ?>" class="filter-btn <?= $fStatut === $key ? 'active' : '' ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?><?php if (isset($sCounts[$key])): ?> <span style="opacity:0.7;">(<?= (int)$sCounts[$key] ?>)</span><?php endif; ?></a>
  <?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title"><?= count($allAchats) ?> achat<?= count($allAchats) > 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($allAchats)): ?>
    <div class="empty-state">
      <i class="fas fa-shopping-cart" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.3;"></i>
      <p>Aucun achat pour le moment.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">Ajoutez vos acheteurs pour suivre leurs projets d'acquisition.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Acheteur</th>
            <th>Bien</th>
            <th>Ville</th>
            <th>Prix</th>
            <th>Financement</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Partenaire</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allAchats as $a): ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars((string)$a['nom_acheteur'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($a['email_acheteur'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$a['email_acheteur'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if (!empty($a['telephone_acheteur'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$a['telephone_acheteur'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($a['type_bien'])): ?>
                  <?= htmlspecialchars((string)$a['type_bien'], ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>
                  <span style="color:var(--admin-muted);">-</span>
                <?php endif; ?>
                <?php if (!empty($a['surface_m2'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= number_format((float)$a['surface_m2'], 0) ?> m&sup2; <?php if (!empty($a['pieces'])): ?>| <?= (int)$a['pieces'] ?> p.<?php endif; ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars((string)($a['ville'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($a['quartier'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$a['quartier'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($a['prix_achat'])): ?>
                  <strong><?= number_format((float)$a['prix_achat'], 0, ',', ' ') ?> &euro;</strong>
                <?php else: ?>
                  <span style="color:var(--admin-muted);">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $finLabels = ['comptant' => 'Comptant', 'credit' => 'Credit', 'mixte' => 'Mixte'];
                  echo $finLabels[$a['type_financement'] ?? 'credit'] ?? 'Credit';
                ?>
              </td>
              <td><span class="badge-score badge-<?= $a['score'] ?? 'froid' ?>"><?= ucfirst((string)($a['score'] ?? 'froid')) ?></span></td>
              <td><span class="badge-statut <?= $a['statut'] ?? 'prospect' ?>"><?= htmlspecialchars($sLabels[$a['statut'] ?? 'prospect'] ?? $a['statut'] ?? 'Prospect', ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><?= htmlspecialchars((string)($a['partenaire_nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td style="white-space:nowrap;font-size:0.8rem;color:var(--admin-muted);"><?= date('d/m/Y', strtotime((string)$a['created_at'])) ?></td>
              <td>
                <div class="actions-cell">
                  <a href="/admin/achats/edit?id=<?= (int)$a['id'] ?>" class="btn-edit"><i class="fas fa-pen"></i></a>
                  <form method="POST" action="/admin/achats/delete" style="display:inline;" onsubmit="return confirm('Supprimer cet achat ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php endif; ?>
