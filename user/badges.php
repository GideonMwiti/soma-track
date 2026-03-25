<?php
/**
 * SomaTrack - Badges Page
 */
$pageTitle = 'Badges';
require_once __DIR__ . '/../includes/dashboard_header.php';

$db = getDB();
$userId = getCurrentUserId();
$stats = getUserStats($userId);

// Get all badges with earned status
$stmt = $db->prepare("SELECT b.*, ub.earned_at FROM badges b LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ? ORDER BY b.criteria_type, b.criteria_value ASC");
$stmt->execute([$userId]);
$badges = $stmt->fetchAll();

$earnedCount = 0;
foreach ($badges as $b) { if ($b['earned_at']) $earnedCount++; }
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-award me-2"></i>Badges</h1>
    <p class="st-page-subtitle"><?= $earnedCount ?> of <?= count($badges) ?> badges earned</p>
</div>

<div class="st-progress mb-4" style="height:12px;"><div class="st-progress-bar" style="width:<?= completionPercent($earnedCount, count($badges)) ?>%"></div></div>

<div class="row g-4">
    <?php foreach ($badges as $badge):
        $earned = !empty($badge['earned_at']);
        $progress = 0;
        switch ($badge['criteria_type']) {
            case 'streak': $progress = min(100, ($stats['longest_streak'] / $badge['criteria_value']) * 100); break;
            case 'journeys_completed': $progress = min(100, ($stats['completed_journeys'] / $badge['criteria_value']) * 100); break;
            case 'steps_completed': $progress = min(100, ($stats['completed_steps'] / $badge['criteria_value']) * 100); break;
            case 'clones': $progress = min(100, ($stats['total_clones'] / $badge['criteria_value']) * 100); break;
            case 'aha_votes': $progress = min(100, ($stats['total_aha_received'] / $badge['criteria_value']) * 100); break;
        }
    ?>
    <div class="col-md-4 col-lg-3">
        <div class="st-card text-center <?= $earned ? '' : 'opacity-50' ?>" style="<?= $earned ? 'border-color:var(--st-warning);' : '' ?>">
            <div style="width:64px;height:64px;border-radius:16px;background:<?= $earned ? 'rgba(253,203,110,0.2)' : 'var(--st-dark-surface)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="bi <?= $badge['icon'] ?> fs-3" style="color:<?= $earned ? 'var(--st-warning)' : 'var(--st-text-muted)' ?>;"></i>
            </div>
            <h6 class="fw-bold mb-1"><?= sanitize($badge['name']) ?></h6>
            <small class="text-muted d-block mb-2"><?= sanitize($badge['description']) ?></small>
            <?php if ($earned): ?>
                <span class="st-badge st-badge-success"><i class="bi bi-check me-1"></i>Earned <?= formatDate($badge['earned_at'], 'M Y') ?></span>
            <?php else: ?>
                <div class="st-progress mt-2 mb-1"><div class="st-progress-bar" style="width:<?= $progress ?>%"></div></div>
                <small class="text-muted"><?= round($progress) ?>% progress</small>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
