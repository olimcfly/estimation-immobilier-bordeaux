-- Migration: administration renforcée (utilisateurs, sessions, modules, sauvegardes)

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS last_page_visited VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS is_online TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS last_activity DATETIME DEFAULT NULL;

CREATE TABLE IF NOT EXISTS user_sessions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    page_visited VARCHAR(255) DEFAULT NULL,
    login_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    logout_at DATETIME DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_session_user (session_id, user_id),
    KEY idx_user_id (user_id),
    CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS modules (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_module_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO modules (name, description, is_active) VALUES
('Google Ads', 'Gestion des campagnes Google Ads', 1),
('Webhooks', 'Intégration des webhooks', 1),
('Leads', 'Gestion des leads et estimations', 1),
('Traffic', 'Analyse du trafic', 1),
('Users', 'Gestion des comptes administrateurs', 1);

-- Comptes par défaut (à adapter en production)
INSERT INTO users (nom, prenom, email, password, role, actif, created_at)
VALUES
('Super', 'User', 'superuser@estimation-immobilier-bordeaux.fr', '$2y$10$N6xSuZ/zNdaNLS2VJxjJ8.O0GMo.vDhQ6yg5fj2B9hGTepf0Q4Qbe', 'admin', 1, NOW()),
('Contact', 'Team', 'contact@estimation-immobilier-bordeaux.fr', '$2y$10$KfppJ4L8CV6LMhK2bGAUkOp1Vu6T4VY9fWf6n3xwVCxQo56E8SruW', 'agent', 1, NOW())
ON DUPLICATE KEY UPDATE
    nom = VALUES(nom),
    prenom = VALUES(prenom),
    role = VALUES(role),
    actif = VALUES(actif);

INSERT INTO admins (id, prenom, nom, email, created_at, last_login)
VALUES
(1, 'Super', 'User', 'superuser@estimation-immobilier-bordeaux.fr', NOW(), NULL),
(2, 'Contact', 'Team', 'contact@estimation-immobilier-bordeaux.fr', NOW(), NULL)
ON DUPLICATE KEY UPDATE
    prenom = VALUES(prenom),
    nom = VALUES(nom);
