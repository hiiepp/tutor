<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      
<div class="col-md-5">
  <img src="assets/images/hinh1.png" class="img-fluid" alt="Wallpaper">
</div>


      <div class="col-md-7">
        <h1>Kết nối học viên và gia sư</h1>
        <p>
          Nền tảng gia sư hàng đầu Việt Nam. Tìm gia sư phù hợp
          hoặc chia sẻ kiến thức của bạn.
        </p>

        <form action="find-class.php" method="GET" class="search-box">
          
          <input 
            type="text" 
            name="keyword" 
            placeholder="Tìm kiếm môn học, gia sư..." 
            required
          >
          
          <button type="submit">Tìm kiếm</button>
        
        </form>

        <div class="mt-3">
          <a href="find-class.php" class="btn btn-success me-2">Tìm gia sư</a>
          <a href="auth/login_register.php" class="btn btn-outline-success">Tôi muốn làm gia sư</a>
        </div>
      </div>

    </div>
  </div>
</section>
<section class="why">
  <div class="container text-center">
    <h3>Tại sao chọn N14Lurny?</h3>
    <p class="text-muted mb-4">
      Chúng tôi cung cấp nền tảng kết nối hiệu quả và an toàn
    </p>

    <div class="row">
      <div class="col-md-4 why-item">
        <div class="icon bg-success">👨‍🏫</div>
        <h6>Gia sư được kiểm chứng</h6>
        <p>Tất cả gia sư đều được xác minh</p>
      </div>
      <div class="col-md-4 why-item">
        <div class="icon bg-info">🤝</div>
        <h6>Thanh toán trực tiếp</h6>
        <p>Không trung gian, không phát sinh</p>
      </div>
      <div class="col-md-4 why-item">
        <div class="icon bg-warning">⏰</div>
        <h6>Linh hoạt thời gian</h6>
        <p>Học online hoặc offline</p>
      </div>
    </div>
  </div>
</section>
<section class="why">
  <div class="container text-center">
    <h3>Tại sao chọn N14Lurny?</h3>
    <p class="text-muted mb-4">
      Chúng tôi cung cấp nền tảng kết nối hiệu quả và an toàn
    </p>

    <div class="row">
      <div class="col-md-4 why-item">
        <div class="icon bg-success">👨‍🏫</div>
        <h6>Gia sư được kiểm chứng</h6>
        <p>Tất cả gia sư đều được xác minh</p>
      </div>
      <div class="col-md-4 why-item">
        <div class="icon bg-info">🤝</div>
        <h6>Thanh toán trực tiếp</h6>
        <p>Không trung gian, không phát sinh</p>
      </div>
      <div class="col-md-4 why-item">
        <div class="icon bg-warning">⏰</div>
        <h6>Linh hoạt thời gian</h6>
        <p>Học online hoặc offline</p>
      </div>
    </div>
  </div>
</section>
<section class="steps text-center">
  <div class="container">
    <h3>Cách thức hoạt động</h3>
    <p class="text-muted mb-4">Quy trình đơn giản chỉ với 3 bước</p>

    <div class="row">
      <div class="col-md-4 step">
        <div class="circle">1</div>
        <h6>Tìm kiếm gia sư</h6>
        <p>Lựa chọn gia sư phù hợp</p>
      </div>
      <div class="col-md-4 step">
        <div class="circle">2</div>
        <h6>Trao đổi & kết nối</h6>
        <p>Thống nhất lịch và học phí</p>
      </div>
      <div class="col-md-4 step">
        <div class="circle">3</div>
        <h6>Bắt đầu học</h6>
        <p>Học tập hiệu quả</p>
      </div>
    </div>
  </div>
</section>
<section class="cta">
  <h3>Sẵn sàng bắt đầu?</h3>
  <p>Tham gia cộng đồng hàng nghìn học viên và gia sư</p>
  <a href="auth/login_register.php" class="btn btn-light me-2">Đăng ký ngay</a>
  <a href="find-class.php" class="btn btn-outline-light">Khám phá lớp học</a>
</section>

<footer class="footer">
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <h6>N14Lurny</h6>
        <p>Nền tảng kết nối gia sư</p>
      </div>
      <div class="col-md-3">
        <h6>Dành cho học viên</h6>
        <p>Tìm gia sư<br>Đăng ký học</p>
      </div>
      <div class="col-md-3">
        <h6>Dành cho gia sư</h6>
        <p>Tìm lớp<br>Đăng ký dạy</p>
      </div>
      <div class="col-md-3">
        <h6>Hỗ trợ</h6>
        <p>Liên hệ<br>Điều khoản</p>
      </div>
    </div>
  </div>
</footer>

</body>
</html>

