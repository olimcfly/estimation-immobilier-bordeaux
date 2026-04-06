<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/guard.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['estimation_id']) || (int) $_SESSION['estimation_id'] <= 0) {
    redirect('index.php');
}

$estimationId = (int) $_SESSION['estimation_id'];
$estimationSession = $_SESSION['estimation_result'] ?? [];

$adresse = sanitize($estimationSession['adresse'] ?? '');
$ville = sanitize($estimationSession['ville'] ?? '');
$typeBien = sanitize($estimationSession['type_bien'] ?? '');
$surface = (int) ($estimationSession['surface'] ?? 0);
$prixEstime = (int) ($estimationSession['prix_estime'] ?? 0);
$prixBas = (int) ($estimationSession['prix_bas'] ?? 0);
$prixHaut = (int) ($estimationSession['prix_haut'] ?? 0);

$csrfToken = generateCSRFToken();
$errorMessage = '';

if (isPost()) {
    $postedToken = $_POST['csrf_token'] ?? null;

    if (!verifyCSRFToken(is_string($postedToken) ? $postedToken : null)) {
        redirect('index.php');
    }

    $nom = sanitize($_POST['nom'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $telephone = sanitize($_POST['telephone'] ?? '');
    $dateSouhaitee = sanitize($_POST['date_souhaitee'] ?? '');
    $creneau = sanitize($_POST['creneau'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    $creneauxAutorises = ['', 'matin', 'apres_midi', 'soir'];

    if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $telephone === '' || !in_array($creneau, $creneauxAutorises, true)) {
        $errorMessage = 'Merci de compléter correctement les champs obligatoires.';
    } else {
        try {
            $pdo = Database::getConnection();

            $insertRdv = $pdo->prepare('INSERT INTO rdv (
                estimation_id, nom, email, telephone, date_souhaitee, creneau, message
            ) VALUES (
                :estimation_id, :nom, :email, :telephone, :date_souhaitee, :creneau, :message
            )');

            $insertRdv->execute([
                'estimation_id' => $estimationId,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'date_souhaitee' => $dateSouhaitee !== '' ? $dateSouhaitee : null,
                'creneau' => $creneau !== '' ? $creneau : null,
                'message' => $message !== '' ? $message : null,
            ]);

            $updateEstimation = $pdo->prepare('UPDATE estimations
                SET nom = :nom, email = :email, telephone = :telephone, rdv_pris = 1
                WHERE id = :id');

            $updateEstimation->execute([
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'id' => $estimationId,
            ]);

            envoyerNotification('new_rdv', [
                'estimation_id' => $estimationId,
                'nom' => $nom,
                'telephone' => $telephone,
                'email' => $email,
                'date_souhaitee' => $dateSouhaitee,
                'creneau' => $creneau,
            ]);

            redirect('merci.php');
        } catch (Throwable $e) {
            $errorMessage = 'Une erreur est survenue. Veuillez réessayer dans quelques instants.';
        }
    }
}

$pageTitle = 'Prendre rendez-vous';
$pageDescription = 'Planifiez un rendez-vous avec un conseiller EstimIA pour affiner votre estimation.';

include __DIR__ . '/includes/header.php';
?>
<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white pb-20 pt-28">
    <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-10 lg:grid-cols-5">
            <aside class="animate-fade-in-up lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-900">Votre estimation</h2>

                <div class="mt-4 rounded-2xl bg-gradient-to-br from-primary to-indigo-700 p-6 text-white">
                    <p class="font-semibold"><?php echo $adresse !== '' ? $adresse : ($ville !== '' ? $ville : 'Votre bien'); ?></p>
                    <p class="mt-1 text-sm text-white/70"><?php echo strtoupper($typeBien ?: 'BIEN'); ?> • <?php echo max(0, $surface); ?> m²</p>
                    <div class="my-4 border-t border-white/20"></div>
                    <p class="text-3xl font-black"><?php echo formatPrice($prixEstime); ?></p>
                    <p class="mt-1 text-sm text-white/60">
                        Entre <?php echo formatPrice($prixBas); ?> et <?php echo formatPrice($prixHaut); ?>
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    <p class="flex items-center gap-3 text-sm text-gray-600">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                        Estimation affinée par un expert
                    </p>
                    <p class="flex items-center gap-3 text-sm text-gray-600">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                        Conseils personnalisés gratuits
                    </p>
                    <p class="flex items-center gap-3 text-sm text-gray-600">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                        Accompagnement de A à Z
                    </p>
                </div>
            </aside>

            <div class="animate-fade-in-up animation-delay-100 lg:col-span-3">
                <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-xl">
                    <h2 class="text-xl font-bold text-gray-900">Vos coordonnées</h2>
                    <p class="mt-1 text-sm text-gray-500">Un conseiller vous recontactera sous 24h</p>

                    <?php if ($errorMessage !== ''): ?>
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="rdv.php" class="mt-6 space-y-5">
                        <div>
                            <label for="nom" class="mb-2 block text-sm font-semibold text-gray-700">Nom complet</label>
                            <div class="relative">
                                <i data-lucide="user" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                                <input id="nom" type="text" name="nom" required placeholder="Jean Dupont" class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-4 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                                <input id="email" type="email" name="email" required placeholder="jean@email.com" class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-4 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>

                        <div>
                            <label for="telephone" class="mb-2 block text-sm font-semibold text-gray-700">Téléphone</label>
                            <div class="relative">
                                <i data-lucide="phone" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                                <input id="telephone" type="tel" name="telephone" required pattern="^((\+33|0)[1-9](\d{2}){4})$" placeholder="06 12 34 56 78" class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-4 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>

                        <div>
                            <label for="date_souhaitee" class="mb-2 block text-sm font-semibold text-gray-700">Date souhaitée</label>
                            <div class="relative">
                                <i data-lucide="calendar" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                                <input id="date_souhaitee" type="date" name="date_souhaitee" min="<?php echo date('Y-m-d'); ?>" class="form-input w-full rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-4 text-gray-800 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10">
                            </div>
                        </div>

                        <div>
                            <label for="creneau" class="mb-2 block text-sm font-semibold text-gray-700">Créneau</label>
                            <div class="relative">
                                <i data-lucide="clock" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"></i>
                                <select id="creneau" name="creneau" class="form-input w-full appearance-none rounded-xl border-2 border-gray-200 py-3.5 pl-12 pr-10 text-gray-800 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10">
                                    <option value="">Peu importe</option>
                                    <option value="matin">Matin (9h-12h)</option>
                                    <option value="apres_midi">Après-midi (14h-17h)</option>
                                    <option value="soir">Soir (17h-19h)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="message" class="mb-2 block text-sm font-semibold text-gray-700">Message (optionnel)</label>
                            <textarea id="message" name="message" rows="3" placeholder="Précisions sur votre projet..." class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3.5 text-gray-800 placeholder-gray-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-blue-500/10"></textarea>
                        </div>

                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 py-4 text-lg font-bold text-white shadow-lg shadow-blue-500/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-blue-500/30">
                            Confirmer mon rendez-vous
                            <i data-lucide="arrow-right" class="h-5 w-5"></i>
                        </button>

                        <p class="text-center text-xs text-gray-400">🔒 Vos données restent confidentielles</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
