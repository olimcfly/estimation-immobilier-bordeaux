<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class Article
{
    public function findPublished(): array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
                WHERE website_id = :website_id
                  AND status = :status
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':status' => 'published',
        ]);

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
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
        $sql = 'SELECT id, title, slug, persona, awareness_level, status, created_at
                FROM articles
                WHERE website_id = :website_id
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
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
        $sql = 'INSERT INTO articles (website_id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at)
                VALUES (:website_id, :title, :slug, :content, :meta_title, :meta_description, :persona, :awareness_level, :status, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':meta_title' => $data['meta_title'],
            ':meta_description' => $data['meta_description'],
            ':persona' => $data['persona'],
            ':awareness_level' => $data['awareness_level'],
            ':status' => $data['status'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE articles
                SET title = :title,
                    slug = :slug,
                    content = :content,
                    meta_title = :meta_title,
                    meta_description = :meta_description,
                    persona = :persona,
                    awareness_level = :awareness_level,
                    status = :status
                WHERE id = :id
                  AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':meta_title' => $data['meta_title'],
            ':meta_description' => $data['meta_description'],
            ':persona' => $data['persona'],
            ':awareness_level' => $data['awareness_level'],
            ':status' => $data['status'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM articles WHERE id = :id AND website_id = :website_id');
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }

    private function createRevisionSnapshot(int $articleId, \PDO $connection): void
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status
                FROM articles
                WHERE id = :id
                LIMIT 1';
        $articleStmt = $connection->prepare($sql);
        $articleStmt->execute([':id' => $articleId]);
        $article = $articleStmt->fetch();

        if (!is_array($article)) {
            throw new \InvalidArgumentException('Article introuvable.');
        }

        $revisionSql = 'SELECT COALESCE(MAX(revision_number), 0) + 1
                        FROM article_revisions
                        WHERE article_id = :article_id';
        $revisionStmt = $connection->prepare($revisionSql);
        $revisionStmt->execute([':article_id' => $articleId]);
        $nextRevisionNumber = (int) $revisionStmt->fetchColumn();

        $insertSql = 'INSERT INTO article_revisions (
                            article_id,
                            revision_number,
                            title,
                            slug,
                            content,
                            meta_title,
                            meta_description,
                            persona,
                            awareness_level,
                            status,
                            created_at
                      ) VALUES (
                            :article_id,
                            :revision_number,
                            :title,
                            :slug,
                            :content,
                            :meta_title,
                            :meta_description,
                            :persona,
                            :awareness_level,
                            :status,
                            NOW()
                      )';

        $insertStmt = $connection->prepare($insertSql);
        $insertStmt->execute([
            ':article_id' => $articleId,
            ':revision_number' => $nextRevisionNumber,
            ':title' => $article['title'],
            ':slug' => $article['slug'],
            ':content' => $article['content'],
            ':meta_title' => $article['meta_title'],
            ':meta_description' => $article['meta_description'],
            ':persona' => $article['persona'],
            ':awareness_level' => $article['awareness_level'],
            ':status' => $article['status'],
        ]);
    }
}
