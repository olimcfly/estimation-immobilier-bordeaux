<style>
  .history-page h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
  .history-page .subtitle { color: var(--admin-muted); margin-bottom: 1.5rem; font-size: 0.9rem; }

  .history-table {
    width: 100%; border-collapse: collapse;
    background: var(--admin-surface); border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius); overflow: hidden;
  }
  .history-table th {
    text-align: left; padding: 0.75rem 1rem; font-size: 0.8rem;
    color: var(--admin-muted); text-transform: uppercase; letter-spacing: 0.05em;
    background: var(--admin-bg); border-bottom: 1px solid var(--admin-border);
  }
  .history-table td {
    padding: 0.75rem 1rem; font-size: 0.85rem;
    border-bottom: 1px solid var(--admin-border);
  }
  .history-table tr:last-child td { border-bottom: none; }
  .history-table tr:hover td { background: rgba(139, 21, 56, 0.03); }

  .history-thumb {
    width: 60px; height: 40px; object-fit: cover; border-radius: 4px;
    border: 1px solid var(--admin-border);
  }
  .template-badge {
    display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px;
    font-size: 0.75rem; font-weight: 600;
  }
  .template-badge.estimation { background: rgba(139,21,56,0.1); color: #8B1538; }
  .template-badge.conseil { background: rgba(212,175,55,0.15); color: #b8960a; }
  .template-badge.stat { background: rgba(15,23,42,0.1); color: #0f172a; }
  .template-badge.story { background: rgba(139,21,56,0.1); color: #8B1538; }
  .template-badge.paysage { background: rgba(45,10,24,0.1); color: #2d0a18; }

  .history-actions { display: flex; gap: 0.5rem; }
  .btn-sm {
    padding: 0.3rem 0.6rem; border-radius: 4px; border: none;
    font-size: 0.75rem; font-weight: 600; cursor: pointer;
    font-family: inherit; transition: all 0.15s;
    display: inline-flex; align-items: center; gap: 0.3rem;
  }
  .btn-sm-primary { background: var(--admin-primary); color: #fff; }
  .btn-sm-primary:hover { opacity: 0.9; }
  .btn-sm-danger { background: var(--admin-danger); color: #fff; }
  .btn-sm-danger:hover { opacity: 0.9; }

  .empty-state {
    text-align: center; padding: 3rem; color: var(--admin-muted);
    background: var(--admin-surface); border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
  }
  .empty-state i { font-size: 2rem; margin-bottom: 0.75rem; display: block; }

  .stats-bar {
    display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;
  }
  .stat-card {
    background: var(--admin-surface); border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius); padding: 1rem 1.25rem;
    flex: 1; min-width: 150px;
  }
  .stat-card .stat-number { font-size: 1.5rem; font-weight: 700; color: var(--admin-primary); }
  .stat-card .stat-label { font-size: 0.8rem; color: var(--admin-muted); margin-top: 0.25rem; }
</style>

<div class="history-page">
  <h1><i class="fas fa-history" style="color:var(--admin-primary);margin-right:0.5rem;"></i>Historique des g&eacute;n&eacute;rations</h1>
  <p class="subtitle">Toutes les images sociales g&eacute;n&eacute;r&eacute;es, avec m&eacute;tadonn&eacute;es et actions rapides.</p>

  <!-- Stats summary -->
  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-number"><?= count($images ?? []) ?></div>
      <div class="stat-label">Images g&eacute;n&eacute;r&eacute;es</div>
    </div>
    <div class="stat-card">
      <div class="stat-number" id="total-size">--</div>
      <div class="stat-label">Taille totale</div>
    </div>
    <div class="stat-card">
      <div class="stat-number" id="most-used-template">--</div>
      <div class="stat-label">Template favori</div>
    </div>
  </div>

  <?php if (empty($images)): ?>
    <div class="empty-state">
      <i class="fas fa-images"></i>
      <p>Aucune image g&eacute;n&eacute;r&eacute;e pour le moment.</p>
      <p><a href="/admin/social-images" style="color:var(--admin-primary);">Cr&eacute;er une image</a></p>
    </div>
  <?php else: ?>
    <table class="history-table">
      <thead>
        <tr>
          <th>Aper&ccedil;u</th>
          <th>Date</th>
          <th>Template</th>
          <th>Fichier</th>
          <th>Taille</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($images as $img):
          // Extract template type from filename (e.g., social-estimation-2026-03-21.png)
          $template = 'inconnu';
          if (preg_match('/social-(\w+)-/', $img['filename'], $m)) {
              $template = $m[1];
          }
          $sizeKo = number_format(($img['size'] ?? 0) / 1024, 1);
        ?>
          <tr>
            <td>
              <img class="history-thumb"
                   src="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>"
                   loading="lazy">
            </td>
            <td><?= htmlspecialchars($img['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><span class="template-badge <?= htmlspecialchars($template, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($template), ENT_QUOTES, 'UTF-8') ?></span></td>
            <td style="font-family:monospace;font-size:0.8rem;"><?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $sizeKo ?> Ko</td>
            <td>
              <div class="history-actions">
                <a href="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>" download class="btn-sm btn-sm-primary">
                  <i class="fas fa-download"></i> T&eacute;l&eacute;charger
                </a>
                <form method="post" action="/admin/social-images/delete" style="margin:0;" onsubmit="return confirm('Supprimer cette image ?');">
                  <input type="hidden" name="filename" value="<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" class="btn-sm btn-sm-danger"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script>
(function() {
  var images = <?= json_encode($images ?? [], JSON_UNESCAPED_UNICODE) ?>;

  // Calculate total size
  var totalBytes = images.reduce(function(sum, img) { return sum + (img.size || 0); }, 0);
  var totalEl = document.getElementById('total-size');
  if (totalBytes > 1048576) {
    totalEl.textContent = (totalBytes / 1048576).toFixed(1) + ' Mo';
  } else {
    totalEl.textContent = (totalBytes / 1024).toFixed(1) + ' Ko';
  }

  // Find most used template
  var counts = {};
  images.forEach(function(img) {
    var match = img.filename.match(/social-(\w+)-/);
    if (match) {
      counts[match[1]] = (counts[match[1]] || 0) + 1;
    }
  });
  var best = '--';
  var bestCount = 0;
  Object.keys(counts).forEach(function(k) {
    if (counts[k] > bestCount) {
      bestCount = counts[k];
      best = k.charAt(0).toUpperCase() + k.slice(1);
    }
  });
  document.getElementById('most-used-template').textContent = best;
})();
</script>
