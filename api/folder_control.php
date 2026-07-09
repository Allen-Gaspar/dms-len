<?php
/**
 * folder_control.php — Asynchronous Endpoint for Folder Access Matrix
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$role = $user['role'];

$action = $_GET['action'] ?? '';

// Prevent casual users from modifying folder security permissions
if ($role === 'casual') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized operation permission layer.']);
    exit;
}

header('Content-Type: application/json');

function can_manage_folder(mysqli $db, array $user, int $folderId): bool {
    $stmt = $db->prepare('SELECT created_by FROM folders WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $folderId);
    $stmt->execute();
    $folder = $stmt->get_result()->fetch_assoc();
    return $folder && ((int)$folder['created_by'] === (int)$user['id'] || $user['role'] === 'admin');
}

function upsert_folder_share(mysqli $db, int $folderId, int $targetUserId, array $perms): bool {
    $stmt = $db->prepare(
        'INSERT INTO folder_shares (folder_id, shared_with_user_id, can_edit, can_add, can_delete, can_checkout, can_download, can_share)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            can_edit = VALUES(can_edit),
            can_add = VALUES(can_add),
            can_delete = VALUES(can_delete),
            can_checkout = VALUES(can_checkout),
            can_download = VALUES(can_download),
            can_share = VALUES(can_share)'
    );
    $stmt->bind_param(
        'iiiiiiii',
        $folderId,
        $targetUserId,
        $perms['can_edit'],
        $perms['can_add'],
        $perms['can_delete'],
        $perms['can_checkout'],
        $perms['can_download'],
        $perms['can_share']
    );
    return $stmt->execute();
}

switch ($action) {
    case 'get_shares':
        $folder_id = (int)($_GET['folder_id'] ?? 0);
        
        if ($folder_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid folder ID.']);
            exit;
        }

        if (!AccessControl::canManageFolder($db, $user, $folder_id)) {
            echo json_encode(['success' => false, 'message' => 'You can only view shares for folders you own.']);
            exit;
        }
        
        $stmt = $db->prepare(
            'SELECT fs.*, u.username, u.role 
             FROM folder_shares fs
             INNER JOIN users u ON u.id = fs.shared_with_user_id
             WHERE fs.folder_id = ? 
             ORDER BY u.username ASC'
        );
        $stmt->bind_param('i', $folder_id);
        $stmt->execute();
        $shares = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $shares]);
        exit;

    case 'grant_access':
        // Read the inbound JSON stream payload
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid data payload.']);
            exit;
        }

        $folder_id           = (int)($input['folder_id'] ?? 0);
        $shared_with_user_id = (int)($input['shared_with_user_id'] ?? 0);
        $can_add             = (int)($input['can_add'] ?? 0);
        $can_edit            = (int)($input['can_edit'] ?? 0);
        $can_delete          = (int)($input['can_delete'] ?? 0);
        $can_checkout        = (int)($input['can_checkout'] ?? 0);
        $can_download        = (int)($input['can_download'] ?? 0);
        $can_share           = (int)($input['can_share'] ?? 0);

        if ($folder_id <= 0 || $shared_with_user_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Missing essential identification properties.']);
            exit;
        }
        if (!can_manage_folder($db, $user, $folder_id)) {
            echo json_encode(['success' => false, 'message' => 'You can only share folders you own.']);
            exit;
        }

        $perms = compact('can_add', 'can_edit', 'can_delete', 'can_checkout', 'can_download', 'can_share');
        if (upsert_folder_share($db, $folder_id, $shared_with_user_id, $perms)) {
            audit_log($user['id'], 'FOLDER_SHARE', "Granted folder access: folder_id={$folder_id}, user_id={$shared_with_user_id}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database write error.']);
        }
        exit;

    case 'grant_access_by_email':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid data payload.']);
            exit;
        }

        $folder_id = (int)($input['folder_id'] ?? 0);
        if ($folder_id <= 0 || !can_manage_folder($db, $user, $folder_id)) {
            echo json_encode(['success' => false, 'message' => 'You can only share folders you own.']);
            exit;
        }

        $perms = [
            'can_add' => (int)($input['can_add'] ?? 1),
            'can_edit' => (int)($input['can_edit'] ?? 1),
            'can_delete' => (int)($input['can_delete'] ?? 1),
            'can_checkout' => (int)($input['can_checkout'] ?? 1),
            'can_download' => (int)($input['can_download'] ?? 1),
            'can_share' => (int)($input['can_share'] ?? 1),
        ];

        $targets = [];
        if (!empty($input['all_users'])) {
            $stmt = $db->prepare("SELECT id FROM users WHERE id != ? AND status = 'active' AND is_deleted = 0");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $targets = array_map('intval', array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'id'));
        } else {
            $email = trim($input['email'] ?? '');
            if ($email === '') {
                echo json_encode(['success' => false, 'message' => 'User email is required.']);
                exit;
            }
            $stmt = $db->prepare("SELECT id FROM users WHERE email=? AND status='active' AND is_deleted=0 LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $target = $stmt->get_result()->fetch_assoc();
            if (!$target) {
                echo json_encode(['success' => false, 'message' => 'No active user found with that email.']);
                exit;
            }
            $targets[] = (int)$target['id'];
        }

        $ok = true;
        $count = 0;
        foreach ($targets as $targetId) {
            if ($targetId !== (int)$user['id']) {
                if (upsert_folder_share($db, $folder_id, $targetId, $perms)) {
                    $count++;
                } else {
                    $ok = false;
                }
            }
        }
        if ($count > 0) {
            audit_log($user['id'], 'FOLDER_SHARE_BATCH', "Granted folder access to {$count} users: folder_id={$folder_id}");
        }
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Folder access updated.' : 'Some shares could not be saved.']);
        exit;

    case 'revoke_access':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $share_id = (int)($_GET['share_id'] ?? 0);
        
        if ($share_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid share ID.']);
            exit;
        }

        // Get share details before deletion for audit log
        $check = $db->prepare('SELECT folder_id, shared_with_user_id FROM folder_shares WHERE id = ?');
        $check->bind_param('i', $share_id);
        $check->execute();
        $share = $check->get_result()->fetch_assoc();

        if (!$share) {
            echo json_encode(['success' => false, 'message' => 'Share not found.']);
            exit;
        }

        // Verify user can revoke (must own the folder)
        if (!AccessControl::canManageFolder($db, $user, (int)$share['folder_id'])) {
            echo json_encode(['success' => false, 'message' => 'You can only revoke shares for folders you own.']);
            exit;
        }

        $stmt = $db->prepare('DELETE FROM folder_shares WHERE id = ?');
        $stmt->bind_param('i', $share_id);
        
        if ($stmt->execute()) {
            audit_log($user['id'], 'FOLDER_SHARE_REVOKE', "Revoked folder access: share_id={$share_id}, user_id={$share['shared_with_user_id']}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to revoke access.']);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Action path route unrecognized.']);
        exit;
}
