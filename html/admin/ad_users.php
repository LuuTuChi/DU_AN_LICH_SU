<?php
/**
 * MODULE: ad_users.php - QUẢN LÝ NGƯỜI DÙNG & DUYỆT VIP
 */
if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

// 1. XỬ LÝ DỮ LIỆU (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = (int)($_POST['id'] ?? 0);

    try {
        if ($action === 'approve_vip') {
            $db->beginTransaction();
            $req = $db->query("SELECT user_id, amount, transaction_code FROM vip_requests WHERE id = $id")->fetch();
            
            if ($req) {
                $db->prepare("UPDATE vip_requests SET status = 'approved' WHERE id = ?")->execute([$id]);
                $db->prepare("UPDATE users SET is_vip = 1, vip_since = NOW() WHERE id = ?")->execute([$req['user_id']]);
                
                $stmt_report = $db->prepare("INSERT INTO revenue_report (user_id, request_id, amount, transaction_code, report_date) VALUES (?, ?, ?, ?, CURRENT_DATE())");
                $stmt_report->execute([$req['user_id'], $id, $req['amount'], $req['transaction_code']]);

                $db->commit();
                echo "<script>alert('Duyệt thành công!'); window.location.href='admin_dashboard.php?page=users';</script>";
                exit;
            }
        } 
        elseif ($action === 'reject_vip') {
            $db->prepare("UPDATE vip_requests SET status = 'rejected' WHERE id = ?")->execute([$id]);
            echo "<script>window.location.href='admin_dashboard.php?page=users';</script>";
            exit;
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        echo "<script>alert(" . json_encode('Lỗi: ' . $e->getMessage()) . ");</script>";
    }
}

// --- 2. TRUY VẤN DỮ LIỆU ---
$pending_list = $db->query("SELECT vr.*, u.fullname, u.username FROM vip_requests vr JOIN users u ON vr.user_id = u.id WHERE vr.status = 'pending' ORDER BY vr.created_at ASC")->fetchAll();

$keyword = $_GET['search'] ?? '';
$sql_users = "SELECT * FROM users WHERE role != 'admin'";

if ($keyword !== '') {
    // SỬA TẠI ĐÂY: Đổi :q thành các định danh khác nhau :q1, :q2, :q3 để PDO không bị nhầm lẫn
    $sql_users .= " AND (fullname LIKE :q1 OR username LIKE :q2 OR email LIKE :q3)";
}

$sql_users .= " ORDER BY FIELD(role, 'teacher', 'student'), is_vip DESC";
$stmt_u = $db->prepare($sql_users);

if ($keyword !== '') {
    // SỬA TẠI ĐÂY: Gán giá trị cho cả 3 định danh
    $searchTerm = "%$keyword%";
    $stmt_u->bindValue(':q1', $searchTerm);
    $stmt_u->bindValue(':q2', $searchTerm);
    $stmt_u->bindValue(':q3', $searchTerm);
}

$stmt_u->execute();
$all_users = $stmt_u->fetchAll();
?>

