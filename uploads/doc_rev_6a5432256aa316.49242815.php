<?php
require_once __DIR__ . '/../core/auth.php';
$user = require_role('admin');
$db = get_db();

$limit = 10;
$offset = max(0, (int)($_GET['offset'] ?? 0));
$filter_date = trim($_GET['filter_date'] ?? '');
$filter_role = trim($_GET['filter_role'] ?? '');
$filter_action = trim($_GET['filter_action'] ?? '');
$user_search = trim($_GET['user_search'] ?? '');

$where = [];
$types = '';
$params = [];
if ($filter_date !== '') { $where[] = 'DATE(al.timestamp)=?'; $types .= 's'; $params[] = $filter_date; }
if ($filter_role !== '') { $where[] = 'u.role=?'; $types .= 's'; $params[] = $filter_role; }
if ($filter_action !== '') { $where[] = 'al.action_type=?'; $types .= 's'; $params[] = $filter_action; }
if ($user_search !== '') { $where[] = 'u.username LIKE ?'; $types .= 's'; $params[] = '%' . $user_search . '%'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT al.*, u.username, u.role
        FROM audit_logs al
        LEFT JOIN users u ON al.user_id=u.id
        $whereSql
        ORDER BY al.timestamp DESC
        LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$bindTypes = $types . 'ii';
$bindParams = array_merge($params, [$limit, $offset]);
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
$entries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$countSql = "SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON al.user_id=u.id $whereSql";
$count = $db->prepare($countSql);
if ($types !== '') $count->bind_param($types, ...$params);
$count->execute();
$total = (int)$count->get_result()->fetch_row()[0];

$actionsRes = $db->query("SELECT DISTINCT action_type FROM audit_logs WHERE action_type IS NOT NULL ORDER BY action_type ASC");
$available_actions = $actionsRes ? $actionsRes->fetch_all(MYSQLI_ASSOC) : [];

function audit_params(string $filter_date, string $filter_role, string $filter_action, string $user_search): string {
    return '&filter_date=' . urlencode($filter_date) . '&filter_role=' . urlencode($filter_role) . '&filter_action=' . urlencode($filter_action) . '&user_search=' . urlencode($user_search);
}

$page_title = 'Audit Log';
include __DIR__ . '/../partials/header.php';
?>

<h2 class="page-title">System Audit Logs</h2>

<form method="GET" action="<?= page_url('audit.php') ?>" id="auditHeaderFilters">
  <input type="hidden" name="offset" value="0">
  <div class="table-responsive">
    <table class="data-table audit-table">
      <thead>
        <tr>
          <th class="date-col">Timestamp<br><input class="thead-filter" type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>" onchange="this.form.submit()"></th>
          <th>User Account<br><input class="thead-filter" type="search" name="user_search" value="<?= htmlspecialchars($user_search) ?>" placeholder="Search user" onchange="this.form.submit()"></th>
          <th>System Role<br><select class="thead-filter" name="filter_role" onchange="this.form.submit()"><option value="">All roles</option><option value="admin" <?= $filter_role === 'admin' ? 'selected' : '' ?>>Admin</option><option value="contributor" <?= $filter_role === 'contributor' ? 'selected' : '' ?>>Contributor</option><option value="casual" <?= $filter_role === 'casual' ? 'selected' : '' ?>>Casual</option></select></th>
          <th>Action Type<br><select class="thead-filter" name="filter_action" onchange="this.form.submit()"><option value="">All actions</option><?php foreach ($available_actions as $act): ?><option value="<?= htmlspecialchars($act['action_type']) ?>" <?= $filter_action === $act['action_type'] ? 'selected' : '' ?>><?= htmlspecialchars(str_replace('_', ' ', $act['action_type'])) ?></option><?php endforeach; ?></select></th>
          <th>Activity Description</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($entries)): ?>
        <tr><td colspan="5" class="empty-row">No matching audit records.</td></tr>
      <?php endif; ?>
      <?php foreach ($entries as $log): ?>
        <tr>
          <td class="date-col"><?= htmlspecialchars(date('Y-m-d', strtotime($log['timestamp']))) ?><span class="timestamp-time"><?= htmlspecialchars(date('h:i A', strtotime($log['timestamp']))) ?></span></td>
          <td><?= htmlspecialchars($log['username'] ?? '-') ?></td>
          <td><span class="badge"><?= htmlspecialchars(strtoupper($log['role'] ?? '-')) ?></span></td>
          <td><span class="file-type-pill"><?= htmlspecialchars(str_replace('_', ' ', $log['action_type'])) ?></span></td>
          <td><?= htmlspecialchars($log['description']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<?php if ($total > $limit): ?>
<div class="pagination">
  <?php if ($offset > 0): ?><a href="<?= page_url('audit.php?offset=' . max(0, $offset - $limit) . audit_params($filter_date, $filter_role, $filter_action, $user_search)) ?>" class="btn btn-outline">&larr; Newer Entries</a><?php endif; ?>
  <span class="pagination-info">Showing rows <?= min($total, $offset + 1) ?> to <?= min($total, $offset + $limit) ?> of <?= $total ?></span>
  <?php if (($offset + $limit) < $total): ?><a href="<?= page_url('audit.php?offset=' . ($offset + $limit) . audit_params($filter_date, $filter_role, $filter_action, $user_search)) ?>" class="btn btn-outline">Older Entries &rarr;</a><?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
