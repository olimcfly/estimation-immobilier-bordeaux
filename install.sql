CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ads_checklist_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(500),
    template VARCHAR(100),
    status ENUM('sent','failed','bounced') DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient),
    INDEX idx_template (template)
);

CREATE TABLE IF NOT EXISTS google_ads_drafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_type VARCHAR(30) NOT NULL,
    titres JSON NOT NULL,
    descriptions JSON NOT NULL,
    final_url VARCHAR(255) NOT NULL,
    path1 VARCHAR(15) DEFAULT '',
    path2 VARCHAR(15) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
