<?php
require 'c:/xampp/htdocs/soma-track/includes/db.php';
$db = getDB();

$journeyId = 4;
$userId = 2; // Gideon

echo "DEBUG: Starting step loop simulation for Journey $journeyId\n";

$stepsSql = "SELECT s.*, 
    (SELECT COUNT(*) FROM step_comments WHERE step_id = s.id AND is_deleted = 0) AS comment_count,
    (SELECT COUNT(*) FROM aha_votes WHERE step_id = s.id) AS aha_count 
    FROM steps s WHERE s.journey_id = ? ORDER BY s.step_number ASC";

$stepsStmt = $db->prepare($stepsSql);
$stepsStmt->execute([$journeyId]);
$steps = $stepsStmt->fetchAll();

$stepBadge = ['pending' => 'st-badge-warning', 'in_progress' => 'st-badge-info', 'completed' => 'st-badge-success'];

foreach ($steps as $step) {
    echo "Processing Step #" . $step['step_number'] . " [ID " . $step['id'] . "]: " . $step['title'] . "\n";
    
    // Check if status exists in badge array
    if (!isset($stepBadge[$step['status']])) {
        echo "FAILED: Status '" . $step['status'] . "' is missing from badge array!\n";
    }
    
    // Check for potential string issues
    try {
        $temp = htmlspecialchars($step['title']);
        if ($step['description']) $temp2 = htmlspecialchars($step['description']);
    } catch (Exception $e) {
        echo "FAILED: String encoding error: " . $e->getMessage() . "\n";
    }
    
    echo "  Step #" . $step['step_number'] . " OK\n";
}

echo "DEBUG: Simulation complete.\n";
