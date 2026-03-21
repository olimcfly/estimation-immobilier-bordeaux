<style>
  .smtp-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .smtp-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .smtp-header h1 i { color: var(--admin-primary); }

  .smtp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 900px) {
    .smtp-grid { grid-template-columns: 1fr; }
  }

  .smtp-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
  }

  .smtp-card.full-width {
    grid-column: 1 / -1;
  }

  .smtp-card h2 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .smtp-card h2 i { color: var(--admin-primary); font-size: 0.95rem; }

  .form-group {
    margin-bottom: 1rem;
  }

  .form-group:last-child {
    margin-bottom: 0;
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

  .form-group input,
  .form-group select {
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

  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(139,21,56,0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  @media (max-width: 640px) {
    .form-row { grid-template-columns: 1fr; }
  }

  .form-hint {
    font-size: 0.78rem;
    color: var(--admin-muted);
    margin-top: 0.25rem;
  }

  .btn-primary {
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
    transition: background 0.15s;
  }

  .btn-primary:hover { background: #6b0f2d; }
  .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

  .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.5rem;
    background: #fff;
    color: var(--admin-text);
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }

  .btn-secondary:hover { background: var(--admin-bg); border-color: var(--admin-muted); }

  .btn-success {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.5rem;
    background: var(--admin-success);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-success:hover { background: #16a34a; }
  .btn-success:disabled { opacity: 0.6; cursor: not-allowed; }

  .btn-info {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: var(--admin-info);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-info:hover { background: #2563eb; }
  .btn-info:disabled { opacity: 0.6; cursor: not-allowed; }

  .btn-danger-outline {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: transparent;
    color: var(--admin-danger);
    border: 1px solid var(--admin-danger);
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }

  .btn-danger-outline:hover { background: #fef2f2; }

  .btn-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 1.25rem;
  }

  .smtp-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
  }

  .smtp-status-badge.configured {
    background: #dcfce7;
    color: #166534;
  }

  .smtp-status-badge.not-configured {
    background: #fee2e2;
    color: #991b1b;
  }

  .smtp-status-badge.override {
    background: #dbeafe;
    color: #1e40af;
  }

  .flash-message {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 500;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .flash-message.success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
  }

  .flash-message.error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
  }

  .test-results {
    margin-top: 1.25rem;
  }

  .test-step {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--admin-border);
  }

  .test-step:last-child { border-bottom: none; }

  .step-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
  }

  .step-icon.ok { background: #dcfce7; color: #16a34a; }
  .step-icon.error { background: #fee2e2; color: #dc2626; }
  .step-icon.pending { background: #f1f5f9; color: #94a3b8; }

  .step-content { flex: 1; min-width: 0; }

  .step-label {
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--admin-text);
    margin-bottom: 0.15rem;
  }

  .step-detail {
    font-size: 0.82rem;
    color: var(--admin-muted);
    word-break: break-word;
  }

  .step-diagnostics {
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fef2f2;
    border-radius: 4px;
    font-size: 0.82rem;
    color: #991b1b;
  }

  .step-advice {
    margin-top: 0.4rem;
    padding: 0.5rem 0.75rem;
    background: #fefce8;
    border-radius: 4px;
    font-size: 0.82rem;
    color: #854d0e;
  }

  .send-form {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
  }

  .send-form .form-group {
    flex: 1;
    margin-bottom: 0;
  }

  .send-result {
    margin-top: 0.75rem;
    padding: 0.6rem 0.85rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    display: none;
  }

  .send-result.success {
    background: #dcfce7;
    color: #166534;
    display: block;
  }

  .send-result.error {
    background: #fee2e2;
    color: #991b1b;
    display: block;
  }

  .env-hint {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    color: var(--admin-muted);
    background: var(--admin-bg);
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    margin-left: 0.5rem;
    font-weight: 400;
    text-transform: none;
    letter-spacing: 0;
  }

  .source-info {
    font-size: 0.8rem;
    color: var(--admin-muted);
    margin-bottom: 1rem;
    padding: 0.6rem 0.85rem;
    background: var(--admin-bg);
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .source-info i { color: var(--admin-info); }
</style>

<?php if ($flash_success): ?>
  <div class="flash-message success">
    <i class="fas fa-check-circle"></i>
    <?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<?php if ($flash_error): ?>
  <div class="flash-message error">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<div class="smtp-header">
  <h1><i class="fas fa-envelope-open-text"></i> Configuration SMTP</h1>
  <div style="display: flex; gap: 0.5rem; align-items: center;">
    <?php if ($smtp_host !== ''): ?>
      <span class="smtp-status-badge configured"><i class="fas fa-check-circle"></i> Configure</span>
    <?php else: ?>
      <span class="smtp-status-badge not-configured"><i class="fas fa-times-circle"></i> Non configure</span>
    <?php endif; ?>
    <?php if ($has_overrides): ?>
      <span class="smtp-status-badge override"><i class="fas fa-pen"></i> Personnalise</span>
    <?php endif; ?>
  </div>
</div>

<div class="source-info">
  <i class="fas fa-info-circle"></i>
  <?php if ($has_overrides): ?>
    Les valeurs ci-dessous proviennent de votre configuration personnalisee (admin).
    Vous pouvez reinitialiser pour revenir aux valeurs du fichier <code>.env</code>.
  <?php else: ?>
    Les valeurs ci-dessous proviennent du fichier <code>.env</code>.
    Modifiez-les et sauvegardez pour personnaliser la configuration SMTP du site.
  <?php endif; ?>
</div>

<form id="smtpConfigForm" method="POST" action="/admin/test-smtp/save">
  <div class="smtp-grid">
    <!-- SMTP Server Config Card -->
    <div class="smtp-card">
      <h2><i class="fas fa-server"></i> Serveur SMTP</h2>

      <div class="form-group">
        <label for="smtp_host">Host SMTP <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_HOST</span></label>
        <input type="text" id="smtp_host" name="smtp_host"
               value="<?= htmlspecialchars($smtp_host, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="mail.example.com" />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="smtp_port">Port <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_PORT</span></label>
          <input type="number" id="smtp_port" name="smtp_port"
                 value="<?= (int) $smtp_port ?>"
                 placeholder="465" />
          <div class="form-hint">465 (SSL) ou 587 (TLS)</div>
        </div>

        <div class="form-group">
          <label for="smtp_encryption">Encryption <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_ENCRYPTION</span></label>
          <select id="smtp_encryption" name="smtp_encryption">
            <option value="ssl" <?= $smtp_enc === 'ssl' ? 'selected' : '' ?>>SSL</option>
            <option value="tls" <?= $smtp_enc === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="smtp_user">Utilisateur SMTP <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_USERNAME</span></label>
        <input type="text" id="smtp_user" name="smtp_user"
               value="<?= htmlspecialchars($smtp_user, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="contact@example.com" />
      </div>

      <div class="form-group">
        <label for="smtp_pass">Mot de passe SMTP <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_PASSWORD</span></label>
        <input type="password" id="smtp_pass" name="smtp_pass"
               value="<?= $smtp_pass !== '' ? '********' : '' ?>"
               placeholder="Mot de passe SMTP" />
        <div class="form-hint">Laissez ******** pour garder le mot de passe actuel</div>
      </div>
    </div>

    <!-- Sender Config Card -->
    <div class="smtp-card">
      <h2><i class="fas fa-user-edit"></i> Expediteur</h2>

      <div class="form-group">
        <label for="mail_from">Email expediteur <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_FROM</span></label>
        <input type="email" id="mail_from" name="mail_from"
               value="<?= htmlspecialchars($mail_from, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="contact@example.com" />
        <div class="form-hint">L'adresse qui apparaitra comme expediteur</div>
      </div>

      <div class="form-group">
        <label for="mail_from_name">Nom expediteur <span class="env-hint"><i class="fas fa-file-alt"></i> MAIL_FROM_NAME</span></label>
        <input type="text" id="mail_from_name" name="mail_from_name"
               value="<?= htmlspecialchars($mail_from_name, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Estimation Immobilier Lannion" />
      </div>

      <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--admin-border);">
        <h2 style="margin-bottom: 0.75rem;"><i class="fas fa-plug"></i> Test de connexion</h2>
        <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 1rem;">
          Teste la connexion au serveur SMTP avec les valeurs du formulaire ci-dessus, sans envoyer de message.
        </p>
        <button type="button" class="btn-primary" id="btnRunTest" onclick="runSmtpTest()">
          <i class="fas fa-play"></i> Tester la connexion
        </button>

        <div class="test-results" id="testResults" style="display:none;"></div>
      </div>
    </div>
  </div>

  <!-- Action buttons -->
  <div class="smtp-card" style="max-width: 100%;">
    <div class="btn-group" style="margin-top: 0;">
      <button type="submit" class="btn-success" id="btnSave">
        <i class="fas fa-save"></i> Sauvegarder la configuration
      </button>
      <button type="button" class="btn-secondary" onclick="loadFromEnv()">
        <i class="fas fa-file-import"></i> Charger depuis .env
      </button>
      <?php if ($has_overrides): ?>
        <button type="button" class="btn-danger-outline" onclick="resetSmtpConfig()">
          <i class="fas fa-undo"></i> Reinitialiser (.env)
        </button>
      <?php endif; ?>
    </div>
  </div>
</form>

<!-- Send Test Email Card -->
<div class="smtp-card" style="max-width: 700px; margin-top: 1.5rem;">
  <h2><i class="fas fa-paper-plane"></i> Envoyer un email de test</h2>
  <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 0.75rem;">
    Envoyez un email de test avec la configuration <strong>sauvegardee</strong> pour verifier que tout fonctionne de bout en bout.
  </p>
  <div class="send-form">
    <div class="form-group">
      <label for="testEmail">Adresse email destinataire</label>
      <input type="email" id="testEmail" placeholder="votre@email.com" />
    </div>
    <button type="button" class="btn-info" id="btnSendTest" onclick="sendTestEmail()">
      <i class="fas fa-paper-plane"></i> Envoyer
    </button>
  </div>
  <div class="send-result" id="sendResult"></div>
</div>

<script>
function getFormValues() {
  return {
    smtp_host: document.getElementById('smtp_host').value.trim(),
    smtp_port: document.getElementById('smtp_port').value.trim(),
    smtp_user: document.getElementById('smtp_user').value.trim(),
    smtp_pass: document.getElementById('smtp_pass').value,
    smtp_encryption: document.getElementById('smtp_encryption').value,
  };
}

function runSmtpTest() {
  var btn = document.getElementById('btnRunTest');
  var results = document.getElementById('testResults');
  var values = getFormValues();

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test en cours...';
  results.style.display = 'block';
  results.innerHTML = '<div class="test-step"><div class="step-icon pending"><i class="fas fa-spinner fa-spin"></i></div><div class="step-content"><div class="step-label">Connexion en cours...</div></div></div>';

  var formData = new FormData();
  formData.append('smtp_host', values.smtp_host);
  formData.append('smtp_port', values.smtp_port);
  formData.append('smtp_user', values.smtp_user);
  formData.append('smtp_pass', values.smtp_pass);
  formData.append('smtp_encryption', values.smtp_encryption);

  fetch('/admin/test-smtp/run', {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var html = '';
      data.steps.forEach(function(step) {
        var iconClass = step.status === 'ok' ? 'ok' : 'error';
        var icon = step.status === 'ok' ? 'fa-check' : 'fa-times';
        html += '<div class="test-step">';
        html += '  <div class="step-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>';
        html += '  <div class="step-content">';
        html += '    <div class="step-label">' + escHtml(step.label) + '</div>';
        html += '    <div class="step-detail">' + escHtml(step.detail) + '</div>';
        if (step.diagnostics && step.diagnostics.length > 0) {
          html += '    <div class="step-diagnostics">' + step.diagnostics.map(escHtml).join('<br>') + '</div>';
        }
        if (step.advice) {
          html += '    <div class="step-advice"><i class="fas fa-lightbulb"></i> ' + escHtml(step.advice) + '</div>';
        }
        html += '  </div>';
        html += '</div>';
      });
      results.innerHTML = html;
    })
    .catch(function(err) {
      results.innerHTML = '<div class="test-step"><div class="step-icon error"><i class="fas fa-times"></i></div><div class="step-content"><div class="step-label">Erreur reseau</div><div class="step-detail">' + escHtml(err.message) + '</div></div></div>';
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-play"></i> Tester la connexion';
    });
}

function sendTestEmail() {
  var btn = document.getElementById('btnSendTest');
  var email = document.getElementById('testEmail').value.trim();
  var result = document.getElementById('sendResult');

  if (!email) {
    result.className = 'send-result error';
    result.textContent = 'Veuillez saisir une adresse email.';
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
  result.className = 'send-result';
  result.style.display = 'none';

  var formData = new FormData();
  formData.append('to', email);

  fetch('/admin/test-smtp/send', {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        result.className = 'send-result success';
        result.innerHTML = '<i class="fas fa-check-circle"></i> Email envoye avec succes a ' + escHtml(email) + ' ! La configuration SMTP est operationnelle.';
      } else {
        result.className = 'send-result error';
        result.innerHTML = '<i class="fas fa-times-circle"></i> ' + escHtml(data.error || 'Echec de l\'envoi.');
      }
    })
    .catch(function(err) {
      result.className = 'send-result error';
      result.textContent = 'Erreur reseau : ' + err.message;
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    });
}

function loadFromEnv() {
  if (!confirm('Recharger les valeurs depuis le fichier .env ? Les modifications non sauvegardees seront perdues.')) {
    return;
  }
  window.location.href = '/admin/test-smtp?from_env=1';
}

function resetSmtpConfig() {
  if (!confirm('Reinitialiser la configuration SMTP aux valeurs du fichier .env ?')) {
    return;
  }
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '/admin/test-smtp/reset';
  document.body.appendChild(form);
  form.submit();
}

function escHtml(str) {
  var d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}
</script>
