<?php
/**
 * SomaTrack - Helper Functions
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Generate CSRF token
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output CSRF hidden input field
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

/**
 * Sanitize user input
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL-friendly slug
 */
function generateSlug(string $text): string {
    $text = preg_replace('/[^a-z0-9\s-]/', '', strtolower(trim($text)));
    $text = preg_replace('/[\s-]+/', '-', $text);
    return rtrim($text, '-');
}

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'M d, Y'): string {
    return date($format, strtotime($date));
}

/**
 * Relative time (e.g., "2 hours ago")
 */
function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message as Bootstrap alert
 */
function displayFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    $type = $flash['type'];
    $msg  = sanitize($flash['message']);
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
        . $msg
        . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

/**
 * Calculate completion percentage
 */
function completionPercent(int $completed, int $total): int {
    if ($total === 0) return 0;
    return (int) round(($completed / $total) * 100);
}

/**
 * Truncate text
 */
function truncateText(string $text, int $length = 150): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Get gravatar URL from email
 */
function getAvatarUrl(string $avatar): string {
    if ($avatar === 'default-avatar.png' || empty($avatar)) {
        return SITE_URL . '/assets/img/default-avatar.png';
    }
    return SITE_URL . '/uploads/avatars/' . $avatar;
}

/**
 * JSON response helper for API
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Redirect with optional flash message
 */
function redirect(string $url, ?string $flashType = null, ?string $flashMsg = null): void {
    if ($flashType && $flashMsg) {
        setFlash($flashType, $flashMsg);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Update streak for a user
 */
function updateStreak(int $userId): void {
    $db = getDB();
    $today = date('Y-m-d');

    // Check if already logged today
    $stmt = $db->prepare("SELECT id FROM streaks WHERE user_id = ? AND streak_date = ?");
    $stmt->execute([$userId, $today]);
    if ($stmt->fetch()) return;

    // Insert today's streak
    $stmt = $db->prepare("INSERT INTO streaks (user_id, streak_date) VALUES (?, ?)");
    $stmt->execute([$userId, $today]);

    // Calculate current streak
    $stmt = $db->prepare("SELECT streak_date FROM streaks WHERE user_id = ? ORDER BY streak_date DESC");
    $stmt->execute([$userId]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $streak = 0;
    $checkDate = new DateTime($today);
    foreach ($dates as $d) {
        if ($d === $checkDate->format('Y-m-d')) {
            $streak++;
            $checkDate->modify('-1 day');
        } else {
            break;
        }
    }

    // Update user streak counters
    $stmt = $db->prepare("UPDATE users SET current_streak = ?, longest_streak = GREATEST(longest_streak, ?), last_activity_date = ? WHERE id = ?");
    $stmt->execute([$streak, $streak, $today, $userId]);
}

/**
 * Create a notification
 */
function createNotification(int $userId, string $type, string $title, string $message, ?string $link = null): void {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $title, $message, $link]);
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount(int $userId): int {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

/**
 * Check and award badges
 */
function checkBadges(int $userId): void {
    $db = getDB();

    // Get user stats
    $stats = getUserStats($userId);

    // Get badges user doesn't have yet
    $stmt = $db->prepare("SELECT b.* FROM badges b WHERE b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)");
    $stmt->execute([$userId]);
    $availableBadges = $stmt->fetchAll();

    foreach ($availableBadges as $badge) {
        $earned = false;
        switch ($badge['criteria_type']) {
            case 'streak':
                $earned = $stats['longest_streak'] >= $badge['criteria_value'];
                break;
            case 'journeys_completed':
                $earned = $stats['completed_journeys'] >= $badge['criteria_value'];
                break;
            case 'steps_completed':
                $earned = $stats['completed_steps'] >= $badge['criteria_value'];
                break;
            case 'clones':
                $earned = $stats['total_clones'] >= $badge['criteria_value'];
                break;
            case 'aha_votes':
                $earned = $stats['total_aha_received'] >= $badge['criteria_value'];
                break;
        }
        if ($earned) {
            $ins = $db->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $ins->execute([$userId, $badge['id']]);
            createNotification($userId, 'badge', 'New Badge Earned!', 'You earned the "' . $badge['name'] . '" badge!', SITE_URL . '/user/profile.php');
        }
    }
}

/**
 * Get user statistics
 */
function getUserStats(int $userId): array {
    $db = getDB();

    $stmt = $db->prepare("SELECT current_streak, longest_streak FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $stmt = $db->prepare("SELECT COUNT(*) FROM journeys WHERE user_id = ? AND status = 'completed'");
    $stmt->execute([$userId]);
    $completedJourneys = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM steps s JOIN journeys j ON s.journey_id = j.id WHERE j.user_id = ? AND s.status = 'completed'");
    $stmt->execute([$userId]);
    $completedSteps = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT SUM(clone_count) FROM journeys WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalClones = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM aha_votes av JOIN steps s ON av.step_id = s.id JOIN journeys j ON s.journey_id = j.id WHERE j.user_id = ?");
    $stmt->execute([$userId]);
    $totalAha = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM journeys WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalJourneys = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM daily_logs WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalLogs = (int)$stmt->fetchColumn();

    return [
        'current_streak'     => $user['current_streak'] ?? 0,
        'longest_streak'     => $user['longest_streak'] ?? 0,
        'completed_journeys' => $completedJourneys,
        'completed_steps'    => $completedSteps,
        'total_clones'       => $totalClones,
        'total_aha_received' => $totalAha,
        'total_journeys'     => $totalJourneys,
        'total_logs'         => $totalLogs,
    ];
}
