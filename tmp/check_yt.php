<?php
require 'c:/xampp/htdocs/soma-track/includes/db.php';
$db = getDB();
$stmt = $db->query("SELECT id, youtube_url FROM daily_logs WHERE youtube_url IS NOT NULL");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | URL: [" . $row['youtube_url'] . "]\n";
}
