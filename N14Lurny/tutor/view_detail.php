<?php 
session_start();
include '../includes/header_tutor.php'; 
require_once '../config/db.php';

// ... (Giữ nguyên phần xử lý ID và lấy thông tin user ở trên) ...
$tutor_id = 0;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tutor_id = intval($_GET['id']);
} elseif (isset($_SESSION['user_id'])) {
    $tutor_id = $_SESSION['user_id'];
} else {
    echo "<script>alert('Không tìm thấy thông tin!'); window.history.back();</script>"; exit();
}

$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$user_res = $stmt->get_result();
if ($user_res->num_rows == 0) {
    echo "<div class='container py-5 text-center'><h3>Tài khoản không tồn tại.</h3></div>"; include '../includes/footer.php'; exit();
}
$tutor = $user_res->fetch_assoc();

// --- MỚI: LẤY ẢNH MINH CHỨNG ---
$proofs_res = $conn->query("SELECT * FROM tutor_proofs WHERE user_id = $tutor_id ORDER BY id DESC");

// ... (Giữ nguyên phần Logic Avatar, Tuổi, Thống kê lớp) ...
$avatar_path = "../assets/uploads/avatars/" . ($tutor['avatar'] ?? '');
$has_avatar = !empty($tutor['avatar']) && file_exists($avatar_path);
$initials = mb_strtoupper(mb_substr($tutor['full_name'], 0, 1, "UTF-8"));

if (!empty($tutor['dob']) && $tutor['dob'] != '0000-00-00') {
    $dob_date = new DateTime($tutor['dob']);
    $now = new DateTime();
    $age = $now->diff($dob_date)->y;
    $age_display = $age . ' tuổi';
    $dob_display = date('d/m/Y', strtotime($tutor['dob']));
} else {
    $age_display = 'Chưa cập nhật'; $dob_display = 'Chưa cập nhật';
}

$count_all = $conn->query("SELECT COUNT(*) as total FROM classes WHERE tutor_id = $tutor_id")->fetch_assoc()['total'];
$count_active = $conn->query("SELECT COUNT(*) as total FROM classes WHERE tutor_id = $tutor_id AND status = 'active'")->fetch_assoc()['total'];

$sql_classes = "SELECT * FROM classes WHERE tutor_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 4";
$stmt_classes = $conn->prepare($sql_classes);
$stmt_classes->bind_param("i", $tutor_id);
$stmt_classes->execute();
$res_classes = $stmt_classes->get_result();

