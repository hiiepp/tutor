<nav class="navbar navbar-expand-lg navbar-dark admin-navbar sticky-top">
  <div class="container-fluid">
    
    <a class="navbar-brand admin-brand" href="admin.php">
        <i class="fas fa-shield-alt me-2"></i>ADMIN
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item">
          <a class="nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>" href="../admin/dashboard.php">
            <i class="fas fa-chart-line me-1"></i> Tổng quan
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo ($activePage == 'users') ? 'active' : ''; ?>" href="../admin/users.php">
            <i class="fas fa-users me-1"></i> QL Người dùng
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo ($activePage == 'classes') ? 'active' : ''; ?>" href="../admin/classes.php">
            <i class="fas fa-book-open me-1"></i> QL Lớp học
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo ($activePage == 'reports') ? 'active' : ''; ?>" href="../admin/reports.php">
            <i class="fas fa-flag me-1"></i> QL Báo cáo
            <?php 
                $pending_reports = $conn->query("SELECT COUNT(*) as total FROM reports WHERE status='pending'")->fetch_assoc()['total'];
                if($pending_reports > 0): 
            ?>
                <span class="badge bg-danger ms-2 rounded-pill"><?= $pending_reports ?></span>
            <?php endif; ?>
          </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="fas fa-home me-1"></i> Xem Trang chủ
            </a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <span class="text-white me-3 d-none d-lg-block">
            Xin chào, <strong><?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin'; ?></strong>
        </span>
        <a href="../auth/logout.php" class="nav-link btn-logout">
            <i class="fas fa-sign-out-alt"></i> Đăng Xuất
        </a>
      </div>

    </div>
  </div>
</nav>