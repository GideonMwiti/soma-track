<?php
require_once __DIR__ . '/../includes/db.php';
$db = getDB();

$stmt = $db->query("SELECT * FROM badges");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
