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

// Advanced progress metrics
$maxJourneyProgress = 0;
$maxDiligent = 0;
$topJourneyStepsRemaining = 0;

$pStmt = $db->prepare("SELECT id, total_steps, completed_steps FROM journeys WHERE user_id = ? AND total_steps > 0");
$pStmt->execute([$userId]);
while ($j = $pStmt->fetch()) {
    $prog = (float)$j['completed_steps'] / $j['total_steps'];
    if ($prog > $maxJourneyProgress) {
        $maxJourneyProgress = $prog;
        $topJourneyStepsRemaining = $j['total_steps'] - $j['completed_steps'];
    }
    
    // Diligent logic
    $dStmt = $db->prepare("SELECT COUNT(DISTINCT step_id) FROM daily_logs l JOIN steps s ON l.step_id = s.id WHERE s.journey_id = ?");
    $dStmt->execute([$j['id']]);
    $logCount = (int)$dStmt->fetchColumn();
    $dil = (float)$logCount / $j['total_steps'];
    if ($dil > $maxDiligent) $maxDiligent = $dil;
}
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-award me-2"></i>Badges</h1>
    <p class="st-page-subtitle"><?= $earnedCount ?> of <?= count($badges) ?> badges earned</p>
</div>

<div class="st-progress mb-4" style="height:12px;"><div class="st-progress-bar" style="width:<?= completionPercent($earnedCount, count($badges)) ?>%"></div></div>

<?php
$categories = [
    'Commitment' => ['committed'],
    'Consistency' => ['consistent', 'streak'],
    'Impact' => ['clones', 'aha_votes', 'aha_votes_received'],
    'Diligence' => ['diligent'],
    'Completion' => ['journeys_completed', 'steps_completed'],
    'Community' => ['community_helper']
];

foreach ($categories as $catName => $types): 
    $catBadges = array_filter($badges, function($b) use ($types) {
        return in_array($b['criteria_type'], $types);
    });
    
    if (empty($catBadges)) continue;
?>
    <div class="mb-5">
        <h4 class="fw-bold mb-3"><i class="bi bi-collection me-2"></i><?= $catName ?></h4>
        <div class="row g-4">
            <?php foreach ($catBadges as $badge):
                $earned = !empty($badge['earned_at']);
                $progress = 0;
                $tooltip = "";
                $val = 0;
                $target = (float)$badge['criteria_value'];

                switch ($badge['criteria_type']) {
                    case 'streak': 
                    case 'consistent':
                        $val = (float)$stats['longest_streak']; 
                        $tooltip = ($target - $val) . " more days to unlock";
                        break;
                    case 'journeys_completed': 
                        $completed = (float)$stats['completed_journeys'];
                        if ($completed == 0 && $maxJourneyProgress > 0) {
                            $val = $maxJourneyProgress * 100;
                            $target = 100;
                            $tooltip = $topJourneyStepsRemaining . " steps to unlock";
                        } else {
                            $val = $completed;
                            $tooltip = ($target - $val) . " more journeys needed";
                        }
                        break;
                    case 'steps_completed': 
                        $val = (float)$stats['completed_steps']; 
                        $tooltip = ($target - $val) . " steps to go";
                        break;
                    case 'clones': 
                        $val = (float)$stats['total_clones']; 
                        $tooltip = ($target - $val) . " more clones needed";
                        break;
                    case 'aha_votes': 
                    case 'aha_votes_received':
                        $val = (float)$stats['total_aha_received']; 
                        $tooltip = ($target - $val) . " more AHA!s needed";
                        break;
                    case 'diligent':
                        // If user has at least level 1, show count of journeys, otherwise show progress of best journey
                        $stmt2 = $db->prepare("SELECT COUNT(*) FROM journeys j 
                            WHERE j.user_id = ? AND j.status = 'completed' AND j.total_steps > 0
                            AND (SELECT COUNT(DISTINCT step_id) FROM daily_logs l 
                                 JOIN steps s2 ON l.step_id = s2.id 
                                 WHERE s2.journey_id = j.id) >= j.total_steps");
                        $stmt2->execute([$userId]);
                        $dilCount = (int)$stmt2->fetchColumn();
                        
                        if ($dilCount > 0) {
                            $val = $dilCount;
                        } else {
                            $val = $maxDiligent * 100;
                            $target = 100;
                        }
                        $tooltip = "Add logs to all steps to unlock!";
                        break;
                    case 'committed':
                        $stmt2 = $db->prepare("SELECT COUNT(*) FROM journeys j 
                            JOIN (SELECT journey_id, SUM(COALESCE(estimated_days, 1)) as total_est FROM steps GROUP BY journey_id) s 
                            ON j.id = s.journey_id 
                            WHERE j.user_id = ? AND j.status = 'completed' 
                            AND DATEDIFF(j.updated_at, j.created_at) <= s.total_est");
                        $stmt2->execute([$userId]);
                        $val = (int)$stmt2->fetchColumn();
                        $tooltip = "Complete journeys within estimate to unlock!";
                        break;
                    case 'community_helper':
                        $val = (float)($stats['total_comments_given'] + $stats['total_aha_given']);
                        $tooltip = ($target - $val) . " more interactions needed";
                        break;
                }
                $progress = $target > 0 ? min(100, ($val / $target) * 100) : 0;
                if (!$tooltip) $tooltip = ($target > $val ? round($target - $val) . " more to go" : "Requirement met!");
            ?>
            <div class="col-md-4 col-lg-3" title="<?= $tooltip ?>">
                <div class="st-card text-center <?= $earned ? '' : 'opacity-50' ?>" style="<?= $earned ? 'border-color:var(--st-warning);' : '' ?>; cursor: help;">
                    <div style="width:64px;height:64px;border-radius:16px;background:<?= $earned ? 'rgba(253,203,110,0.2)' : 'var(--st-dark-surface)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="bi <?= $badge['icon'] ?> fs-3" style="color:<?= $earned ? 'var(--st-warning)' : 'var(--st-text-muted)' ?>;"></i>
                    </div>
                    <h6 class="fw-bold mb-1"><?= sanitize($badge['name']) ?></h6>
                    <small class="text-muted d-block mb-2"><?= sanitize($badge['description']) ?></small>
                    <?php if ($earned): ?>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <span class="st-badge st-badge-success"><i class="bi bi-check me-1"></i>Earned <?= formatDate($badge['earned_at'], 'M Y') ?></span>
                            <a href="<?= SITE_URL ?>/user/share_badge.php?b=<?= $badge['id'] ?>&u=<?= $userId ?>" 
                               class="btn btn-sm btn-outline-warning" 
                               target="_blank"
                               title="Share Achievement">
                                <i class="bi bi-share"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="st-progress mt-2 mb-1"><div class="st-progress-bar" style="width:<?= $progress ?>%"></div></div>
                        <small class="text-muted"><?= round($progress) ?>% progress</small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
