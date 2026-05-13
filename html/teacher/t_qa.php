<?php
/**
 * MODULE: t_qa.php - GIẢI ĐÁP SƯ PHẠM (Hệ thống đã đồng bộ DB)
 */
require_once __DIR__ . '/../../php/config.php';
$db = getDB();
$msg = "";

// 1. XỬ LÝ GỬI PHẢN HỒI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qa_submit'])) {
    $target_id = $_POST['qa_target'];
    $title     = htmlspecialchars($_POST['qa_title']);
    $content   = htmlspecialchars($_POST['qa_content']); // Nội dung trả lời
    $ref_id    = $_POST['qa_ref_id'];

    // Gửi thông báo cho học sinh
    $sql_noti = "INSERT INTO fb_notifications (fb_receiver_id, fb_title, fb_message) VALUES (?, ?, ?)";
    $db->prepare($sql_noti)->execute([$target_id, $title, $content]);
    
    if ($ref_id) { 
        // Cập nhật câu trả lời vào bảng teacher_qa (Cột answer và status='answered')
        $sql_update = "UPDATE teacher_qa SET answer = ?, status = 'answered', answered_at = NOW() WHERE id = ?";
        $db->prepare($sql_update)->execute([$content, $ref_id]); 
        $msg = "Đã gửi lời giải đáp tới Sĩ tử thành công!";
    }
}

// 2. THỐNG KÊ
$count_new = $db->query("SELECT COUNT(*) FROM teacher_qa WHERE status = 'pending'")->fetchColumn() ?: 0;
$count_total = $db->query("SELECT COUNT(*) FROM teacher_qa")->fetchColumn() ?: 0;
$students = $db->query("SELECT id, fullname FROM users WHERE role = 'student' ORDER BY fullname ASC")->fetchAll();

// 3. DANH SÁCH (Fix cột user_id và question)
$qa_list = $db->query("
    SELECT q.*, u.fullname 
    FROM teacher_qa q 
    JOIN users u ON q.user_id = u.id 
    ORDER BY CASE WHEN q.status = 'pending' THEN 0 ELSE 1 END, q.created_at DESC
")->fetchAll();
?>

<div class="qa-container" style="font-family: 'Nunito', sans-serif;">
    <h2 class="t-page-title" style="color: #800000; font-family: 'Playfair Display', serif;">
        <span class="material-symbols-outlined">history_edu</span> Giải đáp chuyên gia
    </h2>

    <div class="qa-stats-row" style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 15px; border-left: 5px solid #800000; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 800; color: #999;">CHỜ GIẢI ĐÁP</div>
            <div style="font-size: 28px; font-weight: 800; color: #800000;"><?= $count_new ?></div>
        </div>
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 15px; border-left: 5px solid #BF9B30; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 800; color: #999;">TỔNG CÂU HỎI</div>
            <div style="font-size: 28px; font-weight: 800; color: #333;"><?= $count_total ?></div>
        </div>
    </div>

    <div class="qa-form-panel" id="qa_form" style="background: #fff; padding: 30px; border-radius: 20px; margin-bottom: 40px; border: 1px solid #eee;">
        <h3 style="font-family: 'Playfair Display', serif; color: #800000; margin-bottom: 20px;">Soạn lời giải đáp</h3>
        <?php if($msg): ?>
            <div style="background: #e6f4ea; color: #1e7e34; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 700;"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="qa_ref_id" id="qa_ref_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 5px;">GỬI ĐẾN</label>
                    <select name="qa_target" id="qa_target" class="qa-field" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="0">Tất cả Sĩ tử</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['fullname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 5px;">TIÊU ĐỀ</label>
                    <input type="text" name="qa_title" id="qa_title" class="qa-field" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required>
                </div>
            </div>
            <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 5px;">NỘI DUNG GIẢI ĐÁP</label>
            <textarea name="qa_content" id="qa_content" rows="4" style="width:100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" required></textarea>
            <div style="text-align: right; margin-top: 15px;">
                <button type="submit" name="qa_submit" style="background: #800000; color: #D4AF37; border: none; padding: 12px 30px; border-radius: 99px;font-family: 'Nunito', sans-serif; font-weight: 800; cursor: pointer;">GỬI LỜI CHỈ DẪN</button>
            </div>
        </form>
    </div>

    <div class="qa-feed">
        <?php foreach($qa_list as $q): ?>
            <div class="qa-feed-item" style="background: #fff; padding: 20px; border-radius: 15px; border: 1px solid #eee; margin-bottom: 15px; display: flex; gap: 15px;">
                <div style="width: 50px; height: 50px; background: #800000; color: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px;">
                    <?= mb_substr($q['fullname'], 0, 1) ?>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between;">
                        <b style="color: #800000;"><?= htmlspecialchars($q['fullname']) ?></b>
                        <span style="font-size: 11px; padding: 4px 10px; border-radius: 10px; font-weight: 800; background: <?= $q['status']=='pending' ? '#f5f5f5':'#e6f4ea' ?>; color: <?= $q['status']=='pending' ? '#888':'#1e7e34' ?>;">
                            <?= $q['status']=='pending' ? 'CHỜ GIẢI ĐÁP' : 'ĐÃ HỒI ĐÁP' ?>
                        </span>
                    </div>
                    <div style="margin-top: 10px; padding: 15px; background: #fdfaf5; border-radius: 10px; font-size: 14px; border: 1px solid #eee;">
                        <b style="color: #BF9B30; font-size: 11px;">CÂU HỎI:</b><br>
                        <?= nl2br(htmlspecialchars($q['question'])) ?>
                    </div>
                    <?php if($q['status'] === 'pending'): ?>
                        <div style="text-align: right; margin-top: 10px;">
                            <button onclick="reply_to('<?= $q['user_id'] ?>', '<?= $q['id'] ?>', '<?= addslashes($q['fullname']) ?>')" style="background: none; border: 1px solid #800000; color: #800000; padding: 5px 15px; border-radius: 5px; font-family: 'Nunito', sans-serif; font-size: 12px; font-weight: 800; cursor: pointer;">Phản hồi ngay</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function reply_to(uid, qid, name) {
    document.getElementById('qa_form').scrollIntoView({ behavior: 'smooth' });
    document.getElementById('qa_target').value = uid;
    document.getElementById('qa_ref_id').value = qid;
    document.getElementById('qa_title').value = "Giải đáp cho Sĩ tử " + name;
    document.getElementById('qa_content').focus();
}
</script>