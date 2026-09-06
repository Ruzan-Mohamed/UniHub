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

// Fetch students
$students = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM users WHERE role='student' ORDER BY created_at DESC");
    $students = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }

$avatarColors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-secondary'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage university students, registration statuses, and academic records in the UniHub Admin portal.">
    <title>Manage Students - UniHub Admin</title>
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
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="students.php" class="nav-link active"><i class="bi bi-people-fill"></i> Manage Students</a>
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
            <a href="../actions/admin/logout.php" class="btn btn-outline-danger btn-sm w-100 py-2"><i class="bi bi-box-arrow-left me-1"></i> Sign Out</a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0">
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle" type="button"><i class="bi bi-list fs-2"></i></button>
                <h4 class="page-title mb-0 d-none d-sm-block">Manage Student Records</h4>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <button class="btn btn-primary btn-sm btn-animate py-2 px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add Student
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 rounded-pill" type="button" data-bs-toggle="dropdown">
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
            <div class="card card-glass border-0 p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Student</th>
                                <th>Registration ID</th>
                                <th>Email</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                            <tr><td colspan="6" class="text-muted text-center py-4">No students registered yet.</td></tr>
                            <?php else: foreach ($students as $i => $s):
                                $col = $avatarColors[$i % count($avatarColors)];
                                $sInitials = getInitials($s['first_name'], $s['last_name']);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="<?= $col ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 600;"><?= $sInitials ?></div>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= sanitize($s['first_name'] . ' ' . $s['last_name']) ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="fw-semibold"><?= sanitize($s['student_id'] ?: '—') ?></span></td>
                                <td><span class="text-muted small"><?= sanitize($s['email']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                                <td><span class="badge badge-soft-<?= $s['is_active'] ? 'success' : 'danger' ?> rounded-pill"><?= $s['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                                <td class="text-end">
                                    <form action="../actions/admin/manage_student.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this student? This cannot be undone.')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="student_id" value="<?= (int)$s['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm btn-animate" type="submit"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="addStudentModalLabel">Add Student Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/admin/manage_student.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="student_id" value="0">
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="addFirst" class="form-label small fw-semibold">First Name</label>
                            <input type="text" class="form-control bg-light" id="addFirst" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="addLast" class="form-label small fw-semibold">Last Name</label>
                            <input type="text" class="form-control bg-light" id="addLast" name="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="addReg" class="form-label small fw-semibold">Registration ID</label>
                        <input type="text" class="form-control bg-light" id="addReg" name="student_id_text" placeholder="e.g. ITT/2024/105" required>
                    </div>
                    <div class="mb-3">
                        <label for="addEmail" class="form-label small fw-semibold">University Email</label>
                        <input type="email" class="form-control bg-light" id="addEmail" name="email" placeholder="student@tec.rjt.ac.lk" required>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-animate fw-semibold">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
</body>
</html>

