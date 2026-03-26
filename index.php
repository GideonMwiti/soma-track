<?php
/**
 * SomaTrack - Landing Page
 */
$pageTitle = 'Home';
$showFullFooter = true;
require_once __DIR__ . '/includes/header.php';

$errors = [];
$success = false;

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || strlen($name) > 100) {
        $errors[] = 'Name is required and must be under 100 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($subject) || strlen($subject) > 200) {
        $errors[] = 'Subject is required and must be under 200 characters.';
    }
    if (empty($message) || strlen($message) < 10 || strlen($message) > 5000) {
        $errors[] = 'Message must be between 10 and 5000 characters.';
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']])) {
            $success = true;
        } else {
            $errors[] = 'Failed to send message. Please try again.';
        }
    }
}

// Get featured journeys for display (Cached for 1 hour)
$db = getDB();
$featured = getCache('featured_journeys');

if ($featured === null) {
    // 1. Try to get featured journeys
    $featuredStmt = $db->query("SELECT j.*, u.username, u.avatar, c.name AS category_name 
        FROM journeys j 
        JOIN users u ON j.user_id = u.id 
        LEFT JOIN categories c ON j.category_id = c.id 
        WHERE j.visibility = 'public' AND j.is_featured = 1 
        ORDER BY j.view_count DESC LIMIT 6");
    $featured = $featuredStmt->fetchAll();

    // 2. Fallback: If no featured, get top viewed public journeys
    if (empty($featured)) {
        $popStmt = $db->query("SELECT j.*, u.username, u.avatar, c.name AS category_name 
            FROM journeys j 
            JOIN users u ON j.user_id = u.id 
            LEFT JOIN categories c ON j.category_id = c.id 
            WHERE j.visibility = 'public' 
            ORDER BY j.view_count DESC LIMIT 3");
        $featured = $popStmt->fetchAll();
    }
    
    setCache('featured_journeys', $featured, 3600);
}

$categories = getCache('categories_list');
if ($categories === null) {
    $catStmt = $db->query("SELECT * FROM categories ORDER BY name");
    $categories = $catStmt->fetchAll();
    setCache('categories_list', $categories, 86400); // 24 hours
}

// Get stats for landing page (Cached for 1 hour)
$stats = getCache('landing_stats');
if ($stats === null) {
    $totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    $totalJourneys = $db->query("SELECT COUNT(*) FROM journeys WHERE visibility='public'")->fetchColumn();
    $stats = [
        'totalUsers' => $totalUsers,
        'totalJourneys' => $totalJourneys
    ];
    setCache('landing_stats', $stats, 3600);
} else {
    $totalUsers = $stats['totalUsers'];
    $totalJourneys = $stats['totalJourneys'];
}
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
                <li class="nav-item"><a class="nav-link" href="#how-it-works">How it Works</a></li>
                <li class="nav-item"><a class="nav-link" href="#trending">Learning Paths</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/user/dashboard.php" class="btn btn-outline-info px-4">Dashboard</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/auth/login.php">Sign In</a></li>
                    <li class="nav-item"><a href="#contact" class="btn btn-st-primary px-4">Get in Touch</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="st-hero" style="background: #0a0a1a;">
    <div class="container py-5">
        <div class="row min-vh-75 align-items-center text-start">
            <div class="col-lg-8" style="z-index:2;">
                <h1 class="st-hero-title display-3 fw-bold mb-4">
                    Stop Learning in <span>Isolation.</span>
                </h1>
                <p class="st-hero-subtitle fs-4 mb-5" style="max-width: 800px;">
                    Turn your daily study habits into shareable roadmaps. Clone proven learning paths, sync updates, and build your digital proof-of-work.
                </p>
                <div class="d-flex gap-3 flex-column flex-md-row">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= SITE_URL ?>/user/dashboard.php" class="btn btn-st-primary btn-lg px-5 py-3">
                            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-st-primary btn-lg px-5 py-3 shadow-lg text-nowrap">
                            Start Your Journey Free
                        </a>
                        <a href="<?= SITE_URL ?>/explore.php" class="btn btn-outline-light btn-lg px-5 py-3 text-nowrap">
                            Explore Paths
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Stats -->
                <div class="d-flex gap-5 mt-5 flex-wrap">
                    <div class="text-start">
                        <div class="h2 fw-bold mb-0 text-white"><?= number_format($totalUsers) ?></div>
                        <small class="text-secondary text-uppercase tracking-wider">Learners</small>
                    </div>
                    <div class="text-start">
                        <div class="h2 fw-bold mb-0 text-white"><?= number_format($totalJourneys) ?></div>
                        <small class="text-secondary text-uppercase tracking-wider">Journeys</small>
                    </div>
                    <div class="text-start">
                        <div class="h2 fw-bold mb-0 text-white"><?= count($categories) ?></div>
                        <small class="text-secondary text-uppercase tracking-wider">Categories</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

            <div class="col-lg-12">
                <div class="st-brand-section">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="st-brand-card">
                                <div class="st-brand-icon"><i class="bi bi-eye"></i></div>
                                <h3 class="st-brand-title">Our Vision</h3>
                                <p class="st-brand-text">To become the global standard for verifiable learning and collaborative skill building, where every learner's journey is a proof of expertise.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="st-brand-card">
                                <div class="st-brand-icon"><i class="bi bi-bullseye"></i></div>
                                <h3 class="st-brand-title">Our Mission</h3>
                                <p class="st-brand-text">We empower individuals to document their growth, share their knowledge, and connect with a community that values progress over static credentials.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="st-brand-card">
                                <div class="st-brand-icon"><i class="bi bi-compass"></i></div>
                                <h3 class="st-brand-title">Our Philosophy</h3>
                                <p class="st-brand-text">Learning is a journey, not a destination. We believe in transparency, consistency, and the power of collaborative roadmaps.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section id="how-it-works" class="py-5" style="background: var(--st-dark-card);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">How it <span style="color: var(--st-secondary);">Works</span></h2>
            <p class="text-muted mx-auto" style="max-width:600px;">Three simple steps to build your modern learning portfolio.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="st-feature-card">
                    <div class="st-feature-icon" style="background:var(--st-primary);"><i class="bi bi-bezier2 text-white"></i></div>
                    <h5 class="fw-bold mb-2">Clone & Sync</h5>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Pick a path, clone it, and stay updated as the creator adds new milestones.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="st-feature-card">
                    <div class="st-feature-icon" style="background:var(--st-secondary);"><i class="bi bi-list-check text-white"></i></div>
                    <h5 class="fw-bold mb-2">Daily Logging</h5>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Document your daily wins with snippets, links, and proof of progress.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="st-feature-card">
                    <div class="st-feature-icon" style="background:#6c5ce7;"><i class="bi bi-shield-check text-white"></i></div>
                    <h5 class="fw-bold mb-2">Proof of Work</h5>
                    <p class="text-muted mb-0" style="font-size:0.9rem;">Generate a professional portfolio that validates your skills to the world.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Journeys Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Trending <span style="color: var(--st-secondary);">Learning Paths</span></h2>
            <p class="text-muted">Top cloned roadmaps by the community</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (empty($featured)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No public learning paths available yet. Be the first to create one!</p>
                </div>
            <?php else: ?>
                <?php foreach ($featured as $j): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="st-journey-card">
                        <div class="card-header-gradient" style="background: var(--st-gradient-1);"></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="card-meta mb-0 d-flex align-items-center gap-2">
                                    <div class="st-avatar-initial" style="width:24px;height:24px;font-size:0.75rem;">
                                        <?= substr(sanitize($j['username']), 0, 1) ?>
                                    </div>
                                    <span class="small"><?= sanitize($j['username']) ?></span>
                                </div>
                                <span class="st-badge st-badge-cloned animate-pulse">
                                    <i class="bi bi-fire me-1"></i> <?= number_format($j['clone_count']) ?> clones
                                </span>
                            </div>
                            <h5 class="card-title"><?= sanitize($j['title']) ?></h5>
                            <p class="text-muted mb-3" style="font-size:0.85rem;"><?= truncateText(sanitize($j['description'] ?? ''), 100) ?></p>
                            <?php if (isLoggedIn() && getCurrentUserId() == $j['user_id']): ?>
                                <div class="st-progress mb-2"><div class="st-progress-bar" style="width:<?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%"></div></div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php if (isLoggedIn() && getCurrentUserId() == $j['user_id']): ?>
                                        <?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?> steps
                                    <?php else: ?>
                                        <?= $j['total_steps'] ?> steps
                                    <?php endif; ?>
                                </small>
                                <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3"><?= getJourneyActionText($j['user_id'], $j['completed_steps'], isLoggedIn()) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/explore.php" class="btn btn-st-primary px-5"><i class="bi bi-compass me-2"></i>Explore All Journeys</a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="st-contact-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Get in <span style="color: var(--st-secondary);">Touch</span></h2>
            <p class="text-muted mx-auto" style="max-width:600px;">Have questions or feedback? We'd love to hear from you. Our team is here to support your learning journey.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
                <i class="bi bi-check-circle me-2"></i><strong>Success!</strong> Your message has been sent successfully. We will get back to you soon.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 ps-3 mt-2">
                    <?php foreach ($errors as $e): ?>
                        <li><?= sanitize($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <div class="col-lg-7">
                <div class="st-contact-card h-100">
                    <form method="POST" action="#contact">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="st-form-label">Your Name</label>
                                <input type="text" name="name" class="form-control st-form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="st-form-label">Email Address</label>
                                <input type="email" name="email" class="form-control st-form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="st-form-label">Subject</label>
                                <input type="text" name="subject" class="form-control st-form-control" placeholder="How can we help?" required>
                            </div>
                            <div class="col-12">
                                <label class="st-form-label">Message</label>
                                <textarea name="message" class="form-control st-form-control" rows="5" placeholder="Your message here..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-st-primary btn-lg px-5">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="st-contact-card h-100 d-flex flex-column">
                    <div class="st-map-container mb-3" style="min-height: 200px; flex: 1;">
                        <!-- Kisii University Map -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15957.947230230537!2d34.7735311!3d-0.704285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182b26006f12255d%3A0xe5a39626e2e584f2!2sKisii%20University!5e0!3m2!1sen!2ske!4v1620000000000!5m2!1sen!2ske" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class="mb-4 text-center">
                        <a href="https://www.google.com/maps?q=Kisii+University" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Expand Map
                        </a>
                    </div>
                    <div class="st-contact-info-wrapper mt-auto">
                        <div class="st-contact-item">
                            <div class="st-contact-icon"><i class="bi bi-geo-alt"></i></div>
                            <div class="st-contact-text">
                                <span class="fw-bold text-white small text-uppercase">Our Location</span>
                                <span class="text-muted small">Kisii University, Kisii-Kilgoris Road, Kisii, Kenya</span>
                            </div>
                        </div>
                        
                        <div class="st-contact-item">
                            <div class="st-contact-icon"><i class="bi bi-envelope"></i></div>
                            <div class="st-contact-text">
                                <span class="fw-bold text-white small text-uppercase">Email Us</span>
                                <span class="text-muted small">hello@somatrack.com</span>
                            </div>
                        </div>
                        
                        <div class="st-contact-item">
                            <div class="st-contact-icon"><i class="bi bi-telephone"></i></div>
                            <div class="st-contact-text">
                                <span class="fw-bold text-white small text-uppercase">Call Support</span>
                                <span class="text-muted small">+254 700 000 000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: var(--st-dark-card); border-top:1px solid var(--st-dark-border);">
    <div class="container py-5 text-center">
        <h2 class="fw-bold mb-3 display-5">Start Your <span style="color: var(--st-secondary);">Learning Odyssey</span> Today</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width:600px; font-size:1.1rem;">Ready to stop learning in isolation? Join a community of active learners documenting their daily progress and building verifiable expertise.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-st-primary btn-lg px-5"><i class="bi bi-rocket me-2"></i>Create Free Account</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/user/journeys.php" class="btn btn-st-primary btn-lg px-5"><i class="bi bi-plus-circle me-2"></i>Create a Journey</a>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
