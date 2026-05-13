<?php
/**
 * MODULE: ad_main.php - TRUNG TÂM VẬN HÀNH (BẢN ĐẦY ĐỦ)
 * Dữ liệu thực từ: users, vip_requests, teacher_qa
 */

if (!isset($db)) {
    require_once '../../php/config.php';
    $db = getDB();
}

try {
    // 1. DOANH THU & VIP
    $total_revenue = $db->query("SELECT SUM(amount) FROM vip_requests WHERE status = 'approved'")->fetchColumn() ?: 0;
    $revenue_month = $db->query("SELECT SUM(amount) FROM vip_requests WHERE status = 'approved' AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn() ?: 0;
    $stat_pending_vip = $db->query("SELECT COUNT(*) FROM vip_requests WHERE status = 'pending'")->fetchColumn() ?: 0;

    // 2. THỐNG KÊ CHI TIẾT USER
    $counts = $db->query("SELECT role, COUNT(*) as qty FROM users GROUP BY role")->fetchAll(PDO::FETCH_KEY_PAIR);
    $stat_students = $counts['student'] ?? 0;
    $stat_teachers = $counts['teacher'] ?? 0;
    $stat_vip      = $db->query("SELECT COUNT(*) FROM users WHERE is_vip = 1 AND role = 'student'")->fetchColumn() ?: 0;

    // 3. DỮ LIỆU BIỂU ĐỒ (7 ngày gần nhất)
    $chart_labels = []; $chart_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = date('d/m', strtotime($d));
        $chart_data[] = $db->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = '$d' AND role = 'student'")->fetchColumn() ?: 0;
    }

    // 4. Q&A TỒN ĐỘNG
    $pending_qa = $db->query("SELECT COUNT(*) FROM teacher_qa WHERE status = 'pending'")->fetchColumn() ?: 0;

    // 5. DANH SÁCH GIAO DỊCH GẦN ĐÂY (Lấp đầy khoảng trống)
    $recent_vips = $db->query("
        SELECT v.*, u.fullname 
        FROM vip_requests v 
        JOIN users u ON v.user_id = u.id 
        ORDER BY v.created_at DESC LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<!-- 1. HÀNG THẺ THỐNG KÊ (Stat Cards) -->
<div class="stats-grid">
    <div class="stat-card" style="border-top-color: #1e8449;">
        <div class="stat-label">Tổng doanh thu thực</div>
        <div class="stat-value"><?= number_format($total_revenue) ?>đ</div>
        <div class="stat-sub">Tháng này: <b style="color:#1e8449;">+<?= number_format($revenue_month) ?>đ</b></div>
    </div>
    <div class="stat-card" style="border-top-color: #BF9B30;">
        <div class="stat-label">VIP chờ phê duyệt</div>
        <div class="stat-value"><?= $stat_pending_vip ?></div>
        <div class="stat-sub"><?= $stat_pending_vip > 0 ? '<span class="ad-blink">🔴 Cần đối soát</span>' : '🟢 Đã khớp' ?></div>
    </div>
    <div class="stat-card" style="border-top-color: #800000;">
        <div class="stat-label">Tổng Sĩ tử</div>
        <div class="stat-value"><?= $stat_students ?></div>
        <div class="stat-sub">Đã lên VIP: <b><?= $stat_vip ?></b></div>
    </div>
    <div class="stat-card" style="border-top-color: #2980b9;">
        <div class="stat-label">Q&A tồn đọng</div>
        <div class="stat-value"><?= $pending_qa ?></div>
        <div class="stat-sub">Câu hỏi chưa giải đáp</div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- 2. BIỂU ĐỒ TĂNG TRƯỞNG (Trái) -->
    <div class="col-lg-8">
        <div class="content-card" style="height: 100%;">
            <div class="content-card-header">
                <div class="content-card-title"><span class="material-symbols-outlined">trending_up</span> Tốc độ tăng trưởng Sĩ tử (7 ngày)</div>
            </div>
            <div class="content-card-body">
                <canvas id="growthChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. DANH SÁCH GIAO DỊCH GẦN ĐÂY (Phải) -->
    <div class="col-lg-4">
        <div class="content-card" style="height: 100%;">
            <div class="content-card-header">
                <div class="content-card-title"><span class="material-symbols-outlined">history</span> Yêu cầu VIP mới nhất</div>
            </div>
            <div class="content-card-body" style="padding: 0;">
                <?php if(empty($recent_vips)): ?>
                    <p style="padding:20px; color:#999; font-style:italic;">Chưa có giao dịch nào.</p>
                <?php else: ?>
                    <?php foreach($recent_vips as $rv): ?>
                        <div style="padding: 12px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 800; font-size: 13px;"><?= htmlspecialchars($rv['fullname']) ?></div>
                                <div style="font-size: 11px; color: #888;"><?= $rv['transaction_code'] ?: 'Không mã' ?></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 800; color: #1e8449; font-size: 13px;">+<?= number_format($rv['amount']) ?>đ</div>
                                <div style="font-size: 10px; text-transform: uppercase;" class="badge <?= $rv['status'] == 'approved' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $rv['status'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 4. THÔNG SỐ KỸ THUẬT & HỆ THỐNG -->
<div class="content-card mt-4">
    <div class="content-card-header">
        <div class="content-card-title"><span class="material-symbols-outlined">settings_suggest</span> Sức khỏe hệ thống</div>
    </div>
    <div class="content-card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <div style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;">Giáo viên/Mentor</div>
                <div style="font-size: 24px; font-weight: 800; color: #800000;"><?= $stat_teachers ?></div>
            </div>
            <div class="col-md-3" style="border-left: 1px solid #eee;">
                <div style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;">Dung lượng Media</div>
                <div style="font-size: 24px; font-weight: 800; color: #2980b9;">1.2GB <small style="font-size:12px;">/ 5GB</small></div>
            </div>
            <div class="col-md-3" style="border-left: 1px solid #eee;">
                <div style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;">Phản hồi lỗi</div>
                <div style="font-size: 24px; font-weight: 800; color: #e67e22;">0</div>
            </div>
            <div class="col-md-3" style="border-left: 1px solid #eee;">
                <div style="font-size: 11px; font-weight: 800; color: #999; text-transform: uppercase;">Phiên bản</div>
                <div style="font-size: 24px; font-weight: 800; color: #666;">v2.1.0</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Sĩ tử mới',
                data: <?= json_encode($chart_data) ?>,
                borderColor: '#BF9B30',
                backgroundColor: 'rgba(191, 155, 48, 0.05)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#BF9B30'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f5f5f5' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<style>
.ad-blink { animation: ad-blink-anim 1.5s infinite; font-weight: 800; color: #c0392b; }
@keyframes ad-blink-anim { 50% { opacity: 0.3; } }
.badge-success { background: #eafaf1; color: #1e8449; padding: 2px 8px; border-radius: 4px; }
.badge-warning { background: #fff4e5; color: #b95000; padding: 2px 8px; border-radius: 4px; }
</style>