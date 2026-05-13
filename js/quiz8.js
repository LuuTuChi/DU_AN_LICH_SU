// ─────────────────────────────────────────────
//  STATE - QUẢN LÝ TRẠNG THÁI HỆ THỐNG
// ─────────────────────────────────────────────
var TOTAL     = 8;
var answered  = {};  
var picAns    = {};  
var tfAns     = {};  
var wSelected = null;
var slotVals  = { bs7a: '', bs7b: '', bs7c: '' };
var submitted = false;


function tick(q) {
  if (submitted) return;
  answered[q] = true;
  refreshBar();
}


function refreshBar() {
  var n = Object.keys(answered).length;
  var countEl = document.getElementById('cntAnswered');
  var progEl = document.getElementById('progFill');
  if (countEl) countEl.textContent = n;
  if (progEl) {
    var percent = (n / TOTAL * 100);
    progEl.style.width = percent + '%';
  }
}


// ─────────────────────────────────────────────
//  CÂU 2 — DRAG & DROP (SỬA LỖI ẨN CHỮ & TRẢ CHIP)
// ─────────────────────────────────────────────
var dragVal = null;
(function initDrag() {
  var chips = document.querySelectorAll('.chip');
  chips.forEach(function(c) {
    c.setAttribute('draggable', true);
    c.addEventListener('dragstart', function(e) {
      if (submitted) return;
      dragVal = this.dataset.val;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', dragVal);
      e.dataTransfer.setData('cid', this.id);
    });
    c.addEventListener('dragend', function() {
      this.classList.remove('dragging');
    });
  });


  var zones = document.querySelectorAll('.dzone');
  zones.forEach(function(z) {
    z.addEventListener('dragover', function(e) {
      e.preventDefault();
      if (!submitted) this.classList.add('over');
    });
    z.addEventListener('dragleave', function() {
      this.classList.remove('over');
    });
    z.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('over');
      if (submitted) return;


      var val = e.dataTransfer.getData('text/plain');
      var cid = e.dataTransfer.getData('cid');
      if (!val) return;


      // --- PHẦN SỬA: LOGIC TRẢ CHIP CŨ VỀ (HIỆN CHỮ) ---
      var existing = this.querySelector('.placed-chip');
      if (existing) {
        var oldCid = existing.dataset.cid;
        var oldOrig = document.getElementById(oldCid);
        if (oldOrig) {
          oldOrig.style.visibility = 'visible'; // Hiện lại chip ở cột trái
          oldOrig.classList.remove('placed');
        }
        existing.remove();
      }


      // --- PHẦN SỬA: TẠO CHIP MỚI TRONG Ô THẢ (ĐẢM BẢO HIỆN CHỮ) ---
      var pc = document.createElement('span');
      pc.className = 'placed-chip';
      pc.dataset.val = val;
      pc.dataset.cid = cid;
      pc.textContent = val; // Gán chữ trực tiếp để không bị mất
     
      // Chèn chip mới vào (giữ lại nhãn giai đoạn bên phải)
      this.prepend(pc);
      this.classList.add('filled');


      // --- PHẦN SỬA: ẨN CHIP GỐC ---
      var orig = document.getElementById(cid);
      if (orig) {
        orig.style.visibility = 'hidden'; // Chỉ ẩn đi, không xóa khỏi DOM
        orig.classList.add('placed');
      }


      var filled = document.querySelectorAll('#q2 .dzone.filled').length;
      if (filled >= 4) tick('q2');
    });
  });
})();


// ─────────────────────────────────────────────
//  CÂU 4 — SẮP XẾP TIMELINE (CLICK ↑↓)
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


  item.classList.add('tl-moved');
  setTimeout(function() {
    item.classList.remove('tl-moved');
  }, 400);


  tick('q4');
}


// ─────────────────────────────────────────────
//  CÂU 5 — CHỌN HÌNH (EMOJI QUIZ)
// ─────────────────────────────────────────────
function pickPic(card, qid) {
  if (submitted) return;
  var parent = card.closest('.pic-group') || document.getElementById(qid);
  parent.querySelectorAll('.pic-card').forEach(function(c) {
    c.classList.remove('selected');
  });
  card.classList.add('selected');
  picAns[qid] = card.dataset.val;
  tick(qid);
}


// ─────────────────────────────────────────────
//  CÂU 6 — ĐÚNG / SAI
// ─────────────────────────────────────────────
function pickTF(val, qid) {
  if (submitted) return;
  var tBtn = document.getElementById('tfT');
  var fBtn = document.getElementById('tfF');
 
  tBtn.classList.remove('tf-selected-t');
  fBtn.classList.remove('tf-selected-f');
 
  if (val === 'true') {
    tBtn.classList.add('tf-selected-t');
  } else {
    fBtn.classList.add('tf-selected-f');
  }
 
  tfAns[qid] = val;
  tick(qid);
}


