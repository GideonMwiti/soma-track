<?php
/**
 * SomaTrack - Cookie Policy
 */
$pageTitle = 'Cookie Policy';
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
            <h1 class="fw-bold mb-4" style="color: var(--st-text-main);">Cookie Policy</h1>
            <p class="text-muted mb-4">Last Updated: <?= date('F j, Y') ?></p>
            
            <div style="color: var(--st-text-main); font-size: 0.95rem; line-height: 1.7;">
                <p>To make SomaTrack work properly, we drop small data files called "cookies" on your device. Most large websites do this too.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">1. What are cookies?</h4>
                <p>A cookie is a small text file that a website saves on your computer or mobile device when you visit the site. It enables the website to remember your actions and preferences over a period of time.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">2. How do we use cookies?</h4>
                <p>Unlike ad-heavy platforms, SomaTrack respects your privacy and primarily uses cookies purely for critical functional necessities.</p>
                <ul class="mt-2 text-muted">
                    <li><strong>Authentication:</strong> Keeping you logged in through PHP Session tokens while you browse the dashboard, explore, and leave comments.</li>
                    <li><strong>Security:</strong> Generating secure CSRF (Cross-Site Request Forgery) tokens so that hackers cannot send unwanted forms on your behalf.</li>
                </ul>

                <h4 class="mt-4 mb-3" style="color: #fff;">3. Third-party Cookies</h4>
                <p>We do not use advertising or tracking cookies such as Google Analytics, Meta Pixel, or other privacy-invasive trackers. SomaTrack is built explicitly for learners and only stores local session validation cookies.</p>

                <h4 class="mt-4 mb-3" style="color: #fff;">4. How to control cookies</h4>
                <p>You can control and/or delete cookies as you wish using your browser settings. You can wipe all cookies that are already on your computer and block them from being placed. If you do this, however, you will not be able to log into SomaTrack or post updates to your learning journeys.</p>

                <hr class="my-5" style="border-color: var(--st-dark-border);">
                <p class="text-center">By continuing to log into the platform, you acknowledge our use of essential session cookies.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
