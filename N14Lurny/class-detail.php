<?php
session_start();
require 'config/db.php'; 

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: find-class.php");
    exit();
}
$class_id = intval($_GET['id']);

$sql = "SELECT c.*, 
               u.full_name as tutor_name, 
               u.email as tutor_email, 
               u.phone as tutor_phone,
               u.avatar as tutor_avatar,
               u.degree, u.major, u.experience, u.address as tutor_address, 
               u.avg_rating, u.review_count    /* <-- Đã có dấu phẩy ở dòng trên và dòng này */
        FROM classes c 
        LEFT JOIN users u ON c.tutor_id = u.id 
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('<div class="container py-5 text-center"><h3>Lớp học không tồn tại!</h3><a href="find-class.php" class="btn btn-primary">Quay lại</a></div>');
}
$row = $result->fetch_assoc();

// Kiểm tra trạng thái đăng ký
$registration_status = null;
if (isset($_SESSION['user_id'])) {
    $stu_id = $_SESSION['user_id'];
    $check_reg = $conn->prepare("SELECT status FROM class_registrations WHERE class_id = ? AND student_id = ?");
    $check_reg->bind_param("ii", $class_id, $stu_id);
    $check_reg->execute();
    $res_reg = $check_reg->get_result();
    if ($res_reg->num_rows > 0) {
        $registration_status = $res_reg->fetch_assoc()['status']; 
    }
}

// Logic kiểm tra hiển thị nút Báo cáo
$show_report = false;
if ($registration_status == 'accepted' && !empty($row['start_date'])) {
    $today = date('Y-m-d');
    if ($today >= $row['start_date']) {
        $show_report = true;
    }
}

$badge_class = ($row['status'] == 'active') ? 'badge green' : 'badge red';
$status_text = ($row['status'] == 'active') ? 'Đang tuyển sinh' : 'Đã đóng';

// Xử lý giá tiền
$price_raw = $row['price'];
$price_display = is_numeric($price_raw) ? number_format($price_raw, 0, ',', '.') . ' đ' : $price_raw;
$unit_display = is_numeric($price_raw) ? '/ buổi' : '';

