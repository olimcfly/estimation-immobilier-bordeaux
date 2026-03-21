<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Core\Validator;
use App\Core\View;
use App\Models\DesignTemplate;
use App\Models\Lead;
use App\Services\EstimationService;
use App\Services\LeadNotificationService;
use App\Services\LeadScoringService;
use App\Services\PerplexityService;

final class EstimationController
{
    private const LEAD_FORM_TTL_SECONDS = 1800;
    private const LEAD_SUBMIT_COOLDOWN_SECONDS = 60;

    private EstimationService $estimationService;

    public function __construct(?EstimationService $estimationService = null)
    {
        $this->estimationService = $estimationService ?? new EstimationService(new PerplexityService());
    }

    public function index(): void
    {
        View::render('estimation/index', [
            'errors' => [],
        ]);
    }

    public function leads(): void
    {
        AuthController::requireAuth();

        $leads = [];
        $dbError = null;
        $statutCounts = [];

        $filterScore = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
        $filterStatut = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $filterType = isset($_GET['type']) ? trim((string) $_GET['type']) : null;

        try {
            $leadModel = new Lead();
            $hasFilters = ($filterScore !== null && $filterScore !== '')
                       || ($filterStatut !== null && $filterStatut !== '')
                       || ($filterType !== null && $filterType !== '');

            if ($hasFilters) {
                $leads = $leadModel->findAllLeadsFiltered(
                    $filterScore ?: null,
                    $filterStatut ?: null,
                    $filterType ?: null
                );
            } else {
                $leads = $leadModel->findAllLeads();
            }
            $statutCounts = $leadModel->countByStatut();
        } catch (\Throwable $e) {
            $dbError = 'Base de données indisponible : les leads ne peuvent pas être chargés.';
        }

        View::renderAdmin('admin/leads', [
            'page_title' => 'Gestion des Leads - Admin',
            'admin_page_title' => 'Leads',
            'admin_current_page' => 'leads',
            'leads' => $leads,
            'leadCount' => count($leads),
            'dbError' => $dbError,
            'statutCounts' => $statutCounts,
            'filterScore' => $filterScore,
            'filterStatut' => $filterStatut,
            'filterType' => $filterType,
        ]);
    }

    public function estimate(): void
    {
        try {
            $city = Validator::string($_POST, 'ville', 2, 120);
            $typeKey = array_key_exists('type', $_POST) ? 'type' : 'type_bien';
            $propertyType = Validator::string($_POST, $typeKey, 2, 80);
            $surface = Validator::float($_POST, 'surface', 5, 10000);

            $roomsRaw = trim((string) ($_POST['pieces'] ?? ''));
            $rooms = $roomsRaw !== '' ? Validator::int($_POST, 'pieces', 1, 50) : 3;

            $estimate = $this->estimationService->estimate($city, $propertyType, $surface, $rooms);
            $now = time();
            $_SESSION['lead_form_context'] = [
                'ip' => $this->getClientIp(),
                'issued_at' => $now,
                'expires_at' => $now + self::LEAD_FORM_TTL_SECONDS,
            ];

            // Capture lead "tendance" (sans coordonnées)
            try {
                $leadModel = new Lead();
                $leadModel->create([
                    'lead_type' => 'tendance',
                    'ville' => $city,
                    'type_bien' => $propertyType,
                    'surface_m2' => $surface,
                    'pieces' => $rooms,
                    'estimation' => $estimate['estimated_mid'],
                    'score' => 'froid',
                    'statut' => 'nouveau',
                ]);
            } catch (\Throwable $e) {
                // Silently fail — don't block the estimation result
                error_log('Tendance lead capture failed: ' . $e->getMessage());
            }

            View::render('estimation/result', [
                'estimate' => $estimate,
                'errors' => [],
            ]);
        } catch (\Throwable $throwable) {
            View::render('estimation/index', [
                'errors' => [$throwable->getMessage()],
            ]);
        }
    }

