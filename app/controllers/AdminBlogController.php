<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Validator;
use App\Core\View;
use App\Models\Article;
use App\Services\AIService;

final class AdminBlogController
{
    public function index(): void
    {
        AuthController::requireAuth();

        try {
            $this->ensureArticlesSchema();

            $articleModel = new Article();
            $articles = $articleModel->findAll();
        } catch (\Throwable $e) {
            error_log('[blog] index error: ' . $e->getMessage());
            View::renderAdmin('admin/blog/index', [
                'page_title' => 'Blog - Admin',
                'admin_page_title' => 'Blog / CMS',
                'admin_page' => 'blog',
                'articles' => [],
                'message' => '',
                'error' => 'Erreur de base de données : ' . $e->getMessage(),
            ]);
            return;
        }

        View::renderAdmin('admin/blog/index', [
            'page_title' => 'Blog - Admin',
            'admin_page_title' => 'Blog / CMS',
            'admin_page' => 'blog',
            'articles' => $articles,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    public function create(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/blog/form', [
            'admin_page_title' => 'Nouvel article',
            'admin_page' => 'blog',
            'article' => null,
            'errors' => [],
            'action' => '/admin/blog/store',
            'submitLabel' => 'Créer l\'article',
        ]);
    }

    public function store(): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();

        try {
            $articleModel->create($this->validatedPayload($_POST));
            $this->redirect('/admin/blog?message=' . urlencode('Article créé avec succès.'));
        } catch (\Throwable $throwable) {
            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Nouvel article',
                'admin_page' => 'blog',
                'article' => $_POST,
                'errors' => [$throwable->getMessage()],
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        }
    }

    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();
        $article = $articleModel->findById((int) $id);

        if ($article === null) {
            $this->redirect('/admin/blog?error=' . urlencode('Article introuvable.'));
        }

        View::renderAdmin('admin/blog/form', [
            'admin_page_title' => 'Modifier l\'article',
            'admin_page' => 'blog',
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
        AuthController::requireAuth();

        $articleModel = new Article();

        try {
            $articleModel->update((int) $id, $this->validatedPayload($_POST));
            $this->redirect('/admin/blog?message=' . urlencode('Article mis à jour.'));
        } catch (\Throwable $throwable) {
            $article = $_POST;
            $article['id'] = (int) $id;

            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Modifier l\'article',
                'admin_page' => 'blog',
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
        AuthController::requireAuth();

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
        AuthController::requireAuth();

        $articleModel = new Article();
        $articleModel->delete((int) $id);
        $this->redirect('/admin/blog?message=' . urlencode('Article supprimé.'));
    }

    public function generate(): void
    {
        AuthController::requireAuth();

        try {
            $persona = Validator::string($_POST, 'persona', 3, 100);
            $awarenessLevel = Validator::string($_POST, 'awareness_level', 3, 50);
            $topic = Validator::string($_POST, 'topic', 5, 180);

            $service = new AIService();
            $generated = $service->generateArticle($persona, $awarenessLevel, $topic);

            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Article généré par IA',
                'admin_page' => 'blog',
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

    /**
     * Ensure the articles and article_revisions tables have the expected schema.
     * Creates missing tables or adds missing columns to existing ones.
     */
    private function ensureArticlesSchema(): void
    {
        $pdo = Database::connection();

        if (!Database::tableExists('articles')) {
            $pdo->exec('CREATE TABLE articles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                meta_title VARCHAR(255) NOT NULL DEFAULT \'\',
                meta_description TEXT NOT NULL,
                persona VARCHAR(100) NOT NULL DEFAULT \'\',
                awareness_level VARCHAR(50) NOT NULL DEFAULT \'\',
                status ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_articles_website_slug (website_id, slug),
                INDEX idx_website_id (website_id),
                INDEX idx_status_created_at (status, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        } else {
            $this->addMissingColumns($pdo, 'articles', [
                'website_id' => 'INT UNSIGNED NOT NULL DEFAULT 1',
                'title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'slug' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'content' => 'LONGTEXT NOT NULL',
                'meta_title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'meta_description' => 'TEXT NOT NULL',
                'persona' => 'VARCHAR(100) NOT NULL DEFAULT \'\'',
                'awareness_level' => 'VARCHAR(50) NOT NULL DEFAULT \'\'',
                'status' => 'ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\'',
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ]);
        }

        if (!Database::tableExists('article_revisions')) {
            $pdo->exec('CREATE TABLE article_revisions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                article_id INT UNSIGNED NOT NULL,
                revision_number INT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                meta_title VARCHAR(255) NOT NULL DEFAULT \'\',
                meta_description TEXT NOT NULL,
                persona VARCHAR(100) NOT NULL DEFAULT \'\',
                awareness_level VARCHAR(50) NOT NULL DEFAULT \'\',
                status ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_article_revision (article_id, revision_number),
                INDEX idx_article_created_at (article_id, created_at),
                CONSTRAINT fk_article_revisions_article
                    FOREIGN KEY (article_id) REFERENCES articles(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        } else {
            $this->addMissingColumns($pdo, 'article_revisions', [
                'article_id' => 'INT UNSIGNED NOT NULL',
                'revision_number' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'slug' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'content' => 'LONGTEXT NOT NULL',
                'meta_title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'meta_description' => 'TEXT NOT NULL',
                'persona' => 'VARCHAR(100) NOT NULL DEFAULT \'\'',
                'awareness_level' => 'VARCHAR(50) NOT NULL DEFAULT \'\'',
                'status' => 'ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\'',
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ]);
        }
    }

    /**
     * Add missing columns to an existing table.
     *
     * @param array<string, string> $expectedColumns column_name => column_definition
     */
    private function addMissingColumns(\PDO $pdo, string $table, array $expectedColumns): void
    {
        $stmt = $pdo->query('SHOW COLUMNS FROM ' . $table);
        $existingColumns = array_column($stmt->fetchAll(), 'Field');

        foreach ($expectedColumns as $column => $definition) {
            if (!in_array($column, $existingColumns, true)) {
                $pdo->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
                error_log(sprintf('[blog] Added missing column %s.%s', $table, $column));
            }
        }
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
