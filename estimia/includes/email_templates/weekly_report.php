<?php

declare(strict_types=1);

$siteName = defined('SITE_NAME') ? SITE_NAME : 'EstimIA';
$siteColor = defined('SITE_COLOR') ? SITE_COLOR : '#1a56db';
$siteUrl = rtrim(defined('SITE_URL') ? SITE_URL : '', '/');
$data = is_array($data ?? null) ? $data : [];
$topLeads = is_array($data['top_leads'] ?? null) ? $data['top_leads'] : [];
$rdvs = is_array($data['next_rdv'] ?? null) ? $data['next_rdv'] : [];
$current = (int) ($data['current_week_total'] ?? 0);
$previous = (int) ($data['previous_week_total'] ?? 0);
$max = max(1, $current, $previous);

ob_start();
?>
<!doctype html>
<html lang="fr"><body style="margin:0;padding:0;background:#f3f4f6;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
<tr><td style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;padding:24px;color:#fff;text-align:center;font-family:Arial,sans-serif;"><h2 style="margin:0;"><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?> — Rapport hebdomadaire</h2></td></tr>
<tr><td style="padding:24px;font-family:Arial,sans-serif;color:#111827;">
<h3 style="margin-top:0;">Résumé de la semaine</h3>
<p><strong>Leads cette semaine :</strong> <?php echo $current; ?></p>
<p><strong>Semaine précédente :</strong> <?php echo $previous; ?></p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:12px 0;">
<tr>
<td style="font-size:12px;color:#6b7280;width:120px;">Semaine N</td>
<td><div style="height:10px;background:#e5e7eb;border-radius:999px;"><div style="height:10px;width:<?php echo (int) round(($current / $max) * 100); ?>%;background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;border-radius:999px;"></div></div></td>
</tr>
<tr>
<td style="font-size:12px;color:#6b7280;width:120px;">Semaine N-1</td>
<td><div style="height:10px;background:#e5e7eb;border-radius:999px;"><div style="height:10px;width:<?php echo (int) round(($previous / $max) * 100); ?>%;background:#94a3b8;border-radius:999px;"></div></div></td>
</tr>
</table>

<h4>Top 5 leads</h4>
<ul>
<?php foreach ($topLeads as $lead): ?>
<li>#<?php echo (int) ($lead['id'] ?? 0); ?> — <?php echo htmlspecialchars((string) ($lead['adresse'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?> (Score <?php echo (int) ($lead['lead_score'] ?? 0); ?>)</li>
<?php endforeach; ?>
</ul>

<h4>Prochains RDV</h4>
<ul>
<?php foreach ($rdvs as $rdv): ?>
<li><?php echo htmlspecialchars((string) ($rdv['date_souhaitee'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars((string) ($rdv['nom'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>

<p style="margin-top:24px;"><a href="<?php echo htmlspecialchars($siteUrl . '/admin/', ENT_QUOTES, 'UTF-8'); ?>" style="background:<?php echo htmlspecialchars($siteColor, ENT_QUOTES, 'UTF-8'); ?>;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;display:inline-block;font-weight:600;">Accéder au dashboard</a></p>
</td></tr>
<tr><td style="padding:16px 24px;font-size:12px;color:#6b7280;font-family:Arial,sans-serif;">Vous recevez ce rapport car la notification hebdomadaire est activée.</td></tr>
</table>
</td></tr>
</table>
</body></html>
<?php
return ob_get_clean();
