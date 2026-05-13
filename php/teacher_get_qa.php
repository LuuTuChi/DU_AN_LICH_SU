<?php
// php/teacher_get_qa.php
require_once 'config.php';
$db = getDB();

try {
    // Lấy câu hỏi kèm tên học sinh (Join với bảng users)
    $stmt = $db->query("SELECT q.*, u.fullname 
                        FROM teacher_qa q 
                        JOIN users u ON q.user_id = u.id 
                        ORDER BY q.created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}