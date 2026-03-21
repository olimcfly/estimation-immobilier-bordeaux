CREATE TABLE IF NOT EXISTS articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255) NOT NULL,
    meta_description TEXT NOT NULL,
    persona VARCHAR(100) NOT NULL,
    awareness_level VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_articles_website_slug (website_id, slug),
    INDEX idx_website_id (website_id),
    INDEX idx_status_created_at (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_revisions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    revision_number INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255) NOT NULL,
    meta_description TEXT NOT NULL,
    persona VARCHAR(100) NOT NULL,
    awareness_level VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    UNIQUE KEY uniq_article_revision (article_id, revision_number),
    INDEX idx_article_created_at (article_id, created_at),
    CONSTRAINT fk_article_revisions_article
        FOREIGN KEY (article_id) REFERENCES articles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_type ENUM('tendance', 'qualifie') NOT NULL DEFAULT 'qualifie',
    nom VARCHAR(120) NULL DEFAULT NULL,
    email VARCHAR(180) NULL DEFAULT NULL,
    telephone VARCHAR(40) NULL DEFAULT NULL,
    adresse VARCHAR(255) NULL DEFAULT NULL,
    ville VARCHAR(120) NOT NULL,
    type_bien VARCHAR(80) NULL,
    surface_m2 DECIMAL(8,2) NULL,
    pieces INT UNSIGNED NULL,
    estimation DECIMAL(12,2) NOT NULL,
    urgence VARCHAR(40) NULL DEFAULT NULL,
    motivation VARCHAR(80) NULL DEFAULT NULL,
    notes TEXT NULL,
    partenaire_id INT UNSIGNED NULL,
    commission_taux DECIMAL(5,2) NULL DEFAULT NULL,
    commission_montant DECIMAL(12,2) NULL DEFAULT NULL,
    assigne_a VARCHAR(180) NULL DEFAULT NULL,
    date_mandat DATE NULL DEFAULT NULL,
    date_compromis DATE NULL DEFAULT NULL,
    date_signature DATE NULL DEFAULT NULL,
    prix_vente DECIMAL(12,2) NULL DEFAULT NULL,
    score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
    statut ENUM(
      'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
      'mandat_simple', 'mandat_exclusif', 'compromis_vente',
      'signe', 'co_signature_partenaire', 'assigne_autre'
    ) NOT NULL DEFAULT 'nouveau',
    created_at DATETIME NOT NULL,
    INDEX idx_website_id (website_id),
    INDEX idx_lead_type (lead_type),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at),
    INDEX idx_partenaire_id (partenaire_id),
    INDEX idx_date_signature (date_signature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL DEFAULT '',
    login_code VARCHAR(255) DEFAULT NULL,
    login_code_expires_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    confirmed_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_newsletter_confirmed_at (confirmed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS design_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body_html LONGTEXT NOT NULL,
    signature TEXT NULL,
    category ENUM('notification', 'client', 'sequence', 'marketing') NOT NULL DEFAULT 'notification',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(180) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body_html LONGTEXT NULL,
    status ENUM('sent', 'failed') NOT NULL DEFAULT 'sent',
    template_id INT UNSIGNED NULL,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_sequences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    persona VARCHAR(50) NULL,
    trigger_event VARCHAR(50) NOT NULL DEFAULT 'lead_created',
    status ENUM('draft', 'active', 'paused') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_persona (persona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_sequence_steps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sequence_id INT UNSIGNED NOT NULL,
    step_order INT UNSIGNED NOT NULL DEFAULT 1,
    delay_days INT UNSIGNED NOT NULL DEFAULT 0,
    subject VARCHAR(255) NOT NULL,
    body_html LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sequence_order (sequence_id, step_order),
    CONSTRAINT fk_seq_steps_sequence
        FOREIGN KEY (sequence_id) REFERENCES email_sequences(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(120) NOT NULL DEFAULT 'Admin',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lead_id (lead_id),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_lead_notes_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_lead_id (lead_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_lead_activities_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS achats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_id INT UNSIGNED NULL,
    nom_acheteur VARCHAR(180) NOT NULL,
    email_acheteur VARCHAR(180) NULL,
    telephone_acheteur VARCHAR(40) NULL,
    adresse_bien VARCHAR(255) NULL,
    ville VARCHAR(120) NOT NULL DEFAULT 'Bordeaux',
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

CREATE TABLE IF NOT EXISTS lead_personas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL UNIQUE,
    neuropersona VARCHAR(50) NULL,
    bant_budget TEXT NULL,
    bant_authority TEXT NULL,
    bant_need TEXT NULL,
    bant_timeline TEXT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_neuropersona (neuropersona),
    CONSTRAINT fk_persona_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
