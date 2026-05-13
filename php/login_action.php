<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php';


// NHẬN DỮ LIỆU
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng nhập đầy đủ thông tin!'
    ]);
    exit();
}

// TÌM USER
$stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    //Kiểm tra xem tài khoản có bị khóa không
    if (isset($user['status']) && $user['status'] === 'locked') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin!'
        ]);
        exit(); // Chặn luôn không cho chạy tiếp xuống dưới
    }

    // KIỂM TRA PASSWORD
    if (password_verify($password, $user['password'])) {

        // LƯU SESSION
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 🔥 CHECK PROFILE
        $check = $conn->prepare("SELECT id FROM student_profile WHERE user_id = ?");
        $check->bind_param("i", $user['id']);
        $check->execute();
        $profileResult = $check->get_result();

        $hasProfile = $profileResult->num_rows > 0;

        // ✅ TRẢ JSON CHUẨN CHO JS
        echo json_encode([
            'status' => 'success',
            'role' => $user['role'],
            'hasProfile' => $hasProfile
        ]);

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sai mật khẩu!'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tài khoản không tồn tại!'
    ]);
}

$stmt->close();
$conn->close();
?>