<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Gia sư - Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-info text-center">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); 
                ?>
            </div>
        <?php endif; ?>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation" style="width: 50%">
                <button class="nav-link active w-100" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button">Đăng nhập</button>
            </li>
            <li class="nav-item" role="presentation" style="width: 50%">
                <button class="nav-link w-100" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button">Đăng ký</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade show active" id="pills-login">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                    <button type="submit" name="btn_login" class="btn btn-primary w-100 py-2 fw-bold">Đăng nhập</button>
                </form>
            </div>

            <div class="tab-pane fade" id="pills-register">
                <form action="register.php" method="POST" id="registerForm">
                    <label class="form-label fw-bold">Bạn là:</label>
                    <div class="role-selector mb-3">
                        <div class="role-option">
                            <input type="radio" name="role" id="roleStudent" value="student" class="role-radio" checked>
                            <label for="roleStudent"><i class="fas fa-user-graduate"></i> Học viên</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="roleTutor" value="tutor" class="role-radio">
                            <label for="roleTutor"><i class="fas fa-chalkboard-teacher"></i> Gia sư</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-3 d-none" id="phone-group">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" 
                               name="phone" 
                               id="phone-input" 
                               class="form-control" 
                               placeholder="VD: 0912345678" 
                               maxlength="10" 
                               pattern="(03|05|07|08|09)+([0-9]{8})\b"
                               title="Số điện thoại phải có 10 số và bắt đầu bằng đầu số hợp lệ của Việt Nam (03, 05, 07, 08, 09)">
                        <div class="form-text text-muted small">Nhập chính xác 10 số điện thoại để phụ huynh liên hệ.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Tự tạo mật khẩu đăng nhập" required>
                    </div>

                    <button type="submit" name="btn_register" class="btn btn-primary w-100 py-2 fw-bold">Đăng ký ngay</button>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center border-top pt-3">
            <a href="../index.php" class="text-decoration-none text-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleRadios = document.querySelectorAll('.role-radio');
    const phoneGroup = document.getElementById('phone-group');
    const phoneInput = document.getElementById('phone-input');

    // 1. Hàm bật/tắt ô nhập SĐT theo vai trò
    function togglePhoneField() {
        const isTutor = document.getElementById('roleTutor').checked;
        if (isTutor) {
            phoneGroup.classList.remove('d-none');
            phoneInput.setAttribute('required', 'required');
            phoneInput.focus();
        } else {
            phoneGroup.classList.add('d-none');
            phoneInput.removeAttribute('required');
            phoneInput.value = '';
        }
    }

    // 2. Hàm chỉ cho phép nhập số
    phoneInput.addEventListener('input', function(e) {
        // Loại bỏ mọi ký tự không phải số
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Nếu số đầu tiên không phải là 0, xóa nó đi (Logic tùy chọn, ở đây ta để người dùng tự sửa nhưng regex sẽ chặn lúc submit)
    });

    // 3. Sự kiện thay đổi Radio
    roleRadios.forEach(radio => {
        radio.addEventListener('change', togglePhoneField);
    });
});
</script>

</body>
</html>