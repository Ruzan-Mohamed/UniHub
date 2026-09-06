<?php
// actions/student/upload_resource.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

requireStudentLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(siteUrl('/student/upload.php'));
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect(siteUrl('/student/upload.php'));
}

$user  = getCurrentUser();
$title = trim($_POST['title']        ?? '');
$desc  = trim($_POST['description']  ?? '');
$course= trim($_POST['course']       ?? '');
$cat   = trim($_POST['category']     ?? 'other');
$link  = trim($_POST['external_link'] ?? '');
$tagsRaw = trim($_POST['tags']       ?? '');

if (empty($title) || empty($desc) || empty($course)) {
    setFlash('danger', 'Title, description and course are required.');
    redirect(siteUrl('/student/upload.php'));
}

$allowedCats = ['slide', 'lab', 'note', 'other'];
if (!in_array($cat, $allowedCats, true)) {
    $cat = 'other';
}

$tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
$tagsJson = !empty($tags) ? json_encode(array_values($tags)) : null;

$filePath = null;
$fileSize = null;

if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['resource_file'];
    $maxBytes = 75 * 1024 * 1024;

    if ($file['size'] > $maxBytes) {
        setFlash('danger', 'File exceeds the 75 MB size limit.');
        redirect(siteUrl('/student/upload.php'));
    }

    $finfo        = new finfo(FILEINFO_MIME_TYPE);
    $mime         = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/x-zip-compressed',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    if (!in_array($mime, $allowedMimes, true)) {
        setFlash('danger', 'Invalid file type. Allowed: PDF, DOCX, PPTX, ZIP.');
        redirect(siteUrl('/student/upload.php'));
    }

    $uploadDir = __DIR__ . '/../../uploads/resources/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = sprintf('%s_%s.%s', $user['id'], bin2hex(random_bytes(8)), strtolower($ext));
    $dest     = $uploadDir . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        setFlash('danger', 'File upload failed. Please try again.');
        redirect(siteUrl('/student/upload.php'));
    }

    $filePath = 'uploads/resources/' . $safeName;
    $fileSize = $file['size'];
}

if (empty($filePath) && empty($link)) {
    setFlash('danger', 'Please upload a file or provide an external link.');
    redirect(siteUrl('/student/upload.php'));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO resources
            (uploader_id, title, description, course, category, tags, file_path, external_link, file_size, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending")'
    );
    $stmt->execute([
        $user['id'],
        $title,
        $desc,
        $course,
        $cat,
        $tagsJson,
        $filePath,
        $link ?: null,
        $fileSize,
    ]);

    setFlash('success', 'Resource submitted successfully! It is pending admin approval.');
    redirect(siteUrl('/student/resources.php'));

} catch (PDOException $e) {
    error_log('Upload error: ' . $e->getMessage());
    setFlash('danger', 'A server error occurred. Please try again.');
    redirect(siteUrl('/student/upload.php'));
}
