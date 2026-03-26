<?php
/**
 * SomaTrack - Daily Logs API
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
requireLogin();

$db = getDB();
$userId = getCurrentUserId();
$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ---- Create Log ----
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        if (!checkRateLimit('api_logs', 30, 3600)) {
            setFlash('danger', 'Too many requests. Please try again later.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $stepId        = (int)($_POST['step_id'] ?? 0);
        $content       = trim($_POST['content'] ?? '');
        $codeSnippet   = trim($_POST['code_snippet'] ?? '') ?: null;
        $codeLanguage  = trim($_POST['code_language'] ?? '') ?: null;
        $youtubeUrl    = trim($_POST['youtube_url'] ?? '') ?: null;
        $githubUrl     = trim($_POST['github_commit_url'] ?? '') ?: null;
        $linksRaw      = trim($_POST['external_links'] ?? '');

        // Parse external links
        $externalLinks = null;
        if (!empty($linksRaw)) {
            $links = array_filter(array_map('trim', explode("\n", $linksRaw)));
            $valid = [];
            foreach ($links as $link) {
                if (filter_var($link, FILTER_VALIDATE_URL)) {
                    $valid[] = $link;
                }
            }
            $externalLinks = !empty($valid) ? json_encode($valid) : null;
        }

        // Verify step exists & user owns journey
        $stmt = $db->prepare("SELECT s.*, j.user_id FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE s.id = ?");
        $stmt->execute([$stepId]);
        $step = $stmt->fetch();

        if (!$step || (int)$step['user_id'] !== $userId) {
            setFlash('danger', 'Step not found.');
            redirect(SITE_URL . '/user/dashboard.php');
        }

        if (empty($content)) {
            setFlash('danger', 'Log content is required.');
            redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
        }

        $logDate = date('Y-m-d');

        // Check if already logged today for this step
        $existing = $db->prepare("SELECT id FROM daily_logs WHERE step_id = ? AND user_id = ? AND log_date = ?");
        $existing->execute([$stepId, $userId, $logDate]);
        if ($existing->fetch()) {
            // Update existing
            $stmt = $db->prepare("UPDATE daily_logs SET content = ?, code_snippet = ?, code_language = ?, youtube_url = ?, github_commit_url = ?, external_links = ?, updated_at = NOW() WHERE step_id = ? AND user_id = ? AND log_date = ?");
            $stmt->execute([$content, $codeSnippet, $codeLanguage, $youtubeUrl, $githubUrl, $externalLinks, $stepId, $userId, $logDate]);
            setFlash('success', 'Today\'s log updated!');
        } else {
            $stmt = $db->prepare("INSERT INTO daily_logs (step_id, user_id, log_date, content, code_snippet, code_language, youtube_url, github_commit_url, external_links) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$stepId, $userId, $logDate, $content, $codeSnippet, $codeLanguage, $youtubeUrl, $githubUrl, $externalLinks]);
            setFlash('success', 'Log entry saved!');
        }

        // Update streak
        updateStreak($userId);
        checkBadges($userId);

        redirect(SITE_URL . '/journey/step.php?id=' . $stepId);
        break;

    // ---- Update Log ----
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(SITE_URL); }
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        $logId         = (int)($_POST['log_id'] ?? 0);
        $content       = trim($_POST['content'] ?? '');
        $codeSnippet   = trim($_POST['code_snippet'] ?? '') ?: null;
        $codeLanguage  = trim($_POST['code_language'] ?? '') ?: null;
        $youtubeUrl    = trim($_POST['youtube_url'] ?? '') ?: null;
        $githubUrl     = trim($_POST['github_commit_url'] ?? '') ?: null;
        $linksRaw      = trim($_POST['external_links'] ?? '');

        // Verify ownership
        $stmt = $db->prepare("SELECT id, step_id FROM daily_logs WHERE id = ? AND user_id = ?");
        $stmt->execute([$logId, $userId]);
        $log = $stmt->fetch();

        if (!$log) {
            setFlash('danger', 'Log entry not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        if (empty($content)) {
            setFlash('danger', 'Log content is required.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }

        // Parse external links
        $externalLinks = null;
        if (!empty($linksRaw)) {
            $links = array_filter(array_map('trim', explode("\n", $linksRaw)));
            $valid = [];
            foreach ($links as $link) {
                if (filter_var($link, FILTER_VALIDATE_URL)) {
                    $valid[] = $link;
                }
            }
            $externalLinks = !empty($valid) ? json_encode($valid) : null;
        }

        $stmt = $db->prepare("UPDATE daily_logs SET content = ?, code_snippet = ?, code_language = ?, youtube_url = ?, github_commit_url = ?, external_links = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$content, $codeSnippet, $codeLanguage, $youtubeUrl, $githubUrl, $externalLinks, $logId, $userId]);

        setFlash('success', 'Log entry updated!');
        redirect(SITE_URL . '/journey/step.php?id=' . $log['step_id']);
        break;

    // ---- Delete Log ----
    case 'delete':
        $logId = (int)($_GET['id'] ?? 0);
        $token = $_GET['token'] ?? '';

        if (!validateCSRFToken($token)) {
            setFlash('danger', 'Invalid request.');
            redirect(SITE_URL);
        }

        $stmt = $db->prepare("SELECT dl.*, s.id AS step_id FROM daily_logs dl JOIN steps s ON dl.step_id = s.id WHERE dl.id = ? AND dl.user_id = ?");
        $stmt->execute([$logId, $userId]);
        $log = $stmt->fetch();

        if (!$log) {
            setFlash('danger', 'Log not found.');
            redirect(SITE_URL . '/user/dashboard.php');
        }

        $db->prepare("DELETE FROM daily_logs WHERE id = ?")->execute([$logId]);
        setFlash('success', 'Log entry deleted.');
        redirect(SITE_URL . '/journey/step.php?id=' . $log['step_id']);
        break;

    default:
        redirect(SITE_URL . '/user/dashboard.php');
}
