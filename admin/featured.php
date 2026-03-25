<?php
/**
 * SomaTrack - Admin: Featured Journeys
 */
$pageTitle = 'Featured Journeys';
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

$featured = $db->query("SELECT j.*, u.username, c.name AS category_name 
    FROM journeys j JOIN users u ON j.user_id = u.id LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.is_featured = 1 ORDER BY j.view_count DESC")->fetchAll();

// Top candidates for featuring
$candidates = $db->query("SELECT j.*, u.username, c.name AS category_name 
    FROM journeys j JOIN users u ON j.user_id = u.id LEFT JOIN categories c ON j.category_id = c.id
    WHERE j.is_featured = 0 AND j.visibility = 'public' AND j.total_steps >= 3
    ORDER BY j.clone_count DESC, j.view_count DESC LIMIT 10")->fetchAll();
?>

<div class="st-page-header">
    <h1 class="st-page-title"><i class="bi bi-star me-2"></i>Featured Journeys</h1>
    <p class="st-page-subtitle">Manage which journeys appear on the landing page</p>
</div>

<!-- Currently Featured -->
<div class="st-card mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Currently Featured (<?= count($featured) ?>)</h5>
    <?php if (empty($featured)): ?>
        <p class="text-muted">No featured journeys. Feature some from the list below!</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="st-table">
                <thead><tr><th>Journey</th><th>Author</th><th>Views</th><th>Clones</th><th>Steps</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($featured as $j): ?>
                    <tr>
                        <td><a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="fw-semibold text-decoration-none"><?= sanitize($j['title']) ?></a></td>
                        <td><small><?= sanitize($j['username']) ?></small></td>
                        <td><?= number_format($j['view_count']) ?></td>
                        <td><?= $j['clone_count'] ?></td>
                        <td><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/journeys.php?action=toggle_feature&id=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-star-fill me-1"></i>Unfeature</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Candidates -->
<div class="st-card">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-info"></i>Top Candidates to Feature</h5>
    <p class="text-muted mb-3" style="font-size:0.85rem;">Public journeys with 3+ steps, sorted by popularity.</p>
    <?php if (empty($candidates)): ?>
        <p class="text-muted">No candidates found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="st-table">
                <thead><tr><th>Journey</th><th>Author</th><th>Category</th><th>Views</th><th>Clones</th><th>Steps</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($candidates as $j): ?>
                    <tr>
                        <td><a href="<?= SITE_URL ?>/journey/view.php?id=<?= $j['id'] ?>" class="fw-semibold text-decoration-none"><?= sanitize(truncateText($j['title'], 35)) ?></a></td>
                        <td><small><?= sanitize($j['username']) ?></small></td>
                        <td><small class="text-muted"><?= sanitize($j['category_name'] ?? '-') ?></small></td>
                        <td><?= number_format($j['view_count']) ?></td>
                        <td><?= $j['clone_count'] ?></td>
                        <td><?= $j['completed_steps'] ?>/<?= $j['total_steps'] ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/journeys.php?action=toggle_feature&id=<?= $j['id'] ?>&token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-st-primary"><i class="bi bi-star me-1"></i>Feature</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
