<?php
/**
 * SomaTrack - Admin: Moderate Comments
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
$db = getDB();
$adminId = getCurrentUserId();

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $commentId = (int)($_GET['id'] ?? 0);
    $token = $_GET['token'] ?? '';
    if (validateCSRFToken($token)) {
        $db->prepare("UPDATE step_comments SET is_deleted = 1 WHERE id = ?")->execute([$commentId]);
        $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address) VALUES (?, 'delete_comment', 'comment', ?, 'Comment moderated', ?)")
            ->execute([$adminId, $commentId, $_SERVER['REMOTE_ADDR']]);
        setFlash('success', 'Comment removed.');
    }
    redirect(SITE_URL . '/admin/comments.php');
}

// Restore
if (isset($_GET['action']) && $_GET['action'] === 'restore') {
    $commentId = (int)($_GET['id'] ?? 0);
    $token = $_GET['token'] ?? '';
    if (validateCSRFToken($token)) {
        $db->prepare("UPDATE step_comments SET is_deleted = 0 WHERE id = ?")->execute([$commentId]);
        setFlash('success', 'Comment restored.');
    }
    redirect(SITE_URL . '/admin/comments.php');
}

require_once __DIR__ . '/includes/admin_header.php';

$showDeleted = isset($_GET['deleted']);

if ($showDeleted) {
    $stmt = $db->query("SELECT sc.*, u.username, u.avatar, s.title AS step_title, j.title AS journey_title
        FROM step_comments sc JOIN users u ON sc.user_id = u.id JOIN steps s ON sc.step_id = s.id JOIN journeys j ON s.journey_id = j.id
        WHERE sc.is_deleted = 1 ORDER BY sc.created_at DESC LIMIT 50");
} else {
    $stmt = $db->query("SELECT sc.*, u.username, u.avatar, s.title AS step_title, j.title AS journey_title
        FROM step_comments sc JOIN users u ON sc.user_id = u.id JOIN steps s ON sc.step_id = s.id JOIN journeys j ON s.journey_id = j.id
        WHERE sc.is_deleted = 0 ORDER BY sc.created_at DESC LIMIT 50");
}
$comments = $stmt->fetchAll();
?>

<div class="st-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="st-page-title"><i class="bi bi-chat-dots me-2"></i>Moderate Comments</h1>
        <p class="st-page-subtitle"><?= $showDeleted ? 'Showing deleted comments' : 'Recent comments' ?></p>
    </div>
    <div>
        <?php if ($showDeleted): ?>
            <a href="<?= SITE_URL ?>/admin/comments.php" class="btn btn-st-secondary btn-sm"><i class="bi bi-eye me-1"></i>Active Comments</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/admin/comments.php?deleted" class="btn btn-st-secondary btn-sm"><i class="bi bi-trash me-1"></i>Deleted Comments</a>
        <?php endif; ?>
    </div>
</div>

<div class="st-card">
    <?php if (empty($comments)): ?>
        <div class="st-empty-state py-4">
            <i class="bi bi-chat d-block"></i>
            <h6 class="text-muted">No comments found</h6>
        </div>
    <?php else: ?>
        <?php foreach ($comments as $c): ?>
        <div class="st-comment">
            <div class="st-avatar-initial" style="width:36px;height:36px;font-size:0.9rem;">
                <?= substr(sanitize(!empty($c['full_name']) ? $c['full_name'] : $c['username']), 0, 1) ?>
            </div>
            <div class="flex-grow-1">
                <div class="st-comment-meta d-flex justify-content-between">
                    <div>
                        <strong><?= sanitize($c['username']) ?></strong>
                        on <em><?= sanitize($c['step_title']) ?></em> in <em><?= sanitize($c['journey_title']) ?></em>
                        · <?= timeAgo($c['created_at']) ?>
                    </div>
                    <div>
                        <?php if ($showDeleted): ?>
                            <a href="?action=restore&id=<?= $c['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</a>
                        <?php else: ?>
                            <a href="?action=delete&id=<?= $c['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete('Delete this comment?')"><i class="bi bi-trash me-1"></i>Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="st-comment-body"><?= nl2br(sanitize($c['content'])) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
