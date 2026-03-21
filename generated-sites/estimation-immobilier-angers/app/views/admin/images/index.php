<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour CMS</a>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Générateur d'images IA</h1>
    <p style="color:#6b6459;margin-bottom:1.5rem;font-size:0.9rem;">Générez des visuels immobiliers avec l'IA (gpt-image-1) pour vos articles de blog.</p>

    <?php if (($message ?? '') !== ''): ?>
      <p class="success"><?= e((string) $message) ?></p>
    <?php endif; ?>
    <?php if (($error ?? '') !== ''): ?>
      <p class="alert"><?= e((string) $error) ?></p>
    <?php endif; ?>

    <?php if (!empty($generated)): ?>
      <div class="card" style="margin-bottom:1.5rem;text-align:center;">
        <h2>Image générée</h2>
        <img src="<?= e($generated['url']) ?>" alt="Image IA" style="max-width:100%;height:auto;border-radius:8px;margin:1rem 0;">
        <div style="margin-top:1rem;">
          <p><strong>Fichier :</strong> <?= e($generated['filename']) ?></p>
          <p><strong>Taille :</strong> <?= number_format(($generated['size'] ?? 0) / 1024, 1) ?> Ko</p>
          <label style="display:block;margin-top:0.75rem;">Balise HTML (copier-coller dans un article) :
            <input type="text" value="<?= e($generated['html_tag']) ?>" readonly onclick="this.select()" style="width:100%;font-family:monospace;font-size:0.85rem;">
          </label>
        </div>
      </div>
    <?php endif; ?>

    <div class="card" style="margin-bottom:2rem;">
      <h2>Nouvelle image</h2>
      <form method="post" action="/admin/images/generate" class="form-grid" id="image-form">

        <fieldset style="border:1px solid var(--border, #e8dfd7);border-radius:8px;padding:1rem;margin-bottom:1rem;">
          <legend style="font-weight:600;">Mode de prompt</legend>
          <label style="display:inline-flex;align-items:center;gap:0.4rem;margin-right:1.5rem;">
            <input type="radio" name="prompt_mode" value="seo" id="mode-seo" checked> Prompt SEO immobilier
          </label>
          <label style="display:inline-flex;align-items:center;gap:0.4rem;">
            <input type="radio" name="prompt_mode" value="custom" id="mode-custom"> Prompt libre
          </label>
        </fieldset>

        <div id="seo-fields">
          <label>Type d'image
            <select name="seo_type" id="seo-type">
              <?php foreach ($promptTypes as $key => $label): ?>
                <option value="<?= e($key) ?>"><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>Quartier (optionnel)
            <select name="quartier">
              <option value="">Angers centre</option>
              <option value="Chartrons">Chartrons</option>
              <option value="Saint-Pierre">Saint-Pierre</option>
              <option value="Saint-Michel">Saint-Michel</option>
              <option value="Caudéran">Caudéran</option>
              <option value="Bastide">Bastide</option>
              <option value="Mériadeck">Mériadeck</option>
            </select>
          </label>

          <label>Style (optionnel)
            <input type="text" name="style" placeholder="ex: moderne et lumineux, haussmannien classique..." value="">
          </label>

          <div style="margin:0.75rem 0;">
            <button type="button" class="btn btn-small btn-ghost" id="preview-prompt-btn">Prévisualiser le prompt</button>
            <p id="prompt-preview" style="margin-top:0.5rem;padding:0.75rem;background:var(--bg, #faf9f7);border-radius:6px;font-style:italic;display:none;"></p>
          </div>
        </div>

        <div id="custom-fields" style="display:none;">
          <label>Prompt libre
            <textarea name="prompt" rows="4" placeholder="Décrivez l'image souhaitée..."><?= e((string) ($lastPrompt ?? '')) ?></textarea>
          </label>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
          <label style="flex:1;min-width:200px;">Taille
            <select name="size">
              <option value="1024x1024" <?= (($lastSize ?? '1024x1024') === '1024x1024') ? 'selected' : '' ?>>1024x1024 (carré)</option>
              <option value="1536x1024" <?= (($lastSize ?? '') === '1536x1024') ? 'selected' : '' ?>>1536x1024 (paysage)</option>
              <option value="1024x1536" <?= (($lastSize ?? '') === '1024x1536') ? 'selected' : '' ?>>1024x1536 (portrait)</option>
            </select>
          </label>

          <label style="flex:1;min-width:200px;">Qualité
            <select name="quality">
              <option value="low" <?= (($lastQuality ?? '') === 'low') ? 'selected' : '' ?>>Basse (rapide)</option>
              <option value="medium" <?= (($lastQuality ?? 'medium') === 'medium') ? 'selected' : '' ?>>Moyenne</option>
              <option value="high" <?= (($lastQuality ?? '') === 'high') ? 'selected' : '' ?>>Haute (lent)</option>
            </select>
          </label>
        </div>

        <button class="btn" type="submit" id="generate-btn">Générer l'image</button>
        <p style="font-size:0.85rem;color:var(--muted);">La génération peut prendre 30 à 90 secondes selon la qualité.</p>
      </form>
    </div>

    <?php if (!empty($images)): ?>
      <div class="card">
        <h2>Images générées (<?= count($images) ?>)</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:1rem;margin-top:1rem;">
          <?php foreach ($images as $img): ?>
            <div style="border:1px solid var(--border, #e8dfd7);border-radius:8px;overflow:hidden;">
              <img src="<?= e($img['url']) ?>" alt="<?= e($img['filename']) ?>" style="width:100%;height:180px;object-fit:cover;" loading="lazy">
              <div style="padding:0.75rem;">
                <p style="font-size:0.8rem;word-break:break-all;margin-bottom:0.25rem;"><?= e($img['filename']) ?></p>
                <p style="font-size:0.75rem;color:var(--muted);"><?= e($img['created_at']) ?> &mdash; <?= number_format(($img['size'] ?? 0) / 1024, 1) ?> Ko</p>
                <div style="display:flex;gap:0.5rem;margin-top:0.5rem;">
                  <button type="button" class="btn btn-small btn-ghost copy-html-btn" data-url="<?= e($img['url']) ?>" data-alt="Image immobilière IA">Copier HTML</button>
                  <form method="post" action="/admin/images/delete" style="margin:0;" onsubmit="return confirm('Supprimer cette image ?');">
                    <input type="hidden" name="filename" value="<?= e($img['filename']) ?>">
                    <button type="submit" class="btn btn-small" style="background:var(--danger, #e24b4a);border-color:var(--danger, #e24b4a);">Suppr.</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
