<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once(__DIR__ . "/../../php/config.php");
$conn = getConn();

// Kiểm tra session và quyền
$role = $_SESSION['role'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? 0;

/* ================= XỬ LÝ LƯU DỮ LIỆU ================= */
if(isset($_POST['create_live']) && $role == 'teacher'){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $topic = mysqli_real_escape_string($conn, $_POST['topic']);
    $time  = $_POST['scheduled_at'];
    $is_visible = 1;

    // CHỈ TẠO VÀ LƯU TÊN PHÒNG (ROOM ID)
    $room_id = "SuViet_HaoKhi_" . time() . "_" . uniqid(); 

    $stmt = $conn->prepare("
        INSERT INTO live_sessions(title, topic, scheduled_at, room_id, is_visible, created_by)
        VALUES(?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssii", $title, $topic, $time, $room_id, $is_visible, $user_id);
    
    if($stmt->execute()){
        echo "<script>window.location.href='teacher_dashboard.php?mod=live&success=1';</script>";
        exit();
    }
}
?>

<!-- UI TIÊU ĐỀ -->
<h2 class="t-page-title">
    <span class="material-symbols-outlined">video_camera_front</span>
    Điều phối Live-Review VIP
</h2>

<style>
    :root {
        --maroon: #800000; --gold: #BF9B30; --gold-pale: #fdfbf5;
        --border: #e0e0e0; --radius-md: 12px; --shadow-sm: 0 2px 8px rgba(0,0,0,0.05); --white: #ffffff;
    }
    .t-form-card { background: var(--white); padding: 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border); margin-bottom: 32px; }
    .t-form-title { font-size: 16px; font-weight: 800; color: var(--maroon); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; text-transform: uppercase; border-bottom: 2px solid var(--gold-pale); padding-bottom: 12px; }
    .t-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
    .t-form-group { display: flex; flex-direction: column; gap: 6px; }
    .t-form-group label { font-size: 12px; font-weight: 800; color: #666; text-transform: uppercase; }
    .t-input { padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; transition: 0.3s; }
    .t-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-pale); }
    .t-btn-submit { background: var(--maroon); color: var(--white); padding: 14px; border: none; border-radius: 8px; font-family: 'Nunito', sans-serif; font-weight: 800; cursor: pointer; width: 100%; margin-top: 10px; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
    .t-btn-submit:hover { opacity: 0.9; transform: translateY(-2px); }
    .t-live-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    .t-live-card { background: var(--white); border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; display: flex; flex-direction: column; }
    .t-card-head { padding: 12px 16px; background: var(--gold-pale); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .t-card-body { padding: 20px; flex-grow: 1; }
    .t-badge { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; }
    .t-badge.live { background: #fee2e2; color: #d32f2f; border: 1px solid #fecaca; animation: blink 2s infinite; }
    .t-badge.upcoming { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .t-badge.ended { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
    .t-btn-join { display: block; text-align: center; padding: 10px; background: var(--gold); color: var(--white); text-decoration: none; border-radius: 6px; font-weight: 800; font-size: 13px; transition: 0.3s; }
    @keyframes blink { 50% { opacity: 0.6; } }
</style>

<div class="t-live-container">
    <?php if($role == 'teacher'): ?>
    <div class="t-form-card">
        <form method="POST">
            <div class="t-form-title">
                <span class="material-symbols-outlined">add_circle</span>
                Khởi tạo phiên thảo luận Sử Việt
            </div>
            <div class="t-form-row">
                <div class="t-form-group">
                    <label>Tiêu đề buổi học</label>
                    <input type="text" name="title" class="t-input" required placeholder="Ví dụ: Tổng ôn Chiến dịch Điện Biên Phủ">
                </div>
                <div class="t-form-group">
                    <label>Thời gian bắt đầu</label>
                    <input type="datetime-local" name="scheduled_at" class="t-input" required>
                </div>
            </div>
            <div class="t-form-group" style="margin-bottom:15px;">
                <label>Nội dung thảo luận trọng tâm</label>
                <textarea name="topic" class="t-input" rows="2" placeholder="Ghi chú các ý chính..."></textarea>
            </div>
            <button name="create_live" class="t-btn-submit">XÁC NHẬN KHỞI TẠO PHIÊN LIVE</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="section-header" style="margin-bottom: 20px; border-left: 5px solid var(--gold); padding-left: 15px;">
        <h3 style="color: var(--maroon); margin:0;">LỊCH TRÌNH LIVE-REVIEW VIP</h3>
    </div>

    <div class="t-live-grid">
        <?php
        $res = $conn->query("SELECT * FROM live_sessions WHERE is_visible = 1 ORDER BY scheduled_at DESC");
        while($row = $res->fetch_assoc()):
            $start_ts = strtotime($row['scheduled_at']);
            $open_before = 300; // 5 phút
            $end_ts = $start_ts + 7200;
            $now = time();

            if($now < ($start_ts - $open_before)) { $st = "upcoming"; $txt = "Sắp diễn ra"; }
            elseif($now <= $end_ts) { $st = "live"; $txt = "ĐANG LIVE"; }
            else { $st = "ended"; $txt = "Đã kết thúc"; }
        ?>
            <div class="t-live-card">
                <div class="t-card-head">
                    <span class="t-badge <?= $st ?>"><?= $txt ?></span>
                    <span style="font-size:12px; font-weight:700; color:#666;"><?= date('H:i, d/m', $start_ts) ?></span>
                </div>
                <div class="t-card-body">
                    <h4 style="margin:0 0 10px 0; color:var(--maroon);"><?= htmlspecialchars($row['title']) ?></h4>
                    <p style="font-size:13px; color:#888; margin-bottom:20px;"><?= nl2br(htmlspecialchars($row['topic'])) ?></p>
                    <?php if($now >= ($start_ts - $open_before) && $now <= $end_ts): ?>
                        <a class="t-btn-join" href="?mod=live&join=<?= $row['id'] ?>#room">VÀO PHÒNG HỌC NGAY</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="room" style="margin-top:40px;">
        <?php
        if(isset($_GET['join']) && ($role == 'teacher')){
            $id = (int)$_GET['join'];
            $live = $conn->query("SELECT * FROM live_sessions WHERE id=$id")->fetch_assoc();
            if($live && !empty($live['room_id'])){
        ?>
                <div style='display:flex; align-items:center; gap:10px; margin-bottom:15px;'>
                    <span class='material-symbols-outlined' style='color:var(--maroon)'>play_circle</span>
                    <h3 style='color:var(--maroon); margin:0;'>Đang điều phối: <?= htmlspecialchars($live['title']) ?></h3>
                </div>
                <script src="https://meet.jit.si/external_api.js"></script>
                <div id="jitsi_div" style="height:600px; border:4px solid var(--gold); border-radius:12px; overflow:hidden;"></div>
                <script>
                    const api = new JitsiMeetExternalAPI("meet.jit.si", {
                        roomName: "<?= $live['room_id'] ?>",
                        width: "100%", height: 600,
                        parentNode: document.querySelector('#jitsi_div'),
                        userInfo: { displayName: "Giáo viên: <?= htmlspecialchars($_SESSION['fullname'] ?? 'Sử Việt') ?>" }
                    });
                </script>
        <?php
            }
        }
        ?>
    </div>
</div>