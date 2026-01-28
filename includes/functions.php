<?php
// includes/functions.php

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// PHP 7 compatibility (if needed)
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function redirect(string $path): void {
    header("Location: " . url($path));
    exit;
}

/**
 * Base URL for this app (supports running inside a subfolder and inside /admin).
 * Examples:
 *  - / -> ''
 *  - /blood -> '/blood'
 *  - /blood/admin -> '/blood'
 */
function app_base_url(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    // If current page is under /admin, strip it.
    if (substr($dir, -6) === '/admin') {
        $dir = substr($dir, 0, -6);
    }
    return $dir;
}

/**
 * Build a URL relative to the application root.
 * - If $path starts with http(s) or '/', it will be returned as-is.
 * - Otherwise it will be prefixed with app_base_url().
 */
function url(string $path): string {
    $path = trim($path);
    if ($path === '') return app_base_url() ?: '/';
    if (preg_match('~^https?://~i', $path)) return $path;
    if (str_starts_with($path, '/')) return $path;

    $base = app_base_url();
    if ($base === '') return $path;
    return $base . '/' . $path;
}

function flash_set(string $key, string $message, string $type = 'info'): void {
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function flash_get(string $key): ?array {
    if (!isset($_SESSION['flash'][$key])) return null;
    $v = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $v;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_verify(): void {
    $token = $_POST['csrf'] ?? '';
    if (!$token || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(400);
        echo "Invalid CSRF token.";
        exit;
    }
}

function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!auth_user()) {
        flash_set('auth', 'Please login first.', 'warning');
        redirect('login.php');
    }
}

/**
 * Safe prepared query helper (mysqli).
 * @return mysqli_stmt
 */
function db_prepare_execute(mysqli $mysqli, string $sql, string $types = '', array $params = []): mysqli_stmt {
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo "DB prepare failed.";
        exit;
    }
    if ($types !== '' && !empty($params)) {
        // bind_param requires references
        $bind = [];
        $bind[] = $types;
        foreach ($params as $k => $v) {
            $bind[] = &$params[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind);
    }
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "DB execute failed.";
        exit;
    }
    return $stmt;
}

function blood_compatible_types(string $recipientType): array {
    $t = strtoupper(trim($recipientType));
    // Compatibility mapping (recipient -> allowed donor types)
    $map = [
        'O-'  => ['O-'],
        'O+'  => ['O-', 'O+'],
        'A-'  => ['O-', 'A-'],
        'A+'  => ['O-', 'O+', 'A-', 'A+'],
        'B-'  => ['O-', 'B-'],
        'B+'  => ['O-', 'O+', 'B-', 'B+'],
        'AB-' => ['O-', 'A-', 'B-', 'AB-'],
        'AB+' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
    ];
    return $map[$t] ?? [];
}

function valid_blood_type(string $t): bool {
    $allowed = ['O-','O+','A-','A+','B-','B+','AB-','AB+'];
    return in_array(strtoupper(trim($t)), $allowed, true);
}

function normalize_phone(string $p): string {
    $p = trim($p);
    // keep digits, +, spaces, hyphen
    return preg_replace('/[^0-9\+\-\s]/', '', $p) ?? $p;
}

/**
 * Theme palette + page icon helpers (used by the shared Page Header).
 * We keep these values server-side so every page automatically gets a consistent look.
 */
function theme_palette(): array {
    return [
        // Classic blood-red
        'home'     => ['accent' => '#c62828', 'dark' => '#a61f1f', 'rgb' => '198,40,40'],
        // Deep red for donating / adding donors
        'donate'   => ['accent' => '#b71c1c', 'dark' => '#8e1616', 'rgb' => '183,28,28'],
        // Rose for matching (search)
        'match'    => ['accent' => '#ad1457', 'dark' => '#880e4f', 'rgb' => '173,20,87'],
        // Orange-red for creating a request
        'request'  => ['accent' => '#e53935', 'dark' => '#b71c1c', 'rgb' => '229,57,53'],
        // Deep orange for listing requests
        'requests' => ['accent' => '#d84315', 'dark' => '#bf360c', 'rgb' => '216,67,21'],
        // Indigo-ish for accounts
        'account'  => ['accent' => '#283593', 'dark' => '#1a237e', 'rgb' => '40,53,147'],
        // Blue-grey for admin
        'admin'    => ['accent' => '#37474f', 'dark' => '#263238', 'rgb' => '55,71,79'],
    ];
}

