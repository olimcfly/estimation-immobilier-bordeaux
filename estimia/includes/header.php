<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$defaultSiteName = siteConfig('name', 'EstimIA');
$pageTitle = isset($pageTitle) && is_string($pageTitle) && $pageTitle !== '' ? $pageTitle : $defaultSiteName;
$pageDescription = isset($pageDescription) && is_string($pageDescription) && $pageDescription !== ''
    ? $pageDescription
    : 'Obtenez une estimation immobilière fiable, gratuite et instantanée à ' . siteConfig('city', 'votre secteur') . ' et alentours.';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'www.estimia.fr';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$canonicalUrl = $scheme . '://' . $host . $requestUri;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($defaultSiteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">

    <title><?php echo htmlspecialchars($pageTitle . ' - Estimation immobilière à ' . siteConfig('city', 'France'), ENT_QUOTES, 'UTF-8'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "<?php echo htmlspecialchars($defaultSiteName, ENT_QUOTES, 'UTF-8'); ?>",
            "description": "Outil d'estimation immobilière en ligne",
            "applicationCategory": "RealEstate",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "EUR"
            }
        }
    </script>

    <!-- Google tag (gtag.js) - REMPLACER UA-XXXXX -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_ID"></script>
    <!-- TODO: Remplacer GA_ID et décommenter la config ci-dessous
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);} // eslint-disable-line
        gtag('js', new Date());
        gtag('config', 'GA_ID');
    </script>
    -->

    <!-- Facebook Pixel Code - TODO: Ajouter votre Pixel ID
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', 'PIXEL_ID');
      fbq('track', 'PageView');
    </script>
    -->

    <script src="https://cdn.tailwindcss.com"></script>
    <style>:root { --primary: <?php echo htmlspecialchars((string) siteConfig('color', '#1a56db'), ENT_QUOTES, 'UTF-8'); ?>; }</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo addslashes((string) siteConfig('color', '#1a56db')); ?>',
                        'primary-dark': '#1042b0',
                        accent: '#f59e0b',
                        secondary: '#0f172a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode((string) siteConfig('maps_key', '')); ?>&libraries=places&callback=initAutocomplete" async defer></script>
</head>
<body class="font-sans bg-slate-50 text-slate-900 antialiased">
<nav class="fixed top-0 z-50 w-full border-b border-gray-100 bg-white/80 backdrop-blur-xl">
    <div class="mx-auto flex h-[72px] max-w-6xl items-center justify-between px-6">
        <a href="/" class="flex items-center gap-3" aria-label="Accueil <?php echo htmlspecialchars($defaultSiteName, ENT_QUOTES, 'UTF-8'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 class="h-7 w-7 text-primary" aria-hidden="true">
                <path d="M3 10.5 12 3l9 7.5"/>
                <path d="M5 9.5V21h14V9.5"/>
                <path d="M9.5 21v-6h5v6"/>
            </svg>
            <span class="bg-gradient-to-r from-primary to-indigo-500 bg-clip-text text-xl font-extrabold text-transparent">
                <?php echo htmlspecialchars($defaultSiteName, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </a>

        <div class="flex items-center gap-4">
            <?php if ((string) siteConfig('phone', '') !== ''): ?>
                <a href="tel:<?php echo htmlspecialchars((string) siteConfig('phone', ''), ENT_QUOTES, 'UTF-8'); ?>" class="hidden text-sm font-semibold text-gray-600 md:block">
                    <?php echo htmlspecialchars((string) siteConfig('phone', ''), ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endif; ?>
            <a href="/#formulaire"
               class="rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-6 py-2.5 font-semibold text-white transition-all hover:-translate-y-0.5 hover:shadow-lg">
                Estimer mon bien →
            </a>
        </div>
    </div>
</nav>
<main class="pt-[88px]">
