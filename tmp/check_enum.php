<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$stmt = $db->query("SHOW COLUMNS FROM badges LIKE 'criteria_type'");
$column = $stmt->fetch();
echo "criteria_type column definition: " . $column['Type'] . "\n";
