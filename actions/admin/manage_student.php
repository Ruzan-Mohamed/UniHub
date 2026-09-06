<?php
// actions/admin/manage_student.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/admin/students.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect(siteUrl('/admin/students.php'));
}

$action    = $_POST['action']     ?? '';
$studentId = (int)($_POST['student_id'] ?? 0);

try {
    $pdo = getDB();

    if ($action === 'add') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $regId     = trim($_POST['student_id_text'] ?? '');
        $email     = trim($_POST['email']      ?? '');

        if (empty($firstName) || empty($lastName) || empty($regId) || empty($email)) {
            setFlash('danger', 'All fields are required to add a student.');
        } else {
            $hash = password_hash('Student@1234', PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare(
                'INSERT INTO users (first_name, last_name, email, student_id, password_hash, role)
                 VALUES (?, ?, ?, ?, ?, "student")'
            );
            $stmt->execute([$firstName, $lastName, $email, strtoupper($regId), $hash]);
            setFlash('success', 'Student record added. Temporary password: Student@1234');
        }

    } elseif ($action === 'edit' && $studentId > 0) {
        $status = $_POST['status'] ?? 'active';
        $active = ($status === 'active') ? 1 : 0;
        $stmt   = $pdo->prepare('UPDATE users SET is_active = ? WHERE id = ? AND role = "student"');
        $stmt->execute([$active, $studentId]);
        setFlash('success', 'Student record updated.');

    } elseif ($action === 'delete' && $studentId > 0) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND role = "student"');
        $stmt->execute([$studentId]);
        setFlash('success', 'Student record deleted.');

    } else {
        setFlash('danger', 'Unknown action.');
    }

    redirect(siteUrl('/admin/students.php'));

} catch (PDOException $e) {
    error_log('Manage student error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred: ' . $e->getMessage());
    redirect(siteUrl('/admin/students.php'));
}
