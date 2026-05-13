<?php
// progress.php - HỆ THỐNG TỰ ĐỘNG CẬP NHẬT TIẾN ĐỘ & ĐIỂM SỐ
require_once 'config.php';

// 1. LẤY ID NGƯỜI DÙNG
$uid = getCurrentUserId();
if (!$uid) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Vui lòng đăng nhập']);
    exit;
}

$db = getDB();
$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('monday this week'));

try {
    // --- 1.1 LẤY THÔNG TIN ROLE VÀ VIP ---
    $stmtUser = $db->prepare("SELECT is_vip, role FROM users WHERE id = ?");
    $stmtUser->execute([$uid]);
    $userBaseInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $isVip = (int)($userBaseInfo['is_vip'] ?? 0);

    $stmtPending = $db->prepare("SELECT COUNT(*) FROM vip_requests WHERE user_id = ? AND status = 'pending'");
    $stmtPending->execute([$uid]);
    $isPending = (int)$stmtPending->fetchColumn() > 0;

    // 2. TIẾN ĐỘ BÀI HỌC
    $stmtLessons = $db->prepare("SELECT lesson_id, pct_done, completed FROM lesson_progress WHERE user_id = ?");
    $stmtLessons->execute([$uid]);
    $lessonRows = $stmtLessons->fetchAll(PDO::FETCH_ASSOC);

    $completedCount = 0;
    $lessonMap = new stdClass(); 
    foreach ($lessonRows as $r) {
        $lid = $r['lesson_id'];
        $lessonMap->$lid = [
            'lesson_id' => (int)$lid,
            'pct_done' => (int)$r['pct_done'],
            'completed' => (int)$r['completed']
        ];
        if ($r['completed']) $completedCount++;
    }
    $overallPct = round(($completedCount / 3) * 100);

    // 3. ĐIỂM QUIZ
    $stmtQuiz = $db->prepare("SELECT lesson_id, score, total_q FROM quiz_scores WHERE user_id = ? AND total_q = 10");
    $stmtQuiz->execute([$uid]);
    $quizRows = $stmtQuiz->fetchAll(PDO::FETCH_ASSOC);

    $quizMap = new stdClass();
    $scoreSum = 0;
    $scoreCount = 0;
    foreach ($quizRows as $r) {
        $lid = $r['lesson_id'];
        $quizMap->$lid = [
            'score' => (int)$r['score'],
            'total_q' => (int)$r['total_q']
        ];
        $scoreSum += (int)$r['score'];
        $scoreCount++;
    }
    $avgScore = $scoreCount > 0 ? round($scoreSum / $scoreCount, 1) : null;

    // 4. THỜI GIAN HỌC TRONG TUẦN
    $stmtTime = $db->prepare("SELECT SUM(duration_s) FROM study_sessions WHERE user_id = ? AND DATE(started_at) >= ?");
    $stmtTime->execute([$uid, $weekStart]);
    $totalS = (int)$stmtTime->fetchColumn();
    $studyLabel = ($totalS >= 3600) ? round($totalS/3600, 1)."h" : round($totalS/60)."m";

    // 5. LOGIC STREAK
    $stmtDates = $db->prepare("SELECT DISTINCT DATE(started_at) as s_date FROM study_sessions WHERE user_id = ? AND duration_s > 0 ORDER BY s_date DESC");
    $stmtDates->execute([$uid]);
    $studyDates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);

    $streak = 0;
    $checkDate = $today;
    foreach ($studyDates as $date) {
        if ($date === $checkDate) {
            $streak++;
            $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
        } else if ($date > $checkDate) {
            continue;
        } else {
            break;
        }
    }

    // 6. BIỂU ĐỒ 4 TUẦN
    $chart = [];
    for ($i = 3; $i >= 0; $i--) {
        $wS = date('Y-m-d', strtotime("-$i week monday this week"));
        $wE = date('Y-m-d', strtotime("-$i week sunday this week"));
        $stmtChart = $db->prepare("SELECT SUM(duration_s) FROM study_sessions WHERE user_id = ? AND DATE(started_at) BETWEEN ? AND ?");
        $stmtChart->execute([$uid, $wS, $wE]);
        $s = (int)$stmtChart->fetchColumn();
        $chart[] = [
            'label' => ($i === 0) ? "Tuần này" : "Tuần -$i",
            'hours' => round($s / 3600, 1),
            'current' => ($i === 0)
        ];
    }

    // 6.5 FLASHCARD DATA
    $stmtFlash = $db->prepare("SELECT lesson_id, SUM(cards_done) as total FROM flashcard_log WHERE user_id = ? GROUP BY lesson_id");
    $stmtFlash->execute([$uid]);
    $flashRows = $stmtFlash->fetchAll(PDO::FETCH_ASSOC);

    $flashTotal = 0;
    $flashMap = ["6" => 0, "7" => 0, "8" => 0];
    foreach ($flashRows as $r) {
        $lid = $r['lesson_id'];
        $count = (int)$r['total'];
        $flashMap[$lid] = $count;
        $flashTotal += $count;
    }

    // ── 6.8 MỚI: LẤY LỊCH LIVE-REVIEW TỪ GIÁO VIÊN ──
    $nowDateTime = date('Y-m-d H:i:s');
    // Lấy buổi Live chưa kết thúc quá 2 tiếng so với giờ bắt đầu
    $stmtLive = $db->prepare("
        SELECT id, title, topic, scheduled_at, room_id, is_visible 
        FROM live_sessions 
        WHERE is_visible = 1 
        AND DATE_ADD(scheduled_at, INTERVAL 2 HOUR) > ? 
        ORDER BY scheduled_at ASC LIMIT 1
    ");
    $stmtLive->execute([$nowDateTime]);
    $nextLive = $stmtLive->fetch(PDO::FETCH_ASSOC);

    // Xác định trạng thái string cho Frontend dễ xử lý
    if ($nextLive) {
        $startTime = strtotime($nextLive['scheduled_at']);
        $endTime = $startTime + 7200; // +2 tiếng
        $nowTs = time();

        if ($nowTs >= ($startTime - 300) && $nowTs <= $endTime) {
            $nextLive['status'] = 'live'; // Đang diễn ra (cho phép vào trước 5p)
        } elseif ($nowTs < $startTime) {
            $nextLive['status'] = 'upcoming'; // Sắp diễn ra
        } else {
            $nextLive['status'] = 'ended'; // Đã kết thúc
        }
    }

    // 7. TRẢ KẾT QUẢ JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'overview' => [
            'completed_lessons' => (int)($completedCount ?? 0),
            'overall_pct'       => (int)($overallPct ?? 0),
            'avg_score'         => $avgScore !== null ? (float)$avgScore : null,
            'flash_this_week'   => (int)($flashTotal ?? 0),
            'study_label'       => $studyLabel ?? '0m',
            'is_vip'            => $isVip,
            'is_pending'        => $isPending
        ],
        'streak'    => ['days' => (int)($streak ?? 0), 'dots' => []],
        'lessons'   => (object)($lessonMap ?? []),
        'quizzes'   => (object)($quizMap ?? []), 
        'flashcard_by_lesson' => (object)($flashMap ?? ["6" => 0, "7" => 0, "8" => 0]),
        'chart'     => $chart ?? [],
        'next_live' => $nextLive // <--- TRẢ THÊM DỮ LIỆU LIVE Ở ĐÂY
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
}