<?php
/**
 * MODULE: ad_pricing.php - QUẢN TRỊ THƯƠNG MẠI & ĐIỀU PHỐI THANH TOÁN
 */
if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

// 1. XỬ LÝ DỮ LIỆU (Giả lập lưu vào Session để Chi demo không lỗi Database)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_vip'])) {
        $_SESSION['vip_price'] = $_POST['vip_price'];
        $_SESSION['stk'] = $_POST['stk'];
        $_SESSION['owner'] = $_POST['owner'];
        echo "<script>alert('✓ Đã cập nhật cấu hình thương mại!');</script>";
    }
    if (isset($_POST['add_new_plan'])) {
        echo "<script>alert('✓ Đã khởi tạo yêu cầu tạo gói: " . $_POST['plan_name'] . "');</script>";
    }
}

// Dữ liệu mặc định lấy theo code Client của Chi
$vip_price = $_SESSION['vip_price'] ?? 999000;
$stk = $_SESSION['stk'] ?? "PSP2611310000000003";
$owner = $_SESSION['owner'] ?? "LUUTUCHI";
?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

<div class="ad-container" style="padding: 10px; font-family: 'Nunito', sans-serif;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span class="material-symbols-outlined" style="font-size: 36px; color: #800000;">payments</span>
            <h3 style="color: #800000; font-family: 'Playfair Display', serif; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                Cấu hình thương mại
            </h3>
        </div>
        
        <button onclick="document.getElementById('modalAddPlan').style.display='flex'" 
                style="background: linear-gradient(135deg, #BF9B30 0%, #d4af37 100%); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-family: 'Nunito'; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(191,155,48,0.2);">
            <span class="material-symbols-outlined">add_card</span> Tạo gói học mới
        </button>
    </div>

    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 25px;">
        
        <div class="content-card" style="background: #fff; border-radius: 20px; border: 1px solid #eee; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
            <div style="font-weight: 800; color: #800000; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px;">
                <span class="material-symbols-outlined">workspace_premium</span> CHI TIẾT GÓI SĨ TỬ VIP
            </div>

            <div style="background: #fdfaf5; border: 2px solid #BF9B30; border-radius: 20px; padding: 25px; position: relative;">
                <div style="position: absolute; top: -12px; right: 25px; background: #BF9B30; color: #fff; padding: 4px 15px; border-radius: 20px; font-size: 11px; font-weight: 800;">ĐANG BÁN CHẠY</div>
                
                <form method="POST">
                    <input type="hidden" name="update_vip" value="1">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0; color: #333; font-weight: 800; font-size: 20px;">Lịch Sử Việt Nam (1945–1975)</h4>
                            <div style="margin-top: 15px;">
                                <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; margin-bottom: 5px;">HỌC PHÍ NIÊM YẾT (VNĐ)</label>
                                <input type="number" name="vip_price" value="<?= $vip_price ?>" style="width: 200px; padding: 12px; border: 1.5px solid #eee; border-radius: 10px; font-family: 'Nunito'; font-weight: 800; font-size: 20px; color: #800000;">
                            </div>
                        </div>
                    </div>

                    <div style="background: #fff; border-radius: 12px; padding: 15px; border: 1px solid #eee; margin-bottom: 20px;">
                        <div style="font-size: 13px; color: #555; display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <span class="material-symbols-outlined" style="color:#BF9B30; font-size:18px;">check_circle</span> Truy cập 3 bài học chuyên sâu & 60 Flashcards
                        </div>
                        <div style="font-size: 13px; color: #555; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color:#BF9B30; font-size:18px;">check_circle</span> Live-Review & Hỏi đáp chuyên gia
                        </div>
                    </div>

                    <button type="submit" style="background: #800000; color: #fff; border: none; padding: 12px 25px; border-radius: 10px; font-family: 'Nunito'; font-weight: 800; cursor: pointer;">LƯU THAY ĐỔI GÓI</button>
                </form>
            </div>
        </div>

        <div class="content-card" style="background: #fff; border-radius: 20px; border: 1px solid #eee; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
            <div style="font-weight: 800; color: #800000; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f5f5f5; padding-bottom: 15px;">
                <span class="material-symbols-outlined">qr_code_2</span> THÀNH PHẦN THANH TOÁN
            </div>

            <div style="text-align: center; margin-bottom: 25px;">
                <div style="display: inline-block; padding: 15px; background: #fafafa; border: 1px dashed #BF9B30; border-radius: 15px;">
                    <img src="https://img.vietqr.io/image/momo-<?= $stk ?>-compact2.png?amount=<?= $vip_price ?>&addInfo=MUAKHOA%20UID&accountName=<?= $owner ?>" 
                         style="width: 130px; border-radius: 10px; border: 1px solid #eee;">
                    <p style="font-size: 10px; color: #888; margin-top: 8px;">QR Preview (VietQR API)</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 18px;">
                <div>
                    <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; margin-bottom: 5px;">SỐ TÀI KHOẢN / MOMO</label>
                    <input type="text" name="stk" value="<?= $stk ?>" style="width: 100%; padding: 12px; border: 1.5px solid #f1f1f1; border-radius: 10px; font-family: 'Nunito'; font-weight: 700; color: #800000;">
                </div>
                <div>
                    <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; margin-bottom: 5px;">CHỦ TÀI KHOẢN (KHÔNG DẤU)</label>
                    <input type="text" name="owner" value="<?= $owner ?>" style="width: 100%; padding: 12px; border: 1.5px solid #f1f1f1; border-radius: 10px; font-family: 'Nunito'; font-weight: 700;">
                </div>
                <div style="background: #fdfaf5; padding: 12px; border-radius: 10px; border-left: 4px solid #BF9B30;">
                    <div style="font-size: 11px; color: #856404; font-weight: 700;">NỘI DUNG MẶC ĐỊNH:</div>
                    <div style="font-size: 14px; color: #800000; font-weight: 800; margin-top: 5px;">MUAKHOA [UID]</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalAddPlan" style="display:none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 40px; width: 450px; border-radius: 25px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); position: relative;">
        <button onclick="document.getElementById('modalAddPlan').style.display='none'" style="position: absolute; top: 20px; right: 20px; border: none; background: none; font-size: 20px; cursor: pointer; color: #ccc;">✕</button>
        
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 60px; height: 60px; background: #fdfaf5; border: 2px solid #BF9B30; color: #BF9B30; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <span class="material-symbols-outlined" style="font-size: 30px;">add_card</span>
            </div>
            <h4 style="color: #800000; font-family: 'Playfair Display', serif; margin: 0; font-size: 24px;">Khởi tạo gói học mới</h4>
        </div>

        <form method="POST">
            <input type="hidden" name="add_new_plan" value="1">
            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; margin-bottom: 8px;">TÊN KHÓA HỌC / GÓI DỊCH VỤ</label>
                <input type="text" name="plan_name" placeholder="VD: Lịch sử Thế giới hiện đại" required style="width: 100%; padding: 12px; border: 1.5px solid #eee; border-radius: 12px; font-family: 'Nunito'; outline: none;">
            </div>
            <div style="margin-bottom: 30px;">
                <label style="display:block; font-size: 11px; font-weight: 800; color: #BF9B30; margin-bottom: 8px;">HỌC PHÍ DỰ KIẾN (VNĐ)</label>
                <input type="number" name="plan_price" placeholder="999000" required style="width: 100%; padding: 12px; border: 1.5px solid #eee; border-radius: 12px; font-family: 'Nunito'; outline: none;">
            </div>
            <button type="submit" style="width: 100%; background: #800000; color: #fff; border: none; padding: 15px; border-radius: 12px; font-family: 'Nunito'; font-weight: 800; cursor: pointer; transition: 0.3s;">XÁC NHẬN TẠO GÓI</button>
        </form>
    </div>
</div>

<style>
    input:focus { border-color: #BF9B30 !important; background: #fffcf5; }
    button:hover { transform: translateY(-2px); filter: brightness(1.1); }
</style>