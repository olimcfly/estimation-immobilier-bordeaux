<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadActivity;
use App\Models\Partenaire;

final class AdminLeadController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $leads = [];
        $dbError = null;

        try {
            $leadModel = new Lead();
            $scoreFilter = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
            $typeFilter = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
            $statutFilter = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;

            $leads = $leadModel->findAllLeads();

            if ($typeFilter !== null && in_array($typeFilter, ['tendance', 'qualifie'], true)) {
                $leads = array_filter($leads, fn($l) => ($l['lead_type'] ?? '') === $typeFilter);
                $leads = array_values($leads);
            }
            if ($scoreFilter !== null && in_array($scoreFilter, ['chaud', 'tiede', 'froid'], true)) {
                $leads = array_filter($leads, fn($l) => ($l['score'] ?? '') === $scoreFilter);
                $leads = array_values($leads);
            }
            if ($statutFilter !== null) {
                $leads = array_filter($leads, fn($l) => ($l['statut'] ?? '') === $statutFilter);
                $leads = array_values($leads);
            }
        } catch (\Throwable $e) {
            $dbError = 'Base de données indisponible : les leads ne peuvent pas être chargés.';
        }

        View::renderAdmin('admin/leads', [
            'page_title' => 'Leads - Admin CRM',
            'admin_page_title' => 'Leads',
            'admin_page' => 'leads',
            'breadcrumb' => 'Leads',
            'leads' => $leads,
            'leadCount' => count($leads),
            'dbError' => $dbError,
        ]);
    }

    public function show(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $lead = $leadModel->findById($id);
        if ($lead === null) {
            header('Location: /admin/leads');
            exit;
        }

        $notes = [];
        $activities = [];
        $partenaire = null;

        try {
            $noteModel = new LeadNote();
            $notes = $noteModel->findByLeadId($id);
        } catch (\Throwable) {
        }

        try {
            $activityModel = new LeadActivity();
            $activities = $activityModel->findByLeadId($id);
        } catch (\Throwable) {
        }

        if (!empty($lead['partenaire_id'])) {
            try {
                $partenaireModel = new Partenaire();
                $partenaire = $partenaireModel->findById((int) $lead['partenaire_id']);
            } catch (\Throwable) {
            }
        }

        View::renderAdmin('admin/lead-detail', [
            'page_title' => 'Lead #' . $id . ' - Admin CRM',
            'admin_page' => 'leads',
            'breadcrumb' => 'Lead #' . $id,
            'lead' => $lead,
            'notes' => $notes,
            'activities' => $activities,
            'partenaire' => $partenaire,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $lead = $leadModel->findById($id);
        if ($lead === null) {
            header('Location: /admin/leads');
            exit;
        }

        $partenaires = [];
        try {
            $partenaireModel = new Partenaire();
            $partenaires = $partenaireModel->findActifs();
        } catch (\Throwable) {
        }

        View::renderAdmin('admin/lead-edit', [
            'page_title' => 'Modifier Lead #' . $id,
            'admin_page' => 'leads',
            'breadcrumb' => 'Modifier Lead #' . $id,
            'lead' => $lead,
            'partenaires' => $partenaires,
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        try {
            $data = [];

            $statut = trim((string) ($_POST['statut'] ?? ''));
            if ($statut !== '') {
                $data['statut'] = $statut;
            }

            $score = trim((string) ($_POST['score'] ?? ''));
            if (in_array($score, ['chaud', 'tiede', 'froid'], true)) {
                $data['score'] = $score;
            }

            $partenaireId = isset($_POST['partenaire_id']) && $_POST['partenaire_id'] !== ''
                ? (int) $_POST['partenaire_id'] : null;
            $data['partenaire_id'] = $partenaireId;

            $commissionTaux = isset($_POST['commission_taux']) && $_POST['commission_taux'] !== ''
                ? (float) $_POST['commission_taux'] : null;
            $data['commission_taux'] = $commissionTaux;

            $commissionMontant = isset($_POST['commission_montant']) && $_POST['commission_montant'] !== ''
                ? (float) $_POST['commission_montant'] : null;
            $data['commission_montant'] = $commissionMontant;

            $assigneA = trim((string) ($_POST['assigne_a'] ?? ''));
            $data['assigne_a'] = $assigneA !== '' ? $assigneA : null;

            $dateMandat = trim((string) ($_POST['date_mandat'] ?? ''));
            $data['date_mandat'] = $dateMandat !== '' ? $dateMandat : null;

            $dateCompromis = trim((string) ($_POST['date_compromis'] ?? ''));
            $data['date_compromis'] = $dateCompromis !== '' ? $dateCompromis : null;

            $dateSignature = trim((string) ($_POST['date_signature'] ?? ''));
            $data['date_signature'] = $dateSignature !== '' ? $dateSignature : null;

            $prixVente = isset($_POST['prix_vente']) && $_POST['prix_vente'] !== ''
                ? (float) $_POST['prix_vente'] : null;
            $data['prix_vente'] = $prixVente;

            $leadModel = new Lead();
            $oldLead = $leadModel->findById($id);
            $leadModel->updateLeadDetails($id, $data);

            // Log activity for status change
            if ($oldLead !== null && isset($data['statut']) && $oldLead['statut'] !== $data['statut']) {
                try {
                    $activityModel = new LeadActivity();
                    $activityModel->log($id, 'statut_change', 'Statut modifié de "' . ($oldLead['statut'] ?? '') . '" à "' . $data['statut'] . '"');
                } catch (\Throwable) {
                }
            }

            header('Location: /admin/leads/detail?id=' . $id);
            exit;
        } catch (\Throwable $e) {
            $leadModel = new Lead();
            $lead = $leadModel->findById($id);
            $partenaires = [];
            try {
                $partenaireModel = new Partenaire();
                $partenaires = $partenaireModel->findActifs();
            } catch (\Throwable) {
            }

            View::renderAdmin('admin/lead-edit', [
                'page_title' => 'Modifier Lead #' . $id,
                'admin_page' => 'leads',
                'breadcrumb' => 'Modifier Lead #' . $id,
                'lead' => $lead,
                'partenaires' => $partenaires,
                'errors' => [$e->getMessage()],
            ]);
        }
    }

    public function updateStatut(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $statut = trim((string) ($_POST['statut'] ?? ''));

        if ($id > 0 && $statut !== '') {
            $leadModel = new Lead();
            $oldLead = $leadModel->findById($id);
            $leadModel->updateStatut($id, $statut);

            if ($oldLead !== null && $oldLead['statut'] !== $statut) {
                try {
                    $activityModel = new LeadActivity();
                    $activityModel->log($id, 'statut_change', 'Statut modifié de "' . ($oldLead['statut'] ?? '') . '" à "' . $statut . '"');
                } catch (\Throwable) {
                }
            }
        }

        header('Location: /admin/leads');
        exit;
    }

    public function addNote(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $leadId = isset($_POST['lead_id']) ? (int) $_POST['lead_id'] : 0;
        $content = trim((string) ($_POST['content'] ?? ''));
        $author = trim((string) ($_SESSION['admin_name'] ?? 'Admin'));

        if ($leadId > 0 && $content !== '') {
            try {
                $noteModel = new LeadNote();
                $noteModel->create($leadId, $content, $author);

                $activityModel = new LeadActivity();
                $activityModel->log($leadId, 'note_added', 'Note ajoutée par ' . $author);
            } catch (\Throwable) {
            }
        }

        header('Location: /admin/leads/detail?id=' . $leadId);
        exit;
    }

    public function deleteNote(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $noteId = isset($_POST['note_id']) ? (int) $_POST['note_id'] : 0;
        $leadId = isset($_POST['lead_id']) ? (int) $_POST['lead_id'] : 0;

        if ($noteId > 0) {
            try {
                $noteModel = new LeadNote();
                $noteModel->delete($noteId);
            } catch (\Throwable) {
            }
        }

        header('Location: /admin/leads/detail?id=' . $leadId);
        exit;
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            $leadModel = new Lead();
            $leadModel->deleteById($id);
        }

        header('Location: /admin/leads');
        exit;
    }
}
