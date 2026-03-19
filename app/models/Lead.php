<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Lead
{
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
}
