<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Fetch active courses with resource count
$courses = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query(
        "SELECT c.*, 
                (SELECT COUNT(*) FROM resources r WHERE (r.course LIKE CONCAT('%', c.code, '%') OR r.course LIKE CONCAT('%', c.name, '%')) AND r.status='approved') AS resource_count 
         FROM courses c 
         ORDER BY c.code ASC"
    );
    $courses = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse university courses, syllabi, and related study materials on UniHub.">
    <title>Academic Courses - UniHub</title>
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
            <a href="courses.php" class="nav-link active"><i class="bi bi-journals"></i> Courses</a>
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
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                <div class="d-none d-md-flex align-items-center" style="width: 280px;">
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="courseSearchInput" class="form-control" placeholder="Search courses by code or title...">
                    </div>
                </div>
                <a href="../index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-mortarboard-fill fs-5"></i></div>
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -0.5px;">Uni Hub</span>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="profile.php" class="d-flex align-items-center gap-2 text-decoration-none">
                        <div class="text-end d-none d-sm-block" style="line-height: 1.2;">
                            <h6 class="mb-0 fw-bold text-dark small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                            <span class="text-muted" style="font-size: 0.65rem;">Student</span>
                        </div>
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;"><?= $initials ?></div>
                    </a>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="fw-bold text-dark mb-0">Academic Courses</h3>
                    <p class="text-muted small">Explore registered university courses and access related resources</p>
                </div>
                <div class="d-md-none w-100 mt-2">
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="mobileCourseSearch" class="form-control" placeholder="Search courses...">
                    </div>
                </div>
            </div>

            <div class="row g-4" id="courseGrid">
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-journals fs-1 d-block mb-3"></i>
                    <p>No courses available at the moment.</p>
                </div>
                <?php else: foreach ($courses as $c): ?>
                <div class="col-lg-4 col-md-6 course-card-wrapper" data-code="<?= strtolower(sanitize($c['code'])) ?>" data-name="<?= strtolower(sanitize($c['name'])) ?>">
                    <div class="card card-glass border-0 p-4 h-100 d-flex flex-column" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 rounded-pill fs-6"><?= sanitize($c['code']) ?></span>
                            <?php if ($c['semester']): ?>
                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-2" style="font-size: 0.72rem;"><?= sanitize($c['semester']) ?></span>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold text-dark mb-2"><?= sanitize($c['name']) ?></h5>

                        <?php if ($c['description']): ?>
                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6; font-size: 0.85rem;"><?= sanitize($c['description']) ?></p>
                        <?php else: ?>
                        <div class="flex-grow-1"></div>
                        <?php endif; ?>

                        <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-medium">
                                <i class="bi bi-file-earmark-text me-1 text-primary"></i><?= (int)$c['resource_count'] ?> Resource<?= $c['resource_count'] == 1 ? '' : 's' ?>
                            </span>
                            <a href="resources.php?search=<?= urlencode($c['code']) ?>" class="btn btn-outline-primary btn-sm rounded-2 fw-semibold px-3">
                                View Materials <i class="bi bi-arrow-right ms-1"></i>
                            </a>
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
<script src="../assets/js/script.js"></script>
<script>
function filterCourses(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.course-card-wrapper').forEach(card => {
        const code = card.dataset.code || '';
        const name = card.dataset.name || '';
        if (code.includes(q) || name.includes(q)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
document.getElementById('courseSearchInput')?.addEventListener('input', e => filterCourses(e.target.value));
document.getElementById('mobileCourseSearch')?.addEventListener('input', e => filterCourses(e.target.value));
</script>
</body>
</html>
