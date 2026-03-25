<?php
/**
 * SomaTrack - Step Detail (Daily Logs + Comments + Aha!)
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . SITE_URL . '/explore.php');
    exit;
}

$stepId = (int)$_GET['id'];

// Get step with journey info
$stmt = $db->prepare("SELECT s.*, j.id AS journey_id, j.title AS journey_title, j.user_id AS journey_owner_id, j.visibility, u.username AS author
    FROM steps s 
    JOIN journeys j ON s.journey_id = j.id 
    JOIN users u ON j.user_id = u.id 
    WHERE s.id = ?");
$stmt->execute([$stepId]);
$step = $stmt->fetch();

if (!$step) {
    setFlash('danger', 'Step not found.');
    header('Location: ' . SITE_URL . '/user/dashboard.php');
    exit;
}

$isOwner = isLoggedIn() && getCurrentUserId() === (int)$step['journey_owner_id'];

// Get daily logs
$logsStmt = $db->prepare("SELECT dl.*, u.username, u.avatar FROM daily_logs dl JOIN users u ON dl.user_id = u.id WHERE dl.step_id = ? ORDER BY dl.log_date DESC");
$logsStmt->execute([$stepId]);
$logs = $logsStmt->fetchAll();

// Get comments
$commentsStmt = $db->prepare("SELECT sc.*, u.username, u.avatar FROM step_comments sc JOIN users u ON sc.user_id = u.id WHERE sc.step_id = ? AND sc.is_deleted = 0 ORDER BY sc.created_at ASC");
$commentsStmt->execute([$stepId]);
$comments = $commentsStmt->fetchAll();

// Get aha votes
$ahaStmt = $db->prepare("SELECT COUNT(*) AS total, 
    SUM(CASE WHEN vote_type='helpful' THEN 1 ELSE 0 END) AS helpful,
    SUM(CASE WHEN vote_type='breakthrough' THEN 1 ELSE 0 END) AS breakthrough
    FROM aha_votes WHERE step_id = ?");
$ahaStmt->execute([$stepId]);
$ahaVotes = $ahaStmt->fetch();

// Check if current user voted
$userVoted = false;
$userVoteType = null;
if (isLoggedIn()) {
    $voteCheck = $db->prepare("SELECT vote_type FROM aha_votes WHERE step_id = ? AND user_id = ?");
    $voteCheck->execute([$stepId, getCurrentUserId()]);
    $userVoteRow = $voteCheck->fetch();
    if ($userVoteRow) {
        $userVoted = true;
        $userVoteType = $userVoteRow['vote_type'];
    }
}

// Private note (for cloned journeys)
$privateNote = null;
if (isLoggedIn() && !$isOwner) {
    $noteStmt = $db->prepare("SELECT * FROM private_notes WHERE step_id = ? AND user_id = ?");
    $noteStmt->execute([$stepId, getCurrentUserId()]);
    $privateNote = $noteStmt->fetch();
}

$pageTitle = $step['title'];
require_once __DIR__ . '/../includes/dashboard_header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb" style="font-size:0.85rem;">
        <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/journey/view.php?id=<?= $step['journey_id'] ?>"><?= sanitize($step['journey_title']) ?></a></li>
        <li class="breadcrumb-item active">Step <?= $step['step_number'] ?>: <?= sanitize($step['title']) ?></li>
    </ol>
</nav>

<!-- Step Header -->
<div class="st-card mb-4">
    <div class="d-flex align-items-start justify-content-between">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="fw-bold" style="color:var(--st-primary-light);">Step <?= $step['step_number'] ?></span>
                <?php $sb = ['pending'=>'st-badge-warning','in_progress'=>'st-badge-info','completed'=>'st-badge-success']; ?>
                <span class="st-badge <?= $sb[$step['status']] ?>"><?= ucfirst(str_replace('_',' ',$step['status'])) ?></span>
                <?php if ($step['estimated_days']): ?>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i>~<?= $step['estimated_days'] ?> days</small>
                <?php endif; ?>
            </div>
            <h3 class="fw-bold mb-1"><?= sanitize($step['title']) ?></h3>
            <?php if ($step['description']): ?>
                <p class="text-muted"><?= nl2br(sanitize($step['description'])) ?></p>
            <?php endif; ?>
        </div>
        <!-- Aha Votes -->
        <div class="text-center">
            <?php if (isLoggedIn() && !$isOwner): ?>
            <div class="d-flex gap-2">
                <form method="POST" action="<?= SITE_URL ?>/api/votes.php" class="d-inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="step_id" value="<?= $stepId ?>">
                    <input type="hidden" name="vote_type" value="helpful">
                    <button type="submit" class="btn btn-sm <?= $userVoteType === 'helpful' ? 'btn-warning' : 'btn-outline-warning' ?>" title="Mark as Helpful">
                        <i class="bi bi-hand-thumbs-up"></i> <?= $ahaVotes['helpful'] ?? 0 ?>
                    </button>
                </form>
                <form method="POST" action="<?= SITE_URL ?>/api/votes.php" class="d-inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="step_id" value="<?= $stepId ?>">
                    <input type="hidden" name="vote_type" value="breakthrough">
                    <button type="submit" class="btn btn-sm <?= $userVoteType === 'breakthrough' ? 'btn-info' : 'btn-outline-info' ?>" title="Breakthrough Resource">
                        <i class="bi bi-lightbulb"></i> <?= $ahaVotes['breakthrough'] ?? 0 ?>
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="d-flex gap-2">
                <span class="st-badge st-badge-warning"><i class="bi bi-hand-thumbs-up me-1"></i><?= $ahaVotes['helpful'] ?? 0 ?></span>
                <span class="st-badge st-badge-info"><i class="bi bi-lightbulb me-1"></i><?= $ahaVotes['breakthrough'] ?? 0 ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Add Daily Log (Owner) -->
        <?php if ($isOwner): ?>
        <div class="st-card mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-plus me-2 text-success"></i>Add Daily Log</h5>
            <form method="POST" action="<?= SITE_URL ?>/api/logs.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="step_id" value="<?= $stepId ?>">
                <div class="mb-2">
                    <label class="st-form-label">What did you learn today? *</label>
                    <textarea name="content" class="form-control st-form-control" rows="4" placeholder="Describe your progress, insights, and challenges..." required data-char-counter maxlength="5000"></textarea>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="st-form-label">Code Snippet (optional)</label>
                        <textarea name="code_snippet" class="form-control st-form-control" rows="3" placeholder="Paste code here..." style="font-family:monospace;font-size:0.85rem;"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="st-form-label">Code Language</label>
                        <select name="code_language" class="form-select st-form-control">
                            <option value="">Select language...</option>
                            <option value="html">HTML</option>
                            <option value="css">CSS</option>
                            <option value="javascript">JavaScript</option>
                            <option value="php">PHP</option>
                            <option value="python">Python</option>
                            <option value="java">Java</option>
                            <option value="sql">SQL</option>
                            <option value="bash">Bash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="st-form-label">YouTube URL (optional)</label>
                        <input type="url" name="youtube_url" class="form-control st-form-control" placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="col-md-6">
                        <label class="st-form-label">GitHub Commit URL (optional)</label>
                        <input type="url" name="github_commit_url" class="form-control st-form-control" placeholder="https://github.com/...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="st-form-label">External Links (optional, one per line)</label>
                    <textarea name="external_links" class="form-control st-form-control" rows="2" placeholder="https://resource1.com&#10;https://resource2.com"></textarea>
                </div>
                <button type="submit" class="btn btn-st-primary btn-sm"><i class="bi bi-save me-1"></i>Save Log Entry</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Daily Logs -->
        <div class="st-card mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Daily Logs (<?= count($logs) ?>)</h5>
            <?php if (empty($logs)): ?>
                <div class="st-empty-state py-3">
                    <i class="bi bi-journal d-block"></i>
                    <p class="text-muted">No logs recorded for this step yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                <div class="p-3 mb-3 rounded" style="background:var(--st-dark-surface);border:1px solid var(--st-dark-border);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= getAvatarUrl($log['avatar']) ?>" class="rounded-circle" width="28" height="28" alt="">
                            <strong style="font-size:0.85rem;"><?= sanitize($log['username']) ?></strong>
                            <small class="text-muted"><?= formatDate($log['log_date']) ?></small>
                        </div>
                        <?php if (isLoggedIn() && (int)$log['user_id'] === getCurrentUserId()): ?>
                            <a href="<?= SITE_URL ?>/api/logs.php?action=delete&id=<?= $log['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-link text-danger" onclick="return confirmDelete('Delete this log?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </div>
                    <p class="mb-2" style="font-size:0.9rem;line-height:1.6;"><?= nl2br(sanitize($log['content'])) ?></p>

                    <?php if ($log['code_snippet']): ?>
                        <div class="st-code-block mb-2">
                            <small class="text-muted d-block mb-1"><?= sanitize($log['code_language'] ?? 'code') ?></small>
                            <pre class="mb-0"><code><?= sanitize($log['code_snippet']) ?></code></pre>
                        </div>
                    <?php endif; ?>

                    <?php if ($log['youtube_url']): ?>
                        <?php
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $log['youtube_url'], $ytMatch);
                        if (!empty($ytMatch[1])):
                        ?>
                        <div class="ratio ratio-16x9 mb-2" style="max-width:400px;border-radius:var(--st-radius-sm);overflow:hidden;">
                            <iframe src="https://www.youtube.com/embed/<?= sanitize($ytMatch[1]) ?>" allowfullscreen></iframe>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($log['github_commit_url']): ?>
                        <a href="<?= sanitize($log['github_commit_url']) ?>" target="_blank" class="d-inline-block mb-1" style="font-size:0.8rem;"><i class="bi bi-github me-1"></i>View Commit</a><br>
                    <?php endif; ?>

                    <?php
                    $links = json_decode($log['external_links'] ?? '[]', true);
                    if (!empty($links)):
                    ?>
                        <div class="mt-1">
                            <?php foreach ($links as $link): ?>
                                <a href="<?= sanitize($link) ?>" target="_blank" class="me-2" style="font-size:0.8rem;"><i class="bi bi-link-45deg me-1"></i><?= sanitize(parse_url($link, PHP_URL_HOST) ?: $link) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Comments -->
        <div class="st-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-chat-dots me-2 text-info"></i>Comments (<?= count($comments) ?>)</h5>

            <?php if (isLoggedIn()): ?>
            <form method="POST" action="<?= SITE_URL ?>/api/comments.php" class="mb-3">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="step_id" value="<?= $stepId ?>">
                <textarea name="content" class="form-control st-form-control mb-2" rows="2" placeholder="Add a comment on this step..." required></textarea>
                <button type="submit" class="btn btn-st-primary btn-sm"><i class="bi bi-send me-1"></i>Post Comment</button>
            </form>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p class="text-muted" style="font-size:0.85rem;">No comments yet. Be the first to share your thoughts!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                <div class="st-comment">
                    <img src="<?= getAvatarUrl($comment['avatar']) ?>" class="st-comment-avatar" alt="">
                    <div class="flex-grow-1">
                        <div class="st-comment-meta">
                            <strong><?= sanitize($comment['username']) ?></strong> · <?= timeAgo($comment['created_at']) ?>
                        </div>
                        <div class="st-comment-body"><?= nl2br(sanitize($comment['content'])) ?></div>
                        <?php if (isLoggedIn() && ((int)$comment['user_id'] === getCurrentUserId() || isAdmin())): ?>
                            <a href="<?= SITE_URL ?>/api/comments.php?action=delete&id=<?= $comment['id'] ?>&token=<?= generateCSRFToken() ?>" class="text-danger" style="font-size:0.75rem;" onclick="return confirmDelete('Delete this comment?')"><i class="bi bi-trash"></i> Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Private Notes -->
        <?php if (isLoggedIn() && !$isOwner): ?>
        <div class="st-card mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-journal-bookmark me-2 text-warning"></i>Private Notes</h6>
            <form method="POST" action="<?= SITE_URL ?>/api/notes.php">
                <?= csrfField() ?>
                <input type="hidden" name="step_id" value="<?= $stepId ?>">
                <textarea name="note_content" class="form-control st-form-control mb-2" rows="4" placeholder="Your private notes for this step..."><?= $privateNote ? sanitize($privateNote['note_content']) : '' ?></textarea>
                <button type="submit" class="btn btn-st-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Save Note</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Step Info -->
        <div class="st-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Step Info</h6>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Logs</small><small><?= count($logs) ?></small></div>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Comments</small><small><?= count($comments) ?></small></div>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Helpful</small><small><?= $ahaVotes['helpful'] ?? 0 ?></small></div>
            <div class="mb-2 d-flex justify-content-between"><small class="text-muted">Breakthrough</small><small><?= $ahaVotes['breakthrough'] ?? 0 ?></small></div>
            <div class="d-flex justify-content-between"><small class="text-muted">Created</small><small><?= formatDate($step['created_at']) ?></small></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
