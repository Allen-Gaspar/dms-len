<?php
/**
 * user_detail.php — Full user info, file count, access control.
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_role('admin');
$db = get_db();
$userModel = new User();

$uid = (int)($_GET['id'] ?? 0);
$target = $userModel->findById($uid);
if (!$target) {
    flash_redirect(page_url('users.php'), 'err', 'User not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_permissions') {
    $perms = [
        'can_add' => isset($_POST['can_add']) ? 1 : 0,
        'can_download' => isset($_POST['can_download']) ? 1 : 0,
        'can_share' => isset($_POST['can_share']) ? 1 : 0,
        'can_delete' => isset($_POST['can_delete']) ? 1 : 0,
        'can_edit' => isset($_POST['can_edit']) ? 1 : 0,
        'can_checkout' => isset($_POST['can_checkout']) ? 1 : 0,
    ];
    $userModel->setPermissions($uid, $perms, (int)$user['id']);
    audit_log($user['id'], 'PERMISSIONS', "Updated permissions for user #{$uid}");
    flash_redirect(page_url('user_detail.php?id=' . $uid), 'ok', 'Access control saved.');
}

$fileCount = $userModel->getFileCount($uid);
$perms = $userModel->getPermissions($uid);

$page_title = 'User: ' . $target['username'];
include __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <a href="<?= page_url('users.php') ?>" class="btn btn-outline btn-sm">&larr; Back to Users</a>
</div>

<div class="settings-grid">
  <div class="settings-card">
    <h3>Account Information</h3>
    <dl class="user-detail-grid">
      <dt>Username</dt><dd><?= htmlspecialchars($target['username']) ?></dd>
      <dt>Email</dt><dd><?= htmlspecialchars($target['email']) ?></dd>
      <dt>Contact</dt><dd><?= htmlspecialchars($target['phone'] ?? '—') ?></dd>
      <dt>Role</dt><dd><span class="badge"><?= $target['role'] ?></span></dd>
      <dt>Status</dt><dd><span class="badge <?= $target['status'] === 'active' ? 'badge-ok' : 'badge-warn' ?>"><?= $target['status'] ?></span></dd>
      <dt>Created</dt><dd><?= htmlspecialchars($target['created_at']) ?></dd>
      <dt>Files Uploaded</dt><dd><strong><?= $fileCount ?></strong> document(s)</dd>
    </dl>
  </div>

  <div class="settings-card">
    <h3>Access Control</h3>
    <form method="POST">
      <input type="hidden" name="action" value="save_permissions">
      <div class="perm-grid">
        <label><input type="checkbox" name="can_add" <?= $perms['can_add'] ? 'checked' : '' ?>> Add File</label>
        <label><input type="checkbox" name="can_download" <?= $perms['can_download'] ? 'checked' : '' ?>> Download</label>
        <label><input type="checkbox" name="can_share" <?= $perms['can_share'] ? 'checked' : '' ?>> Share</label>
        <label><input type="checkbox" name="can_delete" <?= $perms['can_delete'] ? 'checked' : '' ?>> Delete</label>
        <label><input type="checkbox" name="can_edit" <?= $perms['can_edit'] ? 'checked' : '' ?>> Edit</label>
        <label><input type="checkbox" name="can_checkout" <?= $perms['can_checkout'] ? 'checked' : '' ?>> Lock/Unlock</label>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:16px">Save Access Control</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
