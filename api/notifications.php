<?php
/**
 * SomaTrack - Notifications API
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$action = $_GET['action'] ?? '';

if ($action === 'read' && isset($_GET['id'])) {
    $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?")->execute([(int)$_GET['id'], $userId]);
    jsonResponse(['success' => true]);
}

jsonResponse(['error' => 'Invalid action'], 400);
