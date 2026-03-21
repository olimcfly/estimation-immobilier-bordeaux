<section class="card success">
  <h2>Merci, votre demande a bien été enregistrée.</h2>
  <p>Référence lead: <strong>#<?= e((string) $leadId) ?></strong></p>
  <p>Score commercial: <strong><?= e((string) $temperature) ?></strong></p>
</section>

<section class="card">
  <h3>Fiche complète du lead</h3>
  <div class="lead-sheet-grid">
    <div>
      <span class="muted">Nom</span>
      <p><?= e((string) $lead['nom']) ?></p>
    </div>
    <div>
      <span class="muted">Email</span>
      <p><?= e((string) $lead['email']) ?></p>
    </div>
    <div>
      <span class="muted">Téléphone</span>
      <p><?= e((string) $lead['telephone']) ?></p>
    </div>
    <div>
      <span class="muted">Adresse du bien</span>
      <p><?= e((string) $lead['adresse']) ?></p>
    </div>
    <div>
      <span class="muted">Ville</span>
      <p><?= e((string) $lead['ville']) ?></p>
    </div>
    <div>
      <span class="muted">Estimation moyenne</span>
      <p><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</p>
    </div>
    <div>
      <span class="muted">Urgence</span>
      <p><?= e((string) $lead['urgence']) ?></p>
    </div>
    <div>
      <span class="muted">Motivation</span>
      <p><?= e((string) $lead['motivation']) ?></p>
    </div>
    <div>
      <span class="muted">Statut</span>
      <p><?= e((string) $lead['statut']) ?></p>
    </div>
  </div>

  <div class="lead-notes">
    <span class="muted">Notes</span>
    <p><?= nl2br(e((string) ($lead['notes'] !== '' ? $lead['notes'] : 'Aucune note renseignée.'))) ?></p>
  </div>

  <p><a href="/estimation">Faire une nouvelle estimation</a></p>
</section>
