<?php
class Notification {
    private $db;

    public function __construct() {
        $this->db = get_db();
        $this->ensureTable();
    }

    private function ensureTable(): void {
        $this->db->query("CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(255) DEFAULT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_read (user_id, is_read)
        )");
    }

    public function add(int $userId, string $type, string $message, ?string $link = null): void {
        $stmt = $this->db->prepare('INSERT INTO notifications (user_id, type, message, link) VALUES (?,?,?,?)');
        $stmt->bind_param('isss', $userId, $type, $message, $link);
        $stmt->execute();
    }

    public function getUnread(int $userId, int $limit = 10): array {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT ?');
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function markRead(int $userId, int $id): void {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?');
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
    }

    public function getForUser(int $userId, int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE id=? AND user_id=? LIMIT 1');
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function markAllRead(int $userId): void {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }
}
