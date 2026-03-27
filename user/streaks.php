<?php
/**
 * SomaTrack - Streaks Page
 */
$pageTitle = 'Streaks';
require_once __DIR__ . '/../includes/dashboard_header.php';

$db = getDB();
$userId = getCurrentUserId();
$stats = getUserStats($userId);

// Get user created_at for cold-start calendar logic
$userStmt = $db->prepare("SELECT created_at FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$userCreatedAtRaw = $userStmt->fetchColumn();
$userCreatedAt = new DateTime($userCreatedAtRaw);
$userCreatedAt->setTime(0, 0, 0);

// Get streak dates for calendar
$streakStmt = $db->prepare("SELECT streak_date FROM streaks WHERE user_id = ? ORDER BY streak_date DESC LIMIT 90");
$streakStmt->execute([$userId]);
$streakDates = $streakStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-fire me-2"></i>Streaks</h1>
    <p class="st-page-subtitle">Your consistency is your superpower</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="st-stat-card text-center">
            <div class="st-stat-value" style="font-size:3rem;"><?= $stats['current_streak'] ?></div>
            <div class="st-stat-label">Current Streak</div>
            <i class="bi bi-fire text-warning fs-3 mt-2"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="st-stat-card text-center">
            <div class="st-stat-value" style="font-size:3rem;"><?= $stats['longest_streak'] ?></div>
            <div class="st-stat-label">Longest Streak</div>
            <i class="bi bi-trophy text-warning fs-3 mt-2"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="st-stat-card text-center">
            <div class="st-stat-value" style="font-size:3rem;"><?= $stats['total_logs'] ?></div>
            <div class="st-stat-label">Total Log Entries</div>
            <i class="bi bi-journal-check text-info fs-3 mt-2"></i>
        </div>
    </div>
</div>

<!-- Activity Heatmap (Last 90 days) -->
<?php
$today = new DateTime();
$today->setTime(0, 0, 0);
$accountAgeDays = $userCreatedAt->diff($today)->days;

$showOnboarding = false;
$overlayTitle = "";
$overlayMessage = "";
$gradientWidth = "";

if ($accountAgeDays <= 14) {
    $showOnboarding = true;
    $overlayTitle = "<i class='bi bi-rocket-takeoff me-2'></i>Your Journey Begins!";
    $overlayMessage = "Start logging activities to fill your chart.";
    $gradientWidth = "75%";
} elseif ($accountAgeDays <= 45) {
    $showOnboarding = true;
    $overlayTitle = "<i class='bi bi-fire text-warning me-2'></i>Building Momentum!";
    $overlayMessage = "Keep up the great work and watch your chart grow.";
}
?>
<div class="st-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-success"></i>Activity (Last 90 Days)</h5>
        <?php if ($showOnboarding): ?>
            <div class="text-end" style="background: rgba(13, 202, 240, 0.1); border: 1px dashed rgba(13, 202, 240, 0.4); border-radius: 8px; padding: 6px 12px;">
                <span class="text-info fw-bold d-block" style="font-size: 0.9rem;"><?= $overlayTitle ?></span>
                <small class="text-light" style="font-size: 0.8rem;"><?= $overlayMessage ?></small>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="position-relative py-1">
        <div class="d-flex flex-wrap gap-1">
            <?php
            for ($i = 89; $i >= 0; $i--) {
                $currentLoopDate = (clone $today)->modify("-{$i} days");
                $date = $currentLoopDate->format('Y-m-d');
                $active = in_array($date, $streakDates);
                $isPreJoin = $currentLoopDate < $userCreatedAt;
                
                $bg = $active ? 'var(--st-success)' : 'var(--st-dark-surface)';
                $border = $active ? 'var(--st-success)' : 'var(--st-dark-border)';
                // Grey out the squares that represent days before the user joined
                $opacity = $isPreJoin ? '0.2' : '1';
                
                $title = date('M d, Y', strtotime($date)) . ($active ? ' ✓' : '');
                if ($isPreJoin) $title = "Before joining ($title)";
                
                echo '<div style="width:14px;height:14px;border-radius:3px;background:'.$bg.';border:1px solid '.$border.';opacity:'.$opacity.';" title="'.$title.'"></div>';
            }
            ?>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2">
            <small class="text-muted">Less</small>
            <div style="width:14px;height:14px;border-radius:3px;background:var(--st-dark-surface);border:1px solid var(--st-dark-border);"></div>
            <div style="width:14px;height:14px;border-radius:3px;background:var(--st-success);"></div>
            <small class="text-muted">More</small>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
