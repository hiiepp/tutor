<?php 
session_start();
// Sử dụng Header của học sinh/công khai
include '../includes/header.php'; 
require_once '../config/db.php';

// 1. Kiểm tra ID gia sư
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5 text-center'><h3>Không tìm thấy gia sư!</h3><a href='javascript:history.back()' class='btn btn-secondary'>Quay lại</a></div>";
    include '../includes/footer.php';
    exit();
}

$tutor_id = intval($_GET['id']);

// 2. Lấy thông tin Gia sư (Chỉ lấy role = tutor)
$sql = "SELECT * FROM users WHERE id = ? AND role = 'tutor'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$tutor_res = $stmt->get_result();

if ($tutor_res->num_rows == 0) {
    echo "<div class='container py-5 text-center'><h3>Tài khoản gia sư không tồn tại hoặc đã bị khóa.</h3></div>";
    include '../includes/footer.php';
    exit();
}
$tutor = $tutor_res->fetch_assoc();

// --- XỬ LÝ DỮ LIỆU HIỂN THỊ ---
$avatar_url = (!empty($tutor['avatar'])) ? "../assets/uploads/avatars/" . $tutor['avatar'] : "";
$initials = mb_strtoupper(mb_substr($tutor['full_name'], 0, 1, "UTF-8"));

// Tính tuổi
$age_display = 'Chưa cập nhật';
if (!empty($tutor['dob']) && $tutor['dob'] != '0000-00-00') {
    $dob_date = new DateTime($tutor['dob']);
    $now = new DateTime();
    $age = $now->diff($dob_date)->y;
    $age_display = $age . ' tuổi';
}

// 3. Lấy ảnh minh chứng (Bằng cấp, chứng chỉ)
$proofs_res = $conn->query("SELECT * FROM tutor_proofs WHERE user_id = $tutor_id ORDER BY id DESC");

// 4. Lấy các lớp đang mở đăng ký của gia sư này
$classes_res = $conn->query("SELECT * FROM classes WHERE tutor_id = $tutor_id AND status = 'active' ORDER BY created_at DESC");
$count_active = $classes_res->num_rows;

// 5. Thống kê tổng số lớp đã dạy
$count_all = $conn->query("SELECT COUNT(*) as total FROM classes WHERE tutor_id = $tutor_id")->fetch_assoc()['total'];

// 6. LẤY DANH SÁCH ĐÁNH GIÁ (MỚI THÊM)
$reviews_sql = "SELECT r.*, s.full_name as student_name, s.avatar as s_avatar, c.title as class_title 
                FROM reviews r 
                JOIN users s ON r.student_id = s.id 
                JOIN classes c ON r.class_id = c.id 
                WHERE r.tutor_id = $tutor_id 
                ORDER BY r.created_at DESC";
