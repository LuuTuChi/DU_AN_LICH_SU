<?php
// ============================================================
// MODULE: t_heatmap.php v2 — Theo dõi tiến độ & AI
// Thay đổi: Bỏ 8 stat cards → Action Center 3 cột
//           VIP row nền vàng + Accordion chỉ mở với VIP
//           Heatmap cell màu gradient theo % tiến độ
//           Viền đỏ nhấp nháy trên ô quiz < 5.0
//           Nhắc nhở hàng loạt cho học sinh "Xám"
// ============================================================

if (function_exists('getDB')) {
    $hm_pdo  = getDB(); $use_pdo = true;
} else {
    $use_pdo = false;
}
function hm_query(string $sql, array $params = []): array {
    global $hm_pdo, $conn, $use_pdo;
    if ($use_pdo) { $st=$hm_pdo->prepare($sql); $st->execute($params); return $st->fetchAll(PDO::FETCH_ASSOC); }
    if (!empty($params)) { $st=$conn->prepare($sql); $st->bind_param(str_repeat('s',count($params)),...$params); $st->execute(); return $st->get_result()->fetch_all(MYSQLI_ASSOC); }
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}
function hm_scalar(string $sql, array $params=[]) { $r=hm_query($sql,$params); return $r?array_values($r[0])[0]:null; }

// ── STATS (dùng cho Action Center) ───────────────────────
$stat_total       = (int)hm_scalar("SELECT COUNT(*) FROM users WHERE role='student'");
$stat_has_profile = (int)hm_scalar("SELECT COUNT(DISTINCT sp.user_id) FROM student_profile sp INNER JOIN users u ON u.id=sp.user_id WHERE u.role='student'");
$stat_started     = (int)hm_scalar("SELECT COUNT(DISTINCT user_id) FROM lesson_progress");
$stat_vip_count   = (int)hm_scalar("SELECT COUNT(*) FROM users WHERE role='student' AND is_vip=1");
$stat_vip_pending = (int)hm_scalar("SELECT COUNT(*) FROM vip_requests WHERE status='pending'");
$stat_sessions    = (int)hm_scalar("SELECT COUNT(*) FROM study_sessions");
$stat_minutes     = (int)hm_scalar("SELECT ROUND(SUM(duration_s)/60) FROM study_sessions");
$stat_quiz_done   = (int)hm_scalar("SELECT COUNT(*) FROM quiz_scores");
$stat_quiz_avg    = hm_scalar("SELECT ROUND(AVG(score/total_q*10),1) FROM quiz_scores");
$stat_flash       = (int)hm_scalar("SELECT COALESCE(SUM(cards_done),0) FROM flashcard_log");
$stat_active_7d   = (int)hm_scalar("SELECT COUNT(DISTINCT user_id) FROM study_sessions WHERE study_date>=DATE_SUB(CURDATE(),INTERVAL 7 DAY)");

$chk_qa = hm_query("SHOW TABLES LIKE 'teacher_qa'");
$stat_qa_pending   = 0;
$stat_qa_vip_pending = 0;
if (!empty($chk_qa)) {
    $stat_qa_pending     = (int)hm_scalar("SELECT COUNT(*) FROM teacher_qa WHERE status='pending'");
    $stat_qa_vip_pending = (int)hm_scalar("SELECT COUNT(*) FROM teacher_qa tq INNER JOIN users u ON u.id=tq.user_id WHERE tq.status='pending' AND u.is_vip=1");
}

