<?php
/**
 * SomaTrack - Private Notes API
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

$stepId = (int)($_POST['step_id'] ?? 0);
$noteContent = trim($_POST['note_content'] ?? '');

// Verify step exists
$step = $db->prepare("SELECT id FROM steps WHERE id = ?");
$step->execute([$stepId]);
if (!$step->fetch()) {
    setFlash('danger', 'Step not found.');
    redirect(SITE_URL);
}

if (empty($noteContent)) {
    // Delete note
    $db->prepare("DELETE FROM private_notes WHERE step_id = ? AND user_id = ?")->execute([$stepId, $userId]);
    setFlash('success', 'Note removed.');
} else {
    // Upsert
    $existing = $db->prepare("SELECT id FROM private_notes WHERE step_id = ? AND user_id = ?");
    $existing->execute([$stepId, $userId]);

    if ($existing->fetch()) {
        $db->prepare("UPDATE private_notes SET note_content = ?, updated_at = NOW() WHERE step_id = ? AND user_id = ?")->execute([$noteContent, $stepId, $userId]);
    } else {
        $db->prepare("INSERT INTO private_notes (step_id, user_id, note_content) VALUES (?, ?, ?)")->execute([$stepId, $userId, $noteContent]);
    }
    setFlash('success', 'Note saved!');
}

redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
