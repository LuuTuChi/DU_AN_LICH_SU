<?php
session_start();

// ✅ KẾT NỐI DB (KHÔNG DÙNG db.php nữa)
require_once 'config.php';

// CHECK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// ================== UPLOAD AVATAR ==================
$avatar = "";
if (!empty($_FILES['avatar']['name'])) {
    $target_dir = "../uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $avatar = time() . "_" . basename($_FILES["avatar"]["name"]);
    $target_file = $target_dir . $avatar;

    move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);
}

// ================== LẤY DỮ LIỆU ==================
$fullname = $_POST['fullname'] ?? '';
$gender = $_POST['gender'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$school = $_POST['school'] ?? '';
$grade = $_POST['grade'] ?? '';
$city = $_POST['city'] ?? '';

$avg_history_11 = $_POST['avg_history_11'] ?? 0;
$last_test_score = $_POST['last_test_score'] ?? 0;
$self_level = $_POST['self_level'] ?? '';

$interest_level = 0; // chưa có form -> để mặc định
$difficulties = '';  // chưa có form

$study_sessions_per_week = $_POST['study_sessions_per_week'] ?? 0;
$study_time_per_session = $_POST['study_time_per_session'] ?? '';
$study_time_per_day_session = ''; // chưa dùng
$study_time_of_day = $_POST['study_time_of_day'] ?? '';

$study_methods = isset($_POST['study_methods']) ? implode(",", $_POST['study_methods']) : '';
$has_study_plan = $_POST['has_study_plan'] ?? '';

$target_score = $_POST['target_score'] ?? 0;
$target_time_frame = $_POST['target_time_frame'] ?? '';
$specific_goals = isset($_POST['specific_goals']) ? implode(",", $_POST['specific_goals']) : '';


// ================== SQL ==================
$sql = "INSERT INTO student_profile (
    user_id, avatar, fullname, gender, birthday, school, grade, city,
    avg_history_11, last_test_score, self_level, interest_level, difficulties,
    study_sessions_per_week, study_time_per_session, study_time_per_day_session,
    study_time_of_day, study_methods, has_study_plan,
    target_score, target_time_frame, specific_goals
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// ✅ TYPE CHUẨN (22 biến = 22 ký tự)
$stmt->bind_param(
    "issssssiddsisssssssdss",
    $user_id,
    $avatar,
    $fullname,
    $gender,
    $birthday,
    $school,
    $grade,
    $city,
    $avg_history_11,
    $last_test_score,
    $self_level,
    $interest_level,
    $difficulties,
    $study_sessions_per_week,
    $study_time_per_session,
    $study_time_per_day_session,
    $study_time_of_day,
    $study_methods,
    $has_study_plan,
    $target_score,
    $target_time_frame,
    $specific_goals
);

// ================== EXECUTE ==================
if ($stmt->execute()) {
    header("Location: ../html/student/student_dashboard.html");
    exit();
} else {
    echo "Lỗi SQL: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>