// ── DANH SÁCH HỌC SINH ───────────────────────────────────
$all_students = hm_query("
    SELECT u.id, u.fullname AS u_fullname, u.username, u.is_vip,
           u.status AS acc_status, u.created_at AS registered_at,
           sp.fullname AS sp_fullname, sp.school, sp.grade, sp.target_score,
           sp.target_time_frame, sp.last_test_score, sp.self_level,
           sp.has_study_plan, sp.study_time_of_day, sp.study_time_per_session,
           sp.avg_history_11, sp.study_sessions_per_week
    FROM users u
    LEFT JOIN student_profile sp ON sp.user_id=u.id
    WHERE u.role='student'
    ORDER BY u.is_vip DESC, (sp.user_id IS NOT NULL) DESC, u.id ASC
");

// Lesson IDs
$lesson_ids_raw = hm_query("SELECT DISTINCT lesson_id FROM lesson_progress ORDER BY lesson_id");
$lesson_ids = array_column($lesson_ids_raw,'lesson_id');
if (empty($lesson_ids)) $lesson_ids = [6,7,8];

// Ma trận tiến độ
$matrix_raw = hm_query("SELECT user_id,lesson_id,completed,pct_done FROM lesson_progress");
$matrix = [];
foreach ($matrix_raw as $r) $matrix[(int)$r['user_id']][(int)$r['lesson_id']] = ['completed'=>(int)$r['completed'],'pct'=>(int)$r['pct_done']];

// Quiz tổng hợp
$quiz_agg_raw = hm_query("SELECT user_id,ROUND(AVG(score/total_q*10),2) AS avg_10,COUNT(*) AS cnt FROM quiz_scores GROUP BY user_id");
$quiz_agg = [];
foreach ($quiz_agg_raw as $r) $quiz_agg[(int)$r['user_id']] = $r;

// Quiz từng bài (gồm điểm để check < 5.0 per lesson)
$quiz_detail_raw = hm_query("SELECT user_id,lesson_id,score,total_q,ROUND(score/total_q*10,1) AS score_10,taken_at FROM quiz_scores ORDER BY user_id,(score/total_q) ASC");
$quiz_detail = [];
$quiz_per_lesson = []; // [uid][lid] = score_10
foreach ($quiz_detail_raw as $r) {
    $quiz_detail[(int)$r['user_id']][] = $r;
    $quiz_per_lesson[(int)$r['user_id']][(int)$r['lesson_id']] = (float)$r['score_10'];
}

// Sessions
$sess_raw = hm_query("SELECT user_id, COUNT(CASE WHEN study_date>=DATE_SUB(CURDATE(),INTERVAL 7 DAY) THEN 1 END) AS s7, COUNT(CASE WHEN study_date>=DATE_SUB(CURDATE(),INTERVAL 14 DAY) THEN 1 END) AS s14, ROUND(SUM(CASE WHEN study_date>=DATE_SUB(CURDATE(),INTERVAL 7 DAY) THEN duration_s ELSE 0 END)/60,0) AS min7, ROUND(SUM(duration_s)/60,0) AS total_min, COUNT(DISTINCT study_date) AS active_days, MAX(study_date) AS last_active FROM study_sessions GROUP BY user_id");
$sess_map = [];
foreach ($sess_raw as $r) $sess_map[(int)$r['user_id']] = $r;

// Tiến độ
$prog_raw = hm_query("SELECT user_id,COUNT(*) AS started,SUM(completed=1) AS done,ROUND(AVG(pct_done),1) AS avg_pct,MAX(last_seen) AS last_seen FROM lesson_progress GROUP BY user_id");
$prog_map = [];
foreach ($prog_raw as $r) $prog_map[(int)$r['user_id']] = $r;

// Flashcard
$flash_raw = hm_query("SELECT user_id,COALESCE(SUM(cards_done),0) AS total FROM flashcard_log GROUP BY user_id");
$flash_map = [];
foreach ($flash_raw as $r) $flash_map[(int)$r['user_id']] = (int)$r['total'];

// Q&A pending
$pend_raw = !empty($chk_qa) ? hm_query("SELECT user_id,COUNT(*) AS cnt FROM teacher_qa WHERE status='pending' GROUP BY user_id") : [];
$pend_map = [];
foreach ($pend_raw as $r) $pend_map[(int)$r['user_id']] = (int)$r['cnt'];

// VIP requests pending
$vip_req_raw = hm_query("SELECT user_id FROM vip_requests WHERE status='pending'");
$vip_req_list = array_column($vip_req_raw,'user_id');

// Activity by DOW
$dow_raw = hm_query("SELECT DAYOFWEEK(study_date) AS dow,COUNT(*) AS cnt FROM study_sessions GROUP BY dow ORDER BY dow");
$dow_map = [];
foreach ($dow_raw as $r) $dow_map[(int)$r['dow']] = (int)$r['cnt'];
$max_dow = max(array_values($dow_map) ?: [1]);
$dow_labels = [2=>'T2',3=>'T3',4=>'T4',5=>'T5',6=>'T6',7=>'T7',1=>'CN'];

// ── AI CALCULATION (11 nhân tố) ──────────────────────────
function hm_calc_ai(array $s,array $prog,array $quiz_rows,array $quiz_avg,array $sess,int $flash): array {
    $now=$time=time();
    $target  = $s['target_score']!==null?(float)$s['target_score']:null;
    $tf      = $s['target_time_frame']??null;
    $acc_st  = $s['acc_status']??'active';
    $has_plan= $s['has_study_plan']??null;
    $tod     = $s['study_time_of_day']??'tối';
    $tps     = $s['study_time_per_session']??'30-60';
    $l_start = isset($prog['started'])?(int)$prog['started']:0;
    $l_done  = isset($prog['done'])?(int)$prog['done']:0;
    $avg_pct = isset($prog['avg_pct'])?(float)$prog['avg_pct']:null;
    $s7=(int)($sess['s7']??0); $s14=(int)($sess['s14']??0);
    $min7=(int)($sess['min7']??0); $total_min=(int)($sess['total_min']??0);
    $last_act=$sess['last_active']??null;
    $q_avg10=isset($quiz_avg['avg_10'])?(float)$quiz_avg['avg_10']:null;
    $q_cnt  =isset($quiz_avg['cnt'])?(int)$quiz_avg['cnt']:0;
    $days_inact=$last_act?max(0,(int)(($now-strtotime($last_act))/86400)):null;
    $days_reg=(int)(($now-strtotime($s['registered_at']))/86400);
    $days_left=null;
    if($tf){preg_match('/(\d+)\s*(tháng|tuần|ngày)/ui',$tf,$m);if(!empty($m)){$n=(int)$m[1];$u=mb_strtolower($m[2]);$td=str_contains($u,'tháng')?$n*30:(str_contains($u,'tuần')?$n*7:$n);$days_left=max(0,$td-$days_reg);}}
    $exp_pct=null;
    if($days_left!==null){$td2=$days_left+$days_reg;$exp_pct=$td2>0?min(100,round($days_reg/$td2*100)):null;}
    $trend=$s7-($s14-$s7);
    $weakest=null;
    foreach($quiz_rows as $qr){if((float)$qr['score_10']<6.0){$weakest=$qr;break;}}
    $predicted=null;
    if($target!==null&&$avg_pct!==null&&$q_avg10!==null){$bonus=$s7>=5?1.08:($s7>=3?1.0:0.88);$predicted=round(min($target,max(0,$target*($avg_pct/100)*($q_avg10/10)*$bonus*2)),1);}
    $risk=0;$flags=[];$tips=[];
    // NF1
    if($acc_st==='locked'){$risk+=20;$flags[]=['icon'=>'block','color'=>'#7b241c','text'=>'Tài khoản bị khóa — không thể đăng nhập'];$tips[]='Liên hệ Admin mở khóa để học sinh tiếp tục.';}
    // NF2
    if($target!==null&&$avg_pct!==null){if($target>=9.0&&$avg_pct<30){$risk+=35;$flags[]=['icon'=>'rocket_launch','color'=>'#c0392b','text'=>"Mục tiêu {$target}đ rất cao nhưng mới đạt ".round($avg_pct)."% — khoảng cách nguy hiểm"];}elseif($target>=8.0&&$avg_pct<45){$risk+=20;$flags[]=['icon'=>'trending_down','color'=>'#d35400','text'=>"Mục tiêu {$target}đ nhưng tiến độ ".round($avg_pct)."% thấp hơn kỳ vọng"];}elseif($avg_pct>=80){$risk-=10;$flags[]=['icon'=>'local_fire_department','color'=>'#1e8449','text'=>"Tiến độ ".round($avg_pct)."% — đang bám sát lộ trình rất tốt!"];}}
    // NF3
    if($exp_pct!==null&&$avg_pct!==null){$gap=$exp_pct-$avg_pct;if($gap>=20){$risk+=15;$flags[]=['icon'=>'schedule','color'=>'#d35400','text'=>"Tụt hậu ".round($gap)."% so với lịch trình"];}elseif($gap<=-15){$risk-=10;$flags[]=['icon'=>'star','color'=>'#1e8449','text'=>"Vượt lịch trình ".round(abs($gap))."% — đang đi trước kế hoạch!"];$tips[]="Gợi ý thử đề thi thử để kiểm tra thực lực.";}}
    // NF4
    if($days_inact!==null){if($days_inact>=14){$risk+=30;$flags[]=['icon'=>'calendar_month','color'=>'#c0392b','text'=>"Nghỉ học {$days_inact} ngày liên tiếp — nguy cơ quên kiến thức cao"];$tips[]="Nhắn nhắc nhở ngay! Gợi ý ôn lại Bài ".($weakest['lesson_id']??6)." 15 phút để khởi động.";}elseif($days_inact>=7){$risk+=15;$flags[]=['icon'=>'event_busy','color'=>'#d35400','text'=>"Không hoạt động {$days_inact} ngày — cần nhắc nhở"];$tips[]="Gửi nhắc lịch học: 30 phút/{$tod}.";}elseif($days_inact<=1){$flags[]=['icon'=>'check_circle','color'=>'#1e8449','text'=>"Học đều đặn — gần nhất: ".($days_inact===0?'hôm nay':'hôm qua')];}}else{$risk+=25;$flags[]=['icon'=>'info','color'=>'#c0392b','text'=>'Chưa có phiên học nào — học sinh chưa bắt đầu'];$tips[]="Nhắn tin động viên đăng nhập và bắt đầu Bài 1.";}
    // NF5: Quiz tổng hợp
    if($q_avg10!==null&&$q_cnt>0){
        if($q_avg10<5.0){$risk+=25;$flags[]=['icon'=>'quiz','color'=>'#c0392b','text'=>"Quiz TB ".round($q_avg10,1)."/10 — cần củng cố kiến thức nền"];$tips[]="Gửi tài liệu bổ trợ. Nhắc đọc lý thuyết kỹ trước khi làm Quiz.";}
        elseif($q_avg10<6.5){$risk+=10;$flags[]=['icon'=>'school','color'=>'#b7950b','text'=>"Quiz TB ".round($q_avg10,1)."/10 — còn dư địa cải thiện"];}
        elseif($q_avg10>=8.0){$risk-=8;$flags[]=['icon'=>'emoji_events','color'=>'#1e8449','text'=>"Quiz TB ".round($q_avg10,1)."/10 — nắm kiến thức rất vững!"];}
        // Bài đã hoàn thành nhưng chưa làm quiz
        $done_no_quiz = $l_done - $q_cnt;
        if($done_no_quiz > 0){
            $risk+=10;
            $flags[]=['icon'=>'assignment_late','color'=>'#b7950b',
                'text'=>"Đã xong {$l_done} bài nhưng mới làm Quiz {$q_cnt}/{$l_done} bài — còn {$done_no_quiz} bài chưa kiểm tra"];
            $tips[]="Nhắc làm Quiz các bài còn lại để chắc chắn không bị hổng kiến thức.";
        }
    } elseif($q_cnt===0&&$l_start>0){
        $risk+=15;
        $flags[]=['icon'=>'assignment_late','color'=>'#b7950b','text'=>"Đã học {$l_done} bài nhưng chưa làm bài kiểm tra nào"];
        $tips[]="Nhắc làm Quiz sau mỗi bài để chốt kiến thức.";
    }
    // NF6
    if($weakest!==null){$wl=$weakest['lesson_id'];$ws=round((float)$weakest['score_10'],1);$flags[]=['icon'=>'warning','color'=>'#b7950b','text'=>"Bài {$wl} điểm thấp nhất ({$ws}/10) — cần ưu tiên ôn lại"];$tips[]="Gửi tài liệu bổ trợ Bài {$wl}. Mục tiêu ≥ 7/10 mới chuyển bài.";}
    // NF7
    if($min7<60&&$q_avg10!==null&&$q_avg10<6.0&&$days_inact!==null){$risk+=15;$flags[]=['icon'=>'hourglass_empty','color'=>'#c0392b','text'=>"Chỉ học {$min7} phút/tuần + Quiz thấp — combo rủi ro cao"];}
    // NF8
    if($days_left!==null&&$avg_pct!==null){if($days_left<=30&&$avg_pct<70){$risk+=25;$flags[]=['icon'=>'timer','color'=>'#c0392b','text'=>"Còn ~{$days_left} ngày đến thi, mới đạt ".round($avg_pct)."% chương trình"];$w=$days_left>0?ceil((100-$avg_pct)/max(1,$days_left/7)):0;$tips[]="⏰ Cần hoàn thành ít nhất {$w}%/tuần từ bây giờ.";}elseif($days_left<=14&&$avg_pct<90){$risk+=40;$tips[]="🔥 NƯỚC RÚT: ~{$days_left} ngày còn lại! Ưu tiên dạng bài hay ra trong đề.";}}
    // NF9
    if($has_plan==='Không'||$has_plan==='Chưa có'){$risk+=5;$flags[]=['icon'=>'edit_calendar','color'=>'#b7950b','text'=>"Chưa có kế hoạch học cụ thể"];$tips[]="📋 Lập lịch: 1 bài/tuần, {$tps} phút/{$tod} mỗi ngày.";}
    // NF10
    if($trend>=2&&$s7>0){$risk-=10;$flags[]=['icon'=>'trending_up','color'=>'#1e8449','text'=>"Tuần này tăng ".abs($trend)." buổi so với tuần trước — đang lấy lại phong độ!"];}
    // NF11
    if($flash>=20){$risk-=5;$flags[]=['icon'=>'style','color'=>'#1e8449','text'=>"Đã ôn {$flash} Flashcard — thói quen ghi nhớ chủ động tốt"];}
    $risk=max(0,min(100,$risk));
    $level=$risk>=60?'critical':($risk>=35?'danger':($risk>=15?'warning':'on_track'));
    if(empty($tips)){$tips[]="✅ Đang học ổn định. Gợi ý thử đề thi thử.";$tips[]="💡 Ôn Flashcard 10 phút/ngày trước khi ngủ.";}
    $lessons_no_quiz = max(0, $l_done - $q_cnt);
    return compact('risk','level','flags','tips','predicted','avg_pct','days_inact','q_avg10','q_cnt','l_done','l_start','min7','s7','total_min','target','lessons_no_quiz');
}

$ai_data = [];
foreach ($all_students as $s) {
    $uid = (int)$s['id'];
    $ai_data[$uid] = hm_calc_ai($s,$prog_map[$uid]??[],$quiz_detail[$uid]??[],$quiz_agg[$uid]??[],$sess_map[$uid]??[],$flash_map[$uid]??0);
}

// Học sinh risk >= 70
$high_risk = array_filter($all_students, fn($s)=>($ai_data[(int)$s['id']]['risk']??0)>=70);

// Học sinh chưa học (không có lesson_progress nào) = "xám hoàn toàn"
$never_started = array_filter($all_students, fn($s)=>!isset($prog_map[(int)$s['id']]));

// Xử lý gửi nhắc nhở
$hm_sent = false; $hm_sent_bulk = false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    global $hm_pdo, $conn;
    $hm_act = $_POST['hm_action'] ?? '';
    if ($hm_act === 'send_reminder') {
        $r_uid=(int)($_POST['r_uid']??0); $r_msg=trim($_POST['r_msg']??'');
        if ($r_uid>0 && mb_strlen($r_msg)>=5) {
            if ($use_pdo) { $hm_pdo->prepare("INSERT INTO ai_reminders (student_id,message) VALUES (?,?)")->execute([$r_uid,$r_msg]); }
            else { $st=$conn->prepare("INSERT INTO ai_reminders (student_id,message) VALUES (?,?)"); $st->bind_param('is',$r_uid,$r_msg); $st->execute(); }
            $hm_sent = true;
        }
    } elseif ($hm_act === 'bulk_remind') {
        $uids = array_map('intval', (array)($_POST['bulk_uids'] ?? []));
        $msg  = trim($_POST['bulk_msg'] ?? '');
        if (!empty($uids) && mb_strlen($msg) >= 5) {
            foreach ($uids as $bu) {
                if ($use_pdo) { $hm_pdo->prepare("INSERT INTO ai_reminders (student_id,message) VALUES (?,?)")->execute([$bu,$msg]); }
                else { $st=$conn->prepare("INSERT INTO ai_reminders (student_id,message) VALUES (?,?)"); $st->bind_param('is',$bu,$msg); $st->execute(); }
            }
            $hm_sent_bulk = true;
        }
    }
}

