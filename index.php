<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkRateLimit('estimation', 20)) {
        http_response_code(429);
        die('Trop de demandes. Réessayez dans une heure.');
    }

    if (!verifyCsrf()) {
        http_response_code(403);
        die('Session expirée. Rechargez la page.');
    }

    $nom = clean($_POST['nom'] ?? '');
    $email = cleanEmail($_POST['email'] ?? '');
    $telephone = cleanPhone($_POST['telephone'] ?? '');
    $adresse = clean($_POST['adresse'] ?? '');
    $caracteristiques = clean($_POST['caracteristiques'] ?? '');
    $rgpdConsent = isset($_POST['rgpd_consent']) ? 1 : 0;

    $db = Database::getConnection();
    $stmt = $db->prepare(
        'INSERT INTO estimations (nom, email, telephone, adresse, caracteristiques, rgpd_consent, rgpd_consent_date, rgpd_ip)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $nom,
        $email,
        $telephone,
        $adresse,
        $caracteristiques,
        $rgpdConsent,
        $rgpdConsent ? date('Y-m-d H:i:s') : null,
        $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $message = 'Votre demande a bien été enregistrée.';
}
?>

<section class="bg-white rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-semibold mb-2">Estimation immobilière à Bordeaux</h1>
    <p class="text-gray-600 mb-6">Remplissez le formulaire pour recevoir une première estimation de votre bien.</p>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <?= csrfField() ?>
        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="nom" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Téléphone</label>
            <input name="telephone" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Adresse du bien</label>
            <input name="adresse" required class="w-full rounded border border-gray-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Caractéristiques</label>
            <textarea name="caracteristiques" required class="w-full rounded border border-gray-300 px-3 py-2"></textarea>
        </div>

        <label class="flex items-start gap-3 mt-4">
            <input type="checkbox" name="rgpd_consent" required class="mt-1 rounded border-gray-300">
            <span class="text-sm text-gray-600">
                J'accepte que mes données soient utilisées pour réaliser mon estimation immobilière et être recontacté(e) par un conseiller.
                <a href="/politique-confidentialite" class="text-primary underline" target="_blank">Politique de confidentialité</a>
            </span>
        </label>

        <button class="px-4 py-2 rounded bg-primary text-white">Demander mon estimation</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
