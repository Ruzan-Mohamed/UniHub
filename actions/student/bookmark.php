<?php
// actions/student/bookmark.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireStudentLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$user       = getCurrentUser();
$resourceId = (int)($_POST['resource_id'] ?? 0);
$action     = $_POST['action'] ?? 'add'; // 'add' | 'remove'

if ($resourceId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid resource.']);
    exit;
}

try {
    $pdo = getDB();

    if ($action === 'remove') {
        $stmt = $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND resource_id = ?');
        $stmt->execute([$user['id'], $resourceId]);
        echo json_encode(['success' => true, 'bookmarked' => false]);
    } else {
        // INSERT IGNORE avoids duplicate key errors
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO bookmarks (user_id, resource_id) VALUES (?, ?)'
        );
        $stmt->execute([$user['id'], $resourceId]);
        echo json_encode(['success' => true, 'bookmarked' => true]);
    }

} catch (PDOException $e) {
    error_log('Bookmark error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}
