<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Lead;
use App\Services\EstimationService;
use App\Services\LeadScoringService;

final class EstimationController
{
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

    public function estimate(): void
    {
        try {
            $city = Validator::string($_POST, 'ville', 2, 120);
            $typeKey = array_key_exists('type', $_POST) ? 'type' : 'type_bien';
            $propertyType = Validator::string($_POST, $typeKey, 2, 80);
            $surface = Validator::float($_POST, 'surface', 5, 10000);
            $rooms = Validator::int($_POST, 'pieces', 1, 50);

            $estimate = $this->estimationService->estimate($city, $propertyType, $surface, $rooms);

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

    public function storeLead(): void
    {
        try {
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
            $notes = $notesRaw;
            if ($contactPrefere !== '') {
                $notes = $notes !== '' ? "Contact préféré: {$contactPrefere}\n{$notes}" : "Contact préféré: {$contactPrefere}";
            }
            if (mb_strlen($notes) > 1500) {
                throw new \InvalidArgumentException('Les notes ne doivent pas dépasser 1500 caractères.');
            }

            $scoring = new LeadScoringService();
            $temperature = $scoring->score($estimation, $urgence, $motivation);

            $leadModel = new Lead();
            $leadId = $leadModel->create([
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
}
