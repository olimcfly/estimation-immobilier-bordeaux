<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Lead
{
    public function listByScore(?string $score = null): array
    {
        $allowedScores = ['chaud', 'tiede', 'froid'];
        $isFiltered = $score !== null && in_array($score, $allowedScores, true);

        if ($isFiltered) {
            $stmt = Database::connection()->prepare(
                'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
                 FROM leads
                 WHERE score = :score
                 ORDER BY created_at DESC'
            );
            $stmt->execute([':score' => $score]);

            return $stmt->fetchAll() ?: [];
        }

        $stmt = Database::connection()->query(
            'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
             FROM leads
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO leads (nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at)
                VALUES (:nom, :email, :telephone, :ville, :estimation, :urgence, :motivation, :score, :statut, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':nom', (string) $data['nom'], PDO::PARAM_STR);
        $stmt->bindValue(':email', (string) $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':telephone', (string) $data['telephone'], PDO::PARAM_STR);
        $stmt->bindValue(':ville', (string) $data['ville'], PDO::PARAM_STR);
        $stmt->bindValue(':estimation', (float) $data['estimation']);
        $stmt->bindValue(':urgence', (string) $data['urgence'], PDO::PARAM_STR);
        $stmt->bindValue(':motivation', (string) $data['motivation'], PDO::PARAM_STR);
        $stmt->bindValue(':score', (int) $data['score'], PDO::PARAM_INT);
        $stmt->bindValue(':statut', (string) ($data['statut'] ?? 'nouveau'), PDO::PARAM_STR);
        $stmt->execute();

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllLeads(): array
    {
        $sql = 'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateStatut(int $id, string $statut): bool
    {
        $sql = 'UPDATE leads SET statut = :statut WHERE id = :id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function create(array $data): int
    {
        return $this->insertLead($data);
    }
}
