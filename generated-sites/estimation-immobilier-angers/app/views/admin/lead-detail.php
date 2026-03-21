<style>
  .lead-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .lead-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .lead-header h1 i { color: var(--admin-primary); }

  .lead-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid var(--admin-border);
    background: #fff;
    color: var(--admin-text);
    transition: all 0.15s;
  }

  .btn:hover { background: #f8fafc; }
  .btn-primary { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }
  .btn-primary:hover { opacity: 0.9; background: var(--admin-primary); }
  .btn-danger { background: #ef4444; color: #fff; border-color: #ef4444; }
  .btn-danger:hover { opacity: 0.9; background: #ef4444; }

  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .detail-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .detail-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .detail-card-header i { color: var(--admin-primary); font-size: 0.9rem; }

  .detail-card-body { padding: 1.25rem; }

  .detail-row {
    display: flex;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .detail-row:last-child { border-bottom: none; }

  .detail-label {
    width: 140px;
    flex-shrink: 0;
    font-size: 0.8rem;
    color: var(--admin-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .detail-value {
    flex: 1;
    font-size: 0.9rem;
    color: var(--admin-text);
  }

  .full-width { grid-column: 1 / -1; }

  .badge-score {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-chaud { background: rgba(239,68,68,0.1); color: #dc2626; }
  .badge-tiede { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-froid { background: rgba(100,116,139,0.1); color: #475569; }

  .badge-statut {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-nouveau { background: rgba(59,130,246,0.1); color: #2563eb; }
  .badge-contacte { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-rdv_pris { background: rgba(139,92,246,0.1); color: #7c3aed; }
  .badge-visite_realisee { background: rgba(236,72,153,0.1); color: #db2777; }
  .badge-mandat_simple { background: rgba(14,165,233,0.1); color: #0284c7; }
  .badge-mandat_exclusif { background: rgba(20,184,166,0.1); color: #0d9488; }
  .badge-compromis_vente { background: rgba(249,115,22,0.1); color: #c2410c; }
  .badge-signe { background: rgba(34,197,94,0.1); color: #16a34a; }
  .badge-co_signature_partenaire { background: rgba(168,85,247,0.1); color: #7c3aed; }
  .badge-assigne_autre { background: rgba(100,116,139,0.1); color: #475569; }

  .badge-type {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .badge-tendance { background: rgba(168,85,247,0.1); color: #7c3aed; }
  .badge-qualifie { background: rgba(34,197,94,0.1); color: #16a34a; }

  /* Notes */
  .note-form { margin-bottom: 1rem; }

  .note-form textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.85rem;
    resize: vertical;
    color: var(--admin-text);
  }

  .note-form textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .note-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.5rem;
  }

  .note-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .note-item:last-child { border-bottom: none; }

  .note-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.25rem;
  }

  .note-author {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--admin-text);
  }

  .note-date {
    font-size: 0.75rem;
    color: var(--admin-muted);
  }

  .note-content {
    font-size: 0.85rem;
    color: var(--admin-text);
    line-height: 1.5;
    white-space: pre-wrap;
  }

  .note-delete {
    background: none;
    border: none;
    color: var(--admin-muted);
    cursor: pointer;
    font-size: 0.75rem;
    padding: 0.2rem 0.4rem;
  }

  .note-delete:hover { color: #ef4444; }

  /* Activity */
  .activity-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .activity-item:last-child { border-bottom: none; }

  .activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
    background: rgba(59,130,246,0.1);
    color: #3b82f6;
  }

  .activity-icon.note { background: rgba(245,158,11,0.1); color: #d97706; }
  .activity-icon.statut { background: rgba(139,92,246,0.1); color: #7c3aed; }

  .activity-text {
    font-size: 0.85rem;
    color: var(--admin-text);
  }

  .activity-date {
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-top: 2px;
  }

  .empty-note {
    text-align: center;
    padding: 1.5rem;
    color: var(--admin-muted);
    font-size: 0.85rem;
  }

  .delete-form {
    display: inline;
  }

  @media (max-width: 768px) {
    .detail-grid { grid-template-columns: 1fr; }
    .lead-header { flex-direction: column; align-items: flex-start; }
  }
</style>

<?php
  $lead = $lead ?? [];
  $notes = $notes ?? [];
  $activities = $activities ?? [];
  $partenaire = $partenaire ?? null;
  $leadId = (int) ($lead['id'] ?? 0);
  $isTendance = ($lead['lead_type'] ?? 'qualifie') === 'tendance';

  $scoreClass = match($lead['score'] ?? '') {
    'chaud' => 'badge-chaud',
    'tiede' => 'badge-tiede',
    default => 'badge-froid',
  };
  $scoreIcon = match($lead['score'] ?? '') {
    'chaud' => 'fa-fire',
    'tiede' => 'fa-temperature-half',
    default => 'fa-snowflake',
  };

  $statutLabels = [
    'nouveau' => 'Nouveau',
    'contacte' => 'Contact&eacute;',
    'rdv_pris' => 'RDV Pris',
    'visite_realisee' => 'Visite R&eacute;alis&eacute;e',
    'mandat_simple' => 'Mandat Simple',
    'mandat_exclusif' => 'Mandat Exclusif',
    'compromis_vente' => 'Compromis',
    'signe' => 'Sign&eacute;',
    'co_signature_partenaire' => 'Co-signature',
    'assigne_autre' => 'Assign&eacute;',
  ];
  $statutKey = $lead['statut'] ?? 'nouveau';
?>

<!-- HEADER -->
<div class="lead-header">
  <h1>
    <i class="fas fa-user"></i>
    Lead #<?= $leadId ?>
    <?php if ($isTendance): ?>
      <span class="badge-type badge-tendance"><i class="fas fa-chart-line"></i> Tendance</span>
    <?php else: ?>
      <span class="badge-type badge-qualifie"><i class="fas fa-user-check"></i> Qualifi&eacute;</span>
    <?php endif; ?>
    <span class="badge-score <?= $scoreClass ?>"><i class="fas <?= $scoreIcon ?>"></i> <?= e((string) ($lead['score'] ?? 'froid')) ?></span>
    <span class="badge-statut badge-<?= $statutKey ?>"><?= $statutLabels[$statutKey] ?? $statutKey ?></span>
  </h1>
  <div class="lead-actions">
    <a href="/admin/leads" class="btn"><i class="fas fa-arrow-left"></i> Retour</a>
    <a href="/admin/leads/edit?id=<?= $leadId ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Modifier</a>
    <form method="POST" action="/admin/leads/delete" class="delete-form" onsubmit="return confirm('Supprimer ce lead ?');">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="id" value="<?= $leadId ?>">
      <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
    </form>
  </div>
</div>

<!-- DETAIL CARDS -->
<div class="detail-grid">
  <!-- Contact Info -->
  <div class="detail-card">
    <div class="detail-card-header"><i class="fas fa-address-card"></i> Contact</div>
    <div class="detail-card-body">
      <div class="detail-row">
        <div class="detail-label">Nom</div>
        <div class="detail-value"><?= !empty($lead['nom']) ? e((string) $lead['nom']) : '<span style="color:var(--admin-muted);">Anonyme</span>' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Email</div>
        <div class="detail-value"><?= !empty($lead['email']) ? '<a href="mailto:' . e((string) $lead['email']) . '">' . e((string) $lead['email']) . '</a>' : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">T&eacute;l&eacute;phone</div>
        <div class="detail-value"><?= !empty($lead['telephone']) ? '<a href="tel:' . e((string) $lead['telephone']) . '">' . e((string) $lead['telephone']) . '</a>' : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Adresse</div>
        <div class="detail-value"><?= !empty($lead['adresse']) ? e((string) $lead['adresse']) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Ville</div>
        <div class="detail-value"><?= e((string) ($lead['ville'] ?? '')) ?></div>
      </div>
    </div>
  </div>

  <!-- Property Info -->
  <div class="detail-card">
    <div class="detail-card-header"><i class="fas fa-home"></i> Bien Immobilier</div>
    <div class="detail-card-body">
      <div class="detail-row">
        <div class="detail-label">Type</div>
        <div class="detail-value"><?= !empty($lead['type_bien']) ? e(ucfirst((string) $lead['type_bien'])) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Surface</div>
        <div class="detail-value"><?= !empty($lead['surface_m2']) ? number_format((float) $lead['surface_m2'], 0, ',', '') . ' m&sup2;' : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Pi&egrave;ces</div>
        <div class="detail-value"><?= !empty($lead['pieces']) ? (int) $lead['pieces'] : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Estimation</div>
        <div class="detail-value"><strong><?= number_format((float) ($lead['estimation'] ?? 0), 0, ',', ' ') ?> &euro;</strong></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Urgence</div>
        <div class="detail-value"><?= !empty($lead['urgence']) ? e((string) $lead['urgence']) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Motivation</div>
        <div class="detail-value"><?= !empty($lead['motivation']) ? e((string) $lead['motivation']) : '-' ?></div>
      </div>
    </div>
  </div>

  <!-- Pipeline Info -->
  <div class="detail-card">
    <div class="detail-card-header"><i class="fas fa-tasks"></i> Pipeline Commercial</div>
    <div class="detail-card-body">
      <div class="detail-row">
        <div class="detail-label">Assign&eacute; &agrave;</div>
        <div class="detail-value"><?= !empty($lead['assigne_a']) ? e((string) $lead['assigne_a']) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Partenaire</div>
        <div class="detail-value"><?= $partenaire !== null ? e((string) $partenaire['nom']) . (!empty($partenaire['entreprise']) ? ' (' . e((string) $partenaire['entreprise']) . ')' : '') : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Commission</div>
        <div class="detail-value">
          <?php if (!empty($lead['commission_taux'])): ?>
            <?= number_format((float) $lead['commission_taux'], 2, ',', '') ?>%
            <?php if (!empty($lead['commission_montant'])): ?>
              (<?= number_format((float) $lead['commission_montant'], 0, ',', ' ') ?> &euro;)
            <?php endif; ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Prix de vente</div>
        <div class="detail-value"><?= !empty($lead['prix_vente']) ? number_format((float) $lead['prix_vente'], 0, ',', ' ') . ' &euro;' : '-' ?></div>
      </div>
    </div>
  </div>

  <!-- Dates -->
  <div class="detail-card">
    <div class="detail-card-header"><i class="fas fa-calendar-alt"></i> Dates</div>
    <div class="detail-card-body">
      <div class="detail-row">
        <div class="detail-label">Cr&eacute;ation</div>
        <div class="detail-value"><?= e((string) ($lead['created_at'] ?? '')) ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Date mandat</div>
        <div class="detail-value"><?= !empty($lead['date_mandat']) ? e((string) $lead['date_mandat']) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Date compromis</div>
        <div class="detail-value"><?= !empty($lead['date_compromis']) ? e((string) $lead['date_compromis']) : '-' ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Date signature</div>
        <div class="detail-value"><?= !empty($lead['date_signature']) ? e((string) $lead['date_signature']) : '-' ?></div>
      </div>
    </div>
  </div>

  <!-- Notes -->
  <?php if (!empty($lead['notes'])): ?>
  <div class="detail-card full-width">
    <div class="detail-card-header"><i class="fas fa-sticky-note"></i> Notes du lead</div>
    <div class="detail-card-body">
      <div style="font-size: 0.85rem; color: var(--admin-text); white-space: pre-wrap;"><?= e((string) $lead['notes']) ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CRM Notes -->
  <div class="detail-card full-width">
    <div class="detail-card-header"><i class="fas fa-comment-dots"></i> Notes CRM</div>
    <div class="detail-card-body">
      <form method="POST" action="/admin/leads/add-note" class="note-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="lead_id" value="<?= $leadId ?>">
        <textarea name="content" placeholder="Ajouter une note..." required></textarea>
        <div class="note-form-actions">
          <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
        </div>
      </form>

      <?php if (empty($notes)): ?>
        <div class="empty-note">Aucune note pour ce lead.</div>
      <?php else: ?>
        <?php foreach ($notes as $note): ?>
          <div class="note-item">
            <div class="note-meta">
              <span class="note-author"><i class="fas fa-user"></i> <?= e((string) ($note['author'] ?? 'Admin')) ?></span>
              <span>
                <span class="note-date"><?= e((string) ($note['created_at'] ?? '')) ?></span>
                <form method="POST" action="/admin/leads/delete-note" class="delete-form" style="display:inline;" onsubmit="return confirm('Supprimer cette note ?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="note_id" value="<?= (int) ($note['id'] ?? 0) ?>">
                  <input type="hidden" name="lead_id" value="<?= $leadId ?>">
                  <button type="submit" class="note-delete" title="Supprimer"><i class="fas fa-times"></i></button>
                </form>
              </span>
            </div>
            <div class="note-content"><?= e((string) ($note['content'] ?? '')) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Activity Log -->
  <div class="detail-card full-width">
    <div class="detail-card-header"><i class="fas fa-history"></i> Historique d'activit&eacute;</div>
    <div class="detail-card-body">
      <?php if (empty($activities)): ?>
        <div class="empty-note">Aucune activit&eacute; enregistr&eacute;e.</div>
      <?php else: ?>
        <?php foreach ($activities as $activity): ?>
          <?php
            $iconClass = match($activity['activity_type'] ?? '') {
              'note_added' => 'note',
              'statut_change' => 'statut',
              default => '',
            };
            $icon = match($activity['activity_type'] ?? '') {
              'note_added' => 'fa-comment',
              'statut_change' => 'fa-exchange-alt',
              default => 'fa-circle',
            };
          ?>
          <div class="activity-item">
            <div class="activity-icon <?= $iconClass ?>"><i class="fas <?= $icon ?>"></i></div>
            <div>
              <div class="activity-text"><?= e((string) ($activity['description'] ?? '')) ?></div>
              <div class="activity-date"><?= e((string) ($activity['created_at'] ?? '')) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
