CREATE TABLE IF NOT EXISTS articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255) NOT NULL,
    meta_description TEXT NOT NULL,
    persona VARCHAR(100) NOT NULL,
    awareness_level VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    INDEX idx_status_created_at (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
