-- Logs d'envoi d'emails transactionnels
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(500),
    template VARCHAR(100),
    status ENUM('sent','failed','bounced') DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient),
    INDEX idx_template (template)
);

-- Colonnes de suivi des relances et désinscription
ALTER TABLE estimations
    ADD COLUMN email_relance_j3 TINYINT(1) DEFAULT 0,
    ADD COLUMN email_relance_j7 TINYINT(1) DEFAULT 0,
    ADD COLUMN email_relance_j14 TINYINT(1) DEFAULT 0,
    ADD COLUMN unsubscribed TINYINT(1) DEFAULT 0;
