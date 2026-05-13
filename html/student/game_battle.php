<?php
/**
 * GAME_BATTLE.PHP — SỬ VIỆT TOÀN THƯ
 * Phong cách: Maroon + Gold · Nền tối · Sạch, dễ đọc
 */
session_start();
require_once '../../php/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Sĩ tử');
$opp_name = htmlspecialchars($_GET['opp'] ?? 'Đối thủ');
$opp_ava  = htmlspecialchars($_GET['ava']  ?? '👤');

/* ══ PARSE nhap.js ══ */
function loadQuestions() {
    $paths = [
        __DIR__ . '/../../js/nhap.js',
        __DIR__ . '/../js/nhap.js',
        __DIR__ . '/nhap.js',
    ];
    $js = false;
    foreach ($paths as $p) { if (file_exists($p)) { $js = file_get_contents($p); break; } }
    if (!$js) return [];

    $all = [];
    preg_match_all('/\[\s*"((?:[^"\\\\]|\\\\.)*)"\s*,\s*(\[[^\]]+\])\s*,\s*(\d+)/', $js, $m, PREG_SET_ORDER);
    foreach ($m as $row) {
        $q = stripslashes($row[1]);
        preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"/', $row[2], $am);
        $ans = array_map('stripslashes', $am[1]);
        $c   = (int)$row[3];
        if (count($ans) < 2 || $c >= count($ans) || empty(trim($q))) continue;
        $all[] = ['q' => $q, 'a' => $ans, 'c' => $c];
    }
    // Loại trùng
    $seen = []; $unique = [];
    foreach ($all as $item) {
        $k = md5($item['q']);
        if (!isset($seen[$k])) { $seen[$k] = 1; $unique[] = $item; }
    }
    return $unique;
}

$qs = loadQuestions();

if (count($qs) < 5) {
    $qs = [
        ['q'=>'Sự kiện nào tạo ra thời cơ trực tiếp cho Cách mạng tháng Tám 1945?','a'=>['Nhật đảo chính Pháp','Nhật đầu hàng Đồng minh','Đức đầu hàng','Mỹ ném bom nguyên tử'],'c'=>1],
        ['q'=>'Ngày 2/9/1945 diễn ra sự kiện gì?','a'=>['Tổng khởi nghĩa','Quốc dân Đại hội','Bác Hồ đọc Tuyên ngôn Độc lập','Nhật đảo chính Pháp'],'c'=>2],
        ['q'=>'Chiến dịch Điện Biên Phủ kết thúc năm nào?','a'=>['1950','1953','1954','1955'],'c'=>2],
        ['q'=>'Ai chỉ huy chiến dịch Điện Biên Phủ?','a'=>['Hồ Chí Minh','Võ Nguyên Giáp','Phạm Văn Đồng','Trường Chinh'],'c'=>1],
        ['q'=>'Hiệp định Paris ký năm nào?','a'=>['1968','1972','1973','1975'],'c'=>2],
        ['q'=>'Chiến dịch Hồ Chí Minh kết thúc ngày nào?','a'=>['27/1/1973','7/5/1954','30/4/1975','21/7/1954'],'c'=>2],
        ['q'=>'Đại hội II đổi tên Đảng thành gì?','a'=>['Đảng Cộng sản Đông Dương','Đảng Lao động Việt Nam','Việt Minh','Liên Việt'],'c'=>1],
        ['q'=>'Hiệp định Giơ-ne-vơ ký năm nào?','a'=>['1952','1953','1954','1955'],'c'=>2],
        ['q'=>'Sau 1954 đất nước chia cắt ở vĩ tuyến nào?','a'=>['Vĩ tuyến 16','Vĩ tuyến 17','Vĩ tuyến 18','Vĩ tuyến 19'],'c'=>1],
        ['q'=>'Phong trào Đồng Khởi mạnh nhất ở đâu?','a'=>['Bến Tre','Hà Nội','Huế','Hải Phòng'],'c'=>0],
        ['q'=>'Mặt trận Việt Minh thành lập năm nào?','a'=>['1930','1939','1941','1945'],'c'=>2],
        ['q'=>'Lời kêu gọi Toàn quốc kháng chiến ban hành ngày nào?','a'=>['19/12/1945','19/12/1946','19/12/1947','19/12/1954'],'c'=>1],
        ['q'=>'Thắng lợi nào được gọi là Điện Biên Phủ trên không?','a'=>['Chiến dịch Tây Nguyên','Đánh bại B52 Hà Nội 1972','Chiến dịch Đường 9','Chiến dịch Hồ Chí Minh'],'c'=>1],
        ['q'=>'Chiến thắng nào mở đầu đánh bại Chiến tranh đặc biệt?','a'=>['Ấp Bắc','Điện Biên Phủ','Việt Bắc','Biên giới'],'c'=>0],
        ['q'=>'Cách mạng tháng Tám lập nên nhà nước nào?','a'=>['Việt Nam Dân chủ Cộng hòa','CHXHCN Việt Nam','Đế quốc Việt Nam','Liên bang Đông Dương'],'c'=>0],
        ['q'=>'Lực lượng nào giữ vai trò nòng cốt Cách mạng tháng Tám?','a'=>['Lực lượng vũ trang','Lực lượng chính trị quần chúng','Quân Đồng minh','Tư sản dân tộc'],'c'=>1],
        ['q'=>'Chiến dịch Biên Giới thu đông diễn ra năm nào?','a'=>['1947','1948','1950','1951'],'c'=>2],
        ['q'=>'Kế hoạch quân sự Pháp năm 1953 nhằm kết thúc chiến tranh tên gì?','a'=>['Kế hoạch Bô-la-e','Kế hoạch Rơ-ve','Kế hoạch Đờ Lát','Kế hoạch Na-va'],'c'=>3],
        ['q'=>'Chiến dịch Điện Biên Phủ mở màn ngày nào?','a'=>['13/3/1954','7/5/1954','19/12/1946','20/11/1953'],'c'=>0],
        ['q'=>'Cuộc Tổng tiến công Tết Mậu Thân diễn ra năm nào?','a'=>['1965','1968','1972','1975'],'c'=>1],
    ];
}

