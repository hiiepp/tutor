<?php
session_start();
require 'config/db.php';

// --- NHẬN DỮ LIỆU TỪ URL ĐỂ LỌC ---
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$major = isset($_GET['major']) ? $_GET['major'] : '';
$degree = isset($_GET['degree']) ? $_GET['degree'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';

// --- XÂY DỰNG CÂU TRUY VẤN SQL ---
$sql = "SELECT * FROM users WHERE role = 'tutor'";

if (!empty($keyword)) {
    $e_keyword = $conn->real_escape_string($keyword);
    $sql .= " AND (full_name LIKE '%$e_keyword%' OR bio LIKE '%$e_keyword%' OR major LIKE '%$e_keyword%')";
}

if (!empty($major) && $major !== 'Tất cả') {
    $e_major = $conn->real_escape_string($major);
    $sql .= " AND major LIKE '%$e_major%'";
}

if (!empty($degree) && $degree !== 'Tất cả') {
    $e_degree = $conn->real_escape_string($degree);
    $sql .= " AND degree = '$e_degree'";
}

if (!empty($location) && $location !== 'Tất cả') {
    $e_location = $conn->real_escape_string($location);
    $sql .= " AND address LIKE '%$e_location%'";
}

if (!empty($gender) && $gender !== 'Tất cả') {
    $e_gender = $conn->real_escape_string($gender);
    $sql .= " AND gender = '$e_gender'";
}

$sql .= " ORDER BY id DESC"; // Gia sư mới đăng ký hiện trước

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm Gia Sư Giỏi - N14Lurny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<section class="bg-white border-bottom py-5 shadow-sm">
  <div class="container text-center">
    <h3 class="fw-bold text-success mb-3">Đội ngũ Gia sư Chất lượng cao</h3>
    <p class="text-muted mb-4">Hơn <?= $result->num_rows ?> gia sư sẵn sàng hỗ trợ bạn</p>
    
    <form action="find-tutor.php" method="GET" class="d-flex justify-content-center">
        <div class="position-relative" style="max-width: 600px; width: 100%;">
            <input type="text" name="keyword" class="form-control rounded-pill py-3 px-4 shadow border-success" 
                   value="<?= htmlspecialchars($keyword) ?>" 
                   placeholder="Tìm theo tên, môn dạy, chuyên ngành...">
            <button type="submit" class="btn btn-success rounded-pill position-absolute top-0 end-0 m-1 px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row">

      <aside class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="find-tutor.php" method="GET">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                      <h6 class="fw-bold text-dark m-0"><i class="bi bi-funnel me-2"></i>Lọc Gia sư</h6>
                      <a href="find-tutor.php" class="text-decoration-none small text-danger fw-bold">Xóa lọc</a>
                  </div>
                  
                  <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Chuyên môn / Môn dạy</label>
                      <select name="major" class="form-select form-select-sm rounded-pill">
                        <option value="Tất cả">Tất cả</option>
                        <?php $majors = ['Toán', 'Sư phạm Văn', 'Tiếng Anh', 'Vật lý', 'Hóa học', 'Tiểu học', 'Tin học']; foreach($majors as $mj): ?>
                          <option value="<?= $mj ?>" <?= ($major == $mj) ? 'selected' : '' ?>><?= $mj ?></option>
                        <?php endforeach; ?>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Trình độ</label>
                      <select name="degree" class="form-select form-select-sm rounded-pill">
                        <option value="Tất cả">Tất cả</option>
                        <option value="Sinh viên" <?= ($degree == 'Sinh viên') ? 'selected' : '' ?>>Sinh viên</option>
                        <option value="Đã tốt nghiệp" <?= ($degree == 'Đã tốt nghiệp') ? 'selected' : '' ?>>Đã tốt nghiệp</option>
                        <option value="Giáo viên" <?= ($degree == 'Giáo viên') ? 'selected' : '' ?>>Giáo viên</option>
                        <option value="Thạc sĩ" <?= ($degree == 'Thạc sĩ') ? 'selected' : '' ?>>Thạc sĩ</option>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Giới tính</label>
                      <select name="gender" class="form-select form-select-sm rounded-pill">
                        <option value="Tất cả">Tất cả</option>
                        <option value="Nam" <?= ($gender == 'Nam') ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= ($gender == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                      </select>
                  </div>

                  <div class="mb-4">
                      <label class="form-label small fw-bold text-secondary">Khu vực</label>
                      <select name="location" class="form-select form-select-sm rounded-pill">
                        <option value="Tất cả">Tất cả</option>
                        <?php 
                        $locations = ['TP. Thủ Đức','Quận 1', 'Quận 3', 'Quận Gò Vấp', 'Quận Bình Thạnh', 'Quận Tân Bình', 'Quận 7'];
                        foreach($locations as $loc):
                        ?>
                          <option value="<?= $loc ?>" <?= ($location == $loc) ? 'selected' : '' ?>><?= $loc ?></option>
                        <?php endforeach; ?>
                      </select>
                  </div>

                  <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill shadow-sm">Áp dụng</button>
                </form>
            </div>
        </div>
      </aside>

      <main class="col-lg-9">
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="row g-4">
            <?php while($row = $result->fetch_assoc()): 
                $avatar_url = (!empty($row['avatar']) && file_exists("assets/uploads/avatars/" . $row['avatar'])) ? "assets/uploads/avatars/" . $row['avatar'] : null;
                $initial = mb_substr($row['full_name'], 0, 1);
                $major_show = !empty($row['major']) ? $row['major'] : 'Đang cập nhật';
            ?>
                <div class="col-xl-4 col-md-6">
                    <div class="tutor-card text-center">
                        
                        <div class="card-banner-blur" style="<?= $avatar_url ? "background-image: url('$avatar_url');" : '' ?>"></div>

                        <div class="tutor-avatar-wrapper">
                            <?php if($avatar_url): ?>
                                <img src="<?= $avatar_url ?>" class="tutor-avatar-lg">
                            <?php else: ?>
                                <div class="tutor-avatar-initial">
                                    <?= $initial ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="mb-2 text-truncate">
                                <a href="student/tutor_profile.php?id=<?= $row['id'] ?>" class="tutor-name"><?= htmlspecialchars($row['full_name']) ?></a>
                            </h5>

                            <div class="mb-2">
                                <?php 
                                    $avg = isset($row['avg_rating']) && $row['avg_rating'] > 0 ? $row['avg_rating'] : 0;
                                    $count = isset($row['review_count']) && $row['review_count'] > 0 ? $row['review_count'] : 0;
                                ?>
                                <?php if($count > 0): ?>
                                    <div class="text-warning small fw-bold">
                                        <span><?= $avg ?></span> <i class="bi bi-star-fill"></i>
                                        <span class="text-muted fw-normal ms-1">(<?= $count ?> đánh giá)</span>
                                    </div>
                                <?php else: ?>
                                    <span class="small text-muted fst-italic">Chưa có đánh giá</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <span class="badge-major shadow-sm"><?= htmlspecialchars($major_show) ?></span>
                            </div>

                            <ul class="list-unstyled text-start small text-secondary mx-auto" style="max-width: 220px;">
                                <li class="mb-2 text-truncate" title="Trình độ"><i class="bi bi-mortarboard-fill text-warning me-2"></i> <?= htmlspecialchars($row['degree'] ?? 'Chưa cập nhật') ?></li>
                                <li class="mb-2 text-truncate" title="Giới tính"><i class="bi bi-gender-ambiguous text-info me-2"></i> <?= htmlspecialchars($row['gender']) ?></li>
                                <li class="mb-2 text-truncate" title="Khu vực"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <?= htmlspecialchars($row['address'] ?? 'Chưa cập nhật') ?></li>
                            </ul>
                        </div>

                        <div class="card-footer bg-white border-0 pb-4 pt-0 text-center position-relative z-2">
                            <a href="student/tutor_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-4 shadow-sm">
                                Xem hồ sơ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-emoji-frown fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-secondary fw-bold">Không tìm thấy gia sư phù hợp!</h5>
                <p class="text-muted">Hãy thử thay đổi tiêu chí lọc của bạn.</p>
                <a href="find-tutor.php" class="btn btn-success rounded-pill px-4 fw-bold mt-2 shadow-sm">Xem tất cả</a>
            </div>
        <?php endif; ?>
      </main>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>