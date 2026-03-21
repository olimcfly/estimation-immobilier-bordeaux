<style>
  .api-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .api-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .api-header h1 i {
    color: var(--admin-primary);
  }

  .api-summary {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .api-summary-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
  }

  .api-summary-badge.active {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
  }

  .api-summary-badge.inactive {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
  }

  .api-summary-badge.total {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
  }

  .api-category {
    margin-bottom: 2rem;
  }

  .api-category-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #6b6459;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e8dfd7;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .api-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.25rem;
  }

  @media (max-width: 750px) {
    .api-grid {
      grid-template-columns: 1fr;
    }
  }

  .api-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    overflow: hidden;
    transition: box-shadow 0.2s, border-color 0.2s;
  }

  .api-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  }

  .api-card-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #f1f0ed;
  }

  .api-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #fff;
    flex-shrink: 0;
  }

  .api-card-info {
    flex: 1;
    min-width: 0;
  }

  .api-card-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a1410;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .api-card-desc {
    font-size: 0.78rem;
    color: #6b6459;
    margin-top: 0.15rem;
  }

  .api-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .api-status-dot.configured {
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
  }

  .api-status-dot.not-configured {
    background: #d1d5db;
    box-shadow: 0 0 0 3px rgba(209, 213, 219, 0.3);
  }

  .api-card-body {
    padding: 1rem 1.25rem;
  }

  .api-config-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.4rem 0;
    font-size: 0.82rem;
    border-bottom: 1px solid #f8f7f5;
  }

  .api-config-row:last-child {
    border-bottom: none;
  }

  .api-config-label {
    color: #6b6459;
    font-weight: 500;
    font-family: 'DM Sans', monospace;
    font-size: 0.78rem;
  }

  .api-config-value {
    font-weight: 600;
    font-size: 0.8rem;
  }

  .api-config-value.set {
    color: #16a34a;
  }

  .api-config-value.unset {
    color: #d97706;
  }

  .api-pricing {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.75rem;
    padding: 0.6rem 0.8rem;
    background: #faf9f7;
    border-radius: 6px;
    font-size: 0.78rem;
    color: #6b6459;
  }

  .api-pricing i {
    color: var(--admin-accent);
    font-size: 0.75rem;
  }

  .api-pricing a {
    color: var(--admin-primary);
    text-decoration: none;
    font-weight: 600;
    margin-left: auto;
  }

  .api-pricing a:hover {
    text-decoration: underline;
  }

  .api-card-footer {
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 1.25rem;
    border-top: 1px solid #f1f0ed;
    background: #fafbfc;
  }

  .btn-test {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
    flex: 1;
    justify-content: center;
  }

  .btn-test:hover {
    background: #6b0f2d;
  }

  .btn-test:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-test.testing {
    background: #6b6459;
  }

  .btn-configure {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    background: #fff;
    color: #6b6459;
    border: 1px solid #e8dfd7;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
    flex: 1;
    justify-content: center;
  }

  .btn-configure:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
  }

  .api-test-result {
    margin-top: 0.75rem;
    padding: 0.75rem 0.9rem;
    border-radius: 6px;
    font-size: 0.82rem;
    display: none;
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .api-test-result.success {
    background: rgba(34, 197, 94, 0.08);
    border: 1px solid rgba(34, 197, 94, 0.2);
    color: #15803d;
    display: block;
  }

  .api-test-result.error {
    background: rgba(239, 68, 68, 0.06);
    border: 1px solid rgba(239, 68, 68, 0.15);
    color: #dc2626;
    display: block;
  }

  .api-test-result.loading {
    background: rgba(59, 130, 246, 0.06);
    border: 1px solid rgba(59, 130, 246, 0.15);
    color: #2563eb;
    display: block;
  }

  .api-test-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.35rem;
    font-size: 0.78rem;
    opacity: 0.85;
  }

  .api-test-detail .latency {
    background: rgba(0,0,0,0.06);
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.72rem;
  }

  /* Configure Modal */
  .api-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  .api-modal-overlay.open {
    display: flex;
  }

  .api-modal {
    background: #fff;
    border-radius: 12px;
    width: min(500px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  }

  .api-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e8dfd7;
  }

  .api-modal-header h3 {
    font-size: 1.05rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .api-modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #6b6459;
    padding: 0.25rem;
  }

  .api-modal-close:hover {
    color: #1a1410;
  }

  .api-modal-body {
    padding: 1.5rem;
  }

  .api-modal-field {
    margin-bottom: 1rem;
  }

  .api-modal-field label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1a1410;
    margin-bottom: 0.35rem;
  }

  .api-modal-field input {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border: 1px solid #e8dfd7;
    border-radius: 6px;
    font-size: 0.88rem;
    font-family: 'DM Sans', monospace;
    transition: border-color 0.15s;
    box-sizing: border-box;
  }

  .api-modal-field input:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.08);
  }

  .api-modal-footer {
    display: flex;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e8dfd7;
    background: #fafbfc;
    border-radius: 0 0 12px 12px;
  }

  .btn-save {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.2rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-save:hover {
    background: #6b0f2d;
  }

  .btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.2rem;
    background: #fff;
    color: #6b6459;
    border: 1px solid #e8dfd7;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }

  .btn-cancel:hover {
    border-color: #1a1410;
    color: #1a1410;
  }

  .api-modal-msg {
    padding: 0.6rem 0.85rem;
    border-radius: 6px;
    font-size: 0.82rem;
    margin-bottom: 1rem;
    display: none;
  }

  .api-modal-msg.success {
    background: rgba(34, 197, 94, 0.08);
    border: 1px solid rgba(34, 197, 94, 0.2);
    color: #15803d;
    display: block;
  }

  .api-modal-msg.error {
    background: rgba(239, 68, 68, 0.06);
    border: 1px solid rgba(239, 68, 68, 0.15);
    color: #dc2626;
    display: block;
  }
</style>

<?php
  $configuredCount = 0;
  $totalCount = count($apis);
  foreach ($apis as $api) {
    if ($api['configured']) $configuredCount++;
  }
  $notConfiguredCount = $totalCount - $configuredCount;

  $categories = [
    'ia' => ['label' => 'Intelligence Artificielle', 'icon' => 'fa-brain'],
    'geo' => ['label' => 'Geolocalisation & Donnees', 'icon' => 'fa-map'],
    'comm' => ['label' => 'Communication', 'icon' => 'fa-comments'],
    'data' => ['label' => 'Donnees Publiques', 'icon' => 'fa-database'],
  ];
?>

<div class="api-header">
  <h1><i class="fas fa-plug"></i> Gestion des API</h1>
  <div class="api-summary">
    <span class="api-summary-badge total">
      <i class="fas fa-circle-nodes"></i> <?= $totalCount ?> API
    </span>
    <span class="api-summary-badge active">
      <i class="fas fa-check-circle"></i> <?= $configuredCount ?> active(s)
    </span>
    <?php if ($notConfiguredCount > 0): ?>
    <span class="api-summary-badge inactive">
      <i class="fas fa-exclamation-circle"></i> <?= $notConfiguredCount ?> non configur&eacute;e(s)
    </span>
    <?php endif; ?>
  </div>
</div>

<?php foreach ($categories as $catKey => $catInfo): ?>
  <?php
    $catApis = array_filter($apis, fn($a) => ($a['category'] ?? '') === $catKey);
    if (empty($catApis)) continue;
  ?>
  <div class="api-category">
    <div class="api-category-title">
      <i class="fas <?= $catInfo['icon'] ?>"></i> <?= $catInfo['label'] ?>
    </div>
    <div class="api-grid">
      <?php foreach ($catApis as $apiKey => $api): ?>
        <div class="api-card" id="card-<?= $apiKey ?>">
          <div class="api-card-header">
            <div class="api-card-icon" style="background: <?= htmlspecialchars($api['color'], ENT_QUOTES, 'UTF-8') ?>;">
              <i class="fas <?= htmlspecialchars($api['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
            </div>
            <div class="api-card-info">
              <div class="api-card-name">
                <?= htmlspecialchars($api['name'], ENT_QUOTES, 'UTF-8') ?>
                <span class="api-status-dot <?= $api['configured'] ? 'configured' : 'not-configured' ?>" title="<?= $api['configured'] ? 'Configuree' : 'Non configuree' ?>"></span>
              </div>
              <div class="api-card-desc"><?= htmlspecialchars($api['description'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <div class="api-card-body">
            <?php foreach ($api['env_keys'] as $envKey): ?>
              <div class="api-config-row">
                <span class="api-config-label"><?= htmlspecialchars($envKey, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="api-config-value <?= ($_ENV[$envKey] ?? '') !== '' ? 'set' : 'unset' ?>">
                  <?php if (($_ENV[$envKey] ?? '') !== ''): ?>
                    <?php if (stripos($envKey, 'KEY') !== false || stripos($envKey, 'TOKEN') !== false || stripos($envKey, 'PASS') !== false || stripos($envKey, 'SECRET') !== false || stripos($envKey, 'SID') !== false): ?>
                      <?= htmlspecialchars(substr((string)$_ENV[$envKey], 0, 6), ENT_QUOTES, 'UTF-8') ?>...
                    <?php else: ?>
                      <?= htmlspecialchars((string)$_ENV[$envKey], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                  <?php else: ?>
                    Non d&eacute;fini
                  <?php endif; ?>
                </span>
              </div>
            <?php endforeach; ?>

            <?php if (empty($api['env_keys'])): ?>
              <div class="api-config-row">
                <span class="api-config-label">Configuration</span>
                <span class="api-config-value set">Aucune cl&eacute; requise</span>
              </div>
            <?php endif; ?>

            <div class="api-pricing">
              <i class="fas fa-coins"></i>
              <span><?= htmlspecialchars($api['pricing_info'], ENT_QUOTES, 'UTF-8') ?></span>
              <a href="<?= htmlspecialchars($api['pricing_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
                Tarifs <i class="fas fa-external-link-alt" style="font-size: 0.65rem;"></i>
              </a>
            </div>

            <div class="api-test-result" id="result-<?= $apiKey ?>"></div>
          </div>

          <div class="api-card-footer">
            <button class="btn-test" onclick="testApi('<?= $apiKey ?>')" id="btn-test-<?= $apiKey ?>">
              <i class="fas fa-bolt"></i> Tester
            </button>
            <?php if (!empty($api['env_keys'])): ?>
            <button class="btn-configure" onclick="openConfigModal('<?= $apiKey ?>')">
              <i class="fas fa-gear"></i> Configurer
            </button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>

<!-- Configure Modal -->
<div class="api-modal-overlay" id="config-modal">
  <div class="api-modal">
    <div class="api-modal-header">
      <h3><i class="fas fa-gear"></i> <span id="modal-title">Configurer</span></h3>
      <button class="api-modal-close" onclick="closeConfigModal()">&times;</button>
    </div>
    <form id="config-form" onsubmit="saveConfig(event)">
      <div class="api-modal-body">
        <div class="api-modal-msg" id="modal-msg"></div>
        <input type="hidden" name="api_name" id="modal-api-name">
        <div id="modal-fields"></div>
      </div>
      <div class="api-modal-footer">
        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Sauvegarder</button>
        <button type="button" class="btn-cancel" onclick="closeConfigModal()">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script>
var apiEnvKeys = <?= json_encode(
  array_map(fn($a) => $a['env_keys'], $apis),
  JSON_UNESCAPED_UNICODE
) ?>;

var apiNames = <?= json_encode(
  array_map(fn($a) => $a['name'], $apis),
  JSON_UNESCAPED_UNICODE
) ?>;

function testApi(key) {
  var btn = document.getElementById('btn-test-' + key);
  var result = document.getElementById('result-' + key);

  btn.disabled = true;
  btn.classList.add('testing');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test en cours...';

  result.className = 'api-test-result loading';
  result.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test de connexion en cours...';
  result.style.display = 'block';

  fetch('/admin/api/test/' + key, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    btn.disabled = false;
    btn.classList.remove('testing');
    btn.innerHTML = '<i class="fas fa-bolt"></i> Tester';

    if (data.success) {
      var html = '<i class="fas fa-check-circle"></i> ' + escHtml(data.message || 'OK');
      if (data.response) {
        html += '<div class="api-test-detail">' + escHtml(data.response) + '</div>';
      }
      if (data.latency_ms) {
        html += '<div class="api-test-detail"><span class="latency">' + data.latency_ms + ' ms</span>';
        if (data.model) {
          html += ' &middot; Modele: ' + escHtml(data.model);
        }
        if (data.usage) {
          if (data.usage.total_tokens) {
            html += ' &middot; Tokens: ' + data.usage.total_tokens;
          }
          if (data.usage.credits !== undefined) {
            html += ' &middot; Credits: ' + data.usage.credits;
          }
        }
        html += '</div>';
      }
      result.className = 'api-test-result success';
      result.innerHTML = html;
    } else {
      var html = '<i class="fas fa-times-circle"></i> ' + escHtml(data.error || 'Erreur');
      if (data.latency_ms) {
        html += '<div class="api-test-detail"><span class="latency">' + data.latency_ms + ' ms</span></div>';
      }
      result.className = 'api-test-result error';
      result.innerHTML = html;
    }
  })
  .catch(function(err) {
    btn.disabled = false;
    btn.classList.remove('testing');
    btn.innerHTML = '<i class="fas fa-bolt"></i> Tester';
    result.className = 'api-test-result error';
    result.innerHTML = '<i class="fas fa-times-circle"></i> Erreur reseau: ' + escHtml(err.message);
  });
}

function openConfigModal(key) {
  var modal = document.getElementById('config-modal');
  var title = document.getElementById('modal-title');
  var apiName = document.getElementById('modal-api-name');
  var fieldsContainer = document.getElementById('modal-fields');
  var msg = document.getElementById('modal-msg');

  title.textContent = 'Configurer ' + (apiNames[key] || key);
  apiName.value = key;
  msg.style.display = 'none';
  msg.className = 'api-modal-msg';
  fieldsContainer.innerHTML = '';

  var keys = apiEnvKeys[key] || [];
  for (var i = 0; i < keys.length; i++) {
    var envKey = keys[i];
    var div = document.createElement('div');
    div.className = 'api-modal-field';

    var label = document.createElement('label');
    label.textContent = envKey;
    label.setAttribute('for', 'field-' + envKey);

    var input = document.createElement('input');
    input.type = (envKey.indexOf('KEY') !== -1 || envKey.indexOf('TOKEN') !== -1 || envKey.indexOf('PASS') !== -1 || envKey.indexOf('SECRET') !== -1) ? 'password' : 'text';
    input.name = 'fields[' + envKey + ']';
    input.id = 'field-' + envKey;
    input.placeholder = envKey;
    input.autocomplete = 'off';

    div.appendChild(label);
    div.appendChild(input);
    fieldsContainer.appendChild(div);
  }

  modal.classList.add('open');
}

function closeConfigModal() {
  document.getElementById('config-modal').classList.remove('open');
}

function saveConfig(e) {
  e.preventDefault();
  var form = document.getElementById('config-form');
  var msg = document.getElementById('modal-msg');
  var data = new FormData(form);

  fetch('/admin/api/save-keys', {
    method: 'POST',
    body: data,
  })
  .then(function(r) { return r.json(); })
  .then(function(result) {
    if (result.success) {
      msg.className = 'api-modal-msg success';
      msg.textContent = result.message || 'Configuration sauvegardee !';
      msg.style.display = 'block';
      setTimeout(function() { location.reload(); }, 1200);
    } else {
      msg.className = 'api-modal-msg error';
      msg.textContent = result.error || 'Erreur lors de la sauvegarde';
      msg.style.display = 'block';
    }
  })
  .catch(function(err) {
    msg.className = 'api-modal-msg error';
    msg.textContent = 'Erreur reseau: ' + err.message;
    msg.style.display = 'block';
  });
}

function escHtml(str) {
  var div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// Close modal on overlay click
document.getElementById('config-modal').addEventListener('click', function(e) {
  if (e.target === this) closeConfigModal();
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeConfigModal();
});
</script>
