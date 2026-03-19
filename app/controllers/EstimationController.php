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
    public function leads(): void
    {
        $leadModel = new Lead();

        View::render('admin/leads', [
            'leads' => $leadModel->all(),
        ]);
    }

    public function index(): void
    {
        View::render('estimation/index');
    }

    public function estimate(): void
    {
        try {
            $city = Validator::string($_POST, 'ville', 2, 120);
            $typeKey = array_key_exists('type', $_POST) ? 'type' : 'type_bien';
            $propertyType = Validator::string($_POST, $typeKey, 2, 80);
            $surface = Validator::float($_POST, 'surface', 5, 10000);
            $rooms = Validator::int($_POST, 'pieces', 1, 50);

            $service = new EstimationService();
            $estimate = $service->estimate($city, $propertyType, $surface, $rooms);

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
            $ville = Validator::string($_POST, 'ville', 2, 120);
            $estimation = Validator::float($_POST, 'estimation', 10000, 100000000);
            $urgence = Validator::string($_POST, 'urgence', 3, 40);
            $motivation = Validator::string($_POST, 'motivation', 3, 80);

            $scoring = new LeadScoringService();
            $temperature = $scoring->score($estimation, $urgence, $motivation);

            $leadModel = new Lead();
            $leadId = $leadModel->create([
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'ville' => $ville,
                'estimation' => $estimation,
                'urgence' => $urgence,
                'motivation' => $motivation,
                'score' => $temperature,
                'statut' => 'nouveau',
            ]);

            View::render('estimation/lead_saved', [
                'leadId' => $leadId,
                'temperature' => $temperature,
            ]);
        } catch (\Throwable $throwable) {
            View::render('estimation/index', [
                'errors' => [$throwable->getMessage()],
            ]);
        }
    }
}
