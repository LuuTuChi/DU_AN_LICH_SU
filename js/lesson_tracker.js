// js/lesson_tracker.js
let startTime = Date.now();
const lessonId = window.location.pathname.split('/').pop().replace('.html', ''); // Lấy tên file làm ID (vd: lesson6)

// 1. Gửi dữ liệu khi bắt đầu (Start Session)
function trackStart() {
    fetch('../../php/track.php', {
        method: 'POST',
        body: JSON.stringify({
            action: 'start_session',
            lesson_id: lessonId
        })
    });
}

// 2. Tự động tính % hoàn thành dựa trên việc cuộn trang
window.onscroll = function() {
    let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    let scrolled = Math.round((winScroll / height) * 100);

    if (scrolled >= 90) { // Nếu cuộn xuống 90% trang
        updateProgress(100);
    }
};

function updateProgress(percent) {
    fetch('../../php/track.php', {
        method: 'POST',
        body: JSON.stringify({
            action: 'update_progress',
            lesson_id: lessonId,
            progress: percent
        })
    });
}

// 3. Gửi dữ liệu khi đóng trang (End Session)
window.addEventListener('beforeunload', function () {
    let duration = Math.round((Date.now() - startTime) / 1000); // Tính bằng giây
    navigator.sendBeacon('../../php/track.php', JSON.stringify({
        action: 'end_session',
        lesson_id: lessonId,
        duration: duration
    }));
});

// Chạy khởi tạo
document.addEventListener('DOMContentLoaded', trackStart);