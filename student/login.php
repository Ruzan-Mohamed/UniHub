<?php
// student/login.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in → redirect to dashboard
if (isStudentLoggedIn()) {
    redirect('dashboard.php');
}

$csrf = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Access the UniHub Student Portal. Enter credentials to view courses, grades, notices, and upload resources.">
    <title>Sign In - UniHub</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link href="../assets/css/style.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body class="bg-light d-flex flex-column min-height-vh-100" style="min-height: 100vh;">

   
    <header class="auth-logo-header d-flex justify-content-between align-items-center shadow-sm">
        <a href="../index.php" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-mortarboard-fill fs-4"></i>
            </div>
            <span class="fs-4 fw-bold text-primary" style="letter-spacing: -0.5px;">Uni Hub</span>
        </a>
        <div class="d-flex gap-2">
            <a href="../admin/login.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-shield-lock me-1"></i> Admin Portal</a>
            <a href="../index.php" class="btn btn-light btn-sm rounded-pill px-3 py-1.5 fw-semibold border"><i class="bi bi-house-door me-1"></i> Homepage</a>
        </div>
    </header>

    <?php renderFlash(); ?>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3 my-4">
        <div class="auth-split-card d-flex flex-column flex-lg-row">

            <div class="auth-blue-panel col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-5">
                    <div class="bg-white text-primary rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                    </div>
                    <span class="fs-5 fw-bold text-white">Uni Hub</span>
                </div>

                <h1 class="display-5 fw-bold mb-4 text-white" style="line-height: 1.2;">
                    The Central Hub<br>for<br><span style="color: #67e8f9;">Academic Excellence.</span>
                </h1>
                
                <p class="text-white-50 fs-6 mb-5" style="max-width: 420px; line-height: 1.6;">
                    Access thousands of resources, connect with your faculty, and stay updated with campus notices—all in one secure platform.
                </p>

                <div class="mt-auto">
                    <div class="d-inline-flex align-items-center gap-2 bg-black bg-opacity-25 px-3 py-2.5 rounded-3 border border-white border-opacity-10">
                        <i class="bi bi-info-circle-fill text-white fs-5"></i>
                        <span class="text-white small fw-medium">Verified University Credentials Required.</span>
                    </div>
                </div>
            </div>

           
            <div class="auth-form-panel col-lg-6">
                <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem;">Welcome Back !</h2>
                <p class="text-muted small mb-4">Enter your credentials to access your dashboard</p>

                <form id="studentLoginForm" action="../actions/student/login.php" method="POST">
                    <?= csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label small fw-semibold text-dark">University Email :</label>
                        <input type="email" class="form-control bg-light border-0 py-2.5 px-3" id="loginEmail" name="email" placeholder="itt2024001@tec.rjt.ac.lk" required style="border-radius: 6px;">
                    </div>

                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="loginPassword" class="form-label small fw-semibold text-dark mb-0">Password :</label>
                            <a href="#" class="small text-decoration-none fw-semibold" style="color: #2563eb;">Forgot Password</a>
                        </div>
                        <input type="password" class="form-control bg-light border-0 py-2.5 px-3" id="loginPassword" name="password" placeholder="********" required style="border-radius: 6px;">
                    </div>

                    
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberCheck" name="remember" style="border-radius: 4px;">
                        <label class="form-check-label small text-dark fw-medium" for="rememberCheck">Remember me for 30 days</label>
                    </div>

                    
                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 mb-4" style="border-radius: 6px;">
                        Sign In <i class="bi bi-arrow-right"></i>
                    </button>

                    
                    <div class="text-center small mb-3">
                        <span class="text-muted fw-semibold">Don't have an account? </span>
                        <a href="register.php" class="text-decoration-none fw-bold" style="color: #2563eb;">Create an account</a>
                    </div>

                    <hr class="my-3 border-slate-200">

                    <div class="d-flex justify-content-between align-items-center pt-1" style="font-size: 0.78rem;">
                        <a href="../admin/login.php" class="text-muted text-decoration-none fw-semibold"><i class="bi bi-shield-lock me-1 text-primary"></i> Admin Portal</a>
                        <a href="../index.php" class="text-muted text-decoration-none fw-semibold"><i class="bi bi-house-door me-1 text-primary"></i> Homepage</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    
    <footer class="text-center py-3 bg-white border-top border-slate-200 mt-auto">
        <span class="text-dark small fw-semibold" style="font-size: 0.75rem;">&copy; 2026 UniHub University Resource Management. All rights reserved.</span>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
