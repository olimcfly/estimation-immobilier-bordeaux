<?php
$footerSiteName = siteConfig('name', 'EstimIA');
$footerCity = siteConfig('city', 'France');
$footerPhone = siteConfig('phone', '');
?>
</main>
<footer class="bg-gray-900 py-16 text-white">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
            <div>
                <a href="/" class="text-2xl font-extrabold text-white"><?php echo htmlspecialchars((string) $footerSiteName, ENT_QUOTES, 'UTF-8'); ?></a>
                <p class="mt-4 max-w-xs text-sm text-gray-400">
                    Estimation immobilière intelligente, gratuite et instantanée à <?php echo htmlspecialchars((string) $footerCity, ENT_QUOTES, 'UTF-8'); ?> et alentours.
                </p>
            </div>

            <div>
                <h3 class="mb-4 font-semibold">Villes populaires</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a class="transition hover:text-white" href="/?ville=bordeaux">Bordeaux</a></li>
                    <li><a class="transition hover:text-white" href="/?ville=paris">Paris</a></li>
                    <li><a class="transition hover:text-white" href="/?ville=lyon">Lyon</a></li>
                    <li><a class="transition hover:text-white" href="/?ville=nantes">Nantes</a></li>
                    <li><a class="transition hover:text-white" href="/?ville=toulouse">Toulouse</a></li>
                    <li><a class="transition hover:text-white" href="/?ville=marseille">Marseille</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 font-semibold">Contact</h3>
                <p class="text-gray-400"><a href="mailto:<?php echo htmlspecialchars((string) siteConfig('admin_email', 'contact@estimia.fr'), ENT_QUOTES, 'UTF-8'); ?>" class="transition hover:text-white"><?php echo htmlspecialchars((string) siteConfig('admin_email', 'contact@estimia.fr'), ENT_QUOTES, 'UTF-8'); ?></a></p>
                <?php if ($footerPhone !== ''): ?><p class="mt-2 text-gray-400"><?php echo htmlspecialchars((string) $footerPhone, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
                <p class="mt-2 text-gray-400">Du lundi au vendredi, 9h-18h</p>
            </div>
        </div>

        <div class="mt-10 border-t border-gray-800 pt-6">
            <p class="text-sm font-medium text-gray-300">Pages villes principales :</p>
            <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-400">
                <a class="transition hover:text-white" href="/?ville=bordeaux">Bordeaux</a>
                <a class="transition hover:text-white" href="/?ville=paris">Paris</a>
                <a class="transition hover:text-white" href="/?ville=lyon">Lyon</a>
                <a class="transition hover:text-white" href="/?ville=nice">Nice</a>
                <a class="transition hover:text-white" href="/?ville=lille">Lille</a>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-gray-800 pt-8 text-sm text-gray-500 md:flex-row md:items-center">
            <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars((string) $footerSiteName, ENT_QUOTES, 'UTF-8'); ?>. Tous droits réservés.</p>
            <div class="flex items-center gap-6">
                <a href="/mentions-legales.php" class="transition hover:text-white">Mentions légales</a>
                <a href="/politique-confidentialite.php" class="transition hover:text-white">Confidentialité</a>
            </div>
        </div>
    </div>
</footer>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?php echo htmlspecialchars((string) $footerSiteName, ENT_QUOTES, 'UTF-8'); ?>",
        "description": "Service d'estimation immobilière en ligne",
        "url": "<?php echo htmlspecialchars((string) siteConfig('url', ''), ENT_QUOTES, 'UTF-8'); ?>",
        "email": "<?php echo htmlspecialchars((string) siteConfig('admin_email', ''), ENT_QUOTES, 'UTF-8'); ?>",
        "areaServed": "<?php echo htmlspecialchars((string) $footerCity, ENT_QUOTES, 'UTF-8'); ?>"
    }
</script>
<script>
    lucide.createIcons();
</script>
</body>
</html>
