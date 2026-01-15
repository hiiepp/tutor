/**
 * ADMIN MAIN JAVASCRIPT - VERSION 2.0
 * Tích hợp: Live Search, SweetAlert, Counter, Form Handling
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================================
    // 1. DASHBOARD: HIỆU ỨNG SỐ NHẢY (COUNTER ANIMATION)
    // ============================================================
    const counters = document.querySelectorAll('.counter-value');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const speed = 200; // Tốc độ đếm (càng cao càng chậm)
        
        const updateCount = () => {
            const current = +counter.innerText;
            const inc = target / speed;

            if (current < target) {
                counter.innerText = Math.ceil(current + inc);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });

    // ============================================================
    // 2. FORM: BẬT/TẮT HIỂN THỊ MẬT KHẨU
    // ============================================================
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('input[name="password"]');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Toggle type
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle Icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // ============================================================
    // 3. FORM: HIỆU ỨNG LOADING KHI SUBMIT
    // ============================================================
    const adminForms = document.querySelectorAll('form');
    adminForms.forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang xử lý...';
                btn.disabled = true;
            }
        });
    });

    // ============================================================
    // 4. SIDEBAR: XÁC NHẬN ĐĂNG XUẤT
    // ============================================================
    const logoutBtn = document.querySelector('.btn-logout');
    if(logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');

            Swal.fire({
                title: 'Đăng xuất?',
                text: "Bạn có muốn thoát khỏi hệ thống không?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đăng xuất',
                cancelButtonText: 'Ở lại'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    }

    // ============================================================
    // 5. GLOBAL: XÓA DỮ LIỆU (Giữ nguyên logic cũ)
    // ============================================================
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            const deleteUrl = this.getAttribute('href');
            const itemName = this.getAttribute('data-name') || 'mục này';
            
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: `Dữ liệu "${itemName}" sẽ bị xóa vĩnh viễn!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Vâng, xóa đi!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });

    // ============================================================
    // 6. GLOBAL: TÌM KIẾM LIVE SEARCH (Giữ nguyên logic cũ)
    // ============================================================
    const searchInput = document.getElementById('liveSearchInput');
    const tableId = searchInput ? searchInput.getAttribute('data-table-id') : null;
    
    if (searchInput && tableId) {
        const table = document.getElementById(tableId);
        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = table.querySelectorAll('tbody tr:not(.no-result-row)');
            let hasResult = false;
    
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                    hasResult = true;
                } else {
                    row.style.display = 'none';
                }
            });
            let noResultRow = document.getElementById('liveSearchNoResult');
            if (noResultRow) noResultRow.style.display = hasResult ? 'none' : 'table-row';
        });
    }

    // ============================================================
    // 7. GLOBAL: TOAST MESSAGE (Giữ nguyên logic cũ)
    // ============================================================
    const urlParams = new URLSearchParams(window.location.search);
    const msgType = urlParams.get('msg');

    if (msgType) {
        let title = 'Thao tác thành công!';
        let icon = 'success';
        if (msgType === 'deleted') title = 'Đã xóa dữ liệu thành công!';
        else if (msgType === 'error') { title = 'Có lỗi xảy ra!'; icon = 'error'; }

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });
        Toast.fire({ icon: icon, title: title });
        window.history.replaceState(null, null, window.location.pathname);
    }
});