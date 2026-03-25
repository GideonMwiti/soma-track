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

$pageTitle = $profile['full_name'];
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="st-card text-center">
            <img src="<?= getAvatarUrl($profile['avatar']) ?>" class="rounded-circle mb-3" width="100" height="100" alt="">
            <h4 class="fw-bold mb-1"><?= sanitize($profile['full_name']) ?></h4>
            <p class="text-muted mb-2">@<?= sanitize($profile['username']) ?></p>
            <?php if ($profile['bio']): ?>
                <p class="text-muted mb-3" style="font-size:0.9rem;"><?= nl2br(sanitize($profile['bio'])) ?></p>
            <?php endif; ?>
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
            <small class="text-muted">Member since <?= formatDate($profile['created_at']) ?></small>
            <?php if ($isOwn): ?>
                <div class="mt-3"><a href="<?= SITE_URL ?>/user/settings.php" class="btn btn-st-secondary btn-sm w-100"><i class="bi bi-gear me-1"></i>Edit Profile</a></div>
            <?php endif; ?>
        </div>

        <!-- Badges -->
        <div class="st-card mt-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-award me-2 text-warning"></i>Badges (<?= count($badges) ?>)</h6>
            <?php if (empty($badges)): ?>
                <p class="text-muted mb-0" style="font-size:0.85rem;">No badges earned yet.</p>
            <?php else: ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($badges as $b): ?>
                        <div class="text-center" style="width:72px;" title="<?= sanitize($b['description']) ?>">
                            <div style="width:48px;height:48px;border-radius:12px;background:rgba(108,92,231,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 4px;">
                                <i class="bi <?= $b['icon'] ?> fs-5" style="color:var(--st-warning);"></i>
                            </div>
                            <small style="font-size:0.65rem;color:var(--st-text-muted);"><?= sanitize($b['name']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                            <div class="st-progress mt-2 mb-1"><div class="st-progress-bar" style="width:<?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%"></div></div>
                            <small class="text-muted"><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?> steps</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
