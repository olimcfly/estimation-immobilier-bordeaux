-- Migration: Create achats (purchases) table
-- Date: 2026-03-21

CREATE TABLE IF NOT EXISTS achats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_id INT UNSIGNED NULL,
    nom_acheteur VARCHAR(180) NOT NULL,
    email_acheteur VARCHAR(180) NULL,
    telephone_acheteur VARCHAR(40) NULL,
    adresse_bien VARCHAR(255) NULL,
    ville VARCHAR(120) NOT NULL DEFAULT 'Lannion',
    quartier VARCHAR(120) NULL,
    type_bien VARCHAR(80) NULL,
    surface_m2 DECIMAL(8,2) NULL,
    pieces INT UNSIGNED NULL,
    prix_achat DECIMAL(12,2) NULL,
    prix_estime DECIMAL(12,2) NULL,
    type_financement ENUM('comptant', 'credit', 'mixte') NOT NULL DEFAULT 'credit',
    montant_pret DECIMAL(12,2) NULL,
    apport_personnel DECIMAL(12,2) NULL,
    statut ENUM('prospect', 'recherche', 'visite', 'offre', 'negociation', 'compromis', 'financement', 'acte_signe', 'annule') NOT NULL DEFAULT 'prospect',
    score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
    partenaire_id INT UNSIGNED NULL,
    commission_taux DECIMAL(5,2) NULL DEFAULT NULL,
    commission_montant DECIMAL(12,2) NULL DEFAULT NULL,
    date_premiere_visite DATE NULL DEFAULT NULL,
    date_offre DATE NULL DEFAULT NULL,
    date_compromis DATE NULL DEFAULT NULL,
    date_acte DATE NULL DEFAULT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_website_id (website_id),
    INDEX idx_lead_id (lead_id),
    INDEX idx_statut (statut),
    INDEX idx_score (score),
    INDEX idx_ville (ville),
    INDEX idx_created_at (created_at),
    INDEX idx_partenaire_id (partenaire_id),
    CONSTRAINT fk_achats_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
