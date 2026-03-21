<style>
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .kpi-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
  }

  .kpi-card.kpi-revenue::before { background: linear-gradient(90deg, #22c55e, #16a34a); }
  .kpi-card.kpi-projete::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
  .kpi-card.kpi-contacts::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }
  .kpi-card.kpi-conversion::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
  .kpi-card.kpi-portefeuille::before { background: linear-gradient(90deg, #ec4899, #db2777); }
  .kpi-card.kpi-commission::before { background: linear-gradient(90deg, #14b8a6, #0d9488); }

  .kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }

  .kpi-revenue .kpi-icon { background: rgba(34,197,94,0.1); color: #22c55e; }
  .kpi-projete .kpi-icon { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .kpi-contacts .kpi-icon { background: rgba(139,92,246,0.1); color: #8b5cf6; }
  .kpi-conversion .kpi-icon { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .kpi-portefeuille .kpi-icon { background: rgba(236,72,153,0.1); color: #ec4899; }
  .kpi-commission .kpi-icon { background: rgba(20,184,166,0.1); color: #14b8a6; }

  .kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--admin-text);
    line-height: 1;
    margin-bottom: 0.35rem;
  }

  .kpi-label {
    font-size: 0.82rem;
    color: var(--admin-muted);
    font-weight: 500;
  }

  /* Score cards row */
  .score-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .score-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    text-align: center;
  }

  .score-card .score-value {
    font-size: 2rem;
    font-weight: 700;
  }

  .score-card.hot .score-value { color: #ef4444; }
  .score-card.warm .score-value { color: #f59e0b; }
  .score-card.cold .score-value { color: #64748b; }

  .score-card .score-label {
    font-size: 0.85rem;
    color: var(--admin-muted);
    margin-top: 0.25rem;
  }

  /* Pipeline mini */
  .pipeline-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .pipeline-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .pipeline-section h3 i { color: var(--admin-primary); }

  .pipeline-bars {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .pipeline-bar-row {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .pipeline-bar-label {
    width: 160px;
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--admin-text);
    flex-shrink: 0;
    text-align: right;
  }

  .pipeline-bar-track {
    flex: 1;
    height: 28px;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
  }

  .pipeline-bar-fill {
    height: 100%;
    border-radius: 6px;
    display: flex;
    align-items: center;
    padding: 0 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fff;
    min-width: fit-content;
    transition: width 0.6s ease;
  }

  .pipeline-bar-count {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-text);
    width: 40px;
    text-align: right;
    flex-shrink: 0;
  }

  /* Recent leads */
  .recent-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .recent-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .recent-header h3 {
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .recent-header h3 i { color: var(--admin-primary); }

  .recent-link {
    font-size: 0.82rem;
    color: var(--admin-primary);
    text-decoration: none;
    font-weight: 500;
  }

  .recent-link:hover { text-decoration: underline; }

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
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .badge-nouveau { background: rgba(59,130,246,0.1); color: #2563eb; }
  .badge-contacte { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-rdv_pris { background: rgba(139,92,246,0.1); color: #7c3aed; }
  .badge-visite_realisee { background: rgba(236,72,153,0.1); color: #db2777; }
  .badge-mandat_simple { background: rgba(14,165,233,0.1); color: #0284c7; }
  .badge-mandat_exclusif { background: rgba(20,184,166,0.1); color: #0d9488; }
  .badge-compromis_vente { background: rgba(249,115,22,0.1); color: #c2410c; }
  .badge-signe { background: rgba(34,197,94,0.1); color: #16a34a; }
  .badge-co_signature_partenaire { background: rgba(168,85,247,0.1); color: #7c3aed; }
  .badge-assigne_autre { background: rgba(100,116,139,0.1); color: #475569; }

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

  .header-actions {
    display: flex;
    gap: 0.5rem;
  }

  .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid var(--admin-border);
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
    transition: all 0.15s;
  }

  .btn-action:hover {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  @media (max-width: 768px) {
    .dashboard-grid { grid-template-columns: 1fr 1fr; }
    .score-row { grid-template-columns: 1fr; }
    .pipeline-bar-label { width: 100px; font-size: 0.75rem; }
  }
</style>

<?php
  $s = $stats ?? [];
  $pipelineLabels = [
    'nouveau' => ['Nouveau', '#3b82f6'],
    'contacte' => ['Contact&eacute;', '#f59e0b'],
    'rdv_pris' => ['RDV Pris', '#8b5cf6'],
    'visite_realisee' => ['Visite R&eacute;alis&eacute;e', '#ec4899'],
    'mandat_simple' => ['Mandat Simple', '#0ea5e9'],
    'mandat_exclusif' => ['Mandat Exclusif', '#14b8a6'],
    'compromis_vente' => ['Compromis de Vente', '#f97316'],
    'signe' => ['Sign&eacute;', '#22c55e'],
    'co_signature_partenaire' => ['Co-signature Partenaire', '#a855f7'],
  ];
  $funnel = $s['funnel'] ?? [];
  $maxFunnel = max(1, max(array_values($funnel) ?: [1]));
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-chart-line"></i> Tableau de Bord</h1>
  <div class="header-actions">
    <a href="/admin/funnel" class="btn-action"><i class="fas fa-filter"></i> Entonnoir</a>
    <a href="/admin/portfolio" class="btn-action"><i class="fas fa-briefcase"></i> Portefeuille</a>
    <a href="/admin/partenaires" class="btn-action"><i class="fas fa-handshake"></i> Partenaires</a>
  </div>
</div>

<!-- KPI CARDS -->
<div class="dashboard-grid">
  <div class="kpi-card kpi-revenue">
    <div class="kpi-icon"><i class="fas fa-euro-sign"></i></div>
    <div class="kpi-value"><?= number_format($s['revenu_gagne'] ?? 0, 0, ',', ' ') ?> &euro;</div>
    <div class="kpi-label">Revenu Gagn&eacute;</div>
  </div>
  <div class="kpi-card kpi-projete">
    <div class="kpi-icon"><i class="fas fa-chart-bar"></i></div>
    <div class="kpi-value"><?= number_format($s['ca_projete'] ?? 0, 0, ',', ' ') ?> &euro;</div>
    <div class="kpi-label">CA Projet&eacute;</div>
  </div>
  <div class="kpi-card kpi-contacts">
    <div class="kpi-icon"><i class="fas fa-users"></i></div>
    <div class="kpi-value"><?= $s['total_contacts'] ?? 0 ?></div>
    <div class="kpi-label">Nombre de Contacts</div>
  </div>
  <div class="kpi-card kpi-conversion">
    <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
    <div class="kpi-value"><?= $s['taux_conversion'] ?? 0 ?>%</div>
    <div class="kpi-label">Taux de Conversion</div>
  </div>
  <div class="kpi-card kpi-portefeuille">
    <div class="kpi-icon"><i class="fas fa-home"></i></div>
    <div class="kpi-value"><?= number_format($s['valeur_portefeuille'] ?? 0, 0, ',', ' ') ?> &euro;</div>
    <div class="kpi-label">Valeur Portefeuille Immo</div>
  </div>
  <div class="kpi-card kpi-commission">
    <div class="kpi-icon"><i class="fas fa-coins"></i></div>
    <div class="kpi-value"><?= number_format($s['commission_potentielle'] ?? 0, 0, ',', ' ') ?> &euro;</div>
    <div class="kpi-label">Commission Potentielle</div>
  </div>
</div>

<!-- SCORE ROW -->
<div class="score-row">
  <div class="score-card hot">
    <div class="score-value"><i class="fas fa-fire"></i> <?= $s['leads_chaud'] ?? 0 ?></div>
    <div class="score-label">Leads Chauds</div>
  </div>
  <div class="score-card warm">
    <div class="score-value"><i class="fas fa-temperature-half"></i> <?= $s['leads_tiede'] ?? 0 ?></div>
    <div class="score-label">Leads Ti&egrave;des</div>
  </div>
  <div class="score-card cold">
    <div class="score-value"><i class="fas fa-snowflake"></i> <?= $s['leads_froid'] ?? 0 ?></div>
    <div class="score-label">Leads Froids</div>
  </div>
</div>

<!-- PIPELINE PROGRESS -->
<div class="pipeline-section">
  <h3><i class="fas fa-stream"></i> Pipeline Commercial</h3>
  <div class="pipeline-bars">
    <?php foreach ($pipelineLabels as $key => [$label, $color]): ?>
      <?php $count = $funnel[$key] ?? 0; $pct = $maxFunnel > 0 ? ($count / $maxFunnel) * 100 : 0; ?>
      <div class="pipeline-bar-row">
        <div class="pipeline-bar-label"><?= $label ?></div>
        <div class="pipeline-bar-track">
          <div class="pipeline-bar-fill" style="width: <?= max($pct, $count > 0 ? 8 : 0) ?>%; background: <?= $color ?>;">
            <?= $count > 0 ? $count : '' ?>
          </div>
        </div>
        <div class="pipeline-bar-count"><?= $count ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- RECENT LEADS -->
<div class="recent-section">
  <div class="recent-header">
    <h3><i class="fas fa-clock"></i> Leads R&eacute;cents</h3>
    <a href="/admin/leads" class="recent-link">Voir tous les leads &rarr;</a>
  </div>
  <?php if (empty($s['leads_recents'])): ?>
    <div style="text-align:center; padding:2rem; color:var(--admin-muted);">Aucun lead qualifi&eacute; pour le moment.</div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Contact</th>
            <th>Ville</th>
            <th>Estimation</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($s['leads_recents'] as $lead): ?>
            <?php
              $scoreClass = match($lead['score'] ?? '') { 'chaud' => 'badge-chaud', 'tiede' => 'badge-tiede', default => 'badge-froid' };
              $scoreIcon = match($lead['score'] ?? '') { 'chaud' => 'fa-fire', 'tiede' => 'fa-temperature-half', default => 'fa-snowflake' };
              $statutKey = $lead['statut'] ?? 'nouveau';
              $statutLabel = $pipelineLabels[$statutKey][0] ?? ucfirst(str_replace('_', ' ', $statutKey));
            ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars((string)($lead['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)($lead['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
              </td>
              <td><?= htmlspecialchars((string)$lead['ville'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><strong><?= number_format((float)$lead['estimation'], 0, ',', ' ') ?> &euro;</strong></td>
              <td><span class="badge-score <?= $scoreClass ?>"><i class="fas <?= $scoreIcon ?>"></i> <?= htmlspecialchars((string)$lead['score'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><span class="badge-statut badge-<?= $statutKey ?>"><?= $statutLabel ?></span></td>
              <td style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$lead['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
