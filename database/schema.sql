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
    nom VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    telephone VARCHAR(40) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    ville VARCHAR(120) NOT NULL,
    estimation DECIMAL(12,2) NOT NULL,
    urgence VARCHAR(40) NOT NULL,
    motivation VARCHAR(80) NOT NULL,
    notes TEXT NULL,
    score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
    statut ENUM('nouveau', 'contacté', 'signé') NOT NULL DEFAULT 'nouveau',
    created_at DATETIME NOT NULL,
    INDEX idx_website_id (website_id),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at)
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
