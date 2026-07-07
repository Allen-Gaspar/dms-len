<?php
require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/core/DMS.php';

foreach (glob(APP_ROOT . '/classes/*.php') as $file) {
    require_once $file;
}

class Database {
    public function connect() {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }
}

function get_db() {
    static $conn = null;
    if ($conn === null) {
        $conn = (new Database())->connect();
    }
    return $conn;
}

function dms() {
    return new DMS();
}

function audit_log($user_id, $action_type, $description) {
    $db = get_db();
    if ($user_id === null) {
        $stmt = $db->prepare('INSERT INTO audit_logs (user_id, action_type, description) VALUES (NULL, ?, ?)');
        $stmt->bind_param('ss', $action_type, $description);
    } else {
        $stmt = $db->prepare('INSERT INTO audit_logs (user_id, action_type, description) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $user_id, $action_type, $description);
    }
    $stmt->execute();
}

function flash_redirect(string $url, string $type, string $message): void {
    header('Location: ' . $url . (strpos($url, '?') !== false ? '&' : '?') . $type . '=' . urlencode($message));
    exit;
}

function toast_from_get(): ?array {
    if (!empty($_GET['ok']))  return ['type' => 'success', 'msg' => urldecode($_GET['ok'])];
    if (!empty($_GET['err'])) return ['type' => 'error',   'msg' => urldecode($_GET['err'])];
    return null;
}

ensure_schema();
