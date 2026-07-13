<?php
/**
 * version_control.php — Checkout, Checkin, Version History Logging, Overwrites, and Rollback Control Engine.
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$perms = (new User())->getPermissions((int)$user['id']);

$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

// Dynamic redirection destination processor helper function
function context_redirect($message_type, $message_text) {
    $origin = $_GET['origin'] ?? '';
    $folder_id = (int)($_GET['folder_id'] ?? 0);
    
    if ($origin === 'folders') {
        header('Location: ' . page_url("folders.php?id={$folder_id}&{$message_type}=" . urlencode($message_text)));
    } elseif ($origin === 'private') {
        $target = $folder_id > 0 ? "private.php?folder_id={$folder_id}" : 'private.php';
        $join = strpos($target, '?') === false ? '?' : '&';
        header('Location: ' . page_url($target . $join . $message_type . '=' . urlencode($message_text)));
    } else {
        header('Location: ' . page_url("documents.php?{$message_type}=" . urlencode($message_text)));
    }
    exit;
}

function can_access_document(mysqli $db, array $user, array $doc): bool {
    return AccessControl::canViewDocument($db, $user, (int)$doc['id']);
}

function can_document_capability(mysqli $db, array $user, int $documentId, string $capability): bool {
    if ($capability === 'can_edit') {
        return AccessControl::canEditDocument($db, $user, $documentId);
    }
    if ($capability === 'can_delete') {
        return AccessControl::canDeleteDocument($db, $user, $documentId);
    }

    $stmt = $db->prepare('SELECT uploaded_by, is_private, folder_id FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
    $stmt->bind_param('i', $documentId);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    if (!$doc) return false;
    if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
    if ((int)$doc['is_private'] === 0 && $user['role'] === 'admin') return true;

    if (!empty($doc['folder_id'])) {
        $sql = "SELECT {$capability} AS allowed FROM folder_shares WHERE folder_id=? AND shared_with_user_id=? LIMIT 1";
        $fstmt = $db->prepare($sql);
        $fstmt->bind_param('ii', $doc['folder_id'], $user['id']);
        $fstmt->execute();
        $perm = $fstmt->get_result()->fetch_assoc();
        if ($perm && (int)$perm['allowed'] === 1) return true;
    }

    $sql = "SELECT {$capability} AS allowed FROM document_shares WHERE document_id=? AND shared_with_user_id=? LIMIT 1";
    $pstmt = $db->prepare($sql);
    $pstmt->bind_param('ii', $documentId, $user['id']);
    $pstmt->execute();
    $share = $pstmt->get_result()->fetch_assoc();
    return $share && (int)$share['allowed'] === 1;
}

// FIXED ACTIONS WHITELIST: Permitting all modal API fetch requests to pass safely
$allowed_actions = ['checkout', 'checkin', 'get_history', 'silent_lock', 'silent_unlock', 'commit_revision', 'commit_permissions_only', 'rollback'];

if (!in_array($action, $allowed_actions, true)) {
    context_redirect('err', 'Invalid request .');
}

if ($action === 'commit_permissions_only') {
    header('Content-Type: application/json');
    $doc_id = (int)($_POST['document_id'] ?? $_POST['doc_id'] ?? 0);
    if ($doc_id > 0) {
        $unlock = $db->prepare('UPDATE documents SET is_locked = 0, locked_by = NULL WHERE id = ? AND locked_by = ?');
        $unlock->bind_param('ii', $doc_id, $user['id']);
        $unlock->execute();
    }
    echo json_encode(['success' => true, 'message' => 'No file changes submitted.']);
    exit;
}

// ── 1. API FETCH: GET HISTORY LOGGER BACKLOG ───────────────────────────
if ($action === 'get_history') {
    header('Content-Type: application/json');
    if ($id <= 0) { echo json_encode([]); exit; }

    $doc_stmt = $db->prepare('SELECT id, uploaded_by, is_private FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
    $doc_stmt->bind_param('i', $id);
    $doc_stmt->execute();
    $history_doc = $doc_stmt->get_result()->fetch_assoc();
    if (!$history_doc || !can_access_document($db, $user, $history_doc)) { echo json_encode([]); exit; }
    
    $stmt = $db->prepare("SELECT id, version_number, storage_path, created_at FROM document_versions WHERE document_id = ? ORDER BY version_number DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

// ── 2. API FETCH: SILENT AUTOMATED LOCKING CONCURRENCY HOOK ────────────
if ($action === 'silent_lock') {
    header('Content-Type: application/json');
    if ($id > 0) {
        $doc_stmt = $db->prepare('SELECT id, uploaded_by, is_private FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
        $doc_stmt->bind_param('i', $id);
        $doc_stmt->execute();
        $lock_doc = $doc_stmt->get_result()->fetch_assoc();
        if (!$lock_doc || !can_document_capability($db, $user, $id, 'can_checkout')) { echo json_encode(['success' => false]); exit; }
        $stmt = $db->prepare("UPDATE documents SET is_locked = 1, locked_by = ? WHERE id = ? AND is_locked = 0");
        $stmt->bind_param('ii', $user['id'], $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    }
    exit;
}

// ── 3. API FETCH: SILENT UNLOCKING CONCURRENCY HOOK ──────────────────
if ($action === 'silent_unlock') {
    header('Content-Type: application/json');
    if ($id > 0) {
        $doc_stmt = $db->prepare('SELECT id, uploaded_by, is_private FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
        $doc_stmt->bind_param('i', $id);
        $doc_stmt->execute();
        $unlock_doc = $doc_stmt->get_result()->fetch_assoc();
        if (!$unlock_doc || !can_document_capability($db, $user, $id, 'can_checkout')) { echo json_encode(['success' => false]); exit; }
        $stmt = $db->prepare("UPDATE documents SET is_locked = 0, locked_by = NULL WHERE id = ? AND locked_by = ?");
        $stmt->bind_param('ii', $id, $user['id']);
        $stmt->execute();
        echo json_encode(['success' => true]);
    }
    exit;
}

// ── 4. POST ACTION: COMMIT NEW OVERWRITE VERSION FILE REVISION ──────────
if ($action === 'commit_revision' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Set header to JSON

    if (empty($perms['can_edit'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have edit access.']);
        exit;
    }
    
    $doc_id = (int)($_POST['document_id'] ?? 0);
    
    if ($doc_id <= 0 || !isset($_FILES['revised_document']) || $_FILES['revised_document']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No valid file received.']);
        exit;
    }

    $stmt = $db->prepare('SELECT * FROM documents WHERE id = ? AND is_deleted = 0 LIMIT 1');
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    $current_doc = $stmt->get_result()->fetch_assoc();

    if (!$current_doc || !can_document_capability($db, $user, $doc_id, 'can_edit')) {
        echo json_encode(['success' => false, 'message' => 'Access denied or document missing.']);
        exit;
    }

    $file = $_FILES['revised_document'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $requested_filename = basename(trim($_POST['revised_filename'] ?? $file['name']));
    if ($requested_filename === '') $requested_filename = basename($file['name']);
    $final_filename = unique_document_filename($db, $requested_filename, $current_doc['folder_id'] ?? null, $doc_id);
    
    $stored_name = uniqid('doc_rev_', true) . '.' . $ext;
    
if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $stored_name)) {
        // A. Log current version into your document_versions repository table history backlog
        $log_stmt = $db->prepare("INSERT INTO document_versions (document_id, version_number, storage_path, file_size, updated_by) VALUES (?, ?, ?, ?, ?)");
        $log_stmt->bind_param('iisii', $doc_id, $current_doc['version'], $current_doc['storage_path'], $current_doc['size'], $user['id']);
        $log_stmt->execute();

        // B. Apply increments, clear lock status trackers and update primary record data strings
        $next_version = (int)$current_doc['version'] + 1;
        $up_stmt = $db->prepare("UPDATE documents SET filename = ?, storage_path = ?, size = ?, version = ?, is_locked = 0, locked_by = NULL WHERE id = ?");
        $up_stmt->bind_param('ssiii', $final_filename, $stored_name, $file['size'], $next_version, $doc_id);
        $up_stmt->execute();

        audit_log($user['id'], 'VERSION_BUMP', "Committed file revision for '{$current_doc['filename']}' — now version v{$next_version}");
        
        // Updated message to reflect save and unlock status
        echo json_encode([
            'success' => true, 
            'message' => "Saved successfully! Version v{$next_version} is now unlocked."
        ]);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Failed to store update file. Check directory permissions.']);
    exit;
}

// ── 5. GET ACTION: OPERATE HISTORICAL CHECKPOINT REGRESSION ROLLBACK ────
function unique_document_filename(mysqli $db, string $filename, $folderId, int $excludeId): string {
    $filename = basename($filename);
    $pathInfo = pathinfo($filename);
    $base = trim($pathInfo['filename'] ?? 'file');
    $ext = isset($pathInfo['extension']) && $pathInfo['extension'] !== '' ? '.' . $pathInfo['extension'] : '';
    if ($base === '') $base = 'file';

    $candidate = $base . $ext;
    $counter = 1;
    while (document_filename_exists($db, $candidate, $folderId, $excludeId)) {
        $candidate = $base . ' (' . $counter . ')' . $ext;
        $counter++;
    }
    return $candidate;
}

function document_filename_exists(mysqli $db, string $filename, $folderId, int $excludeId): bool {
    $stmt = $db->prepare('SELECT id FROM documents WHERE folder_id <=> ? AND filename = ? AND id != ? AND is_deleted = 0 LIMIT 1');
    $stmt->bind_param('isi', $folderId, $filename, $excludeId);
    $stmt->execute();
    return (bool)$stmt->get_result()->fetch_assoc();
}

if ($action === 'rollback') {
    if (empty($perms['can_edit'])) {
        context_redirect('err', 'You do not have edit access.');
    }
    $doc_id     = (int)($_GET['doc_id'] ?? 0);
    $version_id = (int)($_GET['version_id'] ?? 0);
    
    $v_stmt = $db->prepare("SELECT * FROM document_versions WHERE id = ? AND document_id = ? LIMIT 1");
    $v_stmt->bind_param('ii', $version_id, $doc_id);
    $v_stmt->execute();
    $v_data = $v_stmt->get_result()->fetch_assoc();
    
    if ($v_data) {
        $doc_stmt = $db->prepare('SELECT id, uploaded_by, is_private FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
        $doc_stmt->bind_param('i', $doc_id);
        $doc_stmt->execute();
        $rollback_doc = $doc_stmt->get_result()->fetch_assoc();
        if (!$rollback_doc || !can_document_capability($db, $user, $doc_id, 'can_edit')) {
            context_redirect('err', 'You do not have edit access for this file.');
        }
        $u_stmt = $db->prepare("UPDATE documents SET storage_path = ?, size = ?, version = ?, is_locked = 0, locked_by = NULL WHERE id = ?");
        $u_stmt->bind_param('siii', $v_data['storage_path'], $v_data['file_size'], $v_data['version_number'], $doc_id);
        
        if ($u_stmt->execute()) {
            audit_log($user['id'], 'ROLLBACK', "Rolled back file ID {$doc_id} to historical Version v{$v_data['version_number']}");
            context_redirect('ok', "Document reverted back to historical version checkpoint v" . $v_data['version_number']);
        }
    }
    context_redirect('err', "Failed to process database version rollback rollback metrics.");
}

// ── CORE FALLBACK LOGIC FOR LEGACY ACTION OPERATIONS ───────────────────
if ($id <= 0) { context_redirect('err', 'Invalid document id request.'); }

$stmt = $db->prepare('SELECT * FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) { context_redirect('err', 'Document record missing.'); }
if (!can_access_document($db, $user, $doc)) { context_redirect('err', 'Access denied.'); }

// Checkout Execution
if ($action === 'checkout') {
    if (empty($perms['can_checkout'])) {
        context_redirect('err', 'You do not have lock/unlock access.');
    }
    if (!can_document_capability($db, $user, $id, 'can_checkout')) {
        context_redirect('err', 'You do not have lock/unlock access for this file.');
    }
    if ($doc['is_locked']) { 
        context_redirect('err', "Document is already locked."); 
    }
    
    // Anyone authorized can lock a completely free file
    $stmt = $db->prepare('UPDATE documents SET is_locked=1, locked_by=? WHERE id=?');
    $stmt->bind_param('ii', $user['id'], $id);
    $stmt->execute();
    audit_log($user['id'], 'CHECKOUT', "Checked out '{$doc['filename']}'");
    context_redirect('ok', "'{$doc['filename']}' checked out.");
}

// Checkin Execution (Unlock)
if ($action === 'checkin') {
    if (empty($perms['can_checkout'])) {
        context_redirect('err', 'You do not have lock/unlock access.');
    }
    if (!can_document_capability($db, $user, $id, 'can_checkout')) {
        context_redirect('err', 'You do not have lock/unlock access for this file.');
    }
    if (!$doc['is_locked']) { 
        context_redirect('err', 'Document is not locked.'); 
    }

    // Get locker's role to determine bypass hierarchy
    $locker_id = (int)$doc['locked_by'];
    $l_stmt = $db->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
    $l_stmt->bind_param('i', $locker_id);
    $l_stmt->execute();
    $locker = $l_stmt->get_result()->fetch_assoc();
    $locker_role = $locker['role'] ?? 'user';

    $can_bypass = false;

    // 1. You are the original locker
    if ($locker_id === (int)$user['id']) {
        $can_bypass = true;
    }
    // 2. Admin can unlock anything EXCEPT what another Admin locked (unless it's themselves)
    elseif ($user['role'] === 'admin' && $locker_role !== 'admin') {
        $can_bypass = true;
    }
    // 3. Contributor can unlock files locked by regular users
    elseif ($user['role'] === 'contributor' && $locker_role === 'user') {
        $can_bypass = true;
    }

    if (!$can_bypass) {
        context_redirect('err', 'Unauthorized bypass restriction: This file is locked by a higher or equal tier authority.');
    }

    $active_file_path = UPLOAD_DIR . '/' . basename($doc['storage_path']);

    // Direct Padlock Release Logic
    $stmt = $db->prepare('UPDATE documents SET is_locked=0, locked_by=NULL WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    audit_log($user['id'], 'CHECKIN_CANCEL', "Unlocked/Released padlock on '{$doc['filename']}' via access hierarchy rule.");
    context_redirect('ok', "Padlock release finalized for '{$doc['filename']}' at Version v" . $doc['version']);
}

// ── FIXED CHECKIN EXECUTION WITH DUPLICATE HASH PROTECTION ───────────────────
if ($action === 'checkin') {
    if (!$doc['is_locked']) { 
        context_redirect('err', 'Document is not locked.'); 
    }
    if ((int)$doc['locked_by'] !== $user['id'] && $user['role'] !== 'admin') {
        context_redirect('err', 'Unauthorized checkin restriction bounds.');
    }

    $active_file_path = UPLOAD_DIR . '/' . basename($doc['storage_path']);

    // If an updated file was passed through the post parameters stream, check it
    if (isset($_FILES['revised_document']) && $_FILES['revised_document']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['revised_document'];
        if (file_exists($active_file_path)) {
            $old_file_hash = md5_file($active_file_path);
            $new_file_hash = md5_file($file['tmp_name']);

            if ($old_file_hash === $new_file_hash) {
                // Cancel version bump completely
                $stmt = $db->prepare('UPDATE documents SET is_locked=0, locked_by=NULL WHERE id=?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                audit_log($user['id'], 'CHECKIN_CANCEL', "Checkin '{$doc['filename']}' cancelled—No changes detected.");
                context_redirect('ok', "'{$doc['filename']}' unlocked safely. No content changes detected; version kept at v" . $doc['version']);
            }
        }
    } else {
        // Fallback for direct button clicks: Check if the file's current size matches the active size database record.
        if (file_exists($active_file_path) && (int)filesize($active_file_path) === (int)$doc['size']) {
            $stmt = $db->prepare('UPDATE documents SET is_locked=0, locked_by=NULL WHERE id=?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            audit_log($user['id'], 'CHECKIN_CANCEL', "Checkin '{$doc['filename']}' released—No changes detected.");
            context_redirect('ok', "No changes detected in file contents. Padlock release finalized at Version v" . $doc['version']);
        }
    }

    // Process increment if actual file alterations were successfully written to disk
    $new_v = (int)$doc['version'] + 1;
    $stmt = $db->prepare('UPDATE documents SET is_locked=0, locked_by=NULL, version=? WHERE id=?');
    $stmt->bind_param('ii', $new_v, $id);
    $stmt->execute();
    
    audit_log($user['id'], 'CHECKIN', "Checked in '{$doc['filename']}' — now version v{$new_v}");
    context_redirect('ok', "'{$doc['filename']}' checked in as version v" . $new_v);
}
