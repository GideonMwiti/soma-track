<?php
/**
 * SomaTrack - User Registration
 */
$pageTitle = 'Create Account';
$bodyClass = 'auth-page';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/user/dashboard.php');
    exit;
}

$errors = [];
$old = ['username' => '', 'email' => '', 'full_name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    }

    $username  = trim($_POST['username'] ?? '');
    $username  = '@' . ltrim($username, '@'); // Ensure exactly one @ at the start
    $email     = trim($_POST['email'] ?? '');
    $fullName  = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['password_confirm'] ?? '';

    $old = ['username' => $username, 'email' => $email, 'full_name' => $fullName];

    // Validate
    if ($username === '@' || strlen($username) < 4 || strlen($username) > 50) {
        $errors[] = 'Username must be between 3 and 49 characters (excluding @).';
    }
    if (!preg_match('/^@[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores after the @ symbol. Only one @ is allowed.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($fullName) || strlen($fullName) > 100) {
        $errors[] = 'Full name is required and must be under 100 characters.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $db = getDB();

        // Check existing username/email
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hash, $fullName]);

            setFlash('success', 'Account created successfully! Please log in.');
            header('Location: ' . SITE_URL . '/auth/login.php');
            exit;
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
        <p class="auth-subtitle">Start your learning journey today</p>

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
                <label class="st-form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control st-form-control" placeholder="John Doe" value="<?= sanitize($old['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="st-form-label">Username</label>
                <input type="text" name="username" class="form-control st-form-control" placeholder="Input username" value="<?= sanitize($old['username']) ?>" required>
                <small class="text-muted d-block mt-1">Example: @learner_123</small>
            </div>
            <div class="mb-3">
                <label class="st-form-label">Email</label>
                <input type="email" name="email" class="form-control st-form-control" placeholder="john@example.com" value="<?= sanitize($old['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="st-form-label">Password</label>
                <input type="password" name="password" class="form-control st-form-control" placeholder="Minimum 8 characters" required>
            </div>
            <div class="mb-4">
                <label class="st-form-label">Confirm Password</label>
                <input type="password" name="password_confirm" class="form-control st-form-control" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn btn-st-primary w-100 py-3 mb-3">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="auth-divider"><span>Already have an account?</span></div>
        <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-st-secondary w-100">Sign In</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
