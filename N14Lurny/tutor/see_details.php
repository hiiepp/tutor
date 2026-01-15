<?php 
session_start();
include '../includes/header_tutor.php'; 
require_once '../config/db.php'; 

// 1. Kiểm tra ID hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Không tìm thấy lớp học!'); window.location.href='class_management.php';</script>";
    exit();
}

$class_id = intval($_GET['id']);
$tutor_id = $_SESSION['user_id']; 

// 2. Truy vấn dữ liệu Lớp học
$sql = "SELECT c.*, u.full_name as poster_name 
        FROM classes c 
        LEFT JOIN users u ON c.tutor_id = u.id 
        WHERE c.id = ? AND c.tutor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $class_id, $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='container py-5 text-center'>
            <h3>Lớp học không tồn tại hoặc bạn không có quyền truy cập.</h3>
            <a href='class_management.php' class='btn btn-brand'>Quay lại</a>
          </div>";
    exit();
}

$row = $result->fetch_assoc();

// 3. Lấy danh sách học viên
$sql_students = "SELECT r.*, u.full_name, u.email, u.phone
                 FROM class_registrations r 
                 JOIN users u ON r.student_id = u.id 
                 WHERE r.class_id = ? 
                 ORDER BY FIELD(r.status, 'pending', 'accepted', 'rejected'), r.created_at DESC";
