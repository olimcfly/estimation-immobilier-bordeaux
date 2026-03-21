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

  /* PIPELINE VISUAL TRACKER */
  .pipeline-tracker {
    display: flex;
    align-items: stretch;
    gap: 0;
    margin-bottom: 1.5rem;
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
    overflow-x: auto;
  }

  .pipeline-step {
    flex: 1;
    min-width: 90px;
    padding: 0.6rem 0.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s;
    border-right: 1px solid var(--admin-border);
    text-decoration: none;
    position: relative;
  }

  .pipeline-step:last-child { border-right: none; }

  .pipeline-step:hover {
    background: rgba(139, 21, 56, 0.05);
  }

  .pipeline-step.active {
    background: rgba(139, 21, 56, 0.08);
  }

  .pipeline-step-count {
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.15rem;
  }

  .pipeline-step-label {
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .pipeline-step-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
  }

  /* STAT CARDS */
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

  .stat-icon.total { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .stat-icon.hot { background: rgba(239,68,68,0.1); color: #ef4444; }
  .stat-icon.warm { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .stat-icon.cold { background: rgba(100,116,139,0.1); color: #64748b; }
  .stat-icon.tendance { background: rgba(168,85,247,0.1); color: #a855f7; }
  .stat-icon.qualifie { background: rgba(34,197,94,0.1); color: #16a34a; }

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
    gap: 0.35rem;
    flex-wrap: wrap;
  }

  .filter-btn {
    padding: 0.35rem 0.7rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    background: #fff;
    color: var(--admin-muted);
    font-size: 0.75rem;
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

  .filter-separator {
    width: 1px;
    background: var(--admin-border);
    margin: 0 0.15rem;
    align-self: stretch;
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

  .statut-select {
    padding: 0.3rem 0.5rem;
    border: 1px solid var(--admin-border);
    border-radius: 5px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
  }

  .statut-select:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .score-select {
    padding: 0.3rem 0.5rem;
    border: 1px solid var(--admin-border);
    border-radius: 5px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
    width: 85px;
  }

  .score-select:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .badge-type {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .badge-tendance { background: rgba(168,85,247,0.1); color: #7c3aed; }
  .badge-qualifie { background: rgba(34,197,94,0.1); color: #16a34a; }

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

  /* Toast */
  .toast-notification {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #1e293b;
    color: #fff;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    animation: slideUp 0.3s ease;
  }

  .toast-notification.success { border-left: 4px solid #22c55e; }
  .toast-notification.error { border-left: 4px solid #ef4444; }

  @keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  @media (max-width: 640px) {
    .stats-grid {
      grid-template-columns: 1fr 1fr;
    }

    .admin-page-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .pipeline-tracker {
      overflow-x: auto;
    }
  }
</style>

<?php
  $allLeads = $leads ?? [];
  $totalLeads = count($allLeads);
  $tendanceLeads = count(array_filter($allLeads, fn($l) => ($l['lead_type'] ?? '') === 'tendance'));
  $qualifieLeads = count(array_filter($allLeads, fn($l) => ($l['lead_type'] ?? '') === 'qualifie'));
  $hotLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'chaud'));
  $warmLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'tiede'));
  $coldLeads = count(array_filter($allLeads, fn($l) => ($l['score'] ?? '') === 'froid'));

  $sCounts = $statutCounts ?? [];

  $pipelineSteps = [
    'nouveau'        => ['Nouveau',        '#3b82f6', 'fa-plus-circle'],
    'contacte'       => ['Contact&eacute;',       '#f59e0b', 'fa-phone'],
    'rdv_pris'       => ['RDV Pris',       '#8b5cf6', 'fa-calendar-check'],
    'visite_realisee'=> ['Visite',         '#ec4899', 'fa-home'],
    'mandat_simple'  => ['M. Simple',      '#0ea5e9', 'fa-file-contract'],
    'mandat_exclusif'=> ['M. Exclusif',    '#14b8a6', 'fa-file-signature'],
    'compromis_vente'=> ['Compromis',      '#f97316', 'fa-handshake'],
    'signe'          => ['Sign&eacute;',          '#22c55e', 'fa-check-circle'],
    'co_signature_partenaire' => ['Co-sign.', '#a855f7', 'fa-users'],
    'assigne_autre'  => ['Assign&eacute;',       '#64748b', 'fa-share-square'],
  ];

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

  $activeFilterStatut = $filterStatut ?? null;
  $activeFilterScore = $filterScore ?? null;
  $activeFilterType = $filterType ?? null;
  $hasAnyFilter = ($activeFilterStatut || $activeFilterScore || $activeFilterType);
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-users"></i> Gestion des Leads</h1>
  <div class="header-actions">
    <a href="/admin/pipeline" class="btn-action"><i class="fas fa-columns"></i> Pipeline</a>
    <a href="/admin/funnel" class="btn-action"><i class="fas fa-filter"></i> Entonnoir</a>
  </div>
</div>

<!-- PIPELINE VISUAL TRACKER -->
<div class="pipeline-tracker">
  <?php foreach ($pipelineSteps as $stepKey => [$stepLabel, $stepColor, $stepIcon]):
    $stepCount = (int)($sCounts[$stepKey] ?? 0);
    $isActive = ($activeFilterStatut === $stepKey);
    $href = $isActive ? '/admin/leads' : '/admin/leads?statut=' . $stepKey;
  ?>
    <a href="<?= $href ?>" class="pipeline-step <?= $isActive ? 'active' : '' ?>" title="<?= strip_tags($stepLabel) ?>: <?= $stepCount ?> leads">
      <div class="pipeline-step-count" style="color: <?= $stepColor ?>;"><?= $stepCount ?></div>
      <div class="pipeline-step-label" style="color: <?= $stepColor ?>;"><?= $stepLabel ?></div>
      <div class="pipeline-step-bar" style="background: <?= $stepColor ?>;"></div>
    </a>
  <?php endforeach; ?>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon total"><i class="fas fa-users"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $totalLeads ?></div>
      <div class="stat-label">Total leads<?= $hasAnyFilter ? ' (filtr&eacute;s)' : '' ?></div>
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
  <div class="stat-card">
    <div class="stat-icon tendance"><i class="fas fa-chart-line"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $tendanceLeads ?></div>
      <div class="stat-label">Tendance</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon qualifie"><i class="fas fa-user-check"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= $qualifieLeads ?></div>
      <div class="stat-label">Qualifi&eacute;s</div>
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
    <span class="table-card-title"><?= $totalLeads ?> lead<?= $totalLeads > 1 ? 's' : '' ?><?= $hasAnyFilter ? ' (filtr&eacute;s)' : '' ?></span>
    <div class="table-filters">
      <a href="/admin/leads" class="filter-btn <?= !$hasAnyFilter ? 'active' : '' ?>">Tous</a>
      <div class="filter-separator"></div>
      <a href="/admin/leads?type=tendance" class="filter-btn <?= $activeFilterType === 'tendance' ? 'active' : '' ?>">Tendance</a>
      <a href="/admin/leads?type=qualifie" class="filter-btn <?= $activeFilterType === 'qualifie' ? 'active' : '' ?>">Qualifi&eacute;s</a>
      <div class="filter-separator"></div>
      <a href="/admin/leads?score=chaud" class="filter-btn <?= $activeFilterScore === 'chaud' ? 'active' : '' ?>"><i class="fas fa-fire" style="font-size:0.65rem;"></i> Chauds</a>
      <a href="/admin/leads?score=tiede" class="filter-btn <?= $activeFilterScore === 'tiede' ? 'active' : '' ?>"><i class="fas fa-temperature-half" style="font-size:0.65rem;"></i> Ti&egrave;des</a>
      <a href="/admin/leads?score=froid" class="filter-btn <?= $activeFilterScore === 'froid' ? 'active' : '' ?>"><i class="fas fa-snowflake" style="font-size:0.65rem;"></i> Froids</a>
    </div>
  </div>

  <?php if (empty($allLeads)): ?>
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <p>Aucun lead pour le moment.</p>
      <?php if ($hasAnyFilter): ?>
        <p style="font-size: 0.85rem; margin-top: 0.5rem;"><a href="/admin/leads">Voir tous les leads</a></p>
      <?php else: ?>
        <p style="font-size: 0.85rem; margin-top: 0.5rem;">Les leads appara&icirc;tront ici quand des visiteurs rempliront le formulaire d'estimation.</p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Contact</th>
            <th>Bien</th>
            <th>Ville</th>
            <th>Estimation</th>
            <th>Urgence</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allLeads as $lead): ?>
            <?php
              $isTendance = ($lead['lead_type'] ?? 'qualifie') === 'tendance';
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
              $statutKey = $lead['statut'] ?? 'nouveau';
              $statutClass = 'badge-' . $statutKey;
              $typeBien = $lead['type_bien'] ?? '';
              $surface = $lead['surface_m2'] ?? '';
              $pieces = $lead['pieces'] ?? '';
              $bienInfo = '';
              if ($typeBien !== '' && $typeBien !== null) {
                  $bienInfo = ucfirst(e((string) $typeBien));
                  if ($surface) $bienInfo .= ' &middot; ' . number_format((float) $surface, 0, ',', '') . ' m&sup2;';
                  if ($pieces) $bienInfo .= ' &middot; ' . (int) $pieces . 'p';
              }
            ?>
            <tr data-lead-id="<?= (int) $lead['id'] ?>">
              <td><?= (int) $lead['id'] ?></td>
              <td>
                <?php if ($isTendance): ?>
                  <span class="badge-type badge-tendance"><i class="fas fa-chart-line"></i> Tendance</span>
                <?php else: ?>
                  <span class="badge-type badge-qualifie"><i class="fas fa-user-check"></i> Qualifi&eacute;</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($isTendance): ?>
                  <div class="lead-email" style="color: var(--admin-muted); font-style: italic;">Anonyme</div>
                <?php else: ?>
                  <div class="lead-name"><?= e((string) ($lead['nom'] ?? '')) ?></div>
                  <div class="lead-email"><?= e((string) ($lead['email'] ?? '')) ?></div>
                  <?php if (!empty($lead['telephone'])): ?>
                    <div class="lead-email"><?= e((string) $lead['telephone']) ?></div>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td><?= $bienInfo ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td><?= e((string) $lead['ville']) ?></td>
              <td><strong><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> &euro;</strong></td>
              <td><?= !empty($lead['urgence']) ? e((string) $lead['urgence']) : '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td>
                <select class="score-select inline-score" data-lead-id="<?= (int)$lead['id'] ?>">
                  <option value="chaud" <?= ($lead['score'] ?? '') === 'chaud' ? 'selected' : '' ?>>&#x1F525; Chaud</option>
                  <option value="tiede" <?= ($lead['score'] ?? '') === 'tiede' ? 'selected' : '' ?>>&#x1F321; Ti&egrave;de</option>
                  <option value="froid" <?= ($lead['score'] ?? '') === 'froid' ? 'selected' : '' ?>>&#x2744; Froid</option>
                </select>
              </td>
              <td>
                <select class="statut-select inline-statut" data-lead-id="<?= (int)$lead['id'] ?>">
                  <?php foreach ($statutLabels as $sKey => $sLabel): ?>
                    <option value="<?= $sKey ?>" <?= $statutKey === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><?= e((string) $lead['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Toast -->
<div class="toast-notification" id="leadsToast"></div>

<script>
(function() {
  var csrfToken = <?= json_encode($_SESSION['csrf_token'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP) ?>;

  function showToast(message, type) {
    var toast = document.getElementById('leadsToast');
    toast.textContent = message;
    toast.className = 'toast-notification ' + type;
    toast.style.display = 'block';
    setTimeout(function() { toast.style.display = 'none'; }, 2500);
  }

  function updateLead(leadId, field, value) {
    var body = 'csrf_token=' + encodeURIComponent(csrfToken) + '&id=' + leadId + '&field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/leads/update-inline', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
      if (xhr.status === 200) {
        try {
          var resp = JSON.parse(xhr.responseText);
          if (resp.success) {
            showToast('Lead #' + leadId + ' mis \u00e0 jour', 'success');
          } else {
            showToast(resp.error || 'Erreur', 'error');
          }
        } catch(e) {
          showToast('Erreur', 'error');
        }
      } else {
        showToast('Erreur serveur', 'error');
      }
    };
    xhr.onerror = function() { showToast('Erreur r\u00e9seau', 'error'); };
    xhr.send(body);
  }

  document.querySelectorAll('.inline-statut').forEach(function(sel) {
    sel.addEventListener('change', function() {
      updateLead(this.dataset.leadId, 'statut', this.value);
    });
  });

  document.querySelectorAll('.inline-score').forEach(function(sel) {
    sel.addEventListener('change', function() {
      updateLead(this.dataset.leadId, 'score', this.value);
    });
  });
})();
</script>
