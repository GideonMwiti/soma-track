<?php
/**
 * SomaTrack - Aha! Votes API
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('danger', 'Invalid request.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
}

if (!checkRateLimit('api_votes', 100, 3600)) {
    setFlash('danger', 'Too many votes! Please slow down.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
}

$stepId = (int)($_POST['step_id'] ?? 0);
$voteType = $_POST['vote_type'] ?? '';

if (!in_array($voteType, ['helpful', 'breakthrough'])) {
    setFlash('danger', 'Invalid vote type.');
    redirect(SITE_URL);
}

// Verify step exists
$step = $db->prepare("SELECT s.*, j.user_id AS journey_owner FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
$step->execute([$stepId]);
$stepData = $step->fetch();

if (!$stepData) {
    setFlash('danger', 'Step not found.');
    redirect(SITE_URL);
}

// Can't vote on own step
if ((int)$stepData['journey_owner'] === $userId) {
    setFlash('warning', 'You cannot vote on your own steps.');
    redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
}

// Check existing vote
$existing = $db->prepare("SELECT * FROM aha_votes WHERE step_id = ? AND user_id = ?");
$existing->execute([$stepId, $userId]);
$existingVote = $existing->fetch();

if ($existingVote) {
    if ($existingVote['vote_type'] === $voteType) {
        // Remove vote (toggle)
        $db->prepare("DELETE FROM aha_votes WHERE id = ?")->execute([$existingVote['id']]);
        setFlash('success', 'Vote removed.');
    } else {
        // Change vote type
        $db->prepare("UPDATE aha_votes SET vote_type = ? WHERE id = ?")->execute([$voteType, $existingVote['id']]);
        setFlash('success', 'Vote updated!');
    }
} else {
    // New vote
    $db->prepare("INSERT INTO aha_votes (step_id, user_id, vote_type) VALUES (?, ?, ?)")->execute([$stepId, $userId, $voteType]);

    // Notify journey owner
    $user = getCurrentUser();
    $label = $voteType === 'helpful' ? 'Helpful' : 'Breakthrough';
    createNotification(
        (int)$stepData['journey_owner'],
        'aha_vote',
        'New Aha! Vote',
        $user['username'] . ' marked "' . $stepData['title'] . '" as ' . $label,
        SITE_URL . '/journey/step.php?id=' . $stepId
    );

    checkBadges((int)$stepData['journey_owner']);
    setFlash('success', 'Aha! vote recorded!');
}

redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
