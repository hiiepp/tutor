document.addEventListener("DOMContentLoaded", function () {
    
    // --- 1. Hiệu ứng khi cuộn trang (Sticky Shadow) ---
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            // Khi cuộn xuống hơn 10px
            navbar.classList.add('header-scrolled');
        } else {
            // Khi ở trên cùng
            navbar.classList.remove('header-scrolled');
        }
    });

    // --- 2. Tự động Active menu đang chọn ---
    // Lấy đường dẫn hiện tại (VD: /find-class.php)
    const currentLocation = location.href; 
    const menuItems = document.querySelectorAll('.nav-link');
    
    menuItems.forEach(item => {
        // So sánh link của menu với đường dẫn hiện tại
        if(item.href === currentLocation){
            item.classList.add("active");
            item.classList.add("fw-bold"); // In đậm
            item.style.color = "#28a745"; // Ép màu xanh lá
        }
    });
});