<?php
require_once __DIR__ . '/config/session.php';

// Redirect logged-in users to their dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: " . base_url("/pages/admin/dashboard.php"));
    } else {
        header("Location: " . base_url("/pages/student/dashboard.php"));
    }
    exit();
}

$pageTitle = 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Issue Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo base_url("/css/style.css"); ?>" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1><i class="bi bi-megaphone-fill me-3"></i>Campus Issue Tracker</h1>
        <p class="lead mt-3">Report campus issues easily. Track their resolution in real time.</p>
        <div class="mt-4">
            <a href="<?php echo base_url("/register.php"); ?>" class="btn btn-light btn-lg me-2">
                <i class="bi bi-person-plus me-1"></i>Register
            </a>
            <a href="<?php echo base_url("/login.php"); ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<main class="container py-5">
    <h2 class="text-center mb-5">How It Works</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="card-title">Submit Complaint</h5>
                    <p class="card-text text-muted">Students can easily submit complaints about lab, classroom, hostel, library, or other campus issues.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <h5 class="card-title">Track Status</h5>
                    <p class="card-text text-muted">Monitor the progress of your complaints in real time: Pending, In Progress, or Done.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                    <h5 class="card-title">View Timeline</h5>
                    <p class="card-text text-muted">See the complete timeline of each complaint for full transparency.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-image"></i></div>
                    <h5 class="card-title">Attach Images</h5>
                    <p class="card-text text-muted">Upload photos to provide visual evidence with your complaints.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                    <h5 class="card-title">Secure Access</h5>
                    <p class="card-text text-muted">Separate student and admin panels ensure secure role-based access control.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon"><i class="bi bi-speedometer2"></i></div>
                    <h5 class="card-title">Admin Dashboard</h5>
                    <p class="card-text text-muted">Admins get a comprehensive dashboard to manage and resolve all complaints efficiently.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> Campus Issue Tracker. All rights reserved.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
