<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class Actualite
{
    public function findPublished(int $limit = 20, int $offset = 0): array
    {
        $sql = 'SELECT id, title, slug, excerpt, content, meta_title, meta_description, image_url, status, generated_by, published_at, created_at
                FROM actualites
                WHERE website_id = :website_id
                  AND status = :status
                ORDER BY published_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':website_id', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':status', 'published', \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countPublished(): int
    {
        $sql = 'SELECT COUNT(*) FROM actualites WHERE website_id = :website_id AND status = :status';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId(), ':status' => 'published']);

        return (int) $stmt->fetchColumn();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT id, title, slug, excerpt, content, meta_title, meta_description, image_url, source_query, status, generated_by, published_at, created_at
                FROM actualites
                WHERE website_id = :website_id
                  AND slug = :slug
                  AND status = :status
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':slug' => $slug,
            ':status' => 'published',
        ]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, title, slug, status, generated_by, published_at, created_at, image_url
                FROM actualites
                WHERE website_id = :website_id
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT *
                FROM actualites
                WHERE id = :id
                  AND website_id = :website_id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO actualites (website_id, title, slug, content, excerpt, meta_title, meta_description, image_url, image_prompt, source_query, source_results, status, generated_by, published_at, created_at)
                VALUES (:website_id, :title, :slug, :content, :excerpt, :meta_title, :meta_description, :image_url, :image_prompt, :source_query, :source_results, :status, :generated_by, :published_at, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':meta_title' => $data['meta_title'] ?? $data['title'],
            ':meta_description' => $data['meta_description'] ?? '',
            ':image_url' => $data['image_url'] ?? null,
            ':image_prompt' => $data['image_prompt'] ?? null,
            ':source_query' => $data['source_query'] ?? null,
            ':source_results' => $data['source_results'] ?? null,
            ':status' => $data['status'] ?? 'draft',
            ':generated_by' => $data['generated_by'] ?? 'manual',
            ':published_at' => ($data['status'] ?? 'draft') === 'published' ? date('Y-m-d H:i:s') : null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $currentStatus = null;
        $check = Database::connection()->prepare('SELECT status FROM actualites WHERE id = :id LIMIT 1');
        $check->execute([':id' => $id]);
        $row = $check->fetch();
        $currentStatus = $row['status'] ?? 'draft';

        $publishedAt = null;
        if (($data['status'] ?? 'draft') === 'published' && $currentStatus !== 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $sql = 'UPDATE actualites
                SET title = :title,
                    slug = :slug,
                    content = :content,
                    excerpt = :excerpt,
                    meta_title = :meta_title,
                    meta_description = :meta_description,
                    image_url = :image_url,
                    status = :status'
                . ($publishedAt !== null ? ', published_at = :published_at' : '')
                . ' WHERE id = :id AND website_id = :website_id';

        $params = [
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':meta_title' => $data['meta_title'] ?? $data['title'],
            ':meta_description' => $data['meta_description'] ?? '',
            ':image_url' => $data['image_url'] ?? null,
            ':status' => $data['status'] ?? 'draft',
        ];

        if ($publishedAt !== null) {
            $params[':published_at'] = $publishedAt;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM actualites WHERE id = :id AND website_id = :website_id');
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
    }

    public function logCron(string $query, int $articlesFound, ?int $articlePublishedId, string $status, ?string $errorMessage = null): void
    {
        $sql = 'INSERT INTO actualites_cron_log (website_id, query_used, articles_found, article_published_id, status, error_message, created_at)
                VALUES (:website_id, :query, :found, :pub_id, :status, :error, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':query' => $query,
            ':found' => $articlesFound,
            ':pub_id' => $articlePublishedId,
            ':status' => $status,
            ':error' => $errorMessage,
        ]);
    }

    public function getCronLogs(int $limit = 20): array
    {
        $sql = 'SELECT * FROM actualites_cron_log
                WHERE website_id = :website_id
                ORDER BY created_at DESC
                LIMIT :limit';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':website_id', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
