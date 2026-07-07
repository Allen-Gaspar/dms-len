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
        $stmt = $db->prepare('SELECT id, username, email, role, status, theme FROM users WHERE id=?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        if ($u) {
            $_SESSION['user'] = [
                'id' => (int)$u['id'], 'username' => $u['username'],
                'email' => $u['email'], 'role' => $u['role'],
                'status' => $u['status'], 'theme' => $u['theme'] ?? 'light',
            ];
        }
    }
}

function require_login(): array { return Auth::requireLogin(); }
function current_user(): ?array { return Auth::currentUser(); }
function require_role(string ...$roles): array { return Auth::requireRole(...$roles); }
