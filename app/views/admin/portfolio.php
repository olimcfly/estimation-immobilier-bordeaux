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

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  @media (max-width: 768px) {
    .portfolio-summary { grid-template-columns: 1fr; }
  }
</style>

<?php
  $allLeads = $leads ?? [];
  $totalV = $totalValeur ?? 0;
  $totalC = $totalCommission ?? 0;

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
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-briefcase"></i> Portefeuille Client</h1>
</div>

<a href="/admin/dashboard" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

<!-- SUMMARY CARDS -->
<div class="portfolio-summary">
  <div class="portfolio-card card-valeur">
    <div class="portfolio-card-icon"><i class="fas fa-home"></i></div>
    <div class="portfolio-card-value"><?= number_format($totalV, 0, ',', ' ') ?> &euro;</div>
    <div class="portfolio-card-label">Valeur Immobili&egrave;re Totale</div>
  </div>
  <div class="portfolio-card card-commission">
    <div class="portfolio-card-icon"><i class="fas fa-coins"></i></div>
    <div class="portfolio-card-value"><?= number_format($totalC, 0, ',', ' ') ?> &euro;</div>
    <div class="portfolio-card-label">Commission Potentielle Totale</div>
  </div>
  <div class="portfolio-card card-biens">
    <div class="portfolio-card-icon"><i class="fas fa-building"></i></div>
    <div class="portfolio-card-value"><?= count($allLeads) ?></div>
    <div class="portfolio-card-label">Biens en Portefeuille</div>
  </div>
</div>

<!-- TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title"><?= count($allLeads) ?> bien<?= count($allLeads) > 1 ? 's' : '' ?> en portefeuille</span>
  </div>

  <?php if (empty($allLeads)): ?>
    <div class="empty-state">
      <i class="fas fa-inbox" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.3;"></i>
      <p>Aucun bien en portefeuille pour le moment.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Client</th>
            <th>Bien</th>
            <th>Ville</th>
            <th>Valeur Immo</th>
            <th>Taux</th>
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
            ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars((string)($lead['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)($lead['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
              </td>
              <td><?= $bienInfo ?: '<span style="color:var(--admin-muted);">-</span>' ?></td>
              <td><?= htmlspecialchars((string)$lead['ville'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><strong><?= number_format((float)$lead['estimation'], 0, ',', ' ') ?> &euro;</strong></td>
              <td><?= number_format($lead['commission_taux_effectif'] ?? 3, 1) ?>%</td>
              <td><span class="commission-positive"><?= number_format($lead['commission_calculee'] ?? 0, 0, ',', ' ') ?> &euro;</span></td>
              <td><span class="badge-score <?= $scoreClass ?>"><i class="fas <?= $scoreIcon ?>"></i> <?= htmlspecialchars((string)$lead['score'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><span class="badge-statut badge-<?= $statutKey ?>"><?= $statutLabel ?></span></td>
              <td>
                <?php if (!empty($lead['partenaire_nom'])): ?>
                  <div style="font-weight:500;"><?= htmlspecialchars((string)$lead['partenaire_nom'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php if (!empty($lead['partenaire_entreprise'])): ?>
                    <div style="font-size:0.75rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$lead['partenaire_entreprise'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:var(--admin-muted);">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
