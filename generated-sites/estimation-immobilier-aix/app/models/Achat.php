<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class Achat
{
    public function findAll(): array
    {
        $sql = 'SELECT a.*, p.nom as partenaire_nom
                FROM achats a
                LEFT JOIN partenaires p ON a.partenaire_id = p.id
                WHERE a.website_id = :website_id
                ORDER BY a.created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findAllFiltered(?string $score = null, ?string $statut = null): array
    {
        $conditions = ['a.website_id = :website_id'];
        $params = [':website_id' => $this->websiteId()];

        if ($score !== null && in_array($score, ['chaud', 'tiede', 'froid'], true)) {
            $conditions[] = 'a.score = :score';
            $params[':score'] = $score;
        }
        if ($statut !== null && $statut !== '') {
            $conditions[] = 'a.statut = :statut';
            $params[':statut'] = $statut;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT a.*, p.nom as partenaire_nom
                FROM achats a
                LEFT JOIN partenaires p ON a.partenaire_id = p.id
                WHERE {$where}
                ORDER BY a.created_at DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT a.*, p.nom as partenaire_nom
                FROM achats a
                LEFT JOIN partenaires p ON a.partenaire_id = p.id
                WHERE a.id = :id AND a.website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO achats (website_id, lead_id, nom_acheteur, email_acheteur, telephone_acheteur,
                adresse_bien, ville, quartier, type_bien, surface_m2, pieces, prix_achat, prix_estime,
                type_financement, montant_pret, apport_personnel, statut, score, partenaire_id,
                commission_taux, commission_montant, date_premiere_visite, date_offre, date_compromis,
                date_acte, notes, created_at)
                VALUES (:website_id, :lead_id, :nom_acheteur, :email_acheteur, :telephone_acheteur,
                :adresse_bien, :ville, :quartier, :type_bien, :surface_m2, :pieces, :prix_achat, :prix_estime,
                :type_financement, :montant_pret, :apport_personnel, :statut, :score, :partenaire_id,
                :commission_taux, :commission_montant, :date_premiere_visite, :date_offre, :date_compromis,
                :date_acte, :notes, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':lead_id' => $data['lead_id'] ?: null,
            ':nom_acheteur' => $data['nom_acheteur'],
            ':email_acheteur' => $data['email_acheteur'] ?: null,
            ':telephone_acheteur' => $data['telephone_acheteur'] ?: null,
            ':adresse_bien' => $data['adresse_bien'] ?: null,
            ':ville' => $data['ville'] ?? 'Aix-en-Provence',
            ':quartier' => $data['quartier'] ?: null,
            ':type_bien' => $data['type_bien'] ?: null,
            ':surface_m2' => $data['surface_m2'] ?: null,
            ':pieces' => $data['pieces'] ?: null,
            ':prix_achat' => $data['prix_achat'] ?: null,
            ':prix_estime' => $data['prix_estime'] ?: null,
            ':type_financement' => $data['type_financement'] ?? 'credit',
            ':montant_pret' => $data['montant_pret'] ?: null,
            ':apport_personnel' => $data['apport_personnel'] ?: null,
            ':statut' => $data['statut'] ?? 'prospect',
            ':score' => $data['score'] ?? 'froid',
            ':partenaire_id' => $data['partenaire_id'] ?: null,
            ':commission_taux' => $data['commission_taux'] ?: null,
            ':commission_montant' => $data['commission_montant'] ?: null,
            ':date_premiere_visite' => $data['date_premiere_visite'] ?: null,
            ':date_offre' => $data['date_offre'] ?: null,
            ':date_compromis' => $data['date_compromis'] ?: null,
            ':date_acte' => $data['date_acte'] ?: null,
            ':notes' => $data['notes'] ?: null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE achats SET
                  lead_id = :lead_id, nom_acheteur = :nom_acheteur, email_acheteur = :email_acheteur,
                  telephone_acheteur = :telephone_acheteur, adresse_bien = :adresse_bien, ville = :ville,
                  quartier = :quartier, type_bien = :type_bien, surface_m2 = :surface_m2, pieces = :pieces,
                  prix_achat = :prix_achat, prix_estime = :prix_estime, type_financement = :type_financement,
                  montant_pret = :montant_pret, apport_personnel = :apport_personnel, statut = :statut,
                  score = :score, partenaire_id = :partenaire_id, commission_taux = :commission_taux,
                  commission_montant = :commission_montant, date_premiere_visite = :date_premiere_visite,
                  date_offre = :date_offre, date_compromis = :date_compromis, date_acte = :date_acte,
                  notes = :notes
                WHERE id = :id AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':lead_id' => $data['lead_id'] ?: null,
            ':nom_acheteur' => $data['nom_acheteur'],
            ':email_acheteur' => $data['email_acheteur'] ?: null,
            ':telephone_acheteur' => $data['telephone_acheteur'] ?: null,
            ':adresse_bien' => $data['adresse_bien'] ?: null,
            ':ville' => $data['ville'] ?? 'Aix-en-Provence',
            ':quartier' => $data['quartier'] ?: null,
            ':type_bien' => $data['type_bien'] ?: null,
            ':surface_m2' => $data['surface_m2'] ?: null,
            ':pieces' => $data['pieces'] ?: null,
            ':prix_achat' => $data['prix_achat'] ?: null,
            ':prix_estime' => $data['prix_estime'] ?: null,
            ':type_financement' => $data['type_financement'] ?? 'credit',
            ':montant_pret' => $data['montant_pret'] ?: null,
            ':apport_personnel' => $data['apport_personnel'] ?: null,
            ':statut' => $data['statut'] ?? 'prospect',
            ':score' => $data['score'] ?? 'froid',
            ':partenaire_id' => $data['partenaire_id'] ?: null,
            ':commission_taux' => $data['commission_taux'] ?: null,
            ':commission_montant' => $data['commission_montant'] ?: null,
            ':date_premiere_visite' => $data['date_premiere_visite'] ?: null,
            ':date_offre' => $data['date_offre'] ?: null,
            ':date_compromis' => $data['date_compromis'] ?: null,
            ':date_acte' => $data['date_acte'] ?: null,
            ':notes' => $data['notes'] ?: null,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM achats WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        return $stmt->rowCount() > 0;
    }

    public function getStats(): array
    {
        $sql = 'SELECT
                  COUNT(*) as total,
                  SUM(CASE WHEN score = "chaud" THEN 1 ELSE 0 END) as chauds,
                  SUM(CASE WHEN score = "tiede" THEN 1 ELSE 0 END) as tiedes,
                  SUM(CASE WHEN score = "froid" THEN 1 ELSE 0 END) as froids,
                  SUM(CASE WHEN statut = "acte_signe" THEN 1 ELSE 0 END) as signes,
                  COALESCE(SUM(CASE WHEN statut = "acte_signe" THEN prix_achat ELSE 0 END), 0) as volume_signe,
                  COALESCE(SUM(CASE WHEN statut = "acte_signe" THEN commission_montant ELSE 0 END), 0) as commission_gagnee,
                  COALESCE(SUM(prix_achat), 0) as volume_total
                FROM achats WHERE website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'chauds' => 0, 'tiedes' => 0, 'froids' => 0,
            'signes' => 0, 'volume_signe' => 0, 'commission_gagnee' => 0, 'volume_total' => 0,
        ];
    }

    public function countByStatut(): array
    {
        $sql = 'SELECT statut, COUNT(*) as cnt FROM achats WHERE website_id = :website_id GROUP BY statut';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
