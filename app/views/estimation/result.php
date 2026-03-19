<section class="card">
  <h2>Résultat d'estimation</h2>
  <div class="grid-3">
    <div><strong>Basse</strong><p><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> €</p></div>
    <div><strong>Moyenne</strong><p><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> €</p></div>
    <div><strong>Haute</strong><p><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> €</p></div>
  </div>
  <p>Prix moyen au m²: <strong><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> €/m²</strong></p>
</section>

<section class="card">
  <h3>Recevoir un accompagnement</h3>
  <form action="/lead" method="post">
    <input type="hidden" name="ville" value="<?= e((string) $estimate['city']) ?>">
    <input type="hidden" name="estimation" value="<?= e((string) $estimate['estimated_mid']) ?>">

    <label>Nom
      <input type="text" name="nom" required>
    </label>

    <label>Email
      <input type="email" name="email" required>
    </label>

    <label>Téléphone
      <input type="text" name="telephone" required>
    </label>

    <label>Adresse du bien
      <input type="text" name="adresse" required>
    </label>

    <label>Urgence
      <select name="urgence" required>
        <option value="rapide">Rapide</option>
        <option value="moyen">Moyen</option>
        <option value="long">Long terme</option>
      </select>
    </label>

    <label>Motivation
      <select name="motivation" required>
        <option value="vente">Vente</option>
        <option value="succession">Succession</option>
        <option value="divorce">Divorce</option>
        <option value="investissement">Investissement</option>
      </select>
    </label>

    <label>Notes
      <textarea name="notes" rows="4" placeholder="Ajoutez un contexte utile (travaux, disponibilité, contraintes, etc.)"></textarea>
    </label>

    <button type="submit">Enregistrer mon lead</button>
  </form>
</section>
