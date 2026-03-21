<style>
  .diag-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .diag-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .diag-header h1 i {
    color: var(--admin-primary);
  }

  .diag-actions {
    display: flex;
    gap: 0.75rem;
  }

  .btn-refresh {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.2rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-refresh:hover {
    background: #6b0f2d;
    color: #fff;
  }

  .btn-smtp {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.2rem;
    background: var(--admin-surface);
    color: var(--admin-text);
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: all 0.15s;
  }

  .btn-smtp:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
  }

  .diag-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 900px) {
    .diag-grid {
      grid-template-columns: 1fr;
    }
  }

  .diag-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .diag-card-full {
    grid-column: 1 / -1;
  }

  .diag-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    background: #fafbfc;
  }

  .diag-card-header h3 {
    font-size: 0.95rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .diag-card-header h3 i {
    font-size: 0.9rem;
    color: var(--admin-muted);
  }

  .diag-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.25rem 0.7rem;
    border-radius: 20px;
  }

  .diag-status-ok {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
  }

  .diag-status-error {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
  }

  .diag-status-warn {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
  }

  .diag-card-body {
    padding: 1.25rem;
  }

  .diag-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.55rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.88rem;
  }

  .diag-row:last-child {
    border-bottom: none;
  }

  .diag-row-label {
    color: var(--admin-muted);
    font-weight: 500;
  }

  .diag-row-value {
    font-weight: 600;
    color: var(--admin-text);
    text-align: right;
    word-break: break-all;
  }

  .diag-row-value.ok { color: #16a34a; }
  .diag-row-value.error { color: #dc2626; }
  .diag-row-value.warn { color: #d97706; }
  .diag-row-value.muted { color: var(--admin-muted); font-weight: 400; }

  .diag-table-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.5rem 0;
  }

  .diag-table-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f1f5f9;
    color: var(--admin-text);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 500;
  }

  .diag-table-tag i {
    color: var(--admin-muted);
    font-size: 0.75rem;
  }

  .diag-issue-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .diag-issue-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.88rem;
  }

  .diag-issue-list li:last-child {
    border-bottom: none;
  }

  .diag-issue-list li i {
    margin-top: 0.15rem;
    flex-shrink: 0;
  }

  .diag-admin-list {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0 0;
  }

  .diag-admin-list li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0;
    font-size: 0.85rem;
    color: var(--admin-text);
  }

  .diag-admin-list li i {
    color: var(--admin-primary);
    font-size: 0.8rem;
  }

  .diag-smtp-section {
    margin-top: 0;
  }

  .diag-smtp-detail {
    background: #f8fafc;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
    font-size: 0.82rem;
    color: var(--admin-muted);
    line-height: 1.6;
  }

  .diag-smtp-detail strong {
    color: var(--admin-text);
  }

  .diag-advice {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    background: rgba(59, 130, 246, 0.06);
    border: 1px solid rgba(59, 130, 246, 0.15);
    border-radius: 6px;
    padding: 0.85rem 1rem;
    margin-top: 0.75rem;
    font-size: 0.85rem;
    color: #1e40af;
  }

  .diag-advice i {
    margin-top: 0.1rem;
    flex-shrink: 0;
  }
</style>

<div class="diag-header">
  <h1><i class="fas fa-stethoscope"></i> Diagnostic syst&egrave;me</h1>
  <div class="diag-actions">
    <a href="/admin/diagnostic" class="btn-refresh"><i class="fas fa-sync-alt"></i> Rafra&icirc;chir</a>
    <a href="/admin/test-smtp" class="btn-smtp"><i class="fas fa-envelope-open-text"></i> Test SMTP</a>
  </div>
</div>

