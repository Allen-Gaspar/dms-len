<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$perms = (new User())->getPermissions((int)$user['id']);
if (empty($perms['can_download'])) {
    http_response_code(403);
    die('Download access denied.');
}

$id = (int)($_GET['id'] ?? 0);
$versionId = (int)($_GET['version_id'] ?? 0);
$preview = !empty($_GET['preview']);

if ($id <= 0) { http_response_code(400); die('Invalid document ID.'); }

$stmt = $db->prepare('SELECT * FROM documents WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) { http_response_code(404); die('Document not found.'); }

if ((int)($doc['is_private'] ?? 0) === 1 && (int)$doc['uploaded_by'] !== (int)$user['id']) {
    $chk = $db->prepare('SELECT id FROM document_shares WHERE document_id=? AND shared_with_user_id=? LIMIT 1');
    $chk->bind_param('ii', $id, $user['id']);
    $chk->execute();
    if (!$chk->get_result()->fetch_assoc()) { http_response_code(403); die('Access denied.'); }
} elseif ($user['role'] === 'casual') {
    $chk = $db->prepare('SELECT id FROM document_shares WHERE document_id=? AND shared_with_user_id=? LIMIT 1');
    $chk->bind_param('ii', $id, $user['id']);
    $chk->execute();
    if (!$chk->get_result()->fetch_assoc()) { http_response_code(403); die('Access denied.'); }
}

$storagePath = $doc['storage_path'];
$displayName = $doc['filename'];

if ($versionId > 0) {
    $vstmt = $db->prepare('SELECT * FROM document_versions WHERE id=? AND document_id=? LIMIT 1');
    $vstmt->bind_param('ii', $versionId, $id);
    $vstmt->execute();
    $version = $vstmt->get_result()->fetch_assoc();
    if (!$version) { http_response_code(404); die('Version not found.'); }
    $storagePath = $version['storage_path'];
    $displayName = 'v' . (int)$version['version_number'] . '-' . $doc['filename'];
}

$file_path = UPLOAD_DIR . '/' . basename($storagePath);
if (!file_exists($file_path)) { http_response_code(404); die('File not found.'); }

if (!$preview) audit_log($user['id'], 'DOWNLOAD', "Downloaded '{$displayName}'");

$ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));
$mimeMap = [
    'pdf'=>'application/pdf','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg',
    'gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','mp4'=>'video/mp4','webm'=>'video/webm','mov'=>'video/quicktime',
    'txt'=>'text/plain; charset=utf-8','csv'=>'text/plain; charset=utf-8','json'=>'application/json; charset=utf-8',
    'xml'=>'application/xml; charset=utf-8','html'=>'text/plain; charset=utf-8','htm'=>'text/plain; charset=utf-8',
    'css'=>'text/plain; charset=utf-8','js'=>'text/plain; charset=utf-8','php'=>'text/plain; charset=utf-8',
    'sql'=>'text/plain; charset=utf-8','md'=>'text/plain; charset=utf-8','log'=>'text/plain; charset=utf-8','zip'=>'application/zip',
];
$mime = $mimeMap[$ext] ?? 'application/octet-stream';
$filesize = filesize($file_path);

while (ob_get_level()) ob_end_clean();

header('Content-Type: ' . $mime);
header('Content-Length: ' . $filesize);
header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . rawurlencode($displayName) . '"');
header('X-DMS-Filename: ' . rawurlencode($displayName));
header('Cache-Control: private, max-age=3600');

$fp = fopen($file_path, 'rb');
if ($fp) {
    if (function_exists('flock')) flock($fp, LOCK_SH);
    fpassthru($fp);
    if (function_exists('flock')) flock($fp, LOCK_UN);
    fclose($fp);
} else {
    readfile($file_path);
}
exit;
