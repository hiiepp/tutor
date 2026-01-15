<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/N14Lurny'; 
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom py-2 fixed-top-nav">
  <div class="container">
    <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="<?= $base_url ?>/index.php">
        <i class="bi bi-mortarboard-fill me-2 fs-3"></i> N14Lurny
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link" href="<?= $base_url ?>/index.php">Trang chủ</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $base_url ?>/find-class.php">Tìm lớp học</a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
                $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Học viên';
                $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
                $firstLetter = mb_substr($fullname, 0, 1, 'UTF-8');
                $role = $_SESSION['role'] ?? 'student';
            ?>

            <a href="#" class="text-secondary me-4 position-relative">
                <i class="bi bi-bell-fill fs-5"></i>
            </a>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="studentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px;">
                        <?php echo $firstLetter; ?>
                    </div>
                    <span class="fw-bold d-none d-sm-block"><?php echo htmlspecialchars($fullname); ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0 shadow border-0" aria-labelledby="studentDropdown">
                    <li class="p-3 border-bottom bg-light rounded-top">
                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($fullname); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars($email); ?></div>
                        <div class="badge bg-success mt-2">HỌC VIÊN</div>
                    </li>
                    
                    <li>
                        <a class="dropdown-item mt-2 py-2" href="<?= $base_url ?>/student/profile.php">
                            <i class="bi bi-person-circle me-2 text-success"></i> Hồ sơ cá nhân
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= $base_url ?>/student/dashboard.php">
                            <i class="bi bi-journal-bookmark-fill me-2 text-success"></i> Lớp học của tôi
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="#">
                            <span><i class="bi bi-wallet-fill me-2 text-success"></i> Ví học tập</span>
                            <span class="text-success fw-bold small">0 đ</span>
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider m-0"></li>
                    
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="<?= $base_url ?>/auth/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>

        <?php else: ?>
            <a href="<?= $base_url ?>/auth/login_register.php" class="btn btn-outline-success me-2 fw-semibold">Đăng nhập</a>
            <a href="<?= $base_url ?>/auth/login_register.php" class="btn btn-success fw-semibold">Đăng ký</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>