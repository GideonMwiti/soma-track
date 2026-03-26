<?php
/**
 * SomaTrack - Admin Moderation Queue
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$db = getDB();
$pageTitle = 'Moderation Queue';

// Handle deletion actions
if (isset($_GET['action']) && isset($_GET['id']) && validateCSRFToken($_GET['token'] ?? '')) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'del_log') {
        $db->prepare("DELETE FROM daily_logs WHERE id = ?")->execute([$id]);
        setFlash('success', 'Log deleted successfully.');
    } elseif ($_GET['action'] === 'del_comment') {
        $db->prepare("UPDATE step_comments SET is_deleted = 1 WHERE id = ?")->execute([$id]);
        setFlash('success', 'Comment hidden.');
    }
    header('Location: moderation.php');
    exit;
}

// Fetch recent activity
$recentLogs = $db->query("SELECT dl.*, u.username, s.title as step_title FROM daily_logs dl JOIN users u ON dl.user_id = u.id JOIN steps s ON dl.step_id = s.id ORDER BY dl.created_at DESC LIMIT 50")->fetchAll();
$recentComments = $db->query("SELECT sc.*, u.username, s.title as step_title FROM step_comments sc JOIN users u ON sc.user_id = u.id JOIN steps s ON sc.step_id = s.id WHERE sc.is_deleted = 0 ORDER BY sc.created_at DESC LIMIT 50")->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Moderation Queue</h2>
    <span class="st-badge st-badge-warning animate-pulse">Community Monitor</span>
</div>

<?= displayFlash() ?>

<ul class="nav nav-pills mb-4" id="modTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="logs-tab" data-bs-toggle="pill" data-bs-target="#logs" type="button">Recent Logs (<?= count($recentLogs) ?>)</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="comments-tab" data-bs-toggle="pill" data-bs-target="#comments" type="button">Recent Comments (<?= count($recentComments) ?>)</button>
    </li>
</ul>

<div class="tab-content" id="modTabContent">
    <!-- Logs Tab -->
    <div class="tab-pane fade show active" id="logs" role="tabpanel">
        <div class="st-card p-0" style="overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Entry Content</th>
                            <th>Step</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentLogs as $log): ?>
                            <tr>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                                            <?= substr(sanitize($log['username']), 0, 1) ?>
                                        </div>
                                        <?= sanitize($log['username']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small mb-1"><?= formatDate($log['log_date']) ?></div>
                                    <div class="text-truncate" style="max-width: 300px;"><?= sanitize($log['content']) ?></div>
                                </td>
                                <td><span class="small"><?= sanitize($log['step_title']) ?></span></td>
                                <td class="text-end">
                                    <a href="moderation.php?action=del_log&id=<?= $log['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this log permanently?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Comments Tab -->
    <div class="tab-pane fade" id="comments" role="tabpanel">
        <div class="st-card p-0" style="overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Comment</th>
                            <th>Step</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentComments as $comm): ?>
                            <tr>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="st-avatar-initial" style="width:28px;height:28px;font-size:0.75rem;">
                                            <?= substr(sanitize($comm['username']), 0, 1) ?>
                                        </div>
                                        <?= sanitize($comm['username']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small mb-1"><?= timeAgo($comm['created_at']) ?></div>
                                    <div class="text-truncate" style="max-width: 300px;"><?= sanitize($comm['content']) ?></div>
                                </td>
                                <td><span class="small"><?= sanitize($comm['step_title']) ?></span></td>
                                <td class="text-end">
                                    <a href="moderation.php?action=del_comment&id=<?= $comm['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hide this comment?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
