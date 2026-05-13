// ─────────────────────────────────────────────
//  STATE - QUẢN LÝ TRẠNG THÁI
// ─────────────────────────────────────────────
var TOTAL     = 8;
var answered  = {};   // { q1:true, q2:true, ... }
var picAns    = {};   // { q5: 'b' }
var tfAns     = {};   // { q6: 'true' }
var wSelected = null; // Từ đang chọn trong Word Bank
var slotVals  = { bs7a:'', bs7b:'', bs7c:'' }; // Giá trị điền câu 7
var submitted = false;


function tick(q){
  answered[q] = true;
  refreshBar();
}


function refreshBar(){
  var n = Object.keys(answered).length;
  var countEl = document.getElementById('cntAnswered');
  var progEl = document.getElementById('progFill');
  if(countEl) countEl.textContent = n;
  if(progEl) progEl.style.width = (n / TOTAL * 100) + '%';
}


// ─────────────────────────────────────────────
//  CÂU 2 — DRAG & DROP (NỐI CHIẾN DỊCH)
// ─────────────────────────────────────────────
var dragVal = null;
(function initDrag(){
  var chips = document.querySelectorAll('.chip');
  chips.forEach(function(c){
    c.setAttribute('draggable', true);
    c.addEventListener('dragstart', function(e){
      if(submitted) return;
      dragVal = this.dataset.val;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', dragVal);
      e.dataTransfer.setData('cid', this.id);
    });
    c.addEventListener('dragend', function(){
      this.classList.remove('dragging');
    });
  });


  var zones = document.querySelectorAll('.dzone');
  zones.forEach(function(z){
    z.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('over'); });
    z.addEventListener('dragleave', function(){ this.classList.remove('over'); });
    z.addEventListener('drop', function(e){
      e.preventDefault();
      this.classList.remove('over');
      if(submitted) return;


      var val = e.dataTransfer.getData('text/plain');
      var cid = e.dataTransfer.getData('cid');
      if(!val) return;


      // Trả chip cũ về ngân hàng nếu ô đã có chip
      var existing = this.querySelector('.placed-chip');
      if(existing){
        var oldCid = existing.dataset.cid;
        var oldOrig = document.getElementById(oldCid);
        if(oldOrig) { oldOrig.style.visibility = 'visible'; oldOrig.classList.remove('placed'); }
        existing.remove();
      }


      // Tạo chip mới trong ô drop
      var pc = document.createElement('span');
      pc.className = 'placed-chip';
      pc.dataset.val = val;
      pc.dataset.cid = cid;
      pc.textContent = val;
     
      this.innerHTML = '';
      this.classList.add('filled');
      this.appendChild(pc);


      // Ẩn chip gốc
      var orig = document.getElementById(cid);
      if(orig) { orig.style.visibility = 'hidden'; orig.classList.add('placed'); }


      var filled = document.querySelectorAll('.dzone.filled').length;
      if(filled >= 4) tick('q2');
    });
  });
})();


// ─────────────────────────────────────────────
//  CÂU 4 — CLICK ↑↓ SẮP XẾP (TIMELINE)
// ─────────────────────────────────────────────
function moveItem(btn, dir) {
  if (submitted) return;
  var item = btn.closest('.tl-item');
  var list = document.getElementById('tlList');
  var items = Array.prototype.slice.call(list.querySelectorAll('.tl-item'));
  var idx = items.indexOf(item);
  var newIdx = idx + dir;
 
  if (newIdx < 0 || newIdx >= items.length) return;


  if (dir === -1) {
    list.insertBefore(item, items[newIdx]);
  } else {
    list.insertBefore(item, items[newIdx].nextSibling);
  }


  // Hiệu ứng flash như bản mẫu
  item.classList.add('tl-moved');
  setTimeout(function(){ item.classList.remove('tl-moved'); }, 400);
  tick('q4');
}


