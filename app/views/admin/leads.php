<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Admin</p>
      <h1>Leads</h1>
      <p class="muted">Liste des leads enregistrés depuis le formulaire d'estimation.</p>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="leads-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Ville</th>
              <th>Estimation</th>
              <th>Urgence</th>
              <th>Motivation</th>
              <th>Score</th>
              <th>Statut</th>
              <th>Créé le</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($leads ?? [])): ?>
              <tr>
                <td colspan="11" class="muted">Aucun lead pour le moment.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($leads as $lead): ?>
                <tr>
                  <td><?= e((string) $lead['id']) ?></td>
                  <td><?= e((string) $lead['nom']) ?></td>
                  <td><?= e((string) $lead['email']) ?></td>
                  <td><?= e((string) $lead['telephone']) ?></td>
                  <td><?= e((string) $lead['ville']) ?></td>
                  <td><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
                  <td><?= e((string) $lead['urgence']) ?></td>
                  <td><?= e((string) $lead['motivation']) ?></td>
                  <td><?= e((string) $lead['score']) ?></td>
                  <td><?= e((string) $lead['statut']) ?></td>
                  <td><?= e((string) $lead['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
