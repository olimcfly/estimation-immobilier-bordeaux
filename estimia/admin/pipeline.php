<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/guard.php';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    redirect('login.php');
}

$pdo = Database::getConnection();

$stmtLeads = $pdo->query(
    'SELECT e.*, a.nom AS agent_nom, a.prenom AS agent_prenom
     FROM estimations e
     LEFT JOIN agents a ON a.id = e.agent_assigne
     ORDER BY e.lead_score DESC, e.created_at DESC'
);
$allLeads = $stmtLeads->fetchAll();

$columns = [
    'nouveau' => ['label' => 'Nouveau', 'column_class' => 'bg-blue-50 border-t-4 border-blue-500'],
    'contacte' => ['label' => 'Contacté', 'column_class' => 'bg-yellow-50 border-t-4 border-yellow-500'],
    'qualifie' => ['label' => 'Qualifié', 'column_class' => 'bg-orange-50 border-t-4 border-orange-500'],
    'en_negociation' => ['label' => 'En négociation', 'column_class' => 'bg-purple-50 border-t-4 border-purple-500'],
    'converti' => ['label' => 'Converti ✓', 'column_class' => 'bg-green-50 border-t-4 border-green-500'],
    'perdu' => ['label' => 'Perdu ✗', 'column_class' => 'bg-red-50 border-t-4 border-red-500'],
];

$leadsByStatus = [];
foreach (array_keys($columns) as $statusKey) {
    $leadsByStatus[$statusKey] = [];
}

foreach ($allLeads as $lead) {
    $status = (string) ($lead['lead_statut'] ?? 'nouveau');
    if (!isset($leadsByStatus[$status])) {
        $status = 'nouveau';
    }
    $leadsByStatus[$status][] = $lead;
}

$adminPageTitle = 'Pipeline';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-bold text-gray-900">Pipeline des leads</h2>
    <p class="text-sm text-gray-500"><?php echo count($allLeads); ?> leads au total</p>
</div>

