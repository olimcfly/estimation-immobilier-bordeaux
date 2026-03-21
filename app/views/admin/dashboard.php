<div class="container">

  <?php if (!empty($dbError ?? '')): ?>
    <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
      <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?>
      <a href="/admin/diagnostic" style="color: #991b1b; font-weight: 600; margin-left: 0.5rem;">Voir le diagnostic</a>
    </div>
  <?php endif; ?>

  <!-- Stats Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">

    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b6459;">Leads total</span>
        <span style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(139,21,56,0.08); border-radius: 8px; color: #8B1538;"><i class="fas fa-users"></i></span>
      </div>
      <div style="font-size: 2rem; font-weight: 700; color: #1a1410;"><?= (int) ($stats['total_leads'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #6b6459; margin-top: 0.25rem;">
        <span style="color: #22c55e;"><i class="fas fa-arrow-up"></i> <?= (int) ($stats['new_leads_today'] ?? 0) ?></span> aujourd'hui
      </div>
    </div>

    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b6459;">Leads chauds</span>
        <span style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(249,115,22,0.08); border-radius: 8px; color: #f97316;"><i class="fas fa-fire"></i></span>
      </div>
      <div style="font-size: 2rem; font-weight: 700; color: #f97316;"><?= (int) ($stats['hot_leads'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #6b6459; margin-top: 0.25rem;">Score "chaud"</div>
    </div>

    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b6459;">Articles publiés</span>
        <span style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(59,130,246,0.08); border-radius: 8px; color: #3b82f6;"><i class="fas fa-newspaper"></i></span>
      </div>
      <div style="font-size: 2rem; font-weight: 700; color: #1a1410;"><?= (int) ($stats['total_articles'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #6b6459; margin-top: 0.25rem;">
        <?= (int) ($stats['draft_articles'] ?? 0) ?> brouillon(s)
      </div>
    </div>

    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <span style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b6459;">Nouveaux leads</span>
        <span style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(34,197,94,0.08); border-radius: 8px; color: #22c55e;"><i class="fas fa-clock"></i></span>
      </div>
      <div style="font-size: 2rem; font-weight: 700; color: #22c55e;"><?= (int) ($stats['pending_leads'] ?? 0) ?></div>
      <div style="font-size: 0.8rem; color: #6b6459; margin-top: 0.25rem;">En attente de contact</div>
    </div>

  </div>

  <!-- Quick Actions + Recent Leads -->
  <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">

    <!-- Quick Actions -->
    <div class="card" style="padding: 1.5rem;">
      <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 1rem;">Actions rapides</h2>
      <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        <a href="/admin/leads" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(139,21,56,0.05); border-radius: 8px; text-decoration: none; color: #1a1410; font-size: 0.9rem; transition: background 0.15s;">
          <i class="fas fa-users" style="color: #8B1538; width: 20px; text-align: center;"></i>
          Voir tous les leads
        </a>
        <a href="/admin/blog/create" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(59,130,246,0.05); border-radius: 8px; text-decoration: none; color: #1a1410; font-size: 0.9rem; transition: background 0.15s;">
          <i class="fas fa-plus" style="color: #3b82f6; width: 20px; text-align: center;"></i>
          Nouvel article
        </a>
        <a href="/admin/blog" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(59,130,246,0.05); border-radius: 8px; text-decoration: none; color: #1a1410; font-size: 0.9rem; transition: background 0.15s;">
          <i class="fas fa-newspaper" style="color: #3b82f6; width: 20px; text-align: center;"></i>
          Gestion du blog
        </a>
        <a href="/admin/images" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(212,175,55,0.08); border-radius: 8px; text-decoration: none; color: #1a1410; font-size: 0.9rem; transition: background 0.15s;">
          <i class="fas fa-images" style="color: #D4AF37; width: 20px; text-align: center;"></i>
          Generateur d'images
        </a>
        <a href="/admin/diagnostic" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(107,100,89,0.05); border-radius: 8px; text-decoration: none; color: #1a1410; font-size: 0.9rem; transition: background 0.15s;">
          <i class="fas fa-stethoscope" style="color: #6b6459; width: 20px; text-align: center;"></i>
          Diagnostic systeme
        </a>
      </div>
    </div>

    <!-- Recent Leads -->
    <div class="card" style="padding: 1.5rem;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Derniers leads</h2>
        <a href="/admin/leads" style="font-size: 0.85rem; color: #8B1538; text-decoration: none;">Voir tout <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i></a>
      </div>

      <?php if (empty($recent_leads ?? [])): ?>
        <p style="color: #6b6459; font-size: 0.9rem;">Aucun lead pour le moment.</p>
      <?php else: ?>
        <div class="table-wrapper">
          <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
              <tr style="border-bottom: 2px solid #e8dfd7;">
                <th style="text-align: left; padding: 0.5rem 0.75rem; color: #6b6459; font-weight: 600;">Nom</th>
                <th style="text-align: left; padding: 0.5rem 0.75rem; color: #6b6459; font-weight: 600;">Ville</th>
                <th style="text-align: left; padding: 0.5rem 0.75rem; color: #6b6459; font-weight: 600;">Estimation</th>
                <th style="text-align: left; padding: 0.5rem 0.75rem; color: #6b6459; font-weight: 600;">Score</th>
                <th style="text-align: left; padding: 0.5rem 0.75rem; color: #6b6459; font-weight: 600;">Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_leads as $lead): ?>
                <tr style="border-bottom: 1px solid #f0ebe5;">
                  <td style="padding: 0.6rem 0.75rem; font-weight: 500;"><?= htmlspecialchars((string) $lead['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td style="padding: 0.6rem 0.75rem;"><?= htmlspecialchars((string) $lead['ville'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td style="padding: 0.6rem 0.75rem;"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> &euro;</td>
                  <td style="padding: 0.6rem 0.75rem;">
                    <?php
                      $scoreColor = match((string) $lead['score']) {
                        'chaud' => '#f97316',
                        'tiede' => '#D4AF37',
                        default => '#6b6459',
                      };
                    ?>
                    <span style="display: inline-block; padding: 0.15rem 0.5rem; background: <?= $scoreColor ?>15; color: <?= $scoreColor ?>; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                      <?= htmlspecialchars((string) $lead['score'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td style="padding: 0.6rem 0.75rem; color: #6b6459; font-size: 0.8rem;"><?= htmlspecialchars((string) $lead['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>
