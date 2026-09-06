<?php
// actions/admin/delete_announcement.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/admin/announcements.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect(siteUrl('/admin/announcements.php'));
}

$noticeId = (int)($_POST['notice_id'] ?? 0);

if ($noticeId <= 0) {
    setFlash('danger', 'Invalid notice ID.');
    redirect(siteUrl('/admin/announcements.php'));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('DELETE FROM notices WHERE id = ?');
    $stmt->execute([$noticeId]);

    setFlash('success', 'Announcement removed successfully.');
    redirect(siteUrl('/admin/announcements.php'));

} catch (PDOException $e) {
    error_log('Delete announcement error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred.');
    redirect(siteUrl('/admin/announcements.php'));
}
