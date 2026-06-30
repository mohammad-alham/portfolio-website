<?php
/**
 * Common Functions: Security, Sanitization, CSRF, Sessions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Session Security ────────────────────────────────────────────

/**
 * Regenerate session ID to prevent fixation
 */
function sessionRegenerate(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Set a flash message in session
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ─── CSRF Protection ──────────────────────────────────────────────

/**
 * Generate and store a CSRF token in session
 */
function generateCsrfToken(): string {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

/**
 * Get the current CSRF token (generate if not exists)
 */
function getCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        return generateCsrfToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST data
 */
function validateCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check CSRF token and redirect with error if invalid
 */
function checkCsrfToken(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        setFlash('error', 'Security token validation failed. Please try again.');
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit;
    }
}

/**
 * Render a hidden CSRF token input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . getCsrfToken() . '">';
}

// ─── XSS & Input Sanitization ──────────────────────────────────

/**
 * Sanitize output for HTML (prevents XSS)
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize a string for storage (trim + strip tags)
 */
function sanitize(?string $value): string {
    return trim(strip_tags($value ?? ''));
}

/**
 * Validate email format
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL format
 */
function isValidUrl(string $url): bool {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// ─── Authentication ─────────────────────────────────────────────

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    return $_SESSION['user_data'] ?? null;
}

/**
 * Require authentication - redirect if not logged in
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: admin/login.php');
        exit;
    }
}

// ─── File Upload ────────────────────────────────────────────────

/**
 * Handle file upload with validation
 */
function uploadFile(array $file, string $targetDir, array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'pdf']): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        setFlash('error', 'File type not allowed. Allowed: ' . implode(', ', $allowedTypes));
        return null;
    }

    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        setFlash('error', 'File too large. Maximum size: 5MB');
        return null;
    }

    // Ensure target directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Resolve to absolute path without ../
    $targetDir = realpath($targetDir) ?: $targetDir;

    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = rtrim($targetDir, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Convert absolute path to web-relative URL for browser access
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $webPath = str_replace('\\', '/', $filepath);
        if (strpos($webPath, $docRoot) === 0) {
            return ltrim(substr($webPath, strlen($docRoot)), '/');
        }
        // Fallback: return filename relative to target dir
        return $filename;
    }

    setFlash('error', 'Failed to upload file.');
    return null;
}

/**
 * Convert a stored file path to a browser-accessible URL.
 * Handles both old absolute paths (C:\xampp\...) and new relative paths.
 */
function assetUrl(?string $path): string {
    if (empty($path)) return '';
    $path = str_replace('\\', '/', $path);
    // Absolute URL (http://, https://, //)
    if (preg_match('#^(https?:)?//#i', $path)) return $path;
    // Already absolute from domain root
    if ($path[0] === '/') return $path;
    // Drive-letter absolute path (e.g. C:/xampp/...)
    if (preg_match('#^[a-zA-Z]:/#', $path)) {
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        if (strpos($path, $docRoot) === 0) {
            return '/' . ltrim(substr($path, strlen($docRoot)), '/');
        }
        return 'assets/uploads/' . basename($path);
    }
    // Relative path — make absolute from domain root
    return '/' . $path;
}

// ─── String Helpers ──────────────────────────────────────────────

/**
 * Generate URL-friendly slug
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Truncate text to a certain length
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length - mb_strlen($suffix)) . $suffix;
}

/**
 * Format date for display
 */
function formatDate($date, string $format = 'M Y'): string {
    if (!$date || $date === '0000-00-00') return 'Present';
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : 'Present';
}

/**
 * Get profile data
 */
function getProfile(): ?array {
    return dbRow("SELECT p.*, u.full_name, u.email, u.profile_image 
                  FROM profile p 
                  JOIN users u ON u.id = p.user_id 
                  LIMIT 1");
}

/**
 * Get site settings
 */
function getSiteSettings(): ?array {
    return dbRow("SELECT * FROM site_settings LIMIT 1");
}

/**
 * Get all skills grouped by category
 */
function getSkillsByCategory(): array {
    $skills = dbQuery("SELECT * FROM skills ORDER BY display_order ASC");
    $grouped = [];
    foreach ($skills as $skill) {
        $grouped[$skill['category']][] = $skill;
    }
    return $grouped;
}

/**
 * Get featured projects
 */
function getFeaturedProjects(int $limit = 6): array {
    return dbQuery("SELECT * FROM projects WHERE featured = 1 ORDER BY created_at DESC LIMIT ?", [$limit]);
}

/**
 * Get unread message count
 */
function getUnreadMessageCount(): int {
    $row = dbRow("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
    return $row ? (int)$row['count'] : 0;
}
