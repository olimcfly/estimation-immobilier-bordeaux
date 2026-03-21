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

  .funnel-container {
    max-width: 800px;
    margin: 0 auto;
  }

  /* Funnel visualization */
  .funnel-wrapper {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .funnel-wrapper h3 {
    text-align: center;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 2rem;
    color: var(--admin-text);
  }

  .funnel-stage {
    position: relative;
    margin: 0 auto 4px;
    padding: 1rem 1.5rem;
    color: #fff;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s;
    cursor: default;
    clip-path: polygon(3% 0%, 97% 0%, 100% 100%, 0% 100%);
  }

  .funnel-stage:first-child {
    border-radius: 8px 8px 0 0;
    clip-path: polygon(0% 0%, 100% 0%, 97% 100%, 3% 100%);
  }

  .funnel-stage:last-child {
    border-radius: 0 0 8px 8px;
  }

  .funnel-stage:hover {
    transform: scale(1.02);
    z-index: 2;
  }

  .funnel-stage-label {
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .funnel-stage-label i { font-size: 0.85rem; opacity: 0.8; }

  .funnel-stage-data {
    text-align: right;
  }

  .funnel-stage-count {
    font-size: 1.25rem;
    font-weight: 700;
  }

  .funnel-stage-pct {
    font-size: 0.75rem;
    opacity: 0.8;
  }

  .funnel-stage-value {
    font-size: 0.72rem;
    opacity: 0.7;
  }

  /* Arrow between stages */
  .funnel-arrow {
    text-align: center;
    color: var(--admin-muted);
    font-size: 0.75rem;
    padding: 2px 0;
  }

  /* Score summary cards */
  .score-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .score-summary-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    text-align: center;
  }

  .score-summary-card .score-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }

  .score-summary-card.hot .score-icon { color: #ef4444; }
  .score-summary-card.warm .score-icon { color: #f59e0b; }
  .score-summary-card.cold .score-icon { color: #64748b; }

  .score-summary-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--admin-text);
  }

  .score-summary-label {
    font-size: 0.82rem;
    color: var(--admin-muted);
    margin-top: 0.25rem;
  }

  .score-summary-pct {
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-top: 0.15rem;
  }

  /* Conversion rates */
  .conversion-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .conversion-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .conversion-section h3 i { color: var(--admin-primary); }

  .conversion-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .conversion-item {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
  }

  .conversion-item .conv-from-to {
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-bottom: 0.5rem;
  }

  .conversion-item .conv-rate {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-primary);
  }

  /* Tendance box */
  .tendance-box {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 2rem;
  }

  .tendance-box .tendance-icon {
    font-size: 2rem;
    color: #a855f7;
    margin-bottom: 0.5rem;
  }

  .tendance-box .tendance-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--admin-text);
  }

  .tendance-box .tendance-label {
    font-size: 0.85rem;
    color: var(--admin-muted);
  }

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

  @media (max-width: 768px) {
    .score-summary { grid-template-columns: 1fr; }
    .funnel-stage { padding: 0.75rem 1rem; }
    .funnel-stage-label { font-size: 0.8rem; }
  }
</style>

<?php
  $pipeline = $pipelineData ?? [];
  $scores = $scoreData ?? [];
  $tCount = $tendanceCount ?? 0;
  $totalQ = $total ?? 0;

  $stages = [
    'nouveau'        => ['Visiteurs / Prospects', '#94a3b8', 'fa-eye'],
    'contacte'       => ['Contact&eacute;s', '#3b82f6', 'fa-phone'],
    'rdv_pris'       => ['RDV Pris', '#8b5cf6', 'fa-calendar-check'],
    'visite_realisee'=> ['Visite R&eacute;alis&eacute;e', '#ec4899', 'fa-home'],
    'mandat_simple'  => ['Mandat Simple', '#0ea5e9', 'fa-file-contract'],
    'mandat_exclusif'=> ['Mandat Exclusif', '#14b8a6', 'fa-file-signature'],
    'compromis_vente'=> ['Compromis de Vente', '#f97316', 'fa-handshake'],
    'signe'          => ['Sign&eacute;', '#22c55e', 'fa-check-circle'],
    'co_signature_partenaire' => ['Co-signature Partenaire', '#a855f7', 'fa-users'],
  ];

  // Calculate widths based on count
  $maxCount = max(1, $tCount + $totalQ);
  $stageKeys = array_keys($stages);
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-filter"></i> Entonnoir de Vente</h1>
</div>

