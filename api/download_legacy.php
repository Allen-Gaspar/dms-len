<?php
/**
 * download.php — Secure file download with proper headers.
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('Invalid document ID.'); }

$stmt = $db->prepare('SELECT * FROM documents WHERE id=? AND is_deleted=0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) { http_response_code(404); die('Document not found.'); }

if ($user['role'] === 'casual') {
    $chk = $db->prepare('SELECT id FROM document_shares WHERE document_id=? AND shared_with_user_id=? LIMIT 1');
    $chk->bind_param('ii', $id, $user['id']);
    $chk->execute();
    if (!$chk->get_result()->fetch_assoc()) { http_response_code(403); die('Access denied.'); }
}

$file_path = UPLOAD_DIR . '/' . basename($doc['storage_path']);
if (!file_exists($file_path)) { http_response_code(404); die('File not found on server.'); }

audit_log($user['id'], 'DOWNLOAD', "Downloaded '{$doc['filename']}'");

$ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));
$mimeMap = [
    'pdf'=>'application/pdf','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg',
    'gif'=>'image/gif','doc'=>'application/msword','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'=>'application/vnd.ms-excel','xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt'=>'text/plain','csv'=>'text/csv','zip'=>'application/zip',
];
$mime = $mimeMap[$ext] ?? 'application/octet-stream';
$filesize = filesize($file_path);

while (ob_get_level()) ob_end_clean();

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . rawurlencode($doc['filename']) . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

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
