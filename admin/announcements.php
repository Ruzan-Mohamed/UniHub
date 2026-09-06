<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdminLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);
$csrf = generateCsrfToken();

// Fetch notices from DB
$notices = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT n.*, u.first_name, u.last_name FROM notices n JOIN users u ON n.admin_id = u.id ORDER BY n.created_at DESC");
    $notices = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }

$badgeMap = ['academic' => 'bg-primary', 'exam' => 'bg-danger', 'event' => 'bg-success', 'urgent' => 'bg-warning text-dark'];

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    return match(true) {
        $diff < 3600   => floor($diff / 60) . ' mins ago',
        $diff < 86400  => floor($diff / 3600) . ' hrs ago',
        $diff < 604800 => floor($diff / 86400) . ' days ago',
        default        => date('M d, Y', strtotime($datetime))
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Publish and monitor academic notices, schedules, and alerts for UniHub.">
    <title>Manage Announcements - UniHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="../index.php" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-shield-lock-fill fs-3 text-primary me-2"></i>
                <span class="fs-4 fw-bold">UniHub Admin</span>
            </a>
            <button class="sidebar-close-btn" aria-label="Close sidebar"><i class="bi bi-x fs-2"></i></button>
        </div>
        <div class="nav flex-column nav-pills mt-4 flex-grow-1">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="students.php" class="nav-link"><i class="bi bi-people-fill"></i> Manage Students</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journal-check"></i> Manage Courses</a>
            <a href="resources.php" class="nav-link"><i class="bi bi-folder2-open"></i> Resources</a>
            <a href="announcements.php" class="nav-link active"><i class="bi bi-megaphone-fill"></i> Announcements</a>
        </div>
        <div class="mt-auto p-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2 mb-3 px-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600;"><?= $initials ?></div>
                <div>
                    <h6 class="mb-0 text-white small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                    <span class="text-white-50 small" style="font-size: 0.75rem;">Staff Root ID</span>
                </div>
            </div>
            <a href="../actions/admin/logout.php" class="btn btn-outline-danger btn-sm w-100 py-2"><i class="bi bi-box-arrow-left me-1"></i> Sign Out</a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0">
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle" type="button"><i class="bi bi-list fs-2"></i></button>
                <h4 class="page-title mb-0 d-none d-sm-block">Release Announcements</h4>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 rounded-pill" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 600;"><?= $initials ?></div>
                            <span class="d-none d-sm-inline text-dark small fw-medium pe-2">Admin Root</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="../student/login.php"><i class="bi bi-person-workspace me-2 text-muted"></i> Student Portal</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="../actions/admin/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card card-glass border-0 p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle me-2 text-primary"></i>Compose Announcement</h5>
                        <p class="text-muted small">Release general updates, schedules, and reminders to specific portal feeds.</p>
                        
                        <form action="../actions/admin/announcement.php" method="POST">
                            <?= csrfField() ?>
                            <div class="mb-3">
                                <label for="annTitle" class="form-label small fw-semibold">Notice Title</label>
                                <input type="text" class="form-control bg-light" id="annTitle" name="title" placeholder="e.g. Timetable Release" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="annCat" class="form-label small fw-semibold">Category</label>
                                    <select class="form-select bg-light" id="annCat" name="category">
                                        <option value="academic" selected>Academic</option>
                                        <option value="exam">Examinations</option>
                                        <option value="event">Campus Event</option>
                                        <option value="urgent">Urgent Alert</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="annAudience" class="form-label small fw-semibold">Target Portal</label>
                                    <select class="form-select bg-light" id="annAudience" name="audience">
                                        <option value="all" selected>All Portals</option>
                                        <option value="student">Students Only</option>
                                        <option value="staff">Academic Staff Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="annExpiry" class="form-label small fw-semibold">Expiry Date (Optional)</label>
                                <input type="date" class="form-control bg-light" id="annExpiry" name="expiry_date">
                            </div>
                            <div class="mb-4">
                                <label for="annMessage" class="form-label small fw-semibold">Notice Details</label>
                                <textarea class="form-control bg-light" id="annMessage" name="message" rows="5" placeholder="Compose announcement details here..." required></textarea>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="annPin" name="is_pinned" checked>
                                <label class="form-check-label small text-muted" for="annPin">Pin to the top of dashboard feed</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold btn-animate fs-6">
                                Release notice <i class="bi bi-send ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card card-glass border-0 p-4">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-newspaper me-2 text-primary"></i>Active Announcements</h5>

                        <?php if (empty($notices)): ?>
                        <p class="text-muted small">No announcements yet.</p>
                        <?php else: foreach ($notices as $n):
                            $badge = $badgeMap[$n['category']] ?? 'bg-secondary';
                        ?>
                        <div class="card border p-3 mb-3 bg-light bg-opacity-25 shadow-sm" style="border-radius: 12px;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge <?= $badge ?> mb-2"><?= ucfirst(sanitize($n['category'])) ?></span>
                                    <h6 class="fw-bold mb-1"><?= sanitize($n['title']) ?></h6>
                                </div>
                                <span class="text-muted small"><?= timeAgo($n['created_at']) ?></span>
                            </div>
                            <p class="small text-muted mb-3"><?= sanitize(substr($n['message'], 0, 150)) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-medium"><i class="bi bi-people me-1"></i>Audience: <?= ucfirst(sanitize($n['audience'])) ?></span>
                                <div>
                                    <form action="../actions/admin/delete_announcement.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="notice_id" value="<?= (int)$n['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm" type="submit" aria-label="Delete announcement"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
</body>
</html>

