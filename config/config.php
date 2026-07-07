<?php
/**
 * Application configuration — paths, database, mail.
 */
define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('LOGO_DIR', APP_ROOT . '/assets/logo');

// Base URL always points to app root (works from admin/, settings/, etc.)
if (!defined('BASE_URL')) {
    $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $appRoot = str_replace('\\', '/', rtrim(realpath(APP_ROOT) ?: APP_ROOT, '/'));
    if ($docRoot && strncmp($appRoot, $docRoot, strlen($docRoot)) === 0) {
        $base = substr($appRoot, strlen($docRoot));
    } else {
        // Fallback: walk up from script until we find the app folder name
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base   = preg_replace('#/(admin|cont|casual|settings|auth|api|partials)(/.*)?$#', '', dirname($script));
    }
    define('BASE_URL', '/' . trim($base, '/') . '/');
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kiwi_dms');

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USER', 'len.10212005@gmail.com');
define('MAIL_PASS', 'ezcc bxnp nhks qyrc');
define('MAIL_FROM', 'len.10212005@gmail.com');
define('MAIL_FROM_NAME', 'FILESTAC DMS');

define('ROWS_PER_PAGE', 10);
define('DEFAULT_BRAND', 'FILESTAC DMS');

function app_url(string $path = ''): string {
    return BASE_URL . ltrim($path, '/');
}

function asset_url(string $path = ''): string {
    return app_url('assets/' . ltrim($path, '/'));
}

function page_url(string $path = ''): string {
    return app_url('pages/' . ltrim($path, '/'));
}

function ensure_schema(): void {
    $db = get_db();
    @$db->query("ALTER TABLE documents ADD COLUMN is_private TINYINT(1) NOT NULL DEFAULT 0");
}
