<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$searchTerm = '%fontend%';
$stmt = $db->prepare("SELECT * FROM journeys WHERE title LIKE ?");
$stmt->execute([$searchTerm]);
$journeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($journeys)) {
    $searchTerm = '%frontend%';
    $stmt = $db->prepare("SELECT * FROM journeys WHERE title LIKE ?");
    $stmt->execute([$searchTerm]);
    $journeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo "Journeys found:\n";
foreach ($journeys as $j) {
    echo "ID: {$j['id']}, Title: {$j['title']}, Total Steps: {$j['total_steps']}, Completed Steps: {$j['completed_steps']}, Status: {$j['status']}\n";
    
    // Check steps for this journey
    $stmt2 = $db->prepare("SELECT id, title, status FROM steps WHERE journey_id = ?");
    $stmt2->execute([$j['id']]);
    $steps = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Steps:\n";
    $actualCompleted = 0;
    foreach ($steps as $s) {
        echo "    - ID: {$s['id']}, Title: {$s['title']}, Status: {$s['status']}\n";
        if ($s['status'] === 'completed') {
            $actualCompleted++;
        }
    }
    echo "  Actual Completed Steps (status='completed'): $actualCompleted\n";
    echo "  Total Steps Count: " . count($steps) . "\n";

    // Check if it's a clone
    $stmt3 = $db->prepare("SELECT * FROM cloned_journeys WHERE cloned_journey_id = ?");
    $stmt3->execute([$j['id']]);
    $clone = $stmt3->fetch(PDO::FETCH_ASSOC);
    if ($clone) {
        echo "  This is a CLONE of journey ID: {$clone['original_journey_id']}\n";
    } else {
        echo "  This is an ORIGINAL journey\n";
    }
}
