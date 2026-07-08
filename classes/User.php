<?php
class User {
    private $db;

    public function __construct() {
        $this->db = get_db();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username=? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function usernameExists(string $username, int $excludeId = 0): bool {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username=? AND id!=? LIMIT 1');
        $stmt->bind_param('si', $username, $excludeId);
        $stmt->execute();
        return (bool)$stmt->get_result()->fetch_assoc();
    }

    public function getFileCount(int $userId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM documents WHERE uploaded_by=? AND is_deleted=0');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function getPermissions(int $userId): array {
        $defaults = ['can_add' => 1, 'can_download' => 1, 'can_share' => 1, 'can_delete' => 1, 'can_edit' => 1, 'can_checkout' => 1];
        $stmt = $this->db->prepare('SELECT * FROM user_permissions WHERE user_id=?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: $defaults;
    }

    public function setPermissions(int $userId, array $perms, int $updatedBy): void {
        $this->db->query("CREATE TABLE IF NOT EXISTS user_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            can_add TINYINT(1) DEFAULT 1, can_download TINYINT(1) DEFAULT 1,
            can_share TINYINT(1) DEFAULT 1, can_delete TINYINT(1) DEFAULT 0,
            can_edit TINYINT(1) DEFAULT 0, can_checkout TINYINT(1) DEFAULT 0,
            updated_by INT DEFAULT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $stmt = $this->db->prepare(
            'INSERT INTO user_permissions (user_id,can_add,can_download,can_share,can_delete,can_edit,can_checkout,updated_by)
             VALUES (?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE can_add=VALUES(can_add),can_download=VALUES(can_download),
             can_share=VALUES(can_share),can_delete=VALUES(can_delete),can_edit=VALUES(can_edit),
             can_checkout=VALUES(can_checkout),updated_by=VALUES(updated_by)'
        );
        $ca = (int)($perms['can_add'] ?? 1);
        $cd = (int)($perms['can_download'] ?? 1);
        $cs = (int)($perms['can_share'] ?? 1);
        $cx = (int)($perms['can_delete'] ?? 0);
        $ce = (int)($perms['can_edit'] ?? 0);
        $cc = (int)($perms['can_checkout'] ?? 0);
        $stmt->bind_param('iiiiiiii', $userId, $ca, $cd, $cs, $cx, $ce, $cc, $updatedBy);
        $stmt->execute();
    }

    public function softDelete(int $userId): void {
        $stmt = $this->db->prepare('UPDATE users SET is_deleted=1, status="frozen" WHERE id=?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }

    public function listPaginated(int $offset, int $limit, bool $includeDeleted = false): array {
        $where = $includeDeleted ? '' : 'WHERE is_deleted=0';
        $stmt = $this->db->prepare("SELECT * FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(bool $includeDeleted = false): int {
        $where = $includeDeleted ? '' : 'WHERE is_deleted=0';
        return (int)$this->db->query("SELECT COUNT(*) FROM users $where")->fetch_row()[0];
    }
}
