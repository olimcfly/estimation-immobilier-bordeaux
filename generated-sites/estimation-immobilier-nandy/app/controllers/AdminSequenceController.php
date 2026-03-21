<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;

final class AdminSequenceController
{
    /**
     * BANT criteria for lead qualification.
     */
    private const BANT_CRITERIA = [
        'budget' => [
            'label' => 'Budget',
            'questions' => [
                'Quel est votre budget pour ce projet immobilier ?',
                'Avez-vous obtenu un pr&eacute;-accord bancaire ?',
                'Quel apport personnel avez-vous pr&eacute;vu ?',
            ],
        ],
        'authority' => [
            'label' => 'Autorit&eacute;',
            'questions' => [
                '&Ecirc;tes-vous le d&eacute;cisionnaire principal ?',
                'D\'autres personnes participent-elles &agrave; la d&eacute;cision ?',
            ],
        ],
        'need' => [
            'label' => 'Besoin',
            'questions' => [
                'Quel type de bien recherchez-vous ?',
                'Quels sont vos crit&egrave;res prioritaires (quartier, surface, etc.) ?',
                'Pourquoi souhaitez-vous acheter/vendre maintenant ?',
            ],
        ],
        'timeline' => [
            'label' => 'D&eacute;lai',
            'questions' => [
                'Dans quel d&eacute;lai souhaitez-vous r&eacute;aliser cette op&eacute;ration ?',
                'Avez-vous d&eacute;j&agrave; commenc&eacute; vos recherches ?',
            ],
        ],
    ];

    /**
     * NeuroPersona profiles.
     */
    private const NEUROPERSONAS = [
        'analytique' => [
            'label' => 'Analytique',
            'icon' => 'fa-chart-bar',
            'color' => '#3b82f6',
            'description' => 'D&eacute;cision bas&eacute;e sur les donn&eacute;es, les chiffres et l\'analyse rationnelle.',
            'email_tone' => 'Pr&eacute;cis, factuel, avec des chiffres et des comparaisons.',
            'article_topics' => [
                '&Eacute;volution des prix au m&sup2; par quartier',
                'Analyse comparative du march&eacute; immobilier de nandy',
                'Rendement locatif : les chiffres cl&eacute;s',
                'Statistiques des transactions immobili&egrave;res',
            ],
        ],
        'expressif' => [
            'label' => 'Expressif',
            'icon' => 'fa-heart',
            'color' => '#ef4444',
            'description' => 'D&eacute;cision bas&eacute;e sur les &eacute;motions, le ressenti et l\'enthousiasme.',
            'email_tone' => 'Chaleureux, enthousiaste, avec des images et des t&eacute;moignages.',
            'article_topics' => [
                'Coup de coeur : les plus beaux quartiers de Nandy',
                'T&eacute;moignages de nos clients satisfaits',
                'Vivre &agrave; Nandy : un art de vivre unique',
                'Les p&eacute;pites cach&eacute;es du march&eacute; de nandy',
            ],
        ],
        'directif' => [
            'label' => 'Directif',
            'icon' => 'fa-bullseye',
            'color' => '#f59e0b',
            'description' => 'D&eacute;cision rapide, orient&eacute;e r&eacute;sultats et efficacit&eacute;.',
            'email_tone' => 'Direct, concis, avec des actions cl&eacute;s et des d&eacute;lais.',
            'article_topics' => [
                'Guide express : acheter &agrave; Nandy en 30 jours',
                'Les 5 erreurs &agrave; &eacute;viter lors d\'un achat immobilier',
                'Checklist : les &eacute;tapes cl&eacute;s de votre projet',
                'Investir &agrave; Nandy : les opportunit&eacute;s &agrave; saisir maintenant',
            ],
        ],
        'aimable' => [
            'label' => 'Aimable',
            'icon' => 'fa-handshake',
            'color' => '#22c55e',
            'description' => 'D&eacute;cision bas&eacute;e sur la confiance, la relation et la s&eacute;curit&eacute;.',
            'email_tone' => 'Rassurant, empathique, avec un accompagnement personnalis&eacute;.',
            'article_topics' => [
                'Notre accompagnement pas &agrave; pas pour votre projet',
                'Comment choisir le bon quartier pour votre famille',
                'S&eacute;curiser votre achat immobilier : nos conseils',
                'Les garanties qui prot&egrave;gent votre investissement',
            ],
        ],
    ];

