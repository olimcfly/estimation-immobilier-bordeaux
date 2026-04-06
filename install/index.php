<?php

declare(strict_types=1);

session_start();

$rootDir = dirname(__DIR__);
$configDir = $rootDir . '/config';
$configFile = $configDir . '/config.php';
$installHtaccess = __DIR__ . '/.htaccess';
$uploadDir = $rootDir . '/assets';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = (int) ($_POST['step'] ?? 1);

    if ($step >= 1 && $step <= 4) {
        $_SESSION['install_wizard'] = array_merge(
            $_SESSION['install_wizard'] ?? [],
            [
                'agence_nom' => trim((string) ($_POST['agence_nom'] ?? ($_SESSION['install_wizard']['agence_nom'] ?? ''))),
                'ville_principale' => trim((string) ($_POST['ville_principale'] ?? ($_SESSION['install_wizard']['ville_principale'] ?? ''))),
                'couleur' => trim((string) ($_POST['couleur'] ?? ($_SESSION['install_wizard']['couleur'] ?? '#1e3a5f'))),
                'email_reception' => trim((string) ($_POST['email_reception'] ?? ($_SESSION['install_wizard']['email_reception'] ?? ''))),
                'smtp_host' => trim((string) ($_POST['smtp_host'] ?? ($_SESSION['install_wizard']['smtp_host'] ?? ''))),
                'smtp_port' => (int) ($_POST['smtp_port'] ?? ($_SESSION['install_wizard']['smtp_port'] ?? 587)),
                'smtp_user' => trim((string) ($_POST['smtp_user'] ?? ($_SESSION['install_wizard']['smtp_user'] ?? ''))),
                'smtp_pass' => (string) ($_POST['smtp_pass'] ?? ($_SESSION['install_wizard']['smtp_pass'] ?? '')),
                'email_expediteur' => trim((string) ($_POST['email_expediteur'] ?? ($_SESSION['install_wizard']['email_expediteur'] ?? ''))),
                'h1_titre' => trim((string) ($_POST['h1_titre'] ?? ($_SESSION['install_wizard']['h1_titre'] ?? ''))),
                'sous_titre' => trim((string) ($_POST['sous_titre'] ?? ($_SESSION['install_wizard']['sous_titre'] ?? ''))),
                'meta_description' => trim((string) ($_POST['meta_description'] ?? ($_SESSION['install_wizard']['meta_description'] ?? ''))),
            ]
        );

        if ($step === 1 && isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                die('Impossible de créer le dossier assets/.');
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string) $finfo->file($_FILES['logo']['tmp_name']);
            $allowed = [
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];

            if (isset($allowed[$mime])) {
                $extension = $allowed[$mime];
                $target = $uploadDir . '/logo.' . $extension;
                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                    die('Impossible d\'enregistrer le logo uploadé.');
                }
                $_SESSION['install_wizard']['logo'] = 'assets/logo.' . $extension;
            }
        }

        if ($step === 2) {
            $rawCities = $_POST['villes'] ?? [];
            $cities = [];
            if (is_array($rawCities)) {
                foreach ($rawCities as $city) {
                    $city = trim((string) $city);
                    if ($city !== '') {
                        $cities[] = $city;
                    }
                }
            }
            $_SESSION['install_wizard']['villes'] = array_values(array_unique($cities));
        }

        header('Location: index.php?step=' . ($step + 1));
        exit;
    }

    if ($step === 5) {
        $wizard = $_SESSION['install_wizard'] ?? [];

        $agenceNom = trim((string) ($wizard['agence_nom'] ?? ''));
        $villePrincipale = trim((string) ($wizard['ville_principale'] ?? ''));
        $villes = $wizard['villes'] ?? [];

        if ($agenceNom === '' || $villePrincipale === '' || empty($villes)) {
            header('Location: index.php?step=1&error=missing_data');
            exit;
        }

        if (!is_dir($configDir) && !mkdir($configDir, 0755, true) && !is_dir($configDir)) {
            die('Impossible de créer le dossier config/.');
        }

        $config = [
            'installed' => true,
            'agence_nom' => $agenceNom,
            'ville_principale' => $villePrincipale,
            'logo' => (string) ($wizard['logo'] ?? ''),
            'couleur' => preg_match('/^#[0-9A-Fa-f]{6}$/', (string) ($wizard['couleur'] ?? '')) ? $wizard['couleur'] : '#1e3a5f',
            'email_reception' => (string) ($wizard['email_reception'] ?? ''),
            'smtp_host' => (string) ($wizard['smtp_host'] ?? ''),
            'smtp_port' => (int) ($wizard['smtp_port'] ?? 587),
            'smtp_user' => (string) ($wizard['smtp_user'] ?? ''),
            'smtp_pass' => (string) ($wizard['smtp_pass'] ?? ''),
            'email_expediteur' => (string) ($wizard['email_expediteur'] ?? ''),
            'h1_titre' => (string) ($wizard['h1_titre'] ?: ('Combien vaut votre bien à ' . $villePrincipale . ' ?')),
            'sous_titre' => (string) ($wizard['sous_titre'] ?: 'Obtenez une estimation instantanée basée sur les données du marché local.'),
            'meta_description' => (string) ($wizard['meta_description'] ?: ('Estimation gratuite à ' . $villePrincipale)),
            'villes' => array_values($villes),
        ];

        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configFile, $content);

        $htaccess = <<<HTACCESS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_URI} ^/install/?$
