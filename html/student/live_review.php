<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/**
 * MODULE: live_review.php - PHÒNG HỌC TRỰC TUYẾN DÀNH CHO VIP
 */
session_start();
require_once(__DIR__ . "/../../php/config.php");

try {
    $db = getDB();
} catch (Exception $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT is_vip, fullname FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$is_vip = ($user && $user['is_vip'] == 1);

$joining_id = isset($_GET['join']) ? (int)$_GET['join'] : 0;
$current_live = null;

if ($joining_id > 0 && $is_vip) {
    $stmt_live = $db->prepare("SELECT * FROM live_sessions WHERE id = ? AND is_visible = 1");
    $stmt_live->execute([$joining_id]);
    $current_live = $stmt_live->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Live-Review · Sử Việt Toàn Thư</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* --- ĐỒNG BỘ MENU ACTIVE: NỀN VÀNG CHỮ ĐEN --- */
        .sidebar-menu .menu li.active {
            background: linear-gradient(90deg, #D4AF37, #c9a227) !important;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.3) !important;
            border-radius: 10px;
        }
        .sidebar-menu .menu li.active a {
            color: #1a1a1a !important; /* Chữ đen đậm */
            font-weight: 800 !important;
            opacity: 1 !important;
        }

        /* Menu item VIP mặc định bị mờ */
        li.vip-item a {
            opacity: 0.5;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        /* Khi đã là VIP (có class unlocked) */
        li.vip-item.unlocked a {
            opacity: 1 !important;
            pointer-events: auto !important;
            color: #D4AF37; 
        }

        /* CSS Trang Live Review */
        .live-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-top: 20px; }
        .live-card { background: #fff; border-radius: 16px; border: 1.5px solid #e8dfc8; overflow: hidden; transition: 0.3s; position: relative; }
        .live-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(128,0,0,0.1); border-color: #BF9B30; }
        .card-header { padding: 12px 15px; background: #fdfaf5; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0e8d8; }
        .st-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .st-upcoming { background: #e3f2fd; color: #007bff; }
        .st-live { background: #ffebeb; color: #ff0000; animation: blink 1.5s infinite; }
        .st-ended { background: #f5f5f5; color: #888; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .card-body { padding: 20px; }
        .card-title { font-family: 'Playfair Display', serif; font-size: 18px; color: #800000; margin-bottom: 8px; font-weight: 800; }
        .card-desc { font-size: 13px; color: #666; line-height: 1.5; margin-bottom: 15px; }
        .btn-join { display: block; width: 100%; padding: 12px; background: linear-gradient(135deg, #800000, #b30000); color: #E0C98C; text-align: center; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 14px; border: 1px solid #BF9B30; transition: 0.2s; }
        .lock-box { text-align: center; padding: 12px; background: #f9f9f9; border-radius: 10px; font-size: 12px; color: #800000; border: 1px dashed #BF9B30; }
        .video-wrapper { margin-top: 30px; background: #000; border-radius: 20px; border: 4px solid #BF9B30; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="container">
    <!-- SIDEBAR ICONS -->
    <div class="sidebar-icons">
        <div class="icon-btn" onclick="location.href='student_dashboard.html'"><span class="material-symbols-outlined">home</span></div>
        <div class="icon-btn" onclick="location.href='student_profile.html'"><span class="material-symbols-outlined">groups</span></div>
        <div class="icon-btn" onclick="location.href='student_settings.html'"><span class="material-symbols-outlined">settings</span></div>
        <div class="logout"><a href="../login.html" class="icon-btn"><span class="material-symbols-outlined">logout</span></a></div>
    </div>

    <!-- SIDEBAR MENU -->
    <div class="sidebar-menu">
        <div class="profile">
            <div class="avatar" id="avatarEl">A</div>
            <div class="name" id="nameEl"><?= htmlspecialchars($user['fullname'] ?? 'Sĩ tử') ?></div>
        </div>
        <ul class="menu">
            <li><a href="student_dashboard.html">Lộ trình của tôi</a></li>
            <li><a href="thuvienbaigiang.html">Thư viện bài giảng</a></li>
            <li><a href="khoahoc.html">Khóa học của bạn</a></li>
            <li><a href="luyenthi.html">Phòng luyện thi</a></li>
            <li><a href="bangxephang.html">Bảng xếp hạng</a></li>
            
            <!-- ĐÃ ĐIỀU CHỈNH: Tự động thêm class unlocked và active bằng PHP -->
            <li class="vip-item <?= $is_vip ? 'unlocked' : '' ?> active">
                <a href="live_review.php">Lịch Live-Review</a>
            </li>
            
            <li class="vip-item <?= $is_vip ? 'unlocked' : '' ?>">
                <a href="hoi_chuyen_gia.html">Hỏi đáp chuyên gia</a>
            </li>
            
            <li><a href="sotay.html">Sổ tay ghi chú</a></li>
        </ul>
    </div>

    <!-- CENTER CONTENT -->
    <!-- CENTER CONTENT -->
    <div class="center">
        <h2 class="page-title">LỊCH LIVE-REVIEW</h2>

        <?php if ($current_live): ?>
            <!-- KHU VỰC PHÒNG HỌC ĐANG MỞ -->
            <div id="live-view-area">
                <div class="section-header" style="margin-bottom:15px; display:flex; align-items:center; gap:10px;">
                    <div class="section-badge" style="background:#ff0000;"></div>
                    <h3 style="color:#800000; font-family:'Playfair Display'; text-transform:uppercase;">ĐANG HỌC: <?= htmlspecialchars($current_live['title']) ?></h3>
                </div>
                <div class="video-wrapper">
                    <script src="https://meet.jit.si/external_api.js"></script>
                    <div id="jitsi_container" style="height:600px; width:100%;"></div>
                    <script>
                        const options = {
                            roomName: "<?= $current_live['room_id'] ?>",
                            width: "100%",
                            height: 600,
                            parentNode: document.querySelector('#jitsi_container'),
                            userInfo: { displayName: "Sĩ tử: <?= htmlspecialchars($user['fullname']) ?>" }
                        };
                        const api = new JitsiMeetExternalAPI("meet.jit.si", options);
                    </script>
                </div>
            </div>
        <?php endif; ?>

        <div class="section-header">
            <div class="section-badge"></div>
            <h3>Danh sách phòng Live</h3>
        </div>

        <div class="live-grid">
            <?php
            $res = $db->query("SELECT * FROM live_sessions WHERE is_visible = 1 ORDER BY scheduled_at DESC");
            $sessions = $res->fetchAll();

            if (empty($sessions)):
                echo "<p style='text-align:center; color:#999; grid-column:1/-1; padding:40px;'>Hiện chưa có lịch Live nào được cập nhật.</p>";
            else:
                foreach ($sessions as $row):
                    $start_ts = strtotime($row['scheduled_at']);
$open_before = 300; // 5 phút = 300 giây
$end_ts = $start_ts + 7200; // Buổi học kéo dài 2 tiếng
$now = time();

if ($now < ($start_ts - $open_before)) {
    // Trường hợp: Còn hơn 5 phút nữa mới tới giờ
    $st_class = "st-upcoming"; 
    $st_text = "Sắp diễn ra";
} elseif ($now <= $end_ts) {
    // Trường hợp: Trong khoảng (Giờ bắt đầu - 5 phút) đến (Giờ bắt đầu + 2 tiếng)
    $st_class = "st-live"; 
    $st_text = "ĐANG DIỄN RA";
} else {
    // Trường hợp: Đã quá 2 tiếng kể từ lúc bắt đầu
    $st_class = "st-ended"; 
    $st_text = "Đã kết thúc";
}
?>
                <div class="live-card">
                    <div class="card-header">
                        <span class="st-badge <?= $st_class ?>"><?= $st_text ?></span>
                        <span style="font-size:12px; font-weight:700; color:#9a6a30;">
                            <i class="far fa-calendar-alt"></i> <?= date('H:i - d/m', $start_ts) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                        <p class="card-desc"><?= nl2br(htmlspecialchars($row['topic'])) ?></p>
                        
                        <?php 
                        // ĐIỀU CHỈNH LOGIC HIỆN NÚT: Khớp với thời gian mở sớm 5 phút
                        if ($now >= ($start_ts - $open_before) && $now <= $end_ts): 
                            // Kiểm tra quyền: VIP hoặc Giáo viên
                            if ($is_vip || (isset($_SESSION['role']) && $_SESSION['role'] == 'teacher')): ?>
                                <a href="?join=<?= $row['id'] ?>#live-view-area" class="btn-join">VÀO HỌC NGAY <i class="fas fa-sign-in-alt"></i></a>
                            <?php else: ?>
                                <div class="lock-box"><i class="fas fa-lock"></i> Nâng cấp <b>VIP</b> để vào phòng</div>
                            <?php endif; ?>
                        <?php elseif ($now < ($start_ts - $open_before)): ?>
                            <div style="text-align:center; color:#999; font-size:12px; font-style:italic;">Phòng sẽ mở trước giờ học 5 phút</div>
                        <?php else: ?>
                            <div style="text-align:center; color:#e74c3c; font-size:12px; font-weight:700;">Buổi học đã kết thúc</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<script src="../../js/load_profile.js"></script>
</body>
</html>