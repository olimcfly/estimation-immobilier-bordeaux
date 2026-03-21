<style>
  .email-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .email-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .email-header h1 i { color: var(--admin-primary); }

  .btn-new-template {
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

  .btn-new-template:hover { background: #6b0f2d; color: #fff; }

  .email-tabs {
    display: flex;
    gap: 0;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--admin-border);
  }

  .email-tab {
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
    transition: all 0.15s;
  }

  .email-tab:hover { color: var(--admin-text); }
  .email-tab.active {
    color: var(--admin-primary);
    border-bottom-color: var(--admin-primary);
  }

  .email-panel { display: none; }
  .email-panel.active { display: block; }

  /* Template cards */
  .template-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .template-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    transition: box-shadow 0.15s;
  }

  .template-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  }

  .template-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.75rem;
  }

  .template-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--admin-text);
  }

  .template-card-category {
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  .cat-notification { background: rgba(59,130,246,0.1); color: #2563eb; }
  .cat-client { background: rgba(34,197,94,0.1); color: #16a34a; }
  .cat-sequence { background: rgba(168,85,247,0.1); color: #7c3aed; }
  .cat-marketing { background: rgba(245,158,11,0.1); color: #d97706; }

  .template-card-subject {
    font-size: 0.85rem;
    color: var(--admin-muted);
    margin-bottom: 0.75rem;
  }

  .template-card-actions {
    display: flex;
    gap: 0.5rem;
  }

  .template-card-actions a, .template-card-actions button {
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
    transition: all 0.15s;
  }

  .template-card-actions a:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
  }

  .template-card-actions .btn-del {
    color: var(--admin-danger);
    border-color: transparent;
  }

  .template-card-actions .btn-del:hover {
    background: #fef2f2;
    border-color: var(--admin-danger);
  }

  /* Sent emails log */
  .log-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .log-table thead {
    background: #f8fafc;
  }

  .log-table th {
    padding: 0.65rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid var(--admin-border);
  }

  .log-table td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--admin-text);
  }

  .log-status {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .log-status.sent { background: rgba(34,197,94,0.1); color: #16a34a; }
  .log-status.failed { background: rgba(239,68,68,0.1); color: #dc2626; }

  .flash-msg {
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .flash-msg.success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
  }

  .flash-msg.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
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
    display: block;
  }

  @media (max-width: 640px) {
    .template-grid { grid-template-columns: 1fr; }
  }
</style>

<?php
  $templates = $templates ?? [];
  $sentEmails = $sentEmails ?? [];
  $flash = $_SESSION['email_flash'] ?? null;
  unset($_SESSION['email_flash']);
?>

<!-- HEADER -->
<div class="email-header">
  <h1><i class="fas fa-envelope"></i> Gestion des Emails</h1>
  <a href="/admin/emails/edit" class="btn-new-template">
    <i class="fas fa-plus"></i> Nouveau template
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
<div class="email-tabs">
  <button class="email-tab active" onclick="switchEmailTab('templates', this)">
    <i class="fas fa-file-alt"></i> Templates (<?= count($templates) ?>)
  </button>
  <button class="email-tab" onclick="switchEmailTab('sent', this)">
    <i class="fas fa-paper-plane"></i> Emails envoy&eacute;s (<?= count($sentEmails) ?>)
  </button>
</div>

<!-- TEMPLATES PANEL -->
<div class="email-panel active" id="panel-templates">
  <?php if (empty($templates)): ?>
    <div class="empty-state">
      <i class="fas fa-envelope-open"></i>
      <p>Aucun template email cr&eacute;&eacute;.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">
        <a href="/admin/emails/edit" style="color:var(--admin-primary);">Cr&eacute;er votre premier template</a>
      </p>
    </div>
  <?php else: ?>
    <div class="template-grid">
      <?php foreach ($templates as $tpl): ?>
        <?php
          $catClass = match($tpl['category'] ?? 'notification') {
            'client' => 'cat-client',
            'sequence' => 'cat-sequence',
            'marketing' => 'cat-marketing',
            default => 'cat-notification',
          };
        ?>
        <div class="template-card">
          <div class="template-card-header">
            <span class="template-card-title"><?= htmlspecialchars($tpl['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <span class="template-card-category <?= $catClass ?>"><?= htmlspecialchars($tpl['category'] ?? 'notification', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="template-card-subject">
            <i class="fas fa-tag"></i> <?= htmlspecialchars($tpl['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="template-card-actions">
            <a href="/admin/emails/edit?id=<?= (int) $tpl['id'] ?>"><i class="fas fa-edit"></i> Modifier</a>
            <form method="post" action="/admin/emails/delete" style="display:inline;" onsubmit="return confirm('Supprimer ce template ?');">
              <input type="hidden" name="id" value="<?= (int) $tpl['id'] ?>">
              <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- SENT EMAILS PANEL -->
<div class="email-panel" id="panel-sent">
  <?php if (empty($sentEmails)): ?>
    <div class="empty-state">
      <i class="fas fa-paper-plane"></i>
      <p>Aucun email envoy&eacute; enregistr&eacute;.</p>
    </div>
  <?php else: ?>
    <div style="background:var(--admin-surface);border:1px solid var(--admin-border);border-radius:var(--admin-radius);overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="log-table">
          <thead>
            <tr>
              <th>Destinataire</th>
              <th>Sujet</th>
              <th>Statut</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sentEmails as $email): ?>
              <tr>
                <td><?= htmlspecialchars($email['recipient'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($email['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <span class="log-status <?= ($email['status'] ?? '') === 'sent' ? 'sent' : 'failed' ?>">
                    <i class="fas <?= ($email['status'] ?? '') === 'sent' ? 'fa-check' : 'fa-times' ?>"></i>
                    <?= ($email['status'] ?? '') === 'sent' ? 'Envoy&eacute;' : '&Eacute;chec' ?>
                  </span>
                </td>
                <td style="white-space:nowrap;"><?= htmlspecialchars($email['sent_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
function switchEmailTab(tab, btn) {
  document.querySelectorAll('.email-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.email-panel').forEach(function(p) { p.classList.remove('active'); });
  btn.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}
</script>
