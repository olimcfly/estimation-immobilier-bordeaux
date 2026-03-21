<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Partenaire;

final class AdminPartenaireController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $model = new Partenaire();
        $partenaires = $model->findAll();
        $stats = $model->getStats();

        View::renderAdmin('admin/partenaires', [
            'page_title' => 'Partenaires - Admin CRM',
            'admin_page' => 'partenaires',
            'breadcrumb' => 'Partenaires',
            'partenaires' => $partenaires,
            'stats' => $stats,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $partenaire = null;

        if ($id > 0) {
            $model = new Partenaire();
            $partenaire = $model->findById($id);
            if ($partenaire === null) {
                header('Location: /admin/partenaires');
                exit;
            }
        }

        View::renderAdmin('admin/partenaire-edit', [
            'page_title' => $partenaire ? 'Modifier Partenaire' : 'Nouveau Partenaire',
            'admin_page' => 'partenaires',
            'breadcrumb' => $partenaire ? 'Modifier Partenaire' : 'Nouveau Partenaire',
            'partenaire' => $partenaire,
            'errors' => [],
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        try {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $nom = Validator::string($_POST, 'nom', 2, 180);
            $email = Validator::email($_POST, 'email');
            $entreprise = trim((string) ($_POST['entreprise'] ?? ''));
            $telephone = trim((string) ($_POST['telephone'] ?? ''));
            $specialite = trim((string) ($_POST['specialite'] ?? ''));
            $zone = trim((string) ($_POST['zone_geographique'] ?? ''));
            $commission = isset($_POST['commission_defaut']) && $_POST['commission_defaut'] !== ''
                ? (float) $_POST['commission_defaut'] : 3.00;
            $statut = in_array($_POST['statut'] ?? '', ['actif', 'inactif', 'prospect'], true)
                ? $_POST['statut'] : 'actif';
            $notes = trim((string) ($_POST['notes'] ?? ''));

            $data = [
                'nom' => $nom,
                'email' => $email,
                'entreprise' => $entreprise ?: null,
                'telephone' => $telephone ?: null,
                'specialite' => $specialite ?: null,
                'zone_geographique' => $zone ?: null,
                'commission_defaut' => $commission,
                'statut' => $statut,
                'notes' => $notes ?: null,
            ];

            $model = new Partenaire();

            if ($id > 0) {
                $model->update($id, $data);
            } else {
                $id = $model->create($data);
            }

            header('Location: /admin/partenaires');
            exit;
        } catch (\Throwable $e) {
            View::renderAdmin('admin/partenaire-edit', [
                'page_title' => 'Partenaire',
                'admin_page' => 'partenaires',
                'breadcrumb' => 'Partenaire',
                'partenaire' => $_POST,
                'errors' => [$e->getMessage()],
            ]);
        }
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $model = new Partenaire();
            $model->delete($id);
        }

        header('Location: /admin/partenaires');
        exit;
    }
}
