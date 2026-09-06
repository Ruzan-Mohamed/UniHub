<?php
// actions/student/delete_resource.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireStudentLogin();
$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/resources.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect(siteUrl('/student/resources.php'));
}

$resourceId = (int)($_POST['resource_id'] ?? 0);

if ($resourceId <= 0) {
    setFlash('danger', 'Invalid resource ID.');
    redirect(siteUrl('/student/resources.php'));
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$resourceId]);
    $res = $stmt->fetch();

    if (!$res) {
        setFlash('danger', 'Resource not found.');
        redirect(siteUrl('/student/resources.php'));
    }

    // Only allow uploader or admin to delete resource
    if ($res['uploader_id'] != $user['id'] && ($user['role'] ?? '') !== 'admin') {
        setFlash('danger', 'You can only remove resources uploaded by yourself.');
        redirect(siteUrl('/student/resources.php'));
    }

    // Delete physical file if exists
    if (!empty($res['file_path'])) {
        $cleanPath = str_replace('\\', '/', $res['file_path']);
        $fullPath  = __DIR__ . '/../../' . ltrim($cleanPath, '/');
        if (file_exists($fullPath)) @unlink($fullPath);
    }

    $delStmt = $pdo->prepare("DELETE FROM resources WHERE id = ?");
    $delStmt->execute([$resourceId]);

    setFlash('success', 'Resource removed successfully.');
    redirect(siteUrl('/student/resources.php'));

} catch (PDOException $e) {
    error_log('Delete resource error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred while removing resource.');
    redirect(siteUrl('/student/resources.php'));
}
