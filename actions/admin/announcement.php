<?php
// actions/admin/announcement.php
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
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/admin/announcements.php'));
}

$user     = getCurrentUser();
$title    = trim($_POST['title']    ?? '');
$category = trim($_POST['category'] ?? 'academic');
$audience = trim($_POST['audience'] ?? 'all');
$message  = trim($_POST['message']  ?? '');
$expiry   = trim($_POST['expiry_date'] ?? '') ?: null;
$isPinned = isset($_POST['is_pinned']) ? 1 : 0;

if (empty($title) || empty($message)) {
    setFlash('danger', 'Notice title and message are required.');
    redirect(siteUrl('/admin/announcements.php'));
}

$allowedCats = ['academic', 'exam', 'event', 'urgent'];
if (!in_array($category, $allowedCats, true)) $category = 'academic';

$allowedAud = ['all', 'student', 'staff'];
if (!in_array($audience, $allowedAud, true)) $audience = 'all';

if ($expiry && !DateTime::createFromFormat('Y-m-d', $expiry)) {
    setFlash('danger', 'Invalid expiry date format.');
    redirect(siteUrl('/admin/announcements.php'));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO notices (admin_id, title, category, audience, message, expiry_date, is_pinned)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$user['id'], $title, $category, $audience, $message, $expiry, $isPinned]);

    setFlash('success', 'Announcement released successfully.');
    redirect(siteUrl('/admin/announcements.php'));

} catch (PDOException $e) {
    error_log('Announcement error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again.');
    redirect(siteUrl('/admin/announcements.php'));
}
