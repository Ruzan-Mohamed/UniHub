<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdminLogin();
$user     = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);
$csrf     = generateCsrfToken();

// Fetch all resources with uploader info
$resources = [];
try {
    $pdo  = getDB();
    $stmt = $pdo->query(
        "SELECT r.*, u.first_name, u.last_name, u.student_id
         FROM resources r
         JOIN users u ON r.uploader_id = u.id
         ORDER BY FIELD(r.status,'pending','approved','rejected'), r.created_at DESC"
    );
    $resources = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }

$statusBadge = [
    'pending'  => 'bg-warning text-dark',
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
];
$catIcon = [
    'slide' => 'bi-easel-fill',
    'lab'   => 'bi-flask-fill',
    'note'  => 'bi-journal-text',
    'other' => 'bi-file-earmark-fill',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Review and approve student-uploaded academic resources.">
    <title>Manage Resources - UniHub Admin</title>
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
            <button class="sidebar-close-btn btn btn-link text-white d-lg-none p-0" aria-label="Close"><i class="bi bi-x fs-2"></i></button>
        </div>
        <div class="nav flex-column nav-pills mt-4 flex-grow-1">
            <a href="dashboard.php"     class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="students.php"      class="nav-link"><i class="bi bi-people-fill"></i> Manage Students</a>
            <a href="courses.php"       class="nav-link"><i class="bi bi-journal-check"></i> Manage Courses</a>
            <a href="resources.php"     class="nav-link active"><i class="bi bi-folder2-open"></i> Resources</a>
            <a href="announcements.php" class="nav-link"><i class="bi bi-megaphone-fill"></i> Announcements</a>
        </div>
        <div class="mt-auto p-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2 mb-3 px-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:600;"><?= $initials ?></div>
                <div>
                    <h6 class="mb-0 text-white small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                    <span class="text-white-50 small" style="font-size:.75rem;">Staff Root ID</span>
                </div>
            </div>
            <a href="../actions/admin/logout.php" class="btn btn-outline-danger btn-sm w-100 py-2"><i class="bi bi-box-arrow-left me-1"></i> Sign Out</a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0">
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle"><i class="bi bi-list fs-2"></i></button>
                <h4 class="page-title mb-0 d-none d-sm-block">Resource Moderation</h4>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2 p-1 rounded-pill" type="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.85rem;font-weight:600;"><?= $initials ?></div>
                            <span class="d-none d-sm-inline text-dark small fw-medium pe-2">Admin Root</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2 text-danger" href="../actions/admin/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">

            <!-- Stats bar -->
            <?php
            $pending  = count(array_filter($resources, fn($r) => $r['status'] === 'pending'));
            $approved = count(array_filter($resources, fn($r) => $r['status'] === 'approved'));
            $rejected = count(array_filter($resources, fn($r) => $r['status'] === 'rejected'));
            ?>
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="card card-glass border-0 p-3 d-flex flex-row align-items-center gap-3">
                        <div class="bg-warning-subtle text-warning rounded-3 p-2"><i class="bi bi-hourglass-split fs-4"></i></div>
                        <div><h5 class="fw-bold mb-0"><?= $pending ?></h5><span class="text-muted small">Pending Review</span></div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-glass border-0 p-3 d-flex flex-row align-items-center gap-3">
                        <div class="bg-success-subtle text-success rounded-3 p-2"><i class="bi bi-check-circle-fill fs-4"></i></div>
                        <div><h5 class="fw-bold mb-0"><?= $approved ?></h5><span class="text-muted small">Approved</span></div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card card-glass border-0 p-3 d-flex flex-row align-items-center gap-3">
                        <div class="bg-danger-subtle text-danger rounded-3 p-2"><i class="bi bi-x-circle-fill fs-4"></i></div>
                        <div><h5 class="fw-bold mb-0"><?= $rejected ?></h5><span class="text-muted small">Rejected</span></div>
                    </div>
                </div>
            </div>

            <div class="card card-glass border-0 p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-folder2-open me-2 text-primary"></i>All Uploaded Resources</h5>

                <?php if (empty($resources)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>No resources uploaded yet.</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead>
                            <tr class="text-muted">
                                <th>Resource</th>
                                <th>Uploader</th>
                                <th>Category</th>
                                <th>Uploaded</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resources as $r):
                                $badge = $statusBadge[$r['status']] ?? 'bg-secondary';
                                $icon  = $catIcon[$r['category']]  ?? 'bi-file-earmark-fill';
                            ?>
                            <tr>
                                <td style="max-width:260px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;flex-shrink:0;">
                                            <i class="bi <?= $icon ?>"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width:200px;"><?= sanitize($r['title']) ?></div>
                                            <div class="text-muted" style="font-size:.72rem;max-width:200px;" class="text-truncate"><?= sanitize($r['course'] ?? '—') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= sanitize($r['first_name'] . ' ' . $r['last_name']) ?></span>
                                    <div class="text-muted" style="font-size:.72rem;"><?= sanitize($r['student_id'] ?? '—') ?></div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border"><?= ucfirst(sanitize($r['category'])) ?></span></td>
                                <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                                <td><span class="badge <?= $badge ?> rounded-pill"><?= ucfirst($r['status']) ?></span></td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <?php if ($r['status'] !== 'approved'): ?>
                                        <form action="../actions/admin/manage_resource.php" method="POST" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="resource_id" value="<?= (int)$r['id'] ?>">
                                            <button class="btn btn-success btn-sm" type="submit" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($r['status'] !== 'rejected'): ?>
                                        <form action="../actions/admin/manage_resource.php" method="POST" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="resource_id" value="<?= (int)$r['id'] ?>">
                                            <button class="btn btn-warning btn-sm" type="submit" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($r['file_path']): ?>
                                        <a href="../actions/student/download.php?id=<?= (int)$r['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Download"><i class="bi bi-download"></i></a>
                                        <?php endif; ?>
                                        <form action="../actions/admin/manage_resource.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this resource permanently?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="resource_id" value="<?= (int)$r['id'] ?>">
                                            <button class="btn btn-outline-danger btn-sm" type="submit" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
</body>
</html>
