<?php
/**
 * php/get_ai_reminder.php
 * Tự động phân tích điểm yếu và đưa ra lời khuyên từ Admin
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => null]);
    exit();
}

$uid = (int)$_SESSION['user_id'];
$db = getDB();

// 1. Lấy bài có điểm thấp nhất (Dưới 7 điểm)
$stmt = $db->prepare("
    SELECT lesson_id, (score/total_q*10) as s10 
    FROM quiz_scores 
    WHERE user_id = ? 
    ORDER BY s10 ASC 
    LIMIT 1
");
$stmt->execute([$uid]);
$weakest = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Lấy tiến độ học tập trung bình
$stmt = $db->prepare("SELECT AVG(pct_done) as avg_pct FROM lesson_progress WHERE user_id = ?");
$stmt->execute([$uid]);
$prog = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Logic tạo 2 Đề xuất (Giống hệt ảnh Admin của Chi)
$tips = [];

// Đề xuất 1: Về phong độ học tập
if ($prog && $prog['avg_pct'] >= 80) {
    $tips[] = "✨ Phong độ tốt! Hãy thử đề thi thử để kiểm tra mức độ thực chiến.";
} else {
    $tips[] = "📚 Hãy dành thêm thời gian tuần này để hoàn thành các mục còn dở dang nhé.";
}

// Đề xuất 2: Về bài yếu nhất
if ($weakest && $weakest['s10'] < 7) {
    $tips[] = "⚠️ Dành 2 buổi tuần này ôn lại Bài " . $weakest['lesson_id'] . ". Làm Quiz đến khi đạt 7/10 mới chuyển bài.";
} else {
    $tips[] = "🃏 Bạn đã ôn Flashcard rất tốt - thói quen ghi nhớ chủ động này giúp bạn giữ vững kiến thức.";
}

// Nối 2 đề xuất lại thành 1 tin nhắn
$final_message = implode("\n\n", $tips);

// Trả về cho JS (Chi không cần sửa bảng DB, AI sẽ tự tính mỗi lần học sinh mở web)
echo json_encode(['message' => $final_message], JSON_UNESCAPED_UNICODE);