<style>
  .seq-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .seq-edit-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .seq-edit-header h1 i { color: var(--admin-primary); }

  .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    color: var(--admin-muted);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .btn-back:hover { border-color: var(--admin-primary); color: var(--admin-primary); }

  .seq-form-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    max-width: 800px;
    margin-bottom: 2rem;
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-muted);
    margin-bottom: 0.35rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    background: #fff;
    color: var(--admin-text);
  }

  .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px var(--admin-primary-light);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-save {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.5rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
  }

  .btn-save:hover { background: #6b0f2d; }

  /* Steps section */
  .steps-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    max-width: 800px;
  }

  .steps-section h3 {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .step-card {
    background: #f8fafc;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    position: relative;
  }

  .step-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
  }

  .step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--admin-primary);
    color: #fff;
    border-radius: 50%;
    font-size: 0.8rem;
    font-weight: 700;
  }

  .btn-remove-step {
    padding: 0.3rem 0.6rem;
    background: none;
    border: none;
    color: var(--admin-danger);
    cursor: pointer;
    font-size: 0.9rem;
  }

  .step-fields {
    display: grid;
    gap: 0.75rem;
  }

  .step-fields input, .step-fields textarea {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    font-size: 0.85rem;
    font-family: inherit;
    background: #fff;
  }

  .step-fields textarea {
    min-height: 80px;
    resize: vertical;
  }

  .step-delay {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .step-delay input {
    width: 80px;
  }

  .step-delay span {
    font-size: 0.82rem;
    color: var(--admin-muted);
  }

  .btn-add-step {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: var(--admin-info);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    margin-top: 0.5rem;
  }

  .btn-add-step:hover { opacity: 0.9; }

  /* AI suggestion */
  .suggestion-box {
    margin-top: 1rem;
    background: #faf5ff;
    border: 1px solid #e9d5ff;
    border-radius: 6px;
    padding: 1rem;
    display: none;
  }

  .suggestion-box.show { display: block; }

  .suggestion-box h4 {
    font-size: 0.88rem;
    color: #7c3aed;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .suggestion-list {
    list-style: none;
    padding: 0;
  }

  .suggestion-list li {
    font-size: 0.82rem;
    padding: 0.35rem 0;
    border-bottom: 1px solid #f3e8ff;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .suggestion-list li:last-child { border-bottom: none; }
  .suggestion-list li i { color: #a855f7; font-size: 0.65rem; }

  @media (max-width: 640px) {
    .form-row { grid-template-columns: 1fr; }
  }
</style>

<?php
  $sequence = $sequence ?? null;
  $steps = $steps ?? [];
  $isEdit = $sequence !== null;
  $neuropersonas = $neuropersonas ?? [];
?>

<!-- HEADER -->
<div class="seq-edit-header">
  <h1>
    <i class="fas fa-<?= $isEdit ? 'edit' : 'plus-circle' ?>"></i>
    <?= $isEdit ? 'Modifier la s&eacute;quence' : 'Nouvelle s&eacute;quence email' ?>
  </h1>
  <a href="/admin/sequences" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<!-- FORM -->
<div class="seq-form-card">
  <form method="post" action="/admin/sequences/save" id="seqForm">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int) $sequence['id'] ?>">
    <?php endif; ?>
    <input type="hidden" name="steps_json" id="stepsJsonInput" value="">

    <div class="form-group">
      <label>Nom de la s&eacute;quence</label>
      <input type="text" name="name" value="<?= htmlspecialchars($sequence['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Bienvenue acheteur analytique" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>NeuroPersona cibl&eacute;</label>
        <select name="persona" id="personaSelect" onchange="onPersonaChange(this.value)">
          <option value="">-- Tous les profils --</option>
          <?php foreach ($neuropersonas as $key => $p): ?>
            <option value="<?= $key ?>" <?= ($sequence['persona'] ?? '') === $key ? 'selected' : '' ?>>
              <?= $p['label'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>D&eacute;clencheur</label>
        <select name="trigger_event">
          <option value="lead_created" <?= ($sequence['trigger_event'] ?? '') === 'lead_created' ? 'selected' : '' ?>>Nouveau lead cr&eacute;&eacute;</option>
          <option value="estimation_done" <?= ($sequence['trigger_event'] ?? '') === 'estimation_done' ? 'selected' : '' ?>>Estimation r&eacute;alis&eacute;e</option>
          <option value="lead_qualified" <?= ($sequence['trigger_event'] ?? '') === 'lead_qualified' ? 'selected' : '' ?>>Lead qualifi&eacute;</option>
          <option value="lead_hot" <?= ($sequence['trigger_event'] ?? '') === 'lead_hot' ? 'selected' : '' ?>>Lead chaud d&eacute;tect&eacute;</option>
          <option value="manual" <?= ($sequence['trigger_event'] ?? '') === 'manual' ? 'selected' : '' ?>>D&eacute;clenchement manuel</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Statut</label>
      <select name="status">
        <option value="draft" <?= ($sequence['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
        <option value="active" <?= ($sequence['status'] ?? '') === 'active' ? 'selected' : '' ?>>Actif</option>
        <option value="paused" <?= ($sequence['status'] ?? '') === 'paused' ? 'selected' : '' ?>>En pause</option>
      </select>
    </div>

    <!-- AI Suggestions based on persona -->
    <div class="suggestion-box" id="suggestionBox">
      <h4><i class="fas fa-lightbulb"></i> Suggestions d'articles pour ce persona</h4>
      <ul class="suggestion-list" id="suggestionList"></ul>
    </div>

    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Sauvegarder</button>
  </form>
</div>

<!-- STEPS -->
<div class="steps-section">
  <h3><i class="fas fa-list-ol"></i> &Eacute;tapes de la s&eacute;quence</h3>
  <p style="font-size:0.82rem;color:var(--admin-muted);margin-bottom:1rem;">
    D&eacute;finissez les emails envoy&eacute;s automatiquement avec un d&eacute;lai entre chaque &eacute;tape.
  </p>

  <div id="stepsContainer">
    <?php if (!empty($steps)): ?>
      <?php foreach ($steps as $i => $step): ?>
        <div class="step-card" data-step="<?= $i ?>">
          <div class="step-card-header">
            <span class="step-number"><?= $i + 1 ?></span>
            <button type="button" class="btn-remove-step" onclick="removeStep(this)" title="Supprimer">&times;</button>
          </div>
          <div class="step-fields">
            <div class="step-delay">
              <input type="number" class="step-delay-input" value="<?= (int) ($step['delay_days'] ?? 0) ?>" min="0">
              <span>jour(s) apr&egrave;s l'&eacute;tape pr&eacute;c&eacute;dente</span>
            </div>
            <input type="text" class="step-subject-input" value="<?= htmlspecialchars($step['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Objet de l'email">
            <textarea class="step-body-input" placeholder="Contenu HTML de l'email..."><?= htmlspecialchars($step['body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <button type="button" class="btn-add-step" onclick="addStep()">
    <i class="fas fa-plus"></i> Ajouter une &eacute;tape
  </button>
</div>

<script>
var neuropersonas = <?= json_encode($neuropersonas) ?>;

function onPersonaChange(persona) {
  var box = document.getElementById('suggestionBox');
  var list = document.getElementById('suggestionList');

  if (persona && neuropersonas[persona]) {
    var topics = neuropersonas[persona]['article_topics'] || [];
    list.innerHTML = '';
    topics.forEach(function(topic) {
      var li = document.createElement('li');
      li.innerHTML = '<i class="fas fa-circle"></i> ' + topic;
      list.appendChild(li);
    });
    box.classList.add('show');
  } else {
    box.classList.remove('show');
  }
}

function addStep() {
  var container = document.getElementById('stepsContainer');
  var count = container.querySelectorAll('.step-card').length;
  var card = document.createElement('div');
  card.className = 'step-card';
  card.innerHTML = '<div class="step-card-header">' +
    '<span class="step-number">' + (count + 1) + '</span>' +
    '<button type="button" class="btn-remove-step" onclick="removeStep(this)" title="Supprimer">&times;</button>' +
    '</div>' +
    '<div class="step-fields">' +
    '<div class="step-delay">' +
    '<input type="number" class="step-delay-input" value="' + (count === 0 ? '0' : '3') + '" min="0">' +
    '<span>jour(s) apr\u00e8s l\'\u00e9tape pr\u00e9c\u00e9dente</span>' +
    '</div>' +
    '<input type="text" class="step-subject-input" placeholder="Objet de l\'email">' +
    '<textarea class="step-body-input" placeholder="Contenu HTML de l\'email..."></textarea>' +
    '</div>';
  container.appendChild(card);
  renumberSteps();
}

function removeStep(btn) {
  btn.closest('.step-card').remove();
  renumberSteps();
}

function renumberSteps() {
  var cards = document.querySelectorAll('#stepsContainer .step-card');
  cards.forEach(function(card, i) {
    card.querySelector('.step-number').textContent = i + 1;
  });
}

// Before submit, gather steps into JSON
document.getElementById('seqForm').addEventListener('submit', function(e) {
  var cards = document.querySelectorAll('#stepsContainer .step-card');
  var steps = [];
  cards.forEach(function(card) {
    steps.push({
      delay_days: parseInt(card.querySelector('.step-delay-input').value) || 0,
      subject: card.querySelector('.step-subject-input').value,
      body_html: card.querySelector('.step-body-input').value,
    });
  });
  document.getElementById('stepsJsonInput').value = JSON.stringify(steps);
});

// Init suggestion on page load
var personaSelect = document.getElementById('personaSelect');
if (personaSelect.value) {
  onPersonaChange(personaSelect.value);
}
</script>
