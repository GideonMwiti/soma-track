<?php
/**
 * SomaTrack - Admin: Activity Logs
 */
$pageTitle = 'Admin Logs';
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

$logs = $db->query("SELECT al.*, u.username FROM admin_logs al JOIN users u ON al.admin_id = u.id ORDER BY al.created_at DESC LIMIT 100")->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-journal-code me-2"></i>Admin Activity Logs</h1>
    <p class="st-page-subtitle">All administrative actions</p>
</div>

<div class="st-card">
    <?php if (empty($logs)): ?>
        <div class="st-empty-state py-4">
            <i class="bi bi-journal d-block"></i>
            <h6 class="text-muted">No admin actions recorded yet</h6>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="st-table">
                <thead><tr><th>Admin</th><th>Action</th><th>Target</th><th>Details</th><th>IP</th><th>Time</th></tr></thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="fw-semibold"><?= sanitize($log['username']) ?></td>
                        <td><span class="st-badge st-badge-info"><?= sanitize($log['action']) ?></span></td>
                        <td><small class="text-muted"><?= sanitize($log['target_type'] ?? '-') ?> #<?= $log['target_id'] ?? '-' ?></small></td>
                        <td><small class="text-muted"><?= sanitize($log['details'] ?? '-') ?></small></td>
                        <td><small class="text-muted"><?= sanitize($log['ip_address'] ?? '-') ?></small></td>
                        <td><small class="text-muted"><?= timeAgo($log['created_at']) ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
