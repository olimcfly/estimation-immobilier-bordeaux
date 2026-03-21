<style>
  .db-connect-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 2rem;
    max-width: 600px;
    margin: 0 auto 2rem;
  }

  .db-connect-card h2 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .db-form-group {
    margin-bottom: 1rem;
  }

  .db-form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-muted);
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .db-form-group input, .db-form-group select {
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

  .db-form-group input:focus, .db-form-group select:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px var(--admin-primary-light);
  }

  .db-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .btn-connect {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
    font-family: inherit;
  }

  .btn-connect:hover {
    background: #6b0f2d;
  }

  .btn-disconnect {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    background: var(--admin-danger);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
  }

  .btn-disconnect:hover {
    opacity: 0.9;
  }

  .db-status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .db-status.connected {
    background: rgba(34,197,94,0.1);
    color: #16a34a;
  }

  .db-status.disconnected {
    background: rgba(239,68,68,0.1);
    color: #dc2626;
  }

  .db-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
  }

  .db-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  /* Tables explorer */
  .tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .table-explorer-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .table-explorer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-border);
    cursor: pointer;
  }

  .table-explorer-header:hover {
    background: #f1f5f9;
  }

  .table-explorer-name {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-explorer-name i {
    color: var(--admin-primary);
  }

  .table-row-count {
    font-size: 0.75rem;
    color: var(--admin-muted);
    background: var(--admin-bg);
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
  }

  .table-explorer-body {
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .table-explorer-body.open {
    max-height: 800px;
    overflow-y: auto;
  }

  .col-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .col-list li {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    font-size: 0.82rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .col-list li:last-child {
    border-bottom: none;
  }

  .col-name {
    font-weight: 600;
    color: var(--admin-text);
    font-family: 'Courier New', monospace;
  }

  .col-type {
    color: var(--admin-muted);
    font-size: 0.75rem;
    font-family: 'Courier New', monospace;
  }

  .col-key {
    display: inline-block;
    padding: 0.1rem 0.4rem;
    border-radius: 3px;
    font-size: 0.65rem;
    font-weight: 700;
    margin-left: 0.3rem;
  }

  .col-key.pri { background: rgba(245,158,11,0.15); color: #d97706; }
  .col-key.uni { background: rgba(59,130,246,0.15); color: #2563eb; }
  .col-key.mul { background: rgba(168,85,247,0.15); color: #7c3aed; }

  /* Missing items */
  .missing-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .missing-section h3 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--admin-warning);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .missing-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0.85rem;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
  }

  .missing-item-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .missing-item-badge {
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
  }

  .missing-item-badge.table { background: rgba(239,68,68,0.15); color: #dc2626; }
  .missing-item-badge.column { background: rgba(245,158,11,0.15); color: #d97706; }

  .missing-item-files {
    font-size: 0.75rem;
    color: var(--admin-muted);
  }

  .btn-create-missing {
    padding: 0.3rem 0.7rem;
    background: var(--admin-success);
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
  }

  /* Create forms */
  .create-section {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
  }

  .create-section h3 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--admin-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .create-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }

  .create-tab {
    padding: 0.5rem 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    background: #fff;
    color: var(--admin-muted);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }

  .create-tab:hover, .create-tab.active {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  .create-panel {
    display: none;
  }

  .create-panel.active {
    display: block;
  }

  .dynamic-columns {
    margin-top: 1rem;
  }

  .dynamic-col-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    align-items: end;
  }

  .dynamic-col-row input, .dynamic-col-row select {
    padding: 0.5rem;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    font-size: 0.85rem;
    font-family: inherit;
  }

  .btn-add-col, .btn-remove-col {
    padding: 0.4rem 0.7rem;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    font-family: inherit;
  }

  .btn-add-col {
    background: var(--admin-info);
    color: #fff;
    margin-top: 0.5rem;
  }

  .btn-remove-col {
    background: var(--admin-danger);
    color: #fff;
    width: 34px;
    height: 34px;
  }

  .btn-submit {
    padding: 0.6rem 1.5rem;
    background: var(--admin-success);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 1rem;
    font-family: inherit;
  }

  .btn-submit:hover {
    opacity: 0.9;
  }

  @media (max-width: 640px) {
    .db-form-row {
      grid-template-columns: 1fr;
    }
    .tables-grid {
      grid-template-columns: 1fr;
    }
    .dynamic-col-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<?php
  $connected = $connected ?? false;
  $error = $error ?? '';
  $tables = $tables ?? [];
  $tableDetails = $tableDetails ?? [];
  $missingItems = $missingItems ?? [];
  $flash = $_SESSION['db_flash'] ?? null;
  unset($_SESSION['db_flash']);
?>

<!-- PAGE HEADER -->
<div class="admin-page-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
  <h1 style="font-size:1.5rem;font-weight:700;color:var(--admin-text);display:flex;align-items:center;gap:0.5rem;">
    <i class="fas fa-database" style="color:var(--admin-primary);"></i> Administration Base de Donn&eacute;es
  </h1>
  <?php if ($connected): ?>
    <span class="db-status connected"><i class="fas fa-check-circle"></i> Connect&eacute;</span>
  <?php else: ?>
    <span class="db-status disconnected"><i class="fas fa-times-circle"></i> D&eacute;connect&eacute;</span>
  <?php endif; ?>
</div>

<!-- FLASH MESSAGES -->
<?php if ($flash): ?>
  <div class="<?= $flash['type'] === 'success' ? 'db-success' : 'db-error' ?>">
    <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="db-error">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
      <strong>Erreur de connexion :</strong><br>
      <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
  </div>
<?php endif; ?>

<!-- CONNECTION FORM -->
<?php if (!$connected): ?>
  <div class="db-connect-card">
    <h2><i class="fas fa-plug"></i> Connexion &agrave; la base de donn&eacute;es</h2>

    <?php if (!empty($error)): ?>
      <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:0.75rem 1rem;border-radius:6px;margin-bottom:1rem;font-size:0.85rem;">
        <i class="fas fa-info-circle"></i>
        V&eacute;rifiez vos identifiants de connexion dans le fichier <code>.env</code> ou saisissez-les ci-dessous.
      </div>
    <?php endif; ?>

    <form method="post" action="/admin/database">
      <input type="hidden" name="action" value="connect">
      <div class="db-form-row">
        <div class="db-form-group">
          <label>H&ocirc;te</label>
          <input type="text" name="db_host" value="<?= htmlspecialchars($dbHost ?? '127.0.0.1', ENT_QUOTES, 'UTF-8') ?>" placeholder="127.0.0.1">
        </div>
        <div class="db-form-group">
          <label>Port</label>
          <input type="number" name="db_port" value="<?= (int) ($dbPort ?? 3306) ?>" placeholder="3306">
        </div>
      </div>
      <div class="db-form-group">
        <label>Nom de la base</label>
        <input type="text" name="db_name" value="<?= htmlspecialchars($dbName ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ma_base_de_donnees" required>
      </div>
      <div class="db-form-row">
        <div class="db-form-group">
          <label>Utilisateur</label>
          <input type="text" name="db_user" value="<?= htmlspecialchars($dbUser ?? 'root', ENT_QUOTES, 'UTF-8') ?>" placeholder="root">
        </div>
        <div class="db-form-group">
          <label>Mot de passe</label>
          <input type="password" name="db_pass" value="<?= htmlspecialchars($dbPass ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="********">
        </div>
      </div>
      <button type="submit" class="btn-connect">
        <i class="fas fa-plug"></i> Se connecter
      </button>
    </form>
  </div>
<?php else: ?>

  <!-- CONNECTED: Show info + disconnect -->
  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <span style="font-size:0.85rem;color:var(--admin-muted);">
      <i class="fas fa-server"></i>
      <?= htmlspecialchars($dbHost ?? '', ENT_QUOTES, 'UTF-8') ?>:<?= (int) ($dbPort ?? 3306) ?> /
      <strong><?= htmlspecialchars($dbName ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
      (<?= htmlspecialchars($dbUser ?? '', ENT_QUOTES, 'UTF-8') ?>)
      &mdash; <?= count($tables) ?> table<?= count($tables) > 1 ? 's' : '' ?>
    </span>
    <form method="post" action="/admin/database" style="display:inline;">
      <input type="hidden" name="action" value="disconnect">
      <button type="submit" class="btn-disconnect" style="padding:0.35rem 0.85rem;font-size:0.8rem;">
        <i class="fas fa-sign-out-alt"></i> D&eacute;connexion
      </button>
    </form>
  </div>

  <!-- MISSING ITEMS DETECTION -->
  <?php if (!empty($missingItems)): ?>
    <div class="missing-section">
      <h3><i class="fas fa-exclamation-triangle"></i> &Eacute;l&eacute;ments manquants d&eacute;tect&eacute;s dans le code</h3>
      <?php foreach ($missingItems as $item): ?>
        <div class="missing-item">
          <div class="missing-item-info">
            <span class="missing-item-badge <?= $item['type'] ?>"><?= $item['type'] === 'table' ? 'Table' : 'Colonne' ?></span>
            <strong style="font-family:monospace;">
              <?php if ($item['type'] === 'column'): ?>
                <?= htmlspecialchars($item['table'], ENT_QUOTES, 'UTF-8') ?>.<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
              <?php else: ?>
                <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
              <?php endif; ?>
            </strong>
            <span class="missing-item-files">
              (r&eacute;f&eacute;renc&eacute; dans: <?= htmlspecialchars(implode(', ', $item['referenced_in']), ENT_QUOTES, 'UTF-8') ?>)
            </span>
          </div>
          <?php if ($item['type'] === 'column'): ?>
            <form method="post" action="/admin/database" style="display:inline;">
              <input type="hidden" name="action" value="create_column">
              <input type="hidden" name="target_table" value="<?= htmlspecialchars($item['table'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="col_name" value="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="col_type" value="VARCHAR(255)">
              <input type="hidden" name="col_nullable" value="1">
              <button type="submit" class="btn-create-missing"><i class="fas fa-plus"></i> Cr&eacute;er</button>
            </form>
          <?php else: ?>
            <form method="post" action="/admin/database" style="display:inline;">
              <input type="hidden" name="action" value="create_table">
              <input type="hidden" name="table_name" value="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn-create-missing"><i class="fas fa-plus"></i> Cr&eacute;er</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- TABLES EXPLORER -->
  <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
    <i class="fas fa-table" style="color:var(--admin-info);"></i> Tables (<?= count($tables) ?>)
  </h2>

  <?php if (empty($tables)): ?>
    <div style="text-align:center;padding:3rem;color:var(--admin-muted);">
      <i class="fas fa-database" style="font-size:2.5rem;opacity:0.3;display:block;margin-bottom:1rem;"></i>
      <p>Aucune table trouv&eacute;e dans cette base de donn&eacute;es.</p>
    </div>
  <?php else: ?>
    <div class="tables-grid">
      <?php foreach ($tables as $table): ?>
        <?php $details = $tableDetails[$table] ?? ['columns' => [], 'row_count' => 0]; ?>
        <div class="table-explorer-card">
          <div class="table-explorer-header" onclick="this.nextElementSibling.classList.toggle('open')">
            <span class="table-explorer-name">
              <i class="fas fa-table"></i> <?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?>
              (<?= count($details['columns']) ?> col.)
            </span>
            <span class="table-row-count"><?= number_format($details['row_count']) ?> ligne<?= $details['row_count'] > 1 ? 's' : '' ?></span>
          </div>
          <div class="table-explorer-body">
            <ul class="col-list">
              <?php foreach ($details['columns'] as $col): ?>
                <li>
                  <span>
                    <span class="col-name"><?= htmlspecialchars($col['Field'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if (!empty($col['Key'])): ?>
                      <span class="col-key <?= strtolower($col['Key']) ?>"><?= htmlspecialchars($col['Key'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                  </span>
                  <span class="col-type">
                    <?= htmlspecialchars($col['Type'], ENT_QUOTES, 'UTF-8') ?>
                    <?= $col['Null'] === 'YES' ? ' NULL' : ' NOT NULL' ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- CREATE TABLE / COLUMN -->
  <div class="create-section">
    <h3><i class="fas fa-plus-circle"></i> Cr&eacute;er</h3>

    <div class="create-tabs">
      <button class="create-tab active" onclick="switchCreateTab('table', this)">Nouvelle table</button>
      <button class="create-tab" onclick="switchCreateTab('column', this)">Nouvelle colonne</button>
    </div>

    <!-- Create Table Panel -->
    <div class="create-panel active" id="panel-table">
      <form method="post" action="/admin/database" id="createTableForm">
        <input type="hidden" name="action" value="create_table">
        <input type="hidden" name="columns_json" id="columnsJsonInput" value="">

        <div class="db-form-group">
          <label>Nom de la table</label>
          <input type="text" name="table_name" placeholder="ma_nouvelle_table" pattern="[a-zA-Z_][a-zA-Z0-9_]*" required>
        </div>

        <div class="dynamic-columns" id="dynamicColumns">
          <label style="font-size:0.82rem;font-weight:600;color:var(--admin-muted);text-transform:uppercase;letter-spacing:0.03em;">Colonnes</label>
          <div style="font-size:0.75rem;color:var(--admin-muted);margin-bottom:0.5rem;">
            La colonne <code>id</code> (PRIMARY KEY) est ajout&eacute;e automatiquement.
          </div>
          <div class="dynamic-col-row">
            <input type="text" placeholder="Nom de colonne" class="col-input-name" value="created_at">
            <select class="col-input-type">
              <option value="VARCHAR(255)">VARCHAR(255)</option>
              <option value="INT">INT</option>
              <option value="INT UNSIGNED">INT UNSIGNED</option>
              <option value="BIGINT">BIGINT</option>
              <option value="DECIMAL(10,2)">DECIMAL(10,2)</option>
              <option value="TEXT">TEXT</option>
              <option value="LONGTEXT">LONGTEXT</option>
              <option value="BOOLEAN">BOOLEAN</option>
              <option value="DATE">DATE</option>
              <option value="DATETIME" selected>DATETIME</option>
              <option value="DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP">DATETIME DEFAULT NOW</option>
              <option value="ENUM('a','b','c')">ENUM</option>
              <option value="JSON">JSON</option>
            </select>
            <button type="button" class="btn-remove-col" onclick="this.parentElement.remove()" title="Supprimer">&times;</button>
          </div>
        </div>

        <button type="button" class="btn-add-col" onclick="addColumnRow()">
          <i class="fas fa-plus"></i> Ajouter une colonne
        </button>
        <br>
        <button type="submit" class="btn-submit">
          <i class="fas fa-check"></i> Cr&eacute;er la table
        </button>
      </form>
    </div>

    <!-- Create Column Panel -->
    <div class="create-panel" id="panel-column">
      <form method="post" action="/admin/database">
        <input type="hidden" name="action" value="create_column">

        <div class="db-form-group">
          <label>Table cible</label>
          <select name="target_table" required>
            <option value="">-- Choisir une table --</option>
            <?php foreach ($tables as $table): ?>
              <option value="<?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="db-form-row">
          <div class="db-form-group">
            <label>Nom de la colonne</label>
            <input type="text" name="col_name" placeholder="ma_colonne" pattern="[a-zA-Z_][a-zA-Z0-9_]*" required>
          </div>
          <div class="db-form-group">
            <label>Type</label>
            <select name="col_type">
              <option value="VARCHAR(255)">VARCHAR(255)</option>
              <option value="VARCHAR(120)">VARCHAR(120)</option>
              <option value="VARCHAR(180)">VARCHAR(180)</option>
              <option value="INT">INT</option>
              <option value="INT UNSIGNED">INT UNSIGNED</option>
              <option value="BIGINT">BIGINT</option>
              <option value="DECIMAL(10,2)">DECIMAL(10,2)</option>
              <option value="DECIMAL(12,2)">DECIMAL(12,2)</option>
              <option value="TEXT">TEXT</option>
              <option value="LONGTEXT">LONGTEXT</option>
              <option value="BOOLEAN">BOOLEAN</option>
              <option value="DATE">DATE</option>
              <option value="DATETIME">DATETIME</option>
              <option value="JSON">JSON</option>
            </select>
          </div>
        </div>

        <div class="db-form-row">
          <div class="db-form-group">
            <label>Valeur par d&eacute;faut (optionnel)</label>
            <input type="text" name="col_default" placeholder="Laisser vide si aucune">
          </div>
          <div class="db-form-group" style="display:flex;align-items:center;gap:0.5rem;padding-top:1.5rem;">
            <input type="checkbox" name="col_nullable" id="colNullable" value="1" checked>
            <label for="colNullable" style="text-transform:none;font-size:0.85rem;margin:0;">Nullable (NULL autoris&eacute;)</label>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fas fa-plus"></i> Ajouter la colonne
        </button>
      </form>
    </div>
  </div>

<?php endif; ?>

<script>
function switchCreateTab(tab, btn) {
  document.querySelectorAll('.create-tab').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.create-panel').forEach(function(p) { p.classList.remove('active'); });
  btn.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}

function addColumnRow() {
  var container = document.getElementById('dynamicColumns');
  var row = document.createElement('div');
  row.className = 'dynamic-col-row';
  row.innerHTML = '<input type="text" placeholder="Nom de colonne" class="col-input-name">' +
    '<select class="col-input-type">' +
    '<option value="VARCHAR(255)">VARCHAR(255)</option>' +
    '<option value="INT">INT</option>' +
    '<option value="INT UNSIGNED">INT UNSIGNED</option>' +
    '<option value="BIGINT">BIGINT</option>' +
    '<option value="DECIMAL(10,2)">DECIMAL(10,2)</option>' +
    '<option value="TEXT">TEXT</option>' +
    '<option value="LONGTEXT">LONGTEXT</option>' +
    '<option value="BOOLEAN">BOOLEAN</option>' +
    '<option value="DATE">DATE</option>' +
    '<option value="DATETIME">DATETIME</option>' +
    '<option value="DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP">DATETIME DEFAULT NOW</option>' +
    '<option value="JSON">JSON</option>' +
    '</select>' +
    '<button type="button" class="btn-remove-col" onclick="this.parentElement.remove()" title="Supprimer">&times;</button>';
  container.appendChild(row);
}

// Before submit, gather columns into JSON
document.getElementById('createTableForm').addEventListener('submit', function(e) {
  var rows = document.querySelectorAll('#dynamicColumns .dynamic-col-row');
  var columns = [
    { name: 'id', type: 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY' }
  ];
  rows.forEach(function(row) {
    var name = row.querySelector('.col-input-name').value.trim();
    var type = row.querySelector('.col-input-type').value;
    if (name !== '') {
      columns.push({ name: name, type: type });
    }
  });
  document.getElementById('columnsJsonInput').value = JSON.stringify(columns);
});
</script>
