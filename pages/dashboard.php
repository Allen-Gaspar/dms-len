<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_login();

switch ($user['role']) {
    case 'admin':
        header('Location: ' . app_url('admin/admin_dashboard.php'));
        exit;
    case 'contributor':
        header('Location: ' . app_url('cont/contributor_dashboard.php'));
        exit;
    default:
        header('Location: ' . app_url('casual/casual_user_dashboard.php'));
        exit;
}
