<?php
require_once __DIR__ . '/../core/auth.php';

$user = current_user();
if (!$user) {
    header('Location: ' . page_url('login.php'));
    exit;
}

$notif = new Notification();
$action = $_GET['action'] ?? '';

if ($action === 'mark_all') {
    $notif->markAllRead((int)$user['id']);
    flash_redirect($_SERVER['HTTP_REFERER'] ?? page_url('dashboard.php'), 'ok', 'All notifications marked read.');
}

if ($action === 'mark' && isset($_GET['id'])) {
    $notif->markRead((int)$user['id'], (int)$_GET['id']);
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

header('Content-Type: application/json');
$notifications = $notif->getUnread((int)$user['id'], 20);
echo json_encode(['count' => $notif->countUnread((int)$user['id']), 'items' => $notifications]);
