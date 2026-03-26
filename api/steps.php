<?php
/**
 * SomaTrack - Steps API
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ---- Create Step ----
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL . '/user/dashboard.php'); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        if (!checkRateLimit('api_steps', 20, 3600)) {
            setFlash('danger', 'Too many requests. Please wait a while.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $journeyId    = (int)($_POST['journey_id'] ?? 0);
        $title        = trim($_POST['title'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $estimatedDays = !empty($_POST['estimated_days']) ? (int)$_POST['estimated_days'] : null;

        // Verify ownership
        $jStmt = $db->prepare("SELECT id, total_steps FROM journeys WHERE id = ? AND user_id = ?");
        $jStmt->execute([$journeyId, $userId]);
        $journey = $jStmt->fetch();

        if (!$journey) {
            setFlash('danger', 'Journey not found.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        if (empty($title)) {
            setFlash('danger', 'Step title is required.');
            redirect(SITE_URL . '/journey/view.php?id=' . $journeyId);
        }

        $stepNumber = $journey['total_steps'] + 1;

        $db->prepare("INSERT INTO steps (journey_id, step_number, title, description, estimated_days) VALUES (?, ?, ?, ?, ?)")->execute([$journeyId, $stepNumber, $title, $description ?: null, $estimatedDays]);

        syncJourneyProgress($journeyId);

        // Notify cloned journey users about update
        $clones = $db->prepare("SELECT user_id FROM cloned_journeys WHERE original_journey_id = ?");
        $clones->execute([$journeyId]);
        while ($clone = $clones->fetch()) {
            $db->prepare("UPDATE cloned_journeys SET is_synced = 0 WHERE original_journey_id = ? AND user_id = ?")->execute([$journeyId, $clone['user_id']]);
            createNotification($clone['user_id'], 'sync_update', 'Journey Updated', 'A new step was added to "' . $title . '". Sync your cloned journey.', SITE_URL . '/journey/view.php?id=' . $journeyId);
        }

        setFlash('success', 'Step added!');
        redirect(SITE_URL . '/journey/view.php?id=' . $journeyId);
        break;

    // ---- Update Step ----
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL . '/user/dashboard.php'); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $stepId       = (int)($_POST['step_id'] ?? 0);
        $title        = trim($_POST['title'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $estimatedDays = !empty($_POST['estimated_days']) ? (int)$_POST['estimated_days'] : null;

        // Verify ownership via journey
        $stmt = $db->prepare("SELECT s.*, j.user_id, j.id AS journey_id FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch();

        if (!$step || (int)$step['user_id'] !== $userId) {
            setFlash('danger', 'Step not found.');
            redirect(SITE_URL . '/user/dashboard.php');
        }

        if (empty($title)) {
            setFlash('danger', 'Step title is required.');
            redirect(SITE_URL . '/journey/view.php?id=' . $step['journey_id']);
        }

        $stmt = $db->prepare("UPDATE steps SET title = ?, description = ?, estimated_days = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $description ?: null, $estimatedDays, $stepId]);

        setFlash('success', 'Step updated!');
        redirect(SITE_URL . '/journey/view.php?id=' . $step['journey_id']);
        break;

    // ---- Update Step Status ----
    case 'status':
        $stepId = (int)($_GET['id'] ?? 0);
        $newStatus = $_GET['status'] ?? '';
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        if (!in_array($newStatus, ['pending', 'in_progress', 'completed'])) {
            setFlash('danger', 'Invalid status.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        // Verify ownership
        $stmt = $db->prepare("SELECT s.*, j.user_id, j.id AS journey_id FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch();

        if (!$step || (int)$step['user_id'] !== $userId) {
            setFlash('danger', 'Step not found.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        $oldStatus = $step['status'];
        $db->prepare("UPDATE steps SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$newStatus, $stepId]);

        // Sync journey progress
        syncJourneyProgress($step['journey_id']);

        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            updateStreak($userId);
            checkBadges($userId);
        }

        // Check if all steps completed → mark journey completed
        $remaining = $db->prepare("SELECT COUNT(*) FROM steps WHERE journey_id = ? AND status != 'completed'");
        $remaining->execute([$step['journey_id']]);
        if ((int)$remaining->fetchColumn() === 0) {
            $db->prepare("UPDATE journeys SET status = 'completed', updated_at = NOW() WHERE id = ? AND status = 'active'")->execute([$step['journey_id']]);
            checkBadges($userId);
        }

        if ($_GET['format'] === 'json') {
            jsonResponse(['success' => true, 'message' => 'Step status updated.', 'new_status' => $newStatus]);
        }
        setFlash('success', 'Step status updated.');
        redirect(SITE_URL . '/journey/view.php?id=' . $step['journey_id']);
        break;

    // ---- Delete Step ----
    case 'delete':
        $stepId = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        $stmt = $db->prepare("SELECT s.*, j.user_id, j.id AS journey_id FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch();

        if (!$step || (int)$step['user_id'] !== $userId) {
            setFlash('danger', 'Step not found.');
            redirect(SITE_URL . '/user/journeys.php');
        }

        $wasCompleted = $step['status'] === 'completed';
        $db->prepare("DELETE FROM steps WHERE id = ?")->execute([$stepId]);

        // Renumber remaining steps
        $remainingSteps = $db->prepare("SELECT id FROM steps WHERE journey_id = ? ORDER BY step_number ASC");
        $remainingSteps->execute([$step['journey_id']]);
        $num = 1;
        while ($rs = $remainingSteps->fetch()) {
            $db->prepare("UPDATE steps SET step_number = ? WHERE id = ?")->execute([$num++, $rs['id']]);
        }

        // Sync journey progress
        syncJourneyProgress($step['journey_id']);

        setFlash('success', 'Step deleted.');
        redirect(SITE_URL . '/journey/view.php?id=' . $step['journey_id']);
        break;

    // ---- Toggle Draft ----
    case 'toggle_draft':
        $stepId = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $stmt = $db->prepare("SELECT s.*, j.user_id, j.id AS journey_id FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch();

        if (!$step || (int)$step['user_id'] !== $userId) {
            setFlash('danger', 'Step not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $newDraft = $step['is_draft'] ? 0 : 1;
        $db->prepare("UPDATE steps SET is_draft = ? WHERE id = ?")->execute([$newDraft, $stepId]);

        if ($_GET['format'] === 'json') {
            jsonResponse(['success' => true, 'message' => $newDraft ? 'Step hidden as draft.' : 'Step is now public.', 'is_draft' => $newDraft]);
        }
        setFlash('success', $newDraft ? 'Step hidden as draft.' : 'Step is now public.');
        redirect(SITE_URL . '/journey/view.php?id=' . $step['journey_id']);
        break;

    default:
        redirect(SITE_URL . '/user/dashboard.php');
}
