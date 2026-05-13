<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Ghi Danh Sĩ Tử – Sử Việt</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Crimson+Pro:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet"/>
<style>
:root {
  --red:        #800000;
  --red-deep:   #5a0000;
  --gold:       #BF9B30;
  --gold-light: #F9F1B7;
  --gold-dim:   rgba(191,155,48,0.25);
  --bg:         #fdfcf0;
  --tx:         #2d0a0a;
  --tx-muted:   #6b3a3a;
  --border:     rgba(128,0,0,0.11);
  --ff:         'Be Vietnam Pro', sans-serif;
  --serif:      'Playfair Display', serif;
  --serif2:     'Crimson Pro', serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}

body {
  font-family: var(--ff);
  background: var(--bg);
  color: var(--tx);
  min-height: 100vh;
  padding: 0;
  overflow-x: hidden;
  position: relative;
}

/* ── Trang trí nền ── */
.bg-deco {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
}
/* Gradient mesh */
.bg-deco::before {
  content: ''; position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 50% 70% at 95% 10%, rgba(191,155,48,0.10) 0%, transparent 55%),
    radial-gradient(ellipse 40% 55% at 2%  85%, rgba(128,0,0,0.06)   0%, transparent 50%),
    radial-gradient(ellipse 60% 40% at 50% 50%, rgba(191,155,48,0.04) 0%, transparent 60%);
}
/* Diagonal lines pattern */
.bg-deco::after {
  content: ''; position: absolute; inset: 0; opacity: 0.018;
  background-image:
    repeating-linear-gradient(45deg, #800000 0, #800000 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
}

/* Ngôi sao trang trí góc phải trên */
.deco-star-tr {
  position: fixed; top: -60px; right: -60px;
  width: 320px; height: 320px;
  opacity: 0.045; pointer-events: none; z-index: 0;
}
/* Ngôi sao trang trí góc trái dưới */
.deco-star-bl {
  position: fixed; bottom: -80px; left: -80px;
  width: 280px; height: 280px;
  opacity: 0.03; pointer-events: none; z-index: 0;
}
/* Đường kẻ vàng dọc trái */
.deco-line-l {
  position: fixed; top: 0; left: 40px; bottom: 0;
  width: 1px; z-index: 0; pointer-events: none;
  background: linear-gradient(to bottom, transparent 0%, rgba(191,155,48,0.2) 20%, rgba(191,155,48,0.2) 80%, transparent 100%);
}
.deco-line-r {
  position: fixed; top: 0; right: 40px; bottom: 0;
  width: 1px; z-index: 0; pointer-events: none;
  background: linear-gradient(to bottom, transparent 0%, rgba(191,155,48,0.12) 20%, rgba(191,155,48,0.12) 80%, transparent 100%);
}

/* ── PAGE WRAPPER ── */
.page-wrap {
  position: relative; z-index: 2;
  min-height: 100vh;
  display: flex; flex-direction: column;
  align-items: center;
  padding: 48px 2rem 64px;
}

/* ── PAGE HEADER ── */
.page-header {
  text-align: center; margin-bottom: 36px;
  animation: riseUp 0.7s cubic-bezier(0.16,1,0.3,1) both;
}
.ph-line {
  display: flex; align-items: center; justify-content: center; gap: 14px;
  margin-bottom: 14px;
}
.ph-ornament {
  width: 48px; height: 1px;
  background: linear-gradient(to right, transparent, var(--gold));
}
.ph-ornament.flip { background: linear-gradient(to left, transparent, var(--gold)); }
.ph-tag {
  font-size: 0.68rem; font-weight: 800; letter-spacing: 4px;
  color: var(--gold); text-transform: uppercase;
}
.ph-title {
  font-family: var(--serif); font-size: clamp(1.9rem, 3.5vw, 2.6rem);
  font-weight: 900; color: var(--red); line-height: 1.1; margin-bottom: 8px;
}
.ph-sub {
  font-family: var(--serif2); font-size: 0.95rem;
  color: var(--tx-muted); line-height: 1.65;
}

/* ── 2-COLUMN FORM LAYOUT ── */
.form-outer {
  width: 100%; max-width: 1060px;
  animation: riseUp 0.7s 0.08s cubic-bezier(0.16,1,0.3,1) both;
}

/* Avatar row above columns */
.avatar-row {
  display: flex; align-items: center; gap: 22px;
  background: white;
  border: 1.5px solid rgba(191,155,48,0.18);
  border-radius: 16px;
  padding: 22px 28px; margin-bottom: 18px;
  box-shadow: 0 4px 18px rgba(128,0,0,0.06);
  position: relative; overflow: hidden;
}
.avatar-row::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(to right, var(--red-deep), var(--red), var(--gold));
}
.avatar-circle {
  width: 76px; height: 76px; border-radius: 50%; flex-shrink: 0;
  border: 2px solid rgba(191,155,48,0.3); background: var(--bg);
  overflow: hidden; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: border-color 0.25s, box-shadow 0.25s;
  box-shadow: 0 2px 10px rgba(128,0,0,0.08);
  position: relative;
}
.avatar-placeholder {
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  pointer-events: none;
}
.avatar-placeholder svg { width: 28px; height: 28px; opacity: 0.28; }
.avatar-circle:hover { border-color: var(--gold); box-shadow: 0 4px 18px rgba(128,0,0,0.14); }
.avatar-circle img { position: absolute; inset: 0; width:100%; height:100%; object-fit:cover; display:none; z-index:1; }
.avatar-info { flex: 1; }
.avatar-info-title { font-size: 0.9rem; font-weight: 700; color: var(--tx); margin-bottom: 3px; }
.avatar-info-sub { font-family: var(--serif2); font-size: 0.82rem; color: var(--tx-muted); font-style: italic; margin-bottom: 12px; }
.avatar-info label {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 9px;
  border: 1.5px solid var(--border); background: var(--bg);
  font-size: 0.82rem; font-weight: 700; color: var(--tx-muted);
  cursor: pointer; transition: all 0.2s;
}
.avatar-info label:hover { border-color: var(--gold); color: var(--red); background: rgba(191,155,48,0.05); }
.avatar-input { display: none; }

