CREATE TABLE ads_checklist_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    step_key VARCHAR(100) NOT NULL UNIQUE,
    completed TINYINT(1) DEFAULT 0,
    completed_at DATETIME NULL,
    notes TEXT NULL,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id)
);
