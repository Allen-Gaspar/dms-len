<?php
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_role('contributor', 'admin');
$db = get_db();
$perms = (new User())->getPermissions((int)$user['id']);
if (empty($perms['can_edit'])) {
    echo json_encode(['success' => false, 'message' => 'You do not have edit access.']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$filename = basename(trim($_POST['filename'] ?? ''));

if ($id <= 0 || $filename === '') {
    echo json_encode(['success' => false, 'message' => 'No update made.']);
    exit;
}

$stmt = $db->prepare('SELECT id, filename, uploaded_by, is_private FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'File not found.']);
    exit;
}

if ((int)$doc['is_private'] === 1 && (int)$doc['uploaded_by'] !== (int)$user['id'] && $user['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

if ($filename === $doc['filename']) {
    echo json_encode(['success' => false, 'message' => 'No update made.']);
    exit;
}

$up = $db->prepare('UPDATE documents SET filename=? WHERE id=?');
$up->bind_param('si', $filename, $id);
if ($up->execute()) {
    audit_log($user['id'], 'RENAME_FILE', "Renamed '{$doc['filename']}' to '{$filename}'");
    echo json_encode(['success' => true, 'message' => 'File renamed.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Rename failed.']);
}