/* Columns container */
.cols {
  display: grid; grid-template-columns: 1fr 1fr; gap: 18px;
  align-items: start;
}

/* Each column card */
.col-card {
  background: white;
  border: 1.5px solid rgba(191,155,48,0.16);
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(128,0,0,0.06);
  transition: border-color 0.3s;
}
.col-card:hover { border-color: rgba(191,155,48,0.32); }

/* Top gradient accent */
.col-card::before {
  content: ''; display: block; height: 3px;
  background: linear-gradient(to right, var(--red-deep), var(--red), var(--gold));
  opacity: 0;
  transform: scaleX(0); transform-origin: left;
  transition: opacity 0.3s, transform 0.4s;
}
.col-card:hover::before { opacity: 1; transform: scaleX(1); }

.col-head {
  padding: 22px 26px 18px;
  border-bottom: 1px solid rgba(191,155,48,0.1);
  background: linear-gradient(to bottom, rgba(128,0,0,0.025), transparent);
  display: flex; align-items: baseline; gap: 12px;
}
.col-letter {
  font-family: var(--serif); font-size: 2.4rem; font-weight: 900;
  color: rgba(128,0,0,0.09); line-height: 1; flex-shrink: 0;
}
.col-head-text {}
.col-tag {
  font-size: 0.65rem; font-weight: 800; letter-spacing: 2px;
  color: var(--gold); text-transform: uppercase; display: block; margin-bottom: 3px;
}
.col-title {
  font-family: var(--serif); font-size: 1.15rem;
  color: var(--red); font-weight: 700; line-height: 1.2;
}

.col-body { padding: 24px 26px 28px; }

/* Field spacing */
.f-group { margin-bottom: 18px; }
.f-group:last-child { margin-bottom: 0; }

.f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
.f-row:last-child { margin-bottom: 0; }

