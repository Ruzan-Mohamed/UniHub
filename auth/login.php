<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.2.0/css/line.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="nav-container">
            <a href="../index.php" class="navbar-brand">
                <img src="../images/logo/logo.png" alt="UniHub Logo"> UniHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse.navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="#" class="nav-link active">Sign In </a></li>
                    <li class="nav-item"><a href="./register.php" class="nav-link">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="login-form">
        <div>
            <div class="nav-brand">
                <img src="../images/logo/logo.png" alt="UniHub Logo">
                <h3>UniHub</h3>
            </div>

            <div>
                <h1>The Central Hub for <br> <span>Academic Excellence.</span></h1>
                <p>Access thousands of resources, connect with your faculty, and stay updated with campus notices—all in one secure platform.</p>
            </div>

            <div class="tooltip">
                <i class="uil uil-info-circle"></i> Verified University Credentials Required.
            </div>
        </div>

        <form action="#" method="post">
            <h2>Welcome Back!</h2>
            <p>Enter your credentials to access your dashboard</p>
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" placeholder="itt2024001@tec.rjt.ac.lk" required>

            <div class="password-label-row">
                <label for="email">Password: </label> 
                <a href="#">Forgot Password</a>
            </div>
            <input type="password" name="password" id="password" placeholder="********" required>

            <div>
                <input type="checkbox" name="remember" id="remember"> 
                <label for="remember">Remember me for 30 days</label>
            </div>
            
            <button type="submit" class="btn">Login <i class="uil uil-sign-in-alt"></i></button>

            <div class="divider">
                <span class="line"></span>
                <span class="divider-text">OR CONTINUE WITH</span>
                <span class="line"></span>
            </div>

            <button type="button"><i class="uil uil-google"></i> Google</button>

            <p>Don't have an Account? <a href="./register.php">Create an Account</a></p>
        </form>
    </section>

    <footer>
        <p>&copy; 2026 UniHub University Resource Management. All Rights Reserved</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
</body>
</html>