<?php
// actions/student/download.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$resourceId = (int)($_GET['id'] ?? 0);
if ($resourceId <= 0) {
    http_response_code(400);
    die('Invalid resource ID.');
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
    $stmt->execute([$resourceId]);
    $res = $stmt->fetch();

    if (!$res) {
        http_response_code(404);
        die('Resource not found.');
    }

    // If external link, redirect to external link
    if (empty($res['file_path']) && !empty($res['external_link'])) {
        header('Location: ' . $res['external_link']);
        exit;
    }

    if (empty($res['file_path'])) {
        http_response_code(404);
        die('No file attachment found for this resource.');
    }

    $cleanPath = str_replace('\\', '/', $res['file_path']);
    $fullPath  = __DIR__ . '/../../' . ltrim($cleanPath, '/');

    if (!file_exists($fullPath)) {
        http_response_code(404);
        die('File not found on server.');
    }

    // Increment download/view count
    $updateStmt = $pdo->prepare("UPDATE resources SET views = views + 1 WHERE id = ?");
    $updateStmt->execute([$resourceId]);

    // Format download filename
    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
    $safeTitle = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $res['title']);
    $downloadName = trim($safeTitle) ?: 'resource';
    if ($ext && !str_ends_with(strtolower($downloadName), '.' . strtolower($ext))) {
        $downloadName .= '.' . $ext;
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    if (ob_get_level()) ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fullPath));

    readfile($fullPath);
    exit;

} catch (PDOException $e) {
    error_log('Download error: ' . $e->getMessage());
    http_response_code(500);
    die('Server error processing download.');
}
