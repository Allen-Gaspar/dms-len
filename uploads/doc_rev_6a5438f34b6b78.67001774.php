<?php
/**
 * trash.php — Admin trash: restore or purge documents and users.
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_role('admin');
$db   = get_db();
$userModel = new User();

$tab = $_GET['tab'] ?? 'documents';
$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit = ROWS_PER_PAGE;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'restore_doc') {
        $stmt = $db->prepare('UPDATE documents SET is_deleted=0 WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        audit_log($user['id'], 'RESTORE', "Restored document #$id");
        flash_redirect(page_url('trash.php?tab=documents'), 'ok', 'Document restored.');
    }
    if ($action === 'purge_doc') {
        $stmt = $db->prepare('SELECT * FROM documents WHERE id=? AND is_deleted=1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();
        if ($doc) {
            $fp = UPLOAD_DIR . '/' . basename($doc['storage_path']);
            if (file_exists($fp)) unlink($fp);
            $stmt = $db->prepare('DELETE FROM documents WHERE id=?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            audit_log($user['id'], 'PURGE', "Purged '{$doc['filename']}'");
        }
        flash_redirect(page_url('trash.php?tab=documents'), 'ok', 'Document permanently deleted.');
    }
    if ($action === 'restore_user') {
        $stmt = $db->prepare('UPDATE users SET is_deleted=0, status="active" WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        flash_redirect(page_url('trash.php?tab=users'), 'ok', 'User restored.');
    }
    if ($action === 'purge_user') {
        $stmt = $db->prepare('DELETE FROM users WHERE id=? AND is_deleted=1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        flash_redirect(page_url('trash.php?tab=users'), 'ok', 'User permanently deleted.');
    }
}

if ($tab === 'users') {
    $db->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0");
    $countStmt = $db->query('SELECT COUNT(*) FROM users WHERE is_deleted=1');
    $total = (int)$countStmt->fetch_row()[0];
    $stmt = $db->prepare('SELECT * FROM users WHERE is_deleted=1 ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $total = (int)$db->query('SELECT COUNT(*) FROM documents WHERE is_deleted=1')->fetch_row()[0];
    $stmt = $db->prepare('SELECT d.*, u.username AS uploader_name FROM documents d LEFT JOIN users u ON u.id=d.uploaded_by WHERE d.is_deleted=1 ORDER BY d.created_at DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$page_title = 'Trash';
include __DIR__ . '/../partials/header.php';
?>
    <!-- ── TRASH PAGE HEADER ── -->
<div class="page-header">
  <div style="display:flex;gap:10px">
    <a href="<?= page_url('trash.php?tab=documents') ?>" class="btn <?= $tab === 'documents' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Files</a>
    <a href="<?= page_url('trash.php?tab=users') ?>" class="btn <?= $tab === 'users' ? 'btn-primary' : 'btn-outline' ?> btn-sm">Users</a>
  </div>
</div>

<table class="data-table">
  <thead>
    <?php if ($tab === 'users'): ?>
      <tr><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr>
    <?php else: ?>
      <tr><th>Filename</th><th>Version</th><th>Uploaded By</th><th>Date</th><th>Actions</th></tr>
    <?php endif; ?>
  </thead>
  <tbody>
  <?php if (empty($items)): ?>
    <tr><td colspan="5" class="empty-row">Trash is empty.</td></tr>
  <?php elseif ($tab === 'users'): ?>
    <?php foreach ($items as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['role'] ?></td>
        <td>
          <form method="POST" style="display:inline"><input type="hidden" name="action" value="restore_user"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="btn btn-sm btn-ok">Restore</button></form>
          <button class="btn btn-sm btn-danger" data-confirm="Permanently delete this user?" data-confirm-title="Purge User" data-form="pu-<?= $u['id'] ?>">Purge</button>
          <form method="POST" id="pu-<?= $u['id'] ?>" style="display:none"><input type="hidden" name="action" value="purge_user"><input type="hidden" name="id" value="<?= $u['id'] ?>"></form>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <?php foreach ($items as $doc): ?>
      <tr>
        <td><button type="button" class="file-name-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)"><?= htmlspecialchars($doc['filename']) ?></button></td>
        <td><button type="button" class="version-link" onclick="DMS.openFileDetail(<?= (int)$doc['id'] ?>)">v<?= (int)$doc['version'] ?></button></td>
        <td><?= htmlspecialchars($doc['uploader_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars(substr($doc['created_at'], 0, 10)) ?></td>
        <td>
          <form method="POST" style="display:inline"><input type="hidden" name="action" value="restore_doc"><input type="hidden" name="id" value="<?= $doc['id'] ?>"><button class="btn btn-sm btn-ok">Restore</button></form>
          <button class="btn btn-sm btn-danger" data-confirm="Permanently delete this file?" data-confirm-title="Purge File" data-form="pd-<?= $doc['id'] ?>">Purge</button>
          <form method="POST" id="pd-<?= $doc['id'] ?>" style="display:none"><input type="hidden" name="action" value="purge_doc"><input type="hidden" name="id" value="<?= $doc['id'] ?>"></form>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>

<?= Pagination::render($total, $offset, $limit, page_url('trash.php'), ['tab' => $tab]) ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
