<?php
// php/get_lives.php
require_once 'config.php';
$db = getDB();

$stmt = $db->query("SELECT * FROM live_sessions ORDER BY created_at DESC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));