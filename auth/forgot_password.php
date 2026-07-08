<?php
require_once __DIR__ . '/../bootstrap/init.php';

$error = '';
$message = '';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => BASE_URL, 'secure' => false, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

if (!empty($_SESSION['user'])) {
    header('Location: ' . page_url('dashboard.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Please enter your account email.';
    } else {
        $userModel = new User();
        $found = $userModel->findByEmail($email);

        if (!$found) {
            $error = 'No account found with that email.';
        } else {
            $db = get_db();
            $db->query("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token VARCHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL, used TINYINT(1) DEFAULT 0
            )");
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $db->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)');
            $stmt->bind_param('iss', $found['id'], $token, $expires);
            $stmt->execute();

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetLink = $scheme . '://' . $host . app_url('auth/reset_password.php?token=' . $token);
            Mailer::send($found['email'], $found['username'], 'FILESTAC DMS — Password Reset',
                '<p>Hello ' . htmlspecialchars($found['username']) . ',</p>
                 <p>Click the link below to reset your password:</p>
                 <p><a href="' . htmlspecialchars($resetLink) . '">Reset Password</a></p>
                 <p>This link expires in 1 hour.</p>',
                "Reset your password: $resetLink");

            $message = 'If an account exists, a reset link has been sent to your email.';
            audit_log((int)$found['id'], 'PASSWORD_RESET_REQUEST', 'Password reset requested');
        }
    }
}

$settingsObj = new Settings();
$logoUrl = asset_url('logo/default.png');
$brandName = DEFAULT_BRAND;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot Password — <?= htmlspecialchars($brandName) ?></title>
<link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body class="skin-login">
<div class="login-wrap">
  <div class="login-box forgot-box">
    <div class="login-logo">
      <img src="<?= htmlspecialchars($logoUrl) ?>" alt="" class="forgot-logo">
      <h1>Forgot Password</h1>
    </div>
    <p class="subtitle">Enter your account email to receive a reset link.</p>
    <?php if ($error): ?><div id="toast-data" data-msg="<?= htmlspecialchars($error) ?>" data-type="error" hidden></div><?php endif; ?>
    <?php if ($message): ?><div id="toast-data" data-msg="<?= htmlspecialchars($message) ?>" data-type="success" hidden></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
    </form>
    <p class="login-note"><a href="<?= page_url('login.php') ?>">Back to Login</a></p>
  </div>
</div>
<script src="<?= asset_url('js/app.js') ?>"></script>
</body>
</html>
