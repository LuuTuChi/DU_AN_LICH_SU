<?php
/**
 * TRUNG TÂM ĐIỀU PHỐI SƯ PHẠM - SỬ VIỆT
 * Phiên bản: Hoàn thiện 100% theo Database thực tế
 */

if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

try {
    // 1. THỐNG KÊ SĨ TỬ (Bảng users)
    $st_data = $db->query("SELECT COUNT(*) as total, SUM(CASE WHEN is_vip = 1 THEN 1 ELSE 0 END) as vip_count FROM users WHERE role = 'student'")->fetch();
    $total_students = $st_data['total'] ?? 0;
    $vip_students = $st_data['vip_count'] ?? 0;
    $normal_students = $total_students - $vip_students;

    // 2. ĐIỂM QUIZ TRUNG BÌNH & RỦI RO (Bảng quiz_scores)
    $avg_score_raw = $db->query("SELECT AVG(score / total_q * 10) FROM quiz_scores WHERE total_q > 0")->fetchColumn();
    $avg_score = $avg_score_raw ? number_format($avg_score_raw, 1) : '0.0';

    $risk_students = $db->query("SELECT COUNT(*) FROM (SELECT user_id FROM quiz_scores GROUP BY user_id HAVING AVG(score / total_q * 10) < 5.0) as sub")->fetchColumn() ?: 0;

    // 3. TIẾN ĐỘ LỚP (Bảng lesson_progress)
    $avg_progress = $db->query("SELECT AVG(pct_done) FROM lesson_progress")->fetchColumn();
    $avg_progress_display = $avg_progress ? round($avg_progress) : 0;

    // 4. ACTION CENTER (Bảng teacher_qa)
    $stat_qa = $db->query("SELECT COUNT(*) FROM teacher_qa WHERE status = 'pending'")->fetchColumn() ?: 0;

    // 5. DỮ LIỆU BIỂU ĐỒ XU HƯỚNG (7 ngày gần nhất từ quiz_scores)
    $chart_data = [];
    $chart_labels = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = date('d/m', strtotime($date));
        $val = $db->query("SELECT AVG(score / total_q * 10) FROM quiz_scores WHERE DATE(taken_at) = '$date'")->fetchColumn() ?: 0;
        $chart_data[] = round($val, 1);
    }

    // 6. VINH DANH (Lấy Top 1 điểm Quiz thực tế)
    $top_student = $db->query("
        SELECT u.fullname, AVG(q.score / q.total_q * 10) as avg_point 
        FROM quiz_scores q JOIN users u ON q.user_id = u.id 
        GROUP BY q.user_id ORDER BY avg_point DESC LIMIT 1
    ")->fetch();

    // 7. BÀI HỌC TRỌNG TÂM (Lấy learners thực tế)
    $lesson_stats = $db->query("
        SELECT lp.lesson_id, COUNT(DISTINCT lp.user_id) as learners, AVG(lp.pct_done) as avg_pct,
        (SELECT AVG(score/total_q*10) FROM quiz_scores WHERE lesson_id = lp.lesson_id) as avg_quiz,
        (SELECT COUNT(*) FROM quiz_scores WHERE lesson_id = lp.lesson_id AND (score/total_q*10) < 5.0) as weak_count
        FROM lesson_progress lp GROUP BY lp.lesson_id
    ")->fetchAll(PDO::FETCH_UNIQUE);

    $lessons_meta = [6 => 'Cách Mạng Tháng Tám 1945', 7 => 'Kháng Chiến Chống Pháp (1945–1954)', 8 => 'Kháng Chiến Chống Mỹ (1954–1975)'];

} catch (PDOException $e) {
    die("Lỗi kết nối SQL: " . $e->getMessage());
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ĐỒNG BỘ 100% VARIABLE VỚI T_HEATMAP */
    :root { --hm-m:#800000; --hm-g:#BF9B30; --hm-gp:#e8d5a3; --hm-cbd:#e8d9b8; --hm-cdk:#f0e6cc; --hm-sh:0 2px 12px rgba(128,0,0,.09); }
    .lux-wrap { padding: 30px; background: #fdfaf5; animation: fadeIn 0.5s ease; }
    .hm-title { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--hm-m); display: flex; align-items: center; gap: 12px; }
    .hm-title::after { content: ''; flex: 1; height: 2px; max-width: 120px; background: linear-gradient(90deg, var(--hm-g), transparent); border-radius: 2px; }
    
    /* CARDS */
    .hm-action-center { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 20px 0; }
    .hm-ac { background: #fff; border-radius: 10px; padding: 18px 20px; position: relative; overflow: hidden; box-shadow: var(--hm-sh); border: 1.5px solid var(--hm-cbd); }
    .hm-ac-stripe { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--hm-m); }
    .hm-ac-lbl { font-size: .62rem; font-weight: 900; text-transform: uppercase; color: #9a7040; letter-spacing: .8px; }
    .hm-ac-val { font-family: 'JetBrains Mono', monospace; font-size: 1.8rem; font-weight: 700; color: var(--hm-m); }

    /* PANELS */
    .hm-panel { background: #fff; border: 1px solid var(--hm-cbd); border-radius: 10px; padding: 25px; box-shadow: var(--hm-sh); margin-bottom: 20px; }
    .hm-panel-title { font-size: .66rem; font-weight: 900; text-transform: uppercase; color: var(--hm-g); margin-bottom: 15px; display: flex; align-items: center; gap: 7px; }
    .hm-panel-title::after { content: ''; flex: 1; height: 1px; background: var(--hm-cbd); }
    
    .hm-tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
    .hm-tbl th { background: var(--hm-cdk); padding: 12px; font-size: .62rem; font-weight: 900; text-transform: uppercase; color: #9a6a30; border-bottom: 2px solid #d4c07a; }
    .hm-tbl td { padding: 12px; border-bottom: 1px solid var(--hm-cdk); font-size: .82rem; font-weight: 700; color: #444; }

    .badge-risk { background: #fdecea; color: #c0392b; font-size: 9px; padding: 3px 8px; border-radius: 4px; font-weight: 900; }
    .badge-ok { background: #eafaf1; color: #1e8449; font-size: 9px; padding: 3px 8px; border-radius: 4px; font-weight: 900; }
</style>

<div class="lux-wrap">
    <div class="hm-title"><span class="material-symbols-outlined">analytics</span> Tổng quan sư phạm</div>
    <div style="font-size: .78rem; color: #a07040; margin-bottom: 25px;">Dữ liệu trực tiếp từ SQL · <?= date('H:i, d/m/Y') ?></div>

    <div class="hm-action-center">
        <div class="hm-ac">
            <div class="hm-ac-stripe"></div>
            <div class="hm-ac-lbl">Sĩ tử đang học</div>
            <div class="hm-ac-val"><?= $total_students ?></div>
            <div style="font-size: 11px; color: #9a7040;">👑 VIP: <?= $vip_students ?> | THƯỜNG: <?= $normal_students ?></div>
        </div>
        <div class="hm-ac">
            <div class="hm-ac-stripe" style="background: #1e8449;"></div>
            <div class="hm-ac-lbl">Phổ điểm trung bình</div>
            <div class="hm-ac-val" style="color: #1e8449;"><?= $avg_score ?><small style="font-size: 1rem; opacity: .5">/10</small></div>
            <div style="font-size: 11px; color: #1e8449;">Tiến độ lớp: <?= $avg_progress_display ?>%</div>
        </div>
        <div class="hm-ac">
            <div class="hm-ac-stripe" style="background: #c0392b;"></div>
            <div class="hm-ac-lbl">Cần xử lý ngay</div>
            <div class="hm-ac-val" style="color: #c0392b;"><?= $risk_students ?></div>
            <div style="font-size: 11px; color: #c0392b;">Điểm TB < 5.0 & <?= $stat_qa ?> Q&A chờ</div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="hm-panel" style="height: 100%;">
                <div class="hm-panel-title"><span class="material-symbols-outlined">show_chart</span> Xu hướng học tập 7 ngày qua</div>
                <canvas id="trendChart" height="160"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="hm-panel" style="height: 100%;">
                <div class="hm-panel-title"><span class="material-symbols-outlined">stars</span> Bảng vàng sĩ tử</div>
                <?php if ($top_student): ?>
                <div style="background: #fdf8ee; padding: 15px; border-radius: 8px; border-left: 4px solid var(--hm-g);">
                    <div style="font-weight: 800; color: var(--hm-m);"><?= htmlspecialchars($top_student['fullname']) ?> 👑</div>
                    <div style="font-size: 11px; color: #a07040;">GPA Quiz: <?= number_format($top_student['avg_point'], 1) ?>/10</div>
                </div>
                <?php else: ?>
                <div style="font-size: 12px; color: #ccc; font-style: italic;">Đang cập nhật dữ liệu...</div>
                <?php endif; ?>
                <div style="margin-top: 20px; font-size: 11px; color: #9a6a30; border: 1px dashed var(--hm-g); padding: 12px; border-radius: 8px;">
                    <i class="fas fa-fire me-1"></i> Bài học Hot nhất: <br><strong>Chiến dịch Điện Biên Phủ</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="hm-panel">
        <div class="hm-panel-title"><span class="material-symbols-outlined">list_alt</span> Tình trạng nội dung giảng dạy</div>
        <table class="hm-tbl">
            <thead>
                <tr>
                    <th class="ps-4">Bài học</th>
                    <th class="text-center">Sĩ tử học</th>
                    <th class="text-center">Tiến độ TB</th>
                    <th class="text-center">Quiz TB</th>
                    <th class="text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lessons_meta as $lid => $lname): 
                    $ls = $lesson_stats[$lid] ?? ['learners'=>0, 'avg_pct'=>0, 'avg_quiz'=>0, 'weak_count'=>0];
                    $is_risk = ($ls['learners'] > 0 && ($ls['weak_count'] / $ls['learners'] * 100) > 30);
                ?>
                <tr>
                    <td class="ps-4">
                        <div style="color: var(--hm-m); font-weight: 800;">Bài <?= $lid ?></div>
                        <div style="font-size: 11px; color: #a07040;"><?= $lname ?></div>
                    </td>
                    <td class="text-center"><?= $ls['learners'] ?> bạn</td>
                    <td class="text-center">
                        <div style="width: 80px; height: 6px; background: var(--hm-cdk); border-radius: 10px; display: inline-block; overflow: hidden; margin-right: 5px;">
                            <div style="width: <?= $ls['avg_pct'] ?>%; height: 100%; background: var(--hm-g);"></div>
                        </div>
                        <span style="font-size: 11px;"><?= round($ls['avg_pct']) ?>%</span>
                    </td>
                    <td class="text-center"><?= number_format($ls['avg_quiz'], 1) ?>/10</td>
                    <td class="text-center">
                        <?= $is_risk ? '<span class="badge-risk">ĐỊNH HƯỚNG LẠI</span>' : '<span class="badge-ok">ỔN ĐỊNH</span>' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="background: #fdf8ee; border-left: 10px solid var(--hm-g); padding: 20px; border-radius: 10px; display: flex; gap: 20px; align-items: center; box-shadow: var(--hm-sh);">
        <span class="material-symbols-outlined" style="font-size: 40px; color: var(--hm-m);">psychology</span>
        <div style="font-size: 13px; color: var(--hm-m); font-weight: 600; line-height: 1.5;">
            <b>Cố vấn AI:</b> Phát hiện <b><?= $risk_students ?> sĩ tử</b> có dấu hiệu hổng kiến thức (Điểm < 5.0). 
            Hãy dùng <b>Heatmap</b> để soi chi tiết hoặc mở <b>Live-Review</b> để giảng lại bài.
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Điểm TB lớp',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#800000', backgroundColor: 'rgba(128, 0, 0, 0.05)',
                borderWidth: 3, tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#BF9B30'
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false, grid: { color: '#f0e6cc' } }, x: { grid: { display: false } } } }
    });
</script>