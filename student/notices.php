<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Fetch notices for students
$notices = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT * FROM notices WHERE audience IN ('all','student')
         AND (expiry_date IS NULL OR expiry_date >= CURDATE())
         ORDER BY is_pinned DESC, created_at DESC"
    );
    $notices = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }

$badgeMap = ['academic' => 'bg-primary', 'exam' => 'bg-danger', 'event' => 'bg-success', 'urgent' => 'bg-warning text-dark'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View official academic and university notices.">
    <title>Academic Notices - UniHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="p-3"><span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Main Menu</span></div>
        <div class="nav flex-column nav-pills flex-grow-1">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journals"></i> Courses</a>
            <a href="resources.php" class="nav-link"><i class="bi bi-file-earmark-text-fill"></i> Resources</a>
            <a href="upload.php" class="nav-link"><i class="bi bi-cloud-arrow-up-fill"></i> Upload</a>
            <a href="notices.php" class="nav-link active"><i class="bi bi-bell-fill"></i> Notices</a>
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
                    <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-mortarboard-fill fs-5"></i></div>
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
                <h3 class="fw-bold text-dark mb-0">Academic Notices</h3>
                <p class="text-muted small">Stay informed with the latest updates from the administration and faculty</p>
            </div>

            <div class="row g-4" style="max-width: 960px;">
                <div class="col-12">
                    <?php if (empty($notices)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash fs-1 mb-3 d-block"></i>
                            <p>No notices available at this time.</p>
                        </div>
                    <?php else: foreach ($notices as $n):
                        $badge = $badgeMap[$n['category']] ?? 'bg-secondary';
                    ?>
                    <div class="notice-item-widget p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-1">
                                <?php if ($n['is_pinned']): ?><i class="bi bi-pin-fill text-primary me-1"></i><?php endif; ?>
                                <?= sanitize($n['title']) ?>
                            </h5>
                            <span class="badge <?= $badge ?>"><?= ucfirst(sanitize($n['category'])) ?></span>
                        </div>
                        <p class="text-muted small mb-3"><?= sanitize($n['message']) ?></p>
                        <span class="text-muted small fw-semibold d-block">Published: <?= date('M d, Y', strtotime($n['created_at'])) ?></span>
                    </div>
                    <?php endforeach; endif; ?>
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
