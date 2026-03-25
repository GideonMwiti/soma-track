<?php
/**
 * SomaTrack - Contact Us Page
 */
$pageTitle = 'Contact Us';
$pageDesc = 'Get in touch with the SomaTrack team';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $old = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];

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
            setFlash('success', 'Your message has been sent successfully! We will get back to you soon.');
            header('Location: ' . SITE_URL . '/contact.php');
            exit;
        } else {
            $errors[] = 'Failed to send message. Please try again.';
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-2">Get in Touch</h1>
                <p class="text-muted fs-5">Have questions or feedback? We'd love to hear from you.</p>
            </div>

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

            <div class="st-card">
                <form method="POST">
                    <div class="mb-3">
                        <label class="st-form-label">Full Name</label>
                        <input type="text" name="name" class="form-control st-form-control" placeholder="Your name" value="<?= sanitize($old['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="st-form-label">Email Address</label>
                        <input type="email" name="email" class="form-control st-form-control" placeholder="your@email.com" value="<?= sanitize($old['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="st-form-label">Subject</label>
                        <input type="text" name="subject" class="form-control st-form-control" placeholder="What is this about?" value="<?= sanitize($old['subject']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="st-form-label">Message</label>
                        <textarea name="message" class="form-control st-form-control" rows="5" placeholder="Your message here..." required><?= sanitize($old['message']) ?></textarea>
                        <small class="text-muted">Minimum 10 characters, maximum 5000</small>
                    </div>
                    <button type="submit" class="btn btn-st-primary w-100 py-2">
                        <i class="bi bi-send me-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
