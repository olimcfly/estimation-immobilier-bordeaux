<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

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
        $stmt->execute([
            ':nom' => $data['nom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':ville' => $data['ville'],
            ':estimation' => $data['estimation'],
            ':urgence' => $data['urgence'],
            ':motivation' => $data['motivation'],
            ':score' => $data['score'],
            ':statut' => $data['statut'] ?? 'nouveau',
        ]);

        return (int) Database::connection()->lastInsertId();
    }
}