// ─────────────────────────────────────────────
//  CÂU 7 — WORD BANK (NGÂN HÀNG TỪ)
// ─────────────────────────────────────────────
function selectW(chip) {
  if (submitted || chip.classList.contains('w-used')) return;
 
  if (wSelected === chip) {
    chip.classList.remove('w-active');
    wSelected = null;
    return;
  }


  document.querySelectorAll('.wchip').forEach(function(c) {
    c.classList.remove('w-active');
  });
 
  wSelected = chip;
  chip.classList.add('w-active');
}


function fillB(slot) {
  if (submitted) return;
  if (!wSelected) {
    alert('Vui lòng chọn một từ trong ngân hàng từ trước!');
    return;
  }
 
  var word = wSelected.dataset.word;
 
  var prevWord = slotVals[slot.id];
  if (prevWord) {
    document.querySelectorAll('.wchip').forEach(function(c) {
      if (c.dataset.word === prevWord) c.classList.remove('w-used');
    });
  }


  Object.keys(slotVals).forEach(function(key) {
    if (slotVals[key] === word && key !== slot.id) {
      var otherSlot = document.getElementById(key);
      if (otherSlot) {
        otherSlot.textContent = '__ điền vào __';
        otherSlot.classList.remove('bs-filled');
      }
      slotVals[key] = '';
    }
  });


  slotVals[slot.id] = word;
  slot.textContent = word;
  slot.classList.add('bs-filled');
 
  wSelected.classList.add('w-used');
  wSelected.classList.remove('w-active');
  wSelected = null;


  if (slotVals.bs7a && slotVals.bs7b && slotVals.bs7c) {
    tick('q7');
  } else {
    if (answered.q7) delete answered.q7;
  }
  refreshBar();
}


// ─────────────────────────────────────────────
//  FEEDBACK HELPER
// ─────────────────────────────────────────────
function showFB(id, ok, msg) {
  var el = document.getElementById(id);
  if (!el) return;
  el.className = 'q-fb ' + (ok ? 'fb-correct' : 'fb-wrong');
  el.innerHTML = (ok ? '✅ ' : '❌ ') + msg;
 
  var card = document.getElementById(id.replace('fb', 'q'));
  if (card) {
    card.classList.remove('correct', 'wrong');
    card.classList.add(ok ? 'correct' : 'wrong');
  }
}


