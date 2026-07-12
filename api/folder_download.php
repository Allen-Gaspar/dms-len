<?php
require_once __DIR__ . '/../core/auth.php';

$user = require_login();
$db = get_db();
$perms = (new User())->getPermissions((int)$user['id']);
$folderId = (int)($_GET['id'] ?? 0);

if ($user['role'] !== 'admin' && empty($perms['can_download'])) {
    http_response_code(403);
    die('You do not have download access.');
}

if ($folderId <= 0 || !can_download_folder($db, $user, $folderId)) {
    http_response_code(403);
    die('Folder download access denied.');
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    die('ZIP support is not enabled on this PHP installation.');
}

$root = get_folder($db, $folderId);
if (!$root) {
    http_response_code(404);
    die('Folder not found.');
}

$tmpFile = tempnam(sys_get_temp_dir(), 'dms_folder_');
$zip = new ZipArchive();
if ($zip->open($tmpFile, ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    die('Unable to create folder archive.');
}

$rootName = sanitize_zip_name($root['name'] ?: 'folder');
add_folder_to_zip($db, $user, $zip, $folderId, $rootName);
$zip->close();

$downloadName = $rootName . '.zip';
if (function_exists('audit_log')) {
    audit_log($user['id'], 'FOLDER_DOWNLOAD', "Downloaded folder '{$root['name']}'");
}

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($tmpFile));
header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"');
header('Cache-Control: no-cache, must-revalidate');
readfile($tmpFile);
@unlink($tmpFile);
exit;

function get_folder(mysqli $db, int $folderId): ?array {
    $stmt = $db->prepare('SELECT * FROM folders WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $folderId);
    $stmt->execute();
    $folder = $stmt->get_result()->fetch_assoc();
    return $folder ?: null;
}

function can_download_folder(mysqli $db, array $user, int $folderId): bool {
    $folder = get_folder($db, $folderId);
    if (!$folder) return false;
    if ($user['role'] === 'admin') return true;
    if ((int)$folder['created_by'] === (int)$user['id']) return true;
    if ((int)$folder['is_private'] === 0) return true;

    $stmt = $db->prepare('SELECT can_download FROM folder_shares WHERE folder_id = ? AND shared_with_user_id = ? LIMIT 1');
    $stmt->bind_param('ii', $folderId, $user['id']);
    $stmt->execute();
    $share = $stmt->get_result()->fetch_assoc();
    return $share && (int)$share['can_download'] === 1;
}

function can_download_document_for_folder(mysqli $db, array $user, array $doc): bool {
    if ($user['role'] === 'admin') return true;
    if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
    if ((int)$doc['is_private'] === 0) return true;

    if (!empty($doc['folder_id'])) {
        $stmt = $db->prepare('SELECT can_download FROM folder_shares WHERE folder_id = ? AND shared_with_user_id = ? LIMIT 1');
        $stmt->bind_param('ii', $doc['folder_id'], $user['id']);
        $stmt->execute();
        $share = $stmt->get_result()->fetch_assoc();
        if ($share && (int)$share['can_download'] === 1) return true;
    }

    $stmt = $db->prepare('SELECT can_download FROM document_shares WHERE document_id = ? AND shared_with_user_id = ? LIMIT 1');
    $stmt->bind_param('ii', $doc['id'], $user['id']);
    $stmt->execute();
    $share = $stmt->get_result()->fetch_assoc();
    return $share && (int)$share['can_download'] === 1;
}

function add_folder_to_zip(mysqli $db, array $user, ZipArchive $zip, int $folderId, string $zipPath): void {
    if (!can_download_folder($db, $user, $folderId)) return;
    $zip->addEmptyDir($zipPath);

    $docs = $db->prepare('SELECT * FROM documents WHERE folder_id = ? AND is_deleted = 0 ORDER BY filename ASC');
    $docs->bind_param('i', $folderId);
    $docs->execute();
    foreach ($docs->get_result()->fetch_all(MYSQLI_ASSOC) as $doc) {
        if (!can_download_document_for_folder($db, $user, $doc)) continue;
        $source = UPLOAD_DIR . '/' . basename($doc['storage_path'] ?? '');
        if (!is_file($source)) continue;
        $zip->addFile($source, $zipPath . '/' . sanitize_zip_name($doc['filename'] ?: basename($source)));
    }

    $children = $db->prepare('SELECT id, name FROM folders WHERE parent_id = ? ORDER BY name ASC');
    $children->bind_param('i', $folderId);
    $children->execute();
    foreach ($children->get_result()->fetch_all(MYSQLI_ASSOC) as $child) {
        add_folder_to_zip($db, $user, $zip, (int)$child['id'], $zipPath . '/' . sanitize_zip_name($child['name'] ?: ('folder-' . $child['id'])));
    }
}

function sanitize_zip_name(string $name): string {
    $name = trim(preg_replace('/[\\\\\\/\\:\\*\\?\\"\\<\\>\\|]+/', '-', $name));
    return $name !== '' ? $name : 'item';
}
