<?php
// php/teacher_stats.php
require_once 'config.php';
$db = getDB();

try {
    // 1. Tổng số học sinh (không tính admin/gv)
    $totalHS = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();

    // 2. Học sinh đã mua khóa học (VIP)
    $vipHS = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student' AND is_vip = 1")->fetchColumn();

    // 3. Điểm trung bình Quiz toàn lớp
    $avgScore = $db->query("SELECT AVG(score) FROM quiz_scores")->fetchColumn();

    // 4. Tỷ lệ hoàn thành bài học trung bình
    $avgProgress = $db->query("SELECT AVG(pct_done) FROM lesson_progress")->fetchColumn();

    // 5. Top 3 câu hỏi thường bị sai nhất (Phân tích điểm yếu)
    // Giả sử Chi có lưu log câu trả lời sai, nếu chưa thì ta để placeholder
    $weakPoints = [
        ["topic" => "Cách mạng Tháng Tám", "error_rate" => "65%"],
        ["topic" => "Chiến dịch Điện Biên Phủ", "error_rate" => "42%"],
        ["topic" => "Hiệp định Paris 1973", "error_rate" => "38%"]
    ];

    echo json_encode([
        'stats' => [
            'total' => (int)$totalHS,
            'vip' => (int)$vipHS,
            'avg_score' => round($avgScore, 1),
            'avg_progress' => round($avgProgress) . '%'
        ],
        'weak_points' => $weakPoints
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}