<?php $page_title = 'Calculatrice Immobilière Bordeaux - Estimation Rapide'; ?>
<?php require 'app/views/layouts/header.php'; ?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-calculator"></i> Outil gratuit</p>
      <h1>Calculatrice de prix immobilier</h1>
      <p class="lead">Estimez rapidement une valeur de vente à partir de la surface et du prix au m² à Bordeaux.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <article class="card" style="max-width: 760px; margin: 0 auto;">
      <form id="calculatorForm" class="contact-form" style="display: grid; gap: 1rem;">
        <div class="form-group">
          <label for="surface">Surface (m²)</label>
          <input id="surface" name="surface" type="number" min="1" step="1" placeholder="Ex: 78" required>
        </div>

        <div class="form-group">
          <label for="pricePerM2">Prix au m² (€)</label>
          <input id="pricePerM2" name="pricePerM2" type="number" min="1" step="50" placeholder="Ex: 4800" required>
        </div>

        <div class="form-group">
          <label for="adjustment">Ajustement marché (%)</label>
          <input id="adjustment" name="adjustment" type="number" min="0" max="30" step="0.5" value="5">
          <small style="color: var(--muted);">Permet de générer une fourchette basse/haute.</small>
        </div>

        <button type="submit" class="btn btn-primary" style="justify-content: center;">
          <i class="fas fa-bolt"></i> Calculer
        </button>
      </form>

      <div id="calculatorResult" class="card" style="margin-top: 1.5rem; display:none; background:#fdf7fa; border-color:#efd9e0;">
        <h2 style="margin-bottom:0.5rem;">Résultat estimé</h2>
        <p style="margin:0.35rem 0;"><strong>Prix central :</strong> <span id="medianPrice">-</span></p>
        <p style="margin:0.35rem 0;"><strong>Fourchette basse :</strong> <span id="lowPrice">-</span></p>
        <p style="margin:0.35rem 0;"><strong>Fourchette haute :</strong> <span id="highPrice">-</span></p>
      </div>
    </article>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('calculatorForm');
    const resultCard = document.getElementById('calculatorResult');

    const toCurrency = (value) => new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0,
    }).format(value);

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      const surface = Number(document.getElementById('surface').value);
      const pricePerM2 = Number(document.getElementById('pricePerM2').value);
      const adjustmentPct = Number(document.getElementById('adjustment').value) / 100;

      if (!surface || !pricePerM2) {
        return;
      }

      const median = surface * pricePerM2;
      const low = median * (1 - adjustmentPct);
      const high = median * (1 + adjustmentPct);

      document.getElementById('medianPrice').textContent = toCurrency(median);
      document.getElementById('lowPrice').textContent = toCurrency(low);
      document.getElementById('highPrice').textContent = toCurrency(high);
      resultCard.style.display = 'block';
    });
  });
</script>

<?php require 'app/views/layouts/footer.php'; ?>
