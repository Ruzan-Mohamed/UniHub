<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Sanitization ──────────────────────────────────────────────────────────

function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// ─── Redirect ──────────────────────────────────────────────────────────────

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// ─── Flash Messages ────────────────────────────────────────────────────────

/**
 * Store a one-time flash message in the session.
 * @param string $type  'success' | 'danger' | 'warning' | 'info'
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear the flash message from session.
 * Returns null if none.
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render the flash alert HTML if a flash message exists.
 */
function renderFlash(): void
{
    $flash = getFlash();
    if ($flash) {
        $type    = sanitize($flash['type']);
        $message = sanitize($flash['message']);
        echo <<<HTML
        <div class="alert alert-{$type} alert-dismissible fade show m-3" role="alert">
            {$message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        HTML;
    }
}

// ─── CSRF ──────────────────────────────────────────────────────────────────

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// ─── File Utilities ────────────────────────────────────────────────────────

function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// ─── User Avatar Initials ──────────────────────────────────────────────────

function getInitials(string $firstName, string $lastName): string
{
    return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
}
