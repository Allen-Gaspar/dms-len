<?php
/**
 * api/upload.php — Handle file upload and folder creation (JSON).
 */
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_login();
$db   = get_db();
$role = $user['role'];

if ($role === 'casual') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$maxSize = 200 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_folder') {
    $name = trim($_POST['folder_name'] ?? '');
    $isPrivate = (int)($_POST['is_private'] ?? 0);
    $parentRaw = $_POST['parent_folder_id'] ?? 'root';
    $parentId = ($parentRaw === '' || $parentRaw === 'root') ? null : (int)$parentRaw;
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'Folder name required']);
        exit;
    }
    $type = $isPrivate ? 'private' : 'public';
    $parentCol = $db->query("SHOW COLUMNS FROM folders LIKE 'parent_id'");
    if (!$parentCol || $parentCol->num_rows === 0) {
        $db->query('ALTER TABLE folders ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER id');
    }
    $stmt = $db->prepare('INSERT INTO folders (name, parent_id, type, is_private, created_by) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sisii', $name, $parentId, $type, $isPrivate, $user['id']);
    if ($stmt->execute()) {
        audit_log($user['id'], 'FOLDER_CREATION', "Created folder: $name");
        echo json_encode(['success' => true, 'message' => "Folder '$name' created"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create folder']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['files'])) {
    $files = $_FILES['files'];
    $count = is_array($files['name']) ? count($files['name']) : 1;
    $uploaded = 0;
    $folderId = $_POST['folder_id'] ?? '';
    $folderIdParam = ($folderId === '' || $folderId === 'root') ? null : (int)$folderId;
    $isPrivate = (int)($_POST['is_private'] ?? 0);
    $shareScope = $_POST['sharing_scope'] ?? ($isPrivate ? 'private' : 'all');

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

    for ($i = 0; $i < $count; $i++) {
        $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $tmp  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $err  = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];

        if ($err !== UPLOAD_ERR_OK || !$name) continue;
        if ($size > $maxSize) continue;

        $orig = basename($name);
        $ext  = pathinfo($orig, PATHINFO_EXTENSION);
        $stored = uniqid('doc_', true) . ($ext ? '.' . $ext : '');
        $dest = UPLOAD_DIR . '/' . $stored;

        if (!move_uploaded_file($tmp, $dest)) continue;

        $priv = ($shareScope === 'private' || $isPrivate) ? 1 : 0;
        $stmt = $db->prepare('INSERT INTO documents (filename, storage_path, size, uploaded_by, folder_id, is_private) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssiiii', $orig, $stored, $size, $user['id'], $folderIdParam, $priv);
        $stmt->execute();
        $docId = $stmt->insert_id;

        if ($shareScope === 'all' && !$priv) {
            $col = $db->query("SHOW COLUMNS FROM document_shares LIKE 'share_with_all'");
            if ($col && $col->num_rows > 0) {
                $s = $db->prepare('INSERT INTO document_shares (document_id, share_with_all, shared_by) VALUES (?, 1, ?)');
                if ($s) {
                    $s->bind_param('ii', $docId, $user['id']);
                    $s->execute();
                }
            }
        }

        audit_log($user['id'], 'UPLOAD', "Uploaded '$orig'");
        $uploaded++;
    }

    echo json_encode(['success' => $uploaded > 0, 'message' => $uploaded . ' file(s) uploaded', 'count' => $uploaded]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
