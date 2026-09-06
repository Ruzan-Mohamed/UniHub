<?php
// index.php — UniHub Landing Page
// Simple redirect wrapper that keeps .php extension throughout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to UniHub - University Student Management and Academic Support System. Access student dashboards, upload lecture slides, and monitor resources.">
    <title>UniHub - Centralized University Academic System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link href="assets/css/style.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body class="bg-light d-flex flex-column min-height-vh-100" style="min-height: 100vh;">


    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
                <span class="fs-4 fw-bold text-primary mb-0" style="letter-spacing: -0.5px; line-height: 1;">Uni Hub</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#homeNavbar" aria-controls="homeNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="homeNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link active fw-semibold" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="student/contact.php">Support</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 mt-2 mt-lg-0">
                    <a href="student/login.php" class="btn btn-light px-4 fw-semibold border btn-sm" style="border-radius: 6px; background-color: #f3f4f6; height: 38px; display: inline-flex; align-items: center;">Student Portal</a>
                    <a href="admin/login.php" class="btn btn-primary px-4 fw-semibold btn-sm" style="border-radius: 6px; height: 38px; display: inline-flex; align-items: center;">Admin Access</a>
                </div>
            </div>
        </div>
    </nav>


    <main class="flex-grow-1">

        <section class="hero-section d-flex align-items-center" style="padding: 90px 0 80px;">
            <div class="container text-center px-4">
                <div class="inline-badge mb-4 d-inline-block px-3 py-1 rounded-pill text-primary fw-semibold border border-primary border-opacity-25" style="background-color: #eff6ff; font-size: 0.85rem;">
                    🎓 Trusted by Rajarata University of Sri Lanka
                </div>
                <h1 class="hero-title display-4 fw-bold text-dark mb-4" style="max-width: 740px; margin: 0 auto; line-height: 1.2;">
                    Centralized Hub for <span style="color: #2563eb;">Academic</span> Excellence
                </h1>
                <p class="hero-subtitle text-muted fs-5 mb-5" style="max-width: 580px; margin: 0 auto; line-height: 1.7;">
                    UniHub connects students and faculty into one seamless platform — manage resources, receive notices, and track academic progress.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="student/register.php" class="btn btn-primary px-5 py-2.5 fw-bold btn-animate" style="border-radius: 8px; font-size: 1rem;">
                        Get Started <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="student/login.php" class="btn btn-light px-5 py-2.5 fw-bold border btn-animate" style="border-radius: 8px; font-size: 1rem;">
                        Sign In
                    </a>
                </div>
            </div>
        </section>


        <section id="features" class="py-5 bg-white">
            <div class="container px-4">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-dark mb-2">Everything You Need. In One Place.</h2>
                    <p class="text-muted">Built for the modern university experience</p>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="card border-0 p-4 text-center h-100" style="border-radius: 12px; background: #f8faff;">
                            <div class="bg-primary text-white rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 56px; height: 56px;"><i class="bi bi-journals fs-4"></i></div>
                            <h5 class="fw-bold mb-2">Resource Library</h5>
                            <p class="text-muted small mb-0">Access and share lecture slides, lab reports, and study notes uploaded by your peers and faculty.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 p-4 text-center h-100" style="border-radius: 12px; background: #f0fdf4;">
                            <div class="bg-success text-white rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 56px; height: 56px;"><i class="bi bi-bell-fill fs-4"></i></div>
                            <h5 class="fw-bold mb-2">Official Notices</h5>
                            <p class="text-muted small mb-0">Receive academic notices, exam timetables, and campus events instantly from the administration.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 p-4 text-center h-100" style="border-radius: 12px; background: #fffbeb;">
                            <div class="bg-warning text-dark rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 56px; height: 56px;"><i class="bi bi-shield-lock-fill fs-4"></i></div>
                            <h5 class="fw-bold mb-2">Secure Admin Portal</h5>
                            <p class="text-muted small mb-0">Powerful admin dashboard for registrars and staff to manage students, courses, and announcements.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="text-center py-4 bg-white border-top mt-auto">
        <span class="text-muted small fw-semibold" style="font-size: 0.8rem;">&copy; 2026 UniHub University Resource Management. All rights reserved.</span>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
