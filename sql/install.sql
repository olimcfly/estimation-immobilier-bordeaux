CREATE TABLE IF NOT EXISTS google_ads_drafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_type ENUM('hot','warm','cold') NOT NULL,
    titres JSON NOT NULL,
    descriptions JSON NOT NULL,
    final_url VARCHAR(500),
    path1 VARCHAR(15),
    path2 VARCHAR(15),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
