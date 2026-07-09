<?php
/**
 * api/delete_document.php — Soft delete a document with full access control
 */
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_role('contributor', 'admin');
$db = get_db();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid document ID.']);
    exit;
}

// Fetch document
$stmt = $db->prepare(
    'SELECT id, filename, uploaded_by, folder_id, is_locked, locked_by 
     FROM documents WHERE id = ? AND is_deleted = 0 LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'Document not found.']);
    exit;
}

// Check if document is locked by someone else
if ((int)$doc['is_locked'] === 1 && (int)$doc['locked_by'] !== (int)$user['id']) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete a file locked by another user.']);
    exit;
}

// Check delete permission
if (!AccessControl::canDeleteDocument($db, $user, $id)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this document.']);
    exit;
}

// Soft delete
$update = $db->prepare('UPDATE documents SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ?');
$update->bind_param('ii', $user['id'], $id);

if ($update->execute()) {
    audit_log($user['id'], 'DELETE_DOCUMENT', "Deleted document ID {$id}: '{$doc['filename']}'");
    echo json_encode(['success' => true, 'message' => 'Document deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete document.']);
}
