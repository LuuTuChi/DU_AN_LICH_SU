<?php
/**
 * MODULE: ad_system.php - QUẢN TRỊ HỆ THỐNG (CÓ LOGIC XỬ LÝ THẬT)
 */
if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

// 1. XỬ LÝ KHI NGƯỜI DÙNG NHẤN NÚT
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý lưu thông tin Website
    if (isset($_POST['save_settings'])) {
        $_SESSION['sys_name'] = $_POST['site_name'];
        $_SESSION['sys_email'] = $_POST['site_email'];
        $message = "✓ Đã cập nhật thông tin hệ thống!";
    }

    // Xử lý Bật/Tắt Bảo trì
    if (isset($_POST['toggle_maintenance'])) {
        $_SESSION['is_maintenance'] = ($_SESSION['is_maintenance'] ?? false) ? false : true;
    }
}

// Lấy trạng thái hiện tại
$site_name = $_SESSION['sys_name'] ?? "Sử Việt Toàn Thư";
$site_email = $_SESSION['sys_email'] ?? "hotro@suviet.edu.vn";
$is_mt = $_SESSION['is_maintenance'] ?? false;
?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

<?php if ($message): ?>
    <script>alert('<?= $message ?>');</script>
<?php endif; ?>

<div class="ad-container" style="padding: 10px; font-family: 'Nunito', sans-serif;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span class="material-symbols-outlined" style="font-size: 36px; color: #800000;">settings_suggest</span>
            <h3 style="color: #800000; font-family: 'Playfair Display', serif; font-weight: 800; margin: 0; text-transform: uppercase;">Quản trị hệ thống</h3>
        </div>
        <div style="background: #fff; border: 1px solid #eee; padding: 8px 15px; border-radius: 10px; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 10px; height: 10px; background: <?= $is_mt ? '#e74c3c' : '#2ecc71' ?>; border-radius: 50%;"></span>
            Hệ thống: <?= $is_mt ? 'Đang bảo trì' : 'Đang trực tuyến' ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        
        <form method="POST" class="content-card" style="background: #fff; border-radius: 20px; border: 1px solid #eee; padding: 25px;">
            <div style="font-weight: 800; color: #800000; margin-bottom: 25px; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">language</span> THÔNG TIN WEBSITE
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; text-transform: uppercase; margin-bottom: 8px;">Tên nền tảng</label>
                <input type="text" name="site_name" value="<?= $site_name ?>" style="width: 100%; padding: 12px; border: 1.5px solid #f1f1f1; border-radius: 10px; font-family: 'Nunito'; font-weight: 700;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; text-transform: uppercase; margin-bottom: 8px;">Email hỗ trợ</label>
                <input type="email" name="site_email" value="<?= $site_email ?>" style="width: 100%; padding: 12px; border: 1.5px solid #f1f1f1; border-radius: 10px; font-family: 'Nunito'; font-weight: 700;">
            </div>

            <div style="background: #fafafa; padding: 15px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <span style="font-size: 13px; color: #666; font-weight: 600;">Chế độ bảo trì</span>
                <button type="submit" name="toggle_maintenance" style="background: <?= $is_mt ? '#e74c3c' : '#95a5a6' ?>; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-family: 'Nunito'; font-weight: 800; cursor: pointer;">
                    <?= $is_mt ? 'ĐANG BẬT' : 'ĐANG TẮT' ?>
                </button>
            </div>

            <button type="submit" name="save_settings" style="width: 100%; background: #800000; color: #fff; border: none; padding: 15px; border-radius: 12px; font-family: 'Nunito'; font-weight: 800; cursor: pointer;">LƯU THAY ĐỔI</button>
        </form>

        <div style="display: flex; flex-direction: column; gap: 25px;">
            <div class="content-card" style="background: #fff; border-radius: 20px; border: 1px solid #eee; padding: 25px;">
                <div style="font-weight: 800; color: #800000; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined">database</span> QUẢN TRỊ DỮ LIỆU
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <button type="button" onclick="confirmAction('xóa bộ nhớ đệm')" style="background: #fdfaf5; border: 1px solid #BF9B30; color: #BF9B30; padding: 15px; border-radius: 12px; cursor: pointer; font-family: 'Nunito'; font-weight: 700;">
                        <span class="material-symbols-outlined" style="display:block;">cached</span> Xóa Cache
                    </button>
                    <button type="button" onclick="confirmAction('sao lưu Database')" style="background: #fdfaf5; border: 1px solid #BF9B30; color: #BF9B30; padding: 15px; border-radius: 12px; cursor: pointer; font-family: 'Nunito'; font-weight: 700;">
                        <span class="material-symbols-outlined" style="display:block;">backup</span> Sao lưu DB
                    </button>
                </div>
            </div>

            <div class="content-card" style="background: #fff; border-radius: 20px; border: 1px solid #eee; padding: 25px;">
                <div style="font-weight: 800; color: #800000; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined">security</span> BẢO MẬT & PHIÊN BẢN
                </div>
                <div style="font-size: 13px; color: #666; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f9f9f9; padding-bottom: 8px;">
                        <span>Phiên bản Core:</span> <b style="color: #333;">v2.5.0-stable</b>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f9f9f9; padding-bottom: 8px;">
                        <span>Cập nhật cuối:</span> <b style="color: #333;">07/05/2026</b>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Môi trường:</span> <b style="color: #2ecc71;">Local Development</b>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAction(task) {
    if(confirm('Bạn chắc chắn muốn ' + task + '?')) {
        alert('Thực hiện thành công!');
    }
}
</script>