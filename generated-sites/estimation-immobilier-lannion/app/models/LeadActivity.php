<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class LeadActivity
{
    public function findByLeadId(int $leadId, int $limit = 50): array
    {
        $sql = 'SELECT * FROM lead_activities WHERE lead_id = :lead_id ORDER BY created_at DESC LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':lead_id' => $leadId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function log(int $leadId, string $type, string $description): int
    {
        $sql = 'INSERT INTO lead_activities (lead_id, activity_type, description, created_at) VALUES (:lead_id, :type, :description, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':lead_id' => $leadId,
            ':type' => $type,
            ':description' => $description,
        ]);
        return (int) Database::connection()->lastInsertId();
    }
}
