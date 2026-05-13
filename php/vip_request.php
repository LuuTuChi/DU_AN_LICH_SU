<?php
require_once 'config.php';
header('Content-Type: application/json');

// 1. Kiểm tra đăng nhập
$uid = getCurrentUserId();
if (!$uid) { 
    echo json_encode(['error' => 'Chưa đăng nhập']); 
    exit; 
}

try {
    $db = getDB();

    // 2. Tiếp nhận dữ liệu từ FormData (Dùng $_POST thay vì file json)
    $amount = 999000; // Mặc định học phí trọn khóa
    $trans_code = $_POST['transaction_code'] ?? '';
    $image_path = '';

    // 3. Xử lý tải ảnh minh chứng (Proof Image)
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
       $upload_dir = '../html/proofs/'; // Đảm bảo Chi đã tạo thư mục này
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'proof_' . $uid . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/proofs/' . $file_name; // Lưu đường dẫn tương đối
        }
    }

    // 4. Chèn yêu cầu vào bảng vip_requests (Cập nhật đủ các cột đã thêm)
    // Lưu ý: Nếu user đã có yêu cầu pending trước đó, ta cập nhật lại thông tin mới nhất
    $stmt = $db->prepare("
        INSERT INTO vip_requests (user_id, amount, transaction_code, proof_image, status) 
        VALUES (?, ?, ?, ?, 'pending')
        ON DUPLICATE KEY UPDATE 
            amount = VALUES(amount), 
            transaction_code = VALUES(transaction_code), 
            proof_image = VALUES(proof_image),
            status = 'pending',
            created_at = CURRENT_TIMESTAMP
    ");
    
    $stmt->execute([$uid, $amount, $trans_code, $image_path]);

    echo json_encode(['success' => true, 'message' => 'Đã gửi yêu cầu phê duyệt']);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Lỗi DB: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}