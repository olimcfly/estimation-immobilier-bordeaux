<section class="card success">
  <h2>Merci, votre demande a bien été enregistrée.</h2>
  <p>Référence lead: <strong>#<?= e((string) $leadId) ?></strong></p>
  <p>Score commercial: <strong><?= e((string) $temperature) ?></strong></p>
  <p><a href="/leads?score=<?= e((string) $temperature) ?>">Voir les leads <?= e((string) $temperature) ?></a></p>
  <p><a href="/estimation">Faire une nouvelle estimation</a></p>
</section>
