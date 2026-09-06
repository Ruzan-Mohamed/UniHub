<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireStudentLogin();
$user = getCurrentUser();
$initials = getInitials($user['first_name'], $user['last_name']);

// Fetch bookmarked resources
$bookmarks = [];
try {
    $pdo = getDB();
    $stmt = $pdo->prepare(
        "SELECT r.*, u.first_name, u.last_name FROM bookmarks b
         JOIN resources r ON b.resource_id = r.id
         JOIN users u ON r.uploader_id = u.id
         WHERE b.user_id = ? ORDER BY b.created_at DESC"
    );
    $stmt->execute([$user['id']]);
    $bookmarks = $stmt->fetchAll();
} catch (PDOException $e) { /* silently fail */ }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View your bookmarked dynamic courses and references.">
    <title>My Bookmarks - UniHub</title>
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
            <a href="notices.php" class="nav-link"><i class="bi bi-bell-fill"></i> Notices</a>
            <a href="bookmarks.php" class="nav-link active"><i class="bi bi-bookmark-fill"></i> Bookmarks</a>
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
                <h3 class="fw-bold text-dark mb-0">Bookmarked Resources</h3>
                <p class="text-muted small">Access pinned lecture slides, references, and publications instantly</p>
            </div>

            <div class="row g-4 mb-4">
                <?php if (empty($bookmarks)): ?>
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bookmark-x fs-1 mb-3 d-block"></i>
                        <p>No bookmarks yet. <a href="resources.php">Browse resources</a> and save your favourites.</p>
                    </div>
                </div>
                <?php else: foreach ($bookmarks as $bm): ?>
                <div class="col-md-6">
                    <div class="resource-card d-flex flex-column h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="resource-icon"><i class="bi bi-journal-text fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><?= sanitize($bm['title']) ?></h6>
                                    <span class="text-muted text-truncate d-block" style="font-size: 0.75rem; max-width: 250px;"><?= sanitize($bm['course']) ?></span>
                                </div>
                            </div>
                            <button class="btn btn-link text-primary p-0 bookmark-btn" data-resource-id="<?= $bm['id'] ?>" data-action="remove" aria-label="Remove bookmark">
                                <i class="bi bi-bookmark-fill fs-5"></i>
                            </button>
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="profile-avatar text-primary"><?= getInitials($bm['first_name'], $bm['last_name']) ?></div>
                                <span class="text-muted small fw-medium" style="font-size: 0.75rem;"><?= sanitize($bm['first_name'] . ' ' . $bm['last_name']) ?></span>
                            </div>
                            <div class="d-flex gap-1.5 align-items-center">
                                <?php if ($bm['file_path']): ?>
                                <a href="../actions/student/download.php?id=<?= (int)$bm['id'] ?>" class="btn btn-light btn-sm border" aria-label="Download resource" title="Download"><i class="bi bi-download"></i></a>
                                <?php elseif ($bm['external_link']): ?>
                                <a href="<?= sanitize($bm['external_link']) ?>" class="btn btn-light btn-sm border" target="_blank" rel="noopener" title="Open Link"><i class="bi bi-box-arrow-up-right"></i></a>
                                <?php endif; ?>

                                <?php if ($bm['uploader_id'] == $user['id'] || ($user['role'] ?? '') === 'admin'): ?>
                                <form action="../actions/student/delete_resource.php" method="POST" class="d-inline" onsubmit="return confirm('Remove this resource permanently?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="resource_id" value="<?= (int)$bm['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm border" type="submit" title="Remove Resource">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
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
<script src="../assets/js/script.js?v=<?= time() ?>"></script>
<script>
// Bookmark remove via AJAX
document.querySelectorAll('.bookmark-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
        const resourceId = this.dataset.resourceId;
        const action = this.dataset.action;
        const card = this.closest('.col-md-6');
        try {
            const fd = new FormData();
            fd.append('resource_id', resourceId);
            fd.append('action', action);
            const res = await fetch('../actions/student/bookmark.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success && action === 'remove') {
                card.style.opacity = '0';
                card.style.transition = 'opacity 0.3s';
                setTimeout(() => card.remove(), 300);
            }
        } catch(e) { console.error(e); }
    });
});
</script>
</body>
</html>
