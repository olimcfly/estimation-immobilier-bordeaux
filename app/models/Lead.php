<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
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
                 WHERE website_id = :website_id
                   AND score = :score
                 ORDER BY created_at DESC'
            );
            $stmt->execute([
                ':website_id' => $this->websiteId(),
                ':score' => $score,
            ]);

            return $stmt->fetchAll() ?: [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
             FROM leads
             WHERE website_id = :website_id
             ORDER BY created_at DESC'
        );
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO leads (website_id, lead_type, nom, email, telephone, adresse, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, notes, score, statut, created_at)
                VALUES (:website_id, :lead_type, :nom, :email, :telephone, :adresse, :ville, :type_bien, :surface_m2, :pieces, :estimation, :urgence, :motivation, :notes, :score, :statut, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':lead_type' => $data['lead_type'] ?? 'qualifie',
            ':nom' => $data['nom'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telephone' => $data['telephone'] ?? null,
            ':adresse' => $data['adresse'] ?? null,
            ':ville' => $data['ville'],
            ':type_bien' => $data['type_bien'] ?? null,
            ':surface_m2' => $data['surface_m2'] ?? null,
            ':pieces' => $data['pieces'] ?? null,
            ':estimation' => $data['estimation'],
            ':urgence' => $data['urgence'] ?? null,
            ':motivation' => $data['motivation'] ?? null,
            ':notes' => !empty($data['notes']) ? $data['notes'] : null,
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
        $sql = 'SELECT id, lead_type, nom, email, telephone, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                WHERE website_id = :website_id
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateStatut(int $id, string $statut): bool
    {
        $sql = 'UPDATE leads
                SET statut = :statut
                WHERE id = :id
                  AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':website_id', $this->websiteId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
