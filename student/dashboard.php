<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Fetch recent notices
$notices = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT * FROM notices WHERE audience IN ('all','student')
         AND (expiry_date IS NULL OR expiry_date >= CURDATE())
         ORDER BY is_pinned DESC, created_at DESC LIMIT 3"
    );
    $notices = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail, use empty */ }

// Fetch resource counts for stats
$uploadCount = 0;
$bookmarkCount = 0;
try {
    $pdo = getDB();
    $s1 = $pdo->prepare('SELECT COUNT(*) FROM resources WHERE uploader_id = ?');
    $s1->execute([$user['id']]);
    $uploadCount = (int)$s1->fetchColumn();

    $s2 = $pdo->prepare('SELECT COUNT(*) FROM bookmarks WHERE user_id = ?');
    $s2->execute([$user['id']]);
    $bookmarkCount = (int)$s2->fetchColumn();
} catch (PDOException $e) { /* silently fail */ }

// Fetch recent resources
$resources = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT r.*, u.first_name, u.last_name FROM resources r
         JOIN users u ON r.uploader_id = u.id
         WHERE r.status = 'approved' ORDER BY r.created_at DESC LIMIT 4"
    );
    $resources = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View the UniHub Student Dashboard containing uploaded resources, notices, quick-links, and recent activity.">
    <title>Student Dashboard - UniHub</title>
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
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journals"></i> Courses</a>
            <a href="resources.php" class="nav-link"><i class="bi bi-file-earmark-text-fill"></i> Resources</a>
            <a href="upload.php" class="nav-link"><i class="bi bi-cloud-arrow-up-fill"></i> Upload</a>
            <a href="notices.php" class="nav-link"><i class="bi bi-bell-fill"></i> Notices</a>
            <a href="bookmarks.php" class="nav-link"><i class="bi bi-bookmark-fill"></i> Bookmarks</a>
            <a href="contact.php" class="nav-link"><i class="bi bi-chat-left-text-fill"></i> Contact</a>
        </div>
        <div class="sidebar-signout">
            <a href="../actions/student/logout.php"><i class="bi bi-box-arrow-left"></i> Sign Out</a>
        </div>
    </aside>

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
                    <button class="btn btn-light position-relative p-2 rounded-circle border" type="button">
                        <i class="bi bi-bell text-dark"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end d-none d-sm-block" style="line-height: 1.2;">
                            <h6 class="mb-0 fw-bold text-dark small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                            <span class="text-muted" style="font-size: 0.65rem;">Student</span>
                        </div>
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;">
                            <?= $initials ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-0">Student Dashboard</h3>
                <p class="text-muted small">Welcome back, <?= sanitize($user['first_name']) ?>! Here is what's happening today</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Resources Uploaded</span>
                            <span class="stat-value"><?= $uploadCount ?></span>
                        </div>
                        <div class="stat-icon-wrapper"><i class="bi bi-file-earmark-code-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Active Bookmarks</span>
                            <span class="stat-value"><?= $bookmarkCount ?></span>
                        </div>
                        <div class="stat-icon-wrapper"><i class="bi bi-bookmark-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Total Resource Views</span>
                            <span class="stat-value">1.2k</span>
                            <span class="stat-change text-success d-block mt-1"><i class="bi bi-arrow-up-short"></i> +18.5% since last week</span>
                        </div>
                        <div class="stat-icon-wrapper"><i class="bi bi-eye-fill"></i></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Pending Uploads</span>
                            <span class="stat-value">3</span>
                            <span class="stat-change text-muted d-block mt-1">Activity logs active</span>
                        </div>
                        <div class="stat-icon-wrapper"><i class="bi bi-clock-history"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <a href="resources.php" class="action-button-card bg-action-blue">
                        <div><h5 class="fw-bold mb-0">Browse Popular</h5><span class="small opacity-75">Trending This Week</span></div>
                        <i class="bi bi-arrow-up-right fs-4"></i>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="notices.php" class="action-button-card bg-action-cyan">
                        <div><h5 class="fw-bold mb-0">Latest Notices</h5><span class="small opacity-75">Stay Informed</span></div>
                        <i class="bi bi-bell fs-4"></i>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="contact.php" class="action-button-card bg-action-dark">
                        <div><h5 class="fw-bold mb-0">Need Help ?</h5><span class="small opacity-75">Contact Support</span></div>
                        <i class="bi bi-chat-left-text fs-4"></i>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card bg-white border border-slate-200 p-4 h-100" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark mb-0">Recent Resources</h5>
                            <a href="resources.php" class="text-decoration-none small fw-semibold" style="color: #2563eb;">View Library</a>
                        </div>
                        <div class="row g-3">
                            <?php if (empty($resources)): ?>
                                <div class="col-12"><p class="text-muted small">No resources yet. <a href="upload.php">Upload one!</a></p></div>
                            <?php else: foreach ($resources as $res): ?>
                            <div class="col-md-6">
                                <div class="resource-card d-flex flex-column h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="resource-icon"><i class="bi bi-journal-text fs-5"></i></div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;"><?= sanitize($res['title']) ?></h6>
                                            <span class="text-muted" style="font-size: 0.75rem;"><?= sanitize($res['course']) ?></span>
                                        </div>
                                    </div>
                                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="profile-avatar text-primary"><?= getInitials($res['first_name'], $res['last_name']) ?></div>
                                            <span class="text-muted small fw-medium" style="font-size: 0.75rem;"><?= sanitize($res['first_name'] . ' ' . $res['last_name']) ?></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" style="font-size: 0.7rem;"><?= date('M d, Y', strtotime($res['created_at'])) ?></span>
                                            <?php if ($res['file_path']): ?>
                                            <a href="../<?= sanitize($res['file_path']) ?>" class="btn btn-light btn-sm border" download aria-label="Download resource"><i class="bi bi-download"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="notice-sidebar-card h-100">
                        <h5 class="fw-bold text-dark mb-4">Recent Notices</h5>
                        <?php if (empty($notices)): ?>
                            <p class="text-muted small">No notices available.</p>
                        <?php else: foreach ($notices as $n): ?>
                        <div class="notice-item-widget">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;"><?= sanitize($n['title']) ?></h6>
                            <p class="text-muted small mb-2"><?= sanitize(substr($n['message'], 0, 80)) ?>...</p>
                            <span class="text-muted small fw-semibold" style="font-size: 0.65rem;"><?= date('M d, Y', strtotime($n['created_at'])) ?></span>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<footer class="text-center py-3 bg-white border-top border-slate-200 mt-auto">
    <span class="text-dark small fw-semibold" style="font-size: 0.75rem;">&copy; 2026 UniHub University Resource Management. All rights reserved.</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>
