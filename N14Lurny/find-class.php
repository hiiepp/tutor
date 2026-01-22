<?php
session_start();
require 'config/db.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$method = isset($_GET['method']) ? $_GET['method'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

// --- XỬ LÝ LỌC GIÁ ---
function clean_number($str) { return str_replace(',', '', $str); }
$raw_min = isset($_GET['min_price']) ? clean_number($_GET['min_price']) : '';
$raw_max = isset($_GET['max_price']) ? clean_number($_GET['max_price']) : '';
$min_price = (is_numeric($raw_min)) ? intval($raw_min) : '';
$max_price = (is_numeric($raw_max)) ? intval($raw_max) : '';

// --- 1. NHẬN DỮ LIỆU NGÀY (MỚI) ---
$filter_start = isset($_GET['filter_start']) ? $_GET['filter_start'] : '';
$filter_end = isset($_GET['filter_end']) ? $_GET['filter_end'] : '';

$sql = "SELECT classes.*, users.full_name, users.avatar 
        FROM classes 
        LEFT JOIN users ON classes.tutor_id = users.id 
        WHERE classes.status = 'active'";

if (!empty($keyword)) {
    $e_keyword = $conn->real_escape_string($keyword);
    $sql .= " AND (classes.title LIKE '%$e_keyword%' OR classes.subject LIKE '%$e_keyword%')";
}
if (!empty($grade) && $grade !== 'Tất cả') {
    $e_grade = $conn->real_escape_string($grade);
    $sql .= " AND classes.grade = '$e_grade'";
}
if (!empty($subject) && $subject !== 'Tất cả') {
    $e_subject = $conn->real_escape_string($subject);
    $sql .= " AND classes.subject = '$e_subject'";
}
if (!empty($method) && $method !== 'Tất cả') {
    $e_method = $conn->real_escape_string($method);
    $sql .= " AND classes.method = '$e_method'"; 
}
if (!empty($location) && $location !== 'Tất cả') {
    $e_location = $conn->real_escape_string($location);
    $sql .= " AND classes.location LIKE '%$e_location%'";
}
if ($min_price !== '') {
    $sql .= " AND CAST(classes.price AS UNSIGNED) >= $min_price";
}
if ($max_price !== '') {
    $sql .= " AND CAST(classes.price AS UNSIGNED) <= $max_price";
}

// --- 2. THÊM ĐIỀU KIỆN LỌC NGÀY VÀO SQL (MỚI) ---
if (!empty($filter_start)) {
    // Tìm các lớp bắt đầu từ ngày này trở đi
    $sql .= " AND classes.start_date >= '$filter_start'";
}
if (!empty($filter_end)) {
    // Tìm các lớp kết thúc trước hoặc trong ngày này
    $sql .= " AND classes.end_date <= '$filter_end'";
}

$sql .= " ORDER BY classes.id DESC";

$error_message = "";
$result = null;
try {
    $result = $conn->query($sql);
} catch (Exception $e) {
    $error_message = "Lỗi hệ thống: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm lớp gia sư - N14Lurny</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tutor-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 2px solid var(--primary-light); transition: transform 0.2s; background-color: var(--white); }
        .tutor-thumb:hover { transform: scale(1.1); border-color: var(--primary-color); }
        .tutor-initials-sm { width: 40px; height: 40px; background-color: var(--primary-light); color: var(--primary-text); font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 16px; transition: 0.2s; border: 2px solid var(--white); }
        .tutor-link { text-decoration: none; color: inherit; display: flex; align-items: center; }
        .tutor-link:hover .tutor-name { color: var(--primary-color); text-decoration: underline; }
        .class-card { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid rgba(0,0,0,0.08); }
        .class-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; border-color: var(--primary-light); }
    </style>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<section class="search-header">
  <div class="container">
    <h4 class="text-center fw-bold mb-3 text-brand">Tìm gia sư giỏi, lớp học phù hợp</h4>
    <form action="find-class.php" method="GET">
        <div class="search-container mx-auto">
            <i class="bi bi-search text-muted ps-3"></i>
            <input type="text" name="keyword" class="search-input" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Tìm môn học, lớp, tên gia sư...">
            <button type="submit" class="btn btn-brand search-btn">Tìm kiếm</button>
        </div>
    </form>
  </div>
</section>