$is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $tutor_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hồ sơ: <?= htmlspecialchars($tutor['full_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/tutor.css">
  <style>
      /* CSS cho Gallery ảnh */
      .proof-gallery { display: flex; flex-wrap: wrap; gap: 10px; }
      .proof-img { width: 120px; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: transform 0.2s; border: 1px solid #eee; }
      .proof-img:hover { transform: scale(1.05); border-color: #80BA4C; }
  </style>
</head>
<body class="bg-light">

<div class="container py-5">
  <a href="javascript:history.back()" class="back-link mb-3">
      <i class="bi bi-arrow-left"></i> Quay lại
  </a>
  
  <div class="row g-4">
    <div class="col-lg-8">

      <div class="card card-detail mb-4">
        <div class="d-flex flex-column flex-md-row gap-4 align-items-center align-items-md-start text-center text-md-start">
            <?php if($has_avatar): ?>
                <img src="<?= $avatar_path ?>" class="rounded-circle object-fit-cover shadow-sm" style="width: 100px; height: 100px; border: 3px solid #80BA4C;">
            <?php else: ?>
                <div class="avatar-circle" style="width: 100px; height: 100px; font-size: 40px; margin: 0;"><?= $initials ?></div>
            <?php endif; ?>

            <div class="flex-grow-1">
                <h2 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($tutor['full_name']) ?></h2>
                <div class="text-warning mb-3"><i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <span class="text-muted small ms-1">(Tài khoản uy tín)</span></div>
                <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                    <div class="bg-light px-4 py-2 rounded border"><strong class="d-block text-brand fs-5"><?= $count_all ?></strong> <small class="text-muted">Tổng lớp</small></div>
                    <div class="bg-light px-4 py-2 rounded border"><strong class="d-block text-brand fs-5"><?= $count_active ?></strong> <small class="text-muted">Đang mở</small></div>
                </div>
            </div>
            <?php if($is_owner): ?>
            <div><a href="personal_profile.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil-square"></i> Sửa hồ sơ</a></div>
            <?php endif; ?>
        </div>
      </div>
        
      <div class="card card-detail mb-4">
         <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
             <h5 class="section-title mb-0 border-0 p-0"><i class="bi bi-person-lines-fill"></i> Giới thiệu bản thân</h5>
         </div>
         <div class="text-dark" style="line-height: 1.6;">
             <?php if (!empty($tutor['bio'])): ?>
                 <?= nl2br(htmlspecialchars($tutor['bio'])) ?>
             <?php else: ?>
                 <p class="text-muted fst-italic">Gia sư chưa cập nhật phần giới thiệu.</p>
             <?php endif; ?>
         </div>
      </div>

      <?php if($proofs_res->num_rows > 0): ?>
      <div class="card card-detail mb-4">
         <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
             <h5 class="section-title mb-0 border-0 p-0"><i class="bi bi-images"></i> Hồ sơ năng lực & Bằng cấp</h5>
         </div>
         <div class="proof-gallery">
             <?php while($img = $proofs_res->fetch_assoc()): ?>
                 <img src="../assets/uploads/proofs/<?= $img['image_path'] ?>" class="proof-img" data-bs-toggle="modal" data-bs-target="#imgModal" onclick="showImage(this.src)">
             <?php endwhile; ?>
         </div>
      </div>
      <?php endif; ?>

      <div class="card card-detail">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="section-title mb-0 border-0 p-0"><i class="bi bi-collection-play"></i> Lớp đang tìm học viên</h5>
          <?php if($is_owner): ?>
            <a href="class_management.php" class="text-brand text-decoration-none small fw-bold">Quản lý lớp <i class="bi bi-arrow-right"></i></a>
          <?php endif; ?>
        </div>

        <?php if ($res_classes->num_rows > 0): ?>
        <div class="row g-3">
            <?php while($class = $res_classes->fetch_assoc()): ?>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-white hover-shadow transition-all position-relative">
                    <div class="mb-2">
                         <span class="badge green"><?= htmlspecialchars($class['subject']) ?></span>
                         <span class="badge gray small"><?= htmlspecialchars($class['method']) ?></span>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">
                        <a href="see_details.php?id=<?= $class['id'] ?>" class="text-decoration-none text-dark stretched-link">
                            <?= htmlspecialchars($class['title']) ?>
                        </a>
                    </h6>
                    <div class="text-primary fw-bold mb-2"><?= htmlspecialchars($class['price']) ?></div>
                    <ul class="list-unstyled small text-muted mb-3">
                        <li class="mb-1"><i class="bi bi-mortarboard me-2"></i> <?= htmlspecialchars($class['grade']) ?></li>
                        <li><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($class['location']) ?></li>
                    </ul>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <div class="text-center text-muted py-4">Hiện tại chưa có lớp nào đang mở.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-detail">
        <h5 class="section-title"><i class="bi bi-info-circle"></i> Thông tin chi tiết</h5>
        <ul class="list-unstyled m-0">
            <li class="info-list-item"><span class="text-muted">Số điện thoại</span><span class="fw-medium text-end ps-2 text-dark"><?= htmlspecialchars($tutor['phone'] ?? 'Chưa cập nhật') ?></span></li>
            <li class="info-list-item"><span class="text-muted">Email</span><span class="fw-medium text-end ps-2 text-dark"><?= htmlspecialchars($tutor['email']) ?></span></li>
            <li class="info-list-item"><span class="text-muted">Ngày sinh</span><span class="fw-medium text-end ps-2 text-dark"><?= $dob_display ?></span></li>
            <li class="info-list-item"><span class="text-muted">Tuổi</span><span class="fw-medium text-end ps-2 text-dark"><?= $age_display ?></span></li>
            
            <li class="info-list-item"><span class="text-muted">Giới tính</span><span class="fw-medium text-end ps-2 text-dark"><?= htmlspecialchars($tutor['gender'] ?? 'Chưa cập nhật') ?></span></li>

            <li class="info-list-item"><span class="text-muted">Chuyên ngành</span><span class="fw-medium text-end ps-2"><?= htmlspecialchars($tutor['major'] ?? 'Chưa cập nhật') ?></span></li>
            <li class="info-list-item"><span class="text-muted">Trình độ</span><span class="fw-medium text-end ps-2"><?= htmlspecialchars($tutor['degree'] ?? 'Chưa cập nhật') ?></span></li>
            <li class="info-list-item"><span class="text-muted">Kinh nghiệm</span><span class="fw-medium text-end ps-2"><?= htmlspecialchars($tutor['experience'] ?? 'Chưa cập nhật') ?></span></li>
            <li class="info-list-item"><span class="text-muted">Khu vực</span><span class="fw-medium text-end ps-2"><?= htmlspecialchars($tutor['address'] ?? 'Chưa cập nhật') ?></span></li>
            <li class="info-list-item border-0"><span class="text-muted">Tham gia từ</span><span class="fw-medium"><?= date('m/Y', strtotime($tutor['created_at'])) ?></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 text-center position-relative">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        <img id="modalImage" src="" class="img-fluid rounded shadow-lg">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showImage(src) {
    document.getElementById('modalImage').src = src;
}
</script>
</body>
</html>