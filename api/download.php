<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_login();
$db   = get_db();
$role = $user['role'] ?? '';
$perms = (new User())->getPermissions((int)$user['id']);

$id = (int)($_GET['id'] ?? 0);
$versionId = (int)($_GET['version_id'] ?? 0);
$preview = !empty($_GET['preview']);

// FIXED: Admin has total override access. Restrictions apply only to non-admins when they aren't previewing.
if ($role !== 'admin' && empty($perms['can_download']) && !$preview) {
    http_response_code(403);
    die('Download access denied.');
}

if ($id <= 0) { http_response_code(400); die('Invalid document ID.'); }

$stmt = $db->prepare('SELECT * FROM documents WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc) { http_response_code(404); die('Document not found.'); }

if (!AccessControl::canViewDocument($db, $user, $id)) {
    http_response_code(403); die('Access denied.');
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

$ext = strtolower(pathinfo($doc['filename'], PATHINFO_EXTENSION));

// INTERCEPTOR: Disable preview pane only for PowerPoint files to prevent automatic download
if ($preview && in_array($ext, ['ppt', 'pptx'], true)) {
    while (ob_get_level()) ob_end_clean();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body, html { margin: 0; padding: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #ffffff; font-family: system-ui, sans-serif; }
            .no-preview { text-align: center; color: #64748b; padding: 20px; }
            .icon { font-size: 50px; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="no-preview">
            <div class="icon">📊</div>
            <h3>No Preview Available</h3>
            <p>PowerPoint presentations cannot be viewed inside the web preview pane.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!$preview) audit_log($user['id'], 'DOWNLOAD', "Downloaded '{$displayName}'");

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

// Force Content-Disposition to attachment when not explicit preview
header('Content-Type: ' . $mime);
header('Content-Length: ' . $filesize);
header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . rawurlencode($displayName) . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

$fp = fopen($file_path, 'rb');
if ($fp) {
    fpassthru($fp);
    fclose($fp);
}
exit;
