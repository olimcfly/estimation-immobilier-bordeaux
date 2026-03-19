<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Article;
use App\Services\AIService;

final class AdminBlogController
{
    public function index(): void
    {
        $articleModel = new Article();

        View::render('admin/blog/index', [
            'articles' => $articleModel->findAll(),
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    public function create(): void
    {
        View::render('admin/blog/form', [
            'article' => null,
            'errors' => [],
            'action' => '/admin/blog/store',
            'submitLabel' => 'Créer l\'article',
        ]);
    }

    public function store(): void
    {
        $articleModel = new Article();

        try {
            $articleModel->create($this->validatedPayload($_POST));
            $this->redirect('/admin/blog?message=' . urlencode('Article créé avec succès.'));
        } catch (\Throwable $throwable) {
            View::render('admin/blog/form', [
                'article' => $_POST,
                'errors' => [$throwable->getMessage()],
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        }
    }

    public function edit(string $id): void
    {
        $articleModel = new Article();
        $article = $articleModel->findById((int) $id);

        if ($article === null) {
            $this->redirect('/admin/blog?error=' . urlencode('Article introuvable.'));
        }

        View::render('admin/blog/form', [
            'article' => $article,
            'revisions' => $articleModel->findRevisionsByArticleId((int) $id),
            'errors' => [],
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'action' => '/admin/blog/update/' . (int) $id,
            'submitLabel' => 'Mettre à jour',
        ]);
    }

    public function update(string $id): void
    {
        $articleModel = new Article();

        try {
            $articleModel->update((int) $id, $this->validatedPayload($_POST));
            $this->redirect('/admin/blog?message=' . urlencode('Article mis à jour.'));
        } catch (\Throwable $throwable) {
            $article = $_POST;
            $article['id'] = (int) $id;

            View::render('admin/blog/form', [
                'article' => $article,
                'revisions' => $articleModel->findRevisionsByArticleId((int) $id),
                'errors' => [$throwable->getMessage()],
                'action' => '/admin/blog/update/' . (int) $id,
                'submitLabel' => 'Mettre à jour',
            ]);
        }
    }

    public function restoreRevision(string $id, string $revisionId): void
    {
        $articleModel = new Article();

        try {
            $articleModel->restoreRevision((int) $id, (int) $revisionId);
            $this->redirect('/admin/blog/edit/' . (int) $id . '?message=' . urlencode('Révision restaurée avec succès.'));
        } catch (\Throwable $throwable) {
            $this->redirect('/admin/blog/edit/' . (int) $id . '?error=' . urlencode($throwable->getMessage()));
        }
    }

    public function delete(string $id): void
    {
        $articleModel = new Article();
        $articleModel->delete((int) $id);
        $this->redirect('/admin/blog?message=' . urlencode('Article supprimé.'));
    }

    public function generate(): void
    {
        try {
            $persona = Validator::string($_POST, 'persona', 3, 100);
            $awarenessLevel = Validator::string($_POST, 'awareness_level', 3, 50);
            $topic = Validator::string($_POST, 'topic', 5, 180);

            $service = new AIService();
            $generated = $service->generateArticle($persona, $awarenessLevel, $topic);

            View::render('admin/blog/form', [
                'article' => [
                    'title' => $generated['title'],
                    'slug' => $this->slugify($generated['title']),
                    'content' => $generated['content'],
                    'meta_title' => $generated['meta_title'],
                    'meta_description' => $generated['meta_description'],
                    'persona' => $persona,
                    'awareness_level' => $awarenessLevel,
                    'status' => 'draft',
                ],
                'errors' => [],
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        } catch (\Throwable $throwable) {
            $this->redirect('/admin/blog?error=' . urlencode($throwable->getMessage()));
        }
    }

    private function validatedPayload(array $input): array
    {
        $title = Validator::string($input, 'title', 5, 255);
        $slug = Validator::string($input, 'slug', 5, 255);
        $content = trim((string) ($input['content'] ?? ''));

        if ($content === '') {
            throw new \InvalidArgumentException('Champ invalide: content');
        }

        $metaTitle = Validator::string($input, 'meta_title', 5, 255);
        $metaDescription = Validator::string($input, 'meta_description', 20, 320);
        $persona = Validator::string($input, 'persona', 3, 100);
        $awarenessLevel = Validator::string($input, 'awareness_level', 3, 50);
        $status = Validator::string($input, 'status', 5, 20);

        if (!in_array($status, ['draft', 'published'], true)) {
            throw new \InvalidArgumentException('Statut invalide');
        }

        return [
            'title' => $title,
            'slug' => $this->slugify($slug),
            'content' => $content,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'persona' => $persona,
            'awareness_level' => $awarenessLevel,
            'status' => $status,
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');

        return $text !== '' ? $text : 'article';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
