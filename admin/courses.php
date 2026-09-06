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

// Fetch courses
$courses = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage academic courses in the UniHub Admin portal.">
    <title>Manage Courses - UniHub Admin</title>
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
            <a href="students.php" class="nav-link"><i class="bi bi-people-fill"></i> Manage Students</a>
            <a href="courses.php" class="nav-link active"><i class="bi bi-journal-check"></i> Manage Courses</a>
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
                <h4 class="page-title mb-0 d-none d-sm-block">Manage Academic Courses</h4>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <button class="btn btn-primary btn-sm btn-animate py-2 px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Course
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
            <div class="row g-4">
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                    <p>No courses found. <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add one now.</button></p>
                </div>
                <?php else: foreach ($courses as $c): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-glass border-0 p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-primary-subtle text-primary fw-bold mb-2"><?= sanitize($c['code']) ?></span>
                                <h6 class="fw-bold text-dark mb-1"><?= sanitize($c['name']) ?></h6>
                                <?php if ($c['semester']): ?>
                                <span class="text-muted small"><?= sanitize($c['semester']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-edit-course" 
                                        data-id="<?= (int)$c['id'] ?>"
                                        data-code="<?= sanitize($c['code']) ?>"
                                        data-name="<?= sanitize($c['name']) ?>"
                                        data-semester="<?= sanitize($c['semester'] ?? '') ?>"
                                        data-description="<?= sanitize($c['description'] ?? '') ?>"
                                        title="Edit Course">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="../actions/admin/manage_course.php" method="POST" onsubmit="return confirm('Delete this course?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="course_id" value="<?= (int)$c['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm" type="submit" title="Delete Course"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <?php if ($c['description']): ?>
                        <p class="text-muted small mb-0 flex-grow-1"><?= sanitize(substr($c['description'], 0, 120)) ?><?= strlen($c['description']) > 120 ? '...' : '' ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="addCourseModalLabel"><i class="bi bi-journal-plus me-2 text-primary"></i>Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/admin/manage_course.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="course_id" value="0">
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-md-5">
                            <label for="courseCode" class="form-label small fw-semibold">Course Code *</label>
                            <input type="text" class="form-control bg-light" id="courseCode" name="code" placeholder="e.g. ICT2202" required>
                            <span class="form-text text-muted" style="font-size:0.7rem;">Must be unique</span>
                        </div>
                        <div class="col-md-7">
                            <label for="courseName" class="form-label small fw-semibold">Course Name *</label>
                            <input type="text" class="form-control bg-light" id="courseName" name="name" placeholder="e.g. Software Engineering" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="courseSem" class="form-label small fw-semibold">Semester</label>
                        <input type="text" class="form-control bg-light" id="courseSem" name="semester" placeholder="e.g. Semester 2, 2024">
                    </div>
                    <div class="mb-3">
                        <label for="courseDesc" class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control bg-light" id="courseDesc" name="description" rows="3" placeholder="Brief course overview and topics covered..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-animate fw-semibold px-4">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-glass border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark" id="editCourseModalLabel"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/admin/manage_course.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="course_id" id="editCourseId" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editCourseCode" class="form-label small fw-semibold">Course Code (Read-Only)</label>
                        <input type="text" class="form-control bg-light text-muted" id="editCourseCode" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editCourseName" class="form-label small fw-semibold">Course Name *</label>
                        <input type="text" class="form-control bg-light" id="editCourseName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCourseSem" class="form-label small fw-semibold">Semester</label>
                        <input type="text" class="form-control bg-light" id="editCourseSem" name="semester">
                    </div>
                    <div class="mb-3">
                        <label for="editCourseDesc" class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control bg-light" id="editCourseDesc" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-animate fw-semibold px-4">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
<script>
document.querySelectorAll('.btn-edit-course').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('editCourseId').value = this.dataset.id;
        document.getElementById('editCourseCode').value = this.dataset.code;
        document.getElementById('editCourseName').value = this.dataset.name;
        document.getElementById('editCourseSem').value = this.dataset.semester;
        document.getElementById('editCourseDesc').value = this.dataset.description;
        
        const modal = new bootstrap.Modal(document.getElementById('editCourseModal'));
        modal.show();
    });
});
</script>
</body>
</html>
