<?php
/**
 * SomaTrack - Daily Logs Overview
 */
$pageTitle = 'Daily Logs';
require_once __DIR__ . '/../includes/dashboard_header.php';

$db = getDB();
$userId = getCurrentUserId();

$stmt = $db->prepare("SELECT dl.*, s.title AS step_title, s.step_number, j.title AS journey_title, j.id AS journey_id
    FROM daily_logs dl 
    JOIN steps s ON dl.step_id = s.id 
    JOIN journeys j ON s.journey_id = j.id 
    WHERE dl.user_id = ?
    ORDER BY dl.log_date DESC LIMIT 30");
$stmt->execute([$userId]);
$logs = $stmt->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-journal-text me-2"></i>Daily Logs</h1>
    <p class="st-page-subtitle">Your learning history</p>
</div>

<?php if (empty($logs)): ?>
    <div class="st-card">
        <div class="st-empty-state">
            <i class="bi bi-journal d-block"></i>
            <h5>No logs yet</h5>
            <p>Start logging your daily progress on any journey step.</p>
        </div>
    </div>
<?php else: ?>
    <div class="st-card">
        <div class="st-timeline">
            <?php
            $currentDate = '';
            foreach ($logs as $log):
                $logDate = formatDate($log['log_date'], 'D, M d, Y');
                if ($logDate !== $currentDate):
                    $currentDate = $logDate;
            ?>
                <div class="mb-2 mt-3"><span class="st-badge st-badge-primary"><i class="bi bi-calendar me-1"></i><?= $logDate ?></span></div>
            <?php endif; ?>
            <div class="st-timeline-item completed">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= sanitize($log['step_title']) ?></strong>
                        <br><small class="text-muted">Step <?= $log['step_number'] ?> in <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $log['journey_id'] ?>"><?= sanitize($log['journey_title']) ?></a></small>
                    </div>
                    <a href="<?= SITE_URL ?>/journey/step.php?id=<?= $log['step_id'] ?>" class="btn btn-sm btn-st-secondary">View Step</a>
                </div>
                <p class="text-muted mt-1 mb-0" style="font-size:0.85rem;"><?= truncateText(sanitize($log['content']), 150) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
