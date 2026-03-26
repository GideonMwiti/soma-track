<?php
/**
 * SomaTrack - Admin Analytics
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$db = getDB();
$pageTitle = 'Platform Analytics';

// Fetch stats
$userGrowth = $db->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM users GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 30")->fetchAll();
$journeyStats = $db->query("SELECT status, COUNT(*) as count FROM journeys GROUP BY status")->fetchAll();
$categoryStats = $db->query("SELECT c.name, COUNT(j.id) as count FROM categories c LEFT JOIN journeys j ON c.id = j.category_id GROUP BY c.id ORDER BY count DESC LIMIT 5")->fetchAll();
$stepCompletion = $db->query("SELECT status, COUNT(*) as count FROM steps GROUP BY status")->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Platform Analytics</h2>
    <div class="text-muted small">Real-time system insights</div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="st-stat-card">
            <div class="st-stat-label text-info">Completion Rate</div>
            <div class="st-stat-value">
                <?php
                $totalSteps = array_sum(array_column($stepCompletion, 'count'));
                $completed = 0;
                foreach($stepCompletion as $s) if($s['status'] == 'completed') $completed = $s['count'];
                echo completionPercent($completed, $totalSteps) . '%';
                ?>
            </div>
            <div class="small text-muted">Across all journeys</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="st-stat-card">
            <div class="st-stat-label text-success">Total Logs</div>
            <div class="st-stat-value"><?= number_format($db->query("SELECT COUNT(*) FROM daily_logs")->fetchColumn()) ?></div>
            <div class="small text-muted">Proof-of-work entries</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="st-stat-card">
            <div class="st-stat-label text-warning">Aha! Moments</div>
            <div class="st-stat-value"><?= number_format($db->query("SELECT COUNT(*) FROM aha_votes")->fetchColumn()) ?></div>
            <div class="small text-muted">Breakthroughs shared</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="st-stat-card">
            <div class="st-stat-label text-primary">Cloned Paths</div>
            <div class="st-stat-value"><?= number_format($db->query("SELECT COUNT(*) FROM cloned_journeys")->fetchColumn()) ?></div>
            <div class="small text-muted">Total sync relationships</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Category Distribution -->
    <div class="col-lg-6">
        <div class="st-card h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-pie-chart me-2 text-info"></i>Top Categories</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Journeys</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryStats as $cat): ?>
                            <tr>
                                <td><?= sanitize($cat['name']) ?></td>
                                <td class="text-end fw-bold text-info"><?= $cat['count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Growth (Table for now, can be chart) -->
    <div class="col-lg-6">
        <div class="st-card h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-graph-up me-2 text-success"></i>Recent User Signups</h5>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">New Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userGrowth as $growth): ?>
                            <tr>
                                <td><?= formatDate($growth['date']) ?></td>
                                <td class="text-end fw-bold text-success">+<?= $growth['count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
