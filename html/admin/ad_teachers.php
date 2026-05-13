<?php
/**
 * MODULE: ad_teachers.php - QUẢN LÝ ĐỘI NGŨ MENTOR (PREMIUM GOLD STYLE)
 */
if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

// 1. XỬ LÝ THÊM MENTOR MỚI (PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    $name = trim($_POST['fullname']);
    $user = trim($_POST['username']);
    $mail = trim($_POST['email']);
    $pass = password_hash('123456', PASSWORD_DEFAULT); // Pass mặc định

    try {
        // Kiểm tra username/email tồn tại
        $check = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$user, $mail]);
        if ($check->rowCount() > 0) {
            echo "<script>alert('Lỗi: Tên đăng nhập hoặc Email đã được sử dụng!');</script>";
        } else {
            $sql = "INSERT INTO users (fullname, username, email, password, role, status) VALUES (?, ?, ?, ?, 'teacher', 'active')";
            $db->prepare($sql)->execute([$name, $user, $mail, $pass]);
            echo "<script>alert('Khởi tạo tài khoản Mentor thành công!'); window.location.href='admin_dashboard.php?page=teachers';</script>";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "<script>alert('Lỗi hệ thống, vui lòng thử lại sau.');</script>";
    }
}

// 2. LẤY DANH SÁCH TEACHER (Đồng bộ Style ad_main.php)
try {
    $teachers = $db->query("SELECT id, fullname, username, email, created_at FROM users WHERE role = 'teacher' ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $teachers = [];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<div class="ad-container" style="padding: 10px; font-family: 'Nunito', sans-serif;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span class="material-symbols-outlined" style="font-size: 36px; color: #800000;">psychology</span>
            <h3 style="color: #800000; font-family: 'Playfair Display', serif; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                Đội ngũ Mentor
            </h3>
        </div>
        
        <button onclick="openModalAdd()" 
                style="background: linear-gradient(135deg, #BF9B30 0%, #d4af37 100%); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(191,155,48,0.3); transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px;">
            <span class="material-symbols-outlined" style="font-size: 20px;">person_add</span> Thêm Mentor Mới
        </button>
    </div>

    <div class="content-card" style="background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow: hidden; border: 1px solid #eee;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fdfaf5; border-bottom: 2px solid #BF9B30;">
                    <th style="padding: 18px 20px; text-align: left; color: #800000; font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">Giảng viên / Chuyên gia</th>
                    <th style="padding: 18px 20px; text-align: left; color: #800000; font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">Thông tin liên hệ</th>
                    <th style="padding: 18px 20px; text-align: center; color: #800000; font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">Trạng thái</th>
                    <th style="padding: 18px 20px; text-align: center; color: #800000; font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 1px;">Quản lý</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($teachers)): ?>
                    <tr><td colspan="4" style="padding: 60px; text-align: center; color: #999; font-style: italic;"><span class="material-symbols-outlined" style="font-size:40px; display:block; margin-bottom:10px;">person_off</span>Chưa có Mentor nào trong danh sách.</td></tr>
                <?php else: foreach($teachers as $t): ?>
                    <tr class="teacher-row" style="border-bottom: 1px solid #f9f9f9; transition: 0.2s;">
                        <td style="padding: 18px 20px;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 45px; height: 45px; background: #800000; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; font-family: 'Playfair Display', serif;">
                                    <?= strtoupper(substr($t['fullname'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight: 800; color: #333; font-size: 15px;"><?= htmlspecialchars($t['fullname']) ?></div>
                                    <div style="font-size: 12px; color: #BF9B30; font-weight: 700;">@<?= htmlspecialchars($t['username']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 18px 20px; font-size: 14px; color: #555;">
                            <div style="display: flex; align-items: center; gap: 6px;"><span class="material-symbols-outlined" style="font-size:16px; color: #BF9B30;">mail</span> <?= htmlspecialchars($t['email']) ?></div>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <span style="background: #e6f4ea; color: #1e7e34; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span> ĐANG HOẠT ĐỘNG
                            </span>
                        </td>
                        <td style="padding: 18px 20px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button class="btn-action-gv" title="Chỉnh sửa"><span class="material-symbols-outlined">edit</span></button>
                                <button class="btn-action-gv delete" title="Khóa tài khoản"><span class="material-symbols-outlined">person_remove</span></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalAdd" class="sv-modal">
    <div class="sv-modal-content">
        
        <div class="sv-modal-header">
            <div class="header-icon-circle">
                <span class="material-symbols-outlined">add_moderator</span>
            </div>
            <h4>Khởi tạo chuyên gia</h4>
            <p>Cấp tài khoản Mentor điều phối hệ thống Sử Việt</p>
            <button onclick="closeModalAdd()" class="close-modal-btn">&times;</button>
        </div>

        <form method="POST" class="sv-modal-form">
            <input type="hidden" name="add_teacher" value="1">
            
            <div class="form-group">
                <label>Họ và Tên Mentor</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">badge</span>
                    <input type="text" name="fullname" placeholder="VD: ThS. Nguyễn Văn Sử" required>
                </div>
            </div>

            <div class="form-group">
                <label>Tên đăng nhập (Username)</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">alternate_email</span>
                    <input type="text" name="username" placeholder="nguyenvansu_mentor" required>
                </div>
            </div>

            <div class="form-group">
                <label>Địa chỉ Email công việc</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">mail</span>
                    <input type="email" name="email" placeholder="vasu.mentor@suviet.edu.vn" required>
                </div>
            </div>

            <div class="info-note">
                <span class="material-symbols-outlined">info</span>
                <p>Mật khẩu mặc định là <b>123456</b></p>
            </div>

            <div class="sv-modal-footer">
                <button type="submit" class="btn-submit-gv">XÁC NHẬN CẤP TÀI KHOẢN</button>
                <button type="button" onclick="closeModalAdd()" class="btn-cancel-gv">HỦY BỎ</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalAdd() { document.getElementById('modalAdd').classList.add('open'); }
    function closeModalAdd() { document.getElementById('modalAdd').classList.remove('open'); }
    // Đóng khi click ngoài
    window.onclick = function(event) {
        let modal = document.getElementById('modalAdd');
        if (event.target == modal) { modal.classList.remove('open'); }
    }
</script>

<style>
    /* 1. Global & Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap');
    
    .teacher-row:hover { background-color: #fdfdfa !important; }
    
    .btn-action-gv { background: none; border: 1px solid #eee; color: #BF9B30; padding: 6px; border-radius: 8px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .btn-action-gv span { font-size: 18px; }
    .btn-action-gv:hover { background: #fdfaf5; border-color: #BF9B30; transform: translateY(-1px); }
    .btn-action-gv.delete { color: #e74c3c; }
    .btn-action-gv.delete:hover { background: #fff5f5; border-color: #ffcccc; }

    /* 2. Modal Style Nâng cấp */
    .sv-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); opacity: 0; transition: 0.3s; align-items: center; justify-content: center; }
    .sv-modal.open { display: flex; opacity: 1; }
    
    .sv-modal-content { background: #fff; width: 420px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden; transform: scale(0.9); transition: 0.3s; }
    .sv-modal.open .sv-modal-content { transform: scale(1); }

    /* Modal Header Minimal Gold */
    .sv-modal-header { padding: 30px 30px 10px; text-align: center; position: relative; }
    .header-icon-circle { width: 60px; height: 60px; background: #fdfaf5; border: 2px solid #BF9B30; color: #BF9B30; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
    .header-icon-circle span { font-size: 30px; }
    .sv-modal-header h4 { font-family: 'Playfair Display', serif; color: #800000; margin: 0; font-size: 22px; font-weight: 800; }
    .sv-modal-header p { font-family: 'Nunito', sans-serif; color: #777; font-size: 13px; margin: 5px 0 0; }
    .close-modal-btn { position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 28px; color: #999; cursor: pointer; }
    .close-modal-btn:hover { color: #333; }

    /* Modal Form Nunito */
    .sv-modal-form { padding: 20px 30px 30px; font-family: 'Nunito', sans-serif; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 11px; font-weight: 800; color: #800000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
    
    .input-with-icon { position: relative; }
    .input-with-icon span { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #BF9B30; font-size: 18px; }
    .input-with-icon input { width: 100%; padding: 11px 11px 11px 38px; border: 1px solid #ddd; border-radius: 10px; font-family: 'Nunito', sans-serif; font-size: 14px; outline: none; transition: 0.2s; }
    .input-with-icon input:focus { border-color: #BF9B30; box-shadow: 0 0 0 3px rgba(191,155,48,0.1); }

    .info-note { background: #fffcf5; border-left: 3px solid #BF9B30; padding: 10px 12px; border-radius: 6px; display: flex; align-items: start; gap: 8px; margin-bottom: 25px; }
    .info-note span { color: #b95000; font-size: 16px; margin-top: 2px; }
    .info-note p { margin: 0; font-size: 12px; color: #856404; line-height: 1.4; }

    /* Modal Footer Nunito Buttons */
    .sv-modal-footer { display: flex; flex-direction: column; gap: 10px; }
    .btn-submit-gv { background: linear-gradient(135deg, #800000 0%, #a52a2a 100%); color: #fff; border: none; padding: 14px; border-radius: 10px; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 14px; cursor: pointer; transition: 0.2s; letter-spacing: 0.5px; }
    .btn-submit-gv:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(128,0,0,0.2); }
    .btn-cancel-gv { background: #f5f5f5; color: #666; border: none; padding: 12px; border-radius: 10px; font-family: 'Nunito', sans-serif; font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s; }
    .btn-cancel-gv:hover { background: #eee; color: #333; }
</style>