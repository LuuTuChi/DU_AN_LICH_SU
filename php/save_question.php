<?php
// php/save_question.php
require_once 'config.php';
$db = getDB();

$data = json_decode(file_get_contents('php://input'), true);
$userId = getCurrentUserId(); 

if ($userId && !empty($data['content'])) {
    try {
        // Fix theo ảnh của Chi: user_id và question
        $sql = "INSERT INTO teacher_qa (user_id, question, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $data['content']]);
        echo json_encode(['ok' => true]);
    } catch (PDOException $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}