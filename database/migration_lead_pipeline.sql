-- Migration: Lead Pipeline + Partenaires + Portfolio
-- Extends leads table with full pipeline statuses
-- Adds partenaires table for partner management
-- Adds commission tracking fields

-- 1. Extend leads.statut ENUM to full pipeline
ALTER TABLE leads
  MODIFY COLUMN statut ENUM(
    'nouveau',
    'contacte',
    'rdv_pris',
    'visite_realisee',
    'mandat_simple',
    'mandat_exclusif',
    'compromis_vente',
    'signe',
    'co_signature_partenaire',
    'assigne_autre'
  ) NOT NULL DEFAULT 'nouveau';

-- 2. Add partner and commission fields to leads
ALTER TABLE leads
  ADD COLUMN partenaire_id INT UNSIGNED NULL AFTER notes,
  ADD COLUMN commission_taux DECIMAL(5,2) NULL DEFAULT NULL AFTER partenaire_id,
  ADD COLUMN commission_montant DECIMAL(12,2) NULL DEFAULT NULL AFTER commission_taux,
  ADD COLUMN assigne_a VARCHAR(180) NULL DEFAULT NULL AFTER commission_montant,
  ADD COLUMN date_mandat DATE NULL DEFAULT NULL AFTER assigne_a,
  ADD COLUMN date_compromis DATE NULL DEFAULT NULL AFTER date_mandat,
  ADD COLUMN date_signature DATE NULL DEFAULT NULL AFTER date_compromis,
  ADD COLUMN prix_vente DECIMAL(12,2) NULL DEFAULT NULL AFTER date_signature,
  ADD INDEX idx_partenaire_id (partenaire_id),
  ADD INDEX idx_date_signature (date_signature);

-- 3. Create partenaires table
CREATE TABLE IF NOT EXISTS partenaires (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    nom VARCHAR(180) NOT NULL,
    entreprise VARCHAR(255) NULL,
    email VARCHAR(180) NOT NULL,
    telephone VARCHAR(40) NULL,
    specialite VARCHAR(120) NULL,
    zone_geographique VARCHAR(255) NULL,
    commission_defaut DECIMAL(5,2) NULL DEFAULT 3.00,
    statut ENUM('actif', 'inactif', 'prospect') NOT NULL DEFAULT 'actif',
    notes TEXT NULL,
    nb_mandats INT UNSIGNED NOT NULL DEFAULT 0,
    ca_genere DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_website_id (website_id),
    INDEX idx_statut (statut),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Add foreign key for partenaire
ALTER TABLE leads
  ADD CONSTRAINT fk_leads_partenaire
    FOREIGN KEY (partenaire_id) REFERENCES partenaires(id)
    ON DELETE SET NULL;
