(function () {
  'use strict';

  const API_PROFILE = '../../php/get_profile.php';

  function getInitials(name) {
    if (!name) return 'CH'; // Mặc định là Chi hoặc viết tắt của bạn
    const parts = name.trim().split(/\s+/).filter(Boolean);
    if (parts.length === 1) return parts[0][0].toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }

  function initProfile() {
    fetch(API_PROFILE)
      .then(res => res.json())
      .then(data => {
        if (data.status !== 'success') {
          window.location.href = '../../html/login.html';
          return;
        }

        // 1. Hiển thị Tên (Ưu tiên Fullname)
        const nameEl = document.getElementById('nameEl') || document.querySelector('.sidebar-menu .name');
        if (nameEl) {
          nameEl.textContent = (data.fullname || data.username || 'Học sinh').toUpperCase();
        }

        // 2. Xử lý Avatar
        const avatarEl = document.getElementById('avatarEl') || document.querySelector('.sidebar-menu .avatar');
        if (avatarEl) {
          // Trường hợp 1: Có ảnh upload trong thư mục (data.avatar)
          if (data.avatar) {
            avatarEl.innerHTML = '';
            const img = document.createElement('img');
            img.src = '../../uploads/' + data.avatar;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;';
            
            // Nếu file ảnh trên host bị lỗi, tự động chuyển sang hiện chữ cái đầu
            img.onerror = function () {
              avatarEl.innerHTML = '';
              avatarEl.textContent = getInitials(data.fullname || data.username);
            };
            avatarEl.appendChild(img);
          } 
          // Trường hợp 2: Có link ảnh URL (data.avatar_url)
          else if (data.avatar_url) {
            let url = data.avatar_url.replace('http://', 'https://');
            avatarEl.innerHTML = `<img src="${url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" 
                                   onerror="this.parentElement.textContent='${getInitials(data.fullname)}'">`;
          }
          // Trường hợp 3: Không có ảnh nào cả -> Hiện chữ cái đầu
          else {
            avatarEl.textContent = getInitials(data.fullname || data.username);
          }
        }
      })
      .catch(err => console.warn('Lỗi load profile:', err));
  }

  document.addEventListener('DOMContentLoaded', initProfile);
})();