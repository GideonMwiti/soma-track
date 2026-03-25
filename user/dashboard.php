<?php
/**
 * SomaTrack - User Dashboard
 */
require_once __DIR__ . '/../includes/session.php';
if (isAdmin()) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/dashboard_header.php';

$userId = getCurrentUserId();
$db = getDB();

// Get user stats
$stats = getUserStats($userId);

// Active journeys
$activeStmt = $db->prepare("SELECT j.*, c.name AS category_name FROM journeys j LEFT JOIN categories c ON j.category_id = c.id WHERE j.user_id = ? AND j.status = 'active' ORDER BY j.updated_at DESC LIMIT 5");
$activeStmt->execute([$userId]);
$activeJourneys = $activeStmt->fetchAll();

// Cloned journeys
$clonedStmt = $db->prepare("SELECT cj.*, j.title, j.total_steps, j.completed_steps, j.status, u.username AS creator, c.name AS category_name 
    FROM cloned_journeys cj 
    JOIN journeys j ON cj.cloned_journey_id = j.id 
    JOIN journeys oj ON cj.original_journey_id = oj.id 
    JOIN users u ON oj.user_id = u.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE cj.user_id = ? 
    ORDER BY cj.created_at DESC LIMIT 5");
$clonedStmt->execute([$userId]);
$clonedJourneys = $clonedStmt->fetchAll();

// Recent activity (logs)
$logsStmt = $db->prepare("SELECT dl.*, s.title AS step_title, j.title AS journey_title, j.id AS journey_id
    FROM daily_logs dl 
    JOIN steps s ON dl.step_id = s.id 
    JOIN journeys j ON s.journey_id = j.id 
    WHERE dl.user_id = ? 
    ORDER BY dl.created_at DESC LIMIT 5");
$logsStmt->execute([$userId]);
$recentLogs = $logsStmt->fetchAll();

// Unsync'd clones
$unsyncStmt = $db->prepare("SELECT COUNT(*) FROM cloned_journeys WHERE user_id = ? AND is_synced = 0");
$unsyncStmt->execute([$userId]);
$unsyncCount = (int)$unsyncStmt->fetchColumn();

// Recent notifications
$notifStmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notifStmt->execute([$userId]);
$recentNotifs = $notifStmt->fetchAll();
?>

<!-- Page Header -->
<div class="st-page-header">
    <h1 class="st-page-title">Welcome back, <?= sanitize($currentUser['full_name']) ?>!</h1>
    <p class="st-page-subtitle">Here's your learning overview</p>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="st-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="st-stat-value"><?= $stats['total_journeys'] ?></div>
                    <div class="st-stat-label">Journeys</div>
                </div>
                <div class="st-stat-icon" style="background:rgba(108,92,231,0.15);color:var(--st-primary-light);">
                    <i class="bi bi-map"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="st-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="st-stat-value"><?= $stats['completed_steps'] ?></div>
                    <div class="st-stat-label">Steps Done</div>
                </div>
                <div class="st-stat-icon" style="background:rgba(0,184,148,0.15);color:var(--st-success);">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="st-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="st-stat-value"><?= $stats['current_streak'] ?></div>
                    <div class="st-stat-label">Day Streak</div>
                </div>
                <div class="st-stat-icon" style="background:rgba(253,203,110,0.15);color:var(--st-warning);">
                    <i class="bi bi-fire"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="st-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="st-stat-value"><?= $stats['total_logs'] ?></div>
                    <div class="st-stat-label">Total Logs</div>
                </div>
                <div class="st-stat-icon" style="background:rgba(116,185,255,0.15);color:var(--st-info);">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($unsyncCount > 0): ?>
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-exclamation-triangle me-2 fs-5"></i>
    <div>You have <strong><?= $unsyncCount ?></strong> cloned journey(s) with pending updates. <a href="<?= SITE_URL ?>/user/cloned.php" class="alert-link">Review & Sync</a></div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Active Journeys -->
    <div class="col-lg-8">
        <div class="st-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-map me-2 text-primary"></i>Active Journeys</h5>
                <a href="<?= SITE_URL ?>/user/journeys.php" class="btn btn-sm btn-st-secondary">View All</a>
            </div>
            <?php if (empty($activeJourneys)): ?>
                <div class="st-empty-state py-4">
                    <i class="bi bi-map d-block"></i>
                    <h6 class="text-muted">No active journeys yet</h6>
                    <a href="<?= SITE_URL ?>/journey/create.php" class="btn btn-st-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Create Your First Journey</a>
                </div>
            <?php else: ?>
                <?php foreach ($activeJourneys as $j): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded mb-2" style="background:var(--st-dark-surface);border:1px solid var(--st-dark-border);">
                    <div class="flex-grow-1">
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="fw-semibold text-decoration-none"><?= sanitize($j['title']) ?></a>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <?php if ($j['category_name']): ?>
                                <span class="st-badge st-badge-primary" style="font-size:0.65rem;"><?= sanitize($j['category_name']) ?></span>
                            <?php endif; ?>
                            <small class="text-muted"><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?> steps</small>
                        </div>
                    </div>
                    <div style="width:100px;">
                        <div class="st-progress"><div class="st-progress-bar" style="width:<?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%"></div></div>
                        <small class="text-muted"><?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%</small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Recent Activity -->
        <div class="st-card mt-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-info"></i>Recent Activity</h5>
            <?php if (empty($recentLogs)): ?>
                <p class="text-muted mb-0">No recent activity. Start logging your progress!</p>
            <?php else: ?>
                <div class="st-timeline">
                    <?php foreach ($recentLogs as $log): ?>
                    <div class="st-timeline-item completed">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?= sanitize($log['step_title']) ?></strong>
                                <br><small class="text-muted">in <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $log['journey_id'] ?>"><?= sanitize($log['journey_title']) ?></a></small>
                            </div>
                            <small class="text-muted"><?= timeAgo($log['created_at']) ?></small>
                        </div>
                        <p class="text-muted mt-1 mb-0" style="font-size:0.85rem;"><?= truncateText(sanitize($log['content']), 120) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="st-card mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            <div class="d-grid gap-2">
                <a href="<?= SITE_URL ?>/journey/create.php" class="btn btn-st-primary btn-sm"><i class="bi bi-plus-circle me-2"></i>New Journey</a>
                <a href="<?= SITE_URL ?>/explore.php" class="btn btn-st-secondary btn-sm"><i class="bi bi-compass me-2"></i>Explore Journeys</a>
            </div>
        </div>

        <!-- Cloned Journeys -->
        <div class="st-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-copy me-2 text-secondary"></i>Cloned</h6>
                <a href="<?= SITE_URL ?>/user/cloned.php" class="text-muted" style="font-size:0.8rem;">View All</a>
            </div>
            <?php if (empty($clonedJourneys)): ?>
                <p class="text-muted mb-0" style="font-size:0.85rem;">No cloned journeys yet.</p>
            <?php else: ?>
                <?php foreach ($clonedJourneys as $cj): ?>
                <div class="d-flex align-items-center gap-2 mb-2 pb-2" style="border-bottom:1px solid var(--st-dark-border);">
                    <div class="flex-grow-1">
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $cj['cloned_journey_id'] ?>" class="text-decoration-none" style="font-size:0.85rem;"><?= sanitize($cj['title']) ?></a>
                        <br><small class="text-muted">by <?= sanitize($cj['creator']) ?></small>
                    </div>
                    <?php if (!$cj['is_synced']): ?>
                        <span class="st-badge st-badge-warning" style="font-size:0.6rem;">Update</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Recent Notifications -->
        <div class="st-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i>Notifications</h6>
                <a href="<?= SITE_URL ?>/user/notifications.php" class="text-muted" style="font-size:0.8rem;">View All</a>
            </div>
            <?php if (empty($recentNotifs)): ?>
                <p class="text-muted mb-0" style="font-size:0.85rem;">No new notifications.</p>
            <?php else: ?>
                <?php foreach ($recentNotifs as $n): ?>
                <div class="d-flex gap-2 mb-2 pb-2<?= !$n['is_read'] ? ' ps-2' : '' ?>" style="border-bottom:1px solid var(--st-dark-border);<?= !$n['is_read'] ? 'border-left:2px solid var(--st-primary);' : '' ?>">
                    <div>
                        <div style="font-size:0.8rem;" class="fw-semibold"><?= sanitize($n['title']) ?></div>
                        <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
