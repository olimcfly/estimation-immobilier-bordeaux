<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Actualite;

final class ActualiteController
{
    public function index(): void
    {
        try {
            $model = new Actualite();
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $perPage = 12;
            $offset = ($page - 1) * $perPage;

            $actualites = $model->findPublished($perPage, $offset);
            $total = $model->countPublished();
            $totalPages = (int) ceil($total / $perPage);
        } catch (\Throwable $e) {
            error_log('Actualites index error: ' . $e->getMessage());
            $actualites = [];
            $total = 0;
            $page = 1;
            $totalPages = 1;
        }

        View::render('actualites/index', [
            'actualites' => $actualites,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'page_title' => 'Actualités Immobilières Nantes',
            'meta_description' => 'Suivez l\'actualité du marché immobilier à Nantes et en Loire-Atlantique. Analyses, tendances des prix, projets urbains et conseils pour vendeurs.',
        ]);
    }

    public function show(string $slug): void
    {
        try {
            $model = new Actualite();
            $actualite = $model->findBySlug($slug);
        } catch (\Throwable $e) {
            error_log('Actualite show error: ' . $e->getMessage());
            $actualite = null;
        }

        if ($actualite === null) {
            http_response_code(404);
            echo 'Actualité introuvable';
            return;
        }

        View::render('actualites/show', [
            'actualite' => $actualite,
            'page_title' => $actualite['meta_title'] ?: $actualite['title'],
            'meta_description' => $actualite['meta_description'],
        ]);
    }
}
