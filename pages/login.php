<?php
require_once __DIR__ . '/../bootstrap/init.php';
require_once __DIR__ . '/../core/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_URL,
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}


$error = '';
$message = '';

// Capture the logout message if passed via the URL query string
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $message = 'You have been successfully logged out.';
}

// AUTO-LOGOUT: If a session exists, clear everything immediately instead of redirecting to the dashboard
if (!empty($_SESSION['user'])) {
    $user     = $_SESSION['user'];
    $user_id  = isset($user['id']) ? (int) $user['id'] : null;
    $username = $user['username'] ?? 'Unknown';
    
    if (function_exists('audit_log')) {
        audit_log($user_id, 'LOGOUT', "User '{$username}' auto-logged out by visiting login page");
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    
    // FIX: Redirects back to itself using SCRIPT_NAME to prevent nested folder 404 path breaks
    header('Location: ' . page_url('login.php?msg=logged_out'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $db   = get_db();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        $isValidPassword = password_verify($password, $user['password_hash'] ?? '');

        if (!$user || !$isValidPassword) {
            $error = 'Invalid username or password.';
        } elseif (($user['status'] ?? '') === 'frozen') {
            $error = 'Your account has been frozen. Contact an administrator.';
        } else {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'       => (int) $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role'],
                'status'   => $user['status'],
            ];
            audit_log((int) $user['id'], 'LOGIN', "User '{$user['username']}' logged in");
            header('Location: ' . page_url('dashboard.php'));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login</title>
<link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
<script src="<?= asset_url('js/app.js') ?>"></script>
<script>
    // Append an endless loop state to the active window thread history tracker
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        // If the user tries to click backward or forward, forcefully trap them right here on the login page
        window.history.pushState(null, "", window.location.href);
    };
</script>
</head>
<body class="skin-login">
<!-- HEADER OVERRIDE: Reduced padding down to 8px top/bottom to make the navbar smaller -->
<header class="landing-header" style="padding-top: 19px; padding-bottom: 18px; min-height: unset; display: flex; align-items: center; justify-content: space-between;">
    <div class="landing-brand" style="display: flex; align-items: center; gap: 10px;">
        <!-- CLICKABLE LOGO PREVIEW TRIGGER -->
        <a href="javascript:location.reload()" style="text-decoration: none; display: flex; align-items: center;" title="Refresh">
            <img src="<?= asset_url('logo/default.png') ?>" alt="Logo" class="filestac-icon" style="width: 32px; height: 32px; object-fit: contain; display: block;">
        </a>
        <span style="font-weight: bold; font-size: 18px; letter-spacing: 0.5px;">FILESTAC DMS</span>
    </div>
    <nav class="landing-nav" style="display: flex; align-items: center;">
        <a href="<?= app_url() ?>" class="btn btn-outline" style="padding-top: 5px; padding-bottom: 5px; font-size: 13px;">Home</a>
    </nav>
</header>


<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo" style="text-align: center; margin-bottom: 25px; font-family: sans-serif;">
    <!-- LOGO FIX: Non-clickable static local image layout -->
    <img src="<?= asset_url('logo/default.png') ?>" alt="Logo" class="filestac-icon" style="width: 85px; height: 85px; object-fit: contain; display: block; margin: 0 auto 15px auto;">
    
    <h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #333; letter-spacing: 0.5px;">FILESTAC DMS</h1>
    <p class="subtitle" style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Document Management System</p>
</div>

    
    <?php if ($error): ?><div id="toast-data" data-msg="<?= htmlspecialchars($error) ?>" data-type="error" hidden></div><?php endif; ?>
    <?php if ($message): ?><div id="toast-data" data-msg="<?= htmlspecialchars($message) ?>" data-type="success" hidden></div><?php endif; ?>
    <?php if (!empty($_GET['ok'])): ?><div id="toast-data" data-msg="<?= htmlspecialchars(urldecode($_GET['ok'])) ?>" data-type="success" hidden></div><?php endif; ?>

    <!-- FIX: Updated action attribute to dynamically point to the correct subfolder location -->
    <form method="POST" action="<?= htmlspecialchars($_SERVER['SCRIPT_NAME']) ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top: 10px;">Login</button>
    </form>
    <p class="login-note"><a href="<?= app_url('auth/forgot_password.php') ?>">Forgot password?</a></p>
    <p class="login-note">No self-registration. Fill out the contact us form first.</p>
  </div>
</div>
</body>
</html>
