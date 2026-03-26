<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$stmt = $db->query("SELECT * FROM badges");
$badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current Badges:\n";
foreach ($badges as $b) {
    echo "ID: {$b['id']}, Name: {$b['name']}, Criteria: {$b['criteria_type']}, Value: {$b['criteria_value']}\n";
}
