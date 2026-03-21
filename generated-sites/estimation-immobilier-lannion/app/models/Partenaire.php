<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class Partenaire
{
    public function findAll(): array
    {
        $sql = 'SELECT * FROM partenaires WHERE website_id = :website_id ORDER BY nom ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM partenaires WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findActifs(): array
    {
        $sql = 'SELECT * FROM partenaires WHERE website_id = :website_id AND statut = :statut ORDER BY nom ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId(), ':statut' => 'actif']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO partenaires (website_id, nom, entreprise, email, telephone, specialite, zone_geographique, commission_defaut, statut, notes, created_at)
                VALUES (:website_id, :nom, :entreprise, :email, :telephone, :specialite, :zone_geographique, :commission_defaut, :statut, :notes, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':nom' => $data['nom'],
            ':entreprise' => $data['entreprise'] ?? null,
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? null,
            ':specialite' => $data['specialite'] ?? null,
            ':zone_geographique' => $data['zone_geographique'] ?? null,
            ':commission_defaut' => $data['commission_defaut'] ?? 3.00,
            ':statut' => $data['statut'] ?? 'actif',
            ':notes' => $data['notes'] ?? null,
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE partenaires SET
                  nom = :nom, entreprise = :entreprise, email = :email, telephone = :telephone,
                  specialite = :specialite, zone_geographique = :zone_geographique,
                  commission_defaut = :commission_defaut, statut = :statut, notes = :notes
                WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':nom' => $data['nom'],
            ':entreprise' => $data['entreprise'] ?? null,
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? null,
            ':specialite' => $data['specialite'] ?? null,
            ':zone_geographique' => $data['zone_geographique'] ?? null,
            ':commission_defaut' => $data['commission_defaut'] ?? 3.00,
            ':statut' => $data['statut'] ?? 'actif',
            ':notes' => $data['notes'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM partenaires WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        return $stmt->rowCount() > 0;
    }

    public function getStats(): array
    {
        $sql = 'SELECT
                  COUNT(*) as total,
                  SUM(CASE WHEN statut = "actif" THEN 1 ELSE 0 END) as actifs,
                  SUM(CASE WHEN statut = "inactif" THEN 1 ELSE 0 END) as inactifs,
                  SUM(nb_mandats) as total_mandats,
                  SUM(ca_genere) as total_ca
                FROM partenaires WHERE website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'actifs' => 0, 'inactifs' => 0, 'total_mandats' => 0, 'total_ca' => 0,
        ];
    }

    public function updateStats(int $id): void
    {
        $sql = 'UPDATE partenaires SET
                  nb_mandats = (SELECT COUNT(*) FROM leads WHERE partenaire_id = :pid1),
                  ca_genere = COALESCE((SELECT SUM(commission_montant) FROM leads WHERE partenaire_id = :pid2 AND commission_montant IS NOT NULL), 0)
                WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':pid1' => $id, ':pid2' => $id, ':id' => $id]);
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