$pal = ['critical'=>['c'=>'#c0392b','bg'=>'#fdecea','l'=>'NGUY HIỂM'],'danger'=>['c'=>'#a04000','bg'=>'#fef5ec','l'=>'CẢNH BÁO'],'warning'=>['c'=>'#856404','bg'=>'#fefde7','l'=>'CHÚ Ý'],'on_track'=>['c'=>'#1e8449','bg'=>'#eafaf1','l'=>'ỔN ĐỊNH']];
?>

<?php if ($hm_sent || $hm_sent_bulk): ?>
<div id="hm-toast" style="position:fixed;bottom:24px;right:24px;z-index:9999;background:#800000;color:#e8d5a3;padding:13px 20px;border-radius:10px;font-size:.85rem;font-weight:700;box-shadow:0 6px 24px rgba(0,0,0,.2);display:flex;align-items:center;gap:8px;animation:hm-ti .35s ease both">
    <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
    <?= $hm_sent_bulk ? 'Đã gửi nhắc nhở hàng loạt!' : 'Đã gửi nhắc nhở thành công!' ?>
</div>
<script>setTimeout(()=>{const t=document.getElementById('hm-toast');if(t){t.style.opacity='0';t.style.transition='opacity .4s';setTimeout(()=>t.remove(),400);}},2800);</script>
<?php endif; ?>

<style>
:root{
    --hm-m:#800000;--hm-mdk:#5a0000;--hm-g:#BF9B30;--hm-gp:#e8d5a3;
    --hm-cr:#fdf8ee;--hm-cbd:#e8d9b8;--hm-cdk:#f0e6cc;
    --hm-r:10px;--hm-mono:'JetBrains Mono',monospace;
    --hm-sh:0 2px 12px rgba(128,0,0,.09);
}
@keyframes hm-ti{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:none}}
@keyframes hm-fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
@keyframes hm-fi{from{opacity:0;transform:translateX(-8px)}to{opacity:1;transform:none}}
@keyframes hm-pulse-border{0%,100%{box-shadow:0 0 0 0 rgba(192,57,43,.7)}50%{box-shadow:0 0 0 3px rgba(192,57,43,.0)}}

/* ── PAGE TITLE ── */
.hm-title{font-family:'Playfair Display',serif;font-size:22px;font-weight:800;color:var(--hm-m);
    letter-spacing:.8px;margin-bottom:5px;display:flex;align-items:center;gap:12px;}
.hm-title::after{content:'';flex:1;height:2px;max-width:120px;
    background:linear-gradient(90deg,var(--hm-g),transparent);border-radius:2px;}
