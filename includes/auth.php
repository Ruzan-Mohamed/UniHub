<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Build absolute URL to a project-root-relative path ───────────────────
function siteUrl(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

// ─── Check if student is logged in ─────────────────────────────────────────

function isStudentLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'student';
}

// ─── Check if admin/staff is logged in ─────────────────────────────────────

function isAdminLoggedIn(): bool
{
    return isset($_SESSION['user_id']) &&
           in_array($_SESSION['role'], ['admin', 'staff'], true);
}

// ─── Require student auth (redirect if not) ────────────────────────────────

function requireStudentLogin(): void
{
    if (!isStudentLoggedIn()) {
        setFlash('warning', 'Please log in to access the student portal.');
        redirect(siteUrl('/student/login.php'));
    }
}

// ─── Require admin auth (redirect if not) ──────────────────────────────────

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        setFlash('warning', 'Authorized personnel only. Please log in.');
        redirect(siteUrl('/admin/login.php'));
    }
}

// ─── Get the currently logged-in user's session data ──────────────────────

function getCurrentUser(): array
{
    return [
        'id'         => $_SESSION['user_id']   ?? null,
        'role'       => $_SESSION['role']       ?? null,
        'first_name' => $_SESSION['first_name'] ?? 'User',
        'last_name'  => $_SESSION['last_name']  ?? '',
        'email'      => $_SESSION['email']      ?? '',
        'student_id' => $_SESSION['student_id'] ?? '',
    ];
}

// ─── Destroy session and log out ───────────────────────────────────────────

function destroySession(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
