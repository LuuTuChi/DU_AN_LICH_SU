<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'student';

    // Kiểm tra xem email hoặc tên đăng nhập đã tồn tại chưa
    $check_sql = "SELECT * FROM users WHERE email='$email' OR username='$username'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        echo "Lỗi: Tên đăng nhập hoặc Email này đã được người khác khác sử dụng!";
    } else {
        $sql = "INSERT INTO users (fullname, username, email, password, role) 
                VALUES ('$fullname', '$username', '$email', '$password', '$role')";
        
        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Lỗi hệ thống: " . $conn->error;
        }
    }
}
$conn->close();
?>