<div class="flex gap-4 overflow-x-auto pb-4">
    <?php foreach ($columns as $statusKey => $meta):
        $statusLeads = $leadsByStatus[$statusKey] ?? [];
        $visibleLeads = array_slice($statusLeads, 0, 20);
        $hiddenLeads = array_slice($statusLeads, 20);
        ?>
        <section
            class="kanban-column w-[280px] min-w-[280px] shrink-0 rounded-xl border <?php echo $meta['column_class']; ?>"
            data-status="<?php echo $statusKey; ?>"
        >
            <header class="flex items-center justify-between border-b bg-white/60 px-3 py-2">
                <h3 class="text-sm font-bold text-gray-900"><?php echo $meta['label']; ?></h3>
                <span class="kanban-count rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-gray-700"><?php echo count($statusLeads); ?></span>
            </header>

            <div class="kanban-list max-h-[calc(100vh-240px)] overflow-y-auto p-3" data-status="<?php echo $statusKey; ?>">
                <?php foreach ($visibleLeads as $lead):
                    $score = (int) ($lead['lead_score'] ?? 0);
                    $scoreColor = getLeadColor($score);
                    $agentInitial = strtoupper(substr((string) ($lead['agent_prenom'] ?? ''), 0, 1));
                    if ($agentInitial === '') {
                        $agentInitial = '?';
                    }
                    $leadName = (string) ($lead['nom'] ?? '');
                    ?>
                    <article
                        class="kanban-card mb-3 cursor-grab rounded-lg border bg-white p-4 shadow-sm"
                        draggable="true"
                        data-lead-id="<?php echo (int) $lead['id']; ?>"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900"><?php echo sanitize($leadName !== '' ? $leadName : 'Anonyme'); ?></p>
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold text-white <?php echo $scoreColor === 'green' ? 'bg-green-500' : ($scoreColor === 'yellow' ? 'bg-yellow-500' : ($scoreColor === 'orange' ? 'bg-orange-500' : 'bg-red-500')); ?>"><?php echo $score; ?></span>
                        </div>

                        <p class="mt-1 line-clamp-2 text-xs text-gray-500"><?php echo sanitize((string) ($lead['adresse'] ?? '-')); ?></p>
                        <p class="mt-1 text-xs text-gray-600"><?php echo sanitize((string) ($lead['type_bien'] ?? '-')); ?> • <?php echo (int) ($lead['surface'] ?? 0); ?> m²</p>
                        <p class="mt-2 text-sm font-bold text-primary"><?php echo formatPrice((int) ($lead['prix_estime'] ?? 0)); ?></p>

                        <div class="mt-3 flex items-center justify-between border-t pt-2">
                            <p class="text-xs text-gray-400"><?php echo formatDateRelative((string) ($lead['created_at'] ?? '')); ?></p>
                            <div class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white" title="<?php echo sanitize(trim((string) ($lead['agent_prenom'] ?? '') . ' ' . (string) ($lead['agent_nom'] ?? ''))); ?>">
                                <?php echo sanitize($agentInitial); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php foreach ($hiddenLeads as $lead):
                    $score = (int) ($lead['lead_score'] ?? 0);
                    $scoreColor = getLeadColor($score);
                    $agentInitial = strtoupper(substr((string) ($lead['agent_prenom'] ?? ''), 0, 1));
                    if ($agentInitial === '') {
                        $agentInitial = '?';
                    }
                    $leadName = (string) ($lead['nom'] ?? '');
                    ?>
                    <article
                        class="kanban-card mb-3 hidden cursor-grab rounded-lg border bg-white p-4 shadow-sm"
                        draggable="true"
                        data-lead-id="<?php echo (int) $lead['id']; ?>"
                        data-hidden-card="true"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900"><?php echo sanitize($leadName !== '' ? $leadName : 'Anonyme'); ?></p>
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold text-white <?php echo $scoreColor === 'green' ? 'bg-green-500' : ($scoreColor === 'yellow' ? 'bg-yellow-500' : ($scoreColor === 'orange' ? 'bg-orange-500' : 'bg-red-500')); ?>"><?php echo $score; ?></span>
                        </div>
                        <p class="mt-1 line-clamp-2 text-xs text-gray-500"><?php echo sanitize((string) ($lead['adresse'] ?? '-')); ?></p>
                        <p class="mt-1 text-xs text-gray-600"><?php echo sanitize((string) ($lead['type_bien'] ?? '-')); ?> • <?php echo (int) ($lead['surface'] ?? 0); ?> m²</p>
                        <p class="mt-2 text-sm font-bold text-primary"><?php echo formatPrice((int) ($lead['prix_estime'] ?? 0)); ?></p>
                        <div class="mt-3 flex items-center justify-between border-t pt-2">
                            <p class="text-xs text-gray-400"><?php echo formatDateRelative((string) ($lead['created_at'] ?? '')); ?></p>
                            <div class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold text-white"><?php echo sanitize($agentInitial); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (count($hiddenLeads) > 0): ?>
                    <button type="button" class="see-more w-full rounded-lg border border-dashed border-gray-300 px-3 py-2 text-xs font-semibold text-gray-600 hover:border-gray-400" data-status="<?php echo $statusKey; ?>">
                        Voir plus (<?php echo count($hiddenLeads); ?>)
                    </button>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<script>
    let draggedCard = null;

    function updateColumnCounts() {
        document.querySelectorAll('.kanban-column').forEach((column) => {
            const countEl = column.querySelector('.kanban-count');
            const cards = column.querySelectorAll('.kanban-card');
            if (countEl) {
                countEl.textContent = String(cards.length);
            }
        });
    }

    async function updateLeadStatus(leadId, status) {
        const response = await fetch('api/update-lead.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.adminCsrfToken || '',
            },
            body: JSON.stringify({lead_id: Number(leadId), field: 'lead_statut', value: status}),
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur de mise à jour');
        }
    }

    document.querySelectorAll('.kanban-card').forEach((card) => {
        card.addEventListener('dragstart', function () {
            draggedCard = this;
            this.classList.add('opacity-60');
        });

        card.addEventListener('dragend', function () {
            this.classList.remove('opacity-60');
            draggedCard = null;
        });
    });

    document.querySelectorAll('.kanban-list').forEach((dropZone) => {
        dropZone.addEventListener('dragover', function (event) {
            event.preventDefault();
            this.classList.add('ring-2', 'ring-primary', 'ring-dashed');
        });

        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('ring-2', 'ring-primary', 'ring-dashed');
        });

        dropZone.addEventListener('drop', async function (event) {
            event.preventDefault();
            this.classList.remove('ring-2', 'ring-primary', 'ring-dashed');

            if (!draggedCard) {
                return;
            }

            const newStatus = this.dataset.status;
            const leadId = draggedCard.dataset.leadId;
            const originZone = draggedCard.closest('.kanban-list');
            if (!leadId || !newStatus || !originZone) {
                return;
            }

            this.insertBefore(draggedCard, this.querySelector('.see-more') || null);
            updateColumnCounts();

            try {
                await updateLeadStatus(leadId, newStatus);
            } catch (error) {
                originZone.insertBefore(draggedCard, originZone.querySelector('.see-more') || null);
                updateColumnCounts();
                alert(error.message);
            }
        });
    });

    document.querySelectorAll('.see-more').forEach((button) => {
        button.addEventListener('click', function () {
            const status = this.dataset.status;
            const column = document.querySelector(`.kanban-list[data-status="${status}"]`);
            if (!column) {
                return;
            }

            column.querySelectorAll('[data-hidden-card="true"]').forEach((card) => {
                card.classList.remove('hidden');
                card.removeAttribute('data-hidden-card');
            });
            this.remove();
        });
    });
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