$reviews_res = $conn->query($reviews_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gia sư: <?= htmlspecialchars($tutor['full_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <style>
        .tutor-header-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .tutor-cover { height: 120px; background: linear-gradient(135deg, #198754, #20c997); }
        .tutor-avatar-container { margin-top: -60px; margin-left: 30px; position: relative; display: inline-block; }
        .tutor-avatar-img { width: 120px; height: 120px; border-radius: 50%; border: 4px solid #fff; object-fit: cover; background: #fff; }
        .tutor-initials { width: 120px; height: 120px; border-radius: 50%; border: 4px solid #fff; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; color: #555; }
        
        .info-box { background: #fff; border-radius: 8px; border: 1px solid #eee; padding: 20px; margin-bottom: 20px; }
        .info-label { font-weight: 600; color: #6c757d; font-size: 0.9rem; margin-bottom: 4px; }
        .info-value { color: #212529; font-weight: 500; }
        
        .proof-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: 0.2s; border: 1px solid #dee2e6; }
        .proof-img:hover { transform: scale(1.05); border-color: #198754; }

        .class-mini-card { border: 1px solid #e9ecef; border-radius: 8px; transition: 0.2s; background: #fff; }
        .class-mini-card:hover { border-color: #198754; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* Style cho phần đánh giá */
        .review-item { border-bottom: 1px dashed #e9ecef; padding-bottom: 15px; margin-bottom: 15px; }
        .review-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .student-avatar-sm { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #f8f9fa; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="bg-light">

<div class="bg-white shadow-sm sticky-top">
    <div class="container py-2 d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="text-decoration-none text-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
        <span class="fw-bold text-success">Hồ sơ Gia sư</span>
        <div style="width: 80px;"></div> </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        
        <div class="col-lg-8">
            
            <div class="tutor-header-card mb-4">
                <div class="tutor-cover"></div>
                <div class="d-flex justify-content-between align-items-end pe-4 pb-3">
                    <div class="tutor-avatar-container">
                        <?php if($avatar_url): ?>
                            <img src="<?= $avatar_url ?>" class="tutor-avatar-img shadow-sm">
                        <?php else: ?>
                            <div class="tutor-initials shadow-sm"><?= $initials ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 text-center">
                        <div class="px-3">
                            <div class="fw-bold fs-5 text-dark"><?= $count_all ?></div>
                            <div class="small text-muted">Lớp đã tạo</div>
                        </div>
                        <div class="px-3 border-start">
                            <div class="fw-bold fs-5 text-success"><?= $count_active ?></div>
                            <div class="small text-muted">Đang mở</div>
                        </div>
                        <div class="px-3 border-start">
                            <div class="fw-bold fs-5 text-warning">
                                <?= ($tutor['avg_rating'] > 0) ? $tutor['avg_rating'] : '0.0' ?> <i class="bi bi-star-fill fs-6"></i>
                            </div>
                            <div class="small text-muted"><?= $tutor['review_count'] ?> đánh giá</div>
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($tutor['full_name']) ?></h2>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold text-success"><i class="bi bi-person-lines-fill me-2"></i>Giới thiệu bản thân</h6>
                        <div class="text-secondary" style="line-height: 1.6; white-space: pre-line;">
                            <?= !empty($tutor['bio']) ? htmlspecialchars($tutor['bio']) : "Gia sư chưa cập nhật phần giới thiệu." ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($proofs_res->num_rows > 0): ?>
            <div class="info-box shadow-sm border-0">
                <h6 class="fw-bold text-success mb-3"><i class="bi bi-patch-check-fill me-2"></i>Bằng cấp & Chứng chỉ</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php while($img = $proofs_res->fetch_assoc()): ?>
                        <img src="../assets/uploads/proofs/<?= $img['image_path'] ?>" class="proof-img" data-bs-toggle="modal" data-bs-target="#imgModal" onclick="showImage(this.src)">
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-box shadow-sm border-0">
                <h6 class="fw-bold text-success mb-3"><i class="bi bi-collection-play-fill me-2"></i>Các lớp đang tuyển sinh</h6>
                <?php if($count_active > 0): ?>
                    <div class="row g-3">
                        <?php while($class = $classes_res->fetch_assoc()): ?>
                        <div class="col-md-6">
                            <div class="class-mini-card p-3 h-100 position-relative">
                                <div class="mb-2">
                                    <span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($class['subject']) ?></span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($class['method']) ?></span>
                                </div>
                                <h6 class="fw-bold text-dark mb-2 text-truncate">
                                    <a href="../class-detail.php?id=<?= $class['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                        <?= htmlspecialchars($class['title']) ?>
                                    </a>
                                </h6>
                                <div class="text-success fw-bold small"><?= $class['price'] ?></div>
                                <div class="small text-muted mt-2"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($class['location']) ?></div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted fst-italic">Hiện tại gia sư này chưa có lớp nào khác đang mở.</p>
                <?php endif; ?>
            </div>

            <div class="info-box shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-success m-0"><i class="bi bi-chat-square-quote-fill me-2"></i>Đánh giá từ Học viên</h6>
                    <span class="badge bg-light text-dark border"><?= $reviews_res->num_rows ?> đánh giá</span>
                </div>
                
                <?php if($reviews_res->num_rows > 0): ?>
                    <?php while($rev = $reviews_res->fetch_assoc()): 
                        $s_avatar = !empty($rev['s_avatar']) ? "../assets/uploads/avatars/" . $rev['s_avatar'] : null;
                        $s_initial = mb_substr($rev['student_name'], 0, 1);
                    ?>
                        <div class="review-item">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if($s_avatar): ?>
                                        <img src="<?= $s_avatar ?>" class="student-avatar-sm me-2">
                                    <?php else: ?>
                                        <div class="student-avatar-sm me-2"><?= $s_initial ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark small"><?= htmlspecialchars($rev['student_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Lớp: <?= htmlspecialchars($rev['class_title']) ?></div>
                                    </div>
                                </div>
                                <div class="text-muted small"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></div>
                            </div>

                            <div class="ms-5">
                                <div class="mb-1 text-warning small">
                                    <?php 
                                    for($i=1; $i<=5; $i++) {
                                        echo ($i <= $rev['rating']) ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star text-secondary opacity-25"></i> ';
                                    }
                                    ?>
                                </div>
                                <p class="mb-0 text-secondary fst-italic small">"<?= nl2br(htmlspecialchars($rev['comment'])) ?>"</p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-chat-left-text text-muted fs-2 opacity-25"></i>
                        <p class="text-muted small mt-2">Chưa có đánh giá nào cho gia sư này.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="info-box shadow-sm border-0 sticky-top" style="top: 80px;">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Thông tin chi tiết</h6>
                
                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-mortarboard me-1"></i> Trình độ</div>
                    <div class="info-value"><?= htmlspecialchars($tutor['degree'] ?? 'Chưa cập nhật') ?></div>
                </div>
                
                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-book me-1"></i> Chuyên ngành</div>
                    <div class="info-value"><?= htmlspecialchars($tutor['major'] ?? 'Chưa cập nhật') ?></div>
                </div>

                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-briefcase me-1"></i> Kinh nghiệm</div>
                    <div class="info-value"><?= htmlspecialchars($tutor['experience'] ?? 'Chưa cập nhật') ?></div>
                </div>

                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-geo-alt me-1"></i> Khu vực dạy</div>
                    <div class="info-value"><?= htmlspecialchars($tutor['address'] ?? 'Chưa cập nhật') ?></div>
                </div>

                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-cake2 me-1"></i> Tuổi</div>
                    <div class="info-value"><?= $age_display ?></div>
                </div>

                <div class="mb-3">
                    <div class="info-label"><i class="bi bi-calendar-check me-1"></i> Tham gia từ</div>
                    <div class="info-value"><?= date('m/Y', strtotime($tutor['created_at'])) ?></div>
                </div>

                <div class="alert alert-warning small mt-4 mb-0">
                    <i class="bi bi-shield-lock-fill me-1"></i> Thông tin liên hệ (SĐT/Email) sẽ được cung cấp sau khi bạn đăng ký lớp học thành công.
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 text-center position-relative">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
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