.fld { display: flex; flex-direction: column; gap: 7px; }
.fld-label {
  font-size: 0.8rem; font-weight: 700;
  color: var(--tx); letter-spacing: 0; text-transform: none; line-height: 1.3;
}
.fld-label .req { color: var(--red); margin-left: 2px; }
.fld-hint { font-size: 0.69rem; color: rgba(107,58,58,0.5); margin-top: 0px; font-family: var(--serif2); font-style: italic; }

/* Inputs */
input[type="text"],
input[type="number"],
input[type="date"],
select {
  width: 100%; padding: 11px 14px;
  background: var(--bg); border: 1.5px solid var(--border);
  border-radius: 9px; color: var(--tx);
  font-family: var(--ff); font-size: 0.9rem;
  outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  -webkit-appearance: none; appearance: none;
}
input::placeholder { color: rgba(107,58,58,0.28); font-size: 0.87rem; }
input:focus, select:focus {
  border-color: var(--gold); background: white;
  box-shadow: 0 0 0 3px var(--gold-dim);
}
select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23BF9B30' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 11px center;
  background-color: var(--bg); padding-right: 28px; cursor: pointer;
}

/* Chips */
.chip-group { display: flex; flex-wrap: wrap; gap: 7px; }
.chip-label { cursor: pointer; }
.chip-label input { position: absolute; opacity: 0; pointer-events: none; }
.chip {
  display: inline-block; padding: 7px 14px; border-radius: 8px;
  border: 1.5px solid var(--border); background: var(--bg);
  font-size: 0.82rem; font-weight: 600; color: var(--tx-muted);
  transition: all 0.16s; user-select: none; line-height: 1.3;
}
.chip-label:hover .chip { border-color: rgba(191,155,48,0.4); color: var(--tx); }
.chip-label input:checked + .chip {
  background: rgba(128,0,0,0.07); border-color: var(--red); color: var(--red); font-weight: 700;
}
.chip-label.gold input:checked + .chip {
  background: rgba(191,155,48,0.1); border-color: var(--gold); color: var(--red-deep);
}

/* Star rating */
.star-row { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
.star-btn {
  width: 30px; height: 30px; border-radius: 6px;
  border: 1.5px solid var(--border); background: var(--bg);
  color: rgba(107,58,58,0.3); font-size: 0.9rem;
  cursor: pointer; transition: all 0.16s;
  display: flex; align-items: center; justify-content: center;
}
.star-btn:hover, .star-btn.on {
  border-color: var(--gold); background: rgba(191,155,48,0.1); color: var(--gold);
}
.star-note { font-size: 0.67rem; color: var(--tx-muted); }
#starNote { font-size: 0.68rem; color: var(--gold); font-weight: 600; font-style: italic; margin-left: 4px; }

/* Range */
.range-num {
  font-family: var(--serif); font-size: 1.5rem; font-weight: 900;
  color: var(--red); line-height: 1; margin-bottom: 5px; display: block;
}
input[type="range"] {
  width: 100%; height: 3px; padding: 0; border: none;
  background: rgba(128,0,0,0.1); border-radius: 2px;
  -webkit-appearance: none; cursor: pointer;
}
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none; width: 15px; height: 15px; border-radius: 50%;
  background: var(--red); box-shadow: 0 2px 6px rgba(128,0,0,0.3); cursor: pointer;
}
.range-ends { display: flex; justify-content: space-between; margin-top: 3px; }
.range-ends span { font-size: 0.6rem; color: var(--tx-muted); font-weight: 600; }

/* Divider inside col */
.inner-divider {
  height: 1px; margin: 16px 0;
  background: linear-gradient(to right, rgba(191,155,48,0.18), transparent);
}

/* Tip box */
.tip-box {
  background: rgba(191,155,48,0.06); border: 1.5px solid var(--gold-dim);
  border-radius: 10px; padding: 12px 16px; margin-top: 16px;
}
.tip-box p {
  font-family: var(--serif2); font-size: 0.83rem; color: var(--tx-muted); line-height: 1.65;
}
.tip-box strong { color: var(--red); }

