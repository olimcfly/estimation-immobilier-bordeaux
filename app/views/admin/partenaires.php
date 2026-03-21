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

  .stat-icon.partner { background: rgba(139,21,56,0.1); color: var(--admin-primary); }
  .stat-icon.actif { background: rgba(34,197,94,0.1); color: #22c55e; }
  .stat-icon.mandats { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .stat-icon.ca { background: rgba(245,158,11,0.1); color: #f59e0b; }

  .stat-info { min-width: 0; }
  .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); line-height: 1; }
  .stat-label { font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px; }

  .table-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .table-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
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

  .badge-statut-partenaire {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .badge-actif { background: rgba(34,197,94,0.1); color: #16a34a; }
  .badge-inactif { background: rgba(100,116,139,0.1); color: #475569; }
  .badge-prospect { background: rgba(59,130,246,0.1); color: #2563eb; }

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

  .btn-edit {
    background: #fff;
    color: var(--admin-text);
  }

  .btn-edit:hover {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  .btn-delete {
    background: #fff;
    color: #ef4444;
    border-color: #fecaca;
  }

  .btn-delete:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
  }

  .actions-cell {
    display: flex;
    gap: 0.4rem;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  @media (max-width: 640px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
  }
</style>

<?php
  $allPartenaires = $partenaires ?? [];
  $s = $stats ?? [];
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-handshake"></i> Partenaires</h1>
  <a href="/admin/partenaires/edit" class="btn-primary"><i class="fas fa-plus"></i> Nouveau Partenaire</a>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon partner"><i class="fas fa-handshake"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['total'] ?? 0) ?></div>
      <div class="stat-label">Total Partenaires</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon actif"><i class="fas fa-check-circle"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['actifs'] ?? 0) ?></div>
      <div class="stat-label">Actifs</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mandats"><i class="fas fa-file-contract"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['total_mandats'] ?? 0) ?></div>
      <div class="stat-label">Total Mandats</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon ca"><i class="fas fa-euro-sign"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= number_format((float)($s['total_ca'] ?? 0), 0, ',', ' ') ?> &euro;</div>
      <div class="stat-label">CA G&eacute;n&eacute;r&eacute;</div>
    </div>
  </div>
</div>

<!-- TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title"><?= count($allPartenaires) ?> partenaire<?= count($allPartenaires) > 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($allPartenaires)): ?>
    <div class="empty-state">
      <i class="fas fa-handshake" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.3;"></i>
      <p>Aucun partenaire pour le moment.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">Ajoutez vos partenaires pour g&eacute;rer les co-signatures et commissions.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Entreprise</th>
            <th>Contact</th>
            <th>Sp&eacute;cialit&eacute;</th>
            <th>Zone</th>
            <th>Commission</th>
            <th>Mandats</th>
            <th>CA G&eacute;n&eacute;r&eacute;</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allPartenaires as $p): ?>
            <?php
              $statutClass = match($p['statut'] ?? '') { 'actif' => 'badge-actif', 'inactif' => 'badge-inactif', default => 'badge-prospect' };
            ?>
            <tr>
              <td style="font-weight:600;"><?= htmlspecialchars((string)$p['nom'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)($p['entreprise'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td>
                <div><?= htmlspecialchars((string)$p['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($p['telephone'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$p['telephone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars((string)($p['specialite'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td><?= htmlspecialchars((string)($p['zone_geographique'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td><?= number_format((float)($p['commission_defaut'] ?? 3), 1) ?>%</td>
              <td><?= (int)($p['nb_mandats'] ?? 0) ?></td>
              <td><strong><?= number_format((float)($p['ca_genere'] ?? 0), 0, ',', ' ') ?> &euro;</strong></td>
              <td><span class="badge-statut-partenaire <?= $statutClass ?>"><?= htmlspecialchars((string)$p['statut'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td>
                <div class="actions-cell">
                  <a href="/admin/partenaires/edit?id=<?= (int)$p['id'] ?>" class="btn-edit"><i class="fas fa-pen"></i></a>
                  <form method="POST" action="/admin/partenaires/delete" style="display:inline;" onsubmit="return confirm('Supprimer ce partenaire ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