RewriteRule ^$ /index.php [R=302,L]
</IfModule>

<Files "index.php">
Order allow,deny
Deny from all
</Files>
HTACCESS;
        file_put_contents($installHtaccess, $htaccess);

        unset($_SESSION['install_wizard']);

        header('Location: /index.php');
        exit;
    }
}

if (is_file($configFile)) {
    $config = require $configFile;
    if (is_array($config) && !empty($config['installed'])) {
        header('Location: /index.php');
        exit;
    }
}

$step = max(1, min(5, (int) ($_GET['step'] ?? 1)));
$data = $_SESSION['install_wizard'] ?? [];

$villes = $data['villes'] ?? [''];
if ($villes === []) {
    $villes = [''];
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Assistant d'installation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="mx-auto max-w-3xl p-4 md:p-8">
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold">Installation de votre site d'estimation</h1>
        <p class="mt-2 text-sm text-slate-600">Étape <?= $step; ?>/5</p>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_data'): ?>
            <p class="mt-4 rounded-lg bg-red-100 px-3 py-2 text-sm text-red-700">Merci de compléter toutes les données obligatoires avant de générer le site.</p>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form class="mt-6 space-y-4" method="post" enctype="multipart/form-data">
                <input type="hidden" name="step" value="1">
                <label class="block text-sm font-medium">Nom de l'agence
                    <input name="agence_nom" value="<?= htmlspecialchars((string) ($data['agence_nom'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                </label>
                <label class="block text-sm font-medium">Ville principale ciblée
                    <input name="ville_principale" value="<?= htmlspecialchars((string) ($data['ville_principale'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                </label>
                <label class="block text-sm font-medium">Logo
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="mt-1 block w-full text-sm">
                </label>
                <label class="block text-sm font-medium">Couleur principale
                    <input type="color" name="couleur" value="<?= htmlspecialchars((string) ($data['couleur'] ?? '#1e3a5f'), ENT_QUOTES); ?>" class="mt-1 h-10 w-20 rounded border">
                </label>
                <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 2): ?>
            <form class="mt-6" method="post" id="cities-form">
                <input type="hidden" name="step" value="2">
                <p class="text-sm text-slate-600">Ajoutez les villes desservies (elles alimenteront la liste "Ville" de la homepage).</p>
                <div id="cities-wrapper" class="mt-4 space-y-2">
                    <?php foreach ($villes as $ville): ?>
                        <div class="flex gap-2 city-row">
                            <input name="villes[]" value="<?= htmlspecialchars((string) $ville, ENT_QUOTES); ?>" class="flex-1 rounded-lg border px-3 py-2" placeholder="Ex: Mérignac" required>
                            <button type="button" class="remove-city rounded-lg border px-3 py-2">Supprimer</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-city" class="mt-3 rounded-lg border px-3 py-2 text-sm">+ Ajouter une ville</button>
                <div class="mt-5 flex gap-2">
                    <a href="?step=1" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($step === 3): ?>
            <form class="mt-6 space-y-4" method="post">
                <input type="hidden" name="step" value="3">
                <label class="block text-sm font-medium">Email de réception des rapports
                    <input type="email" name="email_reception" value="<?= htmlspecialchars((string) ($data['email_reception'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                </label>
                <div class="grid gap-3 md:grid-cols-2">
                    <label class="block text-sm font-medium">SMTP host
                        <input name="smtp_host" value="<?= htmlspecialchars((string) ($data['smtp_host'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    </label>
                    <label class="block text-sm font-medium">SMTP port
                        <input type="number" name="smtp_port" value="<?= htmlspecialchars((string) ($data['smtp_port'] ?? 587), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    </label>
                    <label class="block text-sm font-medium">SMTP user
                        <input name="smtp_user" value="<?= htmlspecialchars((string) ($data['smtp_user'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    </label>
                    <label class="block text-sm font-medium">SMTP password
                        <input type="password" name="smtp_pass" value="<?= htmlspecialchars((string) ($data['smtp_pass'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                    </label>
                </div>
                <label class="block text-sm font-medium">Email expéditeur
                    <input type="email" name="email_expediteur" value="<?= htmlspecialchars((string) ($data['email_expediteur'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" required>
                </label>
                <div class="flex gap-2">
                    <a href="?step=2" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($step === 4): ?>
            <form class="mt-6 space-y-4" method="post">
                <input type="hidden" name="step" value="4">
                <label class="block text-sm font-medium">Titre H1
                    <input name="h1_titre" value="<?= htmlspecialchars((string) ($data['h1_titre'] ?? ''), ENT_QUOTES); ?>" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="Combien vaut votre bien à {ville} ?" required>
                </label>
                <label class="block text-sm font-medium">Sous-titre
                    <textarea name="sous_titre" class="mt-1 w-full rounded-lg border px-3 py-2" rows="3" required><?= htmlspecialchars((string) ($data['sous_titre'] ?? ''), ENT_QUOTES); ?></textarea>
                </label>
                <label class="block text-sm font-medium">Meta description
                    <textarea name="meta_description" class="mt-1 w-full rounded-lg border px-3 py-2" rows="2" required><?= htmlspecialchars((string) ($data['meta_description'] ?? ''), ENT_QUOTES); ?></textarea>
                </label>
                <div class="flex gap-2">
                    <a href="?step=3" class="rounded-lg border px-4 py-2">Retour</a>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-white">Continuer</button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($step === 5): ?>
            <div class="mt-6 space-y-4 text-sm">
                <p>Vérifiez les informations ci-dessous puis générez votre site.</p>
                <ul class="list-disc space-y-1 pl-5 text-slate-700">
                    <li><strong>Agence :</strong> <?= htmlspecialchars((string) ($data['agence_nom'] ?? ''), ENT_QUOTES); ?></li>
                    <li><strong>Ville principale :</strong> <?= htmlspecialchars((string) ($data['ville_principale'] ?? ''), ENT_QUOTES); ?></li>
                    <li><strong>Villes :</strong> <?= htmlspecialchars(implode(', ', $data['villes'] ?? []), ENT_QUOTES); ?></li>
                    <li><strong>Email réception :</strong> <?= htmlspecialchars((string) ($data['email_reception'] ?? ''), ENT_QUOTES); ?></li>
                </ul>
                <form method="post">
                    <input type="hidden" name="step" value="5">
                    <div class="flex gap-2">
                        <a href="?step=4" class="rounded-lg border px-4 py-2">Retour</a>
                        <button class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white">Générer mon site</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const addCityBtn = document.getElementById('add-city');
    const wrapper = document.getElementById('cities-wrapper');

    if (addCityBtn && wrapper) {
        addCityBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'flex gap-2 city-row';
            row.innerHTML = `
                <input name="villes[]" class="flex-1 rounded-lg border px-3 py-2" placeholder="Ex: Talence" required>
                <button type="button" class="remove-city rounded-lg border px-3 py-2">Supprimer</button>
            `;
            wrapper.appendChild(row);
        });

        wrapper.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }
            if (!target.classList.contains('remove-city')) {
                return;
            }
            const rows = wrapper.querySelectorAll('.city-row');
            if (rows.length <= 1) {
                const input = wrapper.querySelector('input[name="villes[]"]');
                if (input instanceof HTMLInputElement) {
                    input.value = '';
                    input.focus();
                }
                return;
            }
            const row = target.closest('.city-row');
            if (row) {
                row.remove();
            }
        });
    }
</script>
</body>
</html>
