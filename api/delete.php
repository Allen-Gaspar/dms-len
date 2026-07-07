<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_role('contributor', 'admin');
$db   = get_db();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) flash_redirect(page_url('documents.php'), 'err', 'Invalid document ID.');

$stmt = $db->prepare('SELECT * FROM documents WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$doc = $stmt->get_result()->fetch_assoc();

if (!$doc || $doc['is_deleted']) flash_redirect(page_url('documents.php'), 'err', 'Document not found.');

$folder_id = (int)($doc['folder_id'] ?? 0);
$origin = $_GET['origin'] ?? $_POST['origin'] ?? '';
$stmt = $db->prepare('UPDATE documents SET is_deleted=1 WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
audit_log($user['id'], 'SOFT_DELETE', "Moved '{$doc['filename']}' to trash");

$msg = urlencode("'{$doc['filename']}' moved to trash.");
if ($origin === 'private') {
    $target = $folder_id > 0 ? "private.php?folder_id={$folder_id}" : 'private.php';
    $join = strpos($target, '?') === false ? '?' : '&';
    header('Location: ' . page_url($target . $join . "ok={$msg}"));
} elseif ($folder_id > 0) header('Location: ' . page_url("folders.php?id={$folder_id}&ok={$msg}"));
else header('Location: ' . page_url("documents.php?ok={$msg}"));
exit;
