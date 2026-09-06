<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
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
    <meta name="description" content="Upload academic resources, slides, or documents to UniHub.">
    <title>Upload Resources - UniHub</title>
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
            <a href="upload.php" class="nav-link active"><i class="bi bi-cloud-arrow-up-fill"></i> Upload</a>
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
                    <div class="bg-primary text-white rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-mortarboard-fill fs-5"></i></div>
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -0.5px;">Uni Hub</span>
                </a>
                <div class="d-flex align-items-center gap-3">
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
                <h3 class="fw-bold text-dark mb-0">Upload Resources</h3>
                <p class="text-muted small">Share academic materials with peers and students</p>
            </div>

            <div class="card bg-white border border-slate-200 p-4 shadow-sm" style="border-radius: 12px; max-width: 820px;">
                <form action="../actions/student/upload_resource.php" method="POST" enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="mb-5">
                        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                            <div class="bg-primary text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-weight: 700; font-size: 0.85rem;">T</div>
                            Basic Information
                        </h5>

                        <div class="mb-3">
                            <label for="upTitle" class="form-label small fw-bold text-dark mb-1">Resource Title *</label>
                            <input type="text" class="form-control bg-light border-0 py-2.5 px-3" id="upTitle" name="title" placeholder="e.g. ICT 1207 - Human Computer Interaction - Lecture Slide" required style="border-radius: 6px;">
                        </div>

                        <div class="mb-3">
                            <label for="upDesc" class="form-label small fw-bold text-dark mb-1">Description *</label>
                            <textarea class="form-control bg-light border-0 py-2.5 px-3" id="upDesc" name="description" rows="4" placeholder="Provide context about what this resource covers" required style="border-radius: 6px;"></textarea>
                            <span class="form-text text-muted" style="font-size: 0.7rem;">Briefly describe key topics covered in this topic</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                            <div class="bg-primary text-white rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-layers-fill text-white fs-6"></i></div>
                            Categorization
                        </h5>

<?php
// Fetch courses from database for selection
$dbCourses = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY code ASC");
    $dbCourses = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="upCourse" class="form-label small fw-bold text-dark mb-1">Course *</label>
                                <select class="form-select bg-light border-0 py-2.5 px-3" id="upCourse" name="course" required style="border-radius: 6px;">
                                    <option value="" disabled selected>Select course</option>
                                    <?php foreach ($dbCourses as $c): 
                                        $val = sanitize($c['code'] . ' - ' . $c['name']);
                                    ?>
                                    <option value="<?= $val ?>"><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="upCat" class="form-label small fw-bold text-dark mb-1">Category *</label>
                                <select class="form-select bg-light border-0 py-2.5 px-3" id="upCat" name="category" required style="border-radius: 6px;">
                                    <option value="" disabled>Select category</option>
                                    <option value="slide" selected>Lecture Slide</option>
                                    <option value="lab">Lab Report</option>
                                    <option value="note">Study Note</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="upTags" class="form-label small fw-bold text-dark mb-1">Tags (comma separated)</label>
                            <input type="text" class="form-control bg-light border-0 py-2.5 px-3" id="upTags" name="tags" placeholder="e.g. lecture, week3, hci" style="border-radius: 6px;">
                        </div>

                        <!-- File Upload Zone -->
                        <div class="drag-drop-zone mb-2" id="dropZone">
                            <i class="bi bi-cloud-arrow-up drag-drop-icon"></i>
                            <h6 class="fw-bold text-dark mb-1">Drag and Drop your files here</h6>
                            <p class="text-muted small mb-3">or <span class="fw-semibold" style="color: #2563eb; cursor:pointer;" id="browseBtn">Browse file</span> from your computer</p>
                            <div class="d-flex align-items-center justify-content-center gap-3 text-muted small" style="font-size: 0.75rem;">
                                <span><i class="bi bi-file-earmark-pdf"></i> PDF</span>
                                <span><i class="bi bi-file-earmark-word"></i> DOCX</span>
                                <span><i class="bi bi-file-earmark-zip"></i> ZIP</span>
                                <span class="fw-semibold"><i class="bi bi-info-circle"></i> Max Size: 75MB</span>
                            </div>
                            <div id="fileNameDisplay" class="mt-2 small text-success fw-semibold"></div>
                        </div>
                        <!-- File input OUTSIDE the drop zone to prevent click bubbling -->
                        <input type="file" name="resource_file" id="fileInput" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip" class="d-none">

                        <div class="d-flex align-items-center my-4">
                            <hr class="flex-grow-1 border-slate-300">
                            <span class="mx-3 text-muted small fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">OR PROVIDE AN EXTERNAL LINK</span>
                            <hr class="flex-grow-1 border-slate-300">
                        </div>

                        <div class="mb-3">
                            <label for="upLink" class="form-label small fw-bold text-dark mb-1">External Repository / Link</label>
                            <div class="input-group" style="border-radius: 6px; overflow: hidden;">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg text-muted"></i></span>
                                <input type="url" class="form-control bg-light border-0 py-2.5" id="upLink" name="external_link" placeholder="https://www.github.com/ or https://drive.com">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold" style="border-radius: 6px;">Publish Resource</button>
                    </div>
                </form>
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
const fileInput  = document.getElementById('fileInput');
const dropZone   = document.getElementById('dropZone');
const browseBtn  = document.getElementById('browseBtn');
const fileDisplay = document.getElementById('fileNameDisplay');

// Only browse button and drop zone background trigger file picker
browseBtn.addEventListener('click', (e) => { e.stopPropagation(); fileInput.click(); });
dropZone.addEventListener('click', () => fileInput.click());

// Show filename after selection
fileInput.addEventListener('change', function () {
    const name = this.files[0]?.name || '';
    fileDisplay.textContent = name ? '\u2713 ' + name : '';
    if (name) dropZone.style.borderColor = '#22c55e';
});

// Drag-and-drop
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        // Assign dropped files to the hidden input via DataTransfer
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        fileInput.files = dt.files;
        fileDisplay.textContent = '\u2713 ' + files[0].name;
        dropZone.style.borderColor = '#22c55e';
    }
});
</script>
</body>
</html>
