<?php
/**
 * SomaTrack - Admin Panel
 */
$pageTitle = 'Admin Panel';
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Stats
$totalUsers     = (int)$db->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$totalJourneys  = (int)$db->query("SELECT COUNT(*) FROM journeys")->fetchColumn();
$totalSteps     = (int)$db->query("SELECT COUNT(*) FROM steps")->fetchColumn();
$totalLogs      = (int)$db->query("SELECT COUNT(*) FROM daily_logs")->fetchColumn();
$totalComments  = (int)$db->query("SELECT COUNT(*) FROM step_comments WHERE is_deleted = 0")->fetchColumn();
$totalClones    = (int)$db->query("SELECT SUM(clone_count) FROM journeys")->fetchColumn();
$activeToday    = (int)$db->query("SELECT COUNT(*) FROM users WHERE last_activity_date = CURDATE() AND role != 'admin'")->fetchColumn();
$newUsersWeek   = (int)$db->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND role != 'admin'")->fetchColumn();

// Recent users
$recentUsers = $db->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Top journeys
$topJourneys = $db->query("SELECT j.*, u.username FROM journeys j JOIN users u ON j.user_id = u.id WHERE j.visibility = 'public' ORDER BY j.view_count DESC LIMIT 5")->fetchAll();

// Recent admin logs
$adminLogs = $db->query("SELECT al.*, u.username FROM admin_logs al JOIN users u ON al.admin_id = u.id ORDER BY al.created_at DESC LIMIT 10")->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h1>
    <p class="st-page-subtitle">Platform overview and management</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $statCards = [
        ['Users', $totalUsers, 'bi-people', 'rgba(108,92,231,0.15)', 'var(--st-primary-light)'],
        ['Journeys', $totalJourneys, 'bi-map', 'rgba(0,184,148,0.15)', 'var(--st-success)'],
        ['Steps', $totalSteps, 'bi-signpost-2', 'rgba(116,185,255,0.15)', 'var(--st-info)'],
        ['Daily Logs', $totalLogs, 'bi-journal-text', 'rgba(253,203,110,0.15)', 'var(--st-warning)'],
        ['Comments', $totalComments, 'bi-chat-dots', 'rgba(253,121,168,0.15)', 'var(--st-accent)'],
        ['Clones', $totalClones, 'bi-copy', 'rgba(0,206,201,0.15)', 'var(--st-secondary)'],
        ['Active Today', $activeToday, 'bi-activity', 'rgba(0,184,148,0.15)', 'var(--st-success)'],
        ['New (7d)', $newUsersWeek, 'bi-person-plus', 'rgba(108,92,231,0.15)', 'var(--st-primary-light)'],
    ];
    foreach ($statCards as $sc):
    ?>
    <div class="col-6 col-md-3">
        <div class="st-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="st-stat-value"><?= number_format($sc[1]) ?></div>
                    <div class="st-stat-label"><?= $sc[0] ?></div>
                </div>
                <div class="st-stat-icon" style="background:<?= $sc[3] ?>;color:<?= $sc[4] ?>;">
                    <i class="bi <?= $sc[2] ?>"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="st-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Recent Users</h5>
                <a href="<?= SITE_URL ?>/admin/users.php" class="btn btn-sm btn-st-secondary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="st-table">
                    <thead><tr><th>User</th><th>Joined</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                                        <?= substr(sanitize(!empty($u['full_name']) ? $u['full_name'] : $u['username']), 0, 1) ?>
                                    </div>
                                    <div>
                                          <div class="fw-semibold"><?= sanitize($u['full_name']) ?></div>
                                          <small class="text-muted"><?= sanitize($u['username']) ?> &bull; <?= sanitize($u['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= timeAgo($u['created_at']) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Journeys -->
    <div class="col-lg-6">
        <div class="st-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-trophy me-2"></i>Top Journeys</h5>
                <a href="<?= SITE_URL ?>/admin/journeys.php" class="btn btn-sm btn-st-secondary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="st-table">
                    <thead><tr><th>Journey</th><th>Views</th><th>Clones</th></tr></thead>
                    <tbody>
                        <?php foreach ($topJourneys as $tj): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= sanitize(truncateText($tj['title'], 30)) ?></div>
                                <small class="text-muted">by <?= sanitize($tj['username']) ?></small>
                            </td>
                            <td><?= number_format($tj['view_count']) ?></td>
                            <td><?= $tj['clone_count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Admin Activity Log -->
<?php if (!empty($adminLogs)): ?>
<div class="st-card mt-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-journal-code me-2"></i>Recent Admin Actions</h5>
    <div class="table-responsive">
        <table class="st-table">
            <thead><tr><th>Admin</th><th>Action</th><th>Details</th><th>Time</th></tr></thead>
            <tbody>
                <?php foreach ($adminLogs as $al): ?>
                <tr>
                    <td><?= sanitize($al['username']) ?></td>
                    <td><span class="st-badge st-badge-info"><?= sanitize($al['action']) ?></span></td>
                    <td><small class="text-muted"><?= sanitize(truncateText($al['details'] ?? '', 50)) ?></small></td>
                    <td><small class="text-muted"><?= timeAgo($al['created_at']) ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
