<?php
/**
 * api/document_detail.php — File info, preview URL, versions (JSON).
 */
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_login();
$db   = get_db();
$id   = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($action === 'suggest') {
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode([]);
        exit;
    }
    $like = '%' . $q . '%';
    if ($user['role'] === 'casual') {
        $stmt = $db->prepare(
            'SELECT d.id, d.filename, d.version, d.size
             FROM documents d
             INNER JOIN document_shares ds ON ds.document_id=d.id AND ds.shared_with_user_id=?
             WHERE d.is_deleted=0 AND d.filename LIKE ?
             ORDER BY d.filename ASC LIMIT 8'
        );
        $stmt->bind_param('is', $user['id'], $like);
    } else {
        $stmt = $db->prepare(
            'SELECT id, filename, version, size
             FROM documents
             WHERE is_deleted=0 AND filename LIKE ?
             ORDER BY filename ASC LIMIT 8'
        );
        $stmt->bind_param('s', $like);
    }
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$stmt = $db->prepare(
    'SELECT d.*, u.username AS uploader_name, u.role AS uploader_role,
            lk.username AS locker_name
     FROM documents d
     LEFT JOIN users u ON u.id = d.uploaded_by
     LEFT JOIN users lk ON lk.id = d.locked_by
     WHERE d.id = ? LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) {
    echo json_encode(['success' => false, 'message' => 'Not found']);
    exit;
}

// Use unified access control
if (!AccessControl::canViewDocument($db, $user, $id)) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));
$previewTypes = ['png','jpg','jpeg','gif','webp','svg','pdf','mp4','webm','mov','txt','csv','json','xml','html','htm','css','js','php','sql','md','log','ppt','pptx','doc','docx','xls','xlsx'];
$canPreview = in_array($ext, $previewTypes, true);

$versions = [];
$vstmt = $db->prepare('SELECT * FROM document_versions WHERE document_id=? ORDER BY version_number DESC');
$vstmt->bind_param('i', $id);
$vstmt->execute();
$versions = [];
foreach ($vstmt->get_result()->fetch_all(MYSQLI_ASSOC) as $version) {
    $version['preview_url'] = app_url('api/download.php?id=' . $id . '&version_id=' . (int)$version['id'] . '&preview=1');
    $version['download_url'] = app_url('api/download.php?id=' . $id . '&version_id=' . (int)$version['id']);
    $versions[] = $version;
}

echo json_encode([
    'success' => true,
    'doc' => $doc,
    'preview_url' => $canPreview ? app_url('api/download.php?id=' . $id . '&preview=1') : null,
    'download_url' => app_url('api/download.php?id=' . $id),
    'ext' => $ext,
    'can_preview' => $canPreview,
    'versions' => $versions,
    'is_admin' => $user['role'] === 'admin',
    'can_edit' => AccessControl::canEditDocument($db, $user, $id) && !empty((new User())->getPermissions((int)$user['id'])['can_edit']),
    'can_delete' => AccessControl::canDeleteDocument($db, $user, $id) && !empty((new User())->getPermissions((int)$user['id'])['can_delete']),
]);
