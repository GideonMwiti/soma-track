<?php
/**
 * SomaTrack - User Settings
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    }

    $section = $_POST['section'] ?? '';

    if ($section === 'profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $bio = trim($_POST['bio'] ?? '') ?: null;
        
        $username = trim($_POST['username'] ?? '');
        $username = '@' . ltrim($username, '@');

        if (empty($fullName)) $errors[] = 'Full name is required.';
        if ($username === '@' || strlen($username) < 4 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 49 characters (excluding @).';
        }
        if (!preg_match('/^@[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores after the @. Only one @ is allowed.';
        }
        
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $userId]);
            if ($stmt->fetch()) {
                $errors[] = 'Username is already taken.';
            }
        }

        if (empty($errors)) {
            $db->prepare("UPDATE users SET full_name = ?, username = ?, bio = ? WHERE id = ?")->execute([$fullName, $username, $bio, $userId]);
            $_SESSION['full_name'] = $fullName;
            setFlash('success', 'Profile updated!');
            redirect(SITE_URL . '/user/settings.php');
        }

    } elseif ($section === 'password') {
        $currentPw = $_POST['current_password'] ?? '';
        $newPw = $_POST['new_password'] ?? '';
        $confirmPw = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPw, $user['password_hash'])) $errors[] = 'Current password is incorrect.';
        if (strlen($newPw) < 8) $errors[] = 'New password must be at least 8 characters.';
        if ($newPw !== $confirmPw) $errors[] = 'Passwords do not match.';

        if (empty($errors)) {
            $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $userId]);
            setFlash('success', 'Password changed!');
            redirect(SITE_URL . '/user/settings.php');
        }
    }
}

$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-gear me-2"></i>Settings</h1>
    <p class="st-page-subtitle">Manage your account</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="st-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Profile</h5>
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="section" value="profile">
                <div class="mb-3">
                    <label class="st-form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control st-form-control" value="<?= sanitize($user['full_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">Username</label>
                    <input type="text" name="username" class="form-control st-form-control" value="<?= sanitize($user['username']) ?>" required>
                    <small class="text-muted d-block mt-1">Example: @learner_123</small>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">Bio</label>
                    <textarea name="bio" class="form-control st-form-control" rows="3" placeholder="Tell us about yourself..." data-char-counter maxlength="500"><?= sanitize($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">Email</label>
                    <input type="email" class="form-control st-form-control" value="<?= sanitize($user['email']) ?>" disabled>
                    <small class="text-muted">Email cannot be changed.</small>
                </div>
                <button type="submit" class="btn btn-st-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="st-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-lock me-2"></i>Change Password</h5>
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="section" value="password">
                <div class="mb-3">
                    <label class="st-form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control st-form-control" required>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control st-form-control" required>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control st-form-control" required>
                </div>
                <button type="submit" class="btn btn-st-primary"><i class="bi bi-key me-1"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
