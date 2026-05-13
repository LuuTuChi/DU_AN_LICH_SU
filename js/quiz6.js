// ─────────────────────────────────────────────
//  STATE
// ─────────────────────────────────────────────
var answered = {};   // { q1:true, q2:true, ... }
var picAns   = {};   // { q5: 'b' }
var tfAns    = {};   // { q6: 'true' }
var wSelected= null; // currently selected word chip
var slotVals = { bs7a:'', bs7b:'', bs7c:'' };
var submitted= false;

function tick(q){
  answered[q]=true;
  refreshBar();
}

function refreshBar(){
  var n=Object.keys(answered).length;
  document.getElementById('cntAnswered').textContent=n;
  document.getElementById('progFill').style.width=(n/8*100)+'%';
}

// ─────────────────────────────────────────────
//  CÂU 2 — DRAG & DROP
// ─────────────────────────────────────────────
var dragVal=null;
(function initDrag(){
  var chips=document.querySelectorAll('.chip[draggable]');
  chips.forEach(function(c){
    c.addEventListener('dragstart',function(e){
      dragVal=this.dataset.val;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed='move';
      e.dataTransfer.setData('text/plain',dragVal);
    });
    c.addEventListener('dragend',function(){
      this.classList.remove('dragging');
    });
  });
  var zones=document.querySelectorAll('.dzone');
  zones.forEach(function(z){
    z.addEventListener('dragover',function(e){ e.preventDefault(); this.classList.add('over'); });
    z.addEventListener('dragleave',function(){ this.classList.remove('over'); });
    z.addEventListener('drop',function(e){
      e.preventDefault();
      this.classList.remove('over');
      var val=e.dataTransfer.getData('text/plain');
      if(!val) return;
      document.querySelectorAll('.dzone').forEach(function(oz){
        var pc=oz.querySelector('.placed-chip');
        if(pc && pc.dataset.val===val){ oz.classList.remove('filled'); pc.remove(); }
      });
      var existing=this.querySelector('.placed-chip');
      if(existing){
        var oldVal=existing.dataset.val;
        existing.remove();
        this.classList.remove('filled');
        var origChip=document.getElementById('ch_'+['a','b','c','d'][['13/8/1945','19/8/1945','23/8/1945','2/9/1945'].indexOf(oldVal)]);
        if(origChip){ origChip.style.visibility='visible'; origChip.classList.remove('used'); }
      }
      var pc=document.createElement('span');
      pc.className='placed-chip';
      pc.dataset.val=val;
      pc.textContent=val;
      this.classList.add('filled');
      this.appendChild(pc);
      var vals=['13/8/1945','19/8/1945','23/8/1945','2/9/1945'];
      var ids=['ch_a','ch_b','ch_c','ch_d'];
      var idx=vals.indexOf(val);
      if(idx>=0){ var orig=document.getElementById(ids[idx]); if(orig){orig.style.visibility='hidden';} }
      var filled=document.querySelectorAll('#q2 .dzone.filled').length;
      if(filled>=4) tick('q2');
    });
  });
})();

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
  setTimeout(function(){ item.classList.remove('tl-moved'); }, 400);
  tick('q4');
}

function pickPic(card,qid){
  if(submitted) return;
  document.querySelectorAll('#'+qid+' .pic-card').forEach(function(c){
    c.classList.remove('selected');
  });
  card.classList.add('selected');
  picAns[qid]=card.dataset.val;
  tick(qid);
}

function pickTF(val,qid){
  if(submitted) return;
  document.getElementById('tfT').classList.remove('t-sel');
  document.getElementById('tfF').classList.remove('f-sel');
  if(val==='true') document.getElementById('tfT').classList.add('t-sel');
  else             document.getElementById('tfF').classList.add('f-sel');
  tfAns[qid]=val;
  tick(qid);
}

function selectW(chip){
  if(submitted) return;
  if(chip.classList.contains('wused')) return;
  if(wSelected===chip){
    chip.classList.remove('wselected');
    wSelected=null;
    return;
  }
  document.querySelectorAll('#wb7 .wchip').forEach(function(c){ c.classList.remove('wselected'); });
  wSelected=chip;
  chip.classList.add('wselected');
}

function fillB(slot){
  if(submitted) return;
  if(!wSelected){ alert('Hãy chọn một từ trong ngân hàng từ trước!'); return; }
  var word=wSelected.dataset.word;
  var prevWord=slotVals[slot.id];
  if(prevWord){
    document.querySelectorAll('#wb7 .wchip').forEach(function(c){
      if(c.dataset.word===prevWord){ c.classList.remove('wused'); }
    });
  }
  Object.keys(slotVals).forEach(function(k){
    if(slotVals[k]===word && k!==slot.id){
      var s=document.getElementById(k);
      if(s){ s.textContent='__ điền vào __'; s.classList.remove('bfilled'); }
      slotVals[k]='';
    }
  });
  slotVals[slot.id]=word;
  slot.textContent=word;
  slot.classList.add('bfilled');
  wSelected.classList.add('wused');
  wSelected.classList.remove('wselected');
  wSelected=null;
  if(slotVals.bs7a && slotVals.bs7b && slotVals.bs7c) tick('q7');
  else delete answered.q7;
  refreshBar();
}

