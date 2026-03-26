<?php
/**
 * PHP-based migration for badges
 */
require_once __DIR__ . '/../includes/db.php';

try {
    $db = getDB();
    
    // 1. Update existing badge names and icons
    $db->exec("UPDATE badges SET name = 'Bronze Runner', icon = 'bi-person-walking' WHERE id = 1");
    $db->exec("UPDATE badges SET name = 'Busy Bee', icon = 'bi-bug' WHERE id = 2");
    $db->exec("UPDATE badges SET name = 'First Hill', icon = 'bi-tree' WHERE id = 3");
    $db->exec("UPDATE badges SET name = 'Popular Path', icon = 'bi-people' WHERE id = 4");
    $db->exec("UPDATE badges SET name = 'Friendly Dolphin', icon = 'bi-water' WHERE id = 5");
    $db->exec("UPDATE badges SET name = '7-Day Spark', icon = 'bi-lightning-charge' WHERE id = 6");

    // 2. Add new Criteria Type to ENUM
    // Note: Some DBs might not support ALTER TABLE for ENUM in a simple way, 
    // but the DB already has most of these. Let's try to ensure 'aha_votes_received' exists.
    try {
        $db->exec("ALTER TABLE badges MODIFY COLUMN criteria_type ENUM('streak','journeys_completed','steps_completed','clones','aha_votes','committed','diligent','community_helper','consistent','aha_votes_received') NOT NULL");
    } catch (Exception $e) {
        echo "Note: ENUM update skipped or failed (might already be updated): " . $e->getMessage() . "\n";
    }

    // 3. Insert Level 2 and Level 3 Badges
    $badges = [
        ['Silver Sprinter', 'Completed 5 learning journeys within their total estimated duration.', 'bi-speedometer', 'committed', 5],
        ['Gold Falcon', 'Completed 10 learning journeys within their total estimated duration.', 'bi-lightning-charge', 'committed', 10],
        ['30-Day Flame', 'Maintained a consistent 30-day learning streak.', 'bi-fire', 'consistent', 30],
        ['100-Day Sun', 'Maintained a consistent 100-day learning streak.', 'bi-brightness-high', 'consistent', 100],
        ['Growing Tree', 'Your learning paths have been cloned 25 or more times.', 'bi-tree-fill', 'clones', 25],
        ['Mighty Oak', 'Your breakthrough moments have inspired 50 or more learners.', 'bi-stars', 'aha_votes_received', 50],
        ['Persistent Beaver', 'Authored full daily logs for every step in 5 completed journeys.', 'bi-journal-check', 'diligent', 5],
        ['Master Architect', 'Authored full daily logs for every step in 10 completed journeys.', 'bi-award', 'diligent', 10],
        ['Iron Mountain', 'Successfully completed 5 full learning journeys!', 'bi-signpost-split', 'journeys_completed', 5],
        ['Gold Summit', 'Successfully completed 10 full learning journeys!', 'bi-trophy-fill', 'journeys_completed', 10],
        ['Wise Owl', 'Provided 50+ helpful comments or Aha! inspirations to fellow learners.', 'bi-lightbulb', 'community_helper', 50],
        ['Lion Heart', 'Provided 100+ helpful comments or Aha! inspirations to fellow learners.', 'bi-heart-fill', 'community_helper', 100]
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO badges (name, description, icon, criteria_type, criteria_value) VALUES (?, ?, ?, ?, ?)");
    foreach ($badges as $b) {
        $stmt->execute($b);
        echo "Processed badge: {$b[0]}\n";
    }

    echo "Migration successful!\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
