    <!-- Modern Footer -->
    <?php if (isset($showFullFooter) && $showFullFooter): ?>
    <footer class="st-main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="st-logo-container mb-4">
                        <div class="st-logo-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="st-logo-text">
                            <span class="st-logo-name">Soma Track</span>
                            <span class="st-logo-tagline">Built by Learners For Learners</span>
                        </div>
                    </div>
                    <p style="max-width: 300px;">SomaTrack is the modern platform for collaborative learning. Document your progress, share your journey, and build your digital proof-of-work.</p>
                    <div class="st-social-links">
                        <a href="#" class="st-social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="st-social-icon"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="st-social-icon"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="st-social-icon"><i class="bi bi-github"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2 ms-lg-auto">
                    <h5 class="st-footer-title">Platform</h5>
                    <ul class="st-footer-links">
                        <li><a href="<?= SITE_URL ?>/explore.php">Explore Paths</a></li>
                        <li><a href="<?= SITE_URL ?>/auth/register.php">Start Journey</a></li>
                        <li><a href="<?= SITE_URL ?>/auth/register.php">Join Community</a></li>
                        <li><a href="<?= SITE_URL ?>/#how-it-works">Roadmap Tools</a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-2">
                    <h5 class="st-footer-title">Company</h5>
                    <ul class="st-footer-links">
                        <li><a href="<?= SITE_URL ?>/#philosophy">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/#philosophy">Our Philosophy</a></li>
                        <li><a href="<?= SITE_URL ?>/#contact">Contact Support</a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-2">
                    <h5 class="st-footer-title">Legal</h5>
                    <ul class="st-footer-links">
                        <li><a href="<?= SITE_URL ?>/terms.php">Terms of Service</a></li>
                        <li><a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/cookies.php">Cookie Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/terms.php">User Agreement</a></li>
                    </ul>
                </div>
            </div>
            <div class="st-footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0 small">&copy; <?= date('Y') ?> SomaTrack. Built for learners, by learners.</p>
                <div class="d-flex gap-4 small">
                    <span class="text-white">Built with <i class="bi bi-heart-fill text-danger mx-1"></i> at Kisii University</span>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>
    <?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>