function showFB(id,ok,msg){
  var el=document.getElementById(id);
  el.className='q-fb '+(ok?'show-ok':'show-bad');
  el.innerHTML=(ok?'✅ ':'❌ ')+msg;
  var card=document.getElementById(id.replace('fb','q'));
  card.classList.remove('correct','wrong');
  card.classList.add(ok?'correct':'wrong');
}

// ─────────────────────────────────────────────
//  SUBMIT & LƯU ĐIỂM (PHẦN QUAN TRỌNG NHẤT)
// ─────────────────────────────────────────────
function submitAll(){
  if(submitted) return;
  submitted=true;
  var score=0;

  // Q1
  var r1=document.querySelector('input[name="q1"]:checked');
  if(r1){
    var ok=(r1.value==='b'); if(ok) score++;
    showFB('fb1',ok,ok?'Chính xác!':'Đáp án: Nhật Bản tuyên bố đầu hàng Đồng minh.');
  } else showFB('fb1',false,'Bạn chưa chọn đáp án.');

  // Q2
  var zones=document.querySelectorAll('#q2 .dzone');
  var q2ok=true; var filled=0;
  zones.forEach(function(z){
    var pc=z.querySelector('.placed-chip');
    if(!pc){ q2ok=false; }
    else{
      filled++;
      if(pc.dataset.val!==z.dataset.ans){ q2ok=false; z.style.borderColor='var(--red-bd)'; z.style.background='var(--red-bg)'; }
      else { z.style.borderColor='var(--green-bd)'; z.style.background='var(--green-bg)'; }
    }
  });
  if(filled<4) q2ok=false;
  if(q2ok) score++;
  showFB('fb2',q2ok,q2ok?'Xuất sắc!':'Có mốc thời gian chưa đúng.');

  // Q3
  var a=document.getElementById('fi3a').value.trim().toLowerCase().replace(/\s/g,'');
  var b=document.getElementById('fi3b').value.trim().toLowerCase().replace(/\s/g,'');
  var oka=(a==='2'||a==='hai');
  var okb=(b.indexOf('tântrào')>=0||b.indexOf('tantrao')>=0);
  if(oka&&okb) score++;
  showFB('fb3',oka&&okb,'Đáp án: 2 triệu người & địa danh Tân Trào.');

  // Q4
  var items=Array.prototype.slice.call(document.querySelectorAll('#tlList .tl-item'));
  var orders=items.map(function(i){ return parseInt(i.dataset.order); });
  var ok4=(orders[0]===1&&orders[1]===2&&orders[2]===3&&orders[3]===4);
  if(ok4) score++;
  showFB('fb4',ok4,ok4?'Hoàn hảo!':'Thứ tự: Tổng KN -> Hà Nội -> Sài Gòn -> Tuyên ngôn ĐL.');

  // Q5
  var p5=picAns['q5'];
  var ok5=(p5==='b'); if(ok5) score++;
  showFB('fb5',ok5,ok5?'Đúng!':'Đáp án: Tuyên ngôn Độc lập tại Quảng trường Ba Đình.');

  // Q6
  var tf6=tfAns['q6'];
  var ok6=(tf6==='true'); if(ok6) score++;
  showFB('fb6',ok6,ok6?'Đúng! CM tháng Tám thắng lợi trong 15 ngày.':'Nhận định này ĐÚNG.');

  // Q7
  var ok7=(slotVals.bs7a==='Đảng Cộng sản' && slotVals.bs7b==='yêu nước' && slotVals.bs7c==='Nhật Bản');
  if(ok7) score++;
  showFB('fb7',ok7,ok7?'Xuất sắc!':'Đáp án: Đảng Cộng sản - yêu nước - Nhật Bản.');

  // Q8
  var r8=document.querySelector('input[name="q8"]:checked');
  if(r8){
    var ok8=(r8.value==='b'); if(ok8) score++;
    showFB('fb8',ok8,ok8?'Chính xác!':'Đáp án: Mở ra kỷ nguyên độc lập, tự do.');
  }

  // HIỂN THỊ KẾT QUẢ LÊN GIAO DIỆN
  var pts=Math.round(score/8*10*10)/10;
  document.getElementById('resScore').textContent=score;
  document.getElementById('resPts').textContent='Điểm quy đổi: '+pts+' / 10';
  document.getElementById('rgC').textContent=score;
  document.getElementById('rgW').textContent=8-score;
  document.getElementById('rgP').textContent=pts;
  document.getElementById('resultBox').style.display='block';

  // 🔥 GỬI ĐIỂM VỀ DATABASE (CHỖ CHI ĐANG THIẾU)
  fetch('../../php/quiz_score.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      lesson_id: 6,
      score: score,
      total_q: 8
    })
  })
  .then(res => res.json())
  .then(data => {
    if(data.ok) console.log("Lưu điểm thành công!");
    else console.error("Lỗi server:", data.error);
  })
  .catch(err => console.error("Lỗi kết nối API:", err));
}

function resetAll(){
  location.reload(); // Cách reset nhanh nhất và sạch nhất
}

function goBack(){
  window.location.href = "../student/student_dashboard.html";
}