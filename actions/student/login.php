<?php
// actions/student/login.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/login.php'));
}

// CSRF check
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/student/login.php'));
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']);

if (empty($email) || empty($password)) {
    setFlash('danger', 'Please enter your email and password.');
    redirect(siteUrl('/student/login.php'));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "student" LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        setFlash('danger', 'Invalid email or password. Please try again.');
        redirect(siteUrl('/student/login.php'));
    }

    if (!$user['is_active']) {
        setFlash('warning', 'Your account has been suspended. Please contact the administrator.');
        redirect(siteUrl('/student/login.php'));
    }

    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['student_id'] = $user['student_id'];

    if ($remember) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            time() + (86400 * 30),
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    setFlash('success', 'Welcome back, ' . sanitize($user['first_name']) . '!');
    redirect(siteUrl('/student/dashboard.php'));

} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again later.');
    redirect(siteUrl('/student/login.php'));
}
