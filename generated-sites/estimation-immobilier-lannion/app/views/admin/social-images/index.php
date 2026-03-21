<?php
$message = (string) ($_GET['message'] ?? '');
$error = (string) ($_GET['error'] ?? '');
?>

<style>
  .social-page h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
  .social-page .subtitle { color: var(--admin-muted); margin-bottom: 1.5rem; font-size: 0.9rem; }

  .social-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
  .social-tab {
    padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid var(--admin-border);
    background: var(--admin-surface); cursor: pointer; font-size: 0.85rem; font-weight: 500;
    color: var(--admin-muted); transition: all 0.15s;
  }
  .social-tab:hover { border-color: var(--admin-primary); color: var(--admin-primary); }
  .social-tab.active { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }

  .template-config {
    background: var(--admin-surface); border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius); padding: 1.5rem; margin-bottom: 1.5rem;
  }
  .config-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; }
  .config-grid label { display: flex; flex-direction: column; gap: 0.3rem; font-size: 0.85rem; font-weight: 500; }
  .config-grid input, .config-grid select, .config-grid textarea {
    padding: 0.5rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px;
    font-size: 0.85rem; font-family: inherit;
  }
  .config-grid textarea { resize: vertical; min-height: 60px; }
  .config-grid input[type="color"] { height: 38px; padding: 2px; cursor: pointer; }

  .preview-area {
    background: #e2e8f0; border-radius: var(--admin-radius); padding: 2rem;
    display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem;
    min-height: 400px; overflow: auto;
  }

  .action-bar { display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap; }
  .btn-action {
    padding: 0.6rem 1.25rem; border-radius: 6px; border: none; cursor: pointer;
    font-size: 0.85rem; font-weight: 600; font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; gap: 0.5rem;
  }
  .btn-primary { background: var(--admin-primary); color: #fff; }
  .btn-primary:hover { opacity: 0.9; }
  .btn-secondary { background: var(--admin-surface); color: var(--admin-text); border: 1px solid var(--admin-border); }
  .btn-secondary:hover { border-color: var(--admin-primary); }
  .btn-success { background: var(--admin-success); color: #fff; }

  /* ============================== */
  /* SOCIAL IMAGE TEMPLATES         */
  /* ============================== */
  .social-card {
    font-family: 'DM Sans', -apple-system, sans-serif;
    overflow: hidden;
    position: relative;
  }

  /* Template: Estimation Prix */
  .tpl-estimation {
    width: 1080px; height: 1080px;
    background: linear-gradient(135deg, #8B1538 0%, #5a0e25 60%, #1a0510 100%);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    color: #fff; text-align: center; padding: 80px;
  }
  .tpl-estimation .badge-top {
    background: rgba(212, 175, 55, 0.2); border: 1px solid #D4AF37;
    padding: 10px 28px; border-radius: 30px; font-size: 22px; font-weight: 600;
    color: #D4AF37; margin-bottom: 40px; letter-spacing: 0.05em;
  }
  .tpl-estimation .big-price {
    font-size: 96px; font-weight: 700; line-height: 1.1; margin-bottom: 20px;
  }
  .tpl-estimation .price-label {
    font-size: 28px; color: rgba(255,255,255,0.7); margin-bottom: 50px;
  }
  .tpl-estimation .divider {
    width: 80px; height: 3px; background: #D4AF37; margin-bottom: 50px;
  }
  .tpl-estimation .quartier {
    font-size: 36px; font-weight: 600; margin-bottom: 15px;
  }
  .tpl-estimation .city {
    font-size: 24px; color: rgba(255,255,255,0.6);
  }
  .tpl-estimation .logo-bottom {
    position: absolute; bottom: 50px; font-size: 18px; color: rgba(255,255,255,0.4);
    letter-spacing: 0.1em;
  }

  /* Template: Conseil / Tips */
  .tpl-conseil {
    width: 1080px; height: 1080px;
    background: #faf9f7;
    display: flex; flex-direction: column;
    padding: 0;
  }
  .tpl-conseil .header-bar {
    background: #8B1538; padding: 40px 60px;
    display: flex; align-items: center; justify-content: space-between;
  }
  .tpl-conseil .header-bar .brand { color: #fff; font-size: 22px; font-weight: 700; }
  .tpl-conseil .header-bar .tag {
    background: #D4AF37; color: #1a1410; padding: 8px 20px; border-radius: 20px;
    font-size: 16px; font-weight: 600;
  }
  .tpl-conseil .content-area {
    flex: 1; display: flex; flex-direction: column; justify-content: center;
    padding: 60px 80px;
  }
  .tpl-conseil .tip-number {
    font-size: 72px; font-weight: 700; color: #D4AF37; margin-bottom: 20px; line-height: 1;
  }
  .tpl-conseil .tip-title {
    font-size: 48px; font-weight: 700; color: #1a1410; line-height: 1.2; margin-bottom: 30px;
  }
  .tpl-conseil .tip-text {
    font-size: 26px; color: #6b6459; line-height: 1.5;
  }
  .tpl-conseil .footer-bar {
    padding: 30px 60px; border-top: 2px solid #e8dfd7;
    display: flex; align-items: center; justify-content: space-between;
  }
  .tpl-conseil .footer-bar .url { font-size: 18px; color: #8B1538; font-weight: 600; }
  .tpl-conseil .footer-bar .cta { font-size: 18px; color: #6b6459; }

  /* Template: Statistique / Chiffre Clé */
  .tpl-stat {
    width: 1080px; height: 1080px;
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    color: #fff; text-align: center; padding: 80px;
  }
  .tpl-stat .stat-label {
    font-size: 22px; color: #94a3b8; text-transform: uppercase;
    letter-spacing: 0.15em; margin-bottom: 30px;
  }
  .tpl-stat .stat-value {
    font-size: 120px; font-weight: 700; color: #D4AF37; line-height: 1; margin-bottom: 20px;
  }
  .tpl-stat .stat-unit {
    font-size: 36px; color: #D4AF37; font-weight: 500; margin-bottom: 50px;
  }
  .tpl-stat .stat-desc {
    font-size: 28px; color: rgba(255,255,255,0.7); line-height: 1.4; max-width: 700px;
  }
  .tpl-stat .stat-brand {
    position: absolute; bottom: 50px; font-size: 18px; color: rgba(255,255,255,0.3);
    letter-spacing: 0.1em;
  }

  /* Template: Story (9:16) */
  .tpl-story {
    width: 1080px; height: 1920px;
    background: linear-gradient(180deg, #8B1538 0%, #5a0e25 40%, #1a0510 100%);
    display: flex; flex-direction: column; justify-content: space-between;
    color: #fff; padding: 80px 60px;
  }
  .tpl-story .story-top { text-align: center; }
  .tpl-story .story-brand {
    font-size: 20px; font-weight: 600; letter-spacing: 0.1em; color: rgba(255,255,255,0.5);
    margin-bottom: 60px;
  }
  .tpl-story .story-middle {
    text-align: center; flex: 1; display: flex; flex-direction: column;
    justify-content: center; align-items: center;
  }
  .tpl-story .story-emoji { font-size: 80px; margin-bottom: 40px; }
  .tpl-story .story-title {
    font-size: 52px; font-weight: 700; line-height: 1.2; margin-bottom: 30px;
  }
  .tpl-story .story-text {
    font-size: 28px; color: rgba(255,255,255,0.7); line-height: 1.5; max-width: 800px;
  }
  .tpl-story .story-bottom { text-align: center; }
  .tpl-story .story-cta {
    display: inline-block; background: #D4AF37; color: #1a0510; padding: 20px 50px;
    border-radius: 40px; font-size: 24px; font-weight: 700;
  }
  .tpl-story .story-url {
    margin-top: 20px; font-size: 16px; color: rgba(255,255,255,0.4);
  }

  /* Template: Paysage (16:9) */
  .tpl-paysage {
    width: 1200px; height: 628px;
    background: linear-gradient(135deg, #8B1538 0%, #5a0e25 50%, #2d0a18 100%);
    display: flex; color: #fff;
  }
  .tpl-paysage .left-panel {
    flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 50px 60px;
  }
  .tpl-paysage .right-panel {
    width: 380px; background: rgba(0,0,0,0.2);
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    padding: 40px;
  }
  .tpl-paysage .tag-line {
    font-size: 16px; color: #D4AF37; text-transform: uppercase;
    letter-spacing: 0.1em; font-weight: 600; margin-bottom: 20px;
  }
  .tpl-paysage .main-title {
    font-size: 38px; font-weight: 700; line-height: 1.2; margin-bottom: 20px;
  }
  .tpl-paysage .sub-text {
    font-size: 18px; color: rgba(255,255,255,0.7); line-height: 1.5;
  }
  .tpl-paysage .right-big {
    font-size: 72px; font-weight: 700; color: #D4AF37; line-height: 1;
  }
  .tpl-paysage .right-label {
    font-size: 18px; color: rgba(255,255,255,0.6); margin-top: 10px; text-align: center;
  }
  .tpl-paysage .right-brand {
    margin-top: 30px; font-size: 14px; color: rgba(255,255,255,0.3); letter-spacing: 0.1em;
  }

  /* Saved images grid */
  .saved-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;
  }
  .saved-card {
    background: var(--admin-surface); border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius); overflow: hidden;
  }
  .saved-card img { width: 100%; height: 180px; object-fit: cover; }
  .saved-card .info { padding: 0.75rem; }
  .saved-card .info p { font-size: 0.8rem; word-break: break-all; margin-bottom: 0.25rem; }
  .saved-card .info .meta { font-size: 0.75rem; color: var(--admin-muted); }
  .saved-card .actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; }

  .alert-msg { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.85rem; }
  .alert-success { background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
  .alert-error { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
</style>

<div class="social-page">
  <h1><i class="fas fa-share-alt" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Images R&eacute;seaux Sociaux</h1>
  <p class="subtitle">Cr&eacute;ez des visuels en HTML puis t&eacute;l&eacute;chargez-les en PNG pour vos r&eacute;seaux sociaux.</p>

  <?php if ($message !== ''): ?>
    <div class="alert-msg alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="alert-msg alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <!-- Template selector tabs -->
  <div class="social-tabs">
    <button class="social-tab active" data-template="estimation"><i class="fas fa-euro-sign"></i> Prix / Estimation</button>
    <button class="social-tab" data-template="conseil"><i class="fas fa-lightbulb"></i> Conseil / Tips</button>
    <button class="social-tab" data-template="stat"><i class="fas fa-chart-bar"></i> Chiffre Cl&eacute;</button>
    <button class="social-tab" data-template="story"><i class="fas fa-mobile-alt"></i> Story (9:16)</button>
    <button class="social-tab" data-template="paysage"><i class="fas fa-image"></i> Paysage (16:9)</button>
  </div>

  <!-- Configuration panel -->
  <div class="template-config">
    <div class="config-grid" id="config-fields">
      <!-- Fields populated by JS based on template -->
    </div>
    <div style="margin-top:1rem;">
      <button class="btn-action btn-secondary" id="btn-update"><i class="fas fa-sync-alt"></i> Mettre &agrave; jour l'aper&ccedil;u</button>
    </div>
  </div>

  <!-- Preview -->
  <div class="preview-area" id="preview-area">
    <div id="social-canvas"></div>
  </div>

  <!-- Actions -->
  <div class="action-bar">
    <button class="btn-action btn-primary" id="btn-download"><i class="fas fa-download"></i> T&eacute;l&eacute;charger PNG</button>
    <button class="btn-action btn-success" id="btn-save"><i class="fas fa-save"></i> Sauvegarder sur le serveur</button>
    <span id="action-status" style="font-size:0.85rem;color:var(--admin-muted);align-self:center;"></span>
  </div>

  <!-- Saved images -->
  <?php if (!empty($images)): ?>
    <div style="margin-top:2rem;">
      <h2 style="font-size:1.15rem;margin-bottom:1rem;">Images sauvegard&eacute;es (<?= count($images) ?>)</h2>
      <div class="saved-grid">
        <?php foreach ($images as $img): ?>
          <div class="saved-card">
            <img src="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <div class="info">
              <p><?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?></p>
              <p class="meta"><?= htmlspecialchars($img['created_at'], ENT_QUOTES, 'UTF-8') ?> &mdash; <?= number_format(($img['size'] ?? 0) / 1024, 1) ?> Ko</p>
              <div class="actions">
                <a href="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>" download class="btn-action btn-secondary" style="font-size:0.75rem;padding:0.35rem 0.7rem;"><i class="fas fa-download"></i></a>
                <form method="post" action="/admin/social-images/delete" style="margin:0;" onsubmit="return confirm('Supprimer ?');">
                  <input type="hidden" name="filename" value="<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" class="btn-action" style="background:var(--admin-danger);color:#fff;font-size:0.75rem;padding:0.35rem 0.7rem;"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- html2canvas CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
(function() {
  var currentTemplate = 'estimation';
  var canvas = document.getElementById('social-canvas');
  var configFields = document.getElementById('config-fields');

  // Template configurations
  var templates = {
    estimation: {
      fields: [
        { id: 'prix', label: 'Prix affich\u00e9', type: 'text', value: '4 800 \u20ac/m\u00b2' },
        { id: 'label', label: 'Sous-titre prix', type: 'text', value: 'Prix moyen au m\u00b2' },
        { id: 'quartier', label: 'Quartier', type: 'select', options: ['Lannion Centre', <?php foreach ($quartiers as $q): ?>'<?= $q ?>',<?php endforeach; ?>], value: 'Lannion Centre' },
        { id: 'badge', label: 'Badge', type: 'text', value: 'ESTIMATION GRATUITE' },
        { id: 'brand', label: 'Marque', type: 'text', value: 'estimation-immobilier-lannion.fr' },
        { id: 'bg1', label: 'Couleur d\u00e9but', type: 'color', value: '#8B1538' },
        { id: 'bg2', label: 'Couleur fin', type: 'color', value: '#1a0510' },
      ],
      render: function(v) {
        return '<div class="social-card tpl-estimation" style="background:linear-gradient(135deg,' + v.bg1 + ' 0%,' + v.bg2 + ' 100%);">' +
          '<div class="badge-top">' + esc(v.badge) + '</div>' +
          '<div class="big-price">' + esc(v.prix) + '</div>' +
          '<div class="price-label">' + esc(v.label) + '</div>' +
          '<div class="divider"></div>' +
          '<div class="quartier">' + esc(v.quartier) + '</div>' +
          '<div class="city">Lannion &mdash; Bretagne</div>' +
          '<div class="logo-bottom">' + esc(v.brand) + '</div>' +
        '</div>';
      }
    },
    conseil: {
      fields: [
        { id: 'number', label: 'Num\u00e9ro du conseil', type: 'text', value: '01' },
        { id: 'title', label: 'Titre', type: 'text', value: 'V\u00e9rifiez le DPE avant d\u2019acheter' },
        { id: 'text', label: 'Texte', type: 'textarea', value: 'Le Diagnostic de Performance \u00c9nerg\u00e9tique (DPE) impacte directement la valeur de votre bien. Un logement class\u00e9 F ou G peut perdre jusqu\u2019\u00e0 15% de sa valeur.' },
        { id: 'tag', label: 'Tag', type: 'text', value: 'CONSEIL IMMO' },
        { id: 'brand', label: 'Marque', type: 'text', value: 'Estimation Immobilier Lannion' },
        { id: 'url', label: 'URL', type: 'text', value: 'estimation-immobilier-lannion.fr' },
        { id: 'cta', label: 'CTA', type: 'text', value: 'Estimez votre bien gratuitement \u2192' },
      ],
      render: function(v) {
        return '<div class="social-card tpl-conseil">' +
          '<div class="header-bar"><span class="brand">' + esc(v.brand) + '</span><span class="tag">' + esc(v.tag) + '</span></div>' +
          '<div class="content-area">' +
            '<div class="tip-number">' + esc(v.number) + '</div>' +
            '<div class="tip-title">' + esc(v.title) + '</div>' +
            '<div class="tip-text">' + esc(v.text) + '</div>' +
          '</div>' +
          '<div class="footer-bar"><span class="url">' + esc(v.url) + '</span><span class="cta">' + esc(v.cta) + '</span></div>' +
        '</div>';
      }
    },
    stat: {
      fields: [
        { id: 'label', label: 'Label', type: 'text', value: 'MARCH\u00c9 IMMOBILIER LANNION' },
        { id: 'value', label: 'Chiffre', type: 'text', value: '+12%' },
        { id: 'unit', label: 'Unit\u00e9', type: 'text', value: 'en 2 ans' },
        { id: 'desc', label: 'Description', type: 'textarea', value: 'Les prix de l\u2019immobilier \u00e0 Lannion ont progress\u00e9 de 12% sur les deux derni\u00e8res ann\u00e9es.' },
        { id: 'brand', label: 'Marque', type: 'text', value: 'estimation-immobilier-lannion.fr' },
        { id: 'bg1', label: 'Couleur d\u00e9but', type: 'color', value: '#0f172a' },
        { id: 'bg2', label: 'Couleur fin', type: 'color', value: '#1e293b' },
      ],
      render: function(v) {
        return '<div class="social-card tpl-stat" style="background:linear-gradient(180deg,' + v.bg1 + ' 0%,' + v.bg2 + ' 100%);">' +
          '<div class="stat-label">' + esc(v.label) + '</div>' +
          '<div class="stat-value">' + esc(v.value) + '</div>' +
          '<div class="stat-unit">' + esc(v.unit) + '</div>' +
          '<div class="stat-desc">' + esc(v.desc) + '</div>' +
          '<div class="stat-brand">' + esc(v.brand) + '</div>' +
        '</div>';
      }
    },
    story: {
      fields: [
        { id: 'brand', label: 'Marque', type: 'text', value: 'ESTIMATION IMMOBILIER LANNION' },
        { id: 'emoji', label: 'Emoji / Ic\u00f4ne', type: 'text', value: '\uD83C\uDFE0' },
        { id: 'title', label: 'Titre', type: 'text', value: 'Combien vaut votre bien \u00e0 Lannion ?' },
        { id: 'text', label: 'Texte', type: 'textarea', value: 'Obtenez une estimation gratuite et pr\u00e9cise en quelques minutes.' },
        { id: 'cta', label: 'Bouton CTA', type: 'text', value: 'Estimer mon bien' },
        { id: 'url', label: 'URL', type: 'text', value: 'estimation-immobilier-lannion.fr' },
        { id: 'bg1', label: 'Couleur d\u00e9but', type: 'color', value: '#8B1538' },
        { id: 'bg2', label: 'Couleur fin', type: 'color', value: '#1a0510' },
      ],
      render: function(v) {
        return '<div class="social-card tpl-story" style="background:linear-gradient(180deg,' + v.bg1 + ' 0%,' + v.bg2 + ' 100%);">' +
          '<div class="story-top"><div class="story-brand">' + esc(v.brand) + '</div></div>' +
          '<div class="story-middle">' +
            '<div class="story-emoji">' + v.emoji + '</div>' +
            '<div class="story-title">' + esc(v.title) + '</div>' +
            '<div class="story-text">' + esc(v.text) + '</div>' +
          '</div>' +
          '<div class="story-bottom">' +
            '<div class="story-cta">' + esc(v.cta) + '</div>' +
            '<div class="story-url">' + esc(v.url) + '</div>' +
          '</div>' +
        '</div>';
      }
    },
    paysage: {
      fields: [
        { id: 'tag', label: 'Tag line', type: 'text', value: 'ESTIMATION GRATUITE' },
        { id: 'title', label: 'Titre', type: 'text', value: 'Estimez votre bien immobilier \u00e0 Lannion' },
        { id: 'text', label: 'Sous-texte', type: 'text', value: 'R\u00e9sultat imm\u00e9diat, sans engagement' },
        { id: 'bigValue', label: 'Chiffre droite', type: 'text', value: '4 800\u20ac' },
        { id: 'bigLabel', label: 'Label droite', type: 'text', value: 'Prix moyen / m\u00b2' },
        { id: 'brand', label: 'Marque', type: 'text', value: 'estimation-immobilier-lannion.fr' },
        { id: 'bg1', label: 'Couleur d\u00e9but', type: 'color', value: '#8B1538' },
        { id: 'bg2', label: 'Couleur fin', type: 'color', value: '#2d0a18' },
      ],
      render: function(v) {
        return '<div class="social-card tpl-paysage" style="background:linear-gradient(135deg,' + v.bg1 + ' 0%,' + v.bg2 + ' 100%);">' +
          '<div class="left-panel">' +
            '<div class="tag-line">' + esc(v.tag) + '</div>' +
            '<div class="main-title">' + esc(v.title) + '</div>' +
            '<div class="sub-text">' + esc(v.text) + '</div>' +
          '</div>' +
          '<div class="right-panel">' +
            '<div class="right-big">' + esc(v.bigValue) + '</div>' +
            '<div class="right-label">' + esc(v.bigLabel) + '</div>' +
            '<div class="right-brand">' + esc(v.brand) + '</div>' +
          '</div>' +
        '</div>';
      }
    }
  };

  function esc(str) {
    var div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  }

  function getValues() {
    var tpl = templates[currentTemplate];
    var values = {};
    tpl.fields.forEach(function(f) {
      var el = document.getElementById('field-' + f.id);
      values[f.id] = el ? el.value : f.value;
    });
    return values;
  }

  function renderConfig() {
    var tpl = templates[currentTemplate];
    var html = '';
    tpl.fields.forEach(function(f) {
      html += '<label>' + f.label;
      if (f.type === 'select') {
        html += '<select id="field-' + f.id + '">';
        f.options.forEach(function(o) {
          html += '<option' + (o === f.value ? ' selected' : '') + '>' + esc(o) + '</option>';
        });
        html += '</select>';
      } else if (f.type === 'textarea') {
        html += '<textarea id="field-' + f.id + '">' + esc(f.value) + '</textarea>';
      } else {
        html += '<input type="' + f.type + '" id="field-' + f.id + '" value="' + esc(f.value) + '">';
      }
      html += '</label>';
    });
    configFields.innerHTML = html;
  }

  function renderPreview() {
    var tpl = templates[currentTemplate];
    var values = getValues();
    canvas.innerHTML = tpl.render(values);

    // Scale to fit preview area
    var card = canvas.querySelector('.social-card');
    if (card) {
      var previewArea = document.getElementById('preview-area');
      var scaleX = (previewArea.clientWidth - 40) / card.offsetWidth;
      var scaleY = (previewArea.clientHeight - 40) / card.offsetHeight;
      var scale = Math.min(scaleX, scaleY, 0.5);
      card.style.transform = 'scale(' + scale + ')';
      card.style.transformOrigin = 'center center';
      canvas.style.width = (card.offsetWidth * scale) + 'px';
      canvas.style.height = (card.offsetHeight * scale) + 'px';
    }
  }

  function switchTemplate(name) {
    currentTemplate = name;
    document.querySelectorAll('.social-tab').forEach(function(t) {
      t.classList.toggle('active', t.dataset.template === name);
    });
    renderConfig();
    renderPreview();
  }

  // Tab clicks
  document.querySelectorAll('.social-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
      switchTemplate(this.dataset.template);
    });
  });

  // Update button
  document.getElementById('btn-update').addEventListener('click', renderPreview);

  // Download PNG
  document.getElementById('btn-download').addEventListener('click', function() {
    var card = canvas.querySelector('.social-card');
    if (!card) return;

    var status = document.getElementById('action-status');
    status.textContent = 'G\u00e9n\u00e9ration en cours...';

    // Reset scale for capture
    var prevTransform = card.style.transform;
    var prevOrigin = card.style.transformOrigin;
    card.style.transform = 'none';
    card.style.transformOrigin = '';
    canvas.style.width = '';
    canvas.style.height = '';

    html2canvas(card, {
      scale: 2,
      useCORS: true,
      allowTaint: true,
      backgroundColor: null,
      width: card.scrollWidth,
      height: card.scrollHeight,
    }).then(function(c) {
      card.style.transform = prevTransform;
      card.style.transformOrigin = prevOrigin;
      renderPreview();

      var link = document.createElement('a');
      link.download = 'social-' + currentTemplate + '-' + Date.now() + '.png';
      link.href = c.toDataURL('image/png');
      link.click();
      status.textContent = 'Image t\u00e9l\u00e9charg\u00e9e !';
      setTimeout(function() { status.textContent = ''; }, 3000);
    }).catch(function(err) {
      card.style.transform = prevTransform;
      card.style.transformOrigin = prevOrigin;
      renderPreview();
      status.textContent = 'Erreur: ' + err.message;
    });
  });

  // Save to server
  document.getElementById('btn-save').addEventListener('click', function() {
    var card = canvas.querySelector('.social-card');
    if (!card) return;

    var status = document.getElementById('action-status');
    status.textContent = 'Sauvegarde en cours...';

    var prevTransform = card.style.transform;
    var prevOrigin = card.style.transformOrigin;
    card.style.transform = 'none';
    card.style.transformOrigin = '';
    canvas.style.width = '';
    canvas.style.height = '';

    html2canvas(card, {
      scale: 2,
      useCORS: true,
      allowTaint: true,
      backgroundColor: null,
      width: card.scrollWidth,
      height: card.scrollHeight,
    }).then(function(c) {
      card.style.transform = prevTransform;
      card.style.transformOrigin = prevOrigin;
      renderPreview();

      var dataUrl = c.toDataURL('image/png');
      var filename = 'social-' + currentTemplate + '-' + new Date().toISOString().slice(0,19).replace(/[T:]/g, '-') + '.png';

      fetch('/admin/social-images/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image_data: dataUrl, filename: filename })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          status.textContent = 'Sauvegard\u00e9 : ' + data.filename;
          setTimeout(function() { location.reload(); }, 1500);
        } else {
          status.textContent = 'Erreur: ' + (data.error || 'inconnue');
        }
      })
      .catch(function(err) {
        status.textContent = 'Erreur r\u00e9seau: ' + err.message;
      });
    }).catch(function(err) {
      card.style.transform = prevTransform;
      card.style.transformOrigin = prevOrigin;
      renderPreview();
      status.textContent = 'Erreur: ' + err.message;
    });
  });

  // Initial render
  renderConfig();
  renderPreview();
})();
</script>
