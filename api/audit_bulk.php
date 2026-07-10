<?php
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_role('admin');
$db = get_db();
$action = $_GET['action'] ?? '';

if ($action === 'page_ids') {
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

    $sql = "SELECT al.id FROM audit_logs al LEFT JOIN users u ON al.user_id=u.id $whereSql ORDER BY al.timestamp DESC LIMIT ? OFFSET ?";
    $stmt = $db->prepare($sql);
    $bindTypes = $types . 'ii';
    $bindParams = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($bindTypes, ...$bindParams);
    $stmt->execute();
    echo json_encode(['success' => true, 'ids' => array_map('intval', array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'id'))]);
    exit;
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $ids = array_values(array_filter(array_map('intval', $input['ids'] ?? [])));
    if (!$ids) {
        echo json_encode(['success' => false, 'message' => 'Select at least one audit log.']);
        exit;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $db->prepare("DELETE FROM audit_logs WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => count($ids) . ' audit log(s) deleted.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
