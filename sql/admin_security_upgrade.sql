SET NAMES utf8mb4;

-- ================================================
-- 1) STRUCTURE - MODULES DYNAMIQUES
-- ================================================
CREATE TABLE IF NOT EXISTS `modules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `icon` VARCHAR(30) DEFAULT 'fas fa-cog',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 2) STRUCTURE - SESSIONS UTILISATEURS ADMIN
-- ================================================
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `session_id` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `is_online` BOOLEAN DEFAULT TRUE,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_sessions_user` (`user_id`),
  INDEX `idx_user_sessions_session` (`session_id`),
  INDEX `idx_user_sessions_online` (`is_online`),
  CONSTRAINT `fk_user_sessions_admin` FOREIGN KEY (`user_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- 3) ÉVOLUTION TABLE ADMINS (RÉTRO-COMPAT)
-- ================================================
ALTER TABLE `admins`
  ADD COLUMN IF NOT EXISTS `password` VARCHAR(255) NULL AFTER `email`,
  ADD COLUMN IF NOT EXISTS `role` ENUM('superadmin','admin') DEFAULT 'admin' AFTER `password`,
  ADD COLUMN IF NOT EXISTS `is_active` BOOLEAN DEFAULT TRUE AFTER `role`,
  ADD COLUMN IF NOT EXISTS `is_online` BOOLEAN DEFAULT FALSE AFTER `is_active`;

CREATE INDEX IF NOT EXISTS `idx_admins_role` ON `admins` (`role`);
CREATE INDEX IF NOT EXISTS `idx_admins_online` ON `admins` (`is_online`);

-- ================================================
-- 4) SEED DES MODULES PAR DÉFAUT
-- ================================================
INSERT INTO `modules` (`name`, `slug`, `icon`) VALUES
('Dashboard', 'dashboard', 'fas fa-home'),
('Leads', 'leads', 'fas fa-users'),
('Google Ads', 'google-ads', 'fas fa-ad'),
('Utilisateurs', 'users', 'fas fa-user-shield'),
('Paramètres', 'settings', 'fas fa-cog')
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `icon` = VALUES(`icon`);

-- ================================================
-- 5) SEED COMPTES ADMIN (SI INEXISTANTS)
-- Mot de passe temporaire recommandé: ChangeMeNow!2026
-- ================================================
INSERT INTO `admins` (`prenom`, `nom`, `email`, `password`, `role`, `is_active`, `is_online`)
VALUES
('Super', 'User', 'superuser@skyline.com', '$2y$10$yo3h2M2QWQGvZ5aB3/B5quM0VJfWQ5e6ywA3LQn9J8T5Rgo7fOFfW', 'superadmin', TRUE, FALSE),
('Contact', 'Skyline', 'contact@skyline.com', '$2y$10$yo3h2M2QWQGvZ5aB3/B5quM0VJfWQ5e6ywA3LQn9J8T5Rgo7fOFfW', 'admin', TRUE, FALSE)
ON DUPLICATE KEY UPDATE
  `password` = VALUES(`password`),
  `role` = VALUES(`role`),
  `is_active` = VALUES(`is_active`),
  `is_online` = VALUES(`is_online`);
