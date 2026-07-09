<?php
/**
 * api/folder_stats.php — Get folder statistics (file count, total size, etc.)
 */
require_once __DIR__ . '/../core/auth.php';
header('Content-Type: application/json');

$user = require_login();
$db = get_db();

$folder_id = (int)($_GET['folder_id'] ?? 0);
$action = $_GET['action'] ?? 'summary';

if ($folder_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid folder ID.']);
    exit;
}

// Check access to folder
if (!AccessControl::canViewFolder($db, $user, $folder_id)) {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

switch ($action) {
    case 'summary':
        // Get file count and total size
        $stmt = $db->prepare(
            'SELECT COUNT(DISTINCT d.id) as file_count, COALESCE(SUM(d.size), 0) as total_size
             FROM documents d
             LEFT JOIN document_shares ds ON ds.document_id = d.id AND ds.shared_with_user_id = ?
             WHERE d.folder_id = ? AND d.is_deleted = 0
               AND (d.is_private = 0 OR d.uploaded_by = ? OR ds.shared_with_user_id IS NOT NULL)'
        );
        $stmt->bind_param('iii', $user['id'], $folder_id, $user['id']);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();

        echo json_encode([
            'success' => true,
            'file_count' => (int)$stats['file_count'],
            'total_size' => (int)$stats['total_size'],
            'total_size_formatted' => format_bytes((int)$stats['total_size']),
        ]);
        break;

    case 'files':
        // Get detailed file list with stats
        $limit = (int)($_GET['limit'] ?? 100);
        $offset = (int)($_GET['offset'] ?? 0);

        $stmt = $db->prepare(
            'SELECT d.id, d.filename, d.size, d.uploaded_by, d.created_at, d.is_locked, d.locked_by,
                    u.username AS uploader_name
             FROM documents d
             LEFT JOIN document_shares ds ON ds.document_id = d.id AND ds.shared_with_user_id = ?
             LEFT JOIN users u ON u.id = d.uploaded_by
             WHERE d.folder_id = ? AND d.is_deleted = 0
               AND (d.is_private = 0 OR d.uploaded_by = ? OR ds.shared_with_user_id IS NOT NULL)
             ORDER BY d.filename ASC
             LIMIT ? OFFSET ?'
        );
        $stmt->bind_param('iiiii', $user['id'], $folder_id, $user['id'], $limit, $offset);
        $stmt->execute();
        $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get total count for pagination
        $count_stmt = $db->prepare(
            'SELECT COUNT(DISTINCT d.id) as cnt
             FROM documents d
             LEFT JOIN document_shares ds ON ds.document_id = d.id AND ds.shared_with_user_id = ?
             WHERE d.folder_id = ? AND d.is_deleted = 0
               AND (d.is_private = 0 OR d.uploaded_by = ? OR ds.shared_with_user_id IS NOT NULL)'
        );
        $count_stmt->bind_param('iii', $user['id'], $folder_id, $user['id']);
        $count_stmt->execute();
        $total = (int)$count_stmt->get_result()->fetch_row()[0];

        // Format file sizes
        foreach ($files as &$f) {
            $f['size_formatted'] = format_bytes((int)$f['size']);
        }

        echo json_encode([
            'success' => true,
            'files' => $files,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
        ]);
        break;

    case 'subfolders':
        // Get subfolder stats if parent_id column exists
        $parent_check = $db->query("SHOW COLUMNS FROM folders LIKE 'parent_id'");
        if (!($parent_check && $parent_check->num_rows > 0)) {
            echo json_encode(['success' => false, 'message' => 'Subfolder feature not enabled.']);
            exit;
        }

        $stmt = $db->prepare(
            'SELECT DISTINCT f.id, f.name, f.is_private, f.created_by,
                    COUNT(DISTINCT d.id) as file_count, COALESCE(SUM(d.size), 0) as total_size
             FROM folders f
             LEFT JOIN documents d ON d.folder_id = f.id AND d.is_deleted = 0
             LEFT JOIN folder_shares fs ON fs.folder_id = f.id AND fs.shared_with_user_id = ?
             WHERE f.parent_id = ? 
               AND (f.is_private = 0 OR f.created_by = ? OR fs.shared_with_user_id IS NOT NULL)
             GROUP BY f.id
             ORDER BY f.name ASC'
        );
        $stmt->bind_param('iii', $user['id'], $folder_id, $user['id']);
        $stmt->execute();
        $subfolders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($subfolders as &$sf) {
            $sf['total_size_formatted'] = format_bytes((int)$sf['total_size']);
        }

        echo json_encode([
            'success' => true,
            'subfolders' => $subfolders,
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        exit;
}

function format_bytes(int $bytes): string {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
