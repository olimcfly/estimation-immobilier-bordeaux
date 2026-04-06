<?php
declare(strict_types=1);
$legacy = __DIR__ . '/../core/pages/mentions-legales.php';
if (is_file($legacy)) {
    require $legacy;
    return;
}
?><!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Mentions légales</title></head><body><h1>Mentions légales</h1><p>Le contenu de référence est indisponible.</p></body></html>
