<?php
require_once __DIR__ . '/../bootstrap/init.php';

$error = '';
$token = $_GET['token'] ?? '';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => BASE_URL, 'secure' => false, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

$db = get_db();
$valid = false;
$userId = 0;

if ($token) {
    $stmt = $db->prepare('SELECT user_id FROM password_resets WHERE token=? AND used=0 AND expires_at > NOW() LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) { $valid = true; $userId = (int)$row['user_id']; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET password_hash=? WHERE id=?');
        $stmt->bind_param('si', $hash, $userId);
        $stmt->execute();
        $stmt = $db->prepare('UPDATE password_resets SET used=1 WHERE token=?');
        $stmt->bind_param('s', $token);
        $stmt->execute();

        $userModel = new User();
        $u = $userModel->findById($userId);
        $notif = new Notification();
        $notif->add($userId, 'Security', 'Your password was changed.');
        Mailer::send($u['email'], $u['username'], 'FILESTAC DMS — Password Changed',
            '<p>Your password has been reset successfully.</p>');
        audit_log($userId, 'PASSWORD_RESET', 'Password reset via email link');
        header('Location: ' . page_url('login.php?ok=' . urlencode('Password reset. You can now login.')));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Reset Password</title>
<link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body class="skin-login">
<div class="login-wrap">
  <div class="login-box">
    <h1>Reset Password</h1>
    <?php if (!$valid): ?>
      <p class="subtitle">Invalid or expired reset link.</p>
      <a href="<?= app_url('auth/forgot_password.php') ?>" class="btn btn-primary btn-full">Request New Link</a>
    <?php else: ?>
      <?php if ($error): ?><div id="toast-data" data-msg="<?= htmlspecialchars($error) ?>" data-type="error" hidden></div><?php endif; ?>
      <form method="POST">
        <div class="form-group"><label>New Password</label><input type="password" name="password" required minlength="6"></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm" required></div>
        <button type="submit" class="btn btn-primary btn-full">Reset Password</button>
      </form>
    <?php endif; ?>
    <p class="login-note"><a href="<?= page_url('login.php') ?>">Back to Login</a></p>
  </div>
</div>
<script src="<?= asset_url('js/app.js') ?>"></script>
</body>
</html>
