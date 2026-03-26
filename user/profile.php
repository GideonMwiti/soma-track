<?php
/**
 * SomaTrack - User Profile
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();

// View another user's profile or own
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : (isLoggedIn() ? getCurrentUserId() : 0);
if (!$viewId) { redirect(SITE_URL . '/auth/login.php'); }

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$viewId]);
$profile = $stmt->fetch();
if (!$profile) { redirect(SITE_URL); }

$stats = getUserStats($viewId);
$isOwn = isLoggedIn() && getCurrentUserId() === $viewId;

// Get badges
$badgeStmt = $db->prepare("SELECT b.*, ub.earned_at FROM user_badges ub JOIN badges b ON ub.badge_id = b.id WHERE ub.user_id = ? ORDER BY ub.earned_at DESC");
$badgeStmt->execute([$viewId]);
$badges = $badgeStmt->fetchAll();

// Get public journeys
$jStmt = $db->prepare("SELECT j.*, c.name AS category_name FROM journeys j LEFT JOIN categories c ON j.category_id = c.id WHERE j.user_id = ? AND j.visibility = 'public' ORDER BY j.updated_at DESC LIMIT 6");
$jStmt->execute([$viewId]);
$journeys = $jStmt->fetchAll();

$maxJourneyProgress = 0;
$maxDiligent = 0;
$topJourneyStepsRemaining = 0;

$pStmt = $db->prepare("SELECT id, total_steps, completed_steps FROM journeys WHERE user_id = ? AND total_steps > 0");
$pStmt->execute([$viewId]);
while ($j = $pStmt->fetch()) {
    $prog = (float)$j['completed_steps'] / $j['total_steps'];
    if ($prog > $maxJourneyProgress) {
        $maxJourneyProgress = $prog;
        $topJourneyStepsRemaining = $j['total_steps'] - $j['completed_steps'];
    }
    
    // Diligent logic: count unique steps with logs in this journey
    $dStmt = $db->prepare("SELECT COUNT(DISTINCT step_id) FROM daily_logs l JOIN steps s ON l.step_id = s.id WHERE s.journey_id = ?");
    $dStmt->execute([$j['id']]);
    $logCount = (int)$dStmt->fetchColumn();
    
    $dil = (float)$logCount / $j['total_steps'];
    if ($dil > $maxDiligent) $maxDiligent = $dil;
}

// Get all available badges to calculate progress
$allBadgesStmt = $db->query("SELECT * FROM badges ORDER BY id ASC");
$allBadges = $allBadgesStmt->fetchAll();

$earnedBadgeIds = array_column($badges, 'id');
$lockedBadges = [];
foreach ($allBadges as $ab) {
    if (!in_array($ab['id'], $earnedBadgeIds)) {
        $lockedBadges[] = $ab;
    }
}

$pageTitle = $profile['full_name'];
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="st-card text-center">
            <div class="st-avatar-initial mb-3 mx-auto" style="width:100px;height:100px;font-size:2.5rem;">
                <?= substr(sanitize($profile['username']), 0, 1) ?>
            </div>
            <h4 class="fw-bold mb-1"><?= sanitize($profile['full_name']) ?></h4>
            <p class="text-muted mb-2">@<?= sanitize($profile['username']) ?></p>
            <?php if ($profile['role'] === 'admin'): ?>
                <span class="badge bg-primary mb-3">Administrator</span>
            <?php endif; ?>
            
            <?php if ($profile['bio']): ?>
                <p class="text-muted mb-3" style="font-size:0.9rem;"><?= nl2br(sanitize($profile['bio'])) ?></p>
            <?php endif; ?>

            <?php if ($profile['role'] !== 'admin' || $stats['total_journeys'] > 0): ?>
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--st-primary-light);"><?= $stats['total_journeys'] ?></div>
                        <small class="text-muted">Journeys</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--st-success);"><?= $stats['completed_steps'] ?></div>
                        <small class="text-muted">Steps</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="color:var(--st-warning);"><?= $stats['current_streak'] ?></div>
                        <small class="text-muted">Streak</small>
                    </div>
                </div>
            <?php endif; ?>
            <small class="text-muted">Member since <?= formatDate($profile['created_at']) ?></small>
            <?php if ($isOwn): ?>
                <div class="mt-3"><a href="<?= SITE_URL ?>/user/settings.php" class="btn btn-st-secondary btn-sm w-100"><i class="bi bi-gear me-1"></i>Edit Profile</a></div>
            <?php endif; ?>
        </div>

        <!-- Badges -->
        <div class="st-card mt-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-award me-2 text-warning"></i>Badges (<?= count($badges) ?>)</h6>
            <?php if (empty($badges) && empty($lockedBadges)): ?>
                <p class="text-muted mb-0" style="font-size:0.85rem;">No badges available.</p>
            <?php else: ?>
                <!-- Earned Badges -->
                <?php if (!empty($badges)): ?>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($badges as $b): ?>
                            <div class="text-center" style="width:72px;" title="<?= sanitize($b['description']) ?>">
                                <div style="width:48px;height:48px;border-radius:12px;background:rgba(108,92,231,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 4px; border: 1px solid var(--st-primary-light);">
                                    <i class="bi <?= $b['icon'] ?> fs-5" style="color:var(--st-warning);"></i>
                                </div>
                                <small class="d-block text-truncate" style="font-size:0.65rem;color:var(--st-text-main);"><?= sanitize($b['name']) ?></small>
                                <small class="text-success fw-bold" style="font-size:0.6rem;">EARNED</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- In Progress Badges -->
                <?php if (!empty($lockedBadges) && $isOwn): ?>
                    <!-- DEBUG: maxJP=<?= $maxJourneyProgress ?>, maxDil=<?= $maxDiligent ?> -->
                    <h6 class="fw-bold mb-3 mt-4" style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; color:var(--st-text-muted);">In Progress</h6>
                    <div class="row g-3">
                        <?php foreach ($lockedBadges as $b): 
                            $val = 0;
                            $target = (float)$b['criteria_value'];
                            $display = "";
                            
                            switch ($b['criteria_type']) {
                                case 'streak': 
                                    $val = (float)$stats['current_streak']; 
                                    $display = $val . " / " . $target . " days";
                                    break;
                                case 'journeys_completed': 
                                    $completed = (float)$stats['completed_journeys']; 
                                    if ($completed == 0 && $maxJourneyProgress > 0) {
                                        $val = $maxJourneyProgress * 100;
                                        $target = 100;
                                        $display = round($val) . "% of a journey";
                                    } else {
                                        $val = $completed;
                                        $display = $val . " / " . $target . " journeys";
                                    }
                                    break;
                                case 'steps_completed': 
                                    $val = (float)$stats['completed_steps']; 
                                    $display = $val . " / " . $target . " steps";
                                    break;
                                case 'clones': 
                                    $val = (float)$stats['total_clones']; 
                                    $display = $val . " / " . $target . " clones";
                                    break;
                                case 'aha_votes': 
                                    $val = (float)$stats['total_aha_received']; 
                                    $display = $val . " / " . $target . " AHA!s";
                                    break;
                                case 'community_helper': 
                                    $val = (float)($stats['total_comments_given'] + $stats['total_aha_given']); 
                                    $display = $val . " / " . $target . " contribs";
                                    break;
                                case 'consistent': 
                                    $val = (float)$stats['longest_streak']; 
                                    $display = $val . " / " . $target . " days";
                                    break;
                                case 'diligent': 
                                    $val = $maxDiligent * 100;
                                    $target = 100;
                                    $display = round($val) . "% consistent";
                                    break;
                                case 'committed': 
                                    $val = 0;
                                    $target = 1;
                                    $display = "Finish on time";
                                    break;
                            }
                            $percent = $target > 0 ? min(100, round(($val / $target) * 100)) : 0;
                        ?>
                            <?php 
                                $tooltip = "";
                                if ($b['criteria_type'] == 'journeys_completed' && $completed == 0 && $maxJourneyProgress > 0) {
                                    $tooltip = $topJourneyStepsRemaining . " steps to unlock";
                                } elseif ($b['criteria_type'] == 'diligent' && $val < 100) {
                                    $tooltip = "Add logs to all steps to unlock!";
                                } elseif ($b['criteria_type'] == 'streak') {
                                    $tooltip = ($target - $val) . " more days to unlock";
                                } elseif ($b['criteria_type'] == 'clones') {
                                    $tooltip = ($target - $val) . " more clones needed";
                                } elseif ($b['criteria_type'] == 'community_helper') {
                                    $tooltip = ($target - $val) . " more contributions needed";
                                } else {
                                    $tooltip = ($target > $val ? round($target - $val) . " more to go" : "Requirement met!");
                                }
                            ?>
                            <div class="col-12" title="<?= $tooltip ?>">
                                <div class="p-2 rounded st-badge-card" style="background:rgba(0,0,0,0.1); border:1px solid var(--st-dark-border); cursor: help;">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div style="width:36px;height:36px;border-radius:8px;background:var(--st-dark-surface);display:flex;align-items:center;justify-content:center;opacity:0.6;">
                                            <i class="bi <?= $b['icon'] ?> text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="fw-bold" style="font-size:0.75rem;"><?= sanitize($b['name']) ?></small>
                                                <small class="text-muted" style="font-size:0.7rem;"><?= $display ?></small>
                                            </div>
                                            <div class="st-progress mt-1 st-progress-sm" style="background:rgba(255,255,255,0.1); border-radius:4px;">
                                                <div class="st-progress-bar" style="width:<?= $percent ?>% !important; background:#6610f2 !important; height:100%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block" style="font-size:0.65rem; line-height:1.2;"><?= sanitize($b['description']) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="st-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-map me-2 text-primary"></i>Public Journeys</h5>
            <?php if (empty($journeys)): ?>
                <p class="text-muted">No public journeys yet.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($journeys as $j): ?>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:var(--st-dark-surface);border:1px solid var(--st-dark-border);">
                            <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="fw-semibold text-decoration-none"><?= sanitize($j['title']) ?></a>
                            <?php if ($j['category_name']): ?>
                                <span class="st-badge st-badge-primary ms-1" style="font-size:0.6rem;"><?= sanitize($j['category_name']) ?></span>
                            <?php endif; ?>
                            <?php if (isLoggedIn() && $viewId == getCurrentUserId()): ?>
                                <div class="st-progress mt-2 mb-1"><div class="st-progress-bar" style="width:<?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%"></div></div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted"><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?> steps</small>
                                    <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-st-secondary" style="font-size:0.7rem; padding:2px 8px;"><?= getJourneyActionText($j['user_id'], $j['completed_steps'], isLoggedIn()) ?></a>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted"><i class="bi bi-layers me-1"></i><?= $j['total_steps'] ?> module steps</small>
                                    <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-st-secondary" style="font-size:0.7rem; padding:2px 8px;"><?= getJourneyActionText($j['user_id'], 0, isLoggedIn()) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- DIAGNOSTIC FOOTER (HIDDEN BY DEFAULT, BUT VISIBLE IF NEEDED) -->
<div style="font-size:10px; color:rgba(255,255,255,0.05); padding:20px 0; text-align:center;">
    DEBUG: LoggedIn=<?= getCurrentUserId() ?> | Viewing=<?= $viewId ?> | MaxJP=<?= $maxJourneyProgress ?> | StepsRem=<?= $topJourneyStepsRemaining ?>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
