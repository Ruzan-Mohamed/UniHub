<?php
// actions/student/register.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/register.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/student/register.php'));
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name']  ?? '');
$email     = trim($_POST['email']      ?? '');
$studentId = trim($_POST['student_id'] ?? '');
$password  = $_POST['password']        ?? '';
$confirm   = $_POST['confirm_password'] ?? '';

if (empty($firstName) || empty($lastName) || empty($email) || empty($studentId) || empty($password)) {
    setFlash('danger', 'All fields are required.');
    redirect(siteUrl('/student/register.php'));
}

if (!preg_match('/^[a-zA-Z0-9._%+\-]+@tec\.rjt\.ac\.lk$/', $email)) {
    setFlash('danger', 'Please use a valid university email ending with @tec.rjt.ac.lk');
    redirect(siteUrl('/student/register.php'));
}

if (!preg_match('/^[A-Z]{2,6}\/\d{4}\/\d{3}$/', strtoupper($studentId))) {
    setFlash('danger', 'Invalid Student ID format. Expected e.g. ITT/2024/001');
    redirect(siteUrl('/student/register.php'));
}

if ($password !== $confirm) {
    setFlash('danger', 'Passwords do not match.');
    redirect(siteUrl('/student/register.php'));
}

if (strlen($password) < 8) {
    setFlash('danger', 'Password must be at least 8 characters long.');
    redirect(siteUrl('/student/register.php'));
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        setFlash('danger', 'An account with this email already exists.');
        redirect(siteUrl('/student/register.php'));
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE student_id = ? LIMIT 1');
    $stmt->execute([strtoupper($studentId)]);
    if ($stmt->fetch()) {
        setFlash('danger', 'This Student ID is already registered.');
        redirect(siteUrl('/student/register.php'));
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, student_id, password_hash, role)
         VALUES (?, ?, ?, ?, ?, "student")'
    );
    $stmt->execute([$firstName, $lastName, $email, strtoupper($studentId), $hash]);

    setFlash('success', 'Account created successfully! You can now sign in.');
    redirect(siteUrl('/student/login.php'));

} catch (PDOException $e) {
    error_log('Register error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again later.');
    redirect(siteUrl('/student/register.php'));
}
