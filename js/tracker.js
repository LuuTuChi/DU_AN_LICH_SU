// js/tracker.js
// Gắn vào tất cả lesson6/7/8.html — tự động track mọi hoạt động
// Không cần sửa gì — chỉ cần khai báo LESSON_ID trước khi include:
//   <script>var LESSON_ID = 6;</script>
//   <script src="../../js/tracker.js"></script>

(function () {
  'use strict';

  var API = '../../php/';
  var lid = window.LESSON_ID || 0;
  if (!lid) return; // không track nếu không có lesson ID

  var sessionId   = null;
  var sessionStart = Date.now();
  var cardCount   = 0;
  var cardTimer   = null;

  // ── 1. Bắt đầu session khi tải trang ─────────────────
  function startSession() {
    fetch(API + 'track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'session_start', lesson_id: lid })
    })
    .then(function(r) { return r.json(); })
    .then(function(d) { sessionId = d.session_id || null; });
  }

  // ── 2. Kết thúc session (đóng tab / thoát) ──────────
  function endSession() {
    if (!sessionId) return;
    var duration = Math.round((Date.now() - sessionStart) / 1000);
    // dùng sendBeacon để đảm bảo gửi được khi đóng tab
    navigator.sendBeacon(API + 'track.php', JSON.stringify({
      action:     'session_end',
      lesson_id:  lid,
      session_id: sessionId,
      duration_s: duration
    }));
  }

  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') endSession();
    if (document.visibilityState === 'visible') {
      // Mở lại tab → bắt session mới
      sessionStart = Date.now();
      startSession();
    }
  });
  window.addEventListener('beforeunload', endSession);

  // ── 3. Track khi mở tab ──────────────────────────────
  // Gọi hàm này từ showTab() trong lesson HTML
  window.trackTab = function (tabName) {
    fetch(API + 'track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'open_tab', lesson_id: lid, tab: tabName })
    });
  };

  // ── 4. Track flashcard flip ──────────────────────────
  // Tự động detect click vào .flashcard
  document.addEventListener('click', function (e) {
    if (e.target.closest('.flashcard')) {
      cardCount++;
      clearTimeout(cardTimer);
      // Gộp nhiều lần lật trong 3s → 1 request
      cardTimer = setTimeout(function () {
        if (cardCount > 0) {
          fetch(API + 'track.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'flip_card', lesson_id: lid, count: cardCount })
          });
          cardCount = 0;
        }
      }, 3000);
    }
  });

  // ── 5. Nút "Hoàn thành bài" ──────────────────────────
  window.completeLesson = function () {
    fetch(API + 'track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'complete_lesson', lesson_id: lid })
    }).then(function () {
      // Redirect về dashboard sau khi ghi xong
      window.location.href = '../student/student_dashboard.html';
    });
  };

  // Khởi động
  startSession();

})();