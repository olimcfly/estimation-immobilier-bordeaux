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

  .admin-page-header h1 i {
    color: var(--admin-primary);
  }

  /* STAT CARDS */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

  .stat-icon.total { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .stat-icon.hot { background: rgba(239,68,68,0.1); color: #ef4444; }
  .stat-icon.warm { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .stat-icon.cold { background: rgba(100,116,139,0.1); color: #64748b; }

  .stat-info { min-width: 0; }

  .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    line-height: 1;
  }

  .stat-label {
    font-size: 0.8rem;
    color: var(--admin-muted);
    margin-top: 4px;
  }

  /* ALERT */
  .admin-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .admin-alert i { color: #ef4444; }

  /* TABLE CARD */
  .table-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    flex-wrap: wrap;
    gap: 0.75rem;
  }

  .table-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--admin-text);
  }

  .table-filters {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .filter-btn {
    padding: 0.4rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    background: #fff;
    color: var(--admin-muted);
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
  }

  .filter-btn:hover, .filter-btn.active {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  /* TABLE */
  .admin-table-wrap {
    overflow-x: auto;
  }

  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .admin-table thead {
    background: #f8fafc;
  }

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
    white-space: nowrap;
  }

  .admin-table tbody tr:hover {
    background: #f8fafc;
  }

  .admin-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* BADGES */
  .badge-score {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-chaud { background: rgba(239,68,68,0.1); color: #dc2626; }
  .badge-tiede { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-froid { background: rgba(100,116,139,0.1); color: #475569; }

  .badge-statut {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-nouveau { background: rgba(59,130,246,0.1); color: #2563eb; }
  .badge-contacte { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-signe { background: rgba(34,197,94,0.1); color: #16a34a; }

  .lead-name {
    font-weight: 600;
    color: var(--admin-text);
  }

  .lead-email {
    color: var(--admin-muted);
    font-size: 0.8rem;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  .empty-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.3;
  }

  .empty-state p {
    font-size: 0.95rem;
  }

  @media (max-width: 640px) {
    .stats-grid {
      grid-template-columns: 1fr 1fr;
    }

    .admin-page-header {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

<?php
  $allLeads = $leads ?? [];
  $totalLeads = count($allLeads);
  $hotLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'chaud'));
  $warmLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'tiede'));
  $coldLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'froid'));
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-users"></i> Gestion des Leads</h1>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon total"><i class="fas fa-users"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $totalLeads ?></div>
      <div class="stat-label">Total leads</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon hot"><i class="fas fa-fire"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $hotLeads ?></div>
      <div class="stat-label">Leads chauds</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon warm"><i class="fas fa-temperature-half"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $warmLeads ?></div>
      <div class="stat-label">Leads ti&egrave;des</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon cold"><i class="fas fa-snowflake"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $coldLeads ?></div>
      <div class="stat-label">Leads froids</div>
    </div>
  </div>
</div>

<!-- DB ERROR -->
<?php if (!empty($dbError ?? '')): ?>
  <div class="admin-alert">
    <i class="fas fa-exclamation-triangle"></i>
    <?= e($dbError) ?>
  </div>
<?php endif; ?>

<!-- LEADS TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title"><?= $totalLeads ?> lead<?= $totalLeads > 1 ? 's' : '' ?> enregistr&eacute;<?= $totalLeads > 1 ? 's' : '' ?></span>
    <div class="table-filters">
      <a href="/admin/leads" class="filter-btn active">Tous</a>
      <a href="/admin/leads?score=chaud" class="filter-btn">Chauds</a>
      <a href="/admin/leads?score=tiede" class="filter-btn">Ti&egrave;des</a>
      <a href="/admin/leads?score=froid" class="filter-btn">Froids</a>
    </div>
  </div>

  <?php if (empty($allLeads)): ?>
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <p>Aucun lead pour le moment.</p>
      <p style="font-size: 0.85rem; margin-top: 0.5rem;">Les leads appara&icirc;tront ici quand des visiteurs rempliront le formulaire d'estimation.</p>
    </div>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Contact</th>
            <th>T&eacute;l&eacute;phone</th>
            <th>Ville</th>
            <th>Estimation</th>
            <th>Urgence</th>
            <th>Motivation</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allLeads as $lead): ?>
            <?php
              $scoreClass = match($lead['score'] ?? '') {
                'chaud' => 'badge-chaud',
                'tiede' => 'badge-tiede',
                default => 'badge-froid',
              };
              $scoreIcon = match($lead['score'] ?? '') {
                'chaud' => 'fa-fire',
                'tiede' => 'fa-temperature-half',
                default => 'fa-snowflake',
              };
              $statutClass = match($lead['statut'] ?? '') {
                'nouveau' => 'badge-nouveau',
                'contact&eacute;' => 'badge-contacte',
                'contacté' => 'badge-contacte',
                'sign&eacute;' => 'badge-signe',
                'signé' => 'badge-signe',
                default => 'badge-nouveau',
              };
            ?>
            <tr>
              <td><?= (int) $lead['id'] ?></td>
              <td>
                <div class="lead-name"><?= e((string) $lead['nom']) ?></div>
                <div class="lead-email"><?= e((string) $lead['email']) ?></div>
              </td>
              <td><?= e((string) $lead['telephone']) ?></td>
              <td><?= e((string) $lead['ville']) ?></td>
              <td><strong><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> &euro;</strong></td>
              <td><?= e((string) $lead['urgence']) ?></td>
              <td><?= e((string) $lead['motivation']) ?></td>
              <td><span class="badge-score <?= $scoreClass ?>"><i class="fas <?= $scoreIcon ?>"></i> <?= e((string) $lead['score']) ?></span></td>
              <td><span class="badge-statut <?= $statutClass ?>"><?= e((string) $lead['statut']) ?></span></td>
              <td><?= e((string) $lead['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