// Xử lý Avatar
$tutorName = $row['tutor_name'] ?? 'Gia sư';
$initials = mb_strtoupper(mb_substr($tutorName, 0, 1, "UTF-8"));
$avatar_url = (!empty($row['tutor_avatar']) && file_exists("assets/uploads/avatars/" . $row['tutor_avatar'])) ? "assets/uploads/avatars/" . $row['tutor_avatar'] : "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?> - Chi tiết lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/N14LURNY/assets/css/tutor.css">
    <style>
        .register-box { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .alert-floating { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        .tutor-avatar-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e9ecef; margin: 0 auto; display: block; }
    </style>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-floating shadow border-0">
        <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-floating shadow border-0">
        <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="back-link text-decoration-none">
            <i class="bi bi-arrow-left"></i> Quay lại tìm lớp
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-detail">
                <div class="mb-3">
                    <span class="badge green"><?= htmlspecialchars($row['subject']) ?></span>
                    <span class="badge gray"><i class="bi bi-laptop"></i> <?= htmlspecialchars($row['method']) ?></span>
                    <span class="<?= $badge_class ?>"><?= $status_text ?></span>
                </div>

                <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($row['title']) ?></h2>

                <div class="d-flex flex-wrap gap-4 text-secondary mb-4">
                   <div class="d-flex align-items-center"><i class="bi bi-mortarboard me-2 text-brand"></i> <?= htmlspecialchars($row['grade']) ?></div>
                   <div class="d-flex align-items-center"><i class="bi bi-geo-alt me-2 text-brand"></i> <?= htmlspecialchars($row['location']) ?></div>
                   <div class="d-flex align-items-center"><i class="bi bi-calendar-check me-2 text-brand"></i> 
                        <?php 
                            if (!empty($row['start_date']) && !empty($row['end_date'])) {
                                echo date('d/m/Y', strtotime($row['start_date'])) . " - " . date('d/m/Y', strtotime($row['end_date']));
                            } else {
                                echo "Chưa cập nhật";
                            }
                        ?>
                   </div>
                </div>

                <hr class="text-muted opacity-25">
                <div class="d-flex align-items-end">
                    <h3 class="text-primary fw-bold mb-0 me-2"><?= $price_display ?></h3>
                    <?php if(!empty($unit_display)): ?><span class="text-muted pb-1"><?= htmlspecialchars($unit_display) ?></span><?php endif; ?>
                </div>
            </div>

            <div class="card card-detail">
                <h5 class="section-title"><i class="bi bi-file-text"></i> Mô tả chi tiết</h5>
                <div class="text-dark" style="line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($row['description'] ?? 'Chưa có mô tả chi tiết.')) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-detail text-center">
                <h5 class="fw-bold mb-4">Thông tin Gia sư</h5>
                <a href="student/tutor_profile.php?id=<?= $row['tutor_id'] ?>" class="text-decoration-none">
                    <?php if ($avatar_url): ?>
                        <img src="<?= $avatar_url ?>" class="tutor-avatar-img mb-3 shadow-sm hover-scale">
                    <?php else: ?>
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px; font-size: 40px; font-weight: bold;"><?= $initials ?></div>
                    <?php endif; ?>
                </a>
                <h6 class="fw-bold fs-5 mb-1"><?= htmlspecialchars($tutorName) ?></h6>


                
                <div class="mb-3 mt-1">
                    <?php 
                        $t_avg = isset($row['avg_rating']) && $row['avg_rating'] > 0 ? $row['avg_rating'] : 0;
                        $t_count = isset($row['review_count']) && $row['review_count'] > 0 ? $row['review_count'] : 0;
                    ?>
                    <?php if($t_count > 0): ?>
                        <div class="d-flex justify-content-center align-items-center text-warning gap-1">
                            <span class="fw-bold fs-5"><?= $t_avg ?></span> 
                            <i class="bi bi-star-fill fs-6"></i>
                            <span class="text-muted small ms-1" style="font-weight: 500;">(<?= $t_count ?> đánh giá)</span>
                        </div>
                    <?php else: ?>
                        <div class="text-muted small fst-italic">
                            <i class="bi bi-star me-1"></i> Chưa có đánh giá
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-start bg-light p-3 rounded mb-3 mt-3 small">
                    <div class="mb-2"><i class="bi bi-mortarboard-fill text-muted me-2"></i><strong>Trình độ:</strong> <?= htmlspecialchars($row['degree'] ?? 'Chưa cập nhật') ?></div>
                    <div class="mb-2"><i class="bi bi-book-half text-muted me-2"></i><strong>Chuyên ngành:</strong> <?= htmlspecialchars($row['major'] ?? 'Chưa cập nhật') ?></div>
                    <div><i class="bi bi-geo-alt-fill text-muted me-2"></i><strong>Khu vực:</strong> <?= htmlspecialchars($row['tutor_address'] ?? 'Chưa cập nhật') ?></div>
                </div>
                
                <?php if ($registration_status == 'accepted'): ?>
                    <div class="d-grid gap-2 border-top pt-3">
                        <div class="alert alert-success py-2 small mb-2 fw-bold">
                            <i class="bi bi-check-circle"></i> Bạn đã vào lớp này
                        </div>
                        <a href="mailto:<?= htmlspecialchars($row['tutor_email']) ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($row['tutor_email']) ?>
                        </a>
                        <a href="tel:<?= htmlspecialchars($row['tutor_phone']) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-telephone"></i> <?= htmlspecialchars($row['tutor_phone'] ?? 'Chưa cập nhật SĐT') ?>
                        </a>

                        <?php 
                            // Kiểm tra xem đã đánh giá chưa
                            $has_reviewed = false;
                            if(isset($_SESSION['user_id'])) {
                                $chk_rev = $conn->query("SELECT id FROM reviews WHERE class_id = $class_id AND student_id = " . $_SESSION['user_id']);
                                if($chk_rev->num_rows > 0) $has_reviewed = true;
                            }
                        ?>

                        <?php if (!$has_reviewed): ?>
                            <button type="button" class="btn btn-warning w-100 btn-sm fw-bold mt-2" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="bi bi-star-fill me-1"></i> Đánh giá Gia sư
                            </button>
                        <?php else: ?>
                            <div class="alert alert-success text-center small mt-2 py-2 mb-0">
                                <i class="bi bi-check-circle"></i> Bạn đã đánh giá lớp này.
                            </div>
                        <?php endif; ?>

                        <?php 
                            // Kiểm tra xem user này đã báo cáo lớp này chưa
                            $stu_id = $_SESSION['user_id'];
                            $chk_report = $conn->query("SELECT * FROM reports WHERE student_id = $stu_id AND class_id = $class_id ORDER BY id DESC LIMIT 1");
                            $my_report = $chk_report->fetch_assoc();
                        ?>

                        <?php if($my_report): ?>
                            <div class="mt-3">
                                <?php if($my_report['status'] == 'pending'): ?>
                                    <div class="alert alert-warning small mb-0 border-warning">
                                        <i class="bi bi-hourglass-split"></i> Đã gửi báo cáo. Đang chờ xử lý.
                                    </div>
                                <?php elseif($my_report['status'] == 'approved'): ?>
                                    <div class="alert alert-success small mb-0 border-success">
                                        <i class="bi bi-check-circle-fill"></i> <strong>Báo cáo thành công!</strong><br>
                                        Admin đã xử lý vi phạm của gia sư.
                                    </div>
                                <?php elseif($my_report['status'] == 'rejected'): ?>
                                    <div class="alert alert-danger small mb-0 border-danger">
                                        <i class="bi bi-x-circle-fill"></i> <strong>Báo cáo bị từ chối!</strong><br>
                                        <hr class="my-1">
                                        <strong>Lý do từ Admin:</strong> <?= htmlspecialchars($my_report['admin_reply'] ?? 'Không có lý do') ?>
                                    </div>
                                    <?php if($show_report): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-2 w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#reportModal">
                                            <i class="bi bi-flag-fill me-1"></i> Gửi báo cáo lại
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif($show_report): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm mt-2 fw-bold" data-bs-toggle="modal" data-bs-target="#reportModal">
                                <i class="bi bi-flag-fill me-1"></i> Báo cáo gia sư
                            </button>
                        <?php endif; ?>
                        </div>
                <?php else: ?>
                    <p class="small text-muted fst-italic border-top pt-3">
                        <i class="bi bi-lock-fill"></i> Số điện thoại và Email sẽ hiển thị sau khi yêu cầu đăng ký của bạn được chấp nhận.
                    </p>
                <?php endif; ?>
            </div>



            <div class="card card-detail p-4 register-box border-success border-top border-4">
                <h5 class="fw-bold mb-3">Đăng ký học</h5>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <p class="text-muted small mb-3">Bạn cần đăng nhập để đăng ký lớp học này.</p>
                    <a href="auth/login_register.php" class="btn btn-brand w-100">Đăng nhập ngay</a>
                <?php elseif ($row['status'] != 'active'): ?>
                    <div class="alert alert-secondary text-center m-0"><i class="bi bi-lock-fill"></i> Lớp này đã đóng</div>
                <?php elseif ($_SESSION['role'] == 'tutor' || $_SESSION['role'] == 'admin'): ?>
                     <div class="alert alert-warning text-center small m-0">Bạn đang đăng nhập với tư cách Gia sư/Admin.</div>
                <?php else: ?>
                    <?php if ($registration_status == 'pending'): ?>
                        <div class="alert alert-info text-center m-0 mb-3">
                            <i class="bi bi-hourglass-split"></i> Yêu cầu đang chờ Gia sư duyệt.
                        </div>
                        
                        <form action="student/cancel_registration.php" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu đăng ký này?');">
                            <input type="hidden" name="class_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-outline-secondary w-100 btn-sm fw-bold">
                                <i class="bi bi-x-circle me-1"></i> Hủy đăng ký
                            </button>
                        </form>

                    <?php elseif ($registration_status == 'accepted'): ?>
                        <div class="alert alert-success text-center m-0"><i class="bi bi-check-circle-fill"></i> Đã tham gia.</div>
                    <?php elseif ($registration_status == 'rejected'): ?>
                        <div class="alert alert-danger text-center m-0"><i class="bi bi-x-circle-fill"></i> Bị từ chối.</div>
                    <?php else: ?>
                        <?php 
                            $check_again = $conn->query("SELECT id FROM class_registrations WHERE class_id=$class_id AND student_id=" . $_SESSION['user_id']);
                            if($check_again->num_rows == 0):
                        ?>
                        <form action="/N14LURNY/student/handle_register.php" method="POST" onsubmit="return confirm('Gửi đăng ký?');">
                            <input type="hidden" name="class_id" value="<?= $row['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Lời nhắn cho gia sư:</label>
                                <textarea name="message" class="form-control form-control-sm" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-brand w-100 py-2 fw-bold text-uppercase shadow-sm"><i class="bi bi-send-fill me-2"></i> Gửi yêu cầu ngay</button>
                        </form>
                        <?php else: ?>
                             <div class="alert alert-info text-center m-0">Bạn đã đăng ký lớp này.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($show_report): ?>
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i> Báo cáo Gia sư</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="/N14LURNY/student/handle_report.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="tutor_id" value="<?= $row['tutor_id'] ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Lý do báo cáo:</label>
                <select name="reason" class="form-select" required>
                    <option value="">-- Chọn lý do --</option>
                    <option value="Chất lượng dạy kém">Chất lượng dạy không đảm bảo</option>
                    <option value="Gia sư thường xuyên hủy lịch">Thường xuyên hủy lịch / Đi muộn</option>
                    <option value="Thái độ không tốt">Thái độ ứng xử không phù hợp</option>
                    <option value="Thu phí ngoài luồng">Yêu cầu thu thêm phí ngoài thỏa thuận</option>
                    <option value="Khác">Lý do khác</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Mô tả chi tiết sự việc:</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Vui lòng mô tả rõ vấn đề bạn gặp phải..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hình ảnh minh chứng (Nếu có):</label>
                <input type="file" name="proof_image" class="form-control" accept="image/*">
                <div class="form-text small">Chỉ chấp nhận file ảnh (JPG, PNG, JPEG). Tối đa 5MB.</div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="student/handle_review.php" method="POST">
          <input type="hidden" name="class_id" value="<?= $class_id ?>">
          <input type="hidden" name="tutor_id" value="<?= $row['tutor_id'] ?>">
          
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold"><i class="bi bi-star-half me-2"></i>Đánh giá Gia sư</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
                <p class="mb-2 fw-bold text-secondary">Bạn cảm thấy chất lượng dạy thế nào?</p>
                
                <div class="star-rating-box mb-4">
                    <input type="radio" id="star5" name="rating" value="5" required />
                    <label for="star5" title="Tuyệt vời"></label>

                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="Tốt"></label>

                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="Bình thường"></label>

                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="Tệ"></label>

                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="Rất tệ"></label>
                </div>

                <div class="text-start">
                    <label class="form-label fw-bold">Nhận xét chi tiết:</label>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Ví dụ: Gia sư dạy rất dễ hiểu, nhiệt tình..."></textarea>
                </div>
            </div>

            
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-warning fw-bold">Gửi đánh giá</button>
          </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>