<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$stmt = $db->prepare("SELECT id, title, total_steps, completed_steps FROM journeys WHERE id = 4");
$stmt->execute();
$j = $stmt->fetch();

echo "Journey ID: {$j['id']}\n";
echo "Title: {$j['title']}\n";
echo "Total Steps: {$j['total_steps']}\n";
echo "Completed Steps: {$j['completed_steps']}\n";
echo "Progress: " . ($j['total_steps'] > 0 ? ($j['completed_steps'] / $j['total_steps'] * 100) : 0) . "%\n";
