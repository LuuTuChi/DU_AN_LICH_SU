<?php
header('Content-Type: application/json');
session_start();

// 1. KẾT NỐI DATABASE (Chi kiểm tra lại tên db và pass nhé)
$host = '127.0.0.1';
$db_name = 'suviettoanthu';
$username = 'root';
$password = ''; // Để trống nếu dùng XAMPP mặc định

try {
    $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => 'Kết nối thất bại']);
    exit;
}

// 2. LẤY DỮ LIỆU GỬI LÊN
$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['action'])) {
    echo json_encode(['ok' => false, 'error' => 'Dữ liệu không hợp lệ']);
    exit;
}

// Giả định User ID là 1 nếu chưa đăng nhập, Chi nên dùng $_SESSION['id']
$uid = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 1;
$lid = (int)filter_var($body['lesson_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');

// Hàm xuất JSON nhanh
function jsonOut($data) {
    echo json_encode($data);
    exit;
}

// 3. XỬ LÝ CÁC HÀNH ĐỘNG
switch ($body['action']) {
    
    // Khi bắt đầu mở trang bài học
    case 'session_start':
        $stmt = $db->prepare('
            INSERT INTO study_sessions (user_id, lesson_id, started_at, study_date)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$uid, $lid, $now, $today]);
        $sid = $db->lastInsertId();
        jsonOut(['ok' => true, 'session_id' => $sid]);
        break;

    // Khi cuộn trang (Cập nhật tiến độ % vào bảng lesson_progress)
    case 'update_progress':
        $pct = (int)($body['progress'] ?? 0);
        $completed = ($pct >= 90) ? 1 : 0;
        
        $stmt = $db->prepare('
            INSERT INTO lesson_progress (user_id, lesson_id, pct_done, completed, last_seen)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                pct_done = GREATEST(pct_done, VALUES(pct_done)),
                completed = IF(pct_done >= 90, 1, completed),
                last_seen = VALUES(last_seen)
        ');
        $stmt->execute([$uid, $lid, $pct, $completed, $now]);
        jsonOut(['ok' => true]);
        break;

    // Khi đóng trang hoặc chuyển tab
    case 'session_end':
        $sid      = (int)($body['session_id'] ?? 0);
        $duration = (int)($body['duration_s'] ?? 0);
        if ($sid && $duration > 0) {
            $stmt = $db->prepare('
                UPDATE study_sessions
                SET ended_at = ?, duration_s = ?
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$now, $duration, $sid, $uid]);
            jsonOut(['ok' => true]);
        }
        break;

    default:
        jsonOut(['ok' => false, 'error' => 'Action không tồn tại']);
}