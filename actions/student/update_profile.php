<?php
// actions/student/update_profile.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireStudentLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/profile.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/student/profile.php'));
}

$user      = getCurrentUser();
$section   = $_POST['section'] ?? 'personal';
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name']  ?? '');

try {
    $pdo = getDB();

    if ($section === 'personal') {
        if (empty($firstName) || empty($lastName)) {
            setFlash('danger', 'First and last name are required.');
            redirect(siteUrl('/student/profile.php'));
        }
        $stmt = $pdo->prepare('UPDATE users SET first_name = ?, last_name = ? WHERE id = ?');
        $stmt->execute([$firstName, $lastName, $user['id']]);
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name']  = $lastName;
        setFlash('success', 'Personal details updated successfully.');
    } elseif ($section === 'contact') {
        setFlash('info', 'Contact information update recorded.');
    }

    redirect(siteUrl('/student/profile.php'));

} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again.');
    redirect(siteUrl('/student/profile.php'));
}
