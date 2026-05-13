<?php
// php/get_questions.php
require_once 'config.php';
$db = getDB();
$userId = getCurrentUserId();

$stmt = $db->prepare("SELECT * FROM teacher_qa WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));