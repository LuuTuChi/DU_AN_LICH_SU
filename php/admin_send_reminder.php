<?php
include 'config.php'; // Kết nối DB của Chi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $message = $_POST['message'];

    // Chèn lời nhắc vào bảng Chi vừa tạo
    $sql = "INSERT INTO ai_reminders (student_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $student_id, $message);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Đã gửi lời khuyên!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi gửi dữ liệu."]);
    }
}
?>