/* ── SUBMIT AREA ── */
.submit-area {
  margin-top: 18px;
  display: grid; grid-template-columns: 1fr 1fr; gap: 18px; align-items: center;
}
.submit-note {
  font-family: var(--serif2); font-size: 0.85rem; color: var(--tx-muted); line-height: 1.65;
}
.submit-note strong { color: var(--red); }
.btn-submit {
  width: 100%; padding: 14px; border-radius: 12px; border: none;
  background: linear-gradient(135deg, var(--red-deep) 0%, var(--red) 100%);
  color: white; font-family: var(--ff);
  font-weight: 800; font-size: 0.95rem; letter-spacing: 0.3px;
  cursor: pointer; position: relative; overflow: hidden;
  box-shadow: 0 6px 22px rgba(128,0,0,0.28); transition: all 0.3s;
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(128,0,0,0.38); }
.btn-submit::after {
  content: ''; position: absolute; top: 0; left: -80%; width: 50%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
  transform: skewX(-15deg); transition: left 0.6s;
}
.btn-submit:hover::after { left: 130%; }

/* ── SUCCESS OVERLAY ── */
.success-overlay {
  display: none; position: fixed; inset: 0; z-index: 1000;
  background: rgba(45,10,10,0.7); backdrop-filter: blur(8px);
  justify-content: center; align-items: center;
}
.success-overlay.show { display: flex; }
.success-box {
  background: white; border-radius: 20px;
  border: 1.5px solid rgba(191,155,48,0.25);
  padding: 44px 52px; text-align: center; max-width: 360px;
  box-shadow: 0 20px 60px rgba(128,0,0,0.18);
  animation: riseUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
}
.suc-ornament {
  font-family: var(--serif); font-size: 1.8rem; font-weight: 900;
  color: var(--gold); display: block; margin-bottom: 14px;
  animation: fadeUp 0.5s 0.15s ease both; opacity: 0;
}
.suc-title { font-family: var(--serif); font-size: 1.6rem; color: var(--red); margin-bottom: 10px; }
.suc-sub { font-family: var(--serif2); font-size: 0.92rem; color: var(--tx-muted); line-height: 1.7; }
.suc-btn {
  display: inline-block; margin-top: 22px;
  padding: 11px 30px; border-radius: 10px; border: none;
  background: var(--gold); color: var(--red-deep);
  font-family: var(--ff); font-weight: 800; font-size: 0.88rem;
  cursor: pointer; transition: 0.25s; box-shadow: 0 4px 14px rgba(191,155,48,0.3);
}
.suc-btn:hover { background: var(--gold-light); }