<style>
    .ad-user-wrap { padding: 10px; font-family: 'Nunito', sans-serif !important; }
    .ad-box { background: #fff; border-radius: 15px; border: 1px solid #e8d5a3; box-shadow: 0 2px 12px rgba(128,0,0,.09); overflow: hidden; margin-bottom: 30px; }
    .ad-box-header { padding: 20px 25px; background: #fdfaf5; border-bottom: 2px solid #f0e6cc; display: flex; justify-content: space-between; align-items: center; }
    .u-table { width: 100%; border-collapse: collapse; }
    .u-table th { padding: 15px 25px; text-align: left; font-size: 11px; text-transform: uppercase; color: #9a6a30; font-weight: 900; background: #fcf8ee; }
    .u-table td { padding: 15px 25px; border-bottom: 1px solid #f9f9f9; font-size: 13.5px; vertical-align: middle; }

    /* Fix nút bấm đồng bộ phông chữ */
    .btn-approve, .btn-reject, .btn-lock { 
        font-family: 'Nunito', sans-serif !important; 
        font-size: 13px; font-weight: 700; padding: 10px 20px; 
        border-radius: 12px; cursor: pointer; border: none; transition: 0.3s;
    }
    .btn-approve { background: #467f54; color: #fff; }
    .btn-approve:hover { background: #366141; transform: translateY(-2px); }
    .btn-reject { background: #fff; border: 1px solid #ccc; color: #666; margin-right: 8px; }
    .btn-lock { background: transparent; border: 1px solid #c0392b; color: #c0392b; font-size: 11px; padding: 6px 12px; }

    .badge-vip { background: linear-gradient(135deg, #BF9B30, #f1c40f); color: #1a0000; padding: 3px 10px; border-radius: 5px; font-size: 10px; font-weight: 900; }
    .badge-teacher { background: #800000; color: #fff; padding: 3px 10px; border-radius: 5px; font-size: 10px; font-weight: 800; }
</style>

<div class="ad-user-wrap">
    <?php if ($pending_list): ?>
    <div class="ad-box" style="border-color: #BF9B30; margin-bottom: 30px; background:#fff; border-radius:15px; overflow:hidden; border:1px solid #e8d5a3;">
        <div class="ad-box-header" style="padding:15px 20px; background:#fdfaf5; border-bottom:1px solid #eee;">
            <h3 style="margin:0; font-family:'Playfair Display'; color:#856404;">⚠️ Yêu cầu VIP cần đối soát</h3>
        </div>
        <table class="u-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#fcf8ee; text-align:left; font-size:12px; color:#9a6a30;">
                    <th style="padding:15px;">Sĩ tử</th>
                    <th style="padding:15px;">Số tiền</th>
                    <th style="padding:15px;">Minh chứng</th>
                    <th style="padding:15px; text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_list as $p): ?>
                <tr style="border-bottom:1px solid #f9f9f9;">
                    <td style="padding:15px;"><b><?= htmlspecialchars($p['fullname']) ?></b></td>
                    <td style="padding:15px; color:#1e8449; font-weight:900;"><?= number_format($p['amount']) ?>đ</td>
                    <td style="padding:15px;">
                        <?php if(!empty($p['proof_image'])): ?>
                            <!-- SỬA ĐƯỜNG DẪN ẢNH TẠI ĐÂY -->
                           <a href="../proofs/<?= basename($p['proof_image']) ?>" target="_blank" class="proof-link">Xem ảnh bill</a>
                        <?php else: ?>
                            <span style="font-size:12px; color:#999;">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <!-- Tìm đoạn hiển thị nút trong ad_users.php và thay bằng đoạn này -->
<td style="padding:15px; text-align:right;">
    <!-- Duyệt ngay -->
    <form method="POST" action="admin_dashboard.php?page=users" style="display:inline;">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <input type="hidden" name="action" value="approve_vip">
        <button type="submit" class="btn-approve" onclick="return confirm('Duyệt ngay tài khoản này?')">Duyệt ngay</button>
    </form>

    <!-- Hủy bỏ -->
    <form method="POST" action="admin_dashboard.php?page=users" style="display:inline;">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <input type="hidden" name="action" value="reject_vip">
        <button type="submit" class="btn-reject" onclick="return confirm('Hủy yêu cầu này?')">Hủy bỏ</button>
    </form>
</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
    <!-- 2. DANH SÁCH TÀI KHOẢN -->
    <div class="ad-box">
        <div class="ad-box-header">
            <h3 style="margin:0; font-family:'Playfair Display', serif; color:#800000;">Danh sách người dùng hệ thống</h3>
            <form action="" method="GET">
                <input type="hidden" name="page" value="users">
                <input type="text" name="search" placeholder="Tìm tên, email..." value="<?= htmlspecialchars($keyword) ?>" style="padding:8px 18px; border-radius:25px; border:1px solid #ccc; outline:none; font-size:13px; width:220px;">
            </form>
        </div>
        <table class="u-table">
            <thead>
                <tr>
                    <th>Hệ thống</th>
                    <th>Vai trò</th>
                    <th>Quyền VIP</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right">Quản trị</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $u): ?>
                <tr>
                    <td>
                        <div style="font-weight:800;"><?= htmlspecialchars($u['fullname']) ?></div>
                        <div style="font-size:11px; color:#999;"><?= htmlspecialchars($u['email']) ?></div>
                    </td>
                    <td>
                        <?php if($u['role'] === 'teacher'): ?>
                            <span class="badge-teacher">Mentor/Giáo viên</span>
                        <?php else: ?>
                            <span style="opacity:0.6; font-weight:700;">Học sinh</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u['role'] === 'student' && $u['is_vip']): ?>
                            <span class="badge-vip">👑 VIP HÀO KHÍ</span>
                        <?php elseif ($u['role'] === 'teacher'): ?>
                            <span style="color:#800000; font-weight:800;">Toàn quyền</span>
                        <?php else: ?>
                            <span style="color:#ccc;">Tài khoản thường</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color:<?= $u['status']==='active'?'#1e8449':'#c0392b' ?>; font-weight:800;">
                            ● <?= $u['status']==='active'?'Hoạt động':'Đã khóa' ?>
                        </span>
                    </td>
                    <td style="text-align:right">
                        <form method="POST" action="admin_dashboard.php?page=users">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $u['status'] ?>">
                            <button type="submit" name="action" value="toggle_status" class="btn-lock">
                                <?= $u['status'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>