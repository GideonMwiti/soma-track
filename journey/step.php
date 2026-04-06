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
$allComments = $commentsStmt->fetchAll();

// Group comments by parent
$comments = [];
$replies = [];
foreach ($allComments as $c) {
    if (!$c['parent_id']) {
        $comments[$c['id']] = $c;
        $comments[$c['id']]['replies'] = [];
    } else {
        $replies[] = $c;
    }
}
foreach ($replies as $r) {
    if (isset($comments[$r['parent_id']])) {
        $comments[$r['parent_id']]['replies'][] = $r;
    }
}

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
                            <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                                <?= strtoupper(substr(sanitize(ltrim(!empty($log['full_name']) ? $log['full_name'] : $log['username'], '@')), 0, 1)) ?>
                            </div>
                            <strong style="font-size:0.85rem;"><?= sanitize(ltrim($log['username'], '@')) ?></strong>
                            <small class="text-muted"><?= formatDate($log['log_date']) ?></small>
                        </div>
                        <?php if (isLoggedIn() && (int)$log['user_id'] === getCurrentUserId()): ?>
                            <div class="d-flex gap-1">
                                <a href="#" class="btn btn-sm btn-link text-info st-edit-log-btn" 
                                   data-id="<?= $log['id'] ?>"
                                   data-content="<?= sanitize($log['content']) ?>"
                                   data-code="<?= sanitize($log['code_snippet'] ?? '') ?>"
                                   data-lang="<?= sanitize($log['code_language'] ?? '') ?>"
                                   data-youtube="<?= sanitize($log['youtube_url'] ?? '') ?>"
                                   data-github="<?= sanitize($log['github_commit_url'] ?? '') ?>"
                                   data-links="<?= sanitize(implode("\n", json_decode($log['external_links'] ?? '[]', true))) ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/api/logs.php?action=delete&id=<?= $log['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-link text-danger" onclick="return confirmDelete('Delete this log?')"><i class="bi bi-trash"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="mb-2" style="font-size:0.9rem;line-height:1.6;"><?= nl2br(sanitize($log['content'])) ?></p>

                    <?php if ($log['code_snippet']): ?>
                        <div class="st-code-block mb-3 position-relative group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.5px;"><?= sanitize($log['code_language'] ?? 'code') ?></small>
                                <button class="btn btn-link p-0 text-muted st-copy-code-btn" data-code="<?= sanitize($log['code_snippet']) ?>" title="Copy Code">
                                    <i class="bi bi-copy" style="font-size:0.85rem;"></i>
                                </button>
                            </div>
                            <pre class="mb-0"><code><?= sanitize($log['code_snippet']) ?></code></pre>
                        </div>
                    <?php endif; ?>

                    <?php if ($log['youtube_url']): ?>
                        <?php
                        $ytUrl = $log['youtube_url'];
                        $ytId = '';
                        // Robust regex for YouTube IDs
                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $ytUrl, $match)) {
                            $ytId = $match[1];
                        }
                        if ($ytId):
                        ?>
                        <div class="ratio ratio-16x9 mb-2" style="max-width:400px;border-radius:var(--st-radius-sm);overflow:hidden;">
                            <iframe src="https://www.youtube.com/embed/<?= sanitize($ytId) ?>" allowfullscreen></iframe>
                        </div>
                        <?php else: ?>
                        <a href="<?= sanitize($ytUrl) ?>" target="_blank" class="d-inline-block mb-1 text-danger" style="font-size:0.8rem;"><i class="bi bi-youtube me-1"></i>View YouTube Resource</a><br>
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
                <div class="st-comment mb-3" id="comment-<?= $comment['id'] ?>">
                    <div class="st-avatar-initial" style="width:36px;height:36px;font-size:0.9rem;">
                        <?= strtoupper(substr(sanitize(ltrim(!empty($comment['full_name']) ? $comment['full_name'] : $comment['username'], '@')), 0, 1)) ?>
                    </div>
                    <div class="flex-grow-1">
                        <div class="st-comment-meta">
                            <strong><?= sanitize(ltrim($comment['username'], '@')) ?></strong> · <?= timeAgo($comment['created_at']) ?>
                        </div>
                        <div class="st-comment-body mb-2"><?= nl2br(sanitize($comment['content'])) ?></div>
                        
                        <div class="d-flex align-items-center gap-3">
                            <?php if (isLoggedIn()): ?>
                                <a href="#" class="text-muted st-reply-toggle" data-id="<?= $comment['id'] ?>" style="font-size:0.75rem;"><i class="bi bi-reply me-1"></i>Reply</a>
                                <?php if ((int)$comment['user_id'] === getCurrentUserId()): ?>
                                    <a href="#" class="text-info st-edit-comment-btn" style="font-size:0.75rem;" 
                                       data-id="<?= $comment['id'] ?>" 
                                       data-content="<?= sanitize($comment['content']) ?>">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                <?php endif; ?>
                                <?php if ((int)$comment['user_id'] === getCurrentUserId() || $isOwner || isAdmin()): ?>
                                    <a href="<?= SITE_URL ?>/api/comments.php?action=delete&id=<?= $comment['id'] ?>&token=<?= generateCSRFToken() ?>" class="text-danger" style="font-size:0.75rem;" onclick="return confirmDelete('Delete this comment?')"><i class="bi bi-trash me-1"></i>Delete</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Reply Form (Hidden) -->
                        <div class="mt-2 d-none st-reply-form" id="reply-form-<?= $comment['id'] ?>">
                            <form method="POST" action="<?= SITE_URL ?>/api/comments.php">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="step_id" value="<?= $stepId ?>">
                                <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                <textarea name="content" class="form-control st-form-control mb-2" rows="2" placeholder="Write a reply..." required></textarea>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-st-primary btn-xs" style="padding:2px 10px; font-size:0.7rem;">Post Reply</button>
                                    <button type="button" class="btn btn-st-secondary btn-xs st-reply-cancel" data-id="<?= $comment['id'] ?>" style="padding:2px 10px; font-size:0.7rem;">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Nested Replies -->
                        <?php if (!empty($comment['replies'])): ?>
                            <div class="st-comment-replies mt-3 ps-3 border-start">
                                <?php foreach ($comment['replies'] as $reply): ?>
                                    <div class="st-comment mb-2" id="comment-<?= $reply['id'] ?>">
                                        <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                                            <?= strtoupper(substr(sanitize(ltrim(!empty($reply['full_name']) ? $reply['full_name'] : $reply['username'], '@')), 0, 1)) ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="st-comment-meta">
                                                <strong><?= sanitize(ltrim($reply['username'], '@')) ?></strong> · <?= timeAgo($reply['created_at']) ?>
                                            </div>
                                            <div class="st-comment-body mb-1" style="font-size:0.85rem;"><?= nl2br(sanitize($reply['content'])) ?></div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (isLoggedIn()): ?>
                                                    <?php if ((int)$reply['user_id'] === getCurrentUserId()): ?>
                                                        <a href="#" class="text-info st-edit-comment-btn" style="font-size:0.7rem;" 
                                                           data-id="<?= $reply['id'] ?>" 
                                                           data-content="<?= sanitize($reply['content']) ?>">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ((int)$reply['user_id'] === getCurrentUserId() || $isOwner || isAdmin()): ?>
                                                        <a href="<?= SITE_URL ?>/api/comments.php?action=delete&id=<?= $reply['id'] ?>&token=<?= generateCSRFToken() ?>" class="text-danger" style="font-size:0.7rem;" onclick="return confirmDelete('Delete this?')"><i class="bi bi-trash me-1"></i>Delete</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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

