<?php
// actions/student/contact.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireStudentLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/contact.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/student/contact.php'));
}

$user    = getCurrentUser();
$subject = trim($_POST['subject']        ?? '');
$topic   = trim($_POST['topic_category'] ?? 'tech');
$message = trim($_POST['message']        ?? '');

if (empty($subject) || empty($message)) {
    setFlash('danger', 'Subject and message are required.');
    redirect(siteUrl('/student/contact.php'));
}

$allowedTopics = ['tech', 'academic', 'advisor'];
if (!in_array($topic, $allowedTopics, true)) {
    $topic = 'tech';
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO feedback (user_id, subject, topic_category, message)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$user['id'], $subject, $topic, $message]);

    setFlash('success', 'Your support message has been submitted. We will get back to you shortly.');
    redirect(siteUrl('/student/contact.php'));

} catch (PDOException $e) {
    error_log('Contact error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again.');
    redirect(siteUrl('/student/contact.php'));
}
