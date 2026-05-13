<?php
/**
 * html/teacher/t_resources.php
 * Module: Kho tài nguyên bài học — Đồng bộ 100% giao diện Teacher
 */

if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

$success_msg = '';
$error_msg   = '';

// ── XỬ LÝ LOGIC (Thêm & Cập nhật) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $upload_dir = '../../uploads/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    // 1. CẬP NHẬT HOẶC THÊM MỚI (Dùng chung logic lưu)
    $lid = (int)($_POST['ct_lesson_id'] ?? 0);
    $yt  = trim($_POST['ct_youtube_link'] ?? '');
    $sl  = trim($_POST['ct_slide_link']   ?? '');
    $fq  = trim($_POST['ct_flashcard_question'] ?? '');
    $fa  = trim($_POST['ct_flashcard_answer']   ?? '');

    if ($lid > 0) {
        // Xử lý upload file tài liệu (nếu có)
        $file_path = '';
        if (!empty($_FILES['document_file']['name'])) {
            $ext  = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
            $doc_name = 'doc_' . $lid . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $upload_dir . $doc_name)) {
                $file_path = 'uploads/' . $doc_name;
            }
        }

        // Lấy lại đường dẫn file cũ nếu không upload mới
        if (empty($file_path)) {
            $old = $db->prepare("SELECT ct_file_path FROM lessons_content WHERE ct_lesson_id = ?");
            $old->execute([$lid]);
            $file_path = $old->fetchColumn() ?: '';
        }

        $sql = "INSERT INTO lessons_content 
                (ct_lesson_id, ct_youtube_link, ct_slide_link, ct_flashcard_question, ct_flashcard_answer, ct_file_path) 
                VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE 
                ct_youtube_link=VALUES(ct_youtube_link), 
                ct_slide_link=VALUES(ct_slide_link), 
                ct_flashcard_question=VALUES(ct_flashcard_question), 
                ct_flashcard_answer=VALUES(ct_flashcard_answer),
                ct_file_path=VALUES(ct_file_path)";
        
        if($db->prepare($sql)->execute([$lid, $yt, $sl, $fq, $fa, $file_path])) {
            $success_msg = "Lưu nội dung Bài $lid thành công!";
        }
    }
}

// ── LẤY DỮ LIỆU ──
$rows = $db->query("SELECT * FROM lessons_content ORDER BY ct_lesson_id ASC")->fetchAll(PDO::FETCH_ASSOC);
$ct_map = [];
foreach ($rows as $r) $ct_map[$r['ct_lesson_id']] = $r;

// Danh sách mặc định để hiển thị
$default_ids = [6, 7, 8];
$all_ids = array_unique(array_merge($default_ids, array_keys($ct_map)));
sort($all_ids);

$lesson_names = [
    6 => 'Cách Mạng Tháng Tám 1945',
    7 => 'Kháng Chiến Chống Pháp (1945–1954)',
    8 => 'Kháng Chiến Chống Mỹ (1954–1975)',
];
?>

<style>
/* ══ THEME SYNC ══ */
.t-res-container { animation: fadeIn 0.5s ease; }

/* Thêm mới panel */
.t-add-panel { 
    background: #FFFDF5; border: 2px dashed var(--gold); 
    border-radius: var(--radius-lg); padding: 30px; margin-bottom: 40px; 
}

/* Card bài học */
.t-lesson-card { 
    background: var(--white); border-radius: var(--radius-lg); padding: 35px; 
    margin-bottom: 30px; box-shadow: var(--shadow-sm); border: 1px solid var(--border);
    position: relative; overflow: hidden;
}
.t-lesson-card::before {
    content: ""; position: absolute; top: 0; left: 0; width: 6px; height: 100%;
    background: var(--maroon);
}

.t-card-title { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; color: var(--maroon); }
.t-card-title h3 { font-family: 'Playfair Display', serif; font-weight: 800; font-size: 20px; margin: 0; }

