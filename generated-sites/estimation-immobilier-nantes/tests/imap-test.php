<?php

/**
 * Test de connexion IMAP au serveur o2switch
 *
 * Usage : php tests/imap-test.php
 *
 * Variables d'environnement requises :
 *   IMAP_USER - Adresse email (ex: contact@estimation-immobilier-nantes.fr)
 *   IMAP_PASS - Mot de passe du compte email
 */

// Vérifier que l'extension IMAP est disponible
if (!function_exists('imap_open')) {
    die("[ERREUR] L'extension PHP IMAP n'est pas installée.\n"
      . "Installez-la avec : sudo apt install php-imap\n");
}

// Récupération des identifiants
$user = getenv('IMAP_USER');
$pass = getenv('IMAP_PASS');

if (!$user || !$pass) {
    die("[ERREUR] Variables d'environnement IMAP_USER et IMAP_PASS requises.\n"
      . "Usage : IMAP_USER=xxx IMAP_PASS=xxx php tests/imap-test.php\n");
}

$mailbox = '{mail1.o2switch.net:993/imap/ssl}';

echo "=== Test IMAP ===\n";
echo "Serveur  : mail1.o2switch.net:993 (IMAP/SSL)\n";
echo "Utilisateur : {$user}\n";
echo "Connexion en cours...\n\n";

// Connexion IMAP
$imap = @imap_open($mailbox, $user, $pass);

if (!$imap) {
    $errors = imap_errors();
    echo "[ECHEC] Impossible de se connecter au serveur IMAP.\n";
    if ($errors) {
        echo "Erreurs IMAP :\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    exit(1);
}

echo "[OK] Connexion IMAP réussie !\n\n";

// Informations sur la boîte de réception
$info = imap_mailboxmsginfo($imap);
echo "--- Boîte de réception (INBOX) ---\n";
echo "Messages total : {$info->Nmsgs}\n";
echo "Messages récents : {$info->Recent}\n";
echo "Messages non lus : {$info->Unread}\n";
echo "Taille : {$info->Size} octets\n\n";

// Lister les dossiers disponibles
$folders = imap_list($imap, $mailbox, '*');
if ($folders) {
    echo "--- Dossiers disponibles ---\n";
    foreach ($folders as $folder) {
        $name = str_replace($mailbox, '', $folder);
        echo "  - {$name}\n";
    }
    echo "\n";
}

// Afficher les 5 derniers messages
$numMessages = $info->Nmsgs;
if ($numMessages > 0) {
    $start = max(1, $numMessages - 4);
    echo "--- Derniers messages ---\n";
    for ($i = $numMessages; $i >= $start; $i--) {
        $header = imap_headerinfo($imap, $i);
        $subject = isset($header->subject) ? imap_utf8($header->subject) : '(sans objet)';
        $from = isset($header->fromaddress) ? imap_utf8($header->fromaddress) : '(inconnu)';
        $date = $header->date ?? '';
        echo "  [{$i}] {$date}\n";
        echo "       De : {$from}\n";
        echo "       Objet : {$subject}\n\n";
    }
}

// Fermeture
imap_close($imap);
echo "[OK] Connexion IMAP fermée proprement.\n";
echo "=== Test terminé ===\n";