// ─────────────────────────────────────────────
//  CÂU 5 — PICTURE PICK (EMOJI QUIZ)
// ─────────────────────────────────────────────
function pickPic(card, qid){
  if(submitted) return;
  document.querySelectorAll('#' + qid + ' .pic-card').forEach(function(c){
    c.classList.remove('selected');
  });
  card.classList.add('selected');
  picAns[qid] = card.dataset.val;
  tick(qid);
}


// ─────────────────────────────────────────────
//  CÂU 6 — ĐÚNG / SAI
// ─────────────────────────────────────────────
function pickTF(val, qid){
  if(submitted) return;
  var tBtn = document.getElementById('tfT');
  var fBtn = document.getElementById('tfF');
  tBtn.classList.remove('tf-selected-t');
  fBtn.classList.remove('tf-selected-f');
 
  if(val === 'true') tBtn.classList.add('tf-selected-t');
  else fBtn.classList.add('tf-selected-f');
 
  tfAns[qid] = val;
  tick(qid);
}


// ─────────────────────────────────────────────
//  CÂU 7 — WORD BANK (NGÂN HÀNG TỪ)
// ─────────────────────────────────────────────
function selectW(chip){
  if(submitted || chip.classList.contains('w-used')) return;
 
  if(wSelected === chip){
    chip.classList.remove('w-active');
    wSelected = null;
    return;
  }


  document.querySelectorAll('.wchip').forEach(function(c){ c.classList.remove('w-active'); });
  wSelected = chip;
  chip.classList.add('w-active');
}


function fillB(slot){
  if(submitted) return;
  if(!wSelected){ alert('Hãy chọn một từ trong ngân hàng từ trước!'); return; }
 
  var word = wSelected.dataset.word;
 
  // Trả từ cũ về nếu ô đã có giá trị
  var prevWord = slotVals[slot.id];
  if(prevWord){
    document.querySelectorAll('.wchip').forEach(function(c){
      if(c.dataset.word === prevWord) c.classList.remove('w-used');
    });
  }


  // Chống trùng lặp từ
  Object.keys(slotVals).forEach(function(k){
    if(slotVals[k] === word && k !== slot.id){
      var s = document.getElementById(k);
      if(s){ s.textContent = '__ điền vào __'; s.classList.remove('bs-filled'); }
      slotVals[k] = '';
    }
  });


  slotVals[slot.id] = word;
  slot.textContent = word;
  slot.classList.add('bs-filled');
 
  wSelected.classList.add('w-used');
  wSelected.classList.remove('w-active');
  wSelected = null;


  if(slotVals.bs7a && slotVals.bs7b && slotVals.bs7c) tick('q7');
  else delete answered.q7;
  refreshBar();
}


// ─────────────────────────────────────────────
//  FEEDBACK HELPER
// ─────────────────────────────────────────────
function showFB(id, ok, msg){
  var el = document.getElementById(id);
  if(!el) return;
  el.className = 'q-fb ' + (ok ? 'fb-correct' : 'fb-wrong');
  el.innerHTML = (ok ? '✅ ' : '❌ ') + msg;
 
  var card = document.getElementById(id.replace('fb', 'q'));
  if(card){
    card.classList.remove('correct', 'wrong');
    card.classList.add(ok ? 'correct' : 'wrong');
  }
}


