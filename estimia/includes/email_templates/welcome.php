<?php

declare(strict_types=1);

$siteName = defined('SITE_NAME') ? SITE_NAME : 'EstimIA';
$siteColor = defined('SITE_COLOR') ? SITE_COLOR : '#1a56db';
$siteUrl = rtrim(defined('SITE_URL') ? SITE_URL : '', '/');
$data = is_array($data ?? null) ? $data : [];

ob_start();
?>
<!doctype html>
<html lang="fr"><body style="margin:0;padding:0;background:#f3f4f6;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
<tr><td style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;padding:24px;color:#fff;text-align:center;font-family:Arial,sans-serif;"><h2 style="margin:0;">Bienvenue sur <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></h2></td></tr>
<tr><td style="padding:24px;font-family:Arial,sans-serif;color:#111827;">
<p>Votre installation est terminée avec succès.</p>
<p><strong>Ville cible :</strong> <?php echo htmlspecialchars((string) ($data['city'] ?? (defined('CITY_NAME') ? CITY_NAME : '-')), ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Rayon :</strong> <?php echo htmlspecialchars((string) ($data['radius'] ?? (defined('CITY_RADIUS_KM') ? CITY_RADIUS_KM : '-')), ENT_QUOTES, 'UTF-8'); ?> km</p>
<p><strong>Admin :</strong> <?php echo htmlspecialchars((string) ($data['admin_email'] ?? (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '-')), ENT_QUOTES, 'UTF-8'); ?></p>
<p style="margin-top:24px;"><a href="<?php echo htmlspecialchars($siteUrl . '/admin/', ENT_QUOTES, 'UTF-8'); ?>" style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;display:inline-block;font-weight:600;">Accéder au tableau de bord</a></p>
</td></tr>
<tr><td style="padding:16px 24px;font-size:12px;color:#6b7280;font-family:Arial,sans-serif;">Liens utiles : <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">Site public</a> • <a href="<?php echo htmlspecialchars($siteUrl . '/admin/', ENT_QUOTES, 'UTF-8'); ?>">Admin</a></td></tr>
</table>
</td></tr>
</table>
</body></html>
<?php
return ob_get_clean();
