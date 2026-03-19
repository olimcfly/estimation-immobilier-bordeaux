<section class="card">
  <h2>Leads enregistrés</h2>
  <p class="muted">Filtrer les leads par température commerciale.</p>

  <form method="get" action="/leads" class="filters-row">
    <label>
      Température
      <select name="score" onchange="this.form.submit()">
        <option value="">Tous</option>
        <option value="chaud" <?= ($scoreFilter ?? null) === 'chaud' ? 'selected' : '' ?>>Chaud</option>
        <option value="tiede" <?= ($scoreFilter ?? null) === 'tiede' ? 'selected' : '' ?>>Tiède</option>
        <option value="froid" <?= ($scoreFilter ?? null) === 'froid' ? 'selected' : '' ?>>Froid</option>
      </select>
    </label>
    <noscript>
      <button type="submit" class="btn btn-small">Filtrer</button>
    </noscript>
  </form>

  <?php if (empty($leads ?? [])): ?>
    <p class="muted">Aucun lead trouvé pour ce filtre.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="leads-table">
        <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Ville</th>
          <th>Estimation</th>
          <th>Score</th>
          <th>Statut</th>
          <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead): ?>
          <tr>
            <td>#<?= e((string) $lead['id']) ?></td>
            <td><?= e((string) $lead['nom']) ?></td>
            <td><?= e((string) $lead['ville']) ?></td>
            <td><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
            <td><span class="badge badge-<?= e((string) $lead['score']) ?>"><?= e((string) $lead['score']) ?></span></td>
            <td><?= e((string) $lead['statut']) ?></td>
            <td><?= e((string) $lead['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
