<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Fetch approved resources with uploader info
$resources = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT r.*, u.first_name, u.last_name FROM resources r
         JOIN users u ON r.uploader_id = u.id
         WHERE r.status = 'approved' ORDER BY r.created_at DESC"
    );
    $resources = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse UniHub academic resources, lecture slides, notes, and lab reports.">
    <title>Academic Resources - UniHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="p-3">
            <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Main Menu</span>
        </div>
        <div class="nav flex-column nav-pills flex-grow-1">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journals"></i> Courses</a>
            <a href="resources.php" class="nav-link active"><i class="bi bi-file-earmark-text-fill"></i> Resources</a>
            <a href="upload.php" class="nav-link"><i class="bi bi-cloud-arrow-up-fill"></i> Upload</a>
            <a href="notices.php" class="nav-link"><i class="bi bi-bell-fill"></i> Notices</a>
            <a href="bookmarks.php" class="nav-link"><i class="bi bi-bookmark-fill"></i> Bookmarks</a>
            <a href="contact.php" class="nav-link"><i class="bi bi-chat-left-text-fill"></i> Contact</a>
        </div>
        <div class="sidebar-signout">
            <a href="../actions/student/logout.php"><i class="bi bi-box-arrow-left"></i> Sign Out</a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <div class="d-none d-md-flex align-items-center" style="width: 280px;">
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" class="form-control" placeholder="Search Resources, tags, courses...">
                    </div>
                </div>
                <a href="../index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                    </div>
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -0.5px;">Uni Hub</span>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <button class="sidebar-toggle-btn" id="sidebarToggle" type="button"><i class="bi bi-list"></i></button>
                    <button class="btn btn-light position-relative p-2 rounded-circle border" type="button"><i class="bi bi-bell text-dark"></i></button>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end d-none d-sm-block" style="line-height: 1.2;">
                            <h6 class="mb-0 fw-bold text-dark small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                            <span class="text-muted" style="font-size: 0.65rem;">Student</span>
                        </div>
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;"><?= $initials ?></div>
                    </div>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-0">Academic Resources</h3>
                <p class="text-muted small">Browse and discover educational materials shared by your university community</p>
            </div>

            <div class="row g-4 mb-5">
                <?php if (empty($resources)): ?>
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x fs-1 mb-3 d-block"></i>
                        <p>No resources available yet. <a href="upload.php">Be the first to upload!</a></p>
                    </div>
                </div>
                <?php else: foreach ($resources as $res):
                    $catIcon = match($res['category']) {
                        'lab'   => 'bi-file-earmark-code',
                        'note'  => 'bi-journal-richtext',
                        default => 'bi-journal-text'
                    };
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="resource-card d-flex flex-column h-100 p-4">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="resource-icon" style="width: 42px; height: 42px;">
                                <i class="<?= $catIcon ?> fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><?= sanitize($res['title']) ?></h6>
                                <span class="text-muted" style="font-size: 0.75rem;"><?= sanitize($res['course']) ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="profile-avatar text-primary" style="width: 32px; height: 32px;"><?= getInitials($res['first_name'], $res['last_name']) ?></div>
                            <span class="text-muted small fw-semibold" style="font-size: 0.8rem;"><?= sanitize($res['first_name'] . ' ' . $res['last_name']) ?></span>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                            <div class="d-flex gap-3 text-muted small" style="font-size: 0.75rem;">
                                <span><i class="bi bi-eye"></i> <?= $res['views'] ?></span>
                                <span class="fw-semibold"><?= date('M d, Y', strtotime($res['created_at'])) ?></span>
                            </div>
                            <div class="d-flex gap-1.5 align-items-center">
                                <?php if ($res['file_path']): ?>
                                <a href="../actions/student/download.php?id=<?= (int)$res['id'] ?>" class="btn btn-light btn-sm border" aria-label="Download resource" title="Download"><i class="bi bi-download"></i></a>
                                <?php elseif ($res['external_link']): ?>
                                <a href="<?= sanitize($res['external_link']) ?>" class="btn btn-light btn-sm border" target="_blank" rel="noopener" aria-label="Open resource" title="Open Link"><i class="bi bi-box-arrow-up-right"></i></a>
                                <?php endif; ?>

                                <?php if ($res['uploader_id'] == $user['id'] || ($user['role'] ?? '') === 'admin'): ?>
                                <form action="../actions/student/delete_resource.php" method="POST" class="d-inline" onsubmit="return confirm('Remove this resource permanently?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="resource_id" value="<?= (int)$res['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm border" type="submit" title="Remove Resource">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </main>
</div>

<footer class="text-center py-3 bg-white border-top border-slate-200 mt-auto">
    <span class="text-dark small fw-semibold" style="font-size: 0.75rem;">&copy; 2026 UniHub University Resource Management. All rights reserved.</span>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
</body>
</html>
