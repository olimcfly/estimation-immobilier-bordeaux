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
<tr><td style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;padding:24px;color:#fff;text-align:center;font-family:Arial,sans-serif;"><h2 style="margin:0;"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></h2></td></tr>
<tr><td style="padding:24px;font-family:Arial,sans-serif;color:#111827;">
<div style="display:inline-block;background:#ef4444;color:#fff;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;">Nouveau RDV</div>
<h3 style="margin-top:16px;">Un nouveau rendez-vous a été demandé</h3>
<p><strong>Contact :</strong> <?php echo htmlspecialchars((string) ($data['nom'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Téléphone :</strong> <?php echo htmlspecialchars((string) ($data['telephone'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Email :</strong> <?php echo htmlspecialchars((string) ($data['email'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Créneau :</strong> <?php echo htmlspecialchars((string) ($data['date_souhaitee'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars((string) ($data['creneau'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></p>
<p style="margin-top:24px;"><a href="<?php echo htmlspecialchars($siteUrl . '/admin/lead.php?id=' . (int) ($data['estimation_id'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;display:inline-block;font-weight:600;">Voir la fiche lead →</a></p>
</td></tr>
<tr><td style="padding:16px 24px;font-size:12px;color:#6b7280;font-family:Arial,sans-serif;">Notification automatique • <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></td></tr>
</table>
</td></tr>
</table>
</body></html>
<?php
return ob_get_clean();