/* ── KEYFRAMES ── */
@keyframes riseUp {
  from { opacity:0; transform:translateY(28px) scale(0.98); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}
@keyframes fadeUp {
  from { opacity:0; transform:translateY(10px); }
  to   { opacity:1; transform:translateY(0); }
}

/* ── RESPONSIVE ── */
@media(max-width:860px){
  .cols { grid-template-columns: 1fr; }
  .submit-area { grid-template-columns: 1fr; }
  .submit-note { order: 2; }
}
@media(max-width:560px){
  .page-wrap { padding: 32px 1rem 48px; }
  .col-head, .col-body { padding-left: 18px; padding-right: 18px; }
  .avatar-row { padding: 14px 18px; }
  .f-row { grid-template-columns: 1fr; }
  .deco-line-l, .deco-line-r { display: none; }
}
</style>
</head>
<body>

<!-- Trang trí nền -->
<div class="bg-deco"></div>
<div class="deco-line-l"></div>
<div class="deco-line-r"></div>

<!-- Ngôi sao góc phải trên -->
<svg class="deco-star-tr" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
  <polygon fill="#BF9B30" points="250,25 298,170 450,170 328,260 375,405 250,315 125,405 172,260 50,170 202,170"/>
  <polygon fill="none" stroke="#800000" stroke-width="2"
    points="250,65 291,188 420,188 322,256 358,380 250,312 142,380 178,256 80,188 209,188"/>
</svg>

<!-- Ngôi sao góc trái dưới -->
<svg class="deco-star-bl" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
  <polygon fill="#800000" points="250,25 298,170 450,170 328,260 375,405 250,315 125,405 172,260 50,170 202,170"/>
</svg>

<div class="page-wrap">

  <!-- Header -->
  <div class="page-header">
    <div class="ph-line">
      <span class="ph-ornament"></span>
      <span class="ph-tag">Sử Việt · Lịch sử Lớp 12</span>
      <span class="ph-ornament flip"></span>
    </div>
    <h1 class="ph-title">Hồ Sơ Học Sinh</h1>
    <p class="ph-sub">Hoàn thành một lần để AI xây dựng lộ trình học tập phù hợp với năng lực của bạn.</p>
  </div>

  <div class="form-outer">
  <form action="save_profile.php" method="POST" enctype="multipart/form-data" id="profileForm">

    <!-- Avatar -->
    <div class="avatar-row">
      <label for="avatarFile" class="avatar-circle" id="avatarCircle">
        <img id="avatarImg" src="" alt=""/>
        <div class="avatar-placeholder">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="8" r="4" stroke="#6b3a3a" stroke-width="1.5"/>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="#6b3a3a" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
      </label>
      <div class="avatar-info">
        <div class="avatar-info-title">Ảnh đại diện</div>
        <div class="avatar-info-sub">Hình ảnh hiển thị trên trang học sinh của bạn</div>
        <label for="avatarFile">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Tải ảnh lên
        </label>
      </div>
      <input type="file" id="avatarFile" name="avatar" accept="image/*" class="avatar-input"/>
    </div>

    <!-- 2 columns -->
    <div class="cols">

      <!-- ═══ CỘT TRÁI: A + B ═══ -->
      <div>

        <!-- CARD A -->
        <div class="col-card" style="margin-bottom:18px">
          <div class="col-head">
            <span class="col-letter">A</span>
            <div>
              <span class="col-tag">Thông tin cá nhân</span>
              <div class="col-title">Hồ sơ cơ bản</div>
            </div>
          </div>
          <div class="col-body">

            <div class="f-group fld">
              <label class="fld-label">Họ và tên <span class="req">*</span></label>
              <input type="text" name="fullname" placeholder="Nhập họ tên đầy đủ" required/>
            </div>

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Giới tính</label>
                <div class="chip-group">
                  <label class="chip-label"><input type="radio" name="gender" value="Nam" checked/><span class="chip">Nam</span></label>
                  <label class="chip-label"><input type="radio" name="gender" value="Nữ"/><span class="chip">Nữ</span></label>
                  <label class="chip-label"><input type="radio" name="gender" value="Khác"/><span class="chip">Khác</span></label>
                </div>
              </div>
              <div class="fld">
                <label class="fld-label">Ngày sinh</label>
                <input type="date" name="birthday"/>
              </div>
            </div>

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Trường học</label>
                <input type="text" name="school" placeholder="Tên trường THPT"/>
              </div>
              <div class="fld">
                <label class="fld-label">Lớp</label>
                <select name="grade">
                  <option value="">Chọn lớp</option>
                  <option>12A</option><option>12B</option><option>12C</option>
                  <option>12D</option><option>12E</option><option>Lớp khác</option>
                </select>
              </div>
            </div>

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Tỉnh / Thành phố</label>
                <select name="city">
                  <option value="">Chọn tỉnh/thành</option>
                  <option>Hà Nội</option><option>TP. Hồ Chí Minh</option>
                  <option>Đà Nẵng</option><option>Hải Phòng</option>
                  <option>Cần Thơ</option><option>Nghệ An</option>
                  <option>Thanh Hóa</option><option>Khánh Hòa</option>
                  <option>Bình Dương</option><option>Đồng Nai</option>
                  <option>Thừa Thiên Huế</option><option>Quảng Nam</option>
                  <option>Lâm Đồng</option><option>Tỉnh/thành khác</option>
                </select>
              </div>
              <div class="fld">
                <label class="fld-label">Mục tiêu điểm thi</label>
                <div class="chip-group">
                  <label class="chip-label gold"><input type="radio" name="target_score_init" value="7+"/><span class="chip">7+</span></label>
                  <label class="chip-label gold"><input type="radio" name="target_score_init" value="8+" checked/><span class="chip">8+</span></label>
                  <label class="chip-label gold"><input type="radio" name="target_score_init" value="9+"/><span class="chip">9+</span></label>
                  <label class="chip-label gold"><input type="radio" name="target_score_init" value="10"/><span class="chip">10</span></label>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD B -->
        <div class="col-card">
          <div class="col-head">
            <span class="col-letter">B</span>
            <div>
              <span class="col-tag">Năng lực ban đầu</span>
              <div class="col-title">Điểm xuất phát</div>
            </div>
          </div>
          <div class="col-body">

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Điểm TB Lịch sử lớp 11</label>
                <input type="number" name="avg_history_11" step="0.1" min="0" max="10" placeholder="VD: 7.5"/>
                <span class="fld-hint">Điểm trung bình cả năm, thang 10</span>
              </div>
              <div class="fld">
                <label class="fld-label">Điểm thi gần nhất</label>
                <input type="number" name="last_test_score" step="0.1" min="0" max="10" placeholder="VD: 6.0"/>
                <span class="fld-hint">Kiểm tra hoặc thi thử gần đây</span>
              </div>
            </div>

            <div class="f-group fld">
              <label class="fld-label">Tự đánh giá năng lực hiện tại</label>
              <div class="chip-group">
                <label class="chip-label"><input type="radio" name="self_level" value="Yếu"/><span class="chip">Yếu</span></label>
                <label class="chip-label"><input type="radio" name="self_level" value="Trung bình" checked/><span class="chip">Trung bình</span></label>
                <label class="chip-label"><input type="radio" name="self_level" value="Khá"/><span class="chip">Khá</span></label>
                <label class="chip-label"><input type="radio" name="self_level" value="Giỏi"/><span class="chip">Giỏi</span></label>
              </div>
            </div>

            <div class="f-group fld">
              <label class="fld-label">Mức độ yêu thích môn Lịch sử <span id="starNote"></span></label>
              <div class="star-row">
                <button type="button" class="star-btn" onclick="setStar(1)">★</button>
                <button type="button" class="star-btn" onclick="setStar(2)">★</button>
                <button type="button" class="star-btn" onclick="setStar(3)">★</button>
                <button type="button" class="star-btn" onclick="setStar(4)">★</button>
                <button type="button" class="star-btn" onclick="setStar(5)">★</button>
                <span class="star-note">1 = Không thích · 5 = Rất yêu thích</span>
              </div>
              <input type="hidden" name="interest_level" id="interestVal" value="3"/>
            </div>

            <div class="f-group fld">
              <label class="fld-label">Khó khăn khi học Lịch sử <span style="font-size:0.63rem;color:var(--tx-muted);font-weight:400;text-transform:none">(chọn tất cả phù hợp)</span></label>
              <div class="chip-group">
                <label class="chip-label"><input type="checkbox" name="difficulties[]" value="khó nhớ"/><span class="chip">Khó nhớ ngày tháng, sự kiện</span></label>
                <label class="chip-label"><input type="checkbox" name="difficulties[]" value="khó hiểu"/><span class="chip">Khó hiểu bối cảnh lịch sử</span></label>
                <label class="chip-label"><input type="checkbox" name="difficulties[]" value="chán"/><span class="chip">Nội dung khô khan, thiếu hứng</span></label>
                <label class="chip-label"><input type="checkbox" name="difficulties[]" value="thiếu tài liệu"/><span class="chip">Thiếu tài liệu ôn tập</span></label>
                <label class="chip-label"><input type="checkbox" name="difficulties[]" value="thiếu thời gian"/><span class="chip">Thiếu thời gian học</span></label>
              </div>
            </div>

          </div>
        </div>

      </div><!-- /col trái -->

      <!-- ═══ CỘT PHẢI: C + D ═══ -->
      <div>

        <!-- CARD C -->
        <div class="col-card" style="margin-bottom:18px">
          <div class="col-head">
            <span class="col-letter">C</span>
            <div>
              <span class="col-tag">Thói quen học tập</span>
              <div class="col-title">Lịch trình & Phương pháp</div>
            </div>
          </div>
          <div class="col-body">

            <div class="f-group fld">
              <label class="fld-label">Số buổi học Lịch sử mỗi tuần</label>
              <span class="range-num" id="sessionsDisplay">3 buổi</span>
              <input type="range" name="study_sessions_per_week" min="1" max="14" value="3"
                oninput="document.getElementById('sessionsDisplay').textContent=this.value+' buổi'"/>
              <div class="range-ends"><span>1</span><span>7</span><span>14</span></div>
            </div>

            <div class="inner-divider"></div>

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Thời gian mỗi buổi</label>
                <div class="chip-group" style="flex-direction:column;gap:5px">
                  <label class="chip-label"><input type="radio" name="study_time_per_session" value="<30"/><span class="chip" style="width:100%">Dưới 30 phút</span></label>
                  <label class="chip-label"><input type="radio" name="study_time_per_session" value="30-60" checked/><span class="chip" style="width:100%">30 – 60 phút</span></label>
                  <label class="chip-label"><input type="radio" name="study_time_per_session" value=">60"/><span class="chip" style="width:100%">Trên 60 phút</span></label>
                </div>
              </div>
              <div class="fld">
                <label class="fld-label">Thời điểm học</label>
                <div class="chip-group" style="flex-direction:column;gap:5px">
                  <label class="chip-label"><input type="radio" name="study_time_of_day" value="Sáng"/><span class="chip" style="width:100%">Buổi sáng</span></label>
                  <label class="chip-label"><input type="radio" name="study_time_of_day" value="Chiều" checked/><span class="chip" style="width:100%">Buổi chiều</span></label>
                  <label class="chip-label"><input type="radio" name="study_time_of_day" value="Tối"/><span class="chip" style="width:100%">Buổi tối</span></label>
                  <label class="chip-label"><input type="radio" name="study_time_of_day" value="Khuya"/><span class="chip" style="width:100%">Khuya</span></label>
                </div>
              </div>
            </div>

            <div class="inner-divider"></div>

            <div class="f-group fld">
              <label class="fld-label">Phương pháp học đang sử dụng <span style="font-size:0.63rem;color:var(--tx-muted);font-weight:400;text-transform:none">(có thể chọn nhiều)</span></label>
              <div class="chip-group">
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Đọc sách"/><span class="chip">Đọc sách giáo khoa</span></label>
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Trắc nghiệm"/><span class="chip">Làm bài trắc nghiệm</span></label>
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Video"/><span class="chip">Xem video bài giảng</span></label>
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Học nhóm"/><span class="chip">Học nhóm</span></label>
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Sơ đồ"/><span class="chip">Sơ đồ tư duy</span></label>
                <label class="chip-label"><input type="checkbox" name="study_methods[]" value="Tóm tắt"/><span class="chip">Ghi chú, tóm tắt</span></label>
              </div>
            </div>

            <div class="inner-divider"></div>

            <div class="f-group fld">
              <label class="fld-label">Có lập kế hoạch học tập không?</label>
              <div class="chip-group">
                <label class="chip-label gold"><input type="radio" name="has_study_plan" value="Có" checked/><span class="chip">Có kế hoạch</span></label>
                <label class="chip-label"><input type="radio" name="has_study_plan" value="Không"/><span class="chip">Chưa có</span></label>
              </div>
            </div>

          </div>
        </div>

        <!-- CARD D -->
        <div class="col-card">
          <div class="col-head">
            <span class="col-letter">D</span>
            <div>
              <span class="col-tag">Mục tiêu cá nhân</span>
              <div class="col-title">Đích đến & Kỳ vọng</div>
            </div>
          </div>
          <div class="col-body">

            <div class="f-row">
              <div class="fld">
                <label class="fld-label">Điểm mục tiêu cụ thể <span class="req">*</span></label>
                <input type="number" name="target_score" step="0.1" min="1" max="10" placeholder="VD: 8.5" required/>
                <span class="fld-hint">Điểm thi THPT Quốc gia muốn đạt</span>
              </div>
              <div class="fld">
                <label class="fld-label">Thời gian đạt mục tiêu</label>
                <select name="target_time_frame">
                  <option value="1 tháng">1 tháng</option>
                  <option value="2 tháng">2 tháng</option>
                  <option value="3 tháng" selected>3 tháng</option>
                  <option value="6 tháng">6 tháng</option>
                  <option value="Đến kỳ thi">Đến kỳ thi</option>
                </select>
              </div>
            </div>

            <div class="f-group fld">
              <label class="fld-label">Mục tiêu bạn hướng đến <span style="font-size:0.63rem;color:var(--tx-muted);font-weight:400;text-transform:none">(có thể chọn nhiều)</span></label>
              <div class="chip-group">
                <label class="chip-label gold"><input type="checkbox" name="specific_goals[]" value="Đỗ tốt nghiệp"/><span class="chip">Đỗ tốt nghiệp THPT</span></label>
                <label class="chip-label gold"><input type="checkbox" name="specific_goals[]" value="Thi đại học"/><span class="chip">Xét tuyển đại học</span></label>
                <label class="chip-label gold"><input type="checkbox" name="specific_goals[]" value="Cải thiện điểm"/><span class="chip">Cải thiện điểm số</span></label>
                <label class="chip-label gold"><input type="checkbox" name="specific_goals[]" value="Học bổng"/><span class="chip">Đạt học bổng</span></label>
                <label class="chip-label gold"><input type="checkbox" name="specific_goals[]" value="Hiểu sâu"/><span class="chip">Hiểu sâu môn Lịch sử</span></label>
              </div>
            </div>

            <div class="tip-box">
              <p>Thông tin này giúp AI Sử Việt xây dựng <strong>lộ trình học tập cá nhân hóa</strong> và theo dõi tiến độ của bạn. Không có câu trả lời đúng hay sai — hãy trả lời thật lòng để đạt hiệu quả tốt nhất.</p>
            </div>

          </div>
        </div>

      </div><!-- /col phải -->

    </div><!-- /cols -->

    <!-- Submit -->
    <div class="submit-area">
      <p class="submit-note">Bằng cách ghi danh, bạn đồng ý để Sử Việt sử dụng thông tin này nhằm mục đích <strong>cá nhân hóa trải nghiệm học tập</strong> của bạn.</p>
      <button type="submit" class="btn-submit">Hoàn tất và bắt đầu học</button>
    </div>

  </form>
  </div><!-- /form-outer -->

</div><!-- /page-wrap -->

<!-- Success overlay -->
<div class="success-overlay" id="successOverlay">
  <div class="success-box">
    <span class="suc-ornament">✦</span>
    <h2 class="suc-title">Ghi danh thành công</h2>
    <p class="suc-sub">Hồ sơ đã được lưu.<br/>AI Sử Việt đang xây dựng<br/>lộ trình học tập phù hợp cho bạn.</p>
    <button class="suc-btn" onclick="window.location.href='../html/trangchu.html'">Vào học ngay</button>
  </div>
</div>

<script>
/* Avatar */
document.getElementById('avatarFile').addEventListener('change', function() {
  const file = this.files[0]; if (!file) return;
  const img = document.getElementById('avatarImg');
  img.src = URL.createObjectURL(file);
  img.style.display = 'block';
  const ph = document.querySelector('.avatar-placeholder');
  if (ph) ph.style.display = 'none';
});

/* Stars */
const sLabels = ['','Không thích','Ít thích','Bình thường','Khá thích','Rất yêu thích'];
let sCur = 3; setStar(3);
function setStar(n) {
  sCur = n;
  document.getElementById('interestVal').value = n;
  document.getElementById('starNote').textContent = '— ' + sLabels[n];
  document.querySelectorAll('.star-btn').forEach((b,i) => b.classList.toggle('on', i < n));
}
</script>
</body>
</html>