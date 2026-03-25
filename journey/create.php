<?php
/**
 * SomaTrack - Create / Edit Journey
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$editing = false;
$journey = null;

// Editing mode
if (isset($_GET['id'])) {
    $editing = true;
    $stmt = $db->prepare("SELECT * FROM journeys WHERE id = ? AND user_id = ?");
    $stmt->execute([(int)$_GET['id'], $userId]);
    $journey = $stmt->fetch();
    if (!$journey) {
        setFlash('danger', 'Journey not found.');
        header('Location: ' . SITE_URL . '/user/journeys.php');
        exit;
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$errors = [];
$old = [
    'title'       => $journey['title'] ?? '',
    'description' => $journey['description'] ?? '',
    'category_id' => $journey['category_id'] ?? '',
    'visibility'  => $journey['visibility'] ?? 'public',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    }

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $visibility  = in_array($_POST['visibility'] ?? '', ['public', 'private']) ? $_POST['visibility'] : 'public';

    $old = ['title' => $title, 'description' => $description, 'category_id' => $categoryId, 'visibility' => $visibility];

    if (empty($title) || strlen($title) > 255) {
        $errors[] = 'Title is required and must be under 255 characters.';
    }

    $slug = generateSlug($title);

    // Check duplicate slug for this user
    if (empty($errors)) {
        $checkSql = "SELECT id FROM journeys WHERE user_id = ? AND slug = ?";
        $checkParams = [$userId, $slug];
        if ($editing) {
            $checkSql .= " AND id != ?";
            $checkParams[] = $journey['id'];
        }
        $check = $db->prepare($checkSql);
        $check->execute($checkParams);
        if ($check->fetch()) {
            $slug .= '-' . time();
        }
    }

    if (empty($errors)) {
        if ($editing) {
            $stmt = $db->prepare("UPDATE journeys SET title = ?, slug = ?, description = ?, category_id = ?, visibility = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $slug, $description, $categoryId, $visibility, $journey['id'], $userId]);
            setFlash('success', 'Journey updated!');
            header('Location: ' . SITE_URL . '/journey/view.php?id=' . $journey['id']);
        } else {
            $stmt = $db->prepare("INSERT INTO journeys (user_id, title, slug, description, category_id, visibility) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $slug, $description, $categoryId, $visibility]);
            $newId = $db->lastInsertId();
            setFlash('success', 'Journey created! Add steps to get started.');
            header('Location: ' . SITE_URL . '/journey/view.php?id=' . $newId);
        }
        exit;
    }
}

$pageTitle = $editing ? 'Edit Journey' : 'Create Journey';
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="st-page-header">
    <h1 class="st-page-title"><?= $editing ? 'Edit Journey' : 'Create New Journey' ?></h1>
    <p class="st-page-subtitle"><?= $editing ? 'Update your learning path details' : 'Define a structured learning path' ?></p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="st-card">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <?= csrfField() ?>
                <div class="mb-3">
                    <label class="st-form-label">Journey Title *</label>
                    <input type="text" name="title" class="form-control st-form-control" placeholder="e.g., Full-Stack Web Development in 30 Days" value="<?= sanitize($old['title']) ?>" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="st-form-label">Description</label>
                    <textarea name="description" class="form-control st-form-control" rows="4" placeholder="What will you learn? What's the goal?" data-char-counter maxlength="2000"><?= sanitize($old['description']) ?></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="st-form-label">Category</label>
                        <select name="category_id" class="form-select st-form-control">
                            <option value="">Select category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $old['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="st-form-label">Visibility</label>
                        <select name="visibility" class="form-select st-form-control">
                            <option value="public" <?= $old['visibility'] === 'public' ? 'selected' : '' ?>>🌍 Public - Anyone can see</option>
                            <option value="private" <?= $old['visibility'] === 'private' ? 'selected' : '' ?>>🔒 Private - Only you</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-st-primary"><i class="bi bi-check-circle me-2"></i><?= $editing ? 'Update Journey' : 'Create Journey' ?></button>
                    <a href="<?= SITE_URL ?>/user/journeys.php" class="btn btn-st-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="st-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
            <ul class="text-muted mb-0" style="font-size:0.85rem;line-height:1.8;">
                <li>Use a clear, descriptive title</li>
                <li>Public journeys can be cloned by others</li>
                <li>Add steps after creating the journey</li>
                <li>Each step can have daily logs</li>
                <li>Consistent logging builds your streak!</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
