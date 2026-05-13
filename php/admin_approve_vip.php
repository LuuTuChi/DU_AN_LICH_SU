<?php
// ============================================================
//  admin_approve_vip.php — Duyệt / Thu hồi VIP
//  POST  php/admin_approve_vip.php
//  Body JSON: { action: "approve"|"revoke", target_user_id: int }
//  Chỉ admin (role = 'admin') mới được gọi endpoint này
// ============================================================
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// ── Chỉ nhận POST ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── Kiểm tra quyền admin ────────────────────────────────────
$db     = getDB();
$caller = getCurrentUserId();

if (!$caller) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Chưa đăng nhập']);
    exit;
}

$stmtRole = $db->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$stmtRole->execute([$caller]);
$callerRole = $stmtRole->fetchColumn();

if ($callerRole !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Không có quyền thực hiện']);
    exit;
}

// ── Đọc body ────────────────────────────────────────────────
$raw    = file_get_contents('php://input');
$data   = json_decode($raw, true);
$action = $data['action']         ?? '';          // 'approve' | 'revoke'
$tid    = (int)($data['target_user_id'] ?? 0);    // ID học sinh cần duyệt

if (!$tid || !in_array($action, ['approve', 'revoke'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Thiếu hoặc sai tham số']);
    exit;
}

// ── Kiểm tra user tồn tại ───────────────────────────────────
$stmtCheck = $db->prepare("SELECT id, fullname, is_vip FROM users WHERE id = ? LIMIT 1");
$stmtCheck->execute([$tid]);
$target = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Không tìm thấy học sinh']);
    exit;
}

// ── Cập nhật is_vip ─────────────────────────────────────────
$newVip = ($action === 'approve') ? 1 : 0;

$stmtUpdate = $db->prepare("
    UPDATE users
    SET is_vip     = :vip,
        vip_since  = CASE WHEN :vip2 = 1 AND is_vip = 0 THEN NOW() ELSE vip_since END,
        updated_at = NOW()
    WHERE id = :id
");
$stmtUpdate->execute([
    ':vip'  => $newVip,
    ':vip2' => $newVip,
    ':id'   => $tid,
]);

// ── Ghi log duyệt vào bảng vip_logs (nếu có) ───────────────
// Bảng này không bắt buộc — nếu chưa có thì bỏ qua lỗi
try {
    $stmtLog = $db->prepare("
        INSERT INTO vip_logs (user_id, action, approved_by, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmtLog->execute([$tid, $action, $caller]);
} catch (PDOException $e) {
    // Bảng vip_logs chưa tạo → bỏ qua, không crash
}

// ── Ghi thông báo cho học sinh (bảng fb_notifications) ──────
$msg = ($action === 'approve')
    ? '🎉 Tài khoản của bạn đã được nâng cấp lên VIP! Tận hưởng Live-Review & Hỏi chuyên gia ngay nhé.'
    : '⚠️ Tài khoản VIP của bạn đã bị thu hồi. Liên hệ admin để biết thêm chi tiết.';

try {
    $stmtNotif = $db->prepare("
        INSERT INTO fb_notifications (user_id, message, is_read, created_at)
        VALUES (?, ?, 0, NOW())
    ");
    $stmtNotif->execute([$tid, $msg]);
} catch (PDOException $e) {
    // Cột/bảng không khớp → bỏ qua
}

// ── Trả kết quả ─────────────────────────────────────────────
echo json_encode([
    'ok'             => true,
    'action'         => $action,
    'target_user_id' => $tid,
    'target_name'    => $target['fullname'],
    'is_vip_new'     => $newVip,
    'message'        => $action === 'approve'
        ? "Đã duyệt VIP cho {$target['fullname']}"
        : "Đã thu hồi VIP của {$target['fullname']}",
]);