<a href="/admin/dashboard" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

<div class="funnel-container">

  <!-- Tendance entry point -->
  <div class="tendance-box">
    <div class="tendance-icon"><i class="fas fa-globe"></i></div>
    <div class="tendance-value"><?= $tCount + $totalQ ?></div>
    <div class="tendance-label">Total Visiteurs (<?= $tCount ?> tendance + <?= $totalQ ?> qualifi&eacute;s)</div>
  </div>

  <!-- Score distribution -->
  <div class="score-summary">
    <div class="score-summary-card hot">
      <div class="score-icon"><i class="fas fa-fire"></i></div>
      <div class="score-summary-value"><?= (int)($scores['chaud'] ?? 0) ?></div>
      <div class="score-summary-label">Chauds</div>
      <div class="score-summary-pct"><?= $totalQ > 0 ? round(((int)($scores['chaud'] ?? 0) / $totalQ) * 100, 1) : 0 ?>%</div>
    </div>
    <div class="score-summary-card warm">
      <div class="score-icon"><i class="fas fa-temperature-half"></i></div>
      <div class="score-summary-value"><?= (int)($scores['tiede'] ?? 0) ?></div>
      <div class="score-summary-label">Ti&egrave;des</div>
      <div class="score-summary-pct"><?= $totalQ > 0 ? round(((int)($scores['tiede'] ?? 0) / $totalQ) * 100, 1) : 0 ?>%</div>
    </div>
    <div class="score-summary-card cold">
      <div class="score-icon"><i class="fas fa-snowflake"></i></div>
      <div class="score-summary-value"><?= (int)($scores['froid'] ?? 0) ?></div>
      <div class="score-summary-label">Froids</div>
      <div class="score-summary-pct"><?= $totalQ > 0 ? round(((int)($scores['froid'] ?? 0) / $totalQ) * 100, 1) : 0 ?>%</div>
    </div>
  </div>

  <!-- Funnel visualization -->
  <div class="funnel-wrapper">
    <h3><i class="fas fa-filter"></i> Entonnoir Prospect &rarr; Client</h3>

    <?php $i = 0; foreach ($stages as $key => [$label, $color, $icon]): ?>
      <?php
        $count = $pipeline[$key]['count'] ?? 0;
        $valeur = $pipeline[$key]['valeur'] ?? 0;
        $pct = $totalQ > 0 ? round(($count / $totalQ) * 100, 1) : 0;
        $widthPct = 100 - ($i * 7);
      ?>
      <div class="funnel-stage" style="background: <?= $color ?>; width: <?= $widthPct ?>%; margin-left: auto; margin-right: auto;">
        <div class="funnel-stage-label"><i class="fas <?= $icon ?>"></i> <?= $label ?></div>
        <div class="funnel-stage-data">
          <div class="funnel-stage-count"><?= $count ?></div>
          <div class="funnel-stage-pct"><?= $pct ?>%</div>
          <?php if ($valeur > 0): ?>
            <div class="funnel-stage-value"><?= number_format($valeur, 0, ',', ' ') ?> &euro;</div>
          <?php endif; ?>
        </div>
      </div>
      <?php if ($i < count($stages) - 1): ?>
        <div class="funnel-arrow"><i class="fas fa-chevron-down"></i></div>
      <?php endif; ?>
    <?php $i++; endforeach; ?>
  </div>

  <!-- Conversion rates between stages -->
  <div class="conversion-section">
    <h3><i class="fas fa-exchange-alt"></i> Taux de Conversion par &Eacute;tape</h3>
    <div class="conversion-grid">
      <?php
        $prevKey = null;
        $prevCount = 0;
        foreach ($stages as $key => [$label, $color, $icon]) {
          $count = $pipeline[$key]['count'] ?? 0;
          if ($prevKey !== null && $prevCount > 0) {
            $convRate = round(($count / $prevCount) * 100, 1);
            $prevLabel = $stages[$prevKey][0];
            echo '<div class="conversion-item">';
            echo '<div class="conv-from-to">' . $prevLabel . ' &rarr; ' . $label . '</div>';
            echo '<div class="conv-rate">' . $convRate . '%</div>';
            echo '</div>';
          }
          $prevKey = $key;
          $prevCount = $count;
        }
      ?>
    </div>
  </div>

</div>
