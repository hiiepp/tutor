<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login_register.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N14Lurny - Dành cho Gia sư</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-success" href="class_management.php">
        <i class="bi bi-mortarboard-fill"></i> N14Lurny
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'class_management.php') ? 'active fw-bold text-primary' : '' ?>" 
               href="class_management.php">
               Quản lý lớp học
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'new_class.php') ? 'active fw-bold text-primary' : '' ?>" 
               href="new_class.php">
               Đăng lớp mới
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'view_detail.php' || $current_page == 'personal_profile.php') ? 'active fw-bold text-primary' : '' ?>" 
               href="view_detail.php">
               Hồ sơ cá nhân
            </a>
        </li>

      </ul>

      <div class="d-flex align-items-center gap-3">
          <div class="d-flex align-items-center text-dark fw-medium">
            <div class="avatar avatar-sm bg-brand text-white me-2" style="width: 32px; height: 32px; font-size: 14px;">
                <?= strtoupper(substr($_SESSION['fullname'], 0, 1)) ?>
            </div>
            <span>Xin chào, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
          </div>

          <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i>
          </a>
      </div>
    </div>
  </div>
</nav>