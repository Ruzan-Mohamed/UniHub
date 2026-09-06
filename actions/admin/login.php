<?php
// actions/admin/login.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/admin/login.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/admin/login.php'));
}

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';

if (empty($email) || empty($password)) {
    setFlash('danger', 'Please enter your email and password.');
    redirect(siteUrl('/admin/login.php'));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'SELECT * FROM users WHERE email = ? AND role IN ("admin","staff") LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        setFlash('danger', 'Invalid credentials. Access denied.');
        redirect(siteUrl('/admin/login.php'));
    }

    if (!$user['is_active']) {
        setFlash('warning', 'This account is inactive. Please contact the system administrator.');
        redirect(siteUrl('/admin/login.php'));
    }

    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['email']      = $user['email'];

    setFlash('success', 'Secure login successful. Welcome, ' . sanitize($user['first_name']) . '.');
    redirect(siteUrl('/admin/dashboard.php'));

} catch (PDOException $e) {
    error_log('Admin login error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again later.');
    redirect(siteUrl('/admin/login.php'));
}
