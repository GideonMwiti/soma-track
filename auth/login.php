<?php
/**
 * SomaTrack - User Login
 */
$pageTitle = 'Sign In';
$bodyClass = 'auth-page';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/user/dashboard.php');
    exit;
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $oldEmail = $email;

    if (empty($email) || empty($password)) {
        $errors[] = 'Please fill in all fields.';
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            setUserSession($user);

            // Update last activity
            $stmt = $db->prepare("UPDATE users SET last_activity_date = CURDATE() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Redirect to intended page or dashboard
            $redirect = $_SESSION['redirect_url'] ?? '';
            unset($_SESSION['redirect_url']);
            
            if (empty($redirect) || strpos($redirect, '/auth/') !== false || $redirect === '/') {
                $redirect = ($user['role'] === 'admin') ? SITE_URL . '/admin/dashboard.php' : SITE_URL . '/user/dashboard.php';
            }
            
            header('Location: ' . $redirect);
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="<?= SITE_URL ?>/" class="text-decoration-none">
                <div class="st-logo-container d-inline-flex">
                    <div class="st-logo-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="st-logo-text text-start">
                        <span class="st-logo-name">Soma Track</span>
                        <span class="st-logo-tagline">Built by Learners For Learners</span>
                    </div>
                </div>
            </a>
        </div>
        <p class="auth-subtitle">Welcome back! Sign in to continue</p>

        <?= displayFlash() ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?= sanitize($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="st-form-label">Email</label>
                <input type="email" name="email" class="form-control st-form-control" placeholder="your@email.com" value="<?= sanitize($oldEmail) ?>" required>
            </div>
            <div class="mb-4">
                <label class="st-form-label">Password</label>
                <input type="password" name="password" class="form-control st-form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-st-primary w-100 py-3 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="auth-divider"><span>New to SomaTrack?</span></div>
        <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-st-secondary w-100">Create Account</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
