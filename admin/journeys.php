<?php
/**
 * SomaTrack - Admin: Manage Journeys
 */
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
$db = getDB();
$adminId = getCurrentUserId();

// Handle actions
if (isset($_GET['action'])) {
    $targetId = (int)($_GET['id'] ?? 0);
    $token = $_GET['token'] ?? '';
    if (!validateCSRFToken($token)) { setFlash('danger', 'Invalid request.'); redirect(SITE_URL . '/admin/journeys.php'); }

    switch ($_GET['action']) {
        case 'delete':
            $j = $db->prepare("SELECT title FROM journeys WHERE id = ?");
            $j->execute([$targetId]);
            $journey = $j->fetch();
            $db->prepare("DELETE FROM journeys WHERE id = ?")->execute([$targetId]);
            $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address) VALUES (?, 'delete_journey', 'journey', ?, ?, ?)")
                ->execute([$adminId, $targetId, 'Deleted: ' . ($journey['title'] ?? ''), $_SERVER['REMOTE_ADDR']]);
            setFlash('success', 'Journey deleted.');
            break;
        case 'toggle_feature':
            $db->prepare("UPDATE journeys SET is_featured = NOT is_featured WHERE id = ?")->execute([$targetId]);
            $db->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, ip_address) VALUES (?, 'toggle_featured', 'journey', ?, 'Toggled featured status', ?)")
                ->execute([$adminId, $targetId, $_SERVER['REMOTE_ADDR']]);
            setFlash('success', 'Featured status updated.');
            break;
    }
    redirect(SITE_URL . '/admin/journeys.php');
}

require_once __DIR__ . '/includes/admin_header.php';

$search = trim($_GET['q'] ?? '');
$where = '1=1';
$params = [];
if (!empty($search)) {
    $where = "(j.title LIKE ? OR u.username LIKE ?)";
    $params = ["%$search%", "%$search%"];
}

$stmt = $db->prepare("SELECT j.*, u.username, c.name AS category_name FROM journeys j JOIN users u ON j.user_id = u.id LEFT JOIN categories c ON j.category_id = c.id WHERE $where ORDER BY j.created_at DESC LIMIT 100");
$stmt->execute($params);
$journeys = $stmt->fetchAll();
?>

<div class="st-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="st-page-title"><i class="bi bi-map me-2"></i>Manage Journeys</h1>
        <p class="st-page-subtitle"><?= count($journeys) ?> journeys</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control st-form-control" placeholder="Search..." value="<?= sanitize($search) ?>" style="width:220px;">
        <button class="btn btn-st-primary btn-sm"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="st-card">
    <div class="table-responsive">
        <table class="st-table">
            <thead><tr><th>Journey</th><th>Author</th><th>Category</th><th>Steps</th><th>Views</th><th>Clones</th><th>Status</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($journeys as $j): ?>
                <tr>
                    <td>
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="fw-semibold text-decoration-none"><?= sanitize(truncateText($j['title'], 35)) ?></a>
                        <br><small class="text-muted"><?= $j['visibility'] ?></small>
                    </td>
                    <td><small><?= sanitize($j['username']) ?></small></td>
                    <td><small class="text-muted"><?= sanitize($j['category_name'] ?? '-') ?></small></td>
                    <td><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?></td>
                    <td><?= number_format($j['view_count']) ?></td>
                    <td><?= $j['clone_count'] ?></td>
                    <td><span class="st-badge <?= $j['status'] === 'active' ? 'st-badge-success' : ($j['status'] === 'completed' ? 'st-badge-info' : 'st-badge-warning') ?>"><?= $j['status'] ?></span></td>
                    <td>
                        <a href="?action=toggle_feature&id=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-link">
                            <i class="bi <?= $j['is_featured'] ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>"></i>
                        </a>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                                <li><a class="dropdown-item" href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>"><i class="bi bi-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item" href="?action=toggle_feature&id=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>"><i class="bi bi-star me-2"></i><?= $j['is_featured'] ? 'Unfeature' : 'Feature' ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="?action=delete&id=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>" onclick="return confirmDelete('Delete this journey?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
