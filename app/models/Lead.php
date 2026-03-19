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
        $sql = 'INSERT INTO leads (nom, email, telephone, adresse, ville, estimation, urgence, motivation, notes, score, statut, created_at)
                VALUES (:nom, :email, :telephone, :adresse, :ville, :estimation, :urgence, :motivation, :notes, :score, :statut, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':adresse' => $data['adresse'],
            ':ville' => $data['ville'],
            ':estimation' => $data['estimation'],
            ':urgence' => $data['urgence'],
            ':motivation' => $data['motivation'],
            ':notes' => $data['notes'] !== '' ? $data['notes'] : null,
            ':score' => $data['score'],
            ':statut' => $data['statut'] ?? 'nouveau',
        ]);

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
