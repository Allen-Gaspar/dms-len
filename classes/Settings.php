<?php
class Settings {
    private $db;

    public function __construct() {
        $this->db = get_db();
        $this->ensureTables();
    }

    private function ensureTables(): void {
        $this->db->query("CREATE TABLE IF NOT EXISTS org_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL UNIQUE,
            brand_name VARCHAR(100) DEFAULT 'FILESTAC DMS',
            logo_path VARCHAR(255) DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
    }

    /** Get org admin id for current user (admin themselves or their admin_id) */
    public static function getOrgAdminId(array $user): int {
        if ($user['role'] === 'admin') {
            return (int)$user['id'];
        }
        $db = get_db();
        $stmt = $db->prepare('SELECT admin_id FROM users WHERE id=?');
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['admin_id'] ?? $user['id']);
    }

    public function getBrand(int $adminId): array {
        $stmt = $this->db->prepare('SELECT brand_name, logo_path FROM org_settings WHERE admin_id=?');
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) {
            return ['brand_name' => DEFAULT_BRAND, 'logo_path' => null];
        }
        return $row;
    }

    public function getLogoUrl(int $adminId): string {
        $brand = $this->getBrand($adminId);
        if (!empty($brand['logo_path']) && file_exists(APP_ROOT . '/' . $brand['logo_path'])) {
            return app_url($brand['logo_path']) . '?v=' . filemtime(APP_ROOT . '/' . $brand['logo_path']);
        }
        if (file_exists(APP_ROOT . '/assets/logo/default.png')) {
            return asset_url('logo/default.png');
        }
        return asset_url('logo/default.png');
    }

    public function updateBrand(int $adminId, string $brandName, ?string $logoPath = null): bool {
        $existing = $this->getBrand($adminId);
        $logo = $logoPath ?? $existing['logo_path'];
        $stmt = $this->db->prepare(
            'INSERT INTO org_settings (admin_id, brand_name, logo_path) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE brand_name=VALUES(brand_name), logo_path=COALESCE(VALUES(logo_path), logo_path)'
        );
        $stmt->bind_param('iss', $adminId, $brandName, $logo);
        return $stmt->execute();
    }

    public function updateLogo(int $adminId, string $logoPath): bool {
        $brand = $this->getBrand($adminId);
        return $this->updateBrand($adminId, $brand['brand_name'], $logoPath);
    }

    public function removeLogo(int $adminId): bool {
        $brand = $this->getBrand($adminId);
        if ($brand['logo_path'] && file_exists(APP_ROOT . '/' . $brand['logo_path'])) {
            @unlink(APP_ROOT . '/' . $brand['logo_path']);
        }
        $stmt = $this->db->prepare('UPDATE org_settings SET logo_path=NULL WHERE admin_id=?');
        $stmt->bind_param('i', $adminId);
        return $stmt->execute();
    }
}
