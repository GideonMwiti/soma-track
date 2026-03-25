<?php
/**
 * SomaTrack - Portfolio / Public Journey Page
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();

if (!isset($_GET['id'])) { redirect(SITE_URL); }
$journeyId = (int)$_GET['id'];

$stmt = $db->prepare("SELECT j.*, u.username, u.full_name, u.avatar, u.bio, c.name AS category_name 
    FROM journeys j JOIN users u ON j.user_id = u.id LEFT JOIN categories c ON j.category_id = c.id 
    WHERE j.id = ? AND j.visibility = 'public'");
$stmt->execute([$journeyId]);
$journey = $stmt->fetch();

if (!$journey) { redirect(SITE_URL); }

$steps = $db->prepare("SELECT s.*, 
    (SELECT COUNT(*) FROM aha_votes WHERE step_id = s.id) AS aha_count,
    (SELECT COUNT(*) FROM step_comments WHERE step_id = s.id AND is_deleted = 0) AS comment_count
    FROM steps s WHERE s.journey_id = ? ORDER BY s.step_number ASC");
$steps->execute([$journeyId]);
$allSteps = $steps->fetchAll();

// Get all logs for this journey
$logs = $db->prepare("SELECT dl.*, s.title AS step_title, s.step_number FROM daily_logs dl JOIN steps s ON dl.step_id = s.id WHERE s.journey_id = ? AND dl.user_id = ? ORDER BY dl.log_date ASC");
$logs->execute([$journeyId, $journey['user_id']]);
$allLogs = $logs->fetchAll();

$pageTitle = $journey['title'] . ' - Portfolio';
$bodyClass = '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?> | <?= SITE_NAME ?></title>
    <meta name="description" content="<?= sanitize(truncateText($journey['description'] ?? '', 155)) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Portfolio Header -->
<div class="st-portfolio-header">
    <div class="container text-center">
        <img src="<?= getAvatarUrl($journey['avatar']) ?>" class="rounded-circle mb-3" width="80" height="80" alt="">
        <h1 class="fw-bold mb-2"><?= sanitize($journey['title']) ?></h1>
        <p class="mb-2" style="opacity:0.9;">by <strong><?= sanitize($journey['full_name']) ?></strong> (@<?= sanitize($journey['username']) ?>)</p>
        <?php if ($journey['category_name']): ?>
            <span class="badge bg-light text-dark"><?= sanitize($journey['category_name']) ?></span>
        <?php endif; ?>
        <div class="mt-3">
            <span class="me-3"><i class="bi bi-check-circle me-1"></i><?= $journey['completed_steps'] ?>/<?= $journey['total_steps'] ?> steps</span>
            <span class="me-3"><i class="bi bi-eye me-1"></i><?= number_format($journey['view_count']) ?> views</span>
            <span><i class="bi bi-copy me-1"></i><?= $journey['clone_count'] ?> clones</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if ($journey['description']): ?>
        <div class="st-card mb-4">
            <h5 class="fw-bold mb-2">About this Journey</h5>
            <p class="text-muted mb-0"><?= nl2br(sanitize($journey['description'])) ?></p>
        </div>
    <?php endif; ?>

    <!-- Progress -->
    <div class="st-card mb-4">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-semibold">Overall Progress</span>
            <span style="color:var(--st-primary-light);"><?= completionPercent($journey['completed_steps'], $journey['total_steps']) ?>%</span>
        </div>
        <div class="st-progress" style="height:12px;"><div class="st-progress-bar" style="width:<?= completionPercent($journey['completed_steps'], $journey['total_steps']) ?>%"></div></div>
    </div>

    <!-- Timeline -->
    <h4 class="fw-bold mb-3"><i class="bi bi-signpost-2 me-2"></i>Learning Timeline</h4>
    <div class="st-timeline">
        <?php foreach ($allSteps as $step): ?>
        <div class="st-timeline-item <?= $step['status'] === 'completed' ? 'completed' : ($step['status'] === 'in_progress' ? 'active' : '') ?>">
            <div class="st-card mb-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="fw-bold" style="color:var(--st-primary-light);">Step <?= $step['step_number'] ?></span>
                    <?php $sb = ['pending'=>'st-badge-warning','in_progress'=>'st-badge-info','completed'=>'st-badge-success']; ?>
                    <span class="st-badge <?= $sb[$step['status']] ?>"><?= ucfirst(str_replace('_',' ',$step['status'])) ?></span>
                    <span class="text-muted ms-auto" style="font-size:0.75rem;"><i class="bi bi-lightbulb me-1"></i><?= $step['aha_count'] ?> Aha! · <i class="bi bi-chat me-1"></i><?= $step['comment_count'] ?></span>
                </div>
                <h6 class="fw-semibold"><?= sanitize($step['title']) ?></h6>
                <?php if ($step['description']): ?>
                    <p class="text-muted mb-0" style="font-size:0.85rem;"><?= nl2br(sanitize($step['description'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($allLogs)): ?>
    <h4 class="fw-bold mt-5 mb-3"><i class="bi bi-journal-text me-2"></i>Daily Log Entries</h4>
    <?php foreach ($allLogs as $log): ?>
    <div class="st-card mb-3">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-semibold" style="font-size:0.85rem;">Step <?= $log['step_number'] ?>: <?= sanitize($log['step_title']) ?></span>
            <small class="text-muted"><?= formatDate($log['log_date']) ?></small>
        </div>
        <p class="mb-0" style="font-size:0.9rem;"><?= nl2br(sanitize($log['content'])) ?></p>
        <?php if ($log['code_snippet']): ?>
            <div class="st-code-block mt-2"><pre class="mb-0"><code><?= sanitize($log['code_snippet']) ?></code></pre></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Footer -->
    <div class="text-center mt-5 py-4" style="border-top:1px solid var(--st-dark-border);">
        <p class="text-muted mb-2">Powered by <a href="<?= SITE_URL ?>"><strong>SomaTrack</strong></a></p>
        <p class="text-muted" style="font-size:0.8rem;">Journey started <?= formatDate($journey['created_at']) ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
