<?php
/**
 * One-time script to fix journey progress counts
 */
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

echo "Starting journey progress sync...\n";

$stmt = $db->query("SELECT id, title FROM journeys");
$journeys = $stmt->fetchAll();

foreach ($journeys as $j) {
    echo "Syncing Journey ID: {$j['id']} ({$j['title']})... ";
    if (syncJourneyProgress($j['id'])) {
        echo "Done.\n";
    } else {
        echo "Failed.\n";
    }
}

echo "\nSync complete!\n";
