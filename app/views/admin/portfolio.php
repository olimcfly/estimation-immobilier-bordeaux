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

  .portfolio-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .portfolio-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .portfolio-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
  }

  .portfolio-card.card-valeur::before { background: linear-gradient(90deg, #ec4899, #db2777); }
  .portfolio-card.card-commission::before { background: linear-gradient(90deg, #22c55e, #16a34a); }
  .portfolio-card.card-biens::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
  .portfolio-card.card-taux::before { background: linear-gradient(90deg, #f59e0b, #d97706); }

  .portfolio-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }

  .card-valeur .portfolio-card-icon { background: rgba(236,72,153,0.1); color: #ec4899; }
  .card-commission .portfolio-card-icon { background: rgba(34,197,94,0.1); color: #22c55e; }
  .card-biens .portfolio-card-icon { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .card-taux .portfolio-card-icon { background: rgba(245,158,11,0.1); color: #f59e0b; }

  .portfolio-card-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--admin-text);
    line-height: 1;
    margin-bottom: 0.35rem;
  }

  .portfolio-card-label {
    font-size: 0.82rem;
    color: var(--admin-muted);
    font-weight: 500;
  }

  /* Configurable rate control */
  .rate-config {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
  }

  .rate-config-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .rate-config-label i { color: var(--admin-primary); }

  .rate-slider-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 200px;
  }

  .rate-slider {
    flex: 1;
    max-width: 300px;
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    border-radius: 3px;
    background: var(--admin-border);
    outline: none;
  }

  .rate-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--admin-primary);
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  }

  .rate-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--admin-primary);
    cursor: pointer;
    border: none;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  }

  .rate-value-display {
    background: var(--admin-primary-light);
    color: var(--admin-primary);
    font-weight: 700;
    font-size: 1rem;
    padding: 0.4rem 0.85rem;
    border-radius: 6px;
    min-width: 60px;
    text-align: center;
  }

  .rate-config-hint {
    font-size: 0.78rem;
    color: var(--admin-muted);
  }

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
    gap: 0.4rem;
  }

  .filter-btn {
    padding: 0.35rem 0.75rem;
    border-radius: 5px;
    font-size: 0.78rem;
    font-weight: 500;
    border: 1px solid var(--admin-border);
    background: #fff;
    color: var(--admin-text);
    cursor: pointer;
    transition: all 0.15s;
  }

  .filter-btn:hover,
  .filter-btn.active {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
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

  .commission-positive { color: #16a34a; font-weight: 600; }

  .partenaire-link {
    color: var(--admin-primary);
    text-decoration: none;
    font-weight: 500;
  }

  .partenaire-link:hover { text-decoration: underline; }

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

  .commission-input {
    width: 65px;
    padding: 0.3rem 0.4rem;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    font-size: 0.82rem;
    text-align: center;
    font-weight: 600;
    color: var(--admin-text);
    background: #fff;
    transition: border-color 0.15s;
  }

  .commission-input:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 2px var(--admin-primary-light);
  }

  .commission-input.saving {
    border-color: #f59e0b;
    background: rgba(245,158,11,0.05);
  }

  .commission-input.saved {
    border-color: #22c55e;
    background: rgba(34,197,94,0.05);
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  @media (max-width: 768px) {
    .portfolio-summary { grid-template-columns: 1fr 1fr; }
    .rate-config { flex-direction: column; align-items: flex-start; }
  }

  @media (max-width: 640px) {
    .portfolio-summary { grid-template-columns: 1fr; }
  }
</style>

<?php
  $allLeads = $leads ?? [];
  $totalV = $totalValeur ?? 0;
  $totalC = $totalCommission ?? 0;
  $defaultRate = $defaultRate ?? 3.0;

  $statutLabels = [
    'nouveau' => 'Nouveau',
    'contacte' => 'Contact&eacute;',
    'rdv_pris' => 'RDV Pris',
    'visite_realisee' => 'Visite R&eacute;alis&eacute;e',
    'mandat_simple' => 'Mandat Simple',
    'mandat_exclusif' => 'Mandat Exclusif',
    'compromis_vente' => 'Compromis de Vente',
    'signe' => 'Sign&eacute;',
    'co_signature_partenaire' => 'Co-signature',
    'assigne_autre' => 'Assign&eacute; Autre',
  ];

  $avgRate = count($allLeads) > 0
    ? array_sum(array_column($allLeads, 'commission_taux_effectif')) / count($allLeads)
    : $defaultRate;
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-briefcase"></i> Portefeuille Client</h1>
  <div class="header-actions">
    <a href="/admin/dashboard" class="btn-action"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="/admin/partenaires" class="btn-action"><i class="fas fa-handshake"></i> Partenaires</a>
    <a href="/admin/leads" class="btn-action"><i class="fas fa-users"></i> Leads</a>
  </div>
</div>

<!-- SUMMARY CARDS -->
<div class="portfolio-summary">
  <div class="portfolio-card card-valeur">
    <div class="portfolio-card-icon"><i class="fas fa-home"></i></div>
    <div class="portfolio-card-value" id="total-valeur"><?= number_format($totalV, 0, ',', ' ') ?> &euro;</div>
    <div class="portfolio-card-label">Valeur Immobili&egrave;re Totale</div>
  </div>
  <div class="portfolio-card card-commission">
    <div class="portfolio-card-icon"><i class="fas fa-coins"></i></div>
    <div class="portfolio-card-value" id="total-commission"><?= number_format($totalC, 0, ',', ' ') ?> &euro;</div>
    <div class="portfolio-card-label">Commission Potentielle Totale</div>
  </div>
  <div class="portfolio-card card-biens">
    <div class="portfolio-card-icon"><i class="fas fa-building"></i></div>
    <div class="portfolio-card-value"><?= count($allLeads) ?></div>
    <div class="portfolio-card-label">Biens en Portefeuille</div>
  </div>
  <div class="portfolio-card card-taux">
    <div class="portfolio-card-icon"><i class="fas fa-percentage"></i></div>
    <div class="portfolio-card-value" id="avg-rate"><?= number_format($avgRate, 1, ',', '') ?>%</div>
    <div class="portfolio-card-label">Taux Moyen de Commission</div>
  </div>
</div>

<!-- CONFIGURABLE DEFAULT RATE -->
<div class="rate-config">
  <div class="rate-config-label">
    <i class="fas fa-sliders-h"></i> Taux de commission par d&eacute;faut
  </div>
  <div class="rate-slider-group">
    <input type="range" class="rate-slider" id="default-rate-slider" min="0.5" max="10" step="0.1" value="<?= $defaultRate ?>">
    <div class="rate-value-display" id="default-rate-display"><?= number_format($defaultRate, 1) ?>%</div>
  </div>
  <div class="rate-config-hint">
    Ajustez le taux par d&eacute;faut appliqu&eacute; aux biens sans taux sp&eacute;cifique. Modifiez le taux individuellement dans le tableau.
  </div>
</div>

<!-- FILTER + TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title" id="table-count"><?= count($allLeads) ?> bien<?= count($allLeads) > 1 ? 's' : '' ?> en portefeuille</span>
    <div class="table-filters">
      <button class="filter-btn active" data-filter="all">Tous</button>
      <button class="filter-btn" data-filter="chaud">Chauds</button>
      <button class="filter-btn" data-filter="tiede">Ti&egrave;des</button>
      <button class="filter-btn" data-filter="froid">Froids</button>
    </div>
  </div>

  <?php if (empty($allLeads)): ?>
    <div class="empty-state">
      <i class="fas fa-inbox" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.3;"></i>
      <p>Aucun bien en portefeuille pour le moment.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">Les biens apparaissent ici lorsque des leads qualifi&eacute;s sont enregistr&eacute;s.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table" id="portfolio-table">
        <thead>
          <tr>
            <th>Client</th>
            <th>Bien</th>
            <th>Ville</th>
            <th>Valeur Immo</th>
            <th>Taux (%)</th>
            <th>Commission</th>
            <th>Score</th>
            <th>Statut</th>
            <th>Partenaire</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allLeads as $lead): ?>
            <?php
              $scoreClass = match($lead['score'] ?? '') { 'chaud' => 'badge-chaud', 'tiede' => 'badge-tiede', default => 'badge-froid' };
              $scoreIcon = match($lead['score'] ?? '') { 'chaud' => 'fa-fire', 'tiede' => 'fa-temperature-half', default => 'fa-snowflake' };
              $statutKey = $lead['statut'] ?? 'nouveau';
              $statutLabel = $statutLabels[$statutKey] ?? ucfirst(str_replace('_', ' ', $statutKey));
              $typeBien = $lead['type_bien'] ?? '';
              $surface = $lead['surface_m2'] ?? '';
              $bienInfo = '';
              if ($typeBien) {
                $bienInfo = ucfirst(htmlspecialchars((string)$typeBien, ENT_QUOTES, 'UTF-8'));
                if ($surface) $bienInfo .= ' &middot; ' . number_format((float)$surface, 0, ',', '') . ' m&sup2;';
              }
              if (!empty($lead['pieces'])) {
                $bienInfo .= ($bienInfo ? ' &middot; ' : '') . (int)$lead['pieces'] . 'p';
              }
              $estimation = (float)$lead['estimation'];
              $hasCustomRate = !empty($lead['commission_taux']);
              $taux = $lead['commission_taux_effectif'] ?? $defaultRate;
            ?>
            <tr data-score="<?= htmlspecialchars((string)($lead['score'] ?? 'froid'), ENT_QUOTES, 'UTF-8') ?>"
                data-estimation="<?= $estimation ?>"
                data-lead-id="<?= (int)$lead['id'] ?>"
                data-has-custom-rate="<?= $hasCustomRate ? '1' : '0' ?>"
                data-commission-montant="<?= $lead['commission_montant'] ? (float)$lead['commission_montant'] : '' ?>">
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars((string)($lead['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)($lead['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
              </td>
              <td><?= $bienInfo ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td><?= htmlspecialchars((string)$lead['ville'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><strong><?= number_format($estimation, 0, ',', ' ') ?> &euro;</strong></td>
              <td>
                <input type="number" class="commission-input rate-input"
                       value="<?= number_format((float)$taux, 1, '.', '') ?>"
                       min="0" max="20" step="0.1"
                       data-lead-id="<?= (int)$lead['id'] ?>"
                       title="Taux de commission pour ce bien">
              </td>
              <td><span class="commission-positive commission-cell"><?= number_format($lead['commission_calculee'] ?? 0, 0, ',', ' ') ?> &euro;</span></td>
              <td><span class="badge-score <?= $scoreClass ?>"><i class="fas <?= $scoreIcon ?>"></i> <?= htmlspecialchars((string)$lead['score'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><span class="badge-statut badge-<?= $statutKey ?>"><?= $statutLabel ?></span></td>
              <td>
                <?php if (!empty($lead['partenaire_nom'])): ?>
                  <a href="/admin/partenaires/edit?id=<?= (int)$lead['partenaire_id'] ?>" class="partenaire-link">
                    <?= htmlspecialchars((string)$lead['partenaire_nom'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                  <?php if (!empty($lead['partenaire_entreprise'])): ?>
                    <div style="font-size:0.75rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$lead['partenaire_entreprise'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:var(--admin-muted);">Non assign&eacute;</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
(function() {
  const slider = document.getElementById('default-rate-slider');
  const display = document.getElementById('default-rate-display');
  const totalCommissionEl = document.getElementById('total-commission');
  const avgRateEl = document.getElementById('avg-rate');
  const csrfToken = <?= json_encode($_SESSION['csrf_token'] ?? '') ?>;

  function formatNumber(n) {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function recalcAll() {
    let totalComm = 0;
    let totalRate = 0;
    let count = 0;
    document.querySelectorAll('#portfolio-table tbody tr').forEach(function(row) {
      if (row.style.display === 'none') return;
      const estimation = parseFloat(row.dataset.estimation) || 0;
      const fixedComm = row.dataset.commissionMontant ? parseFloat(row.dataset.commissionMontant) : null;
      const rateInput = row.querySelector('.rate-input');
      const rate = parseFloat(rateInput.value) || 0;
      const commission = fixedComm !== null ? fixedComm : (estimation * rate / 100);
      const commCell = row.querySelector('.commission-cell');
      if (commCell) commCell.textContent = formatNumber(commission) + ' \u20AC';
      totalComm += commission;
      totalRate += rate;
      count++;
    });
    if (totalCommissionEl) totalCommissionEl.innerHTML = formatNumber(totalComm) + ' &euro;';
    if (avgRateEl && count > 0) avgRateEl.textContent = (totalRate / count).toFixed(1).replace('.', ',') + '%';
  }

  // Default rate slider
  if (slider) {
    slider.addEventListener('input', function() {
      const rate = parseFloat(this.value);
      display.textContent = rate.toFixed(1) + '%';
      // Update all rows that don't have a custom rate
      document.querySelectorAll('#portfolio-table tbody tr').forEach(function(row) {
        if (row.dataset.hasCustomRate === '0' && !row.dataset.commissionMontant) {
          const input = row.querySelector('.rate-input');
          if (input) input.value = rate.toFixed(1);
        }
      });
      recalcAll();
    });
  }

  // Per-row rate input changes
  document.querySelectorAll('.rate-input').forEach(function(input) {
    let debounce = null;
    input.addEventListener('input', function() {
      recalcAll();
      const el = this;
      const leadId = el.dataset.leadId;
      const rate = parseFloat(el.value);
      if (isNaN(rate) || rate < 0 || rate > 20) return;
      el.classList.add('saving');
      el.classList.remove('saved');
      clearTimeout(debounce);
      debounce = setTimeout(function() {
        const form = new FormData();
        form.append('csrf_token', csrfToken);
        form.append('id', leadId);
        form.append('commission_taux', rate.toString());
        fetch('/admin/portfolio/update-rate', { method: 'POST', body: form })
          .then(function(r) { return r.json(); })
          .then(function(data) {
            el.classList.remove('saving');
            if (data.success) {
              el.classList.add('saved');
              el.closest('tr').dataset.hasCustomRate = '1';
              setTimeout(function() { el.classList.remove('saved'); }, 1500);
            }
          })
          .catch(function() { el.classList.remove('saving'); });
      }, 600);
    });
  });

  // Score filter buttons
  document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
      this.classList.add('active');
      const filter = this.dataset.filter;
      let visible = 0;
      document.querySelectorAll('#portfolio-table tbody tr').forEach(function(row) {
        if (filter === 'all' || row.dataset.score === filter) {
          row.style.display = '';
          visible++;
        } else {
          row.style.display = 'none';
        }
      });
      const countEl = document.getElementById('table-count');
      if (countEl) countEl.textContent = visible + ' bien' + (visible > 1 ? 's' : '') + ' en portefeuille';
      recalcAll();
    });
  });
})();
</script>
