<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT u.username, u.email, u.created_at, 
           sp.fullname, sp.avatar, sp.gender, sp.birthday, 
           sp.school, sp.grade, sp.city, sp.target_score,
           sp.avg_history_11, sp.last_test_score, sp.self_level,
           sp.study_sessions_per_week, sp.study_time_per_session, 
           sp.study_time_of_day, sp.has_study_plan
    FROM users u
    LEFT JOIN student_profile sp ON sp.user_id = u.id
    WHERE u.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy user']);
    exit();
}

// --- XỬ LÝ ĐƯỜNG DẪN AVATAR ĐỂ KHÔNG LỖI TRÊN HOST ---
$avatar_url = null;
if (!empty($row['avatar'])) {
    // Tự động nhận diện http hay https của host
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    // Tạo link tuyệt đối: https://chitraining.42web.io/uploads/ten_file.jpg
    $avatar_url = $protocol . $_SERVER['HTTP_HOST'] . '/uploads/' . $row['avatar'];
} else {
    // Nếu không có ảnh, dùng ảnh mặc định (Chi có thể đổi link này)
    $avatar_url = '../../assets/default-avatar.png'; 
}

// Gộp kết quả
$response = array_merge([
    'status' => 'success', 
    'avatar_url' => $avatar_url
], $row);

echo json_encode($response);

$stmt->close();
$conn->close();
?>