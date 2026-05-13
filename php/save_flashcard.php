<?php
require_once 'config.php';
session_start();

// Lấy ID người dùng từ session (đảm bảo nick Giang đã đăng nhập)
$uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $uid > 0) {
    $data = json_decode(file_get_contents('php://input'), true);
    $lesson_id = (int)$data['lesson_id'];
    $cards_done = (int)$data['cards_done'];

    // Dùng biến $conn đã tạo ở file config.php của Chi
    $sql = "INSERT INTO flashcard_log (user_id, lesson_id, cards_done, log_date) 
            VALUES (?, ?, ?, CURDATE()) 
            ON DUPLICATE KEY UPDATE cards_done = cards_done + VALUES(cards_done)";
    
    $stmt = $conn->prepare($sql); // Dùng $conn của mysqli hoặc PDO tùy file config
    $stmt->bind_param("iii", $uid, $lesson_id, $cards_done); // Nếu config dùng mysqli
    $stmt->execute();

    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Chưa đăng nhập']);
}