/* Grid & Form */
.t-input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
.t-form-group label { 
    display: block; font-weight: 700; font-size: 11px; color: var(--text-mid); 
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
}
.t-control { 
    width: 100%; padding: 14px; border: 1.5px solid var(--border); 
    border-radius: 12px; background: #fafafa; font-family: 'Nunito', sans-serif;
    transition: 0.3s; box-sizing: border-box;
}
.t-control:focus { border-color: var(--gold); background: #fff; outline: none; }

.t-flash-box { background: #fdfbf5; padding: 20px; border-radius: 15px; border: 1px dashed var(--gold-pale); }

/* Button */
.t-btn-save { 
    background: linear-gradient(135deg, var(--maroon), var(--maroon-dark));
    color: var(--gold-pale); border: none; padding: 15px 35px; border-radius: 50px; 
    font-family: 'Nunito', sans-serif; font-weight: 800; cursor: pointer; transition: 0.4s; display: inline-flex; align-items: center; gap: 8px;
}
.t-btn-save:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(128,0,0,0.2); }
</style>

<div class="t-res-container">
    <h2 class="t-page-title">
        <span class="material-symbols-outlined">edit_document</span> 
        Quản lý nội dung bài học
    </h2>

    <?php if($success_msg): ?>
        <div class="t-alert t-alert-success" style="margin-bottom:30px;">
            <span class="material-symbols-outlined">verified</span> <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <div class="t-add-panel">
        <div class="t-card-title" style="color: var(--gold-dark);">
            <span class="material-symbols-outlined">library_add</span>
            <h3>Khai phá bài học mới hoàn toàn</h3>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            
            <div style="margin-bottom: 20px; width: 200px;">
                <div class="t-form-group">
                    <label>Mã bài (ID) <span style="color:var(--maroon)">*</span></label>
                    <input type="number" class="t-control" name="ct_lesson_id" placeholder="VD: 9" required>
                </div>
            </div>

            <div class="t-input-grid">
                <div class="t-form-group">
                    <label><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">smart_display</span> Link YouTube</label>
                    <input class="t-control" name="ct_youtube_link" placeholder="https://youtube.com/...">
                </div>
                <div class="t-form-group">
                    <label><span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">co_present</span> Link Slide bài giảng</label>
                    <input class="t-control" name="ct_slide_link" placeholder="Link Canva/PPT...">
                </div>
            </div>

            <div class="t-form-group" style="margin-bottom: 20px;">
                <label>Tài liệu đính kèm (PDF/DOCX)</label>
                <input type="file" class="t-control" name="document_file">
            </div>

            <div class="t-input-grid">
                <div class="t-form-group t-flash-box">
                    <label style="color: var(--gold-dark);">Câu hỏi Flashcard</label>
                    <textarea class="t-control" name="ct_flashcard_question" rows="3"></textarea>
                </div>
                <div class="t-form-group t-flash-box">
                    <label style="color: var(--gold-dark);">Đáp án Flashcard</label>
                    <textarea class="t-control" name="ct_flashcard_answer" rows="3"></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="t-btn-save" style="background: var(--gold); color: var(--maroon);">
                    <span class="material-symbols-outlined">add_task</span> TẠO BÀI HỌC MỚI
                </button>
            </div>
        </form>
    </div>

    <div class="section-header">
        <div class="section-badge"></div>
        <h3>Hiệu chỉnh giáo trình hiện hành</h3>
    </div>

    <?php foreach ($all_ids as $lid): 
        $d = $ct_map[$lid] ?? [];
        $name = $lesson_names[$lid] ?? 'Chương mục bổ sung';
    ?>
    <div class="t-lesson-card">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="ct_lesson_id" value="<?= $lid ?>">

            <div class="t-card-title">
                <span class="material-symbols-outlined">menu_book</span>
                <h3>Chương mục Bài <?= $lid ?>: <?= htmlspecialchars($name) ?></h3>
            </div>

            <div class="t-input-grid">
                <div class="t-form-group">
                    <label>Link YouTube bài giảng</label>
                    <input class="t-control" name="ct_youtube_link" value="<?= $d['ct_youtube_link'] ?? '' ?>">
                </div>
                <div class="t-form-group">
                    <label>Link Slide bài giảng</label>
                    <input class="t-control" name="ct_slide_link" value="<?= $d['ct_slide_link'] ?? '' ?>">
                </div>
            </div>

            <div class="t-form-group" style="margin-bottom: 20px;">
                <label>Tài liệu đính kèm (PDF/DOCX)</label>
                <input type="file" class="t-control" name="document_file">
                <?php if(!empty($d['ct_file_path'])): ?>
                    <div style="font-size:11px; color:var(--gold); margin-top:5px; font-style:italic;">
                        📎 Đang có: <?= basename($d['ct_file_path']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="t-input-grid">
                <div class="t-form-group t-flash-box">
                    <label style="color: var(--gold-dark);">Câu hỏi Flashcard</label>
                    <textarea class="t-control" name="ct_flashcard_question" rows="3"><?= $d['ct_flashcard_question'] ?? '' ?></textarea>
                </div>
                <div class="t-form-group t-flash-box">
                    <label style="color: var(--gold-dark);">Đáp án Flashcard</label>
                    <textarea class="t-control" name="ct_flashcard_answer" rows="3"><?= $d['ct_flashcard_answer'] ?? '' ?></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 25px;">
                <button type="submit" class="t-btn-save">
                    <span class="material-symbols-outlined">verified</span> LƯU NỘI DUNG BÀI <?= $lid ?>
                </button>
            </div>
        </form>
    </div>
    <?php endforeach; ?>
</div>