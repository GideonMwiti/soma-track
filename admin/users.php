<?php
/**
 * SomaTrack - Admin: Manage Users
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
$db = getDB();
$adminId = getCurrentUserId();

// Handle actions
if (isset($_GET['action'])) {
    $targetId = (int)($_GET['id'] ?? 0);
    $token = $_GET['token'] ?? '';
    if (!validateCSRFToken($token)) { setFlash('danger', 'Invalid request.'); redirect(SITE_URL . '/admin/users.php'); }

    switch ($_GET['action']) {
        case 'toggle_active':
            $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND id != ?")->execute([$targetId, $adminId]);
            $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address) VALUES (?, 'toggle_user_status', 'user', ?, 'Toggled user active status', ?)")
                ->execute([$adminId, $targetId, $_SERVER['REMOTE_ADDR']]);
            setFlash('success', 'User status updated.');
            break;
        case 'delete':
            if ($targetId !== $adminId) {
                $db->prepare("DELETE FROM users WHERE id = ? AND id != ?")->execute([$targetId, $adminId]);
                $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address) VALUES (?, 'delete_user', 'user', ?, 'User deleted', ?)")
                   ->execute([$adminId, $targetId, $_SERVER['REMOTE_ADDR']]);
                setFlash('success', 'User deleted.');
            }
            break;
    }
    redirect(SITE_URL . '/admin/users.php');
}

require_once __DIR__ . '/includes/admin_header.php';

// Search
$search = trim($_GET['q'] ?? '');
$where = "u.role != 'admin'";
$params = [];
if (!empty($search)) {
    $where = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?) AND u.role != 'admin'";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$users = $db->prepare("SELECT u.*, (SELECT COUNT(*) FROM journeys WHERE user_id = u.id) AS journey_count FROM users u WHERE $where ORDER BY u.created_at DESC");
$users->execute($params);
$allUsers = $users->fetchAll();
?>

<div class="st-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="st-page-title"><i class="bi bi-people me-2"></i>Manage Users</h1>
        <p class="st-page-subtitle"><?= count($allUsers) ?> users total</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control st-form-control" placeholder="Search users..." value="<?= sanitize($search) ?>" style="width:220px;">
        <button class="btn btn-st-primary btn-sm"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="st-card">
    <div class="table-responsive">
        <table class="st-table">
            <thead>
                <tr><th>User</th><th>Email</th><th>Journeys</th><th>Streak</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($allUsers as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="st-avatar-initial" style="width:36px;height:36px;font-size:0.85rem;background:var(--st-primary-light);color:#fff;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                                <?= substr(sanitize(!empty($u['full_name']) ? $u['full_name'] : $u['username']), 0, 1) ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= sanitize($u['full_name']) ?></div>
                                <small class="text-muted"><?= sanitize($u['username']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><small><?= sanitize($u['email']) ?></small></td>
                    <td><?= $u['journey_count'] ?></td>
                    <td><?= $u['current_streak'] ?>d</td>
                    <td>
                        <?php if ($u['is_active']): ?>
                            <span class="st-badge st-badge-success">Active</span>
                        <?php else: ?>
                            <span class="st-badge st-badge-danger">Disabled</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?= formatDate($u['created_at'], 'M d, Y') ?></small></td>
                    <td>
                        <?php if ((int)$u['id'] !== $adminId): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                                <li><a class="dropdown-item" href="<?= SITE_URL ?>/user/profile.php?id=<?= $u['id'] ?>"><i class="bi bi-eye me-2"></i>View Profile</a></li>
                                <li><a class="dropdown-item" href="?action=toggle_active&id=<?= $u['id'] ?>&token=<?= generateCSRFToken() ?>"><i class="bi bi-toggle-on me-2"></i><?= $u['is_active'] ? 'Disable' : 'Enable' ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="?action=delete&id=<?= $u['id'] ?>&token=<?= generateCSRFToken() ?>" onclick="return confirmDelete('Delete this user and all their data?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
