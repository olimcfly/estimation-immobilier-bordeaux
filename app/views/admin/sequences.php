<style>
  .seq-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .seq-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .seq-header h1 i { color: var(--admin-primary); }

  .btn-new-seq {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
  }

  .btn-new-seq:hover { background: #6b0f2d; color: #fff; }

  .seq-tabs {
    display: flex;
    gap: 0;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--admin-border);
  }

  .seq-tab {
    padding: 0.65rem 1.25rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--admin-muted);
    cursor: pointer;
    border: none;
    background: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    font-family: inherit;
  }

  .seq-tab:hover { color: var(--admin-text); }
  .seq-tab.active { color: var(--admin-primary); border-bottom-color: var(--admin-primary); }

  .seq-panel { display: none; }
  .seq-panel.active { display: block; }

  /* Sequence cards */
  .seq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .seq-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
  }

  .seq-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.75rem;
  }

  .seq-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--admin-text);
  }

  .seq-card-status {
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .status-active { background: rgba(34,197,94,0.1); color: #16a34a; }
  .status-draft { background: rgba(100,116,139,0.1); color: #475569; }
  .status-paused { background: rgba(245,158,11,0.1); color: #d97706; }

  .seq-card-info {
    font-size: 0.82rem;
    color: var(--admin-muted);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .seq-card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
  }

  .seq-card-actions a, .seq-card-actions button {
    padding: 0.3rem 0.7rem;
    border-radius: 4px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid var(--admin-border);
    background: #fff;
    color: var(--admin-muted);
    font-family: inherit;
  }

  .seq-card-actions a:hover { border-color: var(--admin-primary); color: var(--admin-primary); }

  .seq-card-actions .btn-del {
    color: var(--admin-danger);
    border-color: transparent;
  }

  .seq-card-actions .btn-del:hover { background: #fef2f2; border-color: var(--admin-danger); }

  /* Persona section */
  .persona-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .persona-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    text-align: center;
  }

  .persona-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin: 0 auto 0.75rem;
  }

  .persona-card-title {
    font-weight: 700;
    font-size: 1rem;
    color: var(--admin-text);
    margin-bottom: 0.5rem;
  }

  .persona-card-desc {
    font-size: 0.82rem;
    color: var(--admin-muted);
    line-height: 1.5;
    margin-bottom: 0.75rem;
  }

  .persona-card-tone {
    font-size: 0.78rem;
    padding: 0.4rem 0.6rem;
    background: #f8fafc;
    border-radius: 4px;
    color: var(--admin-muted);
    margin-bottom: 0.75rem;
  }

  .persona-topics {
    text-align: left;
    list-style: none;
    padding: 0;
  }

  .persona-topics li {
    font-size: 0.78rem;
    color: var(--admin-text);
    padding: 0.3rem 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .persona-topics li:last-child { border-bottom: none; }
  .persona-topics li i { color: var(--admin-primary); font-size: 0.65rem; }

  /* BANT section */
  .bant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .bant-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
  }

  .bant-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--admin-text);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .bant-questions {
    list-style: none;
    padding: 0;
  }

  .bant-questions li {
    font-size: 0.82rem;
    color: var(--admin-muted);
    padding: 0.35rem 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .bant-questions li:last-child { border-bottom: none; }

  /* Leads with personas table */
  .persona-table-wrap {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .persona-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .persona-table thead { background: #f8fafc; }

  .persona-table th {
    padding: 0.65rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    border-bottom: 1px solid var(--admin-border);
  }

  .persona-table td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .persona-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .flash-msg {
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .flash-msg.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
  .flash-msg.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  .empty-state i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3; display: block; }

  @media (max-width: 640px) {
    .seq-grid, .persona-grid, .bant-grid { grid-template-columns: 1fr; }
  }
</style>

<?php
  $sequences = $sequences ?? [];
  $personas = $personas ?? [];
  $bantCriteria = $bantCriteria ?? [];
  $neuropersonas = $neuropersonas ?? [];
  $flash = $_SESSION['seq_flash'] ?? null;
  unset($_SESSION['seq_flash']);
?>

<!-- HEADER -->
<div class="seq-header">
  <h1><i class="fas fa-project-diagram"></i> S&eacute;quences Email & NeuroPersonas</h1>
  <a href="/admin/sequences/edit" class="btn-new-seq">
    <i class="fas fa-plus"></i> Nouvelle s&eacute;quence
  </a>
</div>

<!-- FLASH -->
<?php if ($flash): ?>
  <div class="flash-msg <?= $flash['type'] ?>">
    <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
    <?= $flash['message'] ?>
  </div>
<?php endif; ?>

<!-- TABS -->
<div class="seq-tabs">
  <button class="seq-tab active" onclick="switchSeqTab('sequences', this)">
    <i class="fas fa-list"></i> S&eacute;quences (<?= count($sequences) ?>)
  </button>
  <button class="seq-tab" onclick="switchSeqTab('personas', this)">
    <i class="fas fa-brain"></i> NeuroPersonas
  </button>
  <button class="seq-tab" onclick="switchSeqTab('bant', this)">
    <i class="fas fa-clipboard-check"></i> BANT
  </button>
  <button class="seq-tab" onclick="switchSeqTab('leads', this)">
    <i class="fas fa-users"></i> Leads profil&eacute;s (<?= count($personas) ?>)
  </button>
</div>

<!-- SEQUENCES PANEL -->
<div class="seq-panel active" id="panel-sequences">
  <?php if (empty($sequences)): ?>
    <div class="empty-state">
      <i class="fas fa-project-diagram"></i>
      <p>Aucune s&eacute;quence email cr&eacute;&eacute;e.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">
        <a href="/admin/sequences/edit" style="color:var(--admin-primary);">Cr&eacute;er votre premi&egrave;re s&eacute;quence</a>
      </p>
    </div>
  <?php else: ?>
    <div class="seq-grid">
      <?php foreach ($sequences as $seq): ?>
        <?php
          $statusClass = match($seq['status'] ?? 'draft') {
            'active' => 'status-active',
            'paused' => 'status-paused',
            default => 'status-draft',
          };
        ?>
        <div class="seq-card">
          <div class="seq-card-header">
            <span class="seq-card-title"><?= htmlspecialchars($seq['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <span class="seq-card-status <?= $statusClass ?>"><?= htmlspecialchars($seq['status'] ?? 'draft', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="seq-card-info">
            <i class="fas fa-brain"></i>
            Persona : <strong><?= htmlspecialchars($seq['persona'] ?? 'Non d&eacute;fini', ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
          <div class="seq-card-info">
            <i class="fas fa-bolt"></i>
            D&eacute;clencheur : <?= htmlspecialchars($seq['trigger_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="seq-card-actions">
            <a href="/admin/sequences/edit?id=<?= (int) $seq['id'] ?>"><i class="fas fa-edit"></i> Modifier</a>
            <form method="post" action="/admin/sequences/delete" style="display:inline;" onsubmit="return confirm('Supprimer cette s&eacute;quence ?');">
              <input type="hidden" name="id" value="<?= (int) $seq['id'] ?>">
              <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- NEUROPERSONAS PANEL -->
<div class="seq-panel" id="panel-personas">
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;"><i class="fas fa-brain"></i> Les 4 NeuroPersonas</h2>
  <p style="font-size:0.85rem;color:var(--admin-muted);margin-bottom:1.5rem;">
    Identifiez le profil de chaque lead pour personnaliser vos communications et s&eacute;quences email.
  </p>
  <div class="persona-grid">
    <?php foreach ($neuropersonas as $key => $persona): ?>
      <div class="persona-card">
        <div class="persona-icon" style="background:<?= $persona['color'] ?>15;color:<?= $persona['color'] ?>;">
          <i class="fas <?= $persona['icon'] ?>"></i>
        </div>
        <div class="persona-card-title"><?= $persona['label'] ?></div>
        <div class="persona-card-desc"><?= $persona['description'] ?></div>
        <div class="persona-card-tone">
          <strong>Ton email :</strong> <?= $persona['email_tone'] ?>
        </div>
        <ul class="persona-topics">
          <?php foreach ($persona['article_topics'] as $topic): ?>
            <li><i class="fas fa-circle"></i> <?= $topic ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- BANT PANEL -->
<div class="seq-panel" id="panel-bant">
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;"><i class="fas fa-clipboard-check"></i> Crit&egrave;res BANT</h2>
  <p style="font-size:0.85rem;color:var(--admin-muted);margin-bottom:1.5rem;">
    Les questions cl&eacute;s pour qualifier vos leads selon la m&eacute;thode BANT (Budget, Autorit&eacute;, Besoin, D&eacute;lai).
  </p>
  <div class="bant-grid">
    <?php foreach ($bantCriteria as $key => $criteria): ?>
      <div class="bant-card">
        <div class="bant-card-title">
          <?php
            $bantIcons = ['budget' => 'fa-euro-sign', 'authority' => 'fa-user-shield', 'need' => 'fa-home', 'timeline' => 'fa-clock'];
          ?>
          <i class="fas <?= $bantIcons[$key] ?? 'fa-question' ?>" style="color:var(--admin-primary);"></i>
          <?= $criteria['label'] ?>
        </div>
        <ul class="bant-questions">
          <?php foreach ($criteria['questions'] as $q): ?>
            <li><?= $q ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- LEADS WITH PERSONAS PANEL -->
<div class="seq-panel" id="panel-leads">
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;"><i class="fas fa-users"></i> Leads profil&eacute;s</h2>
  <?php if (empty($personas)): ?>
    <div class="empty-state">
      <i class="fas fa-user-tag"></i>
      <p>Aucun lead profil&eacute; pour le moment.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">Les profils NeuroPersona appara&icirc;tront ici quand vous qualifierez vos leads.</p>
    </div>
  <?php else: ?>
    <div class="persona-table-wrap">
      <div style="overflow-x:auto;">
        <table class="persona-table">
          <thead>
            <tr>
              <th>Lead</th>
              <th>NeuroPersona</th>
              <th>Budget</th>
              <th>Autorit&eacute;</th>
              <th>Besoin</th>
              <th>D&eacute;lai</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($personas as $p): ?>
              <?php
                $personaInfo = $neuropersonas[$p['neuropersona'] ?? ''] ?? null;
              ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($p['nom'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8') ?></strong><br>
                  <span style="font-size:0.78rem;color:var(--admin-muted);"><?= htmlspecialchars($p['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </td>
                <td>
                  <?php if ($personaInfo): ?>
                    <span class="persona-badge" style="background:<?= $personaInfo['color'] ?>15;color:<?= $personaInfo['color'] ?>;">
                      <i class="fas <?= $personaInfo['icon'] ?>"></i> <?= $personaInfo['label'] ?>
                    </span>
                  <?php else: ?>
                    <span style="color:var(--admin-muted);">-</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['bant_budget'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['bant_authority'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['bant_need'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['bant_timeline'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="white-space:nowrap;"><?= htmlspecialchars($p['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
function switchSeqTab(tab, btn) {
  document.querySelectorAll('.seq-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.seq-panel').forEach(function(p) { p.classList.remove('active'); });
  btn.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}
</script>
