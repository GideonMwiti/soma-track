<?php
/**
 * SomaTrack - Notifications Page
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
$db = getDB();
$userId = getCurrentUserId();

// Mark all as read
if (isset($_GET['mark_read'])) {
    $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$userId]);
    setFlash('success', 'All notifications marked as read.');
    redirect(SITE_URL . '/user/notifications.php');
}

require_once __DIR__ . '/../includes/dashboard_header.php';

$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

$typeIcons = [
    'sync_update' => ['bi-arrow-repeat', 'rgba(253,203,110,0.15)', 'var(--st-warning)'],
    'comment'     => ['bi-chat-dots', 'rgba(116,185,255,0.15)', 'var(--st-info)'],
    'aha_vote'    => ['bi-lightbulb', 'rgba(253,203,110,0.15)', 'var(--st-warning)'],
    'badge'       => ['bi-award', 'rgba(253,121,168,0.15)', 'var(--st-accent)'],
    'clone'       => ['bi-copy', 'rgba(0,184,148,0.15)', 'var(--st-success)'],
    'system'      => ['bi-info-circle', 'rgba(108,92,231,0.15)', 'var(--st-primary-light)'],
];
?>

<div class="st-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="st-page-title"><i class="bi bi-bell me-2"></i>Notifications</h1>
        <p class="st-page-subtitle">Stay updated on your learning journey</p>
    </div>
    <?php if (!empty($notifications)): ?>
        <a href="?mark_read=1" class="btn btn-st-secondary btn-sm"><i class="bi bi-check-all me-1"></i>Mark All Read</a>
    <?php endif; ?>
</div>

<div class="st-card">
    <?php if (empty($notifications)): ?>
        <div class="st-empty-state py-4">
            <i class="bi bi-bell-slash d-block"></i>
            <h6 class="text-muted">No notifications yet</h6>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $n):
            $icon = $typeIcons[$n['type']] ?? $typeIcons['system'];
        ?>
        <a href="<?= $n['link'] ? sanitize($n['link']) : '#' ?>" class="st-notif-item text-decoration-none <?= !$n['is_read'] ? 'unread' : '' ?>"
           onclick="<?php if(!$n['is_read']): ?>fetch('<?= SITE_URL ?>/api/notifications.php?action=read&id=<?= $n['id'] ?>');<?php endif; ?>">
            <div class="st-notif-icon" style="background:<?= $icon[1] ?>;color:<?= $icon[2] ?>;">
                <i class="bi <?= $icon[0] ?>"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold" style="font-size:0.88rem;color:var(--st-text);"><?= sanitize($n['title']) ?></div>
                <div class="text-muted" style="font-size:0.8rem;"><?= sanitize($n['message']) ?></div>
                <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
            </div>
            <?php if (!$n['is_read']): ?>
                <span style="width:8px;height:8px;border-radius:50%;background:var(--st-primary);flex-shrink:0;"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