shuffle($qs);
$questions_json = json_encode(array_values($qs), JSON_UNESCAPED_UNICODE);
$q_total = count($qs);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>⚔ Đấu Trí · Sử Việt</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
/* ════════════════════════════════════
   VARS — Sử Việt: Maroon + Gold
════════════════════════════════════ */
:root {
  --bg:     #12080a;
  --bg2:    #1c0d10;
  --bg3:    #250f14;
  --maroon: #8b1a2e;
  --mar2:   #6b1222;
  --mar3:   #c0392b;
  --gold:   #c9a227;
  --gold2:  #f0c040;
  --gold3:  #fff3c0;
  --cream:  #fdf6e3;
  --green:  #27ae60;
  --red:    #e74c3c;
  --txt:    #f0e8d8;
  --txt2:   #c8b89a;
  --txt3:   #8a7060;
  --border: rgba(201,162,39,.2);
  --shadow: 0 4px 24px rgba(0,0,0,.5);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  height: 100%; overflow: hidden;
  background: var(--bg);
  color: var(--txt);
  font-family: 'Nunito', sans-serif;
  user-select: none;
}

/* ── subtle texture overlay ── */
body::before {
  content: '';
  position: fixed; inset: 0; pointer-events: none; z-index: 0;
  background:
    radial-gradient(ellipse 80% 50% at 50% 0%,   rgba(139,26,46,.12) 0%, transparent 70%),
    radial-gradient(ellipse 80% 50% at 50% 100%,  rgba(201,162,39,.08) 0%, transparent 70%);
}

/* ════ SCREENS ════ */
.scr { position: fixed; inset: 0; z-index: 10; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px; }
.scr.off { display: none !important; }

/* ════════════════════════════════════
   INTRO SCREEN
════════════════════════════════════ */
#si { gap: 24px; }

.logo-wrap { text-align: center; }
.logo-main {
  font-family: 'Playfair Display', serif;
  font-size: clamp(32px, 5vw, 58px);
  font-weight: 900;
  color: var(--gold2);
  text-shadow: 0 0 30px rgba(201,162,39,.5), 0 2px 4px rgba(0,0,0,.8);
  letter-spacing: .05em;
}
.logo-main span { color: var(--txt); }
.logo-sub {
  font-size: 11px; font-weight: 700; letter-spacing: .3em;
  color: var(--txt3); text-transform: uppercase; margin-top: 4px;
}

