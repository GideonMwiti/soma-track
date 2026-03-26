<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$stmt = $db->query("SELECT * FROM badges ORDER BY criteria_type, criteria_value");
$badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Badges in DB:\n";
foreach ($badges as $b) {
    echo "{$b['id']} | {$b['name']} | {$b['criteria_type']} | {$b['criteria_value']}\n";
}
echo "\nChecking if badges can be awarded...\n";
// Let's check a user (ID 1)
require_once __DIR__ . '/../includes/helpers.php';
checkBadges(1);
echo "Badge check run for user 1.\n";
