<?php
/**
 * FILE KHUNG QUẢN TRỊ CHÍNH — SỬ VIỆT TOÀN THƯ
 * Tập trung: Vận hành, Tài chính & Hệ thống
 */
session_start();
require_once '../../php/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

try {
    $db = getDB();
} catch (Exception $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$modules = [
    'dashboard' => 'ad_main.php',
    'users'     => 'ad_users.php',
    'teachers'  => 'ad_teachers.php',
    'pricing'   => 'ad_pricing.php',
    'settings'  => 'ad_system.php',
];

$menu = [
    'dashboard' => ['icon' => 'payments',             'label' => 'Tổng quan hệ thống'],
    'users'     => ['icon' => 'group_add',            'label' => 'Duyệt VIP & Sĩ tử'],
    'teachers'  => ['icon' => 'assignment_ind',       'label' => 'Đội ngũ Giáo viên'],
    'pricing'   => ['icon' => 'sell',                 'label' => 'Gói học & Học phí'],
    'settings'  => ['icon' => 'admin_panel_settings', 'label' => 'Hệ thống & Cài đặt'],
];

$view_file = isset($modules[$page]) ? $modules[$page] : $modules['dashboard'];

$page_titles = [
    'dashboard' => ['Trung tâm vận hành',  'Admin · Doanh thu'],
    'users'     => ['Quản lý người dùng',  'Admin · Tài khoản'],
    'teachers'  => ['Đội ngũ Mentor',      'Admin · Giáo viên'],
    'pricing'   => ['Cấu hình thương mại', 'Admin · Tài chính'],
    'settings'  => ['Quản trị hệ thống',   'Admin · Cài đặt'],
];
$cur_title = $page_titles[$page] ?? ['Quản trị', 'Admin Panel'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $cur_title[1] ?> · Sử Việt Toàn Thư</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
  <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

  <!-- ══════════ SIDEBAR ══════════ -->
  <div class="sidebar">
    <a href="admin_dashboard.php" class="nav-logo">
      <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" width="42" height="42">
        <defs>
          <linearGradient id="gLogo" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#BF9B30"/>
            <stop offset="50%" stop-color="#F9F1B7"/>
            <stop offset="100%" stop-color="#BF9B30"/>
          </linearGradient>
        </defs>
        <path d="M42,35 L33,38 L33,72 L42,68 Z" fill="url(#gLogo)"/>
        <path d="M45,35 L36,38 L36,75 L45,71 Z" fill="url(#gLogo)" opacity=".8"/>
        <path d="M48,35 C55,55 58,65 60,78 C65,60 75,40 88,22 L84,19 C72,37 62,57 58,72 C56,62 52,45 48,35Z" fill="url(#gLogo)"/>
        <polygon points="88,16 90,21 96,21 91,25 93,31 88,27 83,31 85,25 80,21 86,21" fill="url(#gLogo)"/>
      </svg>
      <div class="logo-text-wrap">
        <h2 class="logo-text">Sử<em>Việt</em></h2>
        <div class="brand-sub">Admin Panel</div>
      </div>
    </a>

    <div class="sidebar-section-label">Quản trị</div>
    <ul>
      <?php foreach ($menu as $key => $item): ?>
      <li class="<?= $page === $key ? 'active' : '' ?>">
        <a href="?page=<?= $key ?>">
          <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
          <?= $item['label'] ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>

    <div class="sidebar-footer">
      <div class="sidebar-admin-info">
        <div class="sidebar-admin-avatar">A</div>
        <div>
          <div class="sidebar-admin-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Quản trị viên') ?></div>
          <div class="sidebar-admin-role">Administrator</div>
        </div>
      </div>
      <a href="../login.html" class="sidebar-logout">
        <span class="material-symbols-outlined" style="font-size:16px;">logout</span>
        Đăng xuất
      </a>
    </div>
  </div>

  <!-- ══════════ MAIN CONTENT ══════════ -->
  <div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-left">
        <h1><?= $cur_title[0] ?></h1>
        <div class="topbar-breadcrumb">Sử Việt Toàn Thư &rsaquo; Admin &rsaquo; <?= $cur_title[0] ?></div>
      </div>
      <div class="topbar-right">
        <span class="topbar-chip">
          <span class="dot"></span>
          Hệ thống online
        </span>
      </div>
    </div>

    <!-- Nội dung động -->
    <div class="content-wrapper">
      <?php
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<div class="content-card">'
              . '<div class="content-card-header">'
              . '<div class="content-card-title"><span class="material-symbols-outlined">construction</span>Module Đang Xây Dựng</div>'
              . '</div>'
              . '<div class="content-card-body">'
              . '<p style="color:var(--text-muted);font-size:14px;line-height:1.7;">'
              . 'Module <strong>' . htmlspecialchars($page) . '</strong> chưa có nội dung.<br>'
              . 'Thiếu file: <code style="background:var(--cream);padding:2px 8px;border-radius:6px;">' . htmlspecialchars($view_file) . '</code><br>'
              . 'Vui lòng yêu cầu thành viên phụ trách bàn giao file đúng tên.'
              . '</p>'
              . '</div></div>';
        }
      ?>
    </div>

  </div>

 <script src="../../js/admin.js"></script>
</body>
</html>