<?php
// actions/admin/manage_resource.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/admin/resources.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect(siteUrl('/admin/resources.php'));
}

$action     = $_POST['action']      ?? '';
$resourceId = (int)($_POST['resource_id'] ?? 0);

if ($resourceId <= 0) {
    setFlash('danger', 'Invalid resource ID.');
    redirect(siteUrl('/admin/resources.php'));
}

try {
    $pdo = getDB();

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE resources SET status = 'approved' WHERE id = ?");
        $stmt->execute([$resourceId]);
        setFlash('success', 'Resource approved and is now visible to students.');

    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE resources SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$resourceId]);
        setFlash('warning', 'Resource has been rejected.');

    } elseif ($action === 'delete') {
        // Also remove physical file if exists
        $stmt = $pdo->prepare("SELECT file_path FROM resources WHERE id = ?");
        $stmt->execute([$resourceId]);
        $row = $stmt->fetch();
        if ($row && $row['file_path']) {
            $fullPath = __DIR__ . '/../../' . $row['file_path'];
            if (file_exists($fullPath)) @unlink($fullPath);
        }
        $stmt = $pdo->prepare("DELETE FROM resources WHERE id = ?");
        $stmt->execute([$resourceId]);
        setFlash('success', 'Resource deleted.');

    } else {
        setFlash('danger', 'Unknown action.');
    }

    redirect(siteUrl('/admin/resources.php'));

} catch (PDOException $e) {
    error_log('Manage resource error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred.');
    redirect(siteUrl('/admin/resources.php'));
}
