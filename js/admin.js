// DUAN_LICHSU/js/admin.js
document.addEventListener('DOMContentLoaded', function () {
   
    // 1. LẤY CÁC PHẦN TỬ MENU VÀ NỘI DUNG
    const menuItems = document.querySelectorAll('#main-menu li');
    const sections = document.querySelectorAll('.content-section');

    // 2. LẮNG NGHE SỰ KIỆN CLICK MENU
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Loại bỏ class 'active' khỏi tất cả menu
            menuItems.forEach(i => i.classList.remove('active'));
            // Thêm 'active' vào menu vừa click
            this.classList.add('active');

            // Ẩn tất cả các Section nội dung
            sections.forEach(sec => sec.classList.remove('active'));

            // Hiển thị Section tương ứng dựa trên data-target
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });

    // 3. (TÙY CHỌN) HIỆU ỨNG KHI CLICK VÀO Ô HEATMAP
    const cells = document.querySelectorAll('.cell');
    cells.forEach(cell => {
        cell.addEventListener('click', function() {
            const title = this.getAttribute('title') || "Thông tin chi tiết chuyên đề";
            alert("Nội dung: " + title);
        });
    });
});