<div class="diag-grid">

  <!-- 1. Fichier .env -->
  <div class="diag-card">
    <div class="diag-card-header">
      <h3><i class="fas fa-file-alt"></i> Fichier .env</h3>
      <span class="diag-status <?= $envExists ? 'diag-status-ok' : 'diag-status-error' ?>">
        <i class="fas <?= $envExists ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        <?= $envExists ? 'Pr&eacute;sent' : 'Absent' ?>
      </span>
    </div>
    <div class="diag-card-body">
      <?php if (!$envExists): ?>
        <div class="diag-advice">
          <i class="fas fa-info-circle"></i>
          <span>Copiez le fichier <strong>.env.example</strong> en <strong>.env</strong> et configurez vos param&egrave;tres.</span>
        </div>
      <?php else: ?>
        <div class="diag-row">
          <span class="diag-row-label">Chemin</span>
          <span class="diag-row-value muted">.env</span>
        </div>
        <div class="diag-row">
          <span class="diag-row-label">Statut</span>
          <span class="diag-row-value ok">Fichier charg&eacute;</span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 2. Configuration DB -->
  <div class="diag-card">
    <div class="diag-card-header">
      <h3><i class="fas fa-cog"></i> Configuration DB</h3>
      <span class="diag-status <?= $dbPassDefined ? 'diag-status-ok' : 'diag-status-warn' ?>">
        <i class="fas <?= $dbPassDefined ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
        <?= $dbPassDefined ? 'Configur&eacute;' : 'Mot de passe vide' ?>
      </span>
    </div>
    <div class="diag-card-body">
      <div class="diag-row">
        <span class="diag-row-label">Host</span>
        <span class="diag-row-value"><?= e($dbConfig['host']) ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Port</span>
        <span class="diag-row-value"><?= e($dbConfig['port']) ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Base</span>
        <span class="diag-row-value"><?= e($dbConfig['name']) ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Utilisateur</span>
        <span class="diag-row-value"><?= e($dbConfig['user']) ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Mot de passe</span>
        <span class="diag-row-value <?= $dbPassDefined ? 'ok' : 'warn' ?>"><?= $dbPassDefined ? 'D&eacute;fini' : 'Vide' ?></span>
      </div>
    </div>
  </div>

  <!-- 3. Connexion DB -->
  <div class="diag-card">
    <div class="diag-card-header">
      <h3><i class="fas fa-plug"></i> Connexion base de donn&eacute;es</h3>
      <span class="diag-status <?= $dbConnected ? 'diag-status-ok' : 'diag-status-error' ?>">
        <i class="fas <?= $dbConnected ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        <?= $dbConnected ? 'Connect&eacute;' : '&Eacute;chec' ?>
      </span>
    </div>
    <div class="diag-card-body">
      <?php if ($dbConnected): ?>
        <div class="diag-row">
          <span class="diag-row-label">Statut</span>
          <span class="diag-row-value ok">Connexion r&eacute;ussie</span>
        </div>
        <div class="diag-row">
          <span class="diag-row-label">Version serveur</span>
          <span class="diag-row-value"><?= e($dbVersion) ?></span>
        </div>
      <?php else: ?>
        <div class="diag-row">
          <span class="diag-row-label">Statut</span>
          <span class="diag-row-value error">&Eacute;chec de connexion</span>
        </div>
        <div class="diag-advice">
          <i class="fas fa-exclamation-triangle"></i>
          <span><?= e($dbError) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 4. Tables -->
  <div class="diag-card">
    <div class="diag-card-header">
      <h3><i class="fas fa-table"></i> Tables</h3>
      <span class="diag-status <?= !empty($tables) ? 'diag-status-ok' : 'diag-status-error' ?>">
        <i class="fas <?= !empty($tables) ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        <?= !empty($tables) ? count($tables) . ' table(s)' : 'Aucune' ?>
      </span>
    </div>
    <div class="diag-card-body">
      <?php if ($dbConnected && !empty($tables)): ?>
        <div class="diag-table-list">
          <?php foreach ($tables as $table): ?>
            <span class="diag-table-tag"><i class="fas fa-table"></i> <?= e($table) ?></span>
          <?php endforeach; ?>
        </div>
      <?php elseif ($dbConnected): ?>
        <div class="diag-advice">
          <i class="fas fa-info-circle"></i>
          <span>Aucune table trouv&eacute;e. Importez <strong>database/schema.sql</strong> pour cr&eacute;er la structure.</span>
        </div>
      <?php else: ?>
        <div class="diag-row">
          <span class="diag-row-label">Statut</span>
          <span class="diag-row-value muted">Connexion requise</span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 5. Admin Users -->
  <div class="diag-card diag-card-full">
    <div class="diag-card-header">
      <h3><i class="fas fa-user-shield"></i> Administrateurs</h3>
      <span class="diag-status <?= $adminTableOk ? ($adminCount > 0 ? 'diag-status-ok' : 'diag-status-warn') : 'diag-status-error' ?>">
        <i class="fas <?= $adminTableOk ? ($adminCount > 0 ? 'fa-check-circle' : 'fa-exclamation-triangle') : 'fa-times-circle' ?>"></i>
        <?php if (!$adminTableOk): ?>
          Table absente
        <?php elseif ($adminCount > 0): ?>
          <?= $adminCount ?> admin(s)
        <?php else: ?>
          Aucun admin
        <?php endif; ?>
      </span>
    </div>
    <div class="diag-card-body">
      <?php if (!$dbConnected): ?>
        <div class="diag-row">
          <span class="diag-row-label">Statut</span>
          <span class="diag-row-value muted">Connexion requise</span>
        </div>
      <?php elseif (!$adminTableOk): ?>
        <div class="diag-advice">
          <i class="fas fa-exclamation-triangle"></i>
          <span>La table <strong>admin_users</strong> est absente. Ex&eacute;cutez <strong>database/setup-admin.php</strong> pour la cr&eacute;er.</span>
        </div>
      <?php else: ?>
        <div class="diag-row">
          <span class="diag-row-label">Colonnes</span>
          <span class="diag-row-value muted" style="font-size: 0.8rem;"><?= e(implode(', ', $adminColumns)) ?></span>
        </div>
        <?php if (!$loginCodeExists): ?>
          <div class="diag-advice" style="border-color: rgba(239,68,68,0.2); background: rgba(239,68,68,0.05); color: #dc2626;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>La colonne <strong>login_code</strong> est manquante ! L'authentification ne fonctionnera pas.</span>
          </div>
        <?php endif; ?>
        <?php if ($adminCount > 0): ?>
          <ul class="diag-admin-list">
            <?php foreach ($adminEmails as $email): ?>
              <li><i class="fas fa-user-circle"></i> <?= e($email) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="diag-advice">
            <i class="fas fa-info-circle"></i>
            <span>Aucun administrateur. Ex&eacute;cutez <strong>database/setup-admin.php</strong> pour en cr&eacute;er un.</span>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- 6. SMTP Configuration -->
  <div class="diag-card diag-card-full diag-smtp-section">
    <div class="diag-card-header">
      <h3><i class="fas fa-envelope"></i> Configuration SMTP</h3>
      <span class="diag-status <?= $smtpConfigured ? ($smtpConnected ? 'diag-status-ok' : 'diag-status-error') : 'diag-status-warn' ?>">
        <i class="fas <?= $smtpConfigured ? ($smtpConnected ? 'fa-check-circle' : 'fa-times-circle') : 'fa-exclamation-triangle' ?>"></i>
        <?php if (!$smtpConfigured): ?>
          Non configur&eacute;
        <?php elseif ($smtpConnected): ?>
          Connect&eacute;
        <?php else: ?>
          &Eacute;chec
        <?php endif; ?>
      </span>
    </div>
    <div class="diag-card-body">
      <div class="diag-row">
        <span class="diag-row-label">Host</span>
        <span class="diag-row-value <?= $smtpHost !== '' ? '' : 'warn' ?>"><?= $smtpHost !== '' ? e($smtpHost) : 'Non d&eacute;fini' ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Port</span>
        <span class="diag-row-value"><?= (int) $smtpPort ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Utilisateur</span>
        <span class="diag-row-value <?= $smtpUser !== '' ? '' : 'muted' ?>"><?= $smtpUser !== '' ? e($smtpUser) : 'Non d&eacute;fini' ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Mot de passe</span>
        <span class="diag-row-value <?= $smtpPassDefined ? 'ok' : 'warn' ?>"><?= $smtpPassDefined ? 'D&eacute;fini' : 'Vide' ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Chiffrement</span>
        <span class="diag-row-value"><?= e($smtpEncryption) ?></span>
      </div>
      <div class="diag-row">
        <span class="diag-row-label">Exp&eacute;diteur</span>
        <span class="diag-row-value <?= $smtpFrom !== '' ? '' : 'muted' ?>"><?= $smtpFrom !== '' ? e($smtpFrom) : 'Non d&eacute;fini' ?></span>
      </div>

      <?php if ($smtpConfigured): ?>
        <?php if ($smtpConnected): ?>
          <div class="diag-advice" style="background: rgba(34,197,94,0.06); border-color: rgba(34,197,94,0.2); color: #16a34a;">
            <i class="fas fa-check-circle"></i>
            <span>Connexion SMTP r&eacute;ussie. Les emails peuvent &ecirc;tre envoy&eacute;s.</span>
          </div>
        <?php else: ?>
          <div class="diag-advice" style="background: rgba(239,68,68,0.05); border-color: rgba(239,68,68,0.2); color: #dc2626;">
            <i class="fas fa-times-circle"></i>
            <span><?= e($smtpError) ?></span>
          </div>
          <?php if (!empty($smtpDiagnostics)): ?>
            <div class="diag-smtp-detail">
              <strong>Analyse :</strong><br>
              <?php foreach ($smtpDiagnostics as $diag): ?>
                &bull; <?= e($diag) ?><br>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($smtpAdvice)): ?>
            <div class="diag-advice">
              <i class="fas fa-lightbulb"></i>
              <span><?= e($smtpAdvice) ?></span>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      <?php else: ?>
        <div class="diag-advice">
          <i class="fas fa-info-circle"></i>
          <span>Configurez <strong>MAIL_SMTP_HOST</strong> dans votre fichier <strong>.env</strong> pour activer l'envoi d'emails.</span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 7. Issues -->
  <?php if (!empty($issues)): ?>
  <div class="diag-card diag-card-full">
    <div class="diag-card-header">
      <h3><i class="fas fa-exclamation-triangle" style="color: var(--admin-warning);"></i> Probl&egrave;mes d&eacute;tect&eacute;s</h3>
      <span class="diag-status diag-status-warn">
        <i class="fas fa-exclamation-triangle"></i>
        <?= count($issues) ?> probl&egrave;me(s)
      </span>
    </div>
    <div class="diag-card-body">
      <ul class="diag-issue-list">
        <?php foreach ($issues as $issue): ?>
          <li>
            <i class="fas fa-exclamation-circle" style="color: var(--admin-warning);"></i>
            <span><?= e($issue) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

</div>