function theme_style(string $theme): string {
    $pal = theme_palette();
    $t = $pal[$theme] ?? $pal['home'];
    return "--accent:{$t['accent']};--accent-dark:{$t['dark']};--accent-rgb:{$t['rgb']};";
}

function current_script_path(): string {
    return str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
}

function current_script_basename(): string {
    $p = current_script_path();
    return strtolower(basename($p));
}

function page_theme_auto(?string $badge = null): string {
    $badge = strtolower(trim((string)$badge));
    $path  = current_script_path();
    $file  = current_script_basename();

    if (strpos($path, '/admin/') !== false) return 'admin';

    // Prefer explicit badge categories when available
    if ($badge === 'donate') return 'donate';
    if ($badge === 'match') return 'match';
    if ($badge === 'request') return 'request';
    if ($badge === 'requests') return 'requests';
    if ($badge === 'account') return 'account';
    if ($badge === 'admin') return 'admin';

    // Fallback: file-based
    if ($file === 'donor_add.php') return 'donate';
    if ($file === 'match.php') return 'match';
    if ($file === 'request_add.php') return 'request';
    if ($file === 'requests_list.php') return 'requests';
    if ($file === 'login.php' || $file === 'register.php') return 'account';
    return 'home';
}

function page_icon_auto(string $theme, ?string $badge = null): string {
    $badge = strtolower(trim((string)$badge));
    $path  = current_script_path();
    $file  = current_script_basename();

    if (strpos($path, '/admin/') !== false) {
        // Admin: choose based on file
        if ($file === 'requests.php') return 'clipboard';
        return 'shield';
    }

    // Badge-driven
    if ($badge === 'donate') return 'drop';
    if ($badge === 'match') return 'search';
    if ($badge === 'request') return 'plus';
    if ($badge === 'requests') return 'clipboard';
    if ($badge === 'account') return 'lock';

    // Theme-driven
    if ($theme === 'donate') return 'drop';
    if ($theme === 'match') return 'search';
    if ($theme === 'request') return 'plus';
    if ($theme === 'requests') return 'clipboard';
    if ($theme === 'account') return 'lock';
    if ($theme === 'admin') return 'shield';
    return 'heartbeat';
}

function page_icon_svg(string $name, int $size = 16): string {
    $s = max(12, min(28, $size));
    $common = 'xmlns="http://www.w3.org/2000/svg" width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

    // Simple, consistent stroke icons (currentColor)
    switch ($name) {
        case 'drop':
            return "<svg $common><path d=\"M12 2s6 7 6 11a6 6 0 1 1-12 0c0-4 6-11 6-11z\"/></svg>";
        case 'search':
            return "<svg $common><circle cx=\"11\" cy=\"11\" r=\"7\"/><path d=\"M21 21l-4.3-4.3\"/></svg>";
        case 'clipboard':
            return "<svg $common><rect x=\"9\" y=\"2\" width=\"6\" height=\"4\" rx=\"1\"/><path d=\"M9 4H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2\"/><path d=\"M8 11h8\"/><path d=\"M8 15h8\"/><path d=\"M8 19h5\"/></svg>";
        case 'plus':
            return "<svg $common><circle cx=\"12\" cy=\"12\" r=\"9\"/><path d=\"M12 8v8\"/><path d=\"M8 12h8\"/></svg>";
        case 'lock':
            return "<svg $common><rect x=\"6\" y=\"11\" width=\"12\" height=\"10\" rx=\"2\"/><path d=\"M8 11V8a4 4 0 0 1 8 0v3\"/></svg>";
        case 'shield':
            return "<svg $common><path d=\"M12 2l8 4v6c0 5-3.4 9.4-8 10-4.6-.6-8-5-8-10V6l8-4z\"/><path d=\"M9 12l2 2 4-4\"/></svg>";
        case 'heartbeat':
        default:
            return "<svg $common><path d=\"M20 8c0-2.8-2.2-5-5-5-1.6 0-3 .7-4 1.9C10 3.7 8.6 3 7 3 4.2 3 2 5.2 2 8c0 6 10 13 10 13s10-7 10-13z\"/><path d=\"M3.5 12h4l2-4 2.2 6 1.6-3h5.2\"/></svg>";
    }
}
?>
