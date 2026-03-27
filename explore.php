<?php
/**
 * SomaTrack - Explore Journeys
 */
$pageTitle = 'Explore Journeys';
require_once __DIR__ . '/includes/dashboard_header.php';

$db = getDB();

// Filters
$search     = trim($_GET['q'] ?? '');
$catFilter  = (int)($_GET['category'] ?? 0);
$sortBy     = $_GET['sort'] ?? 'popular';

$where = "j.visibility = 'public'";
$params = [];

// Filter out user's own journeys and journeys they have already cloned
if (isLoggedIn()) {
    $currentUserId = getCurrentUserId();
    $where .= " AND j.user_id != ?";
    $params[] = $currentUserId;
    
    $where .= " AND NOT EXISTS (
        SELECT 1 FROM cloned_journeys cj 
        WHERE cj.original_journey_id = j.id AND cj.user_id = ?
    )";
    $params[] = $currentUserId;
}

if (!empty($search)) {
    $where .= " AND (j.title LIKE ? OR j.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catFilter > 0) {
    $where .= " AND j.category_id = ?";
    $params[] = $catFilter;
}

$orderBy = match($sortBy) {
    'newest'     => 'j.created_at DESC',
    'completion' => 'completion_pct DESC',
    'clones'     => 'j.clone_count DESC',
    default      => 'j.view_count DESC'
};

$stmt = $db->prepare("SELECT j.*, u.username, u.avatar, c.name AS category_name,
    CASE WHEN j.total_steps > 0 THEN ROUND((j.completed_steps / j.total_steps) * 100) ELSE 0 END AS completion_pct
    FROM journeys j 
    JOIN users u ON j.user_id = u.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE $where 
    ORDER BY $orderBy 
    LIMIT 30");
$stmt->execute($params);
$journeys = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-compass me-2"></i>Explore Journeys</h1>
    <p class="st-page-subtitle">Discover learning paths from the community</p>
</div>

<!-- Search & Filters -->
<div class="st-card mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="st-form-label">Search</label>
            <input type="text" name="q" class="form-control st-form-control" placeholder="Search journeys..." value="<?= sanitize($search) ?>">
        </div>
        <div class="col-md-3">
            <label class="st-form-label">Category</label>
            <select name="category" class="form-select st-form-control">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $catFilter == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="st-form-label">Sort By</label>
            <select name="sort" class="form-select st-form-control">
                <option value="popular" <?= $sortBy === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="completion" <?= $sortBy === 'completion' ? 'selected' : '' ?>>Completion Rate</option>
                <option value="clones" <?= $sortBy === 'clones' ? 'selected' : '' ?>>Most Cloned</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-st-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
    </form>
</div>

<!-- Results -->
<?php if (empty($journeys)): ?>
    <div class="st-card">
        <div class="st-empty-state">
            <i class="bi bi-compass d-block"></i>
            <h5>No journeys found</h5>
            <p>Try adjusting your filters or search terms.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($journeys as $j): ?>
        <div class="col-md-6 col-xl-4">
            <div class="st-journey-card">
                <div class="card-header-gradient"></div>
                <div class="card-body">
                    <div class="card-meta">
                        <div class="st-avatar-initial" style="width:24px;height:24px;font-size:0.7rem;">
                            <?= substr(sanitize(!empty($j['full_name']) ? $j['full_name'] : $j['username']), 0, 1) ?>
                        </div>
                        <span><?= sanitize($j['username']) ?></span>
                        <?php if ($j['category_name']): ?>
                            <span class="st-badge st-badge-primary"><?= sanitize($j['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title">
                        <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="text-decoration-none text-light"><?= sanitize($j['title']) ?></a>
                    </h5>
                    <p class="text-muted mb-3" style="font-size:0.85rem;"><?= truncateText(sanitize($j['description'] ?? ''), 100) ?></p>
                    <small class="text-muted"><i class="bi bi-layers me-1"></i><?= $j['total_steps'] ?> steps to master</small>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-3">
                        <small class="text-muted"><i class="bi bi-eye me-1"></i><?= number_format($j['view_count']) ?></small>
                        <small class="text-muted"><i class="bi bi-copy me-1"></i><?= $j['clone_count'] ?></small>
                    </div>
                    <a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-st-secondary">Explore Path</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/dashboard_footer.php'; ?>