<!-- Edit Log Modal -->
<div class="modal fade" id="editLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content st-card p-0" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Log Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= SITE_URL ?>/api/logs.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="log_id" id="edit_log_id">
                    
                    <div class="mb-3">
                        <label class="st-form-label">What did you accomplish? *</label>
                        <textarea name="content" id="edit_log_content" class="form-control st-form-control" rows="4" required></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="st-form-label">Code Snippet (optional)</label>
                            <textarea name="code_snippet" id="edit_log_code" class="form-control st-form-control" rows="5" style="font-family:monospace;font-size:0.85rem;"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="st-form-label">Language</label>
                            <select name="code_language" id="edit_log_lang" class="form-select st-form-control">
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

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="st-form-label">YouTube URL</label>
                            <input type="url" name="youtube_url" id="edit_log_youtube" class="form-control st-form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="st-form-label">GitHub Commit URL</label>
                            <input type="url" name="github_commit_url" id="edit_log_github" class="form-control st-form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="st-form-label">External Links (one per line)</label>
                        <textarea name="external_links" id="edit_log_links" class="form-control st-form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-st-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-st-primary btn-sm">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content st-card p-0" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Comment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= SITE_URL ?>/api/comments.php">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="comment_id" id="edit_comment_id">
                    <div class="mb-3">
                        <label class="st-form-label">Comment Content</label>
                        <textarea name="content" id="edit_comment_content" class="form-control st-form-control" rows="4" required></textarea>
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

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
