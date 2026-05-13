<?php
// php/get_leaderboard.php
require_once 'config.php';

// Khởi động session để kiểm tra nếu có user đang đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = getDB();

// Lấy ID người dùng hiện tại (nếu có), nếu không có thì để null/0
// Điều này giúp khách chưa đăng nhập vẫn không bị lỗi 404/500
try {
    $uid = null;
    if (function_exists('getCurrentUserId')) {
        $uid = getCurrentUserId();
    } else {
        $uid = $_SESSION['user_id'] ?? 0;
    }

    // SQL: Lấy danh sách Top 10 sĩ tử có điểm cao nhất ở các bài thi > 10 câu
    $sql = "SELECT u.id, u.fullname, MAX(q.score) as total_score 
            FROM users u
            JOIN quiz_scores q ON u.id = q.user_id
            WHERE u.role = 'student' AND q.total_q > 10
            GROUP BY u.id
            HAVING total_score > 0
            ORDER BY total_score DESC
            LIMIT 10";
    
    $stmt = $db->query($sql);
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về JSON cho cả Khách và Học viên
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'data' => $leaderboard,
        'my_id' => $uid // Nếu khách thì giá trị này sẽ là 0 hoặc null
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false, 
        'error' => "Lỗi truy vấn dữ liệu"
    ], JSON_UNESCAPED_UNICODE);
}