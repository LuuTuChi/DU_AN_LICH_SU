<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'Vui lòng đăng nhập!']);
    exit();
}

$uid = $_SESSION['user_id'];
$old_pass = $_POST['old_pass'];
$new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Lấy mật khẩu cũ để đối chiếu
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (password_verify($old_pass, $res['password'])) {
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->bind_param("si", $new_pass, $uid);
    if ($upd->execute()) {
        echo json_encode(['message' => 'Đổi mật khẩu thành công!']);
    }
} else {
    echo json_encode(['message' => 'Mật khẩu cũ không chính xác!']);
}
?>