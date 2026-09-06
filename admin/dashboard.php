<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdminLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Stats
$totalStudents = 0; $activeCourses = 0; $totalNotices = 0;
$recentUsers = [];
try {
    $pdo = getDB();
    $totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
    $activeCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $totalNotices  = (int)$pdo->query("SELECT COUNT(*) FROM notices")->fetchColumn();
    $stmt = $pdo->query("SELECT * FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5");
    $recentUsers = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View the UniHub Admin Dashboard.">
    <title>Admin Dashboard - UniHub</title>
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
            <button class="sidebar-close-btn btn btn-link text-white d-lg-none p-0" aria-label="Close sidebar"><i class="bi bi-x fs-2"></i></button>
        </div>
        <div class="nav flex-column nav-pills mt-4 flex-grow-1">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="students.php" class="nav-link"><i class="bi bi-people-fill"></i> Manage Students</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journal-check"></i> Manage Courses</a>
            <a href="resources.php" class="nav-link"><i class="bi bi-folder2-open"></i> Resources</a>
            <a href="announcements.php" class="nav-link"><i class="bi bi-megaphone-fill"></i> Announcements</a>
        </div>
        <div class="mt-auto p-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2 mb-3 px-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600;"><?= $initials ?></div>
                <div>
                    <h6 class="mb-0 text-white small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                    <span class="text-white-50 small" style="font-size: 0.75rem;">Staff Root ID</span>
                </div>
            </div>
            <a href="../actions/admin/logout.php" class="btn btn-outline-danger btn-sm w-100 py-2">
                <i class="bi bi-box-arrow-left me-1"></i> Sign Out
            </a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0">
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle" type="button"><i class="bi bi-list fs-2"></i></button>
                <div class="d-none d-md-flex align-items-center gap-2 text-muted small">
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 py-1.5 px-3 rounded-pill">
                        <i class="bi bi-database me-1"></i> MySQL: Connected
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 py-1.5 px-3 rounded-pill">
                        <i class="bi bi-cpu me-1"></i> Apache: Operational
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <a href="announcements.php" class="btn btn-primary btn-sm btn-animate py-2 px-3 fw-medium d-none d-sm-inline-block">
                        <i class="bi bi-plus-lg me-1"></i> Post Announcement
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 rounded-pill" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 600;"><?= $initials ?></div>
                            <span class="d-none d-sm-inline text-dark small fw-medium pe-2"><?= sanitize($user['first_name']) ?></span>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">System Control Panel</h3>
                <span class="text-muted small" id="liveClock">Local time: <?= date('Y-m-d H:i') ?></span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card card-glass border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Total Registered Students</span>
                            <div class="bg-primary-subtle text-primary p-2 rounded-3"><i class="bi bi-people fs-5"></i></div>
                        </div>
                        <h3 class="fw-bold mb-1"><?= number_format($totalStudents) ?></h3>
                        <span class="text-muted small fw-medium">Registered accounts</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card card-glass border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Active Courses</span>
                            <div class="bg-success-subtle text-success p-2 rounded-3"><i class="bi bi-journals fs-5"></i></div>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $activeCourses ?></h3>
                        <span class="text-muted small fw-medium">In database</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card card-glass border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Announcements Released</span>
                            <div class="bg-warning-subtle text-warning p-2 rounded-3"><i class="bi bi-megaphone fs-5"></i></div>
                        </div>
                        <h3 class="fw-bold mb-1"><?= $totalNotices ?></h3>
                        <span class="text-muted small fw-medium">Total notices</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card card-glass border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Total Database Size</span>
                            <div class="bg-danger-subtle text-danger p-2 rounded-3"><i class="bi bi-hdd-network fs-5"></i></div>
                        </div>
                        <h3 class="fw-bold mb-1">—</h3>
                        <span class="text-muted small fw-medium">MySQL unihub DB</span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card card-glass border-0 p-4 h-100">
                        <h6 class="fw-bold mb-3">Recent Student Registrations</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead>
                                    <tr class="text-muted">
                                        <th>Registration ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentUsers)): ?>
                                    <tr><td colspan="4" class="text-muted text-center py-3">No students registered yet.</td></tr>
                                    <?php else: foreach ($recentUsers as $u): ?>
                                    <tr>
                                        <td class="fw-bold"><?= sanitize($u['student_id'] ?: '—') ?></td>
                                        <td><?= sanitize($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                        <td><?= sanitize($u['email']) ?></td>
                                        <td><span class="badge badge-soft-<?= $u['is_active'] ? 'success' : 'danger' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-glass border-0 p-4 h-100">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-clock-history me-2 text-primary"></i>Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="students.php" class="btn btn-outline-primary btn-animate"><i class="bi bi-people me-2"></i>Manage Students</a>
                            <a href="courses.php" class="btn btn-outline-success btn-animate"><i class="bi bi-journal-check me-2"></i>Manage Courses</a>
            <a href="resources.php" class="nav-link"><i class="bi bi-folder2-open"></i> Resources</a>
                            <a href="announcements.php" class="btn btn-outline-warning btn-animate"><i class="bi bi-megaphone me-2"></i>Post Announcement</a>
                        </div>
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

