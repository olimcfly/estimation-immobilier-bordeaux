<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Article
{
    public function findPublished(): array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
                WHERE status = :status
                ORDER BY created_at DESC
                LIMIT 10 OFFSET 0';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':status' => 'published']);

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
                WHERE slug = :slug AND status = :status
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
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
                ORDER BY created_at DESC';

        return Database::connection()->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at
                FROM articles
                WHERE id = :id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO articles (title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at)
                VALUES (:title, :slug, :content, :meta_title, :meta_description, :persona, :awareness_level, :status, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
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
        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $this->createRevisionSnapshot($id, $connection);

            $sql = 'UPDATE articles
                    SET title = :title,
                        slug = :slug,
                        content = :content,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        persona = :persona,
                        awareness_level = :awareness_level,
                        status = :status
                    WHERE id = :id';

            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':content' => $data['content'],
                ':meta_title' => $data['meta_title'],
                ':meta_description' => $data['meta_description'],
                ':persona' => $data['persona'],
                ':awareness_level' => $data['awareness_level'],
                ':status' => $data['status'],
            ]);

            $connection->commit();
        } catch (\Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function findRevisionsByArticleId(int $articleId): array
    {
        $sql = 'SELECT id, revision_number, title, status, created_at
                FROM article_revisions
                WHERE article_id = :article_id
                ORDER BY revision_number DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':article_id' => $articleId]);

        return $stmt->fetchAll();
    }

    public function restoreRevision(int $articleId, int $revisionId): void
    {
        $connection = Database::connection();
        $connection->beginTransaction();

        try {
            $this->createRevisionSnapshot($articleId, $connection);

            $revisionSql = 'SELECT title, slug, content, meta_title, meta_description, persona, awareness_level, status
                            FROM article_revisions
                            WHERE id = :id AND article_id = :article_id
                            LIMIT 1';

            $revisionStmt = $connection->prepare($revisionSql);
            $revisionStmt->execute([
                ':id' => $revisionId,
                ':article_id' => $articleId,
            ]);

            $revision = $revisionStmt->fetch();
            if (!is_array($revision)) {
                throw new \InvalidArgumentException('Révision introuvable.');
            }

            $sql = 'UPDATE articles
                    SET title = :title,
                        slug = :slug,
                        content = :content,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        persona = :persona,
                        awareness_level = :awareness_level,
                        status = :status
                    WHERE id = :id';

            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':id' => $articleId,
                ':title' => $revision['title'],
                ':slug' => $revision['slug'],
                ':content' => $revision['content'],
                ':meta_title' => $revision['meta_title'],
                ':meta_description' => $revision['meta_description'],
                ':persona' => $revision['persona'],
                ':awareness_level' => $revision['awareness_level'],
                ':status' => $revision['status'],
            ]);

            $connection->commit();
        } catch (\Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->execute([':id' => $id]);
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
