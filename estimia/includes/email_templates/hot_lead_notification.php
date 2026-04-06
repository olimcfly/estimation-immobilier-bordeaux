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
<tr><td style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;padding:24px;color:#fff;text-align:center;font-family:Arial,sans-serif;"><h2 style="margin:0;">🔥 <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></h2></td></tr>
<tr><td style="padding:24px;font-family:Arial,sans-serif;color:#111827;">
<h3 style="margin-top:0;">Lead chaud détecté</h3>
<p style="font-size:14px;"><span style="display:inline-block;background:#dc2626;color:#fff;padding:4px 10px;border-radius:999px;font-weight:700;">Score <?php echo (int) ($data['lead_score'] ?? 0); ?>/100</span></p>
<p><?php echo htmlspecialchars((string) ($data['adresse'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars((string) ($data['type_bien'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?> <?php echo (int) ($data['surface'] ?? 0); ?>m²</p>
<p><strong>Prix estimé :</strong> <?php echo number_format((float) ($data['prix_estime'] ?? 0), 0, ',', ' '); ?> €</p>
<p style="margin-top:24px;"><a href="<?php echo htmlspecialchars($siteUrl . '/admin/lead.php?id=' . (int) ($data['id'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" style="background:#dc2626;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;display:inline-block;font-weight:700;">Contacter maintenant</a></p>
</td></tr>
<tr><td style="padding:16px 24px;font-size:12px;color:#6b7280;font-family:Arial,sans-serif;">Vous recevez cet email car l’alerte lead chaud est activée.</td></tr>
</table>
</td></tr>
</table>
</body></html>
<?php
return ob_get_clean();
