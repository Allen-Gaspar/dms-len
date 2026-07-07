-- FILESTAC DMS — Migration patches (run AFTER kiwi_dms.sql if upgrading an old DB)
USE kiwi_dms;

-- Safe column additions (MariaDB 10.4 — ignore errors if columns already exist)
ALTER TABLE users ADD COLUMN phone VARCHAR(30) DEFAULT NULL;
ALTER TABLE users ADD COLUMN theme VARCHAR(20) DEFAULT 'light';
ALTER TABLE users ADD COLUMN admin_id INT DEFAULT NULL;
ALTER TABLE users ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
ALTER TABLE folders ADD COLUMN is_private TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS org_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL UNIQUE,
    brand_name VARCHAR(100) DEFAULT 'FILESTAC DMS',
    logo_path VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    can_add TINYINT(1) DEFAULT 1,
    can_download TINYINT(1) DEFAULT 1,
    can_share TINYINT(1) DEFAULT 1,
    can_delete TINYINT(1) DEFAULT 0,
    can_edit TINYINT(1) DEFAULT 0,
    can_checkout TINYINT(1) DEFAULT 0,
    updated_by INT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_read (user_id, is_read)
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    INDEX idx_token (token)
);
