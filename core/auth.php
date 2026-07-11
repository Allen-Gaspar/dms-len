<?php
require_once __DIR__ . '/../bootstrap/init.php';

class Auth {
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => BASE_URL,
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function requireLogin(): array {
        self::startSession();
        if (empty($_SESSION['user'])) {
            header('Location: ' . page_url('login.php'));
            exit;
        }
        return $_SESSION['user'];
    }

    public static function currentUser(): ?array {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    public static function requireRole(string ...$roles): array {
        $user = self::requireLogin();
        if (!$user || !in_array($user['role'], $roles, true)) {
            header('Location: ' . page_url('login.php'));
            exit;
        }
        return $user;
    }

    public static function refreshSession(int $userId): void {
        self::startSession();
        $db = get_db();
        $stmt = $db->prepare('SELECT id, username, first_name, last_name, email, role, status, theme FROM users WHERE id=?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        if ($u) {
            $_SESSION['user'] = [
                'id' => (int)$u['id'], 'username' => $u['username'],
                'first_name' => $u['first_name'] ?? '', 'last_name' => $u['last_name'] ?? '',
                'email' => $u['email'], 'role' => $u['role'],
                'status' => $u['status'], 'theme' => $u['theme'] ?? 'light',
            ];
        }
    }
}

/**
 * Access Control Helpers
 */
class AccessControl {
    /**
     * Check if user can edit a document
     */
    public static function canEditDocument(mysqli $db, array $user, int $documentId): bool {
        $stmt = $db->prepare(
            'SELECT d.uploaded_by, d.is_private, d.folder_id
             FROM documents d
             WHERE d.id = ? AND d.is_deleted = 0 LIMIT 1'
        );
        $stmt->bind_param('i', $documentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();

        if (!$doc) return false;

        // Owner can edit
        if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
        if ((int)$doc['is_private'] === 0 && $user['role'] === 'admin') return true;

        // Check folder permissions if document is in a folder
        if (!empty($doc['folder_id'])) {
            $fstmt = $db->prepare(
                'SELECT fs.can_edit FROM folder_shares fs
                 WHERE fs.folder_id = ? AND fs.shared_with_user_id = ? LIMIT 1'
            );
            $fstmt->bind_param('ii', $doc['folder_id'], $user['id']);
            $fstmt->execute();
            $perm = $fstmt->get_result()->fetch_assoc();
            if ($perm && (int)$perm['can_edit'] === 1) return true;
        }

        $pstmt = $db->prepare(
            'SELECT can_edit FROM document_shares 
             WHERE document_id = ? AND shared_with_user_id = ? LIMIT 1'
        );
        $pstmt->bind_param('ii', $documentId, $user['id']);
        $pstmt->execute();
        $share = $pstmt->get_result()->fetch_assoc();
        if ($share && (int)$share['can_edit'] === 1) return true;

        return false;
    }

    /**
     * Check if user can delete a document
     */
    public static function canDeleteDocument(mysqli $db, array $user, int $documentId): bool {
        $stmt = $db->prepare(
            'SELECT d.uploaded_by, d.folder_id FROM documents d
             WHERE d.id = ? AND d.is_deleted = 0 LIMIT 1'
        );
        $stmt->bind_param('i', $documentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();

        if (!$doc) return false;

        if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
        if ($user['role'] === 'admin') {
            $privStmt = $db->prepare('SELECT is_private FROM documents WHERE id=? LIMIT 1');
            $privStmt->bind_param('i', $documentId);
            $privStmt->execute();
            $priv = $privStmt->get_result()->fetch_assoc();
            if ($priv && (int)$priv['is_private'] === 0) return true;
        }

        if (!empty($doc['folder_id'])) {
            $fstmt = $db->prepare(
                'SELECT can_delete FROM folder_shares
                 WHERE folder_id = ? AND shared_with_user_id = ? LIMIT 1'
            );
            $fstmt->bind_param('ii', $doc['folder_id'], $user['id']);
            $fstmt->execute();
            $perm = $fstmt->get_result()->fetch_assoc();
            if ($perm && (int)$perm['can_delete'] === 1) return true;
        }

        $pstmt = $db->prepare(
            'SELECT can_delete FROM document_shares
             WHERE document_id = ? AND shared_with_user_id = ? LIMIT 1'
        );
        $pstmt->bind_param('ii', $documentId, $user['id']);
        $pstmt->execute();
        $share = $pstmt->get_result()->fetch_assoc();
        if ($share && (int)$share['can_delete'] === 1) return true;

        return false;
    }

    /**
     * Check if user can view a document
     */
    public static function canViewDocument(mysqli $db, array $user, int $documentId): bool {
        $stmt = $db->prepare(
            'SELECT d.uploaded_by, d.is_private, d.folder_id FROM documents d
             WHERE d.id = ? AND d.is_deleted = 0 LIMIT 1'
        );
        $stmt->bind_param('i', $documentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();

        if (!$doc) return false;

        if ((int)$doc['uploaded_by'] === (int)$user['id']) return true;
        if ((int)$doc['is_private'] === 0) return true;

        if (!empty($doc['folder_id'])) {
            $fstmt = $db->prepare(
                'SELECT id FROM folder_shares
                 WHERE folder_id = ? AND shared_with_user_id = ? LIMIT 1'
            );
            $fstmt->bind_param('ii', $doc['folder_id'], $user['id']);
            $fstmt->execute();
            if ($fstmt->get_result()->fetch_assoc()) return true;
        }

        $pstmt = $db->prepare(
            'SELECT id FROM document_shares
             WHERE document_id = ? AND shared_with_user_id = ? LIMIT 1'
        );
        $pstmt->bind_param('ii', $documentId, $user['id']);
        $pstmt->execute();
        if ($pstmt->get_result()->fetch_assoc()) return true;

        return false;
    }

    /**
     * Check if user can manage folder (edit, share, etc.)
     */
    public static function canManageFolder(mysqli $db, array $user, int $folderId): bool {
        $stmt = $db->prepare('SELECT created_by FROM folders WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $folderId);
        $stmt->execute();
        $folder = $stmt->get_result()->fetch_assoc();

        if (!$folder) return false;
        if ($user['role'] === 'admin') return true;
        return (int)$folder['created_by'] === (int)$user['id'];
    }

    /**
     * Check if user can access folder
     */
    public static function canViewFolder(mysqli $db, array $user, int $folderId): bool {
        $stmt = $db->prepare(
            'SELECT f.created_by, f.is_private FROM folders f
             WHERE f.id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $folderId);
        $stmt->execute();
        $folder = $stmt->get_result()->fetch_assoc();

        if (!$folder) return false;

        if ($user['role'] === 'admin') return true;
        if ((int)$folder['created_by'] === (int)$user['id']) return true;
        if ((int)$folder['is_private'] === 0) return true;

        $stmt = $db->prepare(
            'SELECT id FROM folder_shares
             WHERE folder_id = ? AND shared_with_user_id = ? LIMIT 1'
        );
        $stmt->bind_param('ii', $folderId, $user['id']);
        $stmt->execute();
        return (bool)$stmt->get_result()->fetch_assoc();
    }
}

function require_login(): array { return Auth::requireLogin(); }
function current_user(): ?array { return Auth::currentUser(); }
function require_role(string ...$roles): array { return Auth::requireRole(...$roles); }
