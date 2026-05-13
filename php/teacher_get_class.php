<?php
// php/teacher_get_class.php
require_once 'config.php';
$db = getDB();

try {
    $sql = "SELECT 
                u.id, u.fullname, u.is_vip,
                (SELECT COUNT(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.completed = 1) as completed_lessons,
                (SELECT AVG(score) FROM quiz_scores qs WHERE qs.user_id = u.id) as avg_score,
                (SELECT MAX(started_at) FROM study_sessions ss WHERE ss.user_id = u.id) as last_seen
            FROM users u
            WHERE u.role = 'student'
            ORDER BY last_seen DESC";
            
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}