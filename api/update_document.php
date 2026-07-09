<?php
/**
 * api/update_document.php — Update document metadata (tags, description, etc.)
 */
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_role('contributor', 'admin');
$db = get_db();

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid document ID.']);
    exit;
}

// Fetch document
$stmt = $db->prepare('SELECT * FROM documents WHERE id = ? AND is_deleted = 0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'Document not found.']);
    exit;
}

// Check edit permission
if (!AccessControl::canEditDocument($db, $user, $id)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this document.']);
    exit;
}

// Check if locked by someone else
if ((int)$doc['is_locked'] === 1 && (int)$doc['locked_by'] !== (int)$user['id']) {
    echo json_encode(['success' => false, 'message' => 'Document is locked by another user.']);
    exit;
}

switch ($action) {
    case 'update_description':
        $description = trim($_POST['description'] ?? '');
        
        $update = $db->prepare('UPDATE documents SET description = ? WHERE id = ?');
        $update->bind_param('si', $description, $id);
        
        if ($update->execute()) {
            audit_log($user['id'], 'UPDATE_DOCUMENT', "Updated description for document ID {$id}");
            echo json_encode(['success' => true, 'message' => 'Description updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update description.']);
        }
        break;

    case 'update_tags':
        $tags = trim($_POST['tags'] ?? '');
        
        $update = $db->prepare('UPDATE documents SET tags = ? WHERE id = ?');
        $update->bind_param('si', $tags, $id);
        
        if ($update->execute()) {
            audit_log($user['id'], 'UPDATE_DOCUMENT', "Updated tags for document ID {$id}");
            echo json_encode(['success' => true, 'message' => 'Tags updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update tags.']);
        }
        break;

    case 'lock':
        $lock_update = $db->prepare('UPDATE documents SET is_locked = 1, locked_by = ?, locked_at = NOW() WHERE id = ?');
        $lock_update->bind_param('ii', $user['id'], $id);
        
        if ($lock_update->execute()) {
            audit_log($user['id'], 'LOCK_DOCUMENT', "Locked document ID {$id}");
            echo json_encode(['success' => true, 'message' => 'Document locked.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to lock document.']);
        }
        break;

    case 'unlock':
        if ((int)$doc['locked_by'] !== (int)$user['id'] && $user['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Only the document locker or admin can unlock.']);
            exit;
        }

        $unlock_update = $db->prepare('UPDATE documents SET is_locked = 0, locked_by = NULL, locked_at = NULL WHERE id = ?');
        $unlock_update->bind_param('i', $id);
        
        if ($unlock_update->execute()) {
            audit_log($user['id'], 'UNLOCK_DOCUMENT', "Unlocked document ID {$id}");
            echo json_encode(['success' => true, 'message' => 'Document unlocked.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unlock document.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        exit;
}
