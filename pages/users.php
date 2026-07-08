<?php
/**
 * users.php — Admin user management with pagination, trash, bulk permissions.
 */
require_once __DIR__ . '/../core/auth.php';
$user = require_role('admin');
$db   = get_db();
$userModel = new User();
$notif = new Notification();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $uname = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $role  = $_POST['role'] ?? '';
        $roles = ['admin', 'contributor', 'casual'];

        if (!$uname || !$email || !$pass || !in_array($role, $roles, true)) {
            flash_redirect(page_url('users.php'), 'err', 'All fields required.');
        } elseif (strlen($pass) < 6) {
            flash_redirect(page_url('users.php'), 'err', 'Password min 6 characters.');
        } else {
            try {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $adminId = (int)$user['id'];
                $stmt = $db->prepare('INSERT INTO users (username,email,password_hash,role,admin_id) VALUES (?,?,?,?,?)');
                $stmt->bind_param('ssssi', $uname, $email, $hash, $role, $adminId);
                $stmt->execute();
                $newUserId = (int)$stmt->insert_id;
                $perms = [
                    'can_add' => isset($_POST['can_add']) ? 1 : 0,
                    'can_download' => isset($_POST['can_download']) ? 1 : 0,
                    'can_share' => isset($_POST['can_share']) ? 1 : 0,
                    'can_delete' => isset($_POST['can_delete']) ? 1 : 0,
                    'can_edit' => isset($_POST['can_edit']) ? 1 : 0,
                    'can_checkout' => isset($_POST['can_checkout']) ? 1 : 0,
                ];
                $userModel->setPermissions($newUserId, $perms, $adminId);
                audit_log($user['id'], 'USER_CREATE', "Created user '$uname'");
                flash_redirect(page_url('users.php'), 'ok', "User '$uname' created.");
            } catch (Exception $e) {
                flash_redirect(page_url('users.php'), 'err', 'Username or email exists.');
            }
        }
    }

    if ($action === 'toggle_status') {
        $uid = (int)($_POST['uid'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($uid > 0 && in_array($status, ['active', 'frozen'], true)) {
            $stmt = $db->prepare('UPDATE users SET status=? WHERE id=?');
            $stmt->bind_param('si', $status, $uid);
            $stmt->execute();
            flash_redirect(page_url('users.php'), 'ok', 'Status updated.');
        }
    }

    if ($action === 'soft_delete') {
        $uid = (int)($_POST['uid'] ?? 0);
        if ($uid === (int)$user['id']) {
            flash_redirect(page_url('users.php'), 'err', 'Cannot delete yourself.');
        } elseif ($uid > 0) {
            $u = $userModel->findById($uid);
            $userModel->softDelete($uid);
            audit_log($user['id'], 'USER_TRASH', "Moved user '{$u['username']}' to trash");
            flash_redirect(page_url('users.php'), 'ok', 'User moved to trash.');
        }
    }

    if ($action === 'bulk_permissions') {
        $ids = array_map('intval', $_POST['user_ids'] ?? []);
        if (empty($ids)) {
            flash_redirect(page_url('users.php'), 'err', 'Select at least one user.');
        }
        $perms = [
            'can_add' => isset($_POST['can_add']) ? 1 : 0,
            'can_download' => isset($_POST['can_download']) ? 1 : 0,
            'can_share' => isset($_POST['can_share']) ? 1 : 0,
            'can_delete' => isset($_POST['can_delete']) ? 1 : 0,
            'can_edit' => isset($_POST['can_edit']) ? 1 : 0,
            'can_checkout' => isset($_POST['can_checkout']) ? 1 : 0,
        ];
        foreach ($ids as $uid) {
            if ($uid !== (int)$user['id']) {
                $userModel->setPermissions($uid, $perms, (int)$user['id']);
            }
        }
        audit_log($user['id'], 'BULK_PERMISSIONS', 'Updated permissions for ' . count($ids) . ' users');
        flash_redirect(page_url('users.php'), 'ok', 'Permissions updated for selected users.');
    }
}

$offset = max(0, (int)($_GET['offset'] ?? 0));
$limit  = ROWS_PER_PAGE;
$all_users = $userModel->listPaginated($offset, $limit);
$total = $userModel->countAll();

$page_title = 'User Management';
include __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <h2 class="page-title">User Management</h2>
  <button type="button" class="btn btn-primary" onclick="DMS.openModal('createUserModal')">Add New User</button>
</div>

<div id="createUserModal" class="modal-overlay">
  <div class="modal-card modal-lg">
    <button class="modal-close" onclick="DMS.closeModal('createUserModal')" aria-label="Close">&times;</button>
    <h3>Create New User</h3>
    <form method="POST">
      <input type="hidden" name="action" value="create">
      <div class="user-create-grid">
        <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" minlength="6" required></div>
        <div class="form-group">
          <label>Role</label>
          <select name="role" required>
            <option value="">Select role</option>
            <option value="admin">Admin</option>
            <option value="contributor">Contributor</option>
            <option value="casual">Casual</option>
          </select>
        </div>
      </div>
      <h4 class="modal-section-title">Access Control</h4>
      <div class="perm-grid">
        <label><input type="checkbox" name="can_add" checked> Add File</label>
        <label><input type="checkbox" name="can_edit" checked> Edit</label>
        <label><input type="checkbox" name="can_delete" checked> Delete</label>
        <label><input type="checkbox" name="can_download" checked> Download</label>
        <label><input type="checkbox" name="can_share" checked> Share</label>
        <label><input type="checkbox" name="can_checkout" checked> Lock/Unlock</label>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline" onclick="DMS.closeModal('createUserModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Create User</button>
      </div>
    </form>
  </div>
</div>

<div class="card legacy-create-user-card" hidden>
  <h3>Create New User</h3>
  <form method="POST" class="inline-form">
    <input type="hidden" name="action" value="create">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password (min 6)" required>
    <select name="role" required>
      <option value="">— Role —</option>
      <option value="admin">Admin</option>
      <option value="contributor">Contributor</option>
      <option value="casual">Casual</option>
    </select>
    <button type="submit" class="btn btn-primary">Create User</button>
  </form>
</div>

<form method="POST" id="bulkForm">
<input type="hidden" name="action" value="bulk_permissions">
<div class="card" style="margin:16px 0">
  <h3>Bulk Access Control</h3>
  <p class="muted">Select users below, then set permissions to apply to all selected.</p>
  <div class="perm-grid">
    <label><input type="checkbox" name="can_add" checked> Add File</label>
    <label><input type="checkbox" name="can_download" checked> Download</label>
    <label><input type="checkbox" name="can_share" checked> Share</label>
    <label><input type="checkbox" name="can_delete" checked> Delete</label>
    <label><input type="checkbox" name="can_edit" checked> Edit</label>
    <label><input type="checkbox" name="can_checkout" checked> Lock/Unlock</label>
  </div>
  <button type="submit" class="btn btn-secondary" style="margin-top:12px">Apply to Selected</button>
</div>

<table class="data-table">
  <thead>
    <tr>
      <th><input type="checkbox" id="selectAll" onclick="document.querySelectorAll('.user-cb').forEach(c=>c.checked=this.checked)"></th>
      <th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php if (empty($all_users)): ?>
    <tr><td colspan="7" class="empty-row">No users found.</td></tr>
  <?php endif; ?>
  <?php foreach ($all_users as $u): ?>
    <tr>
      <td><?php if ($u['id'] != $user['id']): ?><input type="checkbox" class="user-cb" name="user_ids[]" value="<?= $u['id'] ?>"><?php endif; ?></td>
      <td><a href="<?= page_url('user_detail.php?id=' . $u['id']) ?>" class="user-link"><?= htmlspecialchars($u['username']) ?></a><?= $u['id'] == $user['id'] ? ' <em>(you)</em>' : '' ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge"><?= $u['role'] ?></span></td>
      <td><span class="badge <?= $u['status'] === 'active' ? 'badge-ok' : 'badge-warn' ?>"><?= $u['status'] ?></span></td>
      <td><?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></td>
      <td class="actions">
        <?php if ($u['id'] !== $user['id']): ?>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="uid" value="<?= $u['id'] ?>">
            <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'frozen' : 'active' ?>">
            <button class="btn btn-sm <?= $u['status'] === 'active' ? 'btn-warn' : 'btn-ok' ?>"><?= $u['status'] === 'active' ? 'Freeze' : 'Unfreeze' ?></button>
          </form>
          <button type="button" class="btn btn-sm btn-danger" data-confirm="Move this user to trash?" data-confirm-title="Delete User" data-form="del-<?= $u['id'] ?>">Trash</button>
          <form method="POST" id="del-<?= $u['id'] ?>" style="display:none">
            <input type="hidden" name="action" value="soft_delete">
            <input type="hidden" name="uid" value="<?= $u['id'] ?>">
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</form>

<?= Pagination::render($total, $offset, $limit, page_url('users.php')) ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
