<?php
// student/register.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

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
    <meta name="description" content="Register a new student account on UniHub. Provide personal, contact, and academic ID credentials.">
    <title>Create Your Account - UniHub</title>
    
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

    
    <div class="container mt-5">
        <div class="stepper-container">
            <div class="step-node">
                <div class="step-circle active">1</div>
                <span class="small fw-bold text-dark mt-1">Account Details</span>
            </div>
            <div class="step-line"></div>
            <div class="step-node">
                <div class="step-circle">2</div>
                <span class="small fw-semibold text-muted mt-1">Email Verification</span>
            </div>
        </div>
    </div>

    
    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3 mb-5">
        <div class="card card-glass border-0 p-4 shadow-lg" style="max-width: 580px; width: 100%; border-radius: 16px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark mb-1">Create Your Account</h2>
                <p class="text-muted small">Join your universities collaborative network today</p>
            </div>

            <form id="registerForm" action="../actions/student/register.php" method="POST">
                <?= csrfField() ?>
               
                <div class="mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <i class="bi bi-person-fill text-primary"></i> Personal Details
                    </h5>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="regFirst" class="form-label small fw-semibold text-dark mb-1">First Name :</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="regFirst" name="first_name" placeholder="Alwis" required style="border-radius: 6px;">
                        </div>
                        <div class="col-6">
                            <label for="regLast" class="form-label small fw-semibold text-dark mb-1">Last Name :</label>
                            <input type="text" class="form-control bg-light border-0 py-2" id="regLast" name="last_name" placeholder="Perera" required style="border-radius: 6px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <label for="regEmail" class="form-label small fw-semibold text-dark mb-0">University Email :</label>
                            <span class="required-badge">REQUIRED</span>
                        </div>
                        <input type="email" class="form-control bg-light border-0 py-2" id="regEmail" name="email" placeholder="itt2024001@tec.rjt.ac.lk" pattern="[a-zA-Z0-9._%+-]+@tec\.rjt\.ac\.lk" title="Please enter a valid university email ending with @tec.rjt.ac.lk" required style="border-radius: 6px;">
                        <span class="form-text small text-muted" style="font-size: 0.7rem;">Must end with @tec.rjt.ac.lk or authorized domain</span>
                    </div>

                    <div class="mb-3">
                        <label for="regID" class="form-label small fw-semibold text-dark mb-1">Student ID Number</label>
                        <div class="input-group" style="border-radius: 6px; overflow: hidden;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-card-text text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" id="regID" name="student_id" placeholder="ITT/2024/001" required>
                        </div>
                    </div>
                </div>

                
                <div class="mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <i class="bi bi-shield-lock-fill text-primary"></i> Account Security
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="regPass" class="form-label small fw-semibold text-dark mb-1">Password :</label>
                            <div class="input-group" style="border-radius: 6px; overflow: hidden;">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" class="form-control bg-light border-0 py-2" id="regPass" name="password" placeholder="********" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="regConfirm" class="form-label small fw-semibold text-dark mb-1">Confirm Password :</label>
                            <div class="input-group" style="border-radius: 6px; overflow: hidden;">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" class="form-control bg-light border-0 py-2" id="regConfirm" name="confirm_password" placeholder="********" required>
                            </div>
                        </div>
                    </div>
                </div>

               
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="termsCheck" required style="border-radius: 4px;">
                    <label class="form-check-label small text-muted fw-semibold" for="termsCheck" style="line-height: 1.4;">
                        I agree to the <a href="#" class="text-decoration-none" style="color: #2563eb;">Terms of Service</a> and <a href="#" class="text-decoration-none" style="color: #2563eb;">Privacy Policy</a>. I confirm that the information provided is accurate and belongs to me.
                    </label>
                </div>

                
                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2 mb-4" style="border-radius: 6px;">
                    Create Account <i class="bi bi-arrow-right"></i>
                </button>

               
                <div class="text-center small mb-3">
                    <span class="text-muted fw-semibold">Already have an account? </span>
                    <a href="login.php" class="text-decoration-none fw-bold" style="color: #2563eb;">Sign In</a>
                </div>

                <hr class="my-3 border-slate-200">

                <div class="d-flex justify-content-between align-items-center pt-1" style="font-size: 0.78rem;">
                    <a href="../admin/login.php" class="text-muted text-decoration-none fw-semibold"><i class="bi bi-shield-lock me-1 text-primary"></i> Admin Portal</a>
                    <a href="../index.php" class="text-muted text-decoration-none fw-semibold"><i class="bi bi-house-door me-1 text-primary"></i> Homepage</a>
                </div>
            </form>
        </div>
    </main>

    
    <footer class="text-center py-3 bg-white border-top border-slate-200 mt-auto">
        <span class="text-dark small fw-semibold" style="font-size: 0.75rem;">&copy; 2026 UniHub University Resource Management. All rights reserved.</span>
    </footer>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