/* VS card */
.vs-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 20px 40px;
  display: flex; align-items: center; gap: 32px;
  box-shadow: var(--shadow);
}
.combatant { text-align: center; }
.c-avatar {
  width: 64px; height: 64px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; margin: 0 auto 8px;
  border: 2px solid;
}
.c-avatar.enemy  { border-color: var(--maroon); background: rgba(139,26,46,.2); }
.c-avatar.player { border-color: var(--gold);   background: rgba(201,162,39,.15); }
.c-name { font-size: 13px; font-weight: 800; }
.c-name.enemy  { color: #e88; }
.c-name.player { color: var(--gold2); }
.vs-badge {
  font-family: 'Playfair Display', serif;
  font-size: 28px; font-weight: 900;
  color: var(--txt); opacity: .5;
}

.intro-desc {
  font-size: 14px; color: var(--txt2); text-align: center;
  line-height: 1.7; max-width: 440px;
}
.intro-desc strong { color: var(--gold2); }

/* Difficulty buttons */
.diff-row { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
.dbt {
  padding: 14px 28px; border-radius: 10px;
  font-family: 'Nunito', sans-serif; font-size: 14px; font-weight: 800;
  cursor: pointer; transition: all .2s; border: 2px solid; background: transparent;
  display: flex; flex-direction: column; align-items: center; gap: 2px;
}
.dbt small { font-size: 10px; font-weight: 600; opacity: .7; letter-spacing: .05em; }
.dbt:hover { transform: translateY(-3px); }
.dbt.e { color: var(--green); border-color: var(--green); }
.dbt.e:hover { background: rgba(39,174,96,.1); box-shadow: 0 0 20px rgba(39,174,96,.3); }
.dbt.n { color: var(--gold2); border-color: var(--gold); }
.dbt.n:hover { background: rgba(201,162,39,.1); box-shadow: 0 0 20px rgba(201,162,39,.3); }
.dbt.h { color: #e88; border-color: var(--maroon); }
.dbt.h:hover { background: rgba(139,26,46,.15); box-shadow: 0 0 20px rgba(139,26,46,.4); }

/* ════════════════════════════════════
   BATTLE SCREEN
════════════════════════════════════ */
#sb {
  display: grid;
  grid-template-rows: 68px 1fr 46px;
  height: 100%; gap: 0;
  background: var(--bg);
  justify-items: stretch; align-items: stretch;
}

/* ── HUD ── */
.hud {
  grid-row: 1;
  display: grid; grid-template-columns: 1fr auto 1fr;
  align-items: center; padding: 0 20px;
  background: var(--bg2);
  border-bottom: 1px solid var(--border);
  box-shadow: 0 2px 12px rgba(0,0,0,.4);
  z-index: 50;
}

.ph { display: flex; align-items: center; gap: 10px; }
.ph.enemy-side { flex-direction: row-reverse; }

.h-av {
  width: 40px; height: 40px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700;
}
.h-av.p { background: rgba(201,162,39,.15); border: 1px solid var(--gold); color: var(--gold2); }
.h-av.e { background: rgba(139,26,46,.2);   border: 1px solid var(--maroon); color: #e88; }

.h-info { display: flex; flex-direction: column; gap: 3px; }
.h-name { font-size: 11px; font-weight: 800; letter-spacing: .04em; }
.h-name.p { color: var(--gold2); }
.h-name.e { color: #e88; }
.h-bar { width: 110px; height: 6px; background: rgba(255,255,255,.1); border-radius: 99px; overflow: hidden; }
.h-fill { height: 100%; border-radius: 99px; transition: width .5s cubic-bezier(.4,2,.6,1); }
.h-fill.p { background: linear-gradient(90deg, var(--gold), var(--gold2)); box-shadow: 0 0 8px rgba(201,162,39,.5); }
.h-fill.e { background: linear-gradient(90deg, var(--mar2), var(--mar3));  box-shadow: 0 0 8px rgba(139,26,46,.5); }
.h-hp { font-size: 9px; color: var(--txt3); font-weight: 700; }

.h-score { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 900; }
.h-score.p { color: var(--gold2); text-shadow: 0 0 12px rgba(201,162,39,.4); }
.h-score.e { color: #e88;         text-shadow: 0 0 12px rgba(139,26,46,.4); }
.h-streak  { font-size: 10px; font-weight: 700; }

.hud-center { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.round-lbl { font-size: 9px; font-weight: 800; letter-spacing: .25em; color: var(--txt3); text-transform: uppercase; }

/* Timer ring */
.t-ring { position: relative; width: 42px; height: 42px; }
.t-ring svg { transform: rotate(-90deg); }
.t-bg   { fill: none; stroke: rgba(255,255,255,.08); stroke-width: 3; }
.t-fill { fill: none; stroke-width: 3; stroke-linecap: round; transition: stroke-dashoffset .1s linear, stroke .3s; }
.t-num  {
  position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800; font-family: 'Nunito', sans-serif;
}

/* ── ARENA ── */
.arena {
  grid-row: 2;
  display: grid;
  grid-template-rows: auto 1fr auto;
  align-items: center;
  padding: 10px 20px;
  gap: 8px;
  overflow: hidden;
}

/* Enemy answers — TOP (ẩn chữ, chỉ hiện ô) */
.e-zone { width: 100%; max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 6px; }
.e-think { display: flex; gap: 5px; align-items: center; font-size: 11px; font-weight: 700; color: #a06060; justify-content: center; }
.dot { width: 5px; height: 5px; border-radius: 50%; background: #c0392b; animation: bk 1s infinite; }
.dot:nth-child(2) { animation-delay: .2s; }
.dot:nth-child(3) { animation-delay: .4s; }
@keyframes bk { 0%,100%{opacity:1} 50%{opacity:.2} }

.e-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; width: 100%; }
.e-opt {
  height: 40px; border-radius: 8px;
  background: rgba(139,26,46,.08);
  border: 1px solid rgba(139,26,46,.2);
  /* chữ ẩn — chỉ hiện khi có class revealed */
  color: transparent;
  font-size: 13px; font-weight: 600;
  display: flex; align-items: center; padding: 0 14px;
  position: relative; overflow: hidden;
}
.e-opt.revealed   { color: #d0a0a0; }
.e-opt.e-correct  { background: rgba(139,26,46,.2) !important; border-color: #c0392b !important; color: #e88 !important; box-shadow: 0 0 14px rgba(139,26,46,.3) !important; }
.e-opt.e-wrong    { background: rgba(80,40,40,.1)  !important; border-color: rgba(139,26,46,.15) !important; color: #704040 !important; }

/* ── ORB CENTER ── */
.orb-section {
  display: flex; flex-direction: column; align-items: center; gap: 14px;
  width: 100%;
}

.tug-wrap { width: min(440px, 84vw); position: relative; }
.tug-track {
  height: 8px; background: rgba(255,255,255,.07); border-radius: 99px;
  position: relative; overflow: visible;
}
.tug-l {
  position: absolute; left: 0; top: 0; bottom: 0;
  background: linear-gradient(90deg, var(--mar2), var(--maroon));
  border-radius: 99px 0 0 99px; width: 50%;
  transition: width .4s cubic-bezier(.4,2,.6,1);
  box-shadow: 0 0 10px rgba(139,26,46,.5);
}
.tug-r {
  position: absolute; right: 0; top: 0; bottom: 0;
  background: linear-gradient(90deg, #9a7a10, var(--gold));
  border-radius: 0 99px 99px 0; width: 50%;
  transition: width .4s cubic-bezier(.4,2,.6,1);
  box-shadow: 0 0 10px rgba(201,162,39,.5);
}
.orb-wrap {
  position: absolute; top: 50%; transform: translate(-50%, -50%); left: 50%;
  transition: left .4s cubic-bezier(.4,2,.6,1); z-index: 20;
}
.orb {
  width: 52px; height: 52px; border-radius: 50%;
  background: radial-gradient(circle at 35% 35%, rgba(255,255,255,.4), transparent 60%),
              radial-gradient(circle at 50% 50%, var(--gold2), var(--maroon));
  box-shadow: 0 0 18px rgba(201,162,39,.5), 0 0 36px rgba(139,26,46,.3), inset 0 0 16px rgba(255,255,255,.1);
  animation: op 2.5s ease-in-out infinite; position: relative;
}
@keyframes op { 0%,100%{transform:scale(1)} 50%{transform:scale(1.07)} }
.orb-r1 { position:absolute;inset:-8px;border-radius:50%;border:1px solid rgba(201,162,39,.25);animation:rr 7s linear infinite; }
.orb-r2 { position:absolute;inset:-16px;border-radius:50%;border:1px dashed rgba(139,26,46,.2);animation:rr 12s linear infinite reverse; }
@keyframes rr { to{transform:rotate(360deg)} }
.orb.slam-l { animation: sl .28s ease-out !important; }
.orb.slam-r { animation: sr .28s ease-out !important; }
@keyframes sl { 0%{transform:scale(1.5) translateX(-8px)} 100%{transform:scale(1)} }
@keyframes sr { 0%{transform:scale(1.5) translateX(8px)}  100%{transform:scale(1)} }

/* Question box */
.q-box {
  width: 100%; max-width: 680px;
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 16px 24px;
  text-align: center;
  box-shadow: var(--shadow);
  position: relative;
}
.q-box::before {
  content: ''; position: absolute; top: 0; left: 10%; right: 10%; height: 2px;
  background: linear-gradient(90deg, transparent, var(--gold), transparent);
  border-radius: 99px;
}
.q-num  { font-size: 10px; font-weight: 700; letter-spacing: .3em; color: var(--txt3); text-transform: uppercase; margin-bottom: 6px; }
.q-text { font-size: clamp(14px, 2.2vw, 18px); font-weight: 700; color: var(--txt); line-height: 1.55; }

/* Player answers — BOTTOM */
.p-zone { width: 100%; max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 6px; }
.p-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; width: 100%; }

.p-opt {
  padding: 12px 16px; border-radius: 10px;
  background: var(--bg3); border: 1.5px solid rgba(201,162,39,.18);
  font-size: clamp(12px, 1.7vw, 14px); font-weight: 700;
  color: var(--txt2); cursor: pointer;
  transition: all .18s; text-align: left;
  display: flex; align-items: flex-start; gap: 8px;
}
.p-opt .key { color: var(--gold); font-size: 12px; flex-shrink: 0; margin-top: 1px; }
.p-opt:hover:not(:disabled) {
  border-color: var(--gold); color: var(--txt);
  background: rgba(201,162,39,.08);
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(201,162,39,.15);
}
.p-opt:disabled { cursor: not-allowed; }
.p-opt.correct {
  background: rgba(39,174,96,.12) !important; border-color: var(--green) !important;
  color: #7de7a0 !important; box-shadow: 0 0 16px rgba(39,174,96,.25) !important;
}
.p-opt.wrong {
  background: rgba(231,76,60,.12) !important; border-color: var(--red) !important;
  color: #f09090 !important; box-shadow: 0 0 16px rgba(231,76,60,.25) !important;
}

/* Streak bar */
.streak-bar {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 800; color: var(--gold2); min-height: 18px;
  justify-content: center;
}

/* ── BOTTOM BAR ── */
.bot-bar {
  grid-row: 3;
  display: flex; align-items: center; justify-content: center; gap: 12px;
  border-top: 1px solid var(--border);
  background: var(--bg2); padding: 0 20px;
}
.bot-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); box-shadow: 0 0 7px var(--green); animation: bk 2s infinite; }
.bot-txt { font-size: 11px; font-weight: 700; letter-spacing: .18em; color: var(--txt3); text-transform: uppercase; }

/* ── GLITCH ── */
.glitch-ov {
  position: fixed; inset: 0; z-index: 900; pointer-events: none; opacity: 0;
  background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(139,26,46,.06) 3px, rgba(139,26,46,.06) 4px);
}
.glitch-ov.go { animation: ga .4s ease-out forwards; }
@keyframes ga {
  0%  { opacity: 1; transform: translateX(0) skewX(0); }
  25% { transform: translateX(-4px) skewX(-2deg); filter: hue-rotate(30deg); }
  50% { transform: translateX(4px)  skewX(2deg);  filter: hue-rotate(-30deg); }
  75% { transform: translateX(-2px); }
  100%{ opacity: 0; transform: translateX(0); }
}

/* ── FLOAT TEXT ── */
.float-txt {
  position: fixed; pointer-events: none; z-index: 800;
  font-size: 22px; font-weight: 900; font-family: 'Playfair Display', serif;
  animation: fu .85s ease-out forwards;
}
@keyframes fu { 0%{opacity:1;transform:translateY(0) scale(1)} 50%{opacity:1;transform:translateY(-24px) scale(1.15)} 100%{opacity:0;transform:translateY(-55px) scale(.85)} }

/* ════════════════════════════════════
   RESULT SCREEN
════════════════════════════════════ */
#sr {
  gap: 20px;
  background: radial-gradient(ellipse 70% 60% at 50% 40%, rgba(201,162,39,.07), transparent 70%), var(--bg);
}
.r-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(36px, 7vw, 68px);
  font-weight: 900; letter-spacing: .06em; text-transform: uppercase;
  animation: rp .55s cubic-bezier(.4,2,.4,1) both;
}
.r-title.win  { color: var(--gold2); text-shadow: 0 0 30px rgba(201,162,39,.5); }
.r-title.lose { color: #e88;         text-shadow: 0 0 30px rgba(139,26,46,.5); }
.r-title.draw { color: var(--txt);   text-shadow: 0 0 20px rgba(255,255,255,.2); }
@keyframes rp { from{transform:scale(.3) rotate(-4deg);opacity:0} to{transform:scale(1);opacity:1} }
.r-sub { font-size: 13px; font-weight: 700; letter-spacing: .2em; color: var(--txt3); text-transform: uppercase; }

.r-stats { display: flex; gap: 28px; flex-wrap: wrap; justify-content: center; }
.r-stat { text-align: center; }
.r-val {
  font-family: 'Playfair Display', serif; font-size: 34px; font-weight: 900;
  color: var(--gold2); text-shadow: 0 0 12px rgba(201,162,39,.3);
}
.r-lbl { font-size: 10px; font-weight: 700; letter-spacing: .2em; color: var(--txt3); margin-top: 2px; text-transform: uppercase; }

.r-btns { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
.r-btn {
  padding: 12px 28px; border-radius: 10px; border: 2px solid;
  font-family: 'Nunito', sans-serif; font-size: 13px; font-weight: 800;
  cursor: pointer; transition: all .2s; background: transparent;
}
.r-btn.pr { color: var(--gold2); border-color: var(--gold); }
.r-btn.pr:hover { background: rgba(201,162,39,.1); box-shadow: 0 0 20px rgba(201,162,39,.3); transform: translateY(-2px); }
.r-btn.sc { color: var(--txt3); border-color: rgba(255,255,255,.15); }
.r-btn.sc:hover { color: var(--txt); border-color: rgba(255,255,255,.3); transform: translateY(-2px); }

@media(max-width:560px){
  .p-grid, .e-grid { grid-template-columns: 1fr; }
  .h-bar { width: 70px; }
  .h-score { font-size: 16px; }
  .orb { width: 40px; height: 40px; }
  .vs-card { padding: 16px 20px; gap: 20px; }
}
</style>
</head>
<body>
<div class="glitch-ov" id="glitch"></div>

<!-- ══════════════ INTRO ══════════════ -->
<div class="scr" id="si">
  <div class="logo-wrap">
    <div class="logo-main">⚔ Đấu Trí <span>Sử Việt</span></div>
    <div class="logo-sub">Chiến trường kiến thức · <?= $q_total ?> câu hỏi</div>
  </div>

  <div class="vs-card">
    <div class="combatant">
      <div class="c-avatar enemy"><?= $opp_ava ?></div>
      <div class="c-name enemy"><?= $opp_name ?></div>
    </div>
    <div class="vs-badge">VS</div>
    <div class="combatant">
      <div class="c-avatar player"><?= mb_strtoupper(mb_substr($username,0,2,'UTF-8')) ?></div>
      <div class="c-name player"><?= $username ?></div>
    </div>
  </div>

  <p class="intro-desc">
    Trả lời đúng để đẩy <strong>quả cầu vàng</strong> về phía đối thủ.<br>
    Streak cao → lực đẩy mạnh hơn · Sai → màn hình rung.
  </p>

  <div class="diff-row">
    <button class="dbt e" onclick="startGame('easy')">Tập Sự<small>30s · AI 50%</small></button>
    <button class="dbt n" onclick="startGame('normal')">Chiến Binh<small>20s · AI 65%</small></button>
    <button class="dbt h" onclick="startGame('hard')">Thủ Lĩnh<small>12s · AI 80%</small></button>
  </div>
</div>

<!-- ══════════════ BATTLE ══════════════ -->
<div class="scr off" id="sb">

  <!-- HUD -->
  <div class="hud">
    <!-- Enemy (trái) -->
    <div class="ph enemy-side">
      <div class="h-av e"><?= mb_strtoupper(mb_substr($opp_name,0,2,'UTF-8')) ?></div>
      <div class="h-info" style="align-items:flex-end">
        <div class="h-name e"><?= $opp_name ?></div>
        <div class="h-bar"><div class="h-fill e" id="e-hp" style="width:100%"></div></div>
        <div class="h-hp" id="e-hpt">100 HP</div>
      </div>
      <div style="text-align:right">
        <div class="h-score e" id="e-sc">0</div>
        <div class="h-streak" style="color:#e88" id="e-str"></div>
      </div>
    </div>

    <!-- Center -->
    <div class="hud-center">
      <div class="round-lbl" id="rnd">ROUND 1/10</div>
      <div class="t-ring">
        <svg width="42" height="42" viewBox="0 0 42 42">
          <circle class="t-bg"   cx="21" cy="21" r="16"/>
          <circle class="t-fill" id="tc" cx="21" cy="21" r="16"
            stroke-dasharray="101" stroke-dashoffset="0" stroke="var(--gold)"/>
        </svg>
        <div class="t-num" id="tn" style="color:var(--gold2)">–</div>
      </div>
    </div>

    <!-- Player (phải) -->
    <div class="ph">
      <div class="h-av p"><?= mb_strtoupper(mb_substr($username,0,2,'UTF-8')) ?></div>
      <div class="h-info">
        <div class="h-name p"><?= $username ?></div>
        <div class="h-bar"><div class="h-fill p" id="p-hp" style="width:100%"></div></div>
        <div class="h-hp" id="p-hpt">100 HP</div>
      </div>
      <div>
        <div class="h-score p" id="p-sc">0</div>
        <div class="h-streak" style="color:var(--gold2)" id="p-str"></div>
      </div>
    </div>
  </div>

  <!-- ARENA -->
  <div class="arena">

    <!-- Enemy zone (top) — CHỈ HIỆN Ô, ẨN CHỮ -->
    <div class="e-zone">
      <div class="e-think" id="e-think">
        <div class="dot"></div><div class="dot"></div><div class="dot"></div>
        <span>Đối thủ đang suy nghĩ...</span>
      </div>
      <div class="e-grid" id="e-grid"></div>
    </div>

    <!-- Orb + Question (giữa) -->
    <div class="orb-section">
      <div class="tug-wrap">
        <div class="tug-track" id="tug">
          <div class="tug-l"  id="tl" style="width:50%"></div>
          <div class="tug-r"  id="tr" style="width:50%"></div>
          <div class="orb-wrap" id="ow">
            <div class="orb" id="orb">
              <div class="orb-r1"></div>
              <div class="orb-r2"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="q-box">
        <div class="q-num" id="qn">CÂU HỎI 1</div>
        <div class="q-text" id="qt">–</div>
      </div>
    </div>

    <!-- Player zone (bottom) -->
    <div class="p-zone">
      <div class="p-grid" id="p-grid"></div>
      <div class="streak-bar" id="streak-bar"></div>
    </div>

  </div>

  <!-- Bottom bar -->
  <div class="bot-bar">
    <div class="bot-dot"></div>
    <div class="bot-txt" id="bot-msg">Chọn đáp án của bạn!</div>
  </div>

</div>

<!-- ══════════════ RESULT ══════════════ -->
<div class="scr off" id="sr">
  <div class="r-title" id="r-title">CHIẾN THẮNG</div>
  <div class="r-sub"   id="r-sub">Xuất sắc!</div>
  <div class="r-stats">
    <div class="r-stat"><div class="r-val" id="r-sc">0</div><div class="r-lbl">Điểm số</div></div>
    <div class="r-stat"><div class="r-val" id="r-co">0</div><div class="r-lbl">Câu đúng</div></div>
    <div class="r-stat"><div class="r-val" id="r-mx">0</div><div class="r-lbl">Streak cao nhất</div></div>
    <div class="r-stat"><div class="r-val" id="r-ac">0%</div><div class="r-lbl">Chính xác</div></div>
  </div>
  <button class="r-btn sc" onclick="window.parent.closeGame()">← Phòng luyện thi</button>
</div>

<script>
/* ════ DATA (nhúng thẳng từ PHP) ════ */
const RAW = <?= $questions_json ?>;
/* ════ AUDIO — Khởi tạo âm thanh ════ */
const soundCorrect = new Audio('../../audio/correct.mp3');
const soundWrong   = new Audio('../../audio/wrong.mp3');

// Hàm bổ trợ để phát âm thanh mượt mà (tránh bị đứng khi bấm liên tục)
function playSound(type) {
    const s = (type === 'correct') ? soundCorrect : soundWrong;
    s.currentTime = 0; // Quay về đầu file để phát lại ngay lập tức
    s.play().catch(e => console.warn("Trình duyệt chặn âm thanh tự động:", e));
}

/* ════ CONFIG ════ */
const CFG = {
  easy:  { time:30, dmg:12, eacc:.50, emin:10000, emax:24000 },
  normal:{ time:20, dmg:18, eacc:.65, emin:5000,  emax:16000 },
  hard:  { time:12, dmg:25, eacc:.80, emin:2500,  emax:9000  },
};
const ROUNDS = 10; // số câu mỗi ván

/* ════ STATE ════ */
let Q = [], S = {};

/* ════ HELPERS ════ */
const G = id => document.getElementById(id);
const L = i  => String.fromCharCode(65 + i);

function show(id) {
  ['si','sb','sr'].forEach(s => {
    G(s).classList.toggle('off', s !== id);
  });
}

/* ════ START GAME ════ */
function startGame(diff) {
  // Xáo + lấy ROUNDS câu, xáo đáp án mỗi câu
  Q = [...RAW].sort(() => Math.random() - .5)
    .slice(0, Math.min(ROUNDS, RAW.length))
    .map(item => {
      const ct = item.a[item.c];
      const ans = [...item.a].sort(() => Math.random() - .5);
      return { q: item.q, a: ans, c: ans.indexOf(ct) };
    });

  S = {
    diff, cfg: CFG[diff], qi: 0,
    php: 100, ehp: 100,
    psc: 0,   esc: 0,
    pstr: 0,  estr: 0,
    pmx: 0,   pcor: 0,
    orb: 50,  done: false,
    tv: 0,    ti: null, et: null,
  };

  show('sb');
  renderQ();
}

/* ════ RENDER QUESTION ════ */
function renderQ() {
  const q = Q[S.qi];
  S.done = false;

  G('rnd').textContent  = `ROUND ${S.qi + 1} / ${Q.length}`;
  G('qn').textContent   = `CÂU HỎI ${S.qi + 1}`;
  G('qt').textContent   = q.q;
  G('bot-msg').textContent = 'Chọn đáp án của bạn!';
  G('e-think').style.display = 'flex';

  /* --- Enemy grid: chỉ hiện ô trống, ẨN CHỮ --- */
  const eg = G('e-grid'); eg.innerHTML = '';
  q.a.forEach((txt, i) => {
    const d = document.createElement('div');
    d.className = 'e-opt';
    d.id = `eo-${i}`;
    d.textContent = `${L(i)}. ${txt}`; // text có nhưng màu transparent
    eg.appendChild(d);
  });

  /* --- Player grid --- */
  const pg = G('p-grid'); pg.innerHTML = '';
  q.a.forEach((txt, i) => {
    const b = document.createElement('button');
    b.className = 'p-opt';
    b.innerHTML = `<span class="key">${L(i)}.</span><span>${txt}</span>`;
    b.onclick = () => playerAnswer(i);
    pg.appendChild(b);
  });

  updateStreakUI();
  startTimer();
  scheduleEnemy();
}

/* ════ TIMER ════ */
const CIRC = 2 * Math.PI * 16;
function startTimer() {
  clearInterval(S.ti);
  S.tv = S.cfg.time;
  tickTimer();
  S.ti = setInterval(() => {
    S.tv--;
    tickTimer();
    if (S.tv <= 0) { clearInterval(S.ti); if (!S.done) timeUp(); }
  }, 1000);
}
function tickTimer() {
  const pct = S.tv / S.cfg.time;
  G('tc').style.strokeDashoffset = CIRC * (1 - pct);
  G('tn').textContent = S.tv;
  const col = pct > .5 ? 'var(--gold)' : pct > .25 ? '#e0a020' : 'var(--red)';
  G('tc').style.stroke = col;
  G('tn').style.color  = col;
}
function timeUp() {
  S.done = true;
  clearTimeout(S.et);
  G('bot-msg').textContent = '⏱ Hết giờ! Bạn không kịp trả lời.';
  revealPlayer(null);
  S.pstr = 0;
  hpChange('p', -S.cfg.dmg);
  pushOrb('e', 1);
  updateStreakUI();
  setTimeout(nextQ, 1800);
}

/* ════ PLAYER ANSWER ════ */
function playerAnswer(idx) {
  
  if (S.done) return;
  S.done = true;
  clearInterval(S.ti);
  clearTimeout(S.et);

  const q  = Q[S.qi];
  const ok = idx === q.c;
  const btns = G('p-grid').querySelectorAll('.p-opt');
  btns.forEach(b => b.disabled = true);
  btns[idx].classList.add(ok ? 'correct' : 'wrong');
  revealPlayer(idx);

  const tb = Math.floor(S.tv / S.cfg.time * 5);
  const sm = 1 + Math.min(S.pstr, 5) * .3;

  if (ok) {
    playSound('correct');
    S.pstr++;
    S.pmx  = Math.max(S.pmx, S.pstr);
    S.pcor++;
    const pts = Math.floor((10 + tb) * sm);
    S.psc += pts;
    G('p-sc').textContent = S.psc;
    pushOrb('p', 1 + Math.min(S.pstr - 1, 4) * .5);
    hpChange('e', -Math.floor(S.cfg.dmg * sm));
    floatTxt(`+${pts}`, 'var(--gold2)', 'bot');
    G('bot-msg').textContent = `✅ Chính xác! +${pts} điểm` + (S.pstr > 1 ? ` 🔥 ×${S.pstr} Streak!` : '');
  } else {
    playSound('wrong');
    S.pstr = 0;
    pushOrb('e', 1);
    hpChange('p', -S.cfg.dmg);
    trigGlitch();
    floatTxt(`-${S.cfg.dmg} HP`, 'var(--red)', 'bot');
    G('bot-msg').textContent = '❌ Sai rồi! Nhận phản đòn!';
  }

  updateStreakUI();
  doEnemyAnswer();
}

/* ════ REVEAL CORRECT (player side) ════ */
function revealPlayer(chosen) {
  const btns = G('p-grid').querySelectorAll('.p-opt');
  Q[S.qi].a.forEach((_, i) => {
    btns[i].disabled = true;
    if (i === Q[S.qi].c && i !== chosen) btns[i].classList.add('correct');
  });
}

/* ════ ENEMY AI ════ */
function scheduleEnemy() {
  const d = S.cfg.emin + Math.random() * (S.cfg.emax - S.cfg.emin);
  S.et = setTimeout(() => {
    if (!S.done) doEnemyAnswer(true);
  }, d);
}

function doEnemyAnswer(first = false) {
  G('e-think').style.display = 'none';
  const q  = Q[S.qi];
  const ok = Math.random() < S.cfg.eacc;
  const ci = ok ? q.c : (() => {
    const wr = q.a.map((_,i)=>i).filter(i=>i!==q.c);
    return wr[Math.floor(Math.random() * wr.length)];
  })();

  // Hiện lựa chọn enemy (reveal chữ)
  setTimeout(() => {
    const eOpts = G('e-grid').querySelectorAll('.e-opt');
    eOpts.forEach(o => o.classList.add('revealed'));

    if (ok) {
      eOpts[ci].classList.add('e-correct');
      S.estr++;
      S.esc += 10 + S.estr * 2;
      G('e-sc').textContent = S.esc;
      G('e-str').textContent = S.estr > 1 ? `🔥 ×${S.estr}` : '';

      if (first && !S.done) {
        // Enemy tấn công trước
        playSound('wrong');
        pushOrb('e', 1 + Math.min(S.estr - 1, 4) * .5);
        hpChange('p', -Math.floor(S.cfg.dmg * .8));
        floatTxt(`-${Math.floor(S.cfg.dmg * .8)} HP`, '#e88', 'top');
        trigGlitch();
        G('bot-msg').textContent = '⚡ Đối thủ ra tay trước! Hãy phản công!';
        return;
      }
    } else {
      eOpts[ci].classList.add('e-wrong');
      S.estr = 0;
      G('e-str').textContent = '';
    }

    if (S.done) setTimeout(nextQ, 1400);
  }, 350);
}

/* ════ ORB TUG ════ */
function pushOrb(who, force) {
  const d = force * 8;
  S.orb = who === 'p' ? Math.min(85, S.orb + d) : Math.max(15, S.orb - d);
  const w = G('tug').offsetWidth || 440;
  G('tl').style.width = (100 - S.orb) + '%';
  G('tr').style.width = S.orb + '%';
  G('ow').style.left  = (S.orb / 100 * w) + 'px';

  const o = G('orb');
  o.classList.remove('slam-l','slam-r');
  void o.offsetWidth;
  o.classList.add(S.orb > 50 ? 'slam-r' : 'slam-l');

  // Màu orb theo phe đang thắng
  if (S.orb > 65) {
    o.style.background = `radial-gradient(circle at 35% 35%,rgba(255,255,255,.4),transparent 60%),radial-gradient(circle at 50% 50%,var(--gold2),#9a7010)`;
    o.style.boxShadow  = `0 0 22px rgba(201,162,39,.7),0 0 44px rgba(201,162,39,.4)`;
  } else if (S.orb < 35) {
    o.style.background = `radial-gradient(circle at 35% 35%,rgba(255,255,255,.3),transparent 60%),radial-gradient(circle at 50% 50%,var(--maroon),var(--mar2))`;
    o.style.boxShadow  = `0 0 22px rgba(139,26,46,.7),0 0 44px rgba(139,26,46,.4)`;
  } else {
    o.style.background = `radial-gradient(circle at 35% 35%,rgba(255,255,255,.4),transparent 60%),radial-gradient(circle at 50% 50%,var(--gold2),var(--maroon))`;
    o.style.boxShadow  = `0 0 18px rgba(201,162,39,.5),0 0 36px rgba(139,26,46,.3)`;
  }
}

/* ════ HP ════ */
function hpChange(who, delta) {
  if (who === 'p') {
    S.php = Math.max(0, Math.min(100, S.php + delta));
    G('p-hp').style.width  = S.php + '%';
    G('p-hpt').textContent = S.php + ' HP';
  } else {
    S.ehp = Math.max(0, Math.min(100, S.ehp + delta));
    G('e-hp').style.width  = S.ehp + '%';
    G('e-hpt').textContent = S.ehp + ' HP';
  }
  if (S.php <= 0 || S.ehp <= 0) {
    clearInterval(S.ti); clearTimeout(S.et);
    setTimeout(showResult, 700);
  }
}

/* ════ STREAK UI ════ */
function updateStreakUI() {
  const s  = S.pstr;
  const el = G('streak-bar');
  if (s >= 2) {
    const fires = '🔥'.repeat(Math.min(s, 5));
    const mult  = (1 + Math.min(s, 5) * .3).toFixed(1);
    el.innerHTML = `<span style="font-size:16px">${fires}</span>
      <span style="color:var(--gold2);font-size:12px;font-weight:800">${s} Streak! ×${mult}</span>`;
  } else { el.innerHTML = ''; }
  G('p-str').textContent = s > 1 ? `🔥 ×${s}` : '';
}

/* ════ EFFECTS ════ */
function trigGlitch() {
  const el = G('glitch');
  el.classList.remove('go'); void el.offsetWidth; el.classList.add('go');
}
function floatTxt(txt, color, pos) {
  const el = document.createElement('div');
  el.className = 'float-txt';
  el.textContent = txt;
  el.style.cssText = `color:${color};text-shadow:0 0 12px ${color};
    left:${28 + Math.random() * 40}%;top:${pos === 'top' ? '12%' : '68%'}`;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 900);
}

/* ════ NEXT / RESULT ════ */
function nextQ() {
  if (S.php <= 0 || S.ehp <= 0) { showResult(); return; }
  S.qi++;
  if (S.qi >= Q.length) { showResult(); return; }
  renderQ();
}

function showResult() {
  clearInterval(S.ti); clearTimeout(S.et);

  const win  = S.php > S.ehp || (S.php === S.ehp && S.psc > S.esc);
  const draw = S.php === S.ehp && S.psc === S.esc;
  const t    = G('r-title');

  if (draw)      { t.textContent = 'HÒA';         t.className = 'r-title draw'; G('r-sub').textContent = 'Thế cờ bất phân thắng bại!'; }
  else if (win)  { t.textContent = 'CHIẾN THẮNG'; t.className = 'r-title win';  G('r-sub').textContent = 'Xuất sắc! Bạn đã chinh phục đối thủ!'; }
  else           { t.textContent = 'THẤT BẠI';    t.className = 'r-title lose'; G('r-sub').textContent = 'Hãy rèn luyện thêm và thử lại!'; }

  const done = S.qi + (S.done ? 1 : 0);
  G('r-sc').textContent = S.psc;
  G('r-co').textContent = S.pcor;
  G('r-mx').textContent = S.pmx;
  G('r-ac').textContent = done > 0 ? Math.round(S.pcor / done * 100) + '%' : '0%';

  show('sr');
}

/* ════ KEYBOARD 1-4 ════ */
document.addEventListener('keydown', e => {
  const i = ['1','2','3','4'].indexOf(e.key);
  if (i !== -1 && !S.done) {
    const b = G('p-grid').querySelectorAll('.p-opt')[i];
    if (b && !b.disabled) b.click();
  }
});
</script>
</body>
</html>