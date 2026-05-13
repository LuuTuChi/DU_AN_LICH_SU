document.addEventListener("DOMContentLoaded", function () {

  /* ═══════════════════════════════════════
     LOGIN
  ═══════════════════════════════════════ */
  const loginForm = document.getElementById('loginForm');

  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData     = new FormData(this);
      const overlay      = document.getElementById('successOverlay');
      const successMsg   = document.getElementById('successMsg');
      const btnLogin     = this.querySelector('.btn-login');

      // Disable nút tránh bấm nhiều lần
      btnLogin.disabled = true;
      btnLogin.textContent = 'Đang xử lý...';

      fetch('../php/login_action.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {

          if (data.status === 'success') {

          if (data.role === 'admin') {
                successMsg.innerText = 'Chào Quản trị viên — đang vào trang quản lý...';
            } else if (data.role === 'teacher') {
                successMsg.innerText = 'Chào Mentor — lớp học đang được chuẩn bị...';
            } else {
                successMsg.innerText = 'Chào Sĩ tử — tiếp tục hành trình học tập...';
            }
            overlay.style.display = 'flex';

            // Redirect
            // 🚀 CHÈN ĐOẠN ĐIỀU HƯỚNG MỚI NÀY VÀO:
            setTimeout(() => {
              if (data.role === 'admin') {
                // Nếu là Admin đi vào Dashboard Admin
                window.location.href = '../html/admin/admin_dashboard.php';
              } else if (data.role === 'teacher') {
                // MỚI: Nếu là Giáo viên đi vào Dashboard Teacher
                window.location.href = '../html/teacher/teacher_dashboard.php';
              } else if (data.hasProfile === false) {
                // Nếu là Học sinh chưa có profile
                window.location.href = '../php/student_profile.php';
              } else {
                // Nếu là Học sinh bình thường
                window.location.href = '../html/student/student_dashboard.html';
              }
            }, 1400);

          } else {
            alert(data.message || 'Đăng nhập thất bại!');
            btnLogin.disabled = false;
            btnLogin.textContent = 'Vào học ngay';
          }

        })
        .catch(() => {
          alert('Lỗi kết nối server!');
          btnLogin.disabled = false;
          btnLogin.textContent = 'Vào học ngay';
        });
    });
  }


  /* ═══════════════════════════════════════
     REGISTER
  ═══════════════════════════════════════ */
  const regForm = document.getElementById('regForm');

  if (regForm) {
    regForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Validate mật khẩu khớp nhau
      const pw  = this.querySelector('[name="password"]').value;
      const cpw = this.querySelector('[name="confirm_password"]').value;
      if (pw !== cpw) {
        alert('Mật khẩu xác nhận không khớp!');
        return;
      }

      const formData  = new FormData(this);
      const tank      = document.getElementById('tankUnit');
      const overlay   = document.getElementById('successOverlay');
      const btnReg    = this.querySelector('.btn-register');
      const formCard  = this.closest('.form-card') || this;

      btnReg.disabled = true;
      btnReg.textContent = 'Đang xử lý...';

      fetch('../php/register_action.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.text())
        .then(data => {

          if (data.trim() === 'success') {

            // 🔥 Kích hoạt xe tăng
            if (tank) {
              tank.classList.add('tank-active');

              // Sau khi xe tăng chạy vào (~1.1s) → form đổ xuống
              setTimeout(() => {
                formCard.classList.add('form-fall-back');
              }, 1000);

              // Hiện success overlay sau khi animation xong
              setTimeout(() => {
                overlay.style.display = 'flex';
              }, 1800);
            } else {
              // Không có xe tăng thì hiện overlay luôn
              overlay.style.display = 'flex';
            }

            // Redirect sang login
            setTimeout(() => {
              window.location.href = 'login.html';
            }, 3200);

          } else {
            alert(data || 'Đăng ký thất bại!');
            btnReg.disabled = false;
            btnReg.textContent = 'Đăng ký ngay';
          }

        })
        .catch(() => {
          alert('Lỗi hệ thống!');
          btnReg.disabled = false;
          btnReg.textContent = 'Đăng ký ngay';
        });
    });
  }

});