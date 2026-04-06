<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/classes/Mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();

    $prenom = trim((string) ($_POST['prenom'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $typeBien = trim((string) ($_POST['type_bien'] ?? ''));
    $surface = (int) ($_POST['surface'] ?? 0);
    $adresse = trim((string) ($_POST['adresse'] ?? ''));
    $ville = trim((string) ($_POST['ville'] ?? ''));

    $villeDetectee = $ville;
    $prixM2 = 3500;
    $prixEstime = $surface * $prixM2;
    $prixBas = (int) ($prixEstime * 0.9);
    $prixHaut = (int) ($prixEstime * 1.1);

    $insert = $db->prepare(
        'INSERT INTO estimations (prenom, email, type_bien, surface, adresse, ville, prix_estime, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())'
    );
    $insert->execute([$prenom, $email, $typeBien, $surface, $adresse, $villeDetectee, $prixEstime]);

    $estimationId = (int) $db->lastInsertId();

    $mailer = new Mailer();
    $mailer->send(
        $email,
        "Votre estimation immobilière à $ville",
        'estimation-result',
        [
            'prenom' => $prenom,
            'type_bien' => $typeBien,
            'surface' => $surface,
            'adresse' => $adresse,
            'ville' => $villeDetectee,
            'prix_estime' => number_format($prixEstime, 0, ',', ' '),
            'prix_bas' => number_format($prixBas, 0, ',', ' '),
            'prix_haut' => number_format($prixHaut, 0, ',', ' '),
            'prix_m2' => number_format($prixM2, 0, ',', ' '),
            'estimation_id' => $estimationId,
            'recipient_email' => $email,
            'unsubscribe_token' => md5($email . 'unsub_salt_' . $estimationId),
        ]
    );

    header('Location: /?estimation=success&id=' . $estimationId);
    exit;
}

require __DIR__ . '/templates/header.php';
require __DIR__ . '/templates/home.php';
require __DIR__ . '/templates/footer.php';