<section class="find-class py-5">
  <div class="container">
    <div class="row">

      <aside class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm sidebar-sticky">
            <div class="card-body p-4">
                <form action="find-class.php" method="GET">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                      <h6 class="fw-bold text-brand m-0"><i class="bi bi-sliders me-2"></i>Bộ lọc</h6>
                      <a href="find-class.php" class="text-decoration-none small text-muted">Đặt lại</a>
                  </div>
                  
                  <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Mức phí (VNĐ)</label>
                      <div class="d-flex gap-2 align-items-center">
                          <input type="text" name="min_price" class="form-control form-control-sm price-input" placeholder="Từ" value="<?php echo ($min_price !== '') ? number_format($min_price) : ''; ?>">
                          <span class="text-muted">-</span>
                          <input type="text" name="max_price" class="form-control form-control-sm price-input" placeholder="Đến" value="<?php echo ($max_price !== '') ? number_format($max_price) : ''; ?>">
                      </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Thời gian học</label>
                      <div class="mb-2">
                          <small class="text-muted d-block mb-1">Ngày bắt đầu từ:</small>
                          <input type="date" name="filter_start" class="form-control form-control-sm" value="<?= $filter_start ?>">
                      </div>
                      <div>
                          <small class="text-muted d-block mb-1">Kết thúc trước:</small>
                          <input type="date" name="filter_end" class="form-control form-control-sm" value="<?= $filter_end ?>">
                      </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Lớp</label>
                      <select name="grade" class="form-select form-select-sm">
                        <option value="Tất cả">Tất cả</option>
                        <?php for ($i = 1; $i <= 12; $i++): $val = "Lớp $i"; ?>
                          <option value="<?= $val ?>" <?= ($grade == $val) ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endfor; ?>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Môn học</label>
                      <select name="subject" class="form-select form-select-sm">
                        <option value="Tất cả">Tất cả</option>
                        <?php $subjects = ['Toán', 'Văn', 'Tiếng Anh', 'Vật lý', 'Hóa học', 'Sinh học', 'Tin học']; foreach($subjects as $sub): ?>
                          <option value="<?= $sub ?>" <?= ($subject == $sub) ? 'selected' : '' ?>><?= $sub ?></option>
                        <?php endforeach; ?>
                      </select>
                  </div>

                  <div class="mb-3">
                      <label class="form-label small fw-bold text-secondary">Hình thức</label>
                      <select name="method" class="form-select form-select-sm">
                        <option value="Tất cả">Tất cả</option>
                        <option value="Offline" <?= ($method == 'Offline') ? 'selected' : '' ?>>Offline (Tại nhà)</option>
                        <option value="Online" <?= ($method == 'Online') ? 'selected' : '' ?>>Online (Trực tuyến)</option>
                      </select>
                  </div>

                  <div class="mb-4">
                      <label class="form-label small fw-bold text-secondary">Khu vực</label>
                      <select name="location" class="form-select form-select-sm">
                        <option value="Tất cả">Tất cả</option>
                        <?php 
                        $locations = ['TP. Thủ Đức','Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 6', 'Quận 7', 'Quận 8', 'Quận 10', 'Quận 11', 'Quận 12','Quận Bình Thạnh', 'Quận Gò Vấp', 'Quận Phú Nhuận', 'Quận Tân Bình', 'Quận Tân Phú', 'Quận Bình Tân','Huyện Bình Chánh', 'Huyện Củ Chi', 'Huyện Hóc Môn', 'Huyện Nhà Bè', 'Huyện Cần Giờ'];
                        foreach($locations as $loc):
                        ?>
                          <option value="<?= $loc ?>" <?= ($location == $loc) ? 'selected' : '' ?>><?= $loc ?></option>
                        <?php endforeach; ?>
                      </select>
                  </div>

                  <button type="submit" class="btn btn-brand w-100 fw-bold mb-2">Áp dụng</button>
                  <a href="find-class.php" class="btn btn-outline-secondary w-100 fw-bold"><i class="bi bi-list-ul me-2"></i>Xem tất cả</a>
                </form>
            </div>
        </div>
      </aside>

      <main class="col-lg-9">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger shadow-sm border-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Lỗi:</strong> <?php echo $error_message; ?></div>
        <?php elseif ($result && $result->num_rows > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0">Kết quả tìm kiếm</h5>
                <span class="badge bg-white text-secondary border shadow-sm px-3 py-2 rounded-pill">Tìm thấy <?php echo $result->num_rows; ?> lớp</span>
            </div>
            <div class="row g-3">
            <?php while($row = $result->fetch_assoc()): 
                $price_raw = $row['price']; $price_display = $price_raw; $unit_display = '';
                if (is_numeric($price_raw)) { $price_display = number_format($price_raw, 0, ',', '.') . ' đ'; $unit_display = '/ buổi'; }
                elseif (preg_match('/^(\d+)\s+(.*)$/', $price_raw, $matches)) { $price_display = number_format($matches[1], 0, ',', '.') . ' đ'; $unit_clean = trim(str_replace('VND', '', $matches[2])); $unit_display = (strpos($unit_clean, '/') === 0) ? $unit_clean : '/ ' . $unit_clean; }
                
                $tutor_name = $row['full_name'] ?? 'Ẩn danh';
                $tutor_initial = mb_substr($tutor_name, 0, 1, 'UTF-8');
                $tutor_link = "student/tutor_profile.php?id=" . $row['tutor_id'];
                $has_avatar = !empty($row['avatar']) && file_exists("assets/uploads/avatars/" . $row['avatar']);
                $avatar_src = $has_avatar ? "assets/uploads/avatars/" . $row['avatar'] : "";

                // 4. XỬ LÝ NGÀY THÁNG ĐỂ HIỂN THỊ (MỚI)
                $date_display = "Chưa cập nhật";
                if (!empty($row['start_date']) && !empty($row['end_date'])) {
                    $date_display = date('d/m/Y', strtotime($row['start_date'])) . ' - ' . date('d/m/Y', strtotime($row['end_date']));
                }
            ?>
                <div class="col-md-12">
                    <div class="class-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="mb-2">
                                    <span class="badge green me-1"><?= htmlspecialchars($row['subject']) ?></span>
                                    <span class="badge gray"><?= htmlspecialchars($row['method']) ?></span>
                                </div>
                                <h5 class="fw-bold mb-3"><a href="class-detail.php?id=<?= $row['id']; ?>" class="text-decoration-none text-dark stretched-link"><?= htmlspecialchars($row['title']); ?></a></h5>
                                <div class="d-flex align-items-center mb-3 position-relative" style="z-index: 2;">
                                    <a href="<?= $tutor_link ?>" class="tutor-link me-3" title="Xem hồ sơ gia sư">
                                        <?php if ($has_avatar): ?><img src="<?= $avatar_src ?>" class="tutor-thumb shadow-sm"><?php else: ?><div class="tutor-initials-sm shadow-sm"><?= $tutor_initial ?></div><?php endif; ?>
                                    </a>
                                    <div class="d-flex flex-column"><span class="small text-muted" style="font-size: 0.75rem;">Gia sư</span><a href="<?= $tutor_link ?>" class="tutor-link fw-bold text-dark tutor-name"><?= htmlspecialchars($tutor_name) ?></a></div>
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-secondary small">
                                    <div class="d-flex align-items-center"><i class="bi bi-mortarboard me-2 text-brand"></i> <?= htmlspecialchars($row['grade']) ?></div>
                                    <div class="d-flex align-items-center"><i class="bi bi-geo-alt me-2 text-brand"></i> <span class="text-truncate" style="max-width: 150px;"><?= htmlspecialchars($row['location']) ?></span></div>
                                    <div class="d-flex align-items-center"><i class="bi bi-calendar-range me-2 text-brand"></i> <?= $date_display ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0 border-start-md ps-md-4 d-flex flex-column justify-content-center align-items-md-end align-items-start">
                                <div class="class-price mb-0"><?= $price_display ?></div>
                                <div class="small text-muted mb-3"><?= htmlspecialchars($unit_display) ?></div>
                                <a href="class-detail.php?id=<?= $row['id']; ?>" class="btn btn-outline-brand btn-sm rounded-pill px-4 fw-bold position-relative" style="z-index: 2;">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="120" alt="No data" class="mb-3 opacity-25">
                <h5 class="text-secondary fw-bold">Không tìm thấy lớp học nào!</h5>
                <p class="text-muted">Hãy thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
                <a href="find-class.php" class="btn btn-outline-success mt-2 rounded-pill px-4">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>
      </main>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const priceInputs = document.querySelectorAll('.price-input');
    priceInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) { value = parseInt(value).toLocaleString('en-US'); }
            e.target.value = value;
        });
    });
});
</script>

</body>
</html>