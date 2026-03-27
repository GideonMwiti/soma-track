<?php
/**
 * SomaTrack - Privacy Policy
 */
$pageTitle = 'Privacy Policy';
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
            <h1 class="fw-bold mb-4" style="color: var(--st-text-main);">Privacy Policy</h1>
            <p class="text-muted mb-4">Last Updated: <?= date('F j, Y') ?></p>
            
            <div style="color: var(--st-text-main); font-size: 0.95rem; line-height: 1.7;">
                <p>At SomaTrack, we value your privacy. This policy outlines how we handle your data to ensure you have a safe and transparent learning experience.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">1. Information We Collect</h4>
                <p><strong>Personal Data:</strong> When you register on SomaTrack, we collect exactly what is required to provide the service: your chosen username, full name, and email address. Your password is securely hashed and never stored in plain text.</p>
                <p><strong>Usage Data:</strong> We track basic usage data such as your daily streaks, logs, comments, and the learning journeys you clone. This data allows us to generate features like heatmaps and statistics.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">2. How We Use Your Information</h4>
                <ul class="mt-2 text-muted">
                    <li>To maintain your account and personal learning progress.</li>
                    <li>To rank and display public "Journeys" based on metrics like views, clones, and completions.</li>
                    <li>To allow social interactions including commenting and sharing achievement badges.</li>
                </ul>

                <h4 class="mt-4 mb-3" style="color: #fff;">3. Sharing Your Information</h4>
                <p>We do <strong>not</strong> sell, trade, or rent your personal identification information to third parties. If you set a Journey to "public," it will be visible to other learners globally. "Private" journeys remain accessible only to you.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">4. Data Security</h4>
                <p>We employ standard industry security protocols (such as prepared PDO database statements, secure hashing algorithms, and CSRF tokens) to protect your account against unauthorized access or breaches.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">5. Your Rights</h4>
                <p>You have the right to request the deletion of your account and associated data. If you wish to wipe your data from SomaTrack systems, please contact our support team.</p>

                <hr class="my-5" style="border-color: var(--st-dark-border);">
                <p class="text-center">For any privacy-related inquiries, please <a href="<?= SITE_URL ?>/#contact" style="color: var(--st-primary-light);">Contact Us</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
