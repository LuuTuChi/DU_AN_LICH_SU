<?php
/* ── Auth check ── */
require_once '../../php/config.php';
$uid = getCurrentUserId();
if (!$uid) {
    header('Location: ../login.html'); exit;
}
$conn = getConn();
$stmt = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
if (!$me || !in_array($me['role'], ['teacher', 'admin'])) {
    die('<div style="padding:60px;text-align:center;font-family:sans-serif;color:#c0392b;font-size:16px">
        ⛔ Bạn không có quyền truy cập trang này.
        <br><br><a href="../login.html" style="color:var(--maroon)">Quay lại đăng nhập</a>
    </div>');
}

/* ── Router ── */
$mod = $_GET['mod'] ?? 'overview';
$allowed = ['overview', 'live', 'qa', 'heatmap', 'resources'];
if (!in_array($mod, $allowed)) $mod = 'overview';
$moduleFile = __DIR__ . '/t_' . $mod . '.php';

/* ── Badge pending QA ── */
$pendingQA = 0;
$pq = $conn->query("SHOW TABLES LIKE 'teacher_qa'");
if ($pq && $pq->num_rows > 0) {
    $pq2 = $conn->query("SELECT COUNT(*) FROM teacher_qa WHERE status='pending'");
    if ($pq2) $pendingQA = (int)$pq2->fetch_row()[0];
}

/* ── Nav config ── */
$navItems = [
    'overview'  => ['icon' => 'dashboard',    'label' => 'Tổng quan lớp'],
    'live'      => ['icon' => 'live_tv',      'label' => 'Live-Review'],
    'qa'        => ['icon' => 'forum',        'label' => 'Hỏi đáp chuyên gia', 'badge' => $pendingQA],
    'heatmap'   => ['icon' => 'grid_on',      'label' => 'Theo dõi tiến độ'],
    'resources' => ['icon' => 'folder_open',  'label' => 'Kho tư liệu'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Giáo viên — Sử Việt</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
<style>
/* ══ RESET ══ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --cream:#f7f4e8;--maroon:#800000;--maroon-dark:#5c0000;
  --gold:#D4AF37;--gold-light:#e8cc6a;--gold-pale:#f5e9b0;
  --ivory:#fffcf3;--white:#ffffff;
  --text-dark:#1a1a1a;--text-mid:#444;--text-muted:#888;
  --border:#e0d9c5;--green:#2e7d32;--green-bg:#f1f8e9;--green-border:#81c784;
  --shadow-sm:0 2px 8px rgba(128,0,0,0.08);
  --shadow-md:0 6px 24px rgba(128,0,0,0.12);
  --shadow-lg:0 12px 40px rgba(128,0,0,0.18);
  --radius-sm:10px;--radius-md:16px;--radius-lg:22px;
}
html{scroll-behavior:smooth}
body{font-family:'Nunito',sans-serif;background:var(--cream);color:var(--text-dark);min-height:100vh;overflow-x:hidden}

/* ══ LAYOUT ══ */
.t-frame{display:flex;min-height:100vh}

/* ══ SIDEBAR ══ */
.t-sidebar{
  width:256px;flex-shrink:0;
  background:linear-gradient(180deg,var(--maroon) 0%,var(--maroon-dark) 100%);
  display:flex;flex-direction:column;
  position:sticky;top:0;height:100vh;overflow-y:auto;
}

/* Brand */
.t-brand{padding:24px 20px 20px;border-bottom:1px solid rgba(255,255,255,.12)}
.t-brand-name{font-family:'Playfair Display',serif;font-size:18px;font-weight:800;color:var(--gold);letter-spacing:.5px}
.t-brand-role{font-size:10.5px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-top:3px}
.t-brand-user{margin-top:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:var(--radius-sm);padding:9px 13px;display:flex;align-items:center;gap:8px}
.t-brand-user .material-symbols-outlined{font-size:18px;color:var(--gold-light);font-variation-settings:'FILL' 1}
.t-brand-user span{font-size:13px;font-weight:700;color:rgba(255,255,255,.85)}

/* Nav */
.t-nav{padding:14px 12px;flex:1}
.t-nav-section{font-size:9.5px;font-weight:800;letter-spacing:1.8px;text-transform:uppercase;color:rgba(255,255,255,.3);padding:10px 10px 6px;margin-top:6px}
.t-nav a{
  display:flex;align-items:center;gap:11px;
  padding:11px 13px;border-radius:var(--radius-sm);
  text-decoration:none;color:rgba(255,255,255,.68);
  font-size:13.5px;font-weight:600;
  transition:all .2s;margin-bottom:2px;position:relative;
}
.t-nav a .material-symbols-outlined{font-size:19px;flex-shrink:0}
.t-nav a:hover{background:rgba(255,255,255,.08);color:white}
.t-nav a.active{
  background:linear-gradient(90deg,var(--gold),#c9a227);
  color:#1a0000;font-weight:800;
  box-shadow:0 3px 12px rgba(212,175,55,.3);
}
.t-nav a.active .material-symbols-outlined{font-variation-settings:'FILL' 1}

/* Badge số */
.nav-badge{
  margin-left:auto;background:#e53935;color:white;
  font-size:10px;font-weight:800;
  padding:2px 7px;border-radius:99px;
  min-width:20px;text-align:center;
}
.t-nav a.active .nav-badge{background:rgba(0,0,0,.2);color:#1a0000}

/* Footer logout */
.t-sidebar-footer{padding:14px 16px 20px;border-top:1px solid rgba(255,255,255,.12)}
.t-sidebar-footer a{
  display:flex;align-items:center;gap:9px;
  color:rgba(255,255,255,.45);text-decoration:none;
  font-size:13px;font-weight:600;padding:9px 10px;
  border-radius:var(--radius-sm);transition:all .2s;
}
.t-sidebar-footer a:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.85)}
.t-sidebar-footer a .material-symbols-outlined{font-size:17px}

/* ══ MAIN ══ */
.t-main{flex:1;min-width:0;padding:36px 40px;overflow-y:auto}

/* Page title — dùng chung cho mọi module */
.t-page-title{
  font-family:'Playfair Display',serif;font-size:26px;font-weight:800;
  color:var(--maroon);letter-spacing:1.5px;margin-bottom:28px;
  display:flex;align-items:center;gap:14px;
}
.t-page-title::after{
  content:'';flex:1;height:2px;max-width:160px;
  background:linear-gradient(90deg,var(--gold),transparent);border-radius:2px;
}

/* Section header — dùng chung */
.section-header{display:flex;align-items:center;gap:10px;margin-bottom:16px;margin-top:28px}
.section-badge{width:8px;height:8px;background:var(--gold);border-radius:50%;box-shadow:0 0 0 3px rgba(212,175,55,.28);flex-shrink:0}
.section-header h3{font-family:'Playfair Display',serif;font-size:15.5px;font-weight:700;color:var(--maroon);letter-spacing:.8px;text-transform:uppercase}
.divider{height:1px;background:linear-gradient(90deg,transparent,var(--border),transparent);margin:6px 0 24px}

/* ══ LOADING / ERROR ══ */
.t-loading{text-align:center;padding:60px 20px;color:var(--text-muted)}
.t-loading .emoji{font-size:2.2rem;margin-bottom:12px}
.t-loading p{font-weight:600;font-size:14px}
.t-error{background:#ffebee;border:1.5px solid #ef9a9a;border-radius:var(--radius-md);padding:16px 20px;color:#c62828;font-weight:700}

/* ══ RESPONSIVE ══ */
@media(max-width:900px){
  .t-sidebar{width:220px}
  .t-main{padding:24px 20px}
}
</style>
</head>
<body>


<div class="t-frame">

  <!-- ══ SIDEBAR ══ -->
  <aside class="t-sidebar">

    <!-- Brand -->
    <div class="t-brand">
      <div class="t-brand-name">SỬ VIỆT</div>
      <div class="t-brand-role">Bảng điều khiển Giáo viên</div>
      <div class="t-brand-user">
        <span class="material-symbols-outlined">school</span>
        <span><?= htmlspecialchars($me['username']) ?></span>
      </div>
    </div>

    <!-- Nav -->
    <nav class="t-nav">
      <div class="t-nav-section">Chức năng chính</div>
      <?php foreach ($navItems as $key => $item): ?>
        <a href="?mod=<?= $key ?>" class="<?= $mod === $key ? 'active' : '' ?>">
          <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
          <?= $item['label'] ?>
          <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
            <span class="nav-badge"><?= $item['badge'] ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Footer -->
    <div class="t-sidebar-footer">
      <a href="../login.html">
        <span class="material-symbols-outlined">logout</span>
        Đăng xuất
      </a>
    </div>

  </aside>

  <!-- ══ MAIN ══ -->
  <main class="t-main">
    <?php
    if (file_exists($moduleFile)) {
        include $moduleFile;
    } else {
        echo '<div class="t-loading">'
           . '<div class="emoji">🔧</div>'
           . '<p>Module <code>t_'.$mod.'.php</code> đang được xây dựng.</p>'
           . '</div>';
    }
    ?>
  </main>

</div><!-- /.t-frame -->
</body>
</html>