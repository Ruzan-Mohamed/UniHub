<?php
// actions/admin/manage_course.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/admin/courses.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect(siteUrl('/admin/courses.php'));
}

$action   = $_POST['action']    ?? '';
$courseId = (int)($_POST['course_id'] ?? 0);

try {
    $pdo = getDB();

    if ($action === 'add') {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name']             ?? '');
        $sem  = trim($_POST['semester']         ?? '');
        $desc = trim($_POST['description']      ?? '');

        if (empty($code) || empty($name)) {
            setFlash('danger', 'Course code and name are required.');
        } elseif (strlen($code) > 20) {
            setFlash('danger', 'Course code cannot exceed 20 characters.');
        } else {
            $checkStmt = $pdo->prepare('SELECT id FROM courses WHERE code = ? LIMIT 1');
            $checkStmt->execute([$code]);
            if ($checkStmt->fetch()) {
                setFlash('danger', 'Course code "' . $code . '" already exists.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO courses (code, name, semester, description) VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([$code, $name, $sem ?: null, $desc ?: null]);
                setFlash('success', 'Course "' . $name . '" added successfully.');
            }
        }

    } elseif ($action === 'edit' && $courseId > 0) {
        $name = trim($_POST['name']        ?? '');
        $sem  = trim($_POST['semester']    ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($name)) {
            setFlash('danger', 'Course name is required.');
        } else {
            $stmt = $pdo->prepare(
                'UPDATE courses SET name = ?, semester = ?, description = ? WHERE id = ?'
            );
            $stmt->execute([$name, $sem ?: null, $desc ?: null, $courseId]);
            setFlash('success', 'Course updated successfully.');
        }

    } elseif ($action === 'delete' && $courseId > 0) {
        $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([$courseId]);
        setFlash('success', 'Course deleted.');

    } else {
        setFlash('danger', 'Unknown action.');
    }

    redirect(siteUrl('/admin/courses.php'));

} catch (PDOException $e) {
    error_log('Manage course error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred: ' . $e->getMessage());
    redirect(siteUrl('/admin/courses.php'));
}