</div>

<script>
(function() {
  const modeRadios = document.querySelectorAll('input[name="prompt_mode"]');
  const seoFields = document.getElementById('seo-fields');
  const customFields = document.getElementById('custom-fields');
  const previewBtn = document.getElementById('preview-prompt-btn');
  const previewEl = document.getElementById('prompt-preview');
  const generateBtn = document.getElementById('generate-btn');

  function toggleMode() {
    const mode = document.querySelector('input[name="prompt_mode"]:checked').value;
    seoFields.style.display = mode === 'seo' ? 'block' : 'none';
    customFields.style.display = mode === 'custom' ? 'block' : 'none';
  }
  modeRadios.forEach(function(r) { r.addEventListener('change', toggleMode); });

  if (previewBtn) {
    previewBtn.addEventListener('click', function() {
      var type = document.querySelector('[name="seo_type"]').value;
      var quartier = document.querySelector('[name="quartier"]').value;
      var style = document.querySelector('[name="style"]').value;
      var url = '/admin/api/images/seo-prompt?type=' + encodeURIComponent(type)
        + '&quartier=' + encodeURIComponent(quartier)
        + '&style=' + encodeURIComponent(style);

      fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
          previewEl.textContent = data.prompt || 'Erreur';
          previewEl.style.display = 'block';
        })
        .catch(function() {
          previewEl.textContent = 'Erreur de chargement.';
          previewEl.style.display = 'block';
        });
    });
  }

  var form = document.getElementById('image-form');
  if (form) {
    form.addEventListener('submit', function() {
      generateBtn.disabled = true;
      generateBtn.textContent = 'Génération en cours...';
    });
  }

  document.querySelectorAll('.copy-html-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var tag = '<img src="' + btn.dataset.url + '" alt="' + btn.dataset.alt + '" loading="lazy">';
      if (navigator.clipboard) {
        navigator.clipboard.writeText(tag).then(function() {
          btn.textContent = 'Copié !';
          setTimeout(function() { btn.textContent = 'Copier HTML'; }, 2000);
        });
      } else {
        var input = document.createElement('input');
        input.value = tag;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        btn.textContent = 'Copié !';
        setTimeout(function() { btn.textContent = 'Copier HTML'; }, 2000);
      }
    });
  });
})();
</script>
