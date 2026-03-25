<?php
/**
 * SomaTrack - Streaks Page
 */
$pageTitle = 'Streaks';
require_once __DIR__ . '/../includes/dashboard_header.php';

$db = getDB();
$userId = getCurrentUserId();
$stats = getUserStats($userId);

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
<div class="st-card">
    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2 text-success"></i>Activity (Last 90 Days)</h5>
    <div class="d-flex flex-wrap gap-1">
        <?php
        $today = new DateTime();
        for ($i = 89; $i >= 0; $i--) {
            $date = (clone $today)->modify("-{$i} days")->format('Y-m-d');
            $active = in_array($date, $streakDates);
            $bg = $active ? 'var(--st-success)' : 'var(--st-dark-surface)';
            $border = $active ? 'var(--st-success)' : 'var(--st-dark-border)';
            $title = date('M d, Y', strtotime($date)) . ($active ? ' ✓' : '');
            echo '<div style="width:14px;height:14px;border-radius:3px;background:'.$bg.';border:1px solid '.$border.';" title="'.$title.'"></div>';
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

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
