<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_login();

$settingsObj = new Settings();
$notif = new Notification();
$userModel = new User();
$db = get_db();

$success = '';
$error = '';
$orgAdminId = Settings::getOrgAdminId($user);
$brand = $settingsObj->getBrand($orgAdminId);
$logoUrl = $settingsObj->getLogoUrl($orgAdminId);
$fullUser = $userModel->findById((int)$user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_account') {
        $uname = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (!$uname || !$email) {
            $error = 'Username and email are required.';
        } elseif ($userModel->usernameExists($uname, (int)$user['id'])) {
            $error = 'Username already taken.';
        } else {
            if ($pass && strlen($pass) >= 6) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE users SET username=?, email=?, phone=?, password_hash=? WHERE id=?');
                $stmt->bind_param('ssssi', $uname, $email, $phone, $hash, $user['id']);
            } else {
                $stmt = $db->prepare('UPDATE users SET username=?, email=?, phone=? WHERE id=?');
                $stmt->bind_param('sssi', $uname, $email, $phone, $user['id']);
            }
            if ($stmt->execute()) {
                Auth::refreshSession((int)$user['id']);
                audit_log($user['id'], 'PROFILE_UPDATE', "Updated account profile");
                $notif->add((int)$user['id'], 'Account', 'Your profile has been updated.');
                Mailer::send($email, $uname, 'FILESTAC DMS — Account Updated',
                    '<p>Hello ' . htmlspecialchars($uname) . ',</p><p>Your account information was updated successfully.</p>');
                flash_redirect(app_url('settings/settings.php'), 'ok', 'Account updated successfully.');
            }
        }
    }

    if ($action === 'update_brand' && $user['role'] === 'admin') {
        $brandName = trim($_POST['brand_name'] ?? DEFAULT_BRAND);
        if ($brandName === '') $brandName = DEFAULT_BRAND;

        $logoPath = $brand['logo_path'] ?? null;
        if (!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $dir = APP_ROOT . '/assets/logo/org_' . $orgAdminId;
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
                $dest = $dir . '/logo.' . $ext;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                    $logoPath = 'assets/logo/org_' . $orgAdminId . '/logo.' . $ext;
                }
            }
        }
        $settingsObj->updateBrand($orgAdminId, $brandName, $logoPath);
        audit_log($user['id'], 'BRAND_UPDATE', "Updated brand to '$brandName'");
        $notif->add((int)$user['id'], 'Branding', 'Logo and brand name updated.');
        Mailer::send($user['email'], $user['username'], 'FILESTAC DMS — Brand Updated',
            '<p>Your workspace branding has been updated to <strong>' . htmlspecialchars($brandName) . '</strong>.</p>');
        flash_redirect(app_url('settings/settings.php'), 'ok', 'Branding updated successfully.');
    }

    if ($action === 'remove_logo' && $user['role'] === 'admin') {
        $settingsObj->removeLogo($orgAdminId);
        flash_redirect(app_url('settings/settings.php'), 'ok', 'Logo reset to default.');
    }

    if ($action === 'update_theme') {
        $theme = in_array($_POST['theme'] ?? '', ['light', 'dark']) ? $_POST['theme'] : 'light';
        $stmt = $db->prepare('UPDATE users SET theme=? WHERE id=?');
        $stmt->bind_param('si', $theme, $user['id']);
        $stmt->execute();
        Auth::refreshSession((int)$user['id']);
        flash_redirect(app_url('settings/settings.php'), 'ok', 'Theme updated.');
    }
}

$brand = $settingsObj->getBrand($orgAdminId);
$logoUrl = $settingsObj->getLogoUrl($orgAdminId);
$fullUser = $userModel->findById((int)$user['id']);

$page_title = 'Settings';
include APP_ROOT . '/partials/header.php';
?>

<div class="settings-grid">
    <div class="settings-card">
        <h3>Account Settings</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_account">
            <div class="form-group">
                <label>Username (unique)</label>
                <input type="text" name="username" value="<?= htmlspecialchars($fullUser['username']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($fullUser['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($fullUser['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>New Password (leave blank to keep)</label>
                <input type="password" name="password" placeholder="Min 6 characters">
            </div>
            <button type="submit" class="btn btn-primary">Save Account</button>
        </form>
    </div>

    <?php if ($user['role'] === 'admin'): ?>
    <div class="settings-card">
        <h3>Workspace Branding</h3>
        <p class="muted">Applies to your account and all members.</p>
        <div class="logo-preview-box">
            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo preview" id="logoPreview">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_brand">
            <div class="form-group">
                <label>Brand Name</label>
                <input type="text" name="brand_name" value="<?= htmlspecialchars($brand['brand_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Logo (PNG/JPG, max 2MB)</label>
                <input type="file" name="logo" accept="image/png,image/jpeg" onchange="previewLogo(this)">
            </div>
            <button type="submit" class="btn btn-primary">Update Branding</button>
        </form>
        <form method="POST" style="margin-top:10px">
            <input type="hidden" name="action" value="remove_logo">
            <button type="submit" class="btn btn-outline btn-sm">Reset Logo</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="settings-card">
        <h3>Theme</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_theme">
            <div class="theme-options">
                <button type="submit" name="theme" value="light" class="theme-btn <?= ($fullUser['theme'] ?? 'light') === 'light' ? 'active' : '' ?>">☀ Light</button>
                <button type="submit" name="theme" value="dark" class="theme-btn <?= ($fullUser['theme'] ?? '') === 'dark' ? 'active' : '' ?>">🌙 Dark</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('logoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include APP_ROOT . '/partials/footer.php'; ?>
