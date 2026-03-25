<?php
/**
 * SomaTrack - Admin Contacts/Messages Management
 */
$pageTitle = 'Contacts';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();
$filter = $_GET['filter'] ?? 'all'; // all, new, replied
$search = $_GET['search'] ?? '';
$action = $_POST['action'] ?? '';

// Handle mark as replied
if ($action === 'mark_replied' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['contact_id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE contact_messages SET status = 'replied', replied_at = NOW() WHERE id = ?");
        if ($stmt->execute([$id])) {
            setFlash('success', 'Message marked as replied.');
        }
        header('Location: ' . SITE_URL . '/admin/contacts.php?filter=' . $filter);
        exit;
    }
}

// Handle delete
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['contact_id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        if ($stmt->execute([$id])) {
            setFlash('success', 'Message deleted successfully.');
        }
        header('Location: ' . SITE_URL . '/admin/contacts.php?filter=' . $filter);
        exit;
    }
}

// Build query
$query = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];

if ($filter === 'new') {
    $query .= " AND status = 'new'";
} elseif ($filter === 'replied') {
    $query .= " AND status = 'replied'";
}

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params = array_fill(0, 4, $searchTerm);
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get counts
$newCount = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
$repliedCount = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'replied'")->fetchColumn();
$totalCount = $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
?>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-envelope me-2"></i>Contact Messages</h1>
        <span class="badge bg-info"><?= $totalCount ?> Total</span>
    </div>

    <!-- Filter tabs -->
    <ul class="nav nav-tabs mb-4" style="border-bottom: 2px solid var(--st-border);">
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'all' ? 'active' : '' ?>" href="?filter=all">
                All <span class="badge bg-secondary ms-2"><?= $totalCount ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'new' ? 'active' : '' ?>" href="?filter=new">
                New <span class="badge bg-danger ms-2"><?= $newCount ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $filter === 'replied' ? 'active' : '' ?>" href="?filter=replied">
                Replied <span class="badge bg-success ms-2"><?= $repliedCount ?></span>
            </a>
        </li>
    </ul>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <input type="hidden" name="filter" value="<?= sanitize($filter) ?>">
        <div class="input-group">
            <input type="text" name="search" class="form-control st-form-control" placeholder="Search by name, email, subject..." value="<?= sanitize($search) ?>">
            <button type="submit" class="btn btn-st-primary">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="?filter=<?= sanitize($filter) ?>" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if (empty($messages)): ?>
    <div class="st-card text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: var(--st-muted);"></i>
        <p class="text-muted mt-3">No messages found.</p>
    </div>
<?php else: ?>
    <div class="st-card table-responsive">
        <table class="table table-hover mb-0">
            <thead style="background: rgba(255,255,255,0.03); border-bottom: 2px solid var(--st-border);">
                <tr>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 20%;">Subject</th>
                    <th style="width: 35%;">Message</th>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr style="border-bottom: 1px solid var(--st-border);">
                        <td>
                            <div>
                                <strong><?= sanitize($msg['name']) ?></strong>
                                <br>
                                <small class="text-muted"><?= sanitize($msg['email']) ?></small>
                            </div>
                            <?php if ($msg['status'] === 'new'): ?>
                                <span class="badge bg-danger mt-2">NEW</span>
                            <?php elseif ($msg['status'] === 'replied'): ?>
                                <span class="badge bg-success mt-2">REPLIED</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= sanitize($msg['subject']) ?></strong>
                        </td>
                        <td>
                            <small class="text-muted"><?= sanitize(truncateText($msg['message'], 80)) ?></small>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= date('M d, Y', strtotime($msg['created_at'])) ?>
                                <br>
                                <?= date('g:i A', strtotime($msg['created_at'])) ?>
                            </small>
                        </td>
                        <td style="text-align: center;">
                            <div class="btn-group" role="group">
                                <a href="mailto:<?= urlencode($msg['email']) ?>?subject=<?= urlencode('Re: ' . $msg['subject']) ?>&body=<?= urlencode("Hi " . $msg['name'] . ",\n\n" . $msg['message'] . "\n\n---\nOriginal message received on " . date('M d, Y', strtotime($msg['created_at']))) ?>" class="btn btn-sm btn-outline-primary" title="Reply via Email">
                                    <i class="bi bi-reply"></i>
                                </a>
                                <?php if ($msg['status'] !== 'replied'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_replied">
                                        <input type="hidden" name="contact_id" value="<?= $msg['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Replied">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $msg['id'] ?>" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Full message modal -->
                    <div class="modal fade" id="messageModal<?= $msg['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content st-modal-content">
                                <div class="modal-header st-modal-header">
                                    <h5 class="modal-title"><?= sanitize($msg['subject']) ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <small class="text-muted">From: <strong><?= sanitize($msg['name']) ?></strong> (<?= sanitize($msg['email']) ?>)</small>
                                        <br>
                                        <small class="text-muted"><?= date('M d, Y @ g:i A', strtotime($msg['created_at'])) ?></small>
                                    </div>
                                    <div class="p-3" style="background: rgba(255,255,255,0.02); border-left: 3px solid var(--st-primary); border-radius: 4px;">
                                        <p class="mb-0 text-wrap"><?= nl2br(sanitize($msg['message'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $msg['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content st-modal-content">
                                <div class="modal-header st-modal-header">
                                    <h5 class="modal-title">Delete Message?</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this message from <strong><?= sanitize($msg['name']) ?></strong>?</p>
                                    <p class="text-muted"><em><?= sanitize($msg['subject']) ?></em></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="contact_id" value="<?= $msg['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
