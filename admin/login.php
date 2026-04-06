<?php

declare(strict_types=1);

require_once __DIR__ . '/auth-utils.php';

session_start();

if (!empty($_SESSION['admin_logged']) && !empty($_SESSION['admin_id'])) {
    header('Location: /admin/index.php');
    exit;
}

$error = null;
$info = null;
$emailValue = '';

try {
    $db = Database::getConnection();
    adminEnsureTables($db);

    $adminCount = (int) $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($adminCount === 0) {
        header('Location: /admin/onboarding.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $emailValue = mb_strtolower(trim((string) ($_POST['email'] ?? '')));

        if ($emailValue === '' || !filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
            $error = 'Veuillez saisir une adresse email valide.';
        } else {
            $stmt = $db->prepare('SELECT id, prenom, nom, email FROM admins WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $emailValue]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($admin)) {
                $error = 'Aucun administrateur trouvé avec cet email.';
            } else {
                $result = adminGenerateAndSendCode($db, $admin);

                $_SESSION['admin_pending_id'] = (int) $admin['id'];
                $_SESSION['admin_pending_email'] = (string) $admin['email'];
                $_SESSION['admin_pending_name'] = trim((string) $admin['prenom'] . ' ' . (string) $admin['nom']);
                $_SESSION['admin_code_sent_at'] = time();

                if (!$result['sent']) {
                    $error = 'Le code a été généré, mais l\'email n\'a pas pu être envoyé. Vérifiez la configuration SMTP/mail().' ;
                } else {
                    $info = 'Un code de vérification vient d\'être envoyé.';
                }

                header('Location: /admin/verify-code.php');
                exit;
            }
        }
    }
} catch (Throwable $exception) {
    $error = 'Impossible de préparer la connexion administrateur. Vérifiez la base de données.';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin | EstimIA Bordeaux</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-blue-100 via-sky-50 to-white text-slate-900">
<div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6 py-16">
    <div class="grid w-full max-w-5xl gap-8 lg:grid-cols-2 lg:items-center">
        <section class="hidden rounded-3xl bg-blue-700/95 p-10 text-white shadow-2xl ring-1 ring-blue-500/40 lg:block">
            <p class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide">Admin sécurisé</p>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight">Bienvenue sur l'espace administration EstimIA</h1>
            <p class="mt-4 text-blue-100">Connexion sans mot de passe : saisissez votre email et recevez un code à 6 chiffres.</p>
        </section>

        <section class="w-full rounded-3xl bg-white p-8 shadow-2xl ring-1 ring-slate-200 sm:p-10">
            <h2 class="text-3xl font-bold text-blue-950">Connexion administrateur</h2>
            <p class="mt-2 text-sm text-slate-600">Entrez votre email pour recevoir un code de sécurité.</p>

            <?php if ($error !== null): ?>
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($info !== null): ?>
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?php echo htmlspecialchars($info, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-8 space-y-5">
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email administrateur</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="<?php echo htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Envoyer mon code
                </button>
            </form>
        </section>
    </div>
</div>
</body>
</html>