    public function apiEstimate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = $this->readApiInput();
            $sanitized = $this->sanitizeEstimationPayload($input);

            $cityKey = $sanitized['ville'] !== '' ? 'ville' : 'localisation';
            $typeKey = $sanitized['type'] !== '' ? 'type' : 'type_bien';

            $city = Validator::string($sanitized, $cityKey, 2, 120);
            $propertyType = Validator::string($sanitized, $typeKey, 2, 80);
            $surface = Validator::float($sanitized, 'surface', 5, 10000);
            $rooms = Validator::int($sanitized, 'pieces', 1, 50);

            $estimate = $this->estimationService->estimate($city, $propertyType, $surface, $rooms);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $estimate,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $throwable->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }
    }

    public function storeLead(): void
    {
        try {
            $this->assertLeadRequestAllowed();

            $nom = Validator::string($_POST, 'nom', 2, 120);
            $email = Validator::email($_POST, 'email');
            $telephone = Validator::string($_POST, 'telephone', 6, 30);
            $adresseInput = trim((string) ($_POST['adresse'] ?? ''));
            $adresse = $adresseInput !== '' ? Validator::string($_POST, 'adresse', 5, 255) : 'Non renseignée';
            $ville = Validator::string($_POST, 'ville', 2, 120);
            $estimation = Validator::float($_POST, 'estimation', 10000, 100000000);
            $urgence = Validator::string($_POST, 'urgence', 3, 40);
            $motivation = Validator::string($_POST, 'motivation', 3, 80);
            $notesRaw = trim((string) ($_POST['notes'] ?? ($_POST['message'] ?? '')));
            $contactPrefere = trim((string) ($_POST['contact_prefere'] ?? ''));
            $layout = trim((string) ($_POST['layout'] ?? ''));
            $notes = $notesRaw;
            if ($contactPrefere !== '') {
                $notes = $notes !== '' ? "Contact préféré: {$contactPrefere}\n{$notes}" : "Contact préféré: {$contactPrefere}";
            }
            if ($layout !== '') {
                $template = (new DesignTemplate())->findBySlug($layout);
                if ($template === null) {
                    throw new \InvalidArgumentException("Template layout inconnu: {$layout}");
                }

                $layoutNote = 'Template layout: ' . (string) $template['slug'];
                $notes = $notes !== '' ? "{$layoutNote}\n{$notes}" : $layoutNote;
            }
            if (mb_strlen($notes) > 1500) {
                throw new \InvalidArgumentException('Les notes ne doivent pas dépasser 1500 caractères.');
            }

            $scoring = new LeadScoringService();
            $temperature = $scoring->score($estimation, $urgence, $motivation);

            $leadModel = new Lead();
            $leadId = $leadModel->create([
                'lead_type' => 'qualifie',
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'estimation' => $estimation,
                'urgence' => $urgence,
                'motivation' => $motivation,
                'notes' => $notes,
                'score' => $temperature,
                'statut' => 'nouveau',
            ]);

            $_SESSION['lead_last_submit'] = [
                'ip' => $this->getClientIp(),
                'submitted_at' => time(),
            ];

            LeadNotificationService::notify($leadId, $temperature, [
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'estimation' => $estimation,
                'urgence' => $urgence,
                'motivation' => $motivation,
                'notes' => $notes,
                'statut' => 'nouveau',
            ]);

            View::render('estimation/lead_saved', [
                'leadId' => $leadId,
                'temperature' => $temperature,
                'lead' => [
                    'nom' => $nom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'ville' => $ville,
                    'estimation' => $estimation,
                    'urgence' => $urgence,
                    'motivation' => $motivation,
                    'notes' => $notes,
                    'statut' => 'nouveau',
                ],
            ]);
        } catch (\Throwable $throwable) {
            View::render('estimation/index', [
                'errors' => [$throwable->getMessage()],
            ]);
        }
    }


    public function updateLeadStatut(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $statut = trim((string) ($_POST['statut'] ?? ''));

        if ($id > 0 && $statut !== '') {
            $leadModel = new Lead();
            $leadModel->updateStatut($id, $statut);
        }

        header('Location: /admin/leads');
        exit;
    }

    public function updateLeadInline(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $field = trim((string) ($_POST['field'] ?? ''));
        $value = trim((string) ($_POST['value'] ?? ''));

        if ($id <= 0 || $field === '' || $value === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $leadModel = new Lead();
        $ok = false;

        if ($field === 'statut') {
            $ok = $leadModel->updateStatut($id, $value);
        } elseif ($field === 'score') {
            $ok = $leadModel->updateScore($id, $value);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Champ non supporté'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
    }

    public function pipeline(): void
    {
        AuthController::requireAuth();

        $leadModel = new Lead();

        try {
            $statutCounts = $leadModel->countByStatut();
            $totalLeads = array_sum($statutCounts);

            $allStatuts = [
                'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
                'mandat_simple', 'mandat_exclusif', 'compromis_vente',
                'signe', 'co_signature_partenaire', 'assigne_autre',
            ];

            $leadsByStatut = [];
            foreach ($allStatuts as $s) {
                $leadsByStatut[$s] = $leadModel->findByStatut($s);
            }

            $pdo = \App\Core\Database::connection();
            $websiteId = (int) \App\Core\Config::get('website.id', 1);
            $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $scoreData = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {
            $statutCounts = [];
            $leadsByStatut = [];
            $scoreData = [];
            $totalLeads = 0;
        }

        View::renderAdmin('admin/pipeline', [
            'page_title' => 'Pipeline - Admin CRM',
            'admin_page' => 'pipeline',
            'breadcrumb' => 'Pipeline',
            'statutCounts' => $statutCounts,
            'leadsByStatut' => $leadsByStatut,
            'scoreData' => $scoreData,
            'totalLeads' => $totalLeads,
        ]);
    }

    private function assertLeadRequestAllowed(): void
    {
        $context = $_SESSION['lead_form_context'] ?? null;

        if (!is_array($context)) {
            throw new \RuntimeException('Session expirée. Merci de relancer une estimation avant de soumettre vos coordonnées.');
        }

        $ip = $this->getClientIp();
        $issuedAt = (int) ($context['issued_at'] ?? 0);
        $expiresAt = (int) ($context['expires_at'] ?? 0);

        if (($context['ip'] ?? '') !== $ip) {
            unset($_SESSION['lead_form_context']);
            throw new \RuntimeException('Vérification de sécurité invalide. Merci de refaire une estimation.');
        }

        $now = time();
        if ($issuedAt <= 0 || $now > $expiresAt) {
            unset($_SESSION['lead_form_context']);
            throw new \RuntimeException('Le formulaire a expiré. Merci de relancer une estimation.');
        }

        $lastSubmit = $_SESSION['lead_last_submit'] ?? null;
        if (is_array($lastSubmit) && ($lastSubmit['ip'] ?? '') === $ip) {
            $lastSubmittedAt = (int) ($lastSubmit['submitted_at'] ?? 0);
            $secondsSinceLastSubmit = $now - $lastSubmittedAt;

            if ($lastSubmittedAt > 0 && $secondsSinceLastSubmit < self::LEAD_SUBMIT_COOLDOWN_SECONDS) {
                throw new \RuntimeException('Merci de patienter une minute avant d\'envoyer une nouvelle demande.');
            }
        }
    }

    private function getClientIp(): string
    {
        $forwardedFor = trim((string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($forwardedFor !== '') {
            $forwardedIp = trim(explode(',', $forwardedFor)[0]);
            if ($forwardedIp !== '') {
                return $forwardedIp;
            }
        }

        $realIp = trim((string) ($_SERVER['HTTP_X_REAL_IP'] ?? ''));
        if ($realIp !== '') {
            return $realIp;
        }

        return trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

}
