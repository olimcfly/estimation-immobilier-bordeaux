<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Actualite;
use App\Services\ActualiteService;

final class AdminActualiteController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();

        View::renderAdmin('admin/actualites/index', [
            'actualites' => $model->findAll(),
            'cronLogs' => $model->getCronLogs(10),
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'page_title' => 'Actualités - Admin',
            'admin_page_title' => 'Actualités',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Actualités',
        ]);
    }

    public function create(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/actualites/form', [
            'actualite' => null,
            'errors' => [],
            'action' => '/admin/actualites/store',
            'submitLabel' => 'Créer l\'actualité',
            'page_title' => 'Nouvelle actualité - Admin',
            'admin_page_title' => 'Nouvelle actualité',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Nouvelle actualité',
        ]);
    }

    public function store(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();

        try {
            $data = $this->validatedPayload($_POST);
            $model->create($data);
            $this->redirect('/admin/actualites?message=' . urlencode('Actualité créée avec succès.'));
        } catch (\Throwable $e) {
            View::renderAdmin('admin/actualites/form', [
                'actualite' => $_POST,
                'errors' => [$e->getMessage()],
                'action' => '/admin/actualites/store',
                'submitLabel' => 'Créer l\'actualité',
                'page_title' => 'Nouvelle actualité - Admin',
                'admin_page_title' => 'Nouvelle actualité',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Nouvelle actualité',
            ]);
        }
    }

    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $actualite = $model->findById((int) $id);

        if ($actualite === null) {
            $this->redirect('/admin/actualites?error=' . urlencode('Actualité introuvable.'));
            return;
        }

        View::renderAdmin('admin/actualites/form', [
            'actualite' => $actualite,
            'errors' => [],
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'action' => '/admin/actualites/update/' . (int) $id,
            'submitLabel' => 'Mettre à jour',
            'page_title' => 'Modifier actualité - Admin',
            'admin_page_title' => 'Modifier actualité',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Modifier actualité',
        ]);
    }

    public function update(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();

        try {
            $data = $this->validatedPayload($_POST);
            $model->update((int) $id, $data);
            $this->redirect('/admin/actualites?message=' . urlencode('Actualité mise à jour.'));
        } catch (\Throwable $e) {
            $actualite = $_POST;
            $actualite['id'] = (int) $id;

            View::renderAdmin('admin/actualites/form', [
                'actualite' => $actualite,
                'errors' => [$e->getMessage()],
                'action' => '/admin/actualites/update/' . (int) $id,
                'submitLabel' => 'Mettre à jour',
                'page_title' => 'Modifier actualité - Admin',
                'admin_page_title' => 'Modifier actualité',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Modifier actualité',
            ]);
        }
    }

    public function delete(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $model->delete((int) $id);
        $this->redirect('/admin/actualites?message=' . urlencode('Actualité supprimée.'));
    }

    /**
     * Search Perplexity for news ideas.
     */
    public function search(): void
    {
        AuthController::requireAuth();

        $query = trim((string) ($_POST['query'] ?? ''));
        $service = new ActualiteService();

        try {
            $results = $service->searchNews($query !== '' ? $query : null);

            View::renderAdmin('admin/actualites/search_results', [
                'query' => $results['query'],
                'results' => $results['results'],
                'source' => $results['source'],
                'page_title' => 'Résultats de recherche - Admin',
                'admin_page_title' => 'Recherche actualités',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Recherche actualités',
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/admin/actualites?error=' . urlencode('Erreur recherche: ' . $e->getMessage()));
        }
    }

    /**
     * Generate a full article from an idea (AI pipeline).
     */
    public function generate(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $service = new ActualiteService();

        try {
            $customQuery = trim((string) ($_POST['query'] ?? ''));
            $query = $customQuery !== '' ? $customQuery : null;
            $result = $service->runAutomatedPipeline($query);

            if (!($result['success'] ?? false)) {
                $model->logCron(
                    $result['query'] ?? ($query ?? 'auto'),
                    0,
                    null,
                    'error',
                    $result['error'] ?? 'Erreur inconnue'
                );
                $this->redirect('/admin/actualites?error=' . urlencode($result['error'] ?? 'Erreur génération.'));
                return;
            }

            $article = $result['article'];

            $model->logCron(
                $result['query'] ?? ($query ?? 'auto'),
                $result['ideas_count'] ?? 0,
                null,
                'success'
            );

            View::renderAdmin('admin/actualites/form', [
                'actualite' => [
                    'title' => $article['title'],
                    'slug' => $this->slugify($article['title']),
                    'content' => $article['content'],
                    'excerpt' => $article['excerpt'],
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'image_url' => $article['image_url'] ?? '',
                    'image_prompt' => $article['image_prompt'] ?? '',
                    'source_query' => $article['source_query'] ?? '',
                    'source_results' => $result['source_results'] ?? '',
                    'generated_by' => 'ai',
                    'status' => 'draft',
                ],
                'errors' => [],
                'action' => '/admin/actualites/store',
                'submitLabel' => 'Publier l\'actualité',
                'page_title' => 'Article généré - Admin',
                'admin_page_title' => 'Article généré par IA',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Article généré par IA',
            ]);
        } catch (\Throwable $e) {
            $model->logCron(
                $customQuery !== '' ? $customQuery : 'auto',
                0,
                null,
                'error',
                $e->getMessage()
            );
            $this->redirect('/admin/actualites?error=' . urlencode($e->getMessage()));
        }
    }

    private function validatedPayload(array $input): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        if (mb_strlen($title) < 5) {
            throw new \InvalidArgumentException('Le titre doit faire au moins 5 caractères.');
        }

        $slug = trim((string) ($input['slug'] ?? ''));
        if ($slug === '') {
            $slug = $title;
        }

        $content = trim((string) ($input['content'] ?? ''));
        if ($content === '') {
            throw new \InvalidArgumentException('Le contenu ne peut pas être vide.');
        }

        $status = trim((string) ($input['status'] ?? 'draft'));
        if (!in_array($status, ['draft', 'published'], true)) {
            throw new \InvalidArgumentException('Statut invalide.');
        }

        return [
            'title' => $title,
            'slug' => $this->slugify($slug),
            'content' => $content,
            'excerpt' => trim((string) ($input['excerpt'] ?? '')),
            'meta_title' => trim((string) ($input['meta_title'] ?? $title)),
            'meta_description' => trim((string) ($input['meta_description'] ?? '')),
            'image_url' => trim((string) ($input['image_url'] ?? '')) ?: null,
            'status' => $status,
            'generated_by' => trim((string) ($input['generated_by'] ?? 'manual')),
            'source_query' => trim((string) ($input['source_query'] ?? '')) ?: null,
            'source_results' => trim((string) ($input['source_results'] ?? '')) ?: null,
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');

        return $text !== '' ? $text : 'actualite';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