// ─────────────────────────────────────────────
//  SUBMIT ALL (CHẤM ĐIỂM)
// ─────────────────────────────────────────────
function submitAll(){
  submitted = true;
  var score = 0;


  // Q1: Trắc nghiệm
  var r1 = document.querySelector('input[name="q1"]:checked');
  if(r1){
    var ok1 = (r1.value === 'b');
    if(ok1) score++;
    showFB('fb1', ok1, ok1 ? 'Chính xác! Ngày 19/12/1946, Lời kêu gọi toàn quốc kháng chiến được ban bố.' : 'Chưa đúng. Đáp án: Ngày 19/12/1946, Bác Hồ ra Lời kêu gọi toàn quốc kháng chiến.');
  } else showFB('fb1', false, 'Bạn chưa chọn đáp án cho câu 1.');
  document.querySelectorAll('input[name="q1"]').forEach(function(r){ r.disabled = true; });


  // Q2: Nối mốc thời gian
  var zones = document.querySelectorAll('.dzone');
  var q2ok = true; var filledCount = 0;
  zones.forEach(function(z){
    var pc = z.querySelector('.placed-chip');
    if(!pc) { q2ok = false; }
    else {
      filledCount++;
      if(pc.dataset.val !== z.dataset.ans){
        q2ok = false; z.style.background = '#fff0f0'; z.style.borderColor = '#ff4d4d';
      } else {
        z.style.background = '#f6ffed'; z.style.borderColor = '#52c41a';
      }
    }
  });
  if(filledCount < 4) showFB('fb2', false, 'Bạn chưa hoàn thành nối các chiến dịch.');
  else { if(q2ok) score++; showFB('fb2', q2ok, q2ok ? 'Tuyệt vời! Bạn đã nối chính xác các chiến dịch và ý nghĩa.' : 'Có mốc chưa đúng. Hãy ôn lại các chiến dịch Việt Bắc 1947, Biên giới 1950 và Điện Biên Phủ.'); }


  // Q3: Điền từ tự do
  var a3 = document.getElementById('fi3a').value.trim().toLowerCase().replace(/\s/g,'');
  var b3 = document.getElementById('fi3b').value.trim().toLowerCase();
  var ok3a = (a3 === 'nava');
  var ok3b = (b3.includes('gi'));
  if(ok3a && ok3b) score++;
  showFB('fb3', (ok3a && ok3b), (ok3a && ok3b) ? 'Đúng! Kế hoạch Nava và Hiệp định Giơ-ne-vơ.' : 'Chưa đúng. Đáp án: (1) Nava - (2) Giơ-ne-vơ.');
  document.getElementById('fi3a').disabled = true;
  document.getElementById('fi3b').disabled = true;


  // Q4: Thứ tự Timeline
  var its = Array.prototype.slice.call(document.querySelectorAll('#tlList .tl-item'));
  var ords = its.map(function(i){ return parseInt(i.dataset.order); });
  var ok4 = (ords.join(',') === '1,2,3,4');
  if(ok4) score++;
  showFB('fb4', ok4, ok4 ? 'Hoàn hảo! Thứ tự các mốc lịch sử hoàn toàn chính xác.' : 'Sai rồi. Thứ tự đúng: Lời kêu gọi (1946) -> Việt Bắc (1947) -> Biên giới (1950) -> Điện Biên Phủ (1954).');
  document.querySelectorAll('#tlList .tl-btn').forEach(function(b){ b.disabled = true; });


  // Q5: Hình ảnh (Emoji)
  var p5 = picAns['q5'];
  if(p5){
    var ok5 = (p5 === 'b');
    if(ok5) score++;
    document.querySelectorAll('#q5 .pic-card').forEach(function(c){
      c.style.pointerEvents = 'none';
      if(c.dataset.val === 'b') c.classList.add('pc-correct');
      else if(c.classList.contains('selected')) c.classList.add('pc-wrong');
    });
    showFB('fb5', ok5, ok5 ? 'Đúng! Đây là biểu tượng của Chiến thắng Điện Biên Phủ "lừng lẫy năm châu".' : 'Chưa đúng. Hình ảnh gợi ý đến chiến dịch kết thúc cuộc kháng chiến: Điện Biên Phủ.');
  } else showFB('fb5', false, 'Bạn chưa chọn hình ảnh.');


  // Q6: Đúng/Sai
  var tf6 = tfAns['q6'];
  if(tf6 !== undefined){
    var ok6 = (tf6 === 'true');
    if(ok6) score++;
    showFB('fb6', ok6, ok6 ? 'Đúng! Đường lối kháng chiến của ta là toàn dân, toàn diện, trường kỳ và tự lực.' : 'Sai rồi. Đây là nhận định ĐÚNG về đường lối kháng chiến của Đảng.');
  } else showFB('fb6', false, 'Bạn chưa chọn Đúng hay Sai.');


  // Q7: Word Bank
var ok7 = (slotVals.bs7a === 'Đảng') && (slotVals.bs7b === 'Việt Bắc') && (slotVals.bs7c === 'Nava');
if (slotVals.bs7a || slotVals.bs7b || slotVals.bs7c) {
    if (ok7) score++;
    showFB('fb7', ok7, ok7 ? 'Xuất sắc! Bạn đã điền đúng các từ khóa.' : 'Chưa đúng. Đáp án: (1) Đảng - (2) Việt Bắc - (3) Nava.');
    
    // Đổi màu từng ô cho sinh động
    ['a','b','c'].forEach(function(k){
        var sl = document.getElementById('bs7' + k);
        var correct = {a:'Đảng', b:'Việt Bắc', c:'Nava'};
        if(slotVals['bs7'+k] === correct[k]) sl.style.color = '#28a745';
        else sl.style.color = '#dc3545';
    });
} else {
    showFB('fb7', false, 'Bạn chưa hoàn thành câu điền từ.');
}


  // Q8: Ý nghĩa lịch sử
  var r8 = document.querySelector('input[name="q8"]:checked');
  if(r8){
    var ok8 = (r8.value === 'a');
    if(ok8) score++;
    showFB('fb8', ok8, ok8 ? 'Chính xác! Thắng lợi buộc Pháp ký Hiệp định Giơ-ne-vơ, chấm dứt xâm lược.' : 'Chưa đúng. Ý nghĩa lớn nhất là buộc Pháp ký Hiệp định Giơ-ne-vơ.');
  } else showFB('fb8', false, 'Bạn chưa chọn đáp án câu 8.');


  // HIỂN THỊ KẾT QUẢ TỔNG QUÁT
  var pts = Math.round(score / TOTAL * 100) / 10;
  document.getElementById('resScore').textContent = score;
  document.getElementById('rgC').textContent = score;
  document.getElementById('rgW').textContent = TOTAL - score;
  document.getElementById('rgP').textContent = pts;
  document.getElementById('resEmoji').textContent = score >= 7 ? '🏆' : score >= 5 ? '⭐' : '📚';
  document.getElementById('resMsg').textContent = score >= 7 ? 'Xuất sắc!' : 'Cố gắng ôn tập thêm nhé!';
  document.getElementById('resPts').textContent = pts + ' / 10 điểm';
 
  var rb = document.getElementById('resultBox');
  rb.classList.add('show');
  rb.style.display = 'block';
  rb.style.animation = 'none';
  rb.offsetHeight;
  rb.style.animation = 'popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
  setTimeout(function(){ rb.scrollIntoView({behavior:'smooth', block:'center'}); }, 200);
  // --- CHÈN VÀO CUỐI HÀM submitAll() ---
fetch('../../php/quiz_score.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        lesson_id: 7, // Lưu ý bài 7
        score: score,
        total_q: TOTAL
    })
})
.then(res => res.json())
.then(data => {
    console.log("Đã lưu điểm bài 7:", data);
})
.catch(err => console.error("Lỗi lưu điểm:", err));
// ─────────────────────────────────────────────
}

//  RESET ALL
// ─────────────────────────────────────────────
function resetAll(){
  location.reload();
}


// Keyframe cho hiệu ứng Pop-in
var style = document.createElement('style');
style.innerHTML = '@keyframes popIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }';
document.head.appendChild(style);


function goBack(){
  window.location.href = "../student/student_dashboard.html";
}

