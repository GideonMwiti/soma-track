<?php
/**
 * SomaTrack - View Journey (Timeline + Steps + Daily Logs)
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . SITE_URL . '/explore.php');
    exit;
}

$journeyId = (int)$_GET['id'];

// Get journey
$stmt = $db->prepare("SELECT j.*, u.username, u.avatar, u.full_name AS author_name, c.name AS category_name 
    FROM journeys j 
    JOIN users u ON j.user_id = u.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE j.id = ?");
$stmt->execute([$journeyId]);
$journey = $stmt->fetch();

if (!$journey) {
    if (isLoggedIn()) {
        setFlash('danger', 'Journey not found.');
        header('Location: ' . SITE_URL . '/user/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/explore.php');
    }
    exit;
}

$isOwner = isLoggedIn() && getCurrentUserId() === (int)$journey['user_id'];

// Private journey check
if ($journey['visibility'] === 'private' && !$isOwner && !isAdmin()) {
    // Check if user has cloned it
    $hasClone = false;
    if (isLoggedIn()) {
        $cloneCheck = $db->prepare("SELECT id FROM cloned_journeys WHERE original_journey_id = ? AND user_id = ?");
        $cloneCheck->execute([$journeyId, getCurrentUserId()]);
        $hasClone = (bool)$cloneCheck->fetch();
    }
    if (!$hasClone) {
        setFlash('danger', 'This journey is private.');
        header('Location: ' . SITE_URL . '/explore.php');
        exit;
    }
}

// Increment view count (once per session)
if (!isset($_SESSION['viewed_journeys'])) $_SESSION['viewed_journeys'] = [];
if (!in_array($journeyId, $_SESSION['viewed_journeys'])) {
    $db->prepare("UPDATE journeys SET view_count = view_count + 1 WHERE id = ?")->execute([$journeyId]);
    $_SESSION['viewed_journeys'][] = $journeyId;
}

// Get steps - Only show drafts to owner or admin
$stepsSql = "SELECT s.*, 
    (SELECT COUNT(*) FROM step_comments WHERE step_id = s.id AND is_deleted = 0) AS comment_count,
    (SELECT COUNT(*) FROM aha_votes WHERE step_id = s.id) AS aha_count 
    FROM steps s WHERE s.journey_id = ? ";

if (!$isOwner && !isAdmin()) {
    $stepsSql .= " AND s.is_draft = 0 ";
}
$stepsSql .= " ORDER BY s.step_number ASC";

$stepsStmt = $db->prepare($stepsSql);
$stepsStmt->execute([$journeyId]);
$steps = $stepsStmt->fetchAll();

// Check if user has cloned this journey
$userHasCloned = false;
$cloneData = null;
if (isLoggedIn() && !$isOwner) {
    $cloneStmt = $db->prepare("SELECT * FROM cloned_journeys WHERE original_journey_id = ? AND user_id = ?");
    $cloneStmt->execute([$journeyId, getCurrentUserId()]);
    $cloneData = $cloneStmt->fetch();
    $userHasCloned = (bool)$cloneData;
}

// Is this a cloned journey? Find original
$isClone = false;
$originalJourney = null;
if ($isOwner) {
    $origStmt = $db->prepare("SELECT cj.*, j.title AS original_title, u.username AS original_author 
        FROM cloned_journeys cj 
        JOIN journeys j ON cj.original_journey_id = j.id 
        JOIN users u ON j.user_id = u.id 
        WHERE cj.cloned_journey_id = ?");
    $origStmt->execute([$journeyId]);
    $originalJourney = $origStmt->fetch();
    $isClone = (bool)$originalJourney;
}

// Aha vote totals for journey
$ahaTotal = $db->prepare("SELECT COUNT(*) FROM aha_votes av JOIN steps s ON av.step_id = s.id WHERE s.journey_id = ?");
$ahaTotal->execute([$journeyId]);
$totalAha = (int)$ahaTotal->fetchColumn();

$pageTitle = $journey['title'];
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<!-- Journey Header -->
<div class="st-card mb-4" style="position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:var(--st-gradient-1);"></div>
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 pt-2">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-2">
                <?php if ($journey['category_name']): ?>
                    <span class="st-badge st-badge-primary"><?= sanitize($journey['category_name']) ?></span>
                <?php endif; ?>
                <span class="st-badge <?= $journey['visibility'] === 'public' ? 'st-badge-success' : 'st-badge-warning' ?>">
                    <i class="bi <?= $journey['visibility'] === 'public' ? 'bi-globe' : 'bi-lock' ?>"></i> <?= ucfirst($journey['visibility']) ?>
                </span>
                <?php
                $sBadge = ['active' => 'st-badge-success', 'completed' => 'st-badge-info', 'archived' => 'st-badge-warning'];
                ?>
                <span class="st-badge <?= $sBadge[$journey['status']] ?>"><?= ucfirst($journey['status']) ?></span>
            </div>
            <h2 class="fw-bold mb-2"><?= sanitize($journey['title']) ?></h2>
            <?php if ($journey['description']): ?>
                <p class="text-muted mb-3"><?= nl2br(sanitize($journey['description'])) ?></p>
            <?php endif; ?>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                        <?= substr(sanitize($journey['username']), 0, 1) ?>
                    </div>
                    <a href="<?= SITE_URL ?>/user/profile.php?id=<?= $journey['user_id'] ?>" class="text-decoration-none"><?= sanitize($journey['username']) ?></a>
                </div>
                <small class="text-muted"><i class="bi bi-eye me-1"></i><?= number_format($journey['view_count']) ?> views</small>
                <small class="text-muted"><i class="bi bi-copy me-1"></i><?= $journey['clone_count'] ?> clones</small>
                <small class="text-muted"><i class="bi bi-lightbulb me-1"></i><?= $totalAha ?> Aha!</small>
                <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= formatDate($journey['created_at']) ?></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if ($isOwner): ?>
                <a href="<?= SITE_URL ?>/journey/edit.php?id=<?= $journeyId ?>" class="btn btn-st-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
            <?php elseif (isLoggedIn() && !$userHasCloned): ?>
                <form method="POST" action="<?= SITE_URL ?>/api/journey.php" class="d-inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="clone">
                    <input type="hidden" name="journey_id" value="<?= $journeyId ?>">
                    <button type="submit" class="btn btn-st-primary btn-sm"><i class="bi bi-copy me-1"></i>Clone Journey</button>
                </form>
            <?php elseif ($userHasCloned): ?>
                <span class="st-badge st-badge-info"><i class="bi bi-check me-1"></i>Cloned</span>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/journey/portfolio.php?id=<?= $journeyId ?>" class="btn btn-sm btn-st-secondary"><i class="bi bi-share me-1"></i>Portfolio</a>
        </div>
    </div>

    <?php if ($isClone && $originalJourney): ?>
        <div class="mt-3 p-2 rounded d-flex align-items-center gap-2" style="background:var(--st-dark-surface);border:1px solid var(--st-dark-border);">
            <i class="bi bi-arrow-repeat text-info"></i>
            <small class="text-muted">Cloned from <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $originalJourney['original_journey_id'] ?>"><?= sanitize($originalJourney['original_title']) ?></a> by <?= sanitize($originalJourney['original_author']) ?></small>
            <?php if (!$originalJourney['is_synced']): ?>
                <a href="<?= SITE_URL ?>/api/journey.php?action=sync&clone_id=<?= $originalJourney['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-warning ms-auto"><i class="bi bi-arrow-repeat me-1"></i>Sync Changes</a>
            <?php else: ?>
                <span class="st-badge st-badge-success ms-auto"><i class="bi bi-check"></i> Synced</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Progress -->
    <div class="mt-3">
        <div class="d-flex justify-content-between mb-1">
            <small class="text-muted"><?= $journey['completed_steps'] ?> of <?= $journey['total_steps'] ?> steps completed</small>
            <small class="fw-semibold" style="color:var(--st-primary-light);"><?= completionPercent($journey['completed_steps'], $journey['total_steps']) ?>%</small>
        </div>
        <div class="st-progress"><div class="st-progress-bar" style="width:<?= completionPercent($journey['completed_steps'], $journey['total_steps']) ?>%"></div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Add Step (Owner only) -->
        <?php if ($isOwner): ?>
        <div class="st-card mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2 text-success"></i>Add New Step</h5>
            <form method="POST" action="<?= SITE_URL ?>/api/steps.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="journey_id" value="<?= $journeyId ?>">
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="title" class="form-control st-form-control" placeholder="Step title (e.g., Learn HTML Basics)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="estimated_days" class="form-control st-form-control" placeholder="Est. days" min="1">
                    </div>
                </div>
                <textarea name="description" class="form-control st-form-control mb-2" rows="2" placeholder="Step description (optional)"></textarea>
                <button type="submit" class="btn btn-st-primary btn-sm"><i class="bi bi-plus me-1"></i>Add Step</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Steps Timeline -->
        <div class="st-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-signpost-2 me-2 text-primary"></i>Learning Steps</h5>
            <?php if (empty($steps)): ?>
                <div class="st-empty-state py-4">
                    <i class="bi bi-signpost d-block"></i>
                    <h6 class="text-muted">No steps added yet</h6>
                    <?php if ($isOwner): ?>
                        <p class="text-muted">Add your first step above to start your journey!</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="st-timeline">
                    <?php foreach ($steps as $step): ?>
                    <div class="st-timeline-item <?= $step['status'] === 'completed' ? 'completed' : ($step['status'] === 'in_progress' ? 'active' : '') ?>">
                        <div class="st-card mb-0" style="padding:16px;">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold" style="color:var(--st-primary-light);font-size:0.8rem;">Step <?= $step['step_number'] ?></span>
                                        <?php
                                        $stepBadge = ['pending' => 'st-badge-warning', 'in_progress' => 'st-badge-info', 'completed' => 'st-badge-success'];
                                        ?>
                                        <span class="st-badge <?= $stepBadge[$step['status']] ?>" style="font-size:0.6rem;"><?= ucfirst(str_replace('_', ' ', $step['status'])) ?></span>
                                        <?php if ($step['is_draft']): ?>
                                            <span class="st-badge st-badge-secondary" style="font-size:0.6rem;"><i class="bi bi-eye-slash-fill me-1"></i>Draft</span>
                                        <?php endif; ?>
                                        <?php if ($step['estimated_days']): ?>
                                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= $step['estimated_days'] ?>d</small>
                                        <?php endif; ?>
                                    </div>
                                    <h6 class="fw-semibold mb-1"><?= sanitize($step['title']) ?></h6>
                                    <?php if ($step['description']): ?>
                                        <p class="text-muted mb-2" style="font-size:0.85rem;"><?= nl2br(sanitize($step['description'])) ?></p>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        <a href="<?= SITE_URL ?>/journey/step.php?id=<?= $step['id'] ?>" class="text-decoration-none" style="font-size:0.8rem;"><i class="bi bi-journal me-1"></i>View Logs</a>
                                        <span class="text-muted" style="font-size:0.8rem;"><i class="bi bi-chat me-1"></i><?= $step['comment_count'] ?></span>
                                        <span class="text-muted" style="font-size:0.8rem;"><i class="bi bi-lightbulb me-1"></i><?= $step['aha_count'] ?> Aha!</span>
                                    </div>
                                </div>
                                <?php if ($isOwner): ?>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                                        <?php if ($step['status'] !== 'completed'): ?>
                                            <li><a class="dropdown-item st-api-toggle-status" href="#" data-id="<?= $step['id'] ?>" data-status="in_progress" data-token="<?= generateCSRFToken() ?>"><i class="bi bi-play me-2"></i>Mark In Progress</a></li>
                                            <li><a class="dropdown-item st-api-toggle-status" href="#" data-id="<?= $step['id'] ?>" data-status="completed" data-token="<?= generateCSRFToken() ?>"><i class="bi bi-check-circle me-2"></i>Mark Complete</a></li>
                                        <?php else: ?>
                                            <li><a class="dropdown-item st-api-toggle-status" href="#" data-id="<?= $step['id'] ?>" data-status="in_progress" data-token="<?= generateCSRFToken() ?>"><i class="bi bi-arrow-counterclockwise me-2"></i>Reopen</a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item st-api-toggle-draft" href="#" data-id="<?= $step['id'] ?>" data-token="<?= generateCSRFToken() ?>">
                                            <i class="bi <?= $step['is_draft'] ? 'bi-eye' : 'bi-eye-slash' ?> me-2"></i><?= $step['is_draft'] ? 'Publish' : 'Make Draft' ?>
                                        </a></li>
                                        <li><a class="dropdown-item st-edit-step-btn" href="#" 
                                            data-id="<?= $step['id'] ?>" 
                                            data-title="<?= sanitize($step['title']) ?>" 
                                            data-description="<?= sanitize($step['description']) ?>" 
                                            data-days="<?= $step['estimated_days'] ?>">
                                            <i class="bi bi-pencil me-2"></i>Edit Step
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="<?= SITE_URL ?>/api/steps.php?action=delete&id=<?= $step['id'] ?>&token=<?= generateCSRFToken() ?>" onclick="return confirmDelete('Delete this step?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Skill Tree Visualization -->
        <div class="st-card mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-2 text-info"></i>Skill Tree</h6>
            <?php if (!empty($steps)): ?>
                <div class="st-skill-tree">
                    <?php foreach ($steps as $i => $step): ?>
                        <div class="st-skill-node <?= $step['status'] ?> d-block mb-2 w-100" style="font-size:0.78rem;">
                            <i class="bi <?= $step['status'] === 'completed' ? 'bi-check-circle-fill text-success' : ($step['status'] === 'in_progress' ? 'bi-play-circle text-primary' : 'bi-circle text-muted') ?>"></i>
                            <span><?= sanitize(truncateText($step['title'], 25)) ?></span>
                        </div>
                        <?php if ($i < count($steps) - 1): ?>
                            <div class="text-center" style="margin: -6px 0;"><i class="bi bi-arrow-down text-muted" style="font-size:0.7rem;"></i></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0" style="font-size:0.85rem;">No steps yet.</p>
            <?php endif; ?>
        </div>

        <!-- Journey Info -->
        <div class="st-card mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Details</h6>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Created</small><small><?= formatDate($journey['created_at']) ?></small></div>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Updated</small><small><?= timeAgo($journey['updated_at']) ?></small></div>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Steps</small><small><?= $journey['total_steps'] ?></small></div>
            <div class="d-flex justify-content-between"><small class="text-muted">Completion</small><small><?= completionPercent($journey['completed_steps'], $journey['total_steps']) ?>%</small></div>
        </div>

        <!-- Private Notes (for cloned journeys) -->
        <?php if (isLoggedIn() && !$isOwner && $userHasCloned): ?>
        <div class="st-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-journal-bookmark me-2 text-warning"></i>Your Notes</h6>
            <p class="text-muted mb-0" style="font-size:0.85rem;">View step details to add private notes on individual steps.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Step Modal -->
<?php if ($isOwner): ?>
<div class="modal fade" id="editStepModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content st-card p-0" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Step</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= SITE_URL ?>/api/steps.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="step_id" id="edit_step_id">
                    <div class="mb-3">
                        <label class="st-form-label">Step Title *</label>
                        <input type="text" name="title" id="edit_step_title" class="form-control st-form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="st-form-label">Estimated Days</label>
                        <input type="number" name="estimated_days" id="edit_step_days" class="form-control st-form-control" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="st-form-label">Description</label>
                        <textarea name="description" id="edit_step_desc" class="form-control st-form-control" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-st-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-st-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