.hm-subtitle{font-size:.79rem;color:#a07040;margin-bottom:20px;}

/* ── ACTION CENTER ── */
.hm-action-center{
    display:grid;grid-template-columns:repeat(3,1fr);gap:14px;
    margin-bottom:20px;
}
@media(max-width:900px){.hm-action-center{grid-template-columns:1fr;}}
.hm-ac{
    border-radius:var(--hm-r);padding:18px 20px;
    display:flex;flex-direction:column;gap:6px;
    position:relative;overflow:hidden;
    animation:hm-fu .4s ease both;box-shadow:var(--hm-sh);
}
.hm-ac.red  {background:#fff0ee;border:1.5px solid #f5b7b1;}
.hm-ac.amber{background:#fffbf0;border:1.5px solid #f9e4a0;}
.hm-ac.blue {background:#f0f8ff;border:1.5px solid #aed6f1;}
.hm-ac-stripe{position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:4px 0 0 4px;}
.hm-ac.red  .hm-ac-stripe{background:#c0392b;}
.hm-ac.amber .hm-ac-stripe{background:#d68910;}
.hm-ac.blue  .hm-ac-stripe{background:#2980b9;}
.hm-ac-header{display:flex;align-items:center;gap:8px;}
.hm-ac-ico{width:38px;height:38px;border-radius:9px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;}
.hm-ac.red  .hm-ac-ico{background:#fdecea;color:#c0392b;}
.hm-ac.amber .hm-ac-ico{background:#fef9e7;color:#d68910;}
.hm-ac.blue  .hm-ac-ico{background:#e8f4fd;color:#2980b9;}
.hm-ac-ico .material-symbols-outlined{font-size:20px;}
.hm-ac-lbl{font-size:.62rem;font-weight:900;text-transform:uppercase;letter-spacing:.9px;color:#9a7040;}
.hm-ac-val{font-family:var(--hm-mono);font-size:1.8rem;font-weight:700;line-height:1.1;}
.hm-ac.red   .hm-ac-val{color:#c0392b;}
.hm-ac.amber .hm-ac-val{color:#d68910;}
.hm-ac.blue  .hm-ac-val{color:#2980b9;}
.hm-ac-desc{font-size:.75rem;color:#9a7040;line-height:1.4;}
.hm-ac-action{margin-top:4px;}
.hm-ac-btn{
    padding:6px 14px;border-radius:7px;border:1.5px solid var(--hm-cbd);
    background:#fff;cursor:pointer;font-family:'Nunito',sans-serif;
    font-size:.76rem;font-weight:800;display:inline-flex;align-items:center;gap:5px;
    transition:all .18s;
}
.hm-ac.red   .hm-ac-btn{color:#c0392b;border-color:#f5b7b1;}
.hm-ac.amber .hm-ac-btn{color:#d68910;border-color:#f9e4a0;}
.hm-ac.blue  .hm-ac-btn{color:#2980b9;border-color:#aed6f1;}
.hm-ac.red   .hm-ac-btn:hover{background:#c0392b;color:#fff;border-color:#c0392b;}
.hm-ac.amber .hm-ac-btn:hover{background:#d68910;color:#fff;border-color:#d68910;}
.hm-ac.blue  .hm-ac-btn:hover{background:#2980b9;color:#fff;border-color:#2980b9;}
.hm-ac-btn .material-symbols-outlined{font-size:14px;}

/* Risk list mini */
.hm-risk-mini{margin-top:10px;display:flex;flex-direction:column;gap:5px;}
.hm-risk-mini-row{display:flex;align-items:center;gap:8px;padding:5px 8px;
    background:#fff;border-radius:6px;border:1px solid #f5b7b1;}
.hm-risk-mini-name{font-size:.76rem;font-weight:800;color:var(--hm-m);flex:1;}
.hm-risk-mini-score{font-family:var(--hm-mono);font-size:.76rem;color:#c0392b;font-weight:700;}

/* ── BULK REMIND MODAL ── */
.hm-bulk-modal{display:none;position:fixed;inset:0;z-index:9000;
    background:rgba(0,0,0,.45);align-items:center;justify-content:center;}
.hm-bulk-modal.open{display:flex;}
.hm-bulk-box{background:#fff;border-radius:14px;padding:28px 30px;
    width:min(560px,94vw);max-height:82vh;overflow-y:auto;
    box-shadow:0 20px 60px rgba(0,0,0,.22);
    animation:hm-fu .3s ease both;}
.hm-bulk-title{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;
    color:var(--hm-m);margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.hm-bulk-list{max-height:220px;overflow-y:auto;border:1px solid var(--hm-cbd);
    border-radius:8px;margin-bottom:14px;}
.hm-bulk-item{display:flex;align-items:center;gap:10px;padding:9px 12px;
    border-bottom:1px solid var(--hm-cdk);cursor:pointer;}
.hm-bulk-item:last-child{border-bottom:none;}
.hm-bulk-item:hover{background:var(--hm-cr);}
.hm-bulk-item label{font-size:.83rem;font-weight:700;color:var(--hm-m);cursor:pointer;flex:1;}
.hm-bulk-item input[type=checkbox]{accent-color:var(--hm-m);width:16px;height:16px;}
.hm-bulk-textarea{width:100%;border:1.5px solid var(--hm-cbd);border-radius:8px;
    padding:10px 13px;font-family:'Nunito',sans-serif;font-size:.84rem;
    resize:vertical;min-height:80px;outline:none;transition:border .2s;}
.hm-bulk-textarea:focus{border-color:var(--hm-m);}
.hm-bulk-footer{display:flex;gap:8px;justify-content:flex-end;margin-top:14px;}

/* ── ANALYTICS ── */
.hm-analytics{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;}
@media(max-width:800px){.hm-analytics{grid-template-columns:1fr;}}
.hm-panel{background:#fff;border:1px solid var(--hm-cbd);border-radius:var(--hm-r);
    padding:18px 20px;box-shadow:var(--hm-sh);}
.hm-panel-title{font-size:.66rem;font-weight:900;text-transform:uppercase;letter-spacing:1.1px;
    color:var(--hm-g);margin-bottom:12px;display:flex;align-items:center;gap:7px;}
.hm-panel-title::after{content:'';flex:1;height:1px;background:var(--hm-cbd);}
.hm-panel-title .material-symbols-outlined{font-size:13px;}

/* DOW chart */
.hm-dow{display:flex;gap:4px;align-items:flex-end;margin-top:6px;}
.hm-dow-col{display:flex;flex-direction:column;align-items:center;gap:3px;flex:1;}
.hm-dow-bar{width:100%;border-radius:4px 4px 0 0;transition:height .3s;min-height:4px;}
.hm-dow-lbl{font-size:.61rem;color:#b08060;font-weight:700;}
.hm-dow-cnt{font-family:var(--hm-mono);font-size:.63rem;color:var(--hm-m);}

/* Lesson bars */
.hm-ls-row{display:flex;align-items:center;gap:10px;padding:7px 0;
    border-bottom:1px solid var(--hm-cdk);}
.hm-ls-row:last-child{border-bottom:none;}
.hm-ls-name{font-size:.79rem;font-weight:800;color:var(--hm-m);min-width:48px;}
.hm-ls-bar-w{flex:1;height:8px;background:var(--hm-cdk);border-radius:99px;overflow:hidden;}
.hm-ls-bar{height:100%;border-radius:99px;
    background:linear-gradient(90deg,var(--hm-m),var(--hm-g));}
.hm-ls-meta{font-size:.67rem;color:#b08060;white-space:nowrap;}

/* ── TABLE ── */
.hm-wrap{overflow-x:auto;}
.hm-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:.82rem;}
.hm-tbl th{background:var(--hm-cdk);padding:9px 11px;text-align:left;
    font-size:.61rem;font-weight:900;text-transform:uppercase;letter-spacing:.8px;
    color:#9a6a30;border-bottom:2px solid #d4c07a;white-space:nowrap;}
.hm-tbl th.c{text-align:center;}
.hm-tbl td{padding:10px 10px;border-bottom:1px solid var(--hm-cdk);vertical-align:middle;}

/* VIP row */
.hm-tbl tr.is-vip td{background:linear-gradient(90deg,rgba(212,160,23,.07) 0%,rgba(212,160,23,.03) 100%);}
.hm-tbl tr.is-vip td:first-child{border-left:3px solid #BF9B30;}
.hm-tbl tr.is-vip:hover td{background:rgba(248,235,180,.35);}
/* Normal row */
.hm-tbl tr.no-prof td{opacity:.65;}
.hm-tbl tr:not(.is-vip):hover td{background:rgba(248,240,220,.5);}

/* Name cell */
.hm-name{font-weight:800;color:var(--hm-m);font-size:.85rem;}
.hm-name-sub{font-size:.68rem;color:#b08060;margin-top:1px;}
.hm-bdg{font-size:.57rem;padding:2px 6px;border-radius:20px;font-weight:800;}
.hm-bdg.vip   {background:linear-gradient(90deg,#d4a017,#f1c40f);color:#1a0000;}
.hm-bdg.vreq  {background:#fef9e7;color:#d68910;border:1px solid #f1c40f;}
.hm-bdg.locked{background:#fdecea;color:#c0392b;}
.hm-bdg.noprof{background:#f5f5f5;color:#bbb;}
.hm-num-cell{font-size:.67rem;font-weight:700;color:#c0a878;}

/* ── HEATMAP CELL ── */
.hm-cell{width:36px;height:36px;border-radius:7px;margin:0 auto;
    display:flex;align-items:center;justify-content:center;
    font-size:12px;transition:transform .15s;position:relative;cursor:default;}
.hm-cell:hover{transform:scale(1.15);}
/* done */
.hm-cell.done{background:#1e8449;color:#fff;}
/* none */
.hm-cell.none{background:#e8e8e8;color:#ccc;}
/* active — gradient vàng nhạt → cam đậm dựa theo % */
.hm-cell.active-lo{background:#fef9c3;color:#92600a;}
.hm-cell.active-md{background:#fcd34d;color:#78350f;}
.hm-cell.active-hi{background:#f59e0b;color:#fff;}
/* quiz fail — viền đỏ nhấp nháy */
.hm-cell.quiz-fail{
    outline:2px solid #e74c3c;
    animation:hm-pulse-border 1.4s infinite;
}
.hm-cell .hm-warn-dot{
    position:absolute;top:-3px;right:-3px;
    width:9px;height:9px;border-radius:50%;background:#e74c3c;
    border:1.5px solid #fff;
}

/* Risk badge */
.hm-risk{display:inline-flex;align-items:center;gap:3px;
    font-family:var(--hm-mono);font-size:.74rem;font-weight:700;
    padding:3px 8px;border-radius:20px;white-space:nowrap;}
.hm-risk.critical{background:#fdecea;color:#922b21;}
.hm-risk.danger  {background:#fef5ec;color:#a04000;}
.hm-risk.warning {background:#fefde7;color:#856404;}
.hm-risk.on_track{background:#eafaf1;color:#1e8449;}

/* Pbar inline */
.hm-pbar-w{height:6px;background:var(--hm-cdk);border-radius:99px;overflow:hidden;flex:1;min-width:55px;}
.hm-pbar{height:100%;border-radius:99px;}

/* Chip */
.hm-chip{display:inline-flex;align-items:center;padding:2px 7px;border-radius:20px;
    font-family:var(--hm-mono);font-size:.72rem;font-weight:700;}

/* Buttons */
.hm-btn{padding:5px 10px;border-radius:7px;border:1.5px solid var(--hm-cbd);
    background:#fff;cursor:pointer;font-family:'Nunito',sans-serif;
    font-size:.73rem;font-weight:800;color:var(--hm-m);
    display:inline-flex;align-items:center;gap:4px;transition:all .18s;white-space:nowrap;}
.hm-btn:hover{background:var(--hm-m);color:#fff;border-color:var(--hm-m);}
.hm-btn .material-symbols-outlined{font-size:13px;}
.hm-btn-send{padding:6px 15px;border:none;border-radius:7px;
    background:var(--hm-m);color:var(--hm-gp);font-family:'Nunito',sans-serif;
    font-size:.8rem;font-weight:800;cursor:pointer;
    display:inline-flex;align-items:center;gap:5px;transition:background .18s;}
.hm-btn-send:hover{background:var(--hm-mdk);}
.hm-btn-send .material-symbols-outlined{font-size:13px;}
.hm-btn-cancel{padding:6px 12px;border:1.5px solid var(--hm-cbd);border-radius:7px;
    background:#fff;color:#9a6a30;font-family:'Nunito',sans-serif;font-size:.8rem;font-weight:700;cursor:pointer;}

/* Remind row */
.hm-remind-td{padding:2px 10px 10px !important;}
.hm-remind-form{display:flex;flex-direction:column;gap:7px;background:var(--hm-cr);
    border:1px solid var(--hm-cbd);border-radius:8px;padding:13px 15px;
    animation:hm-fu .25s ease both;}
.hm-remind-form textarea{border:1.5px solid var(--hm-cbd);border-radius:7px;
    padding:8px 12px;font-family:'Nunito',sans-serif;font-size:.82rem;
    resize:vertical;min-height:64px;background:#fff;outline:none;transition:border .2s;}
.hm-remind-form textarea:focus{border-color:var(--hm-m);}
.hm-remind-footer{display:flex;gap:7px;justify-content:flex-end;}

/* VIP Accordion */
.hm-acc-row{display:none;}
.hm-acc-row.open{display:table-row;}
.hm-acc-td{padding:0 8px 16px !important;}
.hm-acc-panel{
    background:linear-gradient(135deg,#fdf6e8 0%,#fffdf6 100%);
    border:1.5px solid #d4a017;border-radius:12px;
    padding:22px 26px;position:relative;overflow:hidden;
    animation:hm-fu .35s cubic-bezier(.22,1,.36,1) both;
    box-shadow:0 4px 20px rgba(212,160,23,.12);
}
.hm-acc-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(90deg,#BF9B30,#f1c40f,#BF9B30);}
.hm-acc-hdr{display:flex;align-items:center;gap:10px;margin-bottom:18px;
    padding-bottom:14px;border-bottom:1px solid rgba(212,160,23,.25);}
.hm-acc-badge{
    background:linear-gradient(135deg,#BF9B30,#f1c40f);color:#1a0000;
    padding:4px 14px;border-radius:20px;font-size:.66rem;font-weight:900;
    letter-spacing:.6px;white-space:nowrap;
}
.hm-acc-title{
    font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700;
    color:var(--hm-m);
}
.hm-chevron{color:#c0a878;font-size:16px !important;margin-left:3px;
    transition:transform .28s;display:inline-block;vertical-align:middle;}
.hm-chevron.open{transform:rotate(180deg);}

/* KPIs — 8 ô giống ảnh */
.hm-kpi-row{
    display:grid;
    grid-template-columns:repeat(8,1fr);
    gap:8px;margin-bottom:16px;
}
@media(max-width:1100px){.hm-kpi-row{grid-template-columns:repeat(4,1fr);}}
@media(max-width:700px) {.hm-kpi-row{grid-template-columns:repeat(2,1fr);}}
.hm-kpi{background:#fff;border:1px solid var(--hm-cbd);border-radius:8px;
    padding:11px 13px;text-align:center;
    transition:box-shadow .18s;}
.hm-kpi:hover{box-shadow:0 2px 10px rgba(128,0,0,.08);}
.hm-kpi-lbl{font-size:.57rem;font-weight:900;text-transform:uppercase;
    letter-spacing:.9px;color:#b08060;margin-bottom:4px;}
.hm-kpi-val{font-family:var(--hm-mono);font-size:1.55rem;font-weight:700;line-height:1.05;}
.hm-kpi-val.red{color:#c0392b;}.hm-kpi-val.ora{color:#a84300;}
.hm-kpi-val.yel{color:#856404;}.hm-kpi-val.grn{color:#1e8449;}
.hm-kpi-val.nil{color:#ccc;font-size:1rem;}
.hm-kpi-sub{font-size:.6rem;color:#c0a878;margin-top:3px;line-height:1.3;}

/* AI 2-col detail */
.hm-ai-2col{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px;}
@media(max-width:700px){.hm-ai-2col{grid-template-columns:1fr;}}
.hm-ai-card{background:#fff;border:1px solid var(--hm-cbd);border-radius:9px;padding:14px 16px;}

/* Flags */
.hm-flag{display:flex;align-items:center;gap:9px;padding:8px 12px;border-radius:8px;
    margin-bottom:6px;background:var(--hm-cr);border-left:3px solid var(--fc,var(--hm-cbd));
    font-size:.8rem;color:#3a2010;animation:hm-fi .3s ease both;opacity:0;line-height:1.4;}
.hm-flag:nth-child(1){animation-delay:.04s}.hm-flag:nth-child(2){animation-delay:.08s}
.hm-flag:nth-child(3){animation-delay:.12s}.hm-flag:nth-child(4){animation-delay:.16s}
.hm-flag:nth-child(5){animation-delay:.20s}
.hm-flag-ico{width:28px;height:28px;border-radius:7px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;}
.hm-flag-ico .material-symbols-outlined{font-size:14px;}

/* Tips */
.hm-tip{padding:10px 14px;margin-bottom:7px;border-radius:8px;background:#fff;
    border:1px solid var(--hm-cbd);border-left:3px solid var(--hm-g);
    font-size:.82rem;color:#3a2010;line-height:1.65;}

/* Pred box — đỏ maroon như ảnh */
.hm-pred{background:linear-gradient(135deg,#5a0000 0%,#800000 100%);border-radius:9px;
    padding:16px 20px;color:#fff;border:1.5px solid rgba(191,155,48,.3);}
.hm-pred-lbl{font-size:.59rem;opacity:.55;text-transform:uppercase;letter-spacing:1.1px;font-weight:900;margin-bottom:4px;}
.hm-pred-num{font-family:var(--hm-mono);font-size:2.2rem;font-weight:700;
    color:var(--hm-gp);line-height:1;margin-bottom:4px;}
.hm-pred-formula{font-size:.65rem;opacity:.5;margin-bottom:10px;}
.hm-pred-track{height:7px;background:rgba(255,255,255,.12);border-radius:99px;overflow:hidden;}
.hm-pred-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--hm-g),#e67e22);}
.hm-pred-marks{display:flex;justify-content:space-between;font-size:.59rem;opacity:.38;margin-top:3px;}

/* Quiz mini table */
.hm-qtbl{width:100%;border-collapse:collapse;font-size:.8rem;}
.hm-qtbl th{background:var(--hm-cdk);padding:6px 10px;text-align:left;
    font-size:.6rem;font-weight:900;text-transform:uppercase;letter-spacing:.7px;
    color:#9a6a30;border-bottom:1.5px solid #d4c07a;}
.hm-qtbl td{padding:7px 10px;border-bottom:1px solid var(--hm-cdk);}
.hm-qtbl tr:last-child td{border-bottom:none;}
.hm-mbar-w{display:inline-block;vertical-align:middle;margin-left:6px;
    width:56px;height:5px;background:var(--hm-cdk);border-radius:99px;overflow:hidden;}
.hm-mbar{height:100%;border-radius:99px;}

/* Legend */
.hm-legend{display:flex;gap:12px;flex-wrap:wrap;font-size:.71rem;color:#9a6a30;
    margin-bottom:10px;align-items:center;}
.hm-leg-dot{width:13px;height:13px;border-radius:3px;display:inline-block;
    vertical-align:middle;margin-right:3px;}

/* Q&A badge */
.hm-qa-badge{background:#fdecea;color:#c0392b;font-size:.62rem;font-weight:800;
    padding:2px 7px;border-radius:20px;}

/* Divider */
.hm-div{height:1px;background:linear-gradient(90deg,transparent,var(--hm-cbd),transparent);
    margin:16px 0;}
</style>

<!-- ════════════════════════════════════════════
     TIÊU ĐỀ
════════════════════════════════════════════ -->
<div class="hm-title">
    <span class="material-symbols-outlined" style="font-size:21px;color:var(--hm-g)">grid_on</span>
    Theo dõi tiến độ & AI
</div>
<div class="hm-subtitle">
    Cập nhật lúc <?= date('H:i, d/m/Y') ?>
    &nbsp;·&nbsp; <?= $stat_total ?> học sinh (<?= $stat_vip_count ?> VIP)
    &nbsp;·&nbsp; <?= $stat_has_profile ?> hồ sơ đầy đủ
    &nbsp;·&nbsp; <?= $stat_started ?> đã bắt đầu học
    <?php if ($stat_vip_pending): ?>
    &nbsp;·&nbsp; <strong style="color:#d68910">⭐ <?= $stat_vip_pending ?> yêu cầu VIP chờ duyệt</strong>
    <?php endif; ?>
</div>

<!-- ════════════════════════════════════════════
     ACTION CENTER — 3 CỘT THAY THẾ 8 THẺ
════════════════════════════════════════════ -->
<div class="hm-action-center">

    <!-- Cột 1: Q&A + Risk cao -->
    <div class="hm-ac red" style="animation-delay:.04s">
        <div class="hm-ac-stripe"></div>
        <div class="hm-ac-header">
            <div class="hm-ac-ico"><span class="material-symbols-outlined">emergency</span></div>
            <div>
                <div class="hm-ac-lbl">Cần xử lý ngay</div>
                <div class="hm-ac-val"><?= count($high_risk) ?></div>
            </div>
        </div>
        <div class="hm-ac-desc">
            <?= count($high_risk) ?> sĩ tử có <strong>Risk Score ≥ 70</strong> cần can thiệp gấp
            <?php if ($stat_qa_pending > 0): ?>
            <br>+ <strong><?= $stat_qa_pending ?> câu Q&A</strong> chưa trả lời<?= $stat_qa_vip_pending > 0 ? " (trong đó {$stat_qa_vip_pending} từ VIP)" : '' ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($high_risk)): ?>
        <div class="hm-risk-mini">
            <?php foreach (array_slice($high_risk, 0, 3) as $hr):
                $hr_uid = (int)$hr['id'];
                $hr_name = $hr['sp_fullname'] ?: $hr['u_fullname'];
            ?>
            <div class="hm-risk-mini-row">
                <span class="hm-risk-mini-name"><?= htmlspecialchars($hr_name) ?></span>
                <span class="hm-risk-mini-score"><?= $ai_data[$hr_uid]['risk'] ?>/100</span>
                <button class="hm-btn" style="font-size:.68rem;padding:3px 8px" onclick="hmToggleRemind(<?= $hr_uid ?>,'tbl')">
                    <span class="material-symbols-outlined">notifications</span>Nhắc
                </button>
            </div>
            <?php endforeach; ?>
            <?php if (count($high_risk) > 3): ?>
            <div style="font-size:.7rem;color:#c0392b;font-weight:700;padding:4px 8px">
                + <?= count($high_risk) - 3 ?> học sinh khác (xem bảng bên dưới)
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="hm-ac-action">
            <span style="font-size:.78rem;color:#1e8449;font-weight:700">Không có nguy cơ cao</span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cột 2: Nhắc nhở hàng loạt -->
    <div class="hm-ac amber" style="animation-delay:.1s">
        <div class="hm-ac-stripe"></div>
        <div class="hm-ac-header">
            <div class="hm-ac-ico"><span class="material-symbols-outlined">campaign</span></div>
            <div>
                <div class="hm-ac-lbl">Nhắc nhở hàng loạt</div>
                <div class="hm-ac-val"><?= count($never_started) ?></div>
            </div>
        </div>
        <div class="hm-ac-desc">
            <strong><?= count($never_started) ?> học sinh</strong> chưa bắt đầu học (ô xám toàn bộ)
            <br>Chọn và gửi thông báo đến tất cả cùng một lúc
        </div>
        <div class="hm-ac-action">
            <button class="hm-ac-btn" onclick="openBulkModal()">
                <span class="material-symbols-outlined">send</span>
                Gửi nhắc nhở hàng loạt
            </button>
        </div>
    </div>

    <!-- Cột 3: Tổng quan học tập -->
    <div class="hm-ac blue" style="animation-delay:.16s">
        <div class="hm-ac-stripe"></div>
        <div class="hm-ac-header">
            <div class="hm-ac-ico"><span class="material-symbols-outlined">insights</span></div>
            <div>
                <div class="hm-ac-lbl">Tổng quan lớp</div>
                <div class="hm-ac-val"><?= $stat_active_7d ?></div>
            </div>
        </div>
        <div class="hm-ac-desc">
            Học sinh hoạt động 7 ngày qua
        </div>
        <div style="margin-top:8px;display:grid;grid-template-columns:1fr 1fr;gap:6px">
            <div style="background:#fff;border-radius:7px;padding:7px 10px;border:1px solid #aed6f1">
                <div style="font-size:.59rem;font-weight:900;text-transform:uppercase;letter-spacing:.7px;color:#7fb3d3">Phiên học</div>
                <div style="font-family:var(--hm-mono);font-size:1.1rem;font-weight:700;color:#2980b9"><?= $stat_sessions ?></div>
            </div>
            <div style="background:#fff;border-radius:7px;padding:7px 10px;border:1px solid #aed6f1">
                <div style="font-size:.59rem;font-weight:900;text-transform:uppercase;letter-spacing:.7px;color:#7fb3d3">Quiz TB</div>
                <div style="font-family:var(--hm-mono);font-size:1.1rem;font-weight:700;color:#2980b9"><?= $stat_quiz_avg ?? '—' ?>/10</div>
            </div>
            <div style="background:#fff;border-radius:7px;padding:7px 10px;border:1px solid #aed6f1">
                <div style="font-size:.59rem;font-weight:900;text-transform:uppercase;letter-spacing:.7px;color:#7fb3d3">Thời gian</div>
                <div style="font-family:var(--hm-mono);font-size:1.1rem;font-weight:700;color:#2980b9"><?= $stat_minutes ?>ph</div>
            </div>
            <div style="background:#fff;border-radius:7px;padding:7px 10px;border:1px solid #aed6f1">
                <div style="font-size:.59rem;font-weight:900;text-transform:uppercase;letter-spacing:.7px;color:#7fb3d3">Flashcard</div>
                <div style="font-family:var(--hm-mono);font-size:1.1rem;font-weight:700;color:#2980b9"><?= $stat_flash ?></div>
            </div>
        </div>
    </div>

</div>

<!-- ════════════════════════════════════════════
     BULK REMIND MODAL
════════════════════════════════════════════ -->
<div class="hm-bulk-modal" id="hm-bulk-modal">
    <div class="hm-bulk-box">
        <div class="hm-bulk-title">
            <span class="material-symbols-outlined" style="color:var(--hm-g)">campaign</span>
            Gửi nhắc nhở hàng loạt
        </div>
        <form method="post">
            <input type="hidden" name="hm_action" value="bulk_remind">
            <div style="font-size:.75rem;font-weight:800;color:#9a6a30;margin-bottom:6px">
                Chọn học sinh nhận nhắc nhở:
                <button type="button" onclick="bulkSelectAll(true)" style="font-size:.68rem;padding:2px 8px;border:1px solid var(--hm-cbd);border-radius:5px;background:#fff;cursor:pointer;color:var(--hm-m);margin-left:6px">Chọn tất</button>
                <button type="button" onclick="bulkSelectAll(false)" style="font-size:.68rem;padding:2px 8px;border:1px solid var(--hm-cbd);border-radius:5px;background:#fff;cursor:pointer;color:#9a6a30;margin-left:4px">Bỏ chọn</button>
            </div>
            <div class="hm-bulk-list" id="hm-bulk-list">
                <?php foreach ($never_started as $ns):
                    $ns_name = $ns['sp_fullname'] ?: $ns['u_fullname'];
                ?>
                <label class="hm-bulk-item">
                    <input type="checkbox" name="bulk_uids[]" value="<?= $ns['id'] ?>" checked>
                    <span><?= htmlspecialchars($ns_name) ?></span>
                    <span style="font-size:.68rem;color:#b08060"><?= $ns['sp_fullname'] ? htmlspecialchars($ns['school']??'') : '(Chưa có hồ sơ)' ?></span>
                </label>
                <?php endforeach; ?>
                <?php if (empty($never_started)): ?>
                <div style="padding:14px;text-align:center;color:#ccc;font-style:italic;font-size:.8rem">
                    Tất cả học sinh đã bắt đầu học!
                </div>
                <?php endif; ?>
            </div>
            <div style="font-size:.75rem;font-weight:800;color:#9a6a30;margin-bottom:6px">Nội dung tin nhắn:</div>
            <textarea class="hm-bulk-textarea" name="bulk_msg"
                placeholder="Ví dụ: Chào bạn! Bạn chưa bắt đầu học bài nào. Hãy vào học ngay hôm nay để không bị tụt hậu nhé! 📚"></textarea>
            <div class="hm-bulk-footer">
                <button type="button" class="hm-btn-cancel" onclick="closeBulkModal()">Hủy</button>
                <button type="submit" class="hm-btn-send">
                    <span class="material-symbols-outlined">send</span>
                    Gửi hàng loạt
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ════════════════════════════════════════════
     PHÂN TÍCH THỐNG KÊ
════════════════════════════════════════════ -->
<div class="hm-analytics">
    <!-- Hoạt động DOW -->
    <div class="hm-panel">
        <div class="hm-panel-title">
            <span class="material-symbols-outlined">bar_chart</span>
            Hoạt động theo ngày trong tuần
        </div>
        <?php if (empty($dow_map)): ?>
        <div style="text-align:center;padding:18px;color:#ccc;font-size:.8rem;font-style:italic">Chưa có dữ liệu phiên học</div>
        <?php else: ?>
        <div class="hm-dow">
            <?php foreach ($dow_labels as $dow => $lbl):
                $cnt = $dow_map[$dow] ?? 0;
                $h   = $max_dow > 0 ? max(6, round($cnt/$max_dow*76)) : 6;
                $int = $max_dow > 0 ? $cnt/$max_dow : 0;
                $col = "rgba(128,0,0,".round(0.18+$int*0.82,2).")";
            ?>
            <div class="hm-dow-col">
                <div class="hm-dow-cnt"><?= $cnt ?: '' ?></div>
                <div class="hm-dow-bar" style="height:<?= $h ?>px;background:<?= $col ?>"></div>
                <div class="hm-dow-lbl"><?= $lbl ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="font-size:.67rem;color:#c0a878;margin-top:7px">Tổng <?= $stat_sessions ?> phiên · <?= $stat_minutes ?> phút</div>
        <?php endif; ?>
    </div>

    <!-- Bài học phổ biến -->
    <div class="hm-panel">
        <div class="hm-panel-title">
            <span class="material-symbols-outlined">menu_book</span>
            Bài học — Mức độ tham gia
        </div>
        <?php
        $lp_pop = hm_query("SELECT lesson_id,COUNT(DISTINCT user_id) AS learners,ROUND(SUM(duration_s)/60) AS total_min,COUNT(*) AS sessions FROM study_sessions GROUP BY lesson_id ORDER BY learners DESC");
        if (empty($lp_pop)): ?>
        <div style="text-align:center;padding:18px;color:#ccc;font-size:.8rem;font-style:italic">Chưa có dữ liệu</div>
        <?php else:
            $max_l = max(array_column($lp_pop,'learners'));
        ?>
        <?php foreach ($lp_pop as $lp):
            $pct = $max_l > 0 ? round($lp['learners']/$max_l*100) : 0;
        ?>
        <div class="hm-ls-row">
            <div class="hm-ls-name">Bài <?= $lp['lesson_id'] ?></div>
            <div class="hm-ls-bar-w"><div class="hm-ls-bar" style="width:<?= $pct ?>%"></div></div>
            <div class="hm-ls-meta"><?= $lp['learners'] ?> người · <?= $lp['total_min'] ?>ph · <?= $lp['sessions'] ?> phiên</div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ════════════════════════════════════════════
     BẢN ĐỒ NHIỆT — BẢNG CHÍNH
════════════════════════════════════════════ -->
<div class="hm-panel">
    <div class="hm-panel-title">
        <span class="material-symbols-outlined">grid_view</span>
        Bản đồ nhiệt tiến độ toàn lớp
        <span style="font-weight:400;color:#b08060;text-transform:none;letter-spacing:0;font-size:.71rem">
            — 👑 VIP: click để xem phân tích AI đầy đủ &nbsp;|&nbsp; Học sinh thường: chỉ xem trạng thái
        </span>
    </div>

    <div class="hm-legend">
        <span><span class="hm-leg-dot" style="background:#1e8449"></span>Hoàn thành</span>
        <span><span class="hm-leg-dot" style="background:#fcd34d"></span>Đang học</span>
        <span><span class="hm-leg-dot" style="background:#f59e0b"></span>Đang học (>70%)</span>
        <span><span class="hm-leg-dot" style="background:#e8e8e8"></span>Chưa vào</span>
        <span style="display:inline-flex;align-items:center;gap:3px">
            <span class="hm-leg-dot" style="background:#e8e8e8;outline:2px solid #e74c3c;border-radius:3px"></span>Quiz &lt;5.0 ⚠️
        </span>
        <span style="margin-left:auto;display:inline-flex;align-items:center;gap:5px">
            <span style="font-size:.68rem;color:#BF9B30;font-weight:800">▐</span>
            <span style="font-size:.68rem;color:#9a6a30">Dòng viền vàng = VIP</span>
        </span>
    </div>

    <div class="hm-wrap">
    <table class="hm-tbl" id="hm-main-tbl">
        <thead>
            <tr>
                <th style="min-width:16px">#</th>
                <th style="min-width:175px">Học sinh</th>
                <?php foreach ($lesson_ids as $lid): ?>
                <th class="c" style="min-width:50px" title="Trạng thái Bài <?= $lid ?>">Bài <?= $lid ?></th>
                <?php endforeach; ?>
                <th class="c" style="min-width:110px">Tiến độ</th>
                <th class="c">Risk</th>
                <th class="c">Dự báo</th>
                <th class="c">Quiz TB</th>
                <th class="c">Buổi/7d</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rn = 0;
        foreach ($all_students as $s):
            $rn++;
            $uid      = (int)$s['id'];
            $ai       = $ai_data[$uid];
            $name     = $s['sp_fullname'] ?: $s['u_fullname'];
            $has_prof = (bool)$s['sp_fullname'];
            $is_vip       = (bool)$s['is_vip'];
            $has_vreq     = in_array($uid, $vip_req_list);
            $is_vip_or_req = $is_vip || $has_vreq; // accordion mở cho cả 2
            $pend     = $pend_map[$uid] ?? 0;
            $avg_pct  = $ai['avg_pct'];
            $bar_pct  = $avg_pct !== null ? min(100, round($avg_pct)) : 0;
            $bar_col  = $bar_pct >= 75 ? '#1e8449' : ($bar_pct >= 40 ? '#d68910' : ($bar_pct > 0 ? '#c0392b' : '#ccc'));
            $cls = fn($v,$g,$y) => $v===null?'nil':($v>=$g?'grn':($v>=$y?'yel':'red'));
            $cls_quiz = $cls($ai['q_avg10'],7.0,5.5);
        ?>
        <tr id="hm-row-<?= $uid ?>"
            class="<?= $is_vip_or_req ? 'is-vip' : '' ?> <?= !$has_prof ? 'no-prof' : '' ?>">

            <!-- # -->
            <td><span class="hm-num-cell"><?= $rn ?></span></td>

            <!-- Tên -->
            <td>
                <div style="display:flex;align-items:flex-start;gap:8px">
                    <?php if ($is_vip_or_req && $has_prof): ?>
                    <span style="font-size:1.1rem;line-height:1.2;flex-shrink:0;cursor:pointer"
                          onclick="hmToggleAcc(<?= $uid ?>)" title="Xem phân tích AI Mentor">👑</span>
                    <?php endif; ?>
                    <div>
                        <div class="hm-name" style="<?= ($is_vip_or_req && $has_prof) ? 'cursor:pointer' : '' ?>"
                             <?= ($is_vip_or_req && $has_prof) ? "onclick=\"hmToggleAcc({$uid})\"" : '' ?>>
                            <?= htmlspecialchars($name) ?>
                            <?php if ($is_vip): ?>
                                <span class="hm-bdg vip">👑 VIP</span>
                                <span class="material-symbols-outlined hm-chevron" id="hm-ch-<?= $uid ?>" style="font-size:15px;vertical-align:middle">expand_more</span>
                            <?php elseif ($has_vreq): ?>
                                <span class="hm-bdg vreq">⭐ Chờ VIP</span>
                                <?php if ($has_prof): ?>
                                <span class="material-symbols-outlined hm-chevron" id="hm-ch-<?= $uid ?>" style="font-size:15px;vertical-align:middle">expand_more</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($s['acc_status']==='locked'): ?>
                                <span class="hm-bdg locked">KHÓA</span>
                            <?php endif; ?>
                        </div>
                        <div class="hm-name-sub">
                            <?php if (!$has_prof): ?>
                                <span class="hm-bdg noprof">Chưa có hồ sơ</span>
                            <?php else: ?>
                                <?= htmlspecialchars($s['school'] ?? 'Chưa có trường') ?>
                                <?= $s['grade'] ? ' · Lớp '.$s['grade'] : '' ?>
                                <?= $s['target_score'] ? ' · MT: '.$s['target_score'].'đ' : '' ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </td>

            <!-- Ô bài học — heatmap thông minh -->
            <?php foreach ($lesson_ids as $lid):
                $cell   = $matrix[$uid][$lid] ?? null;
                $quiz_s = $quiz_per_lesson[$uid][$lid] ?? null;
                $quiz_fail = ($quiz_s !== null && $quiz_s < 5.0);

                if ($cell === null) {
                    // Chưa vào
                    $dot_cls  = 'none';
                    $dot_ico  = '';
                    $tip_txt  = 'Chưa học';
                } elseif ($cell['completed']) {
                    // Hoàn thành
                    $dot_cls  = 'done' . ($quiz_fail ? ' quiz-fail' : '');
                    $dot_ico  = 'check';
                    $tip_txt  = 'Đã xong (100%)' . ($quiz_fail ? ' — Quiz '.round($quiz_s,1).'/10 ⚠️' : '');
                } else {
                    // Đang học — gradient theo %
                    $pct_cell = $cell['pct'];
                    if ($pct_cell >= 70) {
                        $dot_cls = 'active-hi' . ($quiz_fail ? ' quiz-fail' : '');
                        $dot_ico = 'play_arrow';
                    } elseif ($pct_cell >= 30) {
                        $dot_cls = 'active-md' . ($quiz_fail ? ' quiz-fail' : '');
                        $dot_ico = 'play_arrow';
                    } else {
                        $dot_cls = 'active-lo' . ($quiz_fail ? ' quiz-fail' : '');
                        $dot_ico = 'play_arrow';
                    }
                    $tip_txt  = "Đang học ({$pct_cell}%)" . ($quiz_fail ? ' — Quiz '.round($quiz_s,1).'/10 ⚠️' : '');
                }
            ?>
            <td style="text-align:center">
                <div class="hm-cell <?= $dot_cls ?>" title="Bài <?= $lid ?>: <?= $tip_txt ?>">
                    <?php if ($dot_ico): ?>
                        <span class="material-symbols-outlined" style="font-size:13px"><?= $dot_ico ?></span>
                    <?php elseif ($cell !== null): ?>
                        <span style="font-size:10px;opacity:.4"><?= $cell['pct'] ?>%</span>
                    <?php else: ?>
                        <span style="font-size:11px;color:#d0d0d0">—</span>
                    <?php endif; ?>
                    <?php if ($quiz_fail): ?>
                        <div class="hm-warn-dot" title="Quiz <?= round($quiz_s,1) ?>/10 — Hổng kiến thức!"></div>
                    <?php endif; ?>
                </div>
                <?php if ($cell !== null && !$cell['completed']): ?>
                <div style="font-size:.58rem;color:#a07040;margin-top:2px"><?= $cell['pct'] ?>%</div>
                <?php endif; ?>
            </td>
            <?php endforeach; ?>

            <!-- Tiến độ -->
            <td style="min-width:110px">
                <?php if ($avg_pct !== null): ?>
                <div style="display:flex;align-items:center;gap:6px">
                    <div class="hm-pbar-w"><div class="hm-pbar" style="width:<?= $bar_pct ?>%;background:<?= $bar_col ?>"></div></div>
                    <span style="font-family:var(--hm-mono);font-size:.73rem;color:<?= $bar_col ?>;font-weight:700"><?= round($avg_pct) ?>%</span>
                </div>
                <div style="font-size:.63rem;color:#c0a878;margin-top:1px"><?= $ai['l_done'] ?>/<?= $ai['l_start'] ?> bài</div>
                <?php else: ?>
                <span style="color:#ddd;font-size:.71rem">—</span>
                <?php endif; ?>
            </td>

            <!-- Risk (chỉ hiện nếu có profile) -->
            <td style="text-align:center">
                <?php if ($has_prof): ?>
                <span class="hm-risk <?= $ai['level'] ?>">
                    <span class="material-symbols-outlined" style="font-size:11px">
                        <?= $ai['risk']>=60?'emergency':($ai['risk']>=35?'warning':($ai['risk']>=15?'info':'check_circle')) ?>
                    </span>
                    <?= $ai['risk'] ?>
                </span>
                <?php else: ?><span style="color:#ddd;font-size:.7rem">—</span><?php endif; ?>
            </td>

            <!-- Dự báo -->
            <td style="text-align:center;font-family:var(--hm-mono);font-size:.84rem;font-weight:700;color:var(--hm-m)">
                <?php if ($ai['predicted'] !== null): ?>
                    <?= $ai['predicted'] ?><span style="font-size:.66rem;color:#c0a878">/10</span>
                <?php else: ?><span style="color:#ddd">—</span><?php endif; ?>
            </td>

            <!-- Quiz TB -->
            <td style="text-align:center">
                <?php if ($ai['q_avg10'] !== null): ?>
                <span class="hm-chip" style="background:<?=
                    $ai['q_avg10']>=7?'#1a6e3a18':($ai['q_avg10']>=5.5?'#85640418':'#c0392b18')
                ?>;color:<?=
                    $ai['q_avg10']>=7?'#1a6e3a':($ai['q_avg10']>=5.5?'#856404':'#c0392b')
                ?>">
                    <?= round($ai['q_avg10'],1) ?>
                </span>
                <div style="font-size:.61rem;color:#c0a878;margin-top:1px"><?= $ai['q_cnt'] ?> bài</div>
                <?php else: ?><span style="color:#ddd;font-size:.7rem">—</span><?php endif; ?>
            </td>

            <!-- Buổi/7d -->
            <td style="text-align:center">
                <?php
                $s7v = $ai['s7'];
                $s7c = $s7v>=5?'#1e8449':($s7v>=2?'#856404':($s7v>=1?'#a84300':'#ccc'));
                ?>
                <span style="font-family:var(--hm-mono);font-size:.86rem;font-weight:700;color:<?= $s7c ?>">
                    <?= $s7v ?>
                </span>
                <?php if ($ai['min7']>0): ?>
                <div style="font-size:.61rem;color:#c0a878"><?= $ai['min7'] ?>ph</div>
                <?php endif; ?>
            </td>

            <!-- Hành động -->
            <td>
                <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap">
                    <button class="hm-btn" onclick="hmToggleRemind(<?= $uid ?>,'tbl')">
                        <span class="material-symbols-outlined">notifications</span>Nhắc
                    </button>
                    <?php if ($pend > 0): ?>
                    <span class="hm-qa-badge"><?= $pend ?>❓</span>
                    <?php endif; ?>
                    <?php if ($is_vip_or_req && $has_prof): ?>
                    <button class="hm-btn" onclick="hmToggleAcc(<?= $uid ?>)"
                            style="border-color:var(--hm-g);color:var(--hm-g);font-weight:900">
                        <span class="material-symbols-outlined">psychology</span>AI
                        <span class="material-symbols-outlined hm-chevron" id="hm-ch2-<?= $uid ?>" style="font-size:13px">expand_more</span>
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <!-- REMIND ROW -->
        <tr id="hm-remind-tbl-<?= $uid ?>" style="display:none">
            <td colspan="<?= count($lesson_ids)+8 ?>" class="hm-remind-td">
                <form method="post" class="hm-remind-form">
                    <input type="hidden" name="hm_action" value="send_reminder">
                    <input type="hidden" name="r_uid" value="<?= $uid ?>">
                    <div style="font-size:.75rem;font-weight:800;color:var(--hm-m);margin-bottom:3px">
                        📬 Gửi nhắc nhở cho: <strong><?= htmlspecialchars($name) ?></strong>
                        <?php if ($has_prof && !empty($ai['tips'])): ?>
                        <span style="font-weight:400;color:#b08060"> — Gợi ý AI:</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($has_prof && !empty($ai['tips'])): ?>
                    <div style="display:flex;flex-wrap:wrap;gap:4px">
                        <?php foreach (array_slice($ai['tips'],0,2) as $tip): ?>
                        <button type="button"
                            onclick="this.closest('form').querySelector('textarea').value=this.dataset.t"
                            data-t="<?= htmlspecialchars($tip) ?>"
                            style="font-size:.68rem;padding:3px 8px;border:1px solid var(--hm-cbd);border-radius:5px;background:#fff;cursor:pointer;color:#5d4037;max-width:310px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            💡 <?= htmlspecialchars(mb_substr($tip,0,58)).(mb_strlen($tip)>58?'…':'') ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <textarea name="r_msg" placeholder="Nhập nội dung nhắc nhở..."></textarea>
                    <div class="hm-remind-footer">
                        <button type="button" class="hm-btn-cancel" onclick="hmCloseRemind(<?= $uid ?>)">Hủy</button>
                        <button type="submit" class="hm-btn-send">
                            <span class="material-symbols-outlined">send</span>Gửi
                        </button>
                    </div>
                </form>
            </td>
        </tr>

        <!-- VIP ACCORDION — Mở cho VIP + học sinh đang chờ VIP (có profile) -->
        <?php if ($is_vip_or_req && $has_prof):
            $qrows_u = $quiz_detail[$uid] ?? [];
            $p = $pal[$ai['level']];
            $c = fn($v,$g,$y)=>$v===null?'nil':($v>=$g?'grn':($v>=$y?'yel':'red'));
            $c_pct  = $c($avg_pct,75,40);
            $c_inac = $ai['days_inact']===null?'red':($ai['days_inact']<=2?'grn':($ai['days_inact']<=7?'yel':'red'));
            $c_pred = ($ai['predicted']!==null&&$ai['target']!==null)?($ai['predicted']>=$ai['target']*.9?'grn':($ai['predicted']>=$ai['target']*.7?'yel':'red')):'nil';
            $c_risk = $ai['risk']<=14?'grn':($ai['risk']<=34?'yel':($ai['risk']<=59?'ora':'red'));
            $vip_label = $is_vip ? 'VIP · Phân tích Mentor AI' : '⭐ Chờ VIP · Phân tích Mentor AI';
        ?>
        <tr class="hm-acc-row" id="hm-acc-<?= $uid ?>">
            <td colspan="<?= count($lesson_ids)+8 ?>" class="hm-acc-td">
                <div class="hm-acc-panel">
                    <!-- Header giống ảnh: icon + badge + tên + trạng thái -->
                    <div class="hm-acc-hdr">
                        <span class="material-symbols-outlined" style="font-size:20px;color:var(--hm-g)">psychology</span>
                        <span class="hm-acc-badge"><?= $vip_label ?></span>
                        <span class="hm-acc-title"><?= htmlspecialchars($name) ?></span>
                        <span style="margin-left:auto;padding:4px 14px;border-radius:20px;font-size:.65rem;font-weight:900;
                                     background:<?= $p['bg'] ?>;color:<?= $p['c'] ?>;letter-spacing:.5px">
                            <?= $p['l'] ?>
                        </span>
                    </div>

                    <!-- KPI row -->
                    <div class="hm-kpi-row">
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Risk Score</div><div class="hm-kpi-val <?= $c_risk ?>"><?= $ai['risk'] ?></div><div class="hm-kpi-sub">/ 100 · 11 nhân tố</div></div>
                        <div class="hm-kpi">
                            <div class="hm-kpi-lbl">Bài hoàn thành</div>
                            <div class="hm-kpi-val <?= $c_pct ?>"><?= $ai['l_done'] ?></div>
                            <div class="hm-kpi-sub">/ <?= count($lesson_ids) ?> bài · TB <?= $avg_pct!==null?round($avg_pct,1):'—' ?>%</div>
                        </div>
                        <div class="hm-kpi">
                            <div class="hm-kpi-lbl">Quiz TB</div>
                            <div class="hm-kpi-val <?= $cls_quiz ?>"><?= $ai['q_avg10']!==null?round($ai['q_avg10'],1):'—' ?></div>
                            <div class="hm-kpi-sub">
                                / 10 · đã làm <?= $ai['q_cnt'] ?>/<?= $ai['l_done'] ?> bài
                                <?php if(($ai['lessons_no_quiz']??0)>0): ?>
                                <span style="color:#d68910;font-weight:800"> · <?= $ai['lessons_no_quiz'] ?> bài chưa quiz</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Nghỉ học</div><div class="hm-kpi-val <?= $c_inac ?>"><?= $ai['days_inact']!==null?$ai['days_inact']:'?' ?></div><div class="hm-kpi-sub">ngày liên tiếp</div></div>
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Buổi / 7ng</div><div class="hm-kpi-val <?= $ai['s7']>=3?'grn':($ai['s7']>=1?'yel':'red') ?>"><?= $ai['s7'] ?></div><div class="hm-kpi-sub"><?= $ai['min7'] ?> phút</div></div>
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Tổng học</div><div class="hm-kpi-val <?= $ai['total_min']>60?'grn':($ai['total_min']>10?'yel':'nil') ?>"><?= $ai['total_min'] ?></div><div class="hm-kpi-sub">phút tổng cộng</div></div>
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Dự báo thi</div><div class="hm-kpi-val <?= $c_pred ?>"><?= $ai['predicted']!==null?$ai['predicted']:'—' ?></div><div class="hm-kpi-sub">/ 10 kỳ thi thật</div></div>
                        <div class="hm-kpi"><div class="hm-kpi-lbl">Mục tiêu</div><div class="hm-kpi-val" style="color:var(--hm-m)"><?= $ai['target']!==null?$ai['target']:'—' ?></div><div class="hm-kpi-sub">điểm đặt ra</div></div>
                    </div>

                    <!-- 2 col -->
                    <div class="hm-ai-2col">
                        <!-- Flags -->
                        <div class="hm-ai-card">
                            <div class="hm-panel-title" style="margin-bottom:9px">
                                <span class="material-symbols-outlined">flag</span>
                                Tín hiệu — <?= count($ai['flags']) ?> yếu tố
                            </div>
                            <?php if (empty($ai['flags'])): ?>
                            <div style="text-align:center;padding:10px;color:#c0a878;font-size:.77rem;font-style:italic">Không có cảnh báo</div>
                            <?php else: ?>
                            <?php foreach ($ai['flags'] as $fl): ?>
                            <div class="hm-flag" style="--fc:<?= $fl['color'] ?>">
                                <div class="hm-flag-ico" style="background:<?= $fl['color'] ?>15;color:<?= $fl['color'] ?>">
                                    <span class="material-symbols-outlined"><?= $fl['icon'] ?></span>
                                </div>
                                <?= htmlspecialchars($fl['text']) ?>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Pred + Tips -->
                        <div style="display:flex;flex-direction:column;gap:11px">
                            <div class="hm-pred">
                                <div class="hm-pred-lbl">Điểm dự báo thi thật</div>
                                <?php if ($ai['predicted'] !== null): ?>
                                <div class="hm-pred-num"><?= $ai['predicted'] ?><span style="font-size:.9rem;opacity:.4">/10</span></div>
                                <div class="hm-pred-formula">Công thức: Tiến độ × Quiz × Hoạt động 7 ngày</div>
                                <div class="hm-pred-track"><div class="hm-pred-fill" style="width:<?= $ai['target']?min(100,round($ai['predicted']/$ai['target']*100)):0 ?>%"></div></div>
                                <div class="hm-pred-marks"><span>0</span><span>MT: <?= $ai['target']??'?' ?></span><span>10</span></div>
                                <?php else: ?>
                                <div style="font-size:.78rem;opacity:.55;padding:6px 0">Chưa đủ dữ liệu<br><small style="opacity:.7">(Cần mục tiêu + tiến độ + ≥1 Quiz)</small></div>
                                <?php endif; ?>
                            </div>
                            <div class="hm-ai-card">
                                <div class="hm-panel-title" style="margin-bottom:8px">
                                    <span class="material-symbols-outlined">lightbulb</span>
                                    Chiến thuật Mentor AI
                                </div>
                                <?php foreach ($ai['tips'] as $tip): ?>
                                <div class="hm-tip"><?= htmlspecialchars($tip) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz detail -->
                    <?php if (!empty($qrows_u)): ?>
                    <div class="hm-ai-card" style="margin-top:12px">
                        <div class="hm-panel-title" style="margin-bottom:9px">
                            <span class="material-symbols-outlined">quiz</span>Điểm Quiz từng bài
                        </div>
                        <table class="hm-qtbl">
                            <thead><tr><th>Bài</th><th>Điểm</th><th>Mức</th><th>Đúng/Tổng</th><th>Khi nào</th></tr></thead>
                            <tbody>
                            <?php foreach ($qrows_u as $qr):
                                $s10=$qr['score_10']; $qcol=$s10>=7.5?'#1a6e3a':($s10>=5.5?'#856404':'#c0392b'); $qbg=$qcol.'14';
                                $qlbl=$s10>=8?'Tốt':($s10>=6?'Khá':($s10>=4?'Yếu':'Rất yếu'));
                                $qd=max(0,(int)((time()-strtotime($qr['taken_at']))/86400));
                                $qts=$qd===0?'Hôm nay':($qd===1?'Hôm qua':"{$qd} ngày trước");
                            ?>
                            <tr>
                                <td><strong style="color:var(--hm-m)">Bài <?= $qr['lesson_id'] ?></strong></td>
                                <td><span class="hm-chip" style="background:<?= $qbg ?>;color:<?= $qcol ?>"><?= $s10 ?>/10</span></td>
                                <td><span style="font-size:.67rem;font-weight:800;color:<?= $qcol ?>"><?= $qlbl ?></span><span class="hm-mbar-w"><div class="hm-mbar" style="width:<?= min(100,round($s10*10)) ?>%;background:<?= $qcol ?>"></div></span></td>
                                <td style="font-family:var(--hm-mono);font-size:.74rem;color:#9a6a30"><?= $qr['score'] ?>/<?= $qr['total_q'] ?></td>
                                <td style="font-size:.68rem;color:#c0a878"><?= $qts ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <div style="text-align:right;margin-top:10px;font-size:.61rem;color:#c0a878">
                        Phân tích lúc <?= date('H:i, d/m/Y') ?> · UID:<?= $uid ?> · <?= count($ai['flags']) ?> tín hiệu · <?= count($ai['tips']) ?> gợi ý
                    </div>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<script>
function hmToggleAcc(uid){
    const acc=document.getElementById('hm-acc-'+uid);
    const ch =document.getElementById('hm-ch-'+uid);
    const ch2=document.getElementById('hm-ch2-'+uid);
    if(!acc)return;
    const open=acc.classList.contains('open');
    // Đóng tất cả acc khác
    document.querySelectorAll('.hm-acc-row.open').forEach(r=>{
        if(r.id!=='hm-acc-'+uid){
            r.classList.remove('open');
            const id2=r.id.replace('hm-acc-','');
            const o1=document.getElementById('hm-ch-'+id2);
            const o2=document.getElementById('hm-ch2-'+id2);
            if(o1)o1.classList.remove('open');
            if(o2)o2.classList.remove('open');
        }
    });
    acc.classList.toggle('open',!open);
    if(ch) ch.classList.toggle('open',!open);
    if(ch2)ch2.classList.toggle('open',!open);
    // Scroll mượt vào panel khi mở
    if(!open && acc){setTimeout(()=>acc.scrollIntoView({behavior:'smooth',block:'nearest'}),80);}
}
function hmToggleRemind(uid,ctx){
    const key='hm-remind-tbl-'+uid;
    const el=document.getElementById(key);
    if(!el)return;
    const showing=el.style.display!=='none';
    document.querySelectorAll('[id^="hm-remind-tbl-"]').forEach(e=>e.style.display='none');
    el.style.display=showing?'none':'table-row';
}
function hmCloseRemind(uid){const el=document.getElementById('hm-remind-tbl-'+uid);if(el)el.style.display='none';}
function openBulkModal(){document.getElementById('hm-bulk-modal').classList.add('open');}
function closeBulkModal(){document.getElementById('hm-bulk-modal').classList.remove('open');}
function bulkSelectAll(v){document.querySelectorAll('#hm-bulk-list input[type=checkbox]').forEach(cb=>cb.checked=v);}
// Đóng modal khi click ngoài
document.getElementById('hm-bulk-modal').addEventListener('click',function(e){if(e.target===this)closeBulkModal();});
</script>

