<?php
require_once __DIR__ . '/../core/auth.php';

Auth::startSession();

if (!empty($_SESSION['user'])) {
    $u = $_SESSION['user'];
    audit_log((int)($u['id'] ?? 0), 'LOGOUT', "User '{$u['username']}' logged out");
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, BASE_URL, $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
header('Location: ' . page_url('login.php?msg=logged_out'));
exit;