// ─────────────────────────────────────────────
//  SUBMIT ALL (CHẤM ĐIỂM CHI TIẾT)
// ─────────────────────────────────────────────
function submitAll() {
  if (submitted) return;
  submitted = true;
  var score = 0;


  // Q1
  var r1 = document.querySelector('input[name="q1"]:checked');
  var ok1 = (r1?.value === 'b');
  if (ok1) score++;
  showFB('fb1', ok1, ok1 ? 'Chính xác! Quân đội Sài Gòn là lực lượng chủ yếu.' : 'Chưa đúng. Đáp án B: Quân đội Sài Gòn là lực lượng chủ yếu.');
  document.querySelectorAll('input[name="q1"]').forEach(r => r.disabled = true);


  // Q2 (Chấm điểm chuẩn hóa text)
  var zones = document.querySelectorAll('.dzone');
  var q2ok = true;
  var filledCount = 0;
  zones.forEach(function(z) {
    var pc = z.querySelector('.placed-chip');
    if (!pc) { q2ok = false; }
    else {
      filledCount++;
      var userAns = pc.textContent.trim().toLowerCase();
      var correctAns = z.dataset.ans.trim().toLowerCase();
      if (userAns !== correctAns) {
        q2ok = false;
        z.style.background = '#fff0f0';
        z.style.borderColor = '#ff4d4d';
      } else {
        z.style.background = '#f6ffed';
        z.style.borderColor = '#52c41a';
      }
    }
  });
  if (q2ok && filledCount === zones.length) score++;
  showFB('fb2', q2ok, q2ok ? 'Tuyệt vời! Bạn đã nối đúng các mốc chiến lược.' : 'Có mốc nối chưa đúng. Hãy xem lại bài học.');


  // Q3
  var a3 = document.getElementById('fi3a').value.trim().toLowerCase();
  var b3 = document.getElementById('fi3b').value.trim();
  var ok3 = (a3.includes('ấp bắc') || a3.includes('ap bac')) && (b3 === '1975');
  if (ok3) score++;
  showFB('fb3', ok3, ok3 ? 'Đúng! Trận Ấp Bắc và năm giải phóng 1975.' : 'Chưa chính xác. Đáp án: (1) Ấp Bắc - (2) 1975.');
  document.getElementById('fi3a').disabled = true;
  document.getElementById('fi3b').disabled = true;


  // Q4
  var its = Array.prototype.slice.call(document.querySelectorAll('#tlList .tl-item'));
  var ords = its.map(function(i) { return parseInt(i.dataset.order); });
  var ok4 = (ords.join(',') === '1,2,3,4');
  if (ok4) score++;
  showFB('fb4', ok4, ok4 ? 'Hoàn hảo! Thứ tự sự kiện hoàn toàn chính xác.' : 'Sai rồi. Thứ tự đúng: Đồng khởi -> Ấp Bắc -> Mậu Thân -> 30/4/1975.');
  document.querySelectorAll('#tlList .tl-btn').forEach(b => b.disabled = true);


  // Q5
  var ok5 = (picAns['q5'] === 'b');
  if (ok5) score++;
  document.querySelectorAll('#q5 .pic-card').forEach(function(c) {
    c.style.pointerEvents = 'none';
    if (c.dataset.val === 'b') c.classList.add('pc-correct');
    else if (c.classList.contains('selected')) c.classList.add('pc-wrong');
  });
  showFB('fb5', ok5, ok5 ? 'Đúng! Điện Biên Phủ trên không 1972.' : 'Chưa đúng. Gợi ý: Bắn rơi B52 trên bầu trời Hà Nội 1972.');


  // Q6
  var ok6 = (tfAns['q6'] === 'true');
  if (ok6) score++;
  showFB('fb6', ok6, ok6 ? 'Đúng! Đó là đặc điểm của VN hóa chiến tranh.' : 'Sai rồi. Nhận định này hoàn toàn ĐÚNG.');
  document.getElementById('tfT').disabled = true;
  document.getElementById('tfF').disabled = true;


  // Q7
  var ok7 = (slotVals.bs7a === 'miền Bắc' && slotVals.bs7b === 'Pa-ri' && slotVals.bs7c === '30/4/1975');
  if (ok7) score++;
  ['a', 'b', 'c'].forEach(function(k) {
    var sl = document.getElementById('bs7' + k);
    var correct = { a: 'miền Bắc', b: 'Pa-ri', c: '30/4/1975' };
    sl.style.color = (slotVals['bs7' + k] === correct[k]) ? '#28a745' : '#dc3545';
  });
  showFB('fb7', ok7, ok7 ? 'Xuất sắc! Bạn đã điền đúng các từ khóa.' : 'Chưa đúng. Đáp án: (1) miền Bắc - (2) Pa-ri - (3) 30/4/1975.');


  // Q8
  var r8 = document.querySelector('input[name="q8"]:checked');
  var ok8 = (r8?.value === 'b');
  if (ok8) score++;
  showFB('fb8', ok8, ok8 ? 'Chính xác! Mở ra kỷ nguyên độc lập thống nhất.' : 'Chưa đúng. Đáp án B: Mở ra kỷ nguyên độc lập thống nhất.');
  document.querySelectorAll('input[name="q8"]').forEach(r => r.disabled = true);


  // KẾT QUẢ TỔNG
  var pts = Math.round(score / TOTAL * 100) / 10;
  document.getElementById('resScore').textContent = score;
  document.getElementById('rgC').textContent = score;
  document.getElementById('rgW').textContent = TOTAL - score;
  document.getElementById('rgP').textContent = pts;
  document.getElementById('resPts').textContent = pts + ' / 10 điểm';
 
  var rb = document.getElementById('resultBox');
  rb.classList.add('show');
  rb.style.display = 'block';
  rb.style.animation = 'popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
  setTimeout(function() {
    rb.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }, 200);
fetch('../../php/quiz_score.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
          lesson_id: 8, // Nhớ là bài 8 nhé Chi
          score: score,
          total_q: TOTAL
      })
  })
  .then(res => res.json())
  .then(data => { console.log("Lưu điểm bài 8 thành công:", data); })
  .catch(err => console.error("Lỗi lưu điểm:", err));

}
// ─────────────────────────────────────────────
//  RESET & NAVIGATION
// ─────────────────────────────────────────────
function resetAll() {
  location.reload();
}


var style = document.createElement('style');
style.innerHTML = '@keyframes popIn { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }';
document.head.appendChild(style);


function goBack() {
  window.location.href = "../student/student_dashboard.html";
}

