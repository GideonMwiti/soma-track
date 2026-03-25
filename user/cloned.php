<?php
/**
 * SomaTrack - Cloned Journeys
 */
$pageTitle = 'Cloned Journeys';
require_once __DIR__ . '/../includes/dashboard_header.php';

$db = getDB();
$userId = getCurrentUserId();

$stmt = $db->prepare("SELECT cj.*, j.title, j.slug, j.total_steps, j.completed_steps, j.status, j.category_id,
    oj.title AS original_title, u.username AS original_author, c.name AS category_name
    FROM cloned_journeys cj 
    JOIN journeys j ON cj.cloned_journey_id = j.id 
    JOIN journeys oj ON cj.original_journey_id = oj.id 
    JOIN users u ON oj.user_id = u.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE cj.user_id = ? 
    ORDER BY cj.created_at DESC");
$stmt->execute([$userId]);
$cloned = $stmt->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-copy me-2"></i>Cloned Journeys</h1>
    <p class="st-page-subtitle">Journeys you've cloned from other learners</p>
</div>

<?php if (empty($cloned)): ?>
    <div class="st-card">
        <div class="st-empty-state">
            <i class="bi bi-copy d-block"></i>
            <h5>No cloned journeys yet</h5>
            <p>Explore and clone journeys from other learners to follow their paths.</p>
            <a href="<?= SITE_URL ?>/explore.php" class="btn btn-st-primary mt-2"><i class="bi bi-compass me-1"></i>Explore Journeys</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($cloned as $cj): ?>
        <div class="col-md-6 col-xl-4">
            <div class="st-journey-card">
                <div class="card-header-gradient" style="<?= !$cj['is_synced'] ? 'background:linear-gradient(135deg,#FDCB6E,#E17055);' : '' ?>"></div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <?php if ($cj['category_name']): ?>
                            <span class="st-badge st-badge-primary"><?= sanitize($cj['category_name']) ?></span>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                        <?php if (!$cj['is_synced']): ?>
                            <span class="st-badge st-badge-warning"><i class="bi bi-exclamation-triangle me-1"></i>Needs Sync</span>
                        <?php else: ?>
                            <span class="st-badge st-badge-success"><i class="bi bi-check me-1"></i>Synced</span>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title">
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $cj['cloned_journey_id'] ?>" class="text-decoration-none text-light"><?= sanitize($cj['title']) ?></a>
                    </h5>
                    <small class="text-muted d-block mb-2"><i class="bi bi-person me-1"></i>Original by <?= sanitize($cj['original_author']) ?></small>
                    <div class="st-progress mb-2"><div class="st-progress-bar" style="width:<?= completionPercent($cj['completed_steps'], $cj['total_steps']) ?>%"></div></div>
                    <small class="text-muted"><?= $cj['completed_steps'] ?>/<?= $cj['total_steps'] ?> steps</small>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Cloned <?= timeAgo($cj['created_at']) ?></small>
                    <?php if (!$cj['is_synced']): ?>
                        <a href="<?= SITE_URL ?>/api/journey.php?action=sync&clone_id=<?= $cj['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-warning"><i class="bi bi-arrow-repeat me-1"></i>Sync</a>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $cj['cloned_journey_id'] ?>" class="btn btn-sm btn-st-secondary">View</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
