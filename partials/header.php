<?php
if (!isset($user)) $user = current_user();
$role = $user['role'] ?? 'casual';
$theme = $user['theme'] ?? 'light';

$settingsObj = new Settings();
$orgAdminId = Settings::getOrgAdminId($user);
$brand = $settingsObj->getBrand($orgAdminId);
$logoUrl = $settingsObj->getLogoUrl($orgAdminId);
$brandName = $brand['brand_name'] ?? DEFAULT_BRAND;

$notifObj = new Notification();
$unreadCount = $notifObj->countUnread((int)$user['id']);
$notifications = $notifObj->getUnread((int)$user['id'], 8);

$toast = toast_from_get();
$current_file = basename($_SERVER['PHP_SELF']);
$is_dashboard_active = in_array($current_file, ['dashboard.php', 'admin_dashboard.php', 'contributor_dashboard.php', 'casual_user_dashboard.php'], true);
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= htmlspecialchars($theme) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title ?? $brandName) ?> — <?= htmlspecialchars($brandName) ?></title>
<link rel="stylesheet" href="<?= asset_url('css/bootstrap.css') ?>">
<link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
<script>window.DMS_BASE = '<?= BASE_URL ?>';</script>
<style>
  /* Critical fallback so layout works even if CSS is slow to load */
  .modal-overlay, .loading-overlay { display: none !important; }
  .modal-overlay.is-active { display: flex !important; }
  body.dms-loading .loading-overlay { display: flex !important; }
  .app-layout { display: flex; min-height: 100vh; }
  .sidebar { width: 230px; min-width: 230px; flex-shrink: 0; }
  .main-content { flex: 1; min-width: 0; display: flex; flex-direction: column; }
</style>
</head>
<body class="skin-<?= htmlspecialchars($role) ?> theme-<?= htmlspecialchars($theme) ?> dms-loading">

<div id="page-loading-overlay" class="loading-overlay">
    <div class="folder-loader">
        <div class="folder-icon">📁</div>
        <div class="file-icons"><span>📄</span><span>📄</span><span>📄</span></div>
        <p>Loading files...</p>
    </div>
</div>

<?php if ($toast): ?>
<div id="toast-data" data-msg="<?= htmlspecialchars($toast['msg']) ?>" data-type="<?= htmlspecialchars($toast['type']) ?>" hidden></div>
<?php endif; ?>

<div id="dmsConfirmModal" class="modal-overlay">
    <div class="modal-card modal-sm">
        <h3 id="dmsConfirmTitle">Confirm</h3>
        <p id="dmsConfirmMsg"></p>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="DMS.closeModal('dmsConfirmModal')">Cancel</button>
            <button type="button" id="dmsConfirmBtn" class="btn btn-primary">Confirm</button>
        </div>
    </div>
</div>

<div class="app-layout">
    <aside id="dashboard-sidebar" class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-brand-content">
                <a href="javascript:location.reload()" title="Refresh" class="logo-link">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" class="sidebar-logo-img">
                </a>
                <span class="sidebar-brand-text"><?= htmlspecialchars($brandName) ?></span>
            </div>
            <button id="sidebar-toggle-btn" class="sidebar-toggle" aria-label="Toggle menu">
                <svg id="burger-svg-icon" class="icon-20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="close-svg-icon" class="icon-20" style="display:none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= page_url('dashboard.php') ?>" class="nav-item <?= $is_dashboard_active ? 'active' : '' ?>"><span class="nav-icon">⌂</span><span class="nav-label">Dashboard</span></a>
            <a href="<?= page_url('documents.php') ?>" class="nav-item <?= $current_file === 'documents.php' ? 'active' : '' ?>"><span class="nav-icon">▣</span><span class="nav-label">Files</span></a>
            <a href="<?= page_url('private.php') ?>" class="nav-item <?= $current_file === 'private.php' ? 'active' : '' ?>"><span class="nav-icon">◇</span><span class="nav-label">Private</span></a>
            <a href="<?= page_url('folders.php') ?>" class="nav-item <?= $current_file === 'folders.php' ? 'active' : '' ?>"><span class="nav-icon">□</span><span class="nav-label">Folders</span></a>

            <?php if ($role === 'admin'): ?>
                <a href="<?= page_url('users.php') ?>" class="nav-item <?= in_array($current_file, ['users.php','user_detail.php']) ? 'active' : '' ?>"><span class="nav-icon">◉</span><span class="nav-label">Users</span></a>
                <a href="<?= page_url('trash.php') ?>" class="nav-item <?= $current_file === 'trash.php' ? 'active' : '' ?>"><span class="nav-icon">⌫</span><span class="nav-label">Trash</span></a>
                <a href="<?= page_url('audit.php') ?>" class="nav-item <?= $current_file === 'audit.php' ? 'active' : '' ?>"><span class="nav-icon">≡</span><span class="nav-label">Audit Log</span></a>
                <a href="<?= app_url('admin/admin_approvals.php') ?>" class="nav-item <?= $current_file === 'admin_approvals.php' ? 'active' : '' ?>"><span class="nav-icon">✓</span><span class="nav-label">Approvals</span></a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= app_url('settings/settings.php') ?>" class="nav-item settings-link <?= $current_file === 'settings.php' ? 'active' : '' ?>">
                ⚙ Settings
            </a>
            <div class="user-pill">
                <span class="user-role-dot"></span>
                <div>
                    <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="user-role"><?= ucfirst($role) ?></div>
                </div>
            </div>
            <button type="button" class="btn btn-outline btn-sm btn-full" onclick="DMS.confirm('Logout','Do you want to sign out now?', ()=>location.href='<?= page_url('logout.php') ?>')">Logout</button>
        </div>
    </aside>

    <div class="main-content">
        <header class="top-bar" id="top-bar">
            <h1 class="top-bar-title"><?= htmlspecialchars($page_title ?? '') ?></h1>
            <div class="top-bar-actions">
                <div class="notif-wrap">
                    <button id="notif-btn" class="notif-btn" title="Notifications" aria-label="Notifications">
                        <svg class="notif-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <?php if ($unreadCount > 0): ?><span class="notif-badge"><?= $unreadCount ?></span><?php endif; ?>
                    </button>
                    <div id="notif-panel" class="notif-panel">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notif-count-pill"><?= $unreadCount ?> unread</span>
                            <?php endif; ?>
                        </div>
                        <?php if (empty($notifications)): ?>
                            <div class="notif-empty">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:6px;opacity:.4"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                <span>No new notifications</span>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $n):
                                $link = $n['link'] ?? '';
                                $hasLink = $link && $link !== '#';
                            ?>
                                <a href="<?= $hasLink ? htmlspecialchars($link) : '#' ?>"
                                   class="notif-item notif-unread"
                                   onclick="markNotifRead(<?= (int)$n['id'] ?>)<?= !$hasLink ? '; return false;' : '' ?>">
                                    <div class="notif-item-dot"></div>
                                    <div class="notif-item-body">
                                        <strong class="notif-type-label"><?= htmlspecialchars($n['type']) ?></strong>
                                        <span class="notif-msg"><?= htmlspecialchars($n['message']) ?></span>
                                        <small class="notif-time"><?= htmlspecialchars(substr($n['created_at'], 0, 16)) ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" id="notif-mark-all-btn" class="notif-mark-all">Mark all as read</button>
                    </div>
                </div>
            </div>
        </header>
        <div class="page-body">
