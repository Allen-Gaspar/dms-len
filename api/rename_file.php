<?php
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_role('contributor', 'admin');
$db = get_db();

$id = (int)($_POST['id'] ?? 0);
$filename = basename(trim($_POST['filename'] ?? ''));

if ($id <= 0 || $filename === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters.']);
    exit;
}

// Fetch document with all details
$stmt = $db->prepare(
    'SELECT id, filename, uploaded_by, is_private, folder_id, is_locked, locked_by 
     FROM documents WHERE id=? AND is_deleted=0 LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'File not found.']);
    exit;
}

// Check if document is locked by someone else
if ((int)$doc['is_locked'] === 1 && (int)$doc['locked_by'] !== (int)$user['id']) {
    echo json_encode(['success' => false, 'message' => 'File is locked by another user.']);
    exit;
}

// Use unified access control to check edit permission
if (!AccessControl::canEditDocument($db, $user, $id)) {
    echo json_encode(['success' => false, 'message' => 'You do not have edit permission for this file.']);
    exit;
}

// Check if filename is the same
if ($filename === $doc['filename']) {
    echo json_encode(['success' => false, 'message' => 'New filename must be different from current.']);
    exit;
}

// Check for duplicate filename in same folder
$check = $db->prepare(
    'SELECT id FROM documents 
     WHERE folder_id <=> ? AND filename = ? AND id != ? AND is_deleted = 0 LIMIT 1'
);
$check->bind_param('isi', $doc['folder_id'], $filename, $id);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'A file with this name already exists in the folder.']);
    exit;
}

// Update filename
$up = $db->prepare('UPDATE documents SET filename = ? WHERE id = ?');
$up->bind_param('si', $filename, $id);

if ($up->execute()) {
    audit_log($user['id'], 'RENAME_FILE', "Renamed document ID {$id} from '{$doc['filename']}' to '{$filename}'");
    echo json_encode(['success' => true, 'message' => 'File renamed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
}
