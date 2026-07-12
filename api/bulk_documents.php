<?php
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_login();
$db = get_db();
$perms = (new User())->getPermissions((int)$user['id']);
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$action = $_GET['action'] ?? '';
$ids = array_values(array_unique(array_map('intval', $input['ids'] ?? [])));

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'No files selected.']);
    exit;
}

if ($action === 'delete') {
    if ($user['role'] !== 'admin' && empty($perms['can_delete'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have delete access.']);
        exit;
    }
    $deleted = 0;
    foreach ($ids as $id) {
        if (!AccessControl::canDeleteDocument($db, $user, $id) && $user['role'] !== 'admin') continue;
        $stmt = $db->prepare('UPDATE documents SET is_deleted = 1, deleted_at = NOW() WHERE id = ? AND is_deleted = 0');
        $stmt->bind_param('i', $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) $deleted++;
    }
    echo json_encode(['success' => true, 'message' => "Deleted {$deleted} file(s)."]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid bulk action.']);
