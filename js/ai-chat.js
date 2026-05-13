/**
 * js/ai-chat.js
 * AI Chat Widget — Sử Việt
 * 
 * Cách dùng: thêm vào cuối mỗi trang lesson / dashboard:
 *   <script>
 *     var AI_LESSON_ID   = 7;          // ID bài học (6/7/8), 0 nếu không trong bài
 *     var AI_LESSON_NAME = "Bài 7 — Kháng chiến chống Pháp";
 *     var AI_STUDENT_NAME = ""; // nếu có, điền tên học sinh (load từ profile)
 *   </script>
 *   <script src="../../js/ai-chat.js"></script>
 */

(function () {
  'use strict';

  // ── Config ──────────────────────────────────────────────
  var LESSON_ID   = (typeof AI_LESSON_ID   !== 'undefined') ? AI_LESSON_ID   : 0;
  var LESSON_NAME = (typeof AI_LESSON_NAME !== 'undefined') ? AI_LESSON_NAME : '';
  var STUDENT     = (typeof AI_STUDENT_NAME!== 'undefined') ? AI_STUDENT_NAME: '';

  var API_CHAT    = '../../php/process_ai_chat.php';
var API_REMIND  = '../../php/get_ai_reminder.php';

  var QUICK_QUESTIONS = {
    6: ['Thời cơ CM tháng Tám là gì?', 'Ý nghĩa ngày 2/9/1945?', 'Bài học kinh nghiệm?'],
    7: ['Chiến dịch Điện Biên Phủ khi nào?', 'Kế hoạch Na-va là gì?', 'Đường lối kháng chiến?'],
    8: ['Phong trào Đồng Khởi là gì?', 'Mậu Thân 1968 có ý nghĩa gì?', 'Ngày 30/4/1975?'],
    0: ['Tóm tắt bài 6', 'Tóm tắt bài 7', 'Tóm tắt bài 8']
  };

  // ── Tạo HTML ─────────────────────────────────────────────
  function buildHTML() {
    var qs = QUICK_QUESTIONS[LESSON_ID] || QUICK_QUESTIONS[0];
    var contextHTML = LESSON_NAME
      ? '<div class="ai-context-tag">📖 Đang hỗ trợ: ' + LESSON_NAME + '</div>'
      : '<div class="ai-context-tag">🏛️ Sử Việt — Trợ lý học tập</div>';

    var quickHTML = qs.map(function(q) {
      return '<button class="ai-quick" onclick="SuVietAI.ask(\'' + q.replace(/'/g,"\\'") + '\')">' + q + '</button>';
    }).join('');

    var html = ''
      + '<button class="ai-fab" id="aiFab" title="Trợ lý AI Sử Việt">'
      + '  🤖<span class="ai-fab-badge" id="aiBadge">!</span>'
      + '</button>'
      + '<div class="ai-chat-box" id="aiBox">'
      + '  <div class="ai-chat-header">'
      + '    <div class="ai-header-avatar">🏛️</div>'
      + '    <div class="ai-header-info">'
      + '      <div class="ai-header-name">Trợ lý Sử Việt</div>'
      + '      <div class="ai-header-sub"><span class="dot"></span>Sẵn sàng hỗ trợ</div>'
      + '    </div>'
      + '    <button class="ai-close-btn" id="aiClose">✕</button>'
      + '  </div>'
      + contextHTML
      + '  <div class="ai-messages" id="aiMessages">'
      + '    <div class="ai-typing" id="aiTyping"><span></span><span></span><span></span></div>'
      + '  </div>'
      + '  <div class="ai-quick-wrap" id="aiQuick">' + quickHTML + '</div>'
      // THAY THẾ đoạn .ai-input-row cũ trong hàm buildHTML:
      + '  <div class="ai-input-row">'
      + '    <label class="ai-file-btn" for="aiFile" title="Gửi ảnh bài tập">📎</label>'
      + '    <input type="file" id="aiFile" hidden accept="image/*">'
      + '    <textarea class="ai-input-field" id="aiInput" rows="1" placeholder="Hỏi tôi hoặc gửi ảnh bài tập..." maxlength="400"></textarea>'
      + '    <button class="ai-send-btn" id="aiSend">➤</button>'
      + '  </div>'
      + '</div>';

    var wrap = document.createElement('div');
    wrap.innerHTML = html;
    document.body.appendChild(wrap);
  }

  // ── Hiện/ẩn ──────────────────────────────────────────────
  var _open = false;
  function toggle() {
    _open = !_open;
    document.getElementById('aiBox').classList.toggle('open', _open);
    if (_open) {
      // Ẩn badge khi mở
      document.getElementById('aiBadge').classList.remove('show');
      document.getElementById('aiFab').classList.remove('has-reminder');
      document.getElementById('aiInput').focus();
    }
  }

  // ── Thêm bubble ──────────────────────────────────────────
  // THAY THẾ toàn bộ nội dung hàm addBubble cũ:
  function addBubble(text, type) {
    var msgs = document.getElementById('aiMessages');
    var typing = document.getElementById('aiTyping');
    var div = document.createElement('div');
    div.className = 'ai-bubble ' + type;
    
    var content = text.replace(/\n/g, '<br>');
    
    // Nếu là AI trả lời thì thêm nút loa
    if (type === 'ai') {
      div.innerHTML = content + '<button class="ai-speak-btn" title="Nghe AI đọc">🔊 Nghe đọc</button>';
      // Gán sự kiện click cho nút loa vừa tạo
      setTimeout(function() {
        var btn = div.querySelector('.ai-speak-btn');
        if (btn) {
          btn.onclick = function() { speakText(text, btn); };
        }
      }, 0);
    } else {
      div.innerHTML = content;
    }
    
    msgs.insertBefore(div, typing);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  function addReminder(text) {
    var msgs = document.getElementById('aiMessages');
    var typing = document.getElementById('aiTyping');
    var div = document.createElement('div');
    div.className = 'ai-bubble reminder';
    div.innerHTML = '<div class="reminder-label">Nhắc nhở</div>' + text.replace(/\n/g, '<br>');
    msgs.insertBefore(div, typing);
    msgs.scrollTop = msgs.scrollHeight;
  }

  function showTyping(show) {
    document.getElementById('aiTyping').className = 'ai-typing' + (show ? ' show' : '');
    var msgs = document.getElementById('aiMessages');
    msgs.scrollTop = msgs.scrollHeight;
  }

  // ── Gửi câu hỏi (Đã nâng cấp xử lý File & Text) ──────────────────────────
  function ask(question) {
    var fileInput = document.getElementById('aiFile');
    var file = fileInput ? fileInput.files[0] : null;

    // Nếu không có cả chữ lẫn file thì không gửi
    if (!question.trim() && !file) return;

    // Hiển thị nội dung người dùng gửi lên khung chat
    if (file) {
      addBubble('📎 <i>Đang gửi tệp tin kèm theo...</i><br>' + question, 'user');
    } else {
      addBubble(question, 'user');
    }

    // Ẩn các câu hỏi nhanh
    var quickWrap = document.getElementById('aiQuick');
    if (quickWrap) quickWrap.style.display = 'none';

    // Khóa giao diện nhập liệu khi đang đợi AI trả lời
    var sendBtn = document.getElementById('aiSend');
    var inputEl = document.getElementById('aiInput');
    sendBtn.disabled = true;
    inputEl.value = '';
    inputEl.style.height = '';
    showTyping(true);

    // Sử dụng FormData để truyền dữ liệu đa phương tiện
    var formData = new FormData();
    formData.append('question', question);
    formData.append('lesson_id', LESSON_ID);
    formData.append('lesson_name', LESSON_NAME);
    formData.append('student_name', STUDENT);
    if (file) {
      formData.append('file', file);
    }

    // Gửi yêu cầu tới Backend
    fetch(API_CHAT, {
      method: 'POST',
      body: formData // Với FormData, không cần đặt Header Content-Type thủ công
    })
    .then(function(r) { 
      return r.json(); 
    })
    .then(function(data) {
      showTyping(false);
      
      if (data.answer) {
        addBubble(data.answer, 'ai');

        // Gợi ý hỏi chuyên gia nếu AI trả lời xong
        setTimeout(function() {
          var msgs = document.getElementById('aiMessages');
          var typing = document.getElementById('aiTyping');
          var expertDiv = document.createElement('div');
          expertDiv.className = 'ai-expert-suggest';
          expertDiv.innerHTML = `
            <span>Vẫn chưa rõ?</span>
            <a href="hoi_chuyen_gia.html" class="expert-link">Hỏi chuyên gia ngay 🔒</a>
          `;
          msgs.insertBefore(expertDiv, typing);
          msgs.scrollTop = msgs.scrollHeight;
        }, 1000);

      } else if (data.error) {
        addBubble('⚠️ Lỗi hệ thống: ' + data.error, 'ai');
      }
    })
    .catch(function(e) {
      showTyping(false);
      addBubble('⚠️ Lỗi kết nối API. Chi kiểm tra lại XAMPP hoặc Key nhé!', 'ai');
      console.error('AI chat error:', e);
    })
    .finally(function() {
      sendBtn.disabled = false;
      if (fileInput) fileInput.value = ''; // Reset ô chọn file
      inputEl.focus();
    });
  }
  // ── Kiểm tra reminder từ Admin ────────────────────────────
  function checkReminder() {
    fetch(API_REMIND)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && data.message) {
          // Hiện badge trên nút fab
          document.getElementById('aiBadge').classList.add('show');
          document.getElementById('aiFab').classList.add('has-reminder');

          // Nếu chat đang mở thì hiện luôn, nếu không thì đợi mở
          if (_open) {
            addReminder(data.message);
          } else {
            // Lưu tạm, hiện khi mở chat
            window._pendingReminder = data.message;
          }
        }
      })
      .catch(function() {}); // Không báo lỗi — reminder là feature phụ
  }

  // ── Welcome message ───────────────────────────────────────
  function showWelcome() {
    var name = STUDENT ? STUDENT.split(' ').pop() : '';
    var greeting = name ? (name + ' ơi! ') : '';
    var msg = LESSON_NAME
      ? greeting + '🏛️ Tôi là trợ lý AI cho <b>' + LESSON_NAME + '</b>. Hỏi tôi bất kỳ điều gì về bài học này nhé!'
      : greeting + '🏛️ Chào bạn! Tôi là trợ lý AI của Sử Việt. Tôi có thể giúp bạn ôn tập Lịch sử Lớp 12.';
    addBubble(msg, 'ai');

    // Hiện pending reminder nếu có
    if (window._pendingReminder) {
      setTimeout(function() {
        addReminder(window._pendingReminder);
        window._pendingReminder = null;
      }, 600);
    }
  }

  // ── Sự kiện ──────────────────────────────────────────────
  function bindEvents() {
    document.getElementById('aiFab').addEventListener('click', function() {
      toggle();
      if (_open && document.getElementById('aiMessages').children.length <= 1) {
        showWelcome();
      }
    });
    document.getElementById('aiClose').addEventListener('click', toggle);

    var input = document.getElementById('aiInput');
    document.getElementById('aiSend').addEventListener('click', function() {
      ask(input.value.trim());
    });
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        ask(input.value.trim());
      }
    });
    // Auto-resize textarea
    input.addEventListener('input', function() {
      this.style.height = '';
      this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });
  }

  // ── Init ─────────────────────────────────────────────────
  function init() {
    buildHTML();
    bindEvents();
    // Kiểm tra reminder sau 1.5s (không chặn page load)
    setTimeout(checkReminder, 1500);
    // Kiểm tra lại mỗi 3 phút
    setInterval(checkReminder, 3 * 60 * 1000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // ── Public API ───────────────────────────────────────────
  window.SuVietAI = { ask: ask, toggle: toggle };
// THÊM hàm này vào gần cuối file js/ai-chat.js
  function speakText(text, btn) {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel(); // Dừng câu đang đọc cũ nếu có
      var msg = new SpeechSynthesisUtterance(text.replace(/<[^>]*>/g, '')); // Loại bỏ thẻ html khi đọc
      msg.lang = 'vi-VN'; // Đặt ngôn ngữ tiếng Việt
      msg.rate = 1.0;     // Tốc độ đọc
      
      window.speechSynthesis.speak(msg);

      // Hiệu ứng nút loa khi đang đọc
      btn.classList.add('playing');
      msg.onend = function() { btn.classList.remove('playing'); };
    } else {
      alert("Trình duyệt của bạn không hỗ trợ giọng nói.");
    }
  }
})();