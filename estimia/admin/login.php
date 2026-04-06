<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!empty($_SESSION['admin_logged'])) {
    redirect('index.php');
}

$errorMessage = '';

if (isPost()) {
    $email = sanitize($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errorMessage = 'Veuillez renseigner votre email et mot de passe.';
    } else {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, (string) $admin['password_hash'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_email'] = (string) $admin['email'];
                redirect('index.php');
            }

            $errorMessage = 'Identifiants invalides.';
        } catch (Throwable $e) {
            $errorMessage = 'Erreur de connexion. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion admin | EstimIA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-gray-50 to-white font-sans text-gray-900">
<div class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md rounded-3xl border border-gray-100 bg-white p-8 shadow-xl">
        <div class="mb-8 text-center">
            <p class="text-2xl font-extrabold text-primary">EstimIA</p>
            <h1 class="mt-3 text-xl font-bold">Connexion administrateur</h1>
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-5">
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" name="email" type="email" required class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Mot de passe</label>
                <input id="password" name="password" type="password" required class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 outline-none transition-all focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
            </div>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-semibold text-white shadow-lg transition-all hover:-translate-y-0.5 hover:shadow-xl">
                Se connecter
            </button>
        </form>
    </div>
</div>
</body>
</html>
