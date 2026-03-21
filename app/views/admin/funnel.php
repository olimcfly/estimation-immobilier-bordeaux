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

  /* ========================= */
  /* KPI SUMMARY ROW           */
  /* ========================= */
  .funnel-kpis {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .funnel-kpi {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
  }

  .funnel-kpi::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
  }

  .funnel-kpi:nth-child(1)::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }
  .funnel-kpi:nth-child(2)::before { background: linear-gradient(90deg, #ef4444, #dc2626); }
  .funnel-kpi:nth-child(3)::before { background: linear-gradient(90deg, #22c55e, #16a34a); }
  .funnel-kpi:nth-child(4)::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }

  .funnel-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-bottom: 0.75rem;
  }

  .funnel-kpi:nth-child(1) .funnel-kpi-icon { background: rgba(139,92,246,0.1); color: #8b5cf6; }
  .funnel-kpi:nth-child(2) .funnel-kpi-icon { background: rgba(239,68,68,0.1); color: #ef4444; }
  .funnel-kpi:nth-child(3) .funnel-kpi-icon { background: rgba(34,197,94,0.1); color: #22c55e; }
  .funnel-kpi:nth-child(4) .funnel-kpi-icon { background: rgba(59,130,246,0.1); color: #3b82f6; }

  .funnel-kpi-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .funnel-kpi-label {
    font-size: 0.8rem;
    color: var(--admin-muted);
  }

  /* ========================= */
  /* SCORE DISTRIBUTION         */
  /* ========================= */
  .score-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .score-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .score-section h3 i { color: var(--admin-primary); }

  .score-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .score-card {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1.25rem;
    text-align: center;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }

  .score-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }

  .score-card.hot { border-color: rgba(239,68,68,0.2); }
  .score-card.hot:hover { border-color: #ef4444; }
  .score-card.warm { border-color: rgba(245,158,11,0.2); }
  .score-card.warm:hover { border-color: #f59e0b; }
  .score-card.cold { border-color: rgba(100,116,139,0.2); }
  .score-card.cold:hover { border-color: #64748b; }

  .score-card-icon {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
  }

  .score-card.hot .score-card-icon { color: #ef4444; }
  .score-card.warm .score-card-icon { color: #f59e0b; }
  .score-card.cold .score-card-icon { color: #64748b; }

  .score-card-count {
    font-size: 2rem;
    font-weight: 700;
    color: var(--admin-text);
    line-height: 1;
  }

  .score-card-label {
    font-size: 0.82rem;
    color: var(--admin-muted);
    margin-top: 0.35rem;
  }

  .score-card-pct {
    font-size: 0.78rem;
    font-weight: 600;
    margin-top: 0.5rem;
  }

  .score-card.hot .score-card-pct { color: #ef4444; }
  .score-card.warm .score-card-pct { color: #f59e0b; }
  .score-card.cold .score-card-pct { color: #64748b; }

  .score-card-valeur {
    font-size: 0.72rem;
    color: var(--admin-muted);
    margin-top: 0.25rem;
  }

  /* Score bar visualization */
  .score-bar-container {
    background: #f1f5f9;
    border-radius: 20px;
    height: 28px;
    display: flex;
    overflow: hidden;
    position: relative;
  }

  .score-bar-segment {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    color: #fff;
    transition: width 1s ease-out;
    min-width: 0;
  }

  .score-bar-segment.hot { background: #ef4444; }
  .score-bar-segment.warm { background: #f59e0b; }
  .score-bar-segment.cold { background: #94a3b8; }

  /* ========================= */
  /* FUNNEL VISUALIZATION       */
  /* ========================= */
  .funnel-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .funnel-section h3 {
    text-align: center;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 2rem;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .funnel-section h3 i { color: var(--admin-primary); }

  .funnel-visual {
    max-width: 750px;
    margin: 0 auto;
  }

  .funnel-stage {
    position: relative;
    margin: 0 auto;
    padding: 0.85rem 1.5rem;
    color: #fff;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    cursor: default;
    opacity: 0;
    transform: translateY(10px);
  }

  .funnel-stage.animated {
    opacity: 1;
    transform: translateY(0);
  }

  .funnel-stage:first-child {
    border-radius: 10px 10px 0 0;
    clip-path: polygon(0% 0%, 100% 0%, 97% 100%, 3% 100%);
  }

  .funnel-stage:not(:first-child):not(:last-child) {
    clip-path: polygon(3% 0%, 97% 0%, 94% 100%, 6% 100%);
  }

  .funnel-stage:last-child {
    border-radius: 0 0 6px 6px;
    clip-path: polygon(6% 0%, 94% 0%, 90% 100%, 10% 100%);
  }

  .funnel-stage:hover {
    filter: brightness(1.1);
    transform: scale(1.02);
    z-index: 2;
  }

  .funnel-stage.animated:hover {
    transform: translateY(0) scale(1.02);
  }

  .funnel-stage-left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.88rem;
  }

  .funnel-stage-left i {
    font-size: 0.82rem;
    opacity: 0.85;
  }

  .funnel-stage-right {
    text-align: right;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .funnel-stage-count {
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1;
  }

  .funnel-stage-meta {
    text-align: right;
    line-height: 1.3;
  }

  .funnel-stage-pct {
    font-size: 0.72rem;
    opacity: 0.85;
  }

  .funnel-stage-value {
    font-size: 0.7rem;
    opacity: 0.75;
  }

  /* Conversion arrow between stages */
  .funnel-conv-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 3px 0;
    font-size: 0.72rem;
    color: var(--admin-muted);
  }

  .funnel-conv-arrow .conv-badge {
    background: #f1f5f9;
    border-radius: 10px;
    padding: 1px 8px;
    font-weight: 600;
    font-size: 0.68rem;
  }

  .funnel-conv-arrow .conv-badge.high { color: #16a34a; background: rgba(34,197,94,0.1); }
  .funnel-conv-arrow .conv-badge.mid  { color: #d97706; background: rgba(245,158,11,0.1); }
  .funnel-conv-arrow .conv-badge.low  { color: #dc2626; background: rgba(239,68,68,0.1); }

  /* ========================= */
  /* CONVERSION RATES TABLE     */
  /* ========================= */
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
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .conversion-section h3 i { color: var(--admin-primary); }

  .conversion-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .conversion-row {
    display: grid;
    grid-template-columns: 1fr 80px 1fr;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    background: #f8fafc;
    border-radius: 8px;
    transition: all 0.2s;
  }

  .conversion-row:hover {
    background: #f1f5f9;
  }

  .conv-step-label {
    font-size: 0.82rem;
    color: var(--admin-text);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .conv-step-label .step-count {
    font-size: 0.72rem;
    color: var(--admin-muted);
    font-weight: 400;
  }

  .conv-rate-value {
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
  }

  .conv-rate-value.high { color: #16a34a; }
  .conv-rate-value.mid  { color: #d97706; }
  .conv-rate-value.low  { color: #dc2626; }

  .conv-bar-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .conv-bar-track {
    flex: 1;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
  }

  .conv-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1.2s ease-out;
  }

  .conv-bar-fill.high { background: linear-gradient(90deg, #22c55e, #16a34a); }
  .conv-bar-fill.mid  { background: linear-gradient(90deg, #f59e0b, #d97706); }
  .conv-bar-fill.low  { background: linear-gradient(90deg, #ef4444, #dc2626); }

  .conv-value-label {
    font-size: 0.7rem;
    color: var(--admin-muted);
    white-space: nowrap;
    min-width: 70px;
    text-align: right;
  }

  /* ========================= */
  /* VALUE BY STAGE             */
  /* ========================= */
  .value-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .value-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .value-section h3 i { color: var(--admin-primary); }

  .value-bars {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
  }

  .value-bar-row {
    display: grid;
    grid-template-columns: 160px 1fr 120px;
    align-items: center;
    gap: 0.75rem;
  }

  .value-bar-label {
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .value-bar-label i {
    font-size: 0.75rem;
    opacity: 0.6;
  }

  .value-bar-track {
    height: 22px;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
  }

  .value-bar-fill {
    height: 100%;
    border-radius: 6px;
    transition: width 1.2s ease-out;
    display: flex;
    align-items: center;
    padding-left: 8px;
    font-size: 0.68rem;
    font-weight: 600;
    color: #fff;
    min-width: 0;
  }

  .value-bar-amount {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--admin-text);
    text-align: right;
  }

  /* ========================= */
  /* RESPONSIVE                 */
  /* ========================= */
  @media (max-width: 768px) {
    .funnel-kpis { grid-template-columns: repeat(2, 1fr); }
    .score-cards { grid-template-columns: 1fr; }
    .funnel-stage { padding: 0.65rem 1rem; }
    .funnel-stage-left { font-size: 0.78rem; }
    .conversion-row { grid-template-columns: 1fr; gap: 0.4rem; text-align: center; }
    .conv-bar-wrap { justify-content: center; }
    .value-bar-row { grid-template-columns: 1fr; gap: 0.3rem; }
  }

  @media (max-width: 480px) {
    .funnel-kpis { grid-template-columns: 1fr; }
  }
</style>

<?php
  $pipeline = $pipelineData ?? [];
  $scores = $scoreData ?? [];
  $scoreVals = $scoreValeurs ?? [];
  $tCount = $tendanceCount ?? 0;
  $totalQ = $total ?? 0;
  $totalV = $totalValeur ?? 0;
  $monthly = $monthlyData ?? [];

  $stages = [
    'nouveau'         => ['Visiteurs / Prospects', '#94a3b8', 'fa-eye'],
    'contacte'        => ['Contact&eacute;s', '#3b82f6', 'fa-phone'],
    'rdv_pris'        => ['RDV Pris', '#8b5cf6', 'fa-calendar-check'],
    'visite_realisee' => ['Visite R&eacute;alis&eacute;e', '#ec4899', 'fa-home'],
    'mandat_simple'   => ['Mandat Simple', '#0ea5e9', 'fa-file-contract'],
    'mandat_exclusif' => ['Mandat Exclusif', '#14b8a6', 'fa-file-signature'],
    'compromis_vente' => ['Compromis de Vente', '#f97316', 'fa-handshake'],
    'signe'           => ['Sign&eacute;', '#22c55e', 'fa-check-circle'],
    'co_signature_partenaire' => ['Co-signature Partenaire', '#a855f7', 'fa-users'],
  ];

  // Compute totals
  $allVisitors = $tCount + $totalQ;
  $chaud = (int)($scores['chaud'] ?? 0);
  $tiede = (int)($scores['tiede'] ?? 0);
  $froid = (int)($scores['froid'] ?? 0);
  $chaudPct = $totalQ > 0 ? round(($chaud / $totalQ) * 100, 1) : 0;
  $tiedePct = $totalQ > 0 ? round(($tiede / $totalQ) * 100, 1) : 0;
  $froidPct = $totalQ > 0 ? round(($froid / $totalQ) * 100, 1) : 0;

  // Global conversion rate
  $signes = ($pipeline['signe']['count'] ?? 0) + ($pipeline['co_signature_partenaire']['count'] ?? 0);
  $convGlobal = $totalQ > 0 ? round(($signes / $totalQ) * 100, 1) : 0;

  // Max value for bar chart
  $maxValeur = 1;
  foreach ($stages as $key => $_) {
    $v = $pipeline[$key]['valeur'] ?? 0;
    if ($v > $maxValeur) $maxValeur = $v;
  }
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-filter"></i> Entonnoir de Vente</h1>
</div>

<a href="/admin/dashboard" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

<!-- KPI SUMMARY -->
<div class="funnel-kpis">
  <div class="funnel-kpi">
    <div class="funnel-kpi-icon"><i class="fas fa-globe"></i></div>
    <div class="funnel-kpi-value"><?= $allVisitors ?></div>
    <div class="funnel-kpi-label">Total visiteurs (<?= $tCount ?> tendance + <?= $totalQ ?> qualifi&eacute;s)</div>
  </div>
  <div class="funnel-kpi">
    <div class="funnel-kpi-icon"><i class="fas fa-fire"></i></div>
    <div class="funnel-kpi-value"><?= $chaud ?></div>
    <div class="funnel-kpi-label">Leads chauds (<?= $chaudPct ?>%)</div>
  </div>
  <div class="funnel-kpi">
    <div class="funnel-kpi-icon"><i class="fas fa-trophy"></i></div>
    <div class="funnel-kpi-value"><?= $convGlobal ?>%</div>
    <div class="funnel-kpi-label">Conversion globale</div>
  </div>
  <div class="funnel-kpi">
    <div class="funnel-kpi-icon"><i class="fas fa-euro-sign"></i></div>
    <div class="funnel-kpi-value"><?= number_format($totalV, 0, ',', ' ') ?> &euro;</div>
    <div class="funnel-kpi-label">Valeur totale du pipeline</div>
  </div>
</div>

<!-- SCORE DISTRIBUTION -->
<div class="score-section">
  <h3><i class="fas fa-thermometer-half"></i> Distribution par Score</h3>

  <div class="score-cards">
    <div class="score-card hot">
      <div class="score-card-icon"><i class="fas fa-fire"></i></div>
      <div class="score-card-count"><?= $chaud ?></div>
      <div class="score-card-label">Chauds</div>
      <div class="score-card-pct"><?= $chaudPct ?>%</div>
      <div class="score-card-valeur"><?= number_format((float)($scoreVals['chaud'] ?? 0), 0, ',', ' ') ?> &euro;</div>
    </div>
    <div class="score-card warm">
      <div class="score-card-icon"><i class="fas fa-temperature-half"></i></div>
      <div class="score-card-count"><?= $tiede ?></div>
      <div class="score-card-label">Ti&egrave;des</div>
      <div class="score-card-pct"><?= $tiedePct ?>%</div>
      <div class="score-card-valeur"><?= number_format((float)($scoreVals['tiede'] ?? 0), 0, ',', ' ') ?> &euro;</div>
    </div>
    <div class="score-card cold">
      <div class="score-card-icon"><i class="fas fa-snowflake"></i></div>
      <div class="score-card-count"><?= $froid ?></div>
      <div class="score-card-label">Froids</div>
      <div class="score-card-pct"><?= $froidPct ?>%</div>
      <div class="score-card-valeur"><?= number_format((float)($scoreVals['froid'] ?? 0), 0, ',', ' ') ?> &euro;</div>
    </div>
  </div>

  <!-- Visual bar -->
  <div class="score-bar-container" id="scoreBar">
    <?php if ($totalQ > 0): ?>
      <div class="score-bar-segment hot" style="width: 0%;" data-target="<?= $chaudPct ?>"><?= $chaudPct > 8 ? $chaudPct . '%' : '' ?></div>
      <div class="score-bar-segment warm" style="width: 0%;" data-target="<?= $tiedePct ?>"><?= $tiedePct > 8 ? $tiedePct . '%' : '' ?></div>
      <div class="score-bar-segment cold" style="width: 0%;" data-target="<?= $froidPct ?>"><?= $froidPct > 8 ? $froidPct . '%' : '' ?></div>
    <?php else: ?>
      <div class="score-bar-segment cold" style="width: 100%; background: #e2e8f0; color: var(--admin-muted);">Aucun lead</div>
    <?php endif; ?>
  </div>
</div>

<!-- FUNNEL VISUALIZATION -->
<div class="funnel-section">
  <h3><i class="fas fa-filter"></i> Entonnoir Prospect &rarr; Client</h3>

  <div class="funnel-visual" id="funnelVisual">
    <?php
      $i = 0;
      $stageKeys = array_keys($stages);
      $prevCount = 0;

      foreach ($stages as $key => [$label, $color, $icon]):
        $count = $pipeline[$key]['count'] ?? 0;
        $valeur = $pipeline[$key]['valeur'] ?? 0;
        $pct = $totalQ > 0 ? round(($count / $totalQ) * 100, 1) : 0;
        // Width narrows progressively
        $widthPct = max(30, 100 - ($i * 8));

        // Conversion from previous stage
        if ($i > 0 && $prevCount > 0) {
          $convRate = round(($count / $prevCount) * 100, 1);
          $convClass = $convRate >= 50 ? 'high' : ($convRate >= 25 ? 'mid' : 'low');
    ?>
      <div class="funnel-conv-arrow">
        <i class="fas fa-chevron-down"></i>
        <span class="conv-badge <?= $convClass ?>"><?= $convRate ?>%</span>
        <i class="fas fa-chevron-down"></i>
      </div>
    <?php
        }
    ?>
      <div class="funnel-stage" style="background: <?= $color ?>; width: <?= $widthPct ?>%;" data-delay="<?= $i * 80 ?>">
        <div class="funnel-stage-left">
          <i class="fas <?= $icon ?>"></i> <?= $label ?>
        </div>
        <div class="funnel-stage-right">
          <div class="funnel-stage-meta">
            <div class="funnel-stage-pct"><?= $pct ?>%</div>
            <?php if ($valeur > 0): ?>
              <div class="funnel-stage-value"><?= number_format($valeur, 0, ',', ' ') ?> &euro;</div>
            <?php endif; ?>
          </div>
          <div class="funnel-stage-count"><?= $count ?></div>
        </div>
      </div>
    <?php
        $prevCount = $count;
        $i++;
      endforeach;
    ?>
  </div>
</div>

<!-- CONVERSION RATES BY STAGE -->
<div class="conversion-section">
  <h3><i class="fas fa-exchange-alt"></i> Taux de Conversion par &Eacute;tape</h3>

  <div class="conversion-list">
    <?php
      $prevKey = null;
      $prevCount = 0;

      foreach ($stages as $key => [$label, $color, $icon]):
        $count = $pipeline[$key]['count'] ?? 0;
        $valeur = $pipeline[$key]['valeur'] ?? 0;

        if ($prevKey !== null && $prevCount > 0):
          $convRate = round(($count / $prevCount) * 100, 1);
          $convClass = $convRate >= 50 ? 'high' : ($convRate >= 25 ? 'mid' : 'low');
          $prevLabel = $stages[$prevKey][0];
    ?>
      <div class="conversion-row">
        <div class="conv-step-label">
          <?= $prevLabel ?> &rarr; <?= $label ?>
          <span class="step-count">(<?= $prevCount ?> &rarr; <?= $count ?>)</span>
        </div>
        <div class="conv-rate-value <?= $convClass ?>"><?= $convRate ?>%</div>
        <div class="conv-bar-wrap">
          <div class="conv-bar-track">
            <div class="conv-bar-fill <?= $convClass ?>" style="width: 0%;" data-target="<?= min($convRate, 100) ?>"></div>
          </div>
          <div class="conv-value-label"><?= number_format($valeur, 0, ',', ' ') ?> &euro;</div>
        </div>
      </div>
    <?php
        endif;
        $prevKey = $key;
        $prevCount = $count;
      endforeach;
    ?>
  </div>
</div>

<!-- VALUE BY STAGE (Euro) -->
<div class="value-section">
  <h3><i class="fas fa-euro-sign"></i> Valeurs en Euros par &Eacute;tape</h3>

  <div class="value-bars">
    <?php foreach ($stages as $key => [$label, $color, $icon]):
      $valeur = $pipeline[$key]['valeur'] ?? 0;
      $commission = $pipeline[$key]['commission'] ?? 0;
      $barPct = $maxValeur > 0 ? round(($valeur / $maxValeur) * 100, 1) : 0;
    ?>
      <div class="value-bar-row">
        <div class="value-bar-label"><i class="fas <?= $icon ?>"></i> <?= $label ?></div>
        <div class="value-bar-track">
          <div class="value-bar-fill" style="width: 0%; background: <?= $color ?>;" data-target="<?= $barPct ?>">
            <?php if ($barPct > 15 && $commission > 0): ?>
              <?= number_format($commission, 0, ',', ' ') ?> &euro; com.
            <?php endif; ?>
          </div>
        </div>
        <div class="value-bar-amount"><?= number_format($valeur, 0, ',', ' ') ?> &euro;</div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
(function() {
  // Animate funnel stages on scroll/load
  function animateFunnel() {
    var stages = document.querySelectorAll('.funnel-stage');
    stages.forEach(function(stage) {
      var delay = parseInt(stage.getAttribute('data-delay') || '0', 10);
      setTimeout(function() {
        stage.classList.add('animated');
      }, delay + 100);
    });
  }

  // Animate bar fills
  function animateBars() {
    var bars = document.querySelectorAll('[data-target]');
    bars.forEach(function(bar) {
      var target = bar.getAttribute('data-target');
      setTimeout(function() {
        bar.style.width = target + '%';
      }, 300);
    });
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      animateFunnel();
      animateBars();
    });
  } else {
    animateFunnel();
    animateBars();
  }
})();
</script>
