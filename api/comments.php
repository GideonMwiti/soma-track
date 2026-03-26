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

        if (!checkRateLimit('api_comments', 30, 3600)) {
            setFlash('danger', 'Too many requests. Please try again later.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $stepId    = (int)($_POST['step_id'] ?? 0);
        $parentId  = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $content   = trim($_POST['content'] ?? '');

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

        // Verify parent comment if exists
        if ($parentId) {
            $stmt = $db->prepare("SELECT id, user_id FROM step_comments WHERE id = ? AND step_id = ?");
            $stmt->execute([$parentId, $stepId]);
            $parent = $stmt->fetch();
            if (!$parent) $parentId = null; // Ignore invalid parent
        }

        $stmt = $db->prepare("INSERT INTO step_comments (step_id, user_id, content, parent_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$stepId, $userId, $content, $parentId]);
        $newId = $db->lastInsertId();

        // Notify parent author if it's a reply
        if ($parentId && (int)$parent['user_id'] !== $userId) {
            $user = getCurrentUser();
            createNotification(
                (int)$parent['user_id'],
                'comment',
                'Reply to your comment',
                $user['username'] . ' replied to your comment',
                SITE_URL . '/journey/step.php?id=' . $stepId . '#comment-' . $newId
            );
        }

        // Notify journey owner (if not the one who commented and not already notified as parent)
        if ((int)$stepData['journey_owner'] !== $userId && (!$parentId || (int)$parent['user_id'] !== (int)$stepData['journey_owner'])) {
            $user = getCurrentUser();
            createNotification(
                (int)$stepData['journey_owner'],
                'comment',
                'New Comment',
                $user['username'] . ' commented on "' . $stepData['title'] . '"',
                SITE_URL . '/journey/step.php?id=' . $stepId
            );
        }

        redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $content   = trim($_POST['content'] ?? '');

        if (empty($content)) {
            setFlash('danger', 'Comment cannot be empty.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        // Verify ownership
        $stmt = $db->prepare("SELECT * FROM step_comments WHERE id = ? AND user_id = ?");
        $stmt->execute([$commentId, $userId]);
        $comment = $stmt->fetch();

        if (!$comment) {
            setFlash('danger', 'Comment not found or access denied.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $db->prepare("UPDATE step_comments SET content = ?, updated_at = NOW() WHERE id = ?")->execute([$content, $commentId]);

        setFlash('success', 'Comment updated!');
        redirect(SITE_URL . '/journey/step.php?id=' . $comment['step_id']);
        break;

    case 'delete':
        $commentId = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL);
        }

        // Get comment with journey owner info for moderation check
        $stmt = $db->prepare("SELECT c.*, j.user_id AS journey_owner_id FROM step_comments c 
            JOIN steps s ON c.step_id = s.id 
            JOIN journeys j ON s.journey_id = j.id 
            WHERE c.id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch();

        if (!$comment) {
            setFlash('danger', 'Comment not found.');
            redirect(SITE_URL);
        }

        // Allow delete by comment author, journey owner, or admin
        $isAuthor = (int)$comment['user_id'] === $userId;
        $isJourneyOwner = (int)$comment['journey_owner_id'] === $userId;
        
        if (!$isAuthor && !$isJourneyOwner && !isAdmin()) {
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
