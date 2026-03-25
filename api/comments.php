<?php
/**
 * SomaTrack - Comments API
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $stepId  = (int)($_POST['step_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            setFlash('danger', 'Comment cannot be empty.');
            redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
        }

        // Verify step exists
        $step = $db->prepare("SELECT s.*, j.user_id AS journey_owner FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $step->execute([$stepId]);
        $stepData = $step->fetch();

        if (!$stepData) {
            setFlash('danger', 'Step not found.');
            redirect(SITE_URL);
        }

        $db->prepare("INSERT INTO step_comments (step_id, user_id, content) VALUES (?, ?, ?)")->execute([$stepId, $userId, $content]);

        // Notify journey owner (if not self)
        if ((int)$stepData['journey_owner'] !== $userId) {
            $user = getCurrentUser();
            createNotification(
                (int)$stepData['journey_owner'],
                'comment',
                'New Comment',
                $user['username'] . ' commented on "' . $stepData['title'] . '"',
                SITE_URL . '/journey/step.php?id=' . $stepId
            );
        }

        setFlash('success', 'Comment posted!');
        redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
        break;

    case 'delete':
        $commentId = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL);
        }

        $stmt = $db->prepare("SELECT * FROM step_comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch();

        if (!$comment) {
            setFlash('danger', 'Comment not found.');
            redirect(SITE_URL);
        }

        // Allow delete by comment author or admin
        if ((int)$comment['user_id'] !== $userId && !isAdmin()) {
            setFlash('danger', 'Not authorized.');
            redirect(SITE_URL);
        }

        $db->prepare("UPDATE step_comments SET is_deleted = 1 WHERE id = ?")->execute([$commentId]);
        setFlash('success', 'Comment deleted.');
        redirect(SITE_URL . '/journey/step.php?id=' . $comment['step_id']);
        break;

    default:
        redirect(SITE_URL);
}
