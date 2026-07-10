<?php
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_login();
$db = get_db();
$folderId = (int)($_GET['folder_id'] ?? 0);

if ($folderId <= 0 || !AccessControl::canViewFolder($db, $user, $folderId)) {
    echo json_encode(['success' => false, 'message' => 'Folder not accessible.']);
    exit;
}

$perms = (new User())->getPermissions((int)$user['id']);

function shared_capability(mysqli $db, array $user, array $doc, string $capability): bool {
    if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
    if ((int)$doc['is_private'] === 0 && $user['role'] === 'admin') return true;

    if (!empty($doc['folder_id'])) {
        $sql = "SELECT {$capability} AS allowed FROM folder_shares WHERE folder_id=? AND shared_with_user_id=? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ii', $doc['folder_id'], $user['id']);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && (int)$row['allowed'] === 1) return true;
    }

    $sql = "SELECT {$capability} AS allowed FROM document_shares WHERE document_id=? AND shared_with_user_id=? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ii', $doc['id'], $user['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row && (int)$row['allowed'] === 1;
}

$stmt = $db->prepare(
    'SELECT d.*, u.username AS uploader_name, lk.username AS locker_name
     FROM documents d
     LEFT JOIN users u ON u.id = d.uploaded_by
     LEFT JOIN users lk ON lk.id = d.locked_by
     WHERE d.folder_id = ? AND d.is_deleted = 0
     ORDER BY d.filename ASC'
);
$stmt->bind_param('i', $folderId);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$files = [];
foreach ($rows as $doc) {
    if (!AccessControl::canViewDocument($db, $user, (int)$doc['id'])) continue;
    $files[] = [
        'id' => (int)$doc['id'],
        'filename' => $doc['filename'],
        'ext' => strtoupper(pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION) ?: 'FILE'),
        'version' => (int)($doc['version'] ?? 1),
        'size' => (int)($doc['size'] ?? 0),
        'uploaded_by' => $doc['uploader_name'] ?? '-',
        'is_locked' => (int)($doc['is_locked'] ?? 0),
        'created_at' => $doc['created_at'] ?? '',
        'can_edit' => !empty($perms['can_edit']) && AccessControl::canEditDocument($db, $user, (int)$doc['id']),
        'can_delete' => !empty($perms['can_delete']) && AccessControl::canDeleteDocument($db, $user, (int)$doc['id']),
        'can_download' => !empty($perms['can_download']) && shared_capability($db, $user, $doc, 'can_download'),
        'can_checkout' => !empty($perms['can_checkout']) && shared_capability($db, $user, $doc, 'can_checkout'),
        'can_share' => !empty($perms['can_share']) && shared_capability($db, $user, $doc, 'can_share'),
    ];
}

echo json_encode(['success' => true, 'files' => $files]);