    public function index(): void
    {
        AuthController::requireAuth();

        $sequences = [];
        $personas = [];

        try {
            $pdo = Database::connection();

            if (Database::tableExists('email_sequences')) {
                $stmt = $pdo->query('SELECT * FROM email_sequences ORDER BY updated_at DESC');
                $sequences = $stmt->fetchAll();
            }

            if (Database::tableExists('lead_personas')) {
                $stmt = $pdo->query('SELECT lp.*, l.nom, l.email FROM lead_personas lp LEFT JOIN leads l ON lp.lead_id = l.id ORDER BY lp.created_at DESC LIMIT 50');
                $personas = $stmt->fetchAll();
            }
        } catch (\Throwable $e) {
            error_log('AdminSequence error: ' . $e->getMessage());
        }

        View::renderAdmin('admin/sequences', [
            'page_title' => 'S&eacute;quences Email & Personas',
            'admin_page_title' => 'Séquences Email',
            'admin_page' => 'sequences',
            'breadcrumb' => 'S&eacute;quences',
            'sequences' => $sequences,
            'personas' => $personas,
            'bantCriteria' => self::BANT_CRITERIA,
            'neuropersonas' => self::NEUROPERSONAS,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $sequence = null;
        $steps = [];

        if ($id > 0) {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('SELECT * FROM email_sequences WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $sequence = $stmt->fetch();

            if ($sequence && Database::tableExists('email_sequence_steps')) {
                $stmt = $pdo->prepare('SELECT * FROM email_sequence_steps WHERE sequence_id = :id ORDER BY step_order ASC');
                $stmt->execute(['id' => $id]);
                $steps = $stmt->fetchAll();
            }
        }

        View::renderAdmin('admin/sequence-edit', [
            'page_title' => $sequence ? 'Modifier la s&eacute;quence' : 'Nouvelle s&eacute;quence',
            'admin_page' => 'sequences',
            'breadcrumb' => $sequence ? 'Modifier s&eacute;quence' : 'Nouvelle s&eacute;quence',
            'sequence' => $sequence,
            'steps' => $steps,
            'neuropersonas' => self::NEUROPERSONAS,
            'bantCriteria' => self::BANT_CRITERIA,
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $persona = trim((string) ($_POST['persona'] ?? ''));
        $triggerEvent = trim((string) ($_POST['trigger_event'] ?? 'lead_created'));
        $status = trim((string) ($_POST['status'] ?? 'draft'));
        $stepsJson = trim((string) ($_POST['steps_json'] ?? '[]'));

        if ($name === '') {
            $_SESSION['seq_flash'] = ['type' => 'error', 'message' => 'Nom de la s&eacute;quence requis.'];
            header('Location: /admin/sequences/edit' . ($id > 0 ? '?id=' . $id : ''));
            exit;
        }

        $pdo = Database::connection();

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE email_sequences SET name = :name, persona = :persona, trigger_event = :trigger_event, status = :status, updated_at = NOW() WHERE id = :id');
                $stmt->execute([
                    'name' => $name,
                    'persona' => $persona,
                    'trigger_event' => $triggerEvent,
                    'status' => $status,
                    'id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO email_sequences (name, persona, trigger_event, status, created_at, updated_at) VALUES (:name, :persona, :trigger_event, :status, NOW(), NOW())');
                $stmt->execute([
                    'name' => $name,
                    'persona' => $persona,
                    'trigger_event' => $triggerEvent,
                    'status' => $status,
                ]);
                $id = (int) $pdo->lastInsertId();
            }

            // Save steps
            $steps = json_decode($stepsJson, true) ?: [];
            if (!empty($steps) && Database::tableExists('email_sequence_steps')) {
                $pdo->prepare('DELETE FROM email_sequence_steps WHERE sequence_id = :id')->execute(['id' => $id]);

                $stmt = $pdo->prepare('INSERT INTO email_sequence_steps (sequence_id, step_order, delay_days, subject, body_html, created_at) VALUES (:seq_id, :step_order, :delay_days, :subject, :body_html, NOW())');

                foreach ($steps as $i => $step) {
                    $stmt->execute([
                        'seq_id' => $id,
                        'step_order' => $i + 1,
                        'delay_days' => (int) ($step['delay_days'] ?? 0),
                        'subject' => trim($step['subject'] ?? ''),
                        'body_html' => trim($step['body_html'] ?? ''),
                    ]);
                }
            }

            $_SESSION['seq_flash'] = ['type' => 'success', 'message' => 'S&eacute;quence sauvegard&eacute;e.'];
        } catch (\Throwable $e) {
            $_SESSION['seq_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/sequences');
        exit;
    }

    public function delete(): void
    {
        AuthController::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::connection();
            $pdo->prepare('DELETE FROM email_sequences WHERE id = :id')->execute(['id' => $id]);
            $_SESSION['seq_flash'] = ['type' => 'success', 'message' => 'S&eacute;quence supprim&eacute;e.'];
        }

        header('Location: /admin/sequences');
        exit;
    }

    /**
     * Save persona assessment for a lead.
     */
    public function savePersona(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $leadId = (int) ($_POST['lead_id'] ?? 0);
        $neuropersona = trim((string) ($_POST['neuropersona'] ?? ''));
        $bantBudget = trim((string) ($_POST['bant_budget'] ?? ''));
        $bantAuthority = trim((string) ($_POST['bant_authority'] ?? ''));
        $bantNeed = trim((string) ($_POST['bant_need'] ?? ''));
        $bantTimeline = trim((string) ($_POST['bant_timeline'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if ($leadId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Lead ID requis.']);
            return;
        }

        try {
            $pdo = Database::connection();

            // Check if persona already exists for this lead
            $stmt = $pdo->prepare('SELECT id FROM lead_personas WHERE lead_id = :lead_id');
            $stmt->execute(['lead_id' => $leadId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $stmt = $pdo->prepare('UPDATE lead_personas SET neuropersona = :neuropersona, bant_budget = :bant_budget, bant_authority = :bant_authority, bant_need = :bant_need, bant_timeline = :bant_timeline, notes = :notes, updated_at = NOW() WHERE lead_id = :lead_id');
            } else {
                $stmt = $pdo->prepare('INSERT INTO lead_personas (lead_id, neuropersona, bant_budget, bant_authority, bant_need, bant_timeline, notes, created_at, updated_at) VALUES (:lead_id, :neuropersona, :bant_budget, :bant_authority, :bant_need, :bant_timeline, :notes, NOW(), NOW())');
            }

            $stmt->execute([
                'lead_id' => $leadId,
                'neuropersona' => $neuropersona,
                'bant_budget' => $bantBudget,
                'bant_authority' => $bantAuthority,
                'bant_need' => $bantNeed,
                'bant_timeline' => $bantTimeline,
                'notes' => $notes,
            ]);

            // Get article suggestions based on persona
            $suggestions = [];
            if (isset(self::NEUROPERSONAS[$neuropersona])) {
                $suggestions = self::NEUROPERSONAS[$neuropersona]['article_topics'];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Persona sauvegard&eacute;.',
                'article_suggestions' => $suggestions,
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Get article suggestions based on persona.
     */
    public function articleSuggestions(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $persona = trim((string) ($_GET['persona'] ?? ''));

        if (!isset(self::NEUROPERSONAS[$persona])) {
            echo json_encode(['success' => false, 'suggestions' => []]);
            return;
        }

        echo json_encode([
            'success' => true,
            'persona' => self::NEUROPERSONAS[$persona],
            'suggestions' => self::NEUROPERSONAS[$persona]['article_topics'],
        ]);
    }
}
