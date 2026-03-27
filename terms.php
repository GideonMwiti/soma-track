<?php
/**
 * SomaTrack - Terms of Service
 */
$pageTitle = 'Terms of Service';
$showFullFooter = true;
require_once __DIR__ . '/includes/header.php';
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: #0a0a1a; border-bottom: 1px solid var(--st-dark-border);">
    <div class="container">
        <a class="navbar-brand" href="<?= SITE_URL ?>/">
            <div class="st-logo-container">
                <div class="st-logo-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="st-logo-text">
                    <span class="st-logo-name">Soma Track</span>
                    <span class="st-logo-tagline">Built by Learners For Learners</span>
                </div>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/#how-it-works">How it Works</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/#trending">Learning Paths</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/user/dashboard.php" class="btn btn-outline-info px-4">Dashboard</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/auth/login.php">Sign In</a></li>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/#contact" class="btn btn-st-primary px-4">Get in Touch</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 mt-5">
    <div class="row justify-content-center pt-5">
        <div class="col-lg-8" style="background: var(--st-dark-card); border: 1px solid var(--st-dark-border); border-radius: 12px; padding: 40px;">
            <h1 class="fw-bold mb-4" style="color: var(--st-text-main);">Terms of Service</h1>
            <p class="text-muted mb-4">Last Updated: <?= date('F j, Y') ?></p>
            
            <div style="color: var(--st-text-main); font-size: 0.95rem; line-height: 1.7;">
                <h4 class="mt-4 mb-3" style="color: #fff;">1. Acceptance of Terms</h4>
                <p>Welcome to SomaTrack! By accessing or using our platform, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">2. User Accounts</h4>
                <p>You must create an account to access certain features like creating and cloning learning journeys. You agree to provide accurate, complete, and updated registration information. You are responsible for safeguarding your login credentials.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">3. Acceptable Use</h4>
                <p>You agree to use SomaTrack exclusively for lawful educational and collaborative purposes. You must not:
                    <ul class="mt-2 text-muted">
                        <li>Post discriminatory, highly offensive, or hateful content.</li>
                        <li>Attempt to compromise the security of the platform.</li>
                        <li>Plagiarize the intellectual property of others.</li>
                    </ul>
                </p>

                <h4 class="mt-4 mb-3" style="color: #fff;">4. Intellectual Property & User Content</h4>
                <p>By creating or sharing public roadmaps, logs, and comments ("User Content"), you grant SomaTrack a non-exclusive, worldwide, royalty-free license to display, host, and distribute your User Content on the platform. You retain full ownership over the content you create.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">5. Termination</h4>
                <p>We reserve the right to suspend or terminate your account without notice if we have reasonable belief that you have violated these terms. We want a safe environment for all learners.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">6. Changes to Terms</h4>
                <p>We reserve the right to modify these terms at any time. We will notify you of material changes. Continued use of the platform after changes have been posted constitutes your acceptance of the updated terms.</p>
                
                <hr class="my-5" style="border-color: var(--st-dark-border);">
                <p class="text-center">If you have any questions regarding these terms, please feel free to <a href="<?= SITE_URL ?>/#contact" style="color: var(--st-primary-light);">Contact Support</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
