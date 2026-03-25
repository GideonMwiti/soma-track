<?php
/**
 * SomaTrack - My Journeys List
 */
$pageTitle = 'My Journeys';
require_once __DIR__ . '/../includes/dashboard_header.php';

$userId = getCurrentUserId();
$db = getDB();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!validateCSRFToken($_GET['token'] ?? '')) {
        setFlash('danger', 'Invalid request.');
    } else {
        $delStmt = $db->prepare("DELETE FROM journeys WHERE id = ? AND user_id = ?");
        $delStmt->execute([(int)$_GET['delete'], $userId]);
        if ($delStmt->rowCount() > 0) {
            setFlash('success', 'Journey deleted.');
        }
    }
    header('Location: ' . SITE_URL . '/user/journeys.php');
    exit;
}

// Filters
$statusFilter = $_GET['status'] ?? 'all';
$where = "j.user_id = ?";
$params = [$userId];

if (in_array($statusFilter, ['active', 'completed', 'archived'])) {
    $where .= " AND j.status = ?";
    $params[] = $statusFilter;
}

$stmt = $db->prepare("SELECT j.*, c.name AS category_name FROM journeys j LEFT JOIN categories c ON j.category_id = c.id WHERE $where ORDER BY j.updated_at DESC");
$stmt->execute($params);
$journeys = $stmt->fetchAll();
?>

<div class="st-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="st-page-title">My Journeys</h1>
        <p class="st-page-subtitle">Manage your learning paths</p>
    </div>
    <a href="<?= SITE_URL ?>/journey/create.php" class="btn btn-st-primary"><i class="bi bi-plus-circle me-2"></i>New Journey</a>
</div>

<!-- Filters -->
<div class="st-filter-pills mb-4">
    <a href="?status=all" class="st-filter-pill <?= $statusFilter === 'all' ? 'active' : '' ?>">All</a>
    <a href="?status=active" class="st-filter-pill <?= $statusFilter === 'active' ? 'active' : '' ?>"><i class="bi bi-lightning me-1"></i>Active</a>
    <a href="?status=completed" class="st-filter-pill <?= $statusFilter === 'completed' ? 'active' : '' ?>"><i class="bi bi-check-circle me-1"></i>Completed</a>
    <a href="?status=archived" class="st-filter-pill <?= $statusFilter === 'archived' ? 'active' : '' ?>"><i class="bi bi-archive me-1"></i>Archived</a>
</div>

<?php if (empty($journeys)): ?>
    <div class="st-card">
        <div class="st-empty-state">
            <i class="bi bi-map d-block"></i>
            <h5>No journeys found</h5>
            <p>Create your first learning journey and start tracking progress.</p>
            <a href="<?= SITE_URL ?>/journey/create.php" class="btn btn-st-primary mt-2"><i class="bi bi-plus me-1"></i>Create Journey</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($journeys as $j): ?>
        <div class="col-md-6 col-xl-4">
            <div class="st-journey-card">
                <div class="card-header-gradient"></div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <?php if ($j['category_name']): ?>
                            <span class="st-badge st-badge-primary"><?= sanitize($j['category_name']) ?></span>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                        <?php
                        $statusBadges = ['active' => 'st-badge-success', 'completed' => 'st-badge-info', 'archived' => 'st-badge-warning'];
                        ?>
                        <span class="st-badge <?= $statusBadges[$j['status']] ?? '' ?>"><?= ucfirst($j['status']) ?></span>
                    </div>
                    <h5 class="card-title">
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="text-decoration-none text-light"><?= sanitize($j['title']) ?></a>
                    </h5>
                    <p class="text-muted mb-3" style="font-size:0.85rem;"><?= truncateText(sanitize($j['description'] ?? 'No description'), 100) ?></p>
                    
                    <div class="st-progress mb-2"><div class="st-progress-bar" style="width:<?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%"></div></div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted"><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?> steps</small>
                        <small class="text-muted"><?= completionPercent($j['completed_steps'], $j['total_steps']) ?>%</small>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted"><i class="bi bi-eye me-1"></i><?= $j['view_count'] ?></small>
                        <small class="text-muted"><i class="bi bi-copy me-1"></i><?= $j['clone_count'] ?></small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end st-dropdown">
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>"><i class="bi bi-eye me-2"></i>View</a></li>
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/journey/edit.php?id=<?= $j['id'] ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="?delete=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>" onclick="return confirmDelete('Delete this journey and all its steps?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/dashboard_footer.php'; ?>
