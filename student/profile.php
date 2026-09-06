<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);
$csrf = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage your academic and personal information on the UniHub Student Profile page.">
    <title>My Profile - UniHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="dashboard.php" class="d-flex align-items-center text-white text-decoration-none">
                <i class="bi bi-mortarboard-fill fs-3 text-primary me-2"></i>
                <span class="fs-4 fw-bold">UniHub</span>
            </a>
            <button class="sidebar-close-btn btn btn-link text-white d-lg-none p-0" aria-label="Close sidebar">
                <i class="bi bi-x fs-2"></i>
            </button>
        </div>
        <div class="nav flex-column nav-pills mt-4 flex-grow-1">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="courses.php" class="nav-link"><i class="bi bi-journals"></i> Courses</a>
            <a href="profile.php" class="nav-link active"><i class="bi bi-person-fill"></i> My Profile</a>
        </div>
        <div class="mt-auto p-3 border-top border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2 mb-3 px-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600;"><?= $initials ?></div>
                <div>
                    <h6 class="mb-0 text-white small"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                    <span class="text-white-50 small" style="font-size: 0.75rem;"><?= sanitize($user['student_id']) ?></span>
                </div>
            </div>
            <a href="../actions/student/logout.php" class="btn btn-outline-danger btn-sm w-100 py-2">
                <i class="bi bi-box-arrow-left me-1"></i> Sign Out
            </a>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <nav class="navbar navbar-expand navbar-light top-navbar sticky-top">
            <div class="container-fluid p-0">
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-2"></i>
                </button>
                <h4 class="page-title mb-0 d-none d-sm-block">Profile Settings</h4>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 rounded-pill" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 600;"><?= $initials ?></div>
                            <span class="d-none d-sm-inline text-dark small fw-medium pe-2"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="profile.php"><i class="bi bi-person me-2 text-muted"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="../actions/student/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <?php renderFlash(); ?>

        <div class="container-fluid p-4">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card card-glass border-0 text-center p-4">
                        <div class="position-relative d-inline-block mx-auto mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 3.5rem; font-weight: 600;">
                                <?= $initials ?>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-1"><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                        <p class="text-muted small mb-3">Student at Rajarata University</p>
                        <span class="badge badge-soft-success px-3 py-2 rounded-pill mb-4">Active Undergraduate</span>
                        <hr class="my-4">
                        <div class="text-start">
                            <div class="mb-3">
                                <span class="text-muted small d-block">Student ID</span>
                                <span class="fw-medium text-dark"><?= sanitize($user['student_id'] ?: '—') ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block">University Email</span>
                                <span class="fw-medium text-dark"><?= sanitize($user['email']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-glass border-0 p-4">
                        <ul class="nav nav-tabs nav-fill mb-4 border-bottom" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-pane" type="button" role="tab">Personal Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-pane" type="button" role="tab">Contact Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic-pane" type="button" role="tab">Academic Info</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="profileTabsContent">
                            <div class="tab-pane fade show active" id="personal-pane" role="tabpanel">
                                <form action="../actions/student/update_profile.php" method="POST">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="section" value="personal">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label small fw-semibold">First Name</label>
                                            <input type="text" class="form-control bg-light" id="firstName" name="first_name" value="<?= sanitize($user['first_name']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label small fw-semibold">Last Name</label>
                                            <input type="text" class="form-control bg-light" id="lastName" name="last_name" value="<?= sanitize($user['last_name']) ?>" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary py-2 px-4 btn-animate fw-semibold">Save Personal Details</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="contact-pane" role="tabpanel">
                                <form action="../actions/student/update_profile.php" method="POST">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="section" value="contact">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="personalEmail" class="form-label small fw-semibold">Personal Email</label>
                                            <input type="email" class="form-control bg-light" id="personalEmail" name="personal_email" placeholder="your.personal@email.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="uniEmail" class="form-label small fw-semibold">University Email (Read-Only)</label>
                                            <input type="email" class="form-control bg-light" id="uniEmail" value="<?= sanitize($user['email']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="phone" class="form-label small fw-semibold">Phone Number</label>
                                        <input type="tel" class="form-control bg-light" id="phone" name="phone" placeholder="+94 7X XXX XXXX">
                                    </div>
                                    <button type="submit" class="btn btn-primary py-2 px-4 btn-animate fw-semibold">Save Contact Details</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="academic-pane" role="tabpanel">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">Student ID</label>
                                        <p class="form-control-plaintext fw-bold"><?= sanitize($user['student_id'] ?: '—') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">University Email</label>
                                        <p class="form-control-plaintext"><?= sanitize($user['email']) ?></p>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="bg-light p-3 rounded-3 border">
                                    <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-info-circle me-1"></i> Academic Advisor Note</h6>
                                    <p class="small text-muted mb-0">For changes to your academic major or course specializations, please contact the Registrar Office or submit an official form to your department advisor.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>
