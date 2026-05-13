<?php
// php/config.php - File cấu hình thông minh (Local & Host)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. TỰ ĐỘNG NHẬN DIỆN MÔI TRƯỜNG
$isLocal = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_NAME'] === 'localhost');

if ($isLocal) {
    // --- THÔNG SỐ XAMPP (Máy của Chi) ---
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'suviettoanthu');
} else {
    // --- THÔNG SỐ HOST (InfinityFree) ---
    define('DB_HOST', 'sql211.infinityfree.com');
    define('DB_USER', 'if0_41458930');
    define('DB_PASS', '1MGPUoy3Q7');
    define('DB_NAME', 'if0_41458930_suviet');
}

// 2. KẾT NỐI KIỂU MYSQLI (Dùng hàm getConn để an toàn hơn)
function getConn() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Lỗi kết nối MySQLi: ' . $conn->connect_error
            ]));
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

// Khởi tạo biến $conn toàn cục để các file cũ vẫn chạy được
$conn = getConn();

// 3. KẾT NỐI KIỂU PDO (Dùng cho progress.php, track.php, quiz_score.php)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die(json_encode(['status' => 'error', 'message' => 'Lỗi kết nối PDO: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// 4. HÀM TIỆN ÍCH CHO DASHBOARD
function getCurrentUserId(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function jsonOut(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}