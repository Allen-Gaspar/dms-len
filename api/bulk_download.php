<?php
require_once __DIR__ . '/../core/auth.php';

$user = require_login();
$db = get_db();
$perms = (new User())->getPermissions((int)$user['id']);
$ids = array_values(array_unique(array_map('intval', explode(',', $_GET['ids'] ?? ''))));
$ids = array_filter($ids);

if ($user['role'] !== 'admin' && empty($perms['can_download'])) {
    http_response_code(403);
    die('You do not have download access.');
}
if (empty($ids) || !class_exists('ZipArchive')) {
    http_response_code(400);
    die('No files selected or ZIP unavailable.');
}

$tmpFile = tempnam(sys_get_temp_dir(), 'dms_files_');
$zip = new ZipArchive();
if ($zip->open($tmpFile, ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    die('Unable to create ZIP.');
}

$added = 0;
foreach ($ids as $id) {
    if (!AccessControl::canViewDocument($db, $user, $id) && $user['role'] !== 'admin') continue;
    $stmt = $db->prepare('SELECT filename, storage_path FROM documents WHERE id = ? AND is_deleted = 0 LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    if (!$doc) continue;
    $path = UPLOAD_DIR . '/' . basename($doc['storage_path']);
    if (!is_file($path)) continue;
    $zip->addFile($path, sanitize_bulk_zip_name($doc['filename']));
    $added++;
}
$zip->close();

if ($added === 0) {
    @unlink($tmpFile);
    http_response_code(404);
    die('No downloadable files found.');
}

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($tmpFile));
header('Content-Disposition: attachment; filename="selected-files.zip"');
readfile($tmpFile);
@unlink($tmpFile);
exit;

function sanitize_bulk_zip_name(string $name): string {
    $name = trim(preg_replace('/[\\\\\\/\\:\\*\\?\\"\\<\\>\\|]+/', '-', $name));
    return $name !== '' ? $name : 'file';
}
