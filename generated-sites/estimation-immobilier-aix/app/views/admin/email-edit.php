<style>
  .email-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .email-edit-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .email-edit-header h1 i { color: var(--admin-primary); }

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

  .edit-form-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    max-width: 800px;
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-group label {
    display: flex;
    align-items: center;
    justify-content: space-between;
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
    transition: border-color 0.15s;
  }

  .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px var(--admin-primary-light);
  }

  .form-group textarea {
    min-height: 200px;
    resize: vertical;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-ai {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.6rem;
    background: linear-gradient(135deg, #8B5CF6, #6366F1);
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: opacity 0.15s;
  }

  .btn-ai:hover { opacity: 0.85; }
  .btn-ai:disabled { opacity: 0.5; cursor: not-allowed; }

  .btn-ai i {
    font-size: 0.7rem;
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
    margin-top: 0.5rem;
  }

  .btn-save:hover { background: #6b0f2d; }

  .btn-test-send {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.5rem;
    background: var(--admin-info);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    margin-top: 0.5rem;
    margin-left: 0.5rem;
  }

  .btn-test-send:hover { opacity: 0.9; }

  .preview-section {
    margin-top: 2rem;
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    max-width: 800px;
  }

  .preview-header {
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .preview-body {
    padding: 1.5rem;
    font-size: 0.9rem;
    line-height: 1.6;
  }

  .test-send-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 200;
    justify-content: center;
    align-items: center;
  }

  .test-send-modal.open {
    display: flex;
  }

  .test-send-content {
    background: #fff;
    border-radius: var(--admin-radius);
    padding: 2rem;
    width: 400px;
    max-width: 90vw;
  }

  .test-send-content h3 {
    margin-bottom: 1rem;
    font-size: 1.1rem;
  }

  .test-send-content input {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    margin-bottom: 1rem;
  }

  .test-result {
    padding: 0.5rem 0.85rem;
    border-radius: 6px;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: none;
  }

  .test-result.show { display: block; }
  .test-result.success { background: #f0fdf4; color: #166534; }
  .test-result.error { background: #fef2f2; color: #991b1b; }

  @media (max-width: 640px) {
    .form-row { grid-template-columns: 1fr; }
  }
</style>

<?php
  $template = $template ?? null;
  $isEdit = $template !== null;
?>

<!-- HEADER -->
<div class="email-edit-header">
  <h1>
    <i class="fas fa-<?= $isEdit ? 'edit' : 'plus-circle' ?>"></i>
    <?= $isEdit ? 'Modifier le template' : 'Nouveau template email' ?>
  </h1>
  <a href="/admin/emails" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<!-- FORM -->
<div class="edit-form-card">
  <form method="post" action="/admin/emails/save" id="emailForm">
    <?php if ($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int) $template['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label>Nom du template</label>
        <input type="text" name="name" value="<?= htmlspecialchars($template['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Bienvenue nouveau lead" required>
      </div>
      <div class="form-group">
        <label>Cat&eacute;gorie</label>
        <select name="category">
          <option value="notification" <?= ($template['category'] ?? '') === 'notification' ? 'selected' : '' ?>>Notification (admin)</option>
          <option value="client" <?= ($template['category'] ?? '') === 'client' ? 'selected' : '' ?>>Client</option>
          <option value="sequence" <?= ($template['category'] ?? '') === 'sequence' ? 'selected' : '' ?>>S&eacute;quence</option>
          <option value="marketing" <?= ($template['category'] ?? '') === 'marketing' ? 'selected' : '' ?>>Marketing</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Slug (identifiant unique)</label>
      <input type="text" name="slug" value="<?= htmlspecialchars($template['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="bienvenue-nouveau-lead">
    </div>

    <div class="form-group">
      <label>
        Objet de l'email
        <button type="button" class="btn-ai" onclick="aiGenerate('subject')">
          <i class="fas fa-robot"></i> AI
        </button>
      </label>
      <input type="text" name="subject" id="field-subject" value="<?= htmlspecialchars($template['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Objet de l'email" required>
    </div>

    <div class="form-group">
      <label>
        Contenu HTML
        <button type="button" class="btn-ai" onclick="aiGenerate('body')">
          <i class="fas fa-robot"></i> AI
        </button>
      </label>
      <textarea name="body_html" id="field-body" placeholder="<p>Bonjour {{nom}},</p>&#10;<p>Votre contenu ici...</p>"><?= htmlspecialchars($template['body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      <div style="font-size:0.75rem;color:var(--admin-muted);margin-top:0.3rem;">
        Variables disponibles : <code>{{nom}}</code>, <code>{{email}}</code>, <code>{{estimation}}</code>, <code>{{ville}}</code>, <code>{{type_bien}}</code>
      </div>
    </div>

    <div class="form-group">
      <label>
        Signature
        <button type="button" class="btn-ai" onclick="aiGenerate('signature')">
          <i class="fas fa-robot"></i> AI
        </button>
      </label>
      <textarea name="signature" id="field-signature" style="min-height:80px;" placeholder="Cordialement,&#10;Votre &eacute;quipe Estimation Immobilier Aix-en-Provence"><?= htmlspecialchars($template['signature'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <button type="submit" class="btn-save"><i class="fas fa-save"></i> Sauvegarder</button>
      <button type="button" class="btn-test-send" onclick="openTestSend()"><i class="fas fa-paper-plane"></i> Envoyer un test</button>
    </div>
  </form>
</div>

<!-- PREVIEW -->
<div class="preview-section">
  <div class="preview-header"><i class="fas fa-eye"></i> Aper&ccedil;u</div>
  <div class="preview-body" id="emailPreview">
    <p style="color:var(--admin-muted);font-style:italic;">L'aper&ccedil;u apparait ici quand vous &eacute;crivez le contenu...</p>
  </div>
</div>

<!-- TEST SEND MODAL -->
<div class="test-send-modal" id="testSendModal">
  <div class="test-send-content">
    <h3><i class="fas fa-paper-plane"></i> Envoyer un email test</h3>
    <input type="email" id="testRecipient" placeholder="Adresse email de test" required>
    <div style="display:flex;gap:0.5rem;">
      <button onclick="sendTestEmail()" class="btn-save" style="margin:0;"><i class="fas fa-send"></i> Envoyer</button>
      <button onclick="closeTestSend()" class="btn-back" style="cursor:pointer;border:1px solid var(--admin-border);">Annuler</button>
    </div>
    <div class="test-result" id="testResult"></div>
  </div>
</div>

<script>
// Live preview
var bodyField = document.getElementById('field-body');
var sigField = document.getElementById('field-signature');
var preview = document.getElementById('emailPreview');

function updatePreview() {
  var html = bodyField.value || '';
  var sig = sigField.value || '';
  if (sig) {
    html += '<br><br><div style="border-top:1px solid #e2e8f0;padding-top:1rem;margin-top:1rem;color:#64748b;font-size:0.85rem;">' + sig.replace(/\n/g, '<br>') + '</div>';
  }
  preview.innerHTML = html || '<p style="color:var(--admin-muted);font-style:italic;">L\'aper\u00e7u apparait ici...</p>';
}

bodyField.addEventListener('input', updatePreview);
sigField.addEventListener('input', updatePreview);
updatePreview();

// AI generate
function aiGenerate(field) {
  var btn = event.target.closest('.btn-ai');
  var input = document.getElementById('field-' + field);
  if (!input) return;

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

  var formData = new FormData();
  formData.append('field', field);
  formData.append('context', document.querySelector('[name="name"]').value || 'email immobilier');
  formData.append('current_value', input.value);

  fetch('/admin/emails/ai-generate', {
    method: 'POST',
    body: formData,
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success && data.content) {
      if (input.tagName === 'TEXTAREA') {
        input.value = data.content;
      } else {
        input.value = data.content;
      }
      updatePreview();
    } else {
      alert(data.message || 'Erreur AI');
    }
  })
  .catch(function(err) {
    alert('Erreur: ' + err.message);
  })
  .finally(function() {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-robot"></i> AI';
  });
}

// Test send
function openTestSend() {
  document.getElementById('testSendModal').classList.add('open');
}

function closeTestSend() {
  document.getElementById('testSendModal').classList.remove('open');
}

function sendTestEmail() {
  var to = document.getElementById('testRecipient').value;
  if (!to) return;

  var formData = new FormData();
  formData.append('to', to);
  formData.append('subject', document.getElementById('field-subject').value);
  formData.append('body', document.getElementById('field-body').value);
  formData.append('signature', document.getElementById('field-signature').value);

  var result = document.getElementById('testResult');
  result.className = 'test-result show';
  result.textContent = 'Envoi en cours...';
  result.style.background = '#f0f9ff';
  result.style.color = '#1e40af';

  fetch('/admin/emails/send-test', {
    method: 'POST',
    body: formData,
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    result.className = 'test-result show ' + (data.success ? 'success' : 'error');
    result.textContent = data.message;
  })
  .catch(function(err) {
    result.className = 'test-result show error';
    result.textContent = 'Erreur: ' + err.message;
  });
}

// Close modal on overlay click
document.getElementById('testSendModal').addEventListener('click', function(e) {
  if (e.target === this) closeTestSend();
});
</script>
