<?php
/**
 * SomaTrack - Journey API (Clone & Sync)
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ---- Clone Journey ----
    case 'clone':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $journeyId = (int)($_POST['journey_id'] ?? 0);

        // Get original journey
        $origStmt = $db->prepare("SELECT * FROM journeys WHERE id = ? AND visibility = 'public'");
        $origStmt->execute([$journeyId]);
        $original = $origStmt->fetch();

        if (!$original) {
            setFlash('danger', 'Journey not found or is private.');
            redirect(SITE_URL . '/explore.php');
        }

        // Can't clone own journey
        if ((int)$original['user_id'] === $userId) {
            setFlash('warning', 'You cannot clone your own journey.');
            redirect(SITE_URL . '/journey/view.php?id=' . $journeyId);
        }

        // Check if already cloned
        $existingClone = $db->prepare("SELECT id FROM cloned_journeys WHERE original_journey_id = ? AND user_id = ?");
        $existingClone->execute([$journeyId, $userId]);
        if ($existingClone->fetch()) {
            setFlash('warning', 'You have already cloned this journey.');
            redirect(SITE_URL . '/journey/view.php?id=' . $journeyId);
        }

        // Create cloned journey
        $slug = generateSlug($original['title']);
        $checkSlug = $db->prepare("SELECT id FROM journeys WHERE user_id = ? AND slug = ?");
        $checkSlug->execute([$userId, $slug]);
        if ($checkSlug->fetch()) {
            $slug .= '-' . time();
        }

        $db->beginTransaction();
        try {
            // Insert new journey
            $ins = $db->prepare("INSERT INTO journeys (user_id, title, slug, description, category_id, visibility, total_steps) VALUES (?, ?, ?, ?, ?, 'private', ?)");
            $ins->execute([$userId, $original['title'], $slug, $original['description'], $original['category_id'], $original['total_steps']]);
            $newJourneyId = $db->lastInsertId();

            // Clone all steps
            $stepsStmt = $db->prepare("SELECT * FROM steps WHERE journey_id = ? ORDER BY step_number ASC");
            $stepsStmt->execute([$journeyId]);
            $steps = $stepsStmt->fetchAll();

            foreach ($steps as $step) {
                $db->prepare("INSERT INTO steps (journey_id, step_number, title, description, estimated_days) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$newJourneyId, $step['step_number'], $step['title'], $step['description'], $step['estimated_days']]);
            }

            // Record clone relationship
            $db->prepare("INSERT INTO cloned_journeys (original_journey_id, cloned_journey_id, user_id, last_synced_at) VALUES (?, ?, ?, NOW())")
                ->execute([$journeyId, $newJourneyId, $userId]);

            // Increment clone count
            $db->prepare("UPDATE journeys SET clone_count = clone_count + 1 WHERE id = ?")->execute([$journeyId]);

            // Notify original author
            $user = getCurrentUser();
            createNotification(
                (int)$original['user_id'],
                'clone',
                'Journey Cloned!',
                $user['username'] . ' cloned your journey "' . $original['title'] . '"',
                SITE_URL . '/journey/view.php?id=' . $journeyId
            );

            checkBadges((int)$original['user_id']);

            $db->commit();
            setFlash('success', 'Journey cloned! You can now track your own progress.');
            redirect(SITE_URL . '/journey/view.php?id=' . $newJourneyId);

        } catch (Exception $e) {
            $db->rollBack();
            error_log('Clone failed: ' . $e->getMessage());
            setFlash('danger', 'Failed to clone journey.');
            redirect(SITE_URL . '/journey/view.php?id=' . $journeyId);
        }
        break;

    // ---- Sync Clone ----
    case 'sync':
        $cloneId = (int)($_GET['clone_id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL . '/user/cloned.php');
        }

        $cloneStmt = $db->prepare("SELECT * FROM cloned_journeys WHERE id = ? AND user_id = ?");
        $cloneStmt->execute([$cloneId, $userId]);
        $clone = $cloneStmt->fetch();

        if (!$clone) {
            setFlash('danger', 'Clone not found.');
            redirect(SITE_URL . '/user/cloned.php');
        }

        $db->beginTransaction();
        try {
            // Get original steps
            $origSteps = $db->prepare("SELECT * FROM steps WHERE journey_id = ? ORDER BY step_number ASC");
            $origSteps->execute([$clone['original_journey_id']]);
            $originalSteps = $origSteps->fetchAll();

            // Get current cloned steps
            $clonedSteps = $db->prepare("SELECT * FROM steps WHERE journey_id = ? ORDER BY step_number ASC");
            $clonedSteps->execute([$clone['cloned_journey_id']]);
            $existingSteps = $clonedSteps->fetchAll();
            $existingCount = count($existingSteps);

            // Add new steps that don't exist yet
            foreach ($originalSteps as $os) {
                if ($os['step_number'] > $existingCount) {
                    $db->prepare("INSERT INTO steps (journey_id, step_number, title, description, estimated_days) VALUES (?, ?, ?, ?, ?)")
                        ->execute([$clone['cloned_journey_id'], $os['step_number'], $os['title'], $os['description'], $os['estimated_days']]);
                }
            }

            // Update journey total steps
            $newTotal = count($originalSteps);
            $db->prepare("UPDATE journeys SET total_steps = ?, updated_at = NOW() WHERE id = ?")->execute([$newTotal, $clone['cloned_journey_id']]);

            // Mark as synced
            $db->prepare("UPDATE cloned_journeys SET is_synced = 1, last_synced_at = NOW() WHERE id = ?")->execute([$cloneId]);

            $db->commit();
            setFlash('success', 'Journey synced with latest updates!');

        } catch (Exception $e) {
            $db->rollBack();
            error_log('Sync failed: ' . $e->getMessage());
            setFlash('danger', 'Sync failed.');
        }

        redirect(SITE_URL . '/journey/view.php?id=' . $clone['cloned_journey_id']);
        break;

    default:
        redirect(SITE_URL . '/user/dashboard.php');
}
