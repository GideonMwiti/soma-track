<?php
require_once __DIR__ . '/../../includes/db.php';
$db = getDB();

try {
    // 1. Update the ENUM types in the badges table
    $db->exec("ALTER TABLE badges MODIFY COLUMN criteria_type ENUM('streak','journeys_completed','steps_completed','clones','aha_votes', 'committed', 'diligent', 'community_helper', 'consistent') NOT NULL");

    // 2. Clear existing badges and user associations to start fresh as requested
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE user_badges");
    $db->exec("TRUNCATE TABLE badges");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 3. Seed new high-value badges
    $badges = [
        [
            'name' => 'Commitment Master',
            'description' => 'Completed a learning journey within the total estimated duration.',
            'icon' => 'bi-clock-history',
            'criteria_type' => 'committed',
            'criteria_value' => 1
        ],
        [
            'name' => 'Diligent Scholar',
            'description' => 'Authored daily logs for every single step in a completed journey.',
            'icon' => 'bi-journal-check',
            'criteria_type' => 'diligent',
            'criteria_value' => 1
        ],
        [
            'name' => 'Pathfinder',
            'description' => 'Successfully completed your first full learning journey!',
            'icon' => 'bi-flag-fill',
            'criteria_type' => 'journeys_completed',
            'criteria_value' => 1
        ],
        [
            'name' => 'Industry Icon',
            'description' => 'Your learning path has been cloned 5 or more times by the community.',
            'icon' => 'bi-stars',
            'criteria_type' => 'clones',
            'criteria_value' => 5
        ],
        [
            'name' => 'Community Pillar',
            'description' => 'Provided 10+ helpful comments or Aha! inspirations to fellow learners.',
            'icon' => 'bi-people-fill',
            'criteria_type' => 'community_helper',
            'criteria_value' => 10
        ],
        [
            'name' => 'Steady Progress',
            'description' => 'Maintained a consistent 7-day learning streak.',
            'icon' => 'bi-fire',
            'criteria_type' => 'consistent',
            'criteria_value' => 7
        ]
    ];

    $stmt = $db->prepare("INSERT INTO badges (name, description, icon, criteria_type, criteria_value) VALUES (?, ?, ?, ?, ?)");
    foreach ($badges as $b) {
        $stmt->execute([$b['name'], $b['description'], $b['icon'], $b['criteria_type'], $b['criteria_value']]);
    }

    echo "Badge migration and seeding completed successfully!\n";

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
}
