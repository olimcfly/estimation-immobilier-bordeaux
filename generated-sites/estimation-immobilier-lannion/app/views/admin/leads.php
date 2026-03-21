<div class="container">
    <div style="margin-bottom: 1.5rem;">
      <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Leads</h1>
      <p style="color: #6b6459; font-size: 0.9rem; margin: 0;">Liste des leads enregistrés depuis le formulaire d'estimation.</p>
    </div>

    <?php if (!empty($dbError ?? '')): ?>
      <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <?= e($dbError) ?>
      </div>
    <?php endif; ?>

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