$stmt_students = $conn->prepare($sql_students);
$stmt_students->bind_param("i", $class_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

// 4. Đếm số lượng học viên ĐÃ ĐƯỢC DUYỆT
$count_accepted_sql = "SELECT COUNT(*) as total FROM class_registrations WHERE class_id = ? AND status = 'accepted'";
$stmt_count = $conn->prepare($count_accepted_sql);
$stmt_count->bind_param("i", $class_id);
$stmt_count->execute();
$current_accepted = $stmt_count->get_result()->fetch_assoc()['total'];

$max_students = $row['max_students'] ?? 1;

// 5. Xử lý hiển thị Trạng thái
$badge_class = '';
$status_text = '';
switch ($row['status']) {
    case 'active':
        $badge_class = 'badge green'; 
        $status_text = 'Đang tuyển sinh';
        break;
    case 'hidden':
        $badge_class = 'badge red'; 
        $status_text = 'Đã đóng / Đủ học viên';
        break;
    default:
        $badge_class = 'badge gray';
        $status_text = 'Chờ duyệt';
        break;
    }

// 6. XỬ LÝ HIỂN THỊ GIÁ
$price_raw = $row['price'];
$price_display = '';
$unit_display = '';

if (is_numeric($price_raw)) {
    $price_display = number_format($price_raw, 0, ',', '.') . ' đ';
    $unit_display = '/ buổi'; 
} elseif (preg_match('/^(\d+)\s+(.*)$/', $price_raw, $matches)) {
    $price_display = number_format($matches[1], 0, ',', '.') . ' đ';
    $unit_display = (strpos($matches[2], '/') === false) ? '/ ' . str_replace('VND', '', $matches[2]) : str_replace('VND', '', $matches[2]);
} else {
    $price_display = $price_raw;
    $unit_display = '';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý lớp: <?= htmlspecialchars($row['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/N14LURNY/assets/css/tutor.css">
  <style>
      .no-arrow::after { display: none; }
      .dropdown-item:active { background-color: var(--brand-color); }
  </style>
</head>
<body class="bg-light">

<div class="container py-5">
  <a href="class_management.php" class="back-link mb-3">
      <i class="bi bi-arrow-left"></i> Quay lại danh sách lớp
  </a>

  <?php if(isset($_SESSION['message'])): ?>
      <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['message'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <div class="row g-4">
    
    <div class="col-lg-8">
      
      <div class="card card-detail mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h5 class="section-title mb-0 border-0 p-0"><i class="bi bi-people-fill"></i> Danh sách đăng ký</h5>
            
            <div class="text-end">
                <span class="text-muted small d-block">Tiến độ tuyển sinh</span>
                <span class="fw-bold text-success fs-5"><?= $current_accepted ?></span> 
                <span class="text-muted">/ <?= $max_students ?> học viên</span>
            </div>
        </div>
        
        <?php if ($result_students->num_rows > 0): ?>
            <div class="d-flex flex-column gap-3">
                <?php while($stu = $result_students->fetch_assoc()): 
                    $stuInitials = mb_strtoupper(mb_substr($stu['full_name'], 0, 1, "UTF-8"));
                    
                    $status_label = '';
                    $status_class = '';
                    $bg_color = 'bg-white';
                    
                    if($stu['status'] == 'pending') {
                        $status_label = 'Chờ duyệt';
                        $status_class = 'badge gray';
                    } elseif($stu['status'] == 'accepted') {
                        $status_label = 'Đã nhận lớp';
                        $status_class = 'badge green';
                        $bg_color = 'bg-success bg-opacity-10 border-success';
                    } else {
                        $status_label = 'Đã từ chối';
                        $status_class = 'badge red';
                        $bg_color = 'bg-light opacity-75';
                    }
                ?>
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 border rounded <?= $bg_color ?> hover-shadow transition-all">
                    
                    <div class="d-flex align-items-start mb-3 mb-md-0">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold me-3 border shadow-sm" 
                             style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <?= $stuInitials ?>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">
                                <?= htmlspecialchars($stu['full_name']) ?>
                                <span class="<?= $status_class ?> ms-2"><?= $status_label ?></span>
                            </div>
                            
                            <div class="small text-muted mb-1">
                                <i class="bi bi-clock me-1"></i><?= date('H:i d/m/Y', strtotime($stu['created_at'])) ?>
                            </div>

                            <?php if($stu['status'] == 'accepted'): ?>
                                <div class="mt-1 small">
                                    <span class="me-2"><i class="bi bi-telephone-fill text-success"></i> <?= htmlspecialchars($stu['phone'] ?? 'Chưa cập nhật') ?></span>
                                    <span><i class="bi bi-envelope-fill text-primary"></i> <?= htmlspecialchars($stu['email']) ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($stu['message'])): ?>
                                <div class="mt-2 small fst-italic text-secondary bg-white p-2 rounded border border-dashed">
                                    <i class="bi bi-chat-quote-fill text-muted me-1"></i> "<?= htmlspecialchars($stu['message']) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-end d-flex align-items-center gap-2">
                        <?php if ($stu['status'] == 'pending'): ?>
                            <?php if ($row['status'] == 'active' && $current_accepted < $max_students): ?>
                                <a href="handle_request.php?reg_id=<?= $stu['id'] ?>&class_id=<?= $class_id ?>&action=accept" 
                                   class="btn btn-sm btn-success fw-bold px-3 shadow-sm"
                                   onclick="return confirm('Xác nhận nhận học viên này? \n(Nếu đủ số lượng, lớp sẽ tự động đóng).')">
                                    <i class="bi bi-check-lg"></i> Duyệt
                                </a>
                                <a href="handle_request.php?reg_id=<?= $stu['id'] ?>&class_id=<?= $class_id ?>&action=reject" 
                                   class="btn btn-sm btn-outline-danger fw-bold px-3"
                                   onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?')">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php else: ?>
                                <span class="badge red"><i class="bi bi-lock-fill"></i> Lớp đã đóng</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="80" class="opacity-25 mb-3" alt="Empty">
                <p class="text-muted fw-medium">Chưa có yêu cầu đăng ký nào.</p>
            </div>
        <?php endif; ?>
      </div>

      <div class="card card-detail">
        <h5 class="section-title"><i class="bi bi-file-text"></i> Nội dung lớp học</h5>
        <div class="text-dark" style="line-height: 1.6;">
            <?= nl2br(htmlspecialchars($row['description'] ?? 'Chưa có mô tả')) ?>
        </div>
      </div>

    </div>

    <div class="col-lg-4">
      
      <div class="card card-detail">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <span class="badge green"><?= htmlspecialchars($row['subject']) ?></span>
              <span class="badge gray"><i class="bi bi-laptop"></i> <?= htmlspecialchars($row['method']) ?></span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-circle shadow-sm no-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tùy chọn">
                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <a class="dropdown-item py-2" href="edit_class.php?id=<?= $class_id ?>">
                            <i class="bi bi-pencil-square me-2 text-primary"></i> Chỉnh sửa lớp
                        </a>
                    </li>
                    <?php if($row['status'] == 'active'): ?>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="update_status.php?id=<?= $class_id ?>&action=close" onclick="return confirm('Bạn muốn tạm đóng lớp này? Học viên sẽ không tìm thấy nữa.')">
                            <i class="bi bi-lock-fill me-2"></i> Đóng lớp thủ công
                        </a>
                    </li>
                    <?php elseif($row['status'] == 'hidden'): ?>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 text-success" href="update_status.php?id=<?= $class_id ?>&action=open">
                            <i class="bi bi-unlock-fill me-2"></i> Mở lại lớp
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <h4 class="fw-bold text-dark mb-3"><?= htmlspecialchars($row['title']) ?></h4>

        <div class="d-flex flex-column gap-2 text-secondary mb-4">
           <div class="d-flex align-items-center border-bottom border-dashed pb-2">
               <i class="bi bi-mortarboard me-3 text-brand fs-5"></i> 
               <?= isset($row['grade']) ? htmlspecialchars($row['grade']) : 'Lớp cơ bản' ?>
           </div>
           
           <div class="d-flex align-items-center border-bottom border-dashed pb-2">
               <?php if($row['method'] == 'Online'): ?>
                   <i class="bi bi-camera-video me-3 text-brand fs-5"></i> 
                   <a href="<?= htmlspecialchars($row['location']) ?>" target="_blank" class="text-decoration-none text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($row['location']) ?>">
                       <?= htmlspecialchars($row['location']) ?> <i class="bi bi-box-arrow-up-right small ms-1"></i>
                   </a>
               <?php else: ?>
                   <i class="bi bi-geo-alt me-3 text-brand fs-5"></i> 
                   <?= htmlspecialchars($row['location']) ?>
               <?php endif; ?>
           </div>
           <div class="d-flex align-items-center pb-2">
               <i class="bi bi-clock-history me-3 text-brand fs-5"></i> 
               <?= date('d/m/Y', strtotime($row['created_at'])) ?>
           </div>
        </div>

        <div class="alert alert-light border text-center">
            <small class="text-muted text-uppercase fw-bold">Học phí dự kiến</small>
            <h3 class="text-primary fw-bold mb-0 mt-1"><?= $price_display ?></h3>
            <?php if(!empty($unit_display)): ?>
                <small class="text-muted"><?= htmlspecialchars($unit_display) ?></small>
            <?php endif; ?>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">Trạng thái:</span>
            <span class="<?= $badge_class ?> fs-6"><?= $status_text ?></span>
        </div>
      </div>
      
      </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>