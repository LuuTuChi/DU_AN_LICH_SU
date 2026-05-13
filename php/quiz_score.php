<?php
/**
 * php/quiz_score.php
 * POST: lưu điểm quiz
 * GET:  lấy điểm quiz
 */
session_start();
header('Content-Type: application/json');

// ── Kết nối DB — giống login_action.php ──────────────────
$conn = new mysqli("localhost", "root", "", "suviettoanthu");
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB error: ' . $conn->connect_error]);
    exit();
}

// ── Kiểm tra đăng nhập ────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not logged in']);
    exit();
}
$uid = (int)$_SESSION['user_id'];

// ══════════════════ POST — LƯU ĐIỂM ══════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);

    // Fallback: thử $_POST nếu không có JSON body
    if (!$body) {
        $body = $_POST;
    }

    $lesson_id = (int)($body['lesson_id'] ?? 0);
    $score     = (int)($body['score']     ?? 0);
    $total_q   = (int)($body['total_q']   ?? 10);

    if (!$lesson_id || $score < 0 || $score > $total_q) {
        echo json_encode(['error' => 'invalid params', 'got' => $body]);
        exit();
    }

    // Lưu điểm — ON DUPLICATE KEY UPDATE (unique: user_id + lesson_id)
    $sql = "INSERT INTO quiz_scores (user_id, lesson_id, score, total_q, taken_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                score    = IF(total_q = VALUES(total_q), VALUES(score), score),
                taken_at = IF(total_q = VALUES(total_q), NOW(), taken_at)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("iiii", $uid, $lesson_id, $score, $total_q);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        echo json_encode(['error' => 'execute failed: ' . $conn->error]);
        exit();
    }

    // Nếu điểm >= 5 → đánh dấu bài hoàn thành
    if ($score >= 5) {
        $sql2 = "INSERT INTO lesson_progress (user_id, lesson_id, completed, pct_done, last_seen)
                 VALUES (?, ?, 1, 100, NOW())
                 ON DUPLICATE KEY UPDATE
                     completed = 1,
                     pct_done  = 100,
                     last_seen = NOW()";
        $stmt2 = $conn->prepare($sql2);
        if ($stmt2) {
            $stmt2->bind_param("ii", $uid, $lesson_id);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    echo json_encode([
        'ok'        => true,
        'lesson_id' => $lesson_id,
        'score'     => $score,
        'total_q'   => $total_q
    ]);
    $conn->close();
    exit();
}

// ══════════════════ GET — LẤY ĐIỂM ═══════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

    if ($lesson_id) {
        $stmt = $conn->prepare(
            "SELECT lesson_id, score, total_q, taken_at
             FROM quiz_scores WHERE user_id = ? AND lesson_id = ?"
        );
        $stmt->bind_param("ii", $uid, $lesson_id);
    } else {
        $stmt = $conn->prepare(
            "SELECT lesson_id, score, total_q, taken_at
             FROM quiz_scores WHERE user_id = ?"
        );
        $stmt->bind_param("i", $uid);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows   = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    $conn->close();

    echo json_encode($rows);
    exit();
}

echo json_encode(['error' => 'method not allowed']);
$conn->close();
?>