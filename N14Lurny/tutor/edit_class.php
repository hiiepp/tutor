<?php 
include '../includes/header_tutor.php'; 
require_once '../config/db.php';

// 1. THIẾT LẬP MÚI GIỜ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_GET['id'])) {
    header("Location: class_management.php");
    exit();
}

$class_id = intval($_GET['id']);
$tutor_id = $_SESSION['user_id'];

$sql = "SELECT * FROM classes WHERE id = ? AND tutor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $class_id, $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

if (!$class) {
    echo "<div class='container py-5 text-center'><h3>Lớp không tồn tại.</h3><a href='class_management.php' class='btn btn-brand'>Quay lại</a></div>";
    include '../includes/footer.php'; exit();
}

// Xử lý giá tiền
$price_val = ''; $unit = 'VND/Buổi'; 
$price_raw = $class['price']; 
if (preg_match('/(\d+)\s+(.*)/', $price_raw, $matches)) {
    $price_val = $matches[1]; $unit = trim($matches[2]);
} elseif (is_numeric($price_raw)) {
    $price_val = intval($price_raw);
}

// Xử lý mô tả (Tách ngược lại để điền vào form)
$full_desc = $class['description'];
$mo_ta_part = $full_desc; 
$lich_hoc = ''; 
$yeu_cau = '';

// Regex để lấy các phần mô tả cũ
if (strpos($full_desc, 'Mô tả:') !== false) {
    if (preg_match('/Mô tả: (.*?)(?=\nLịch học:|$)/s', $full_desc, $m)) $mo_ta_part = trim($m[1]);
    if (preg_match('/Lịch học: (.*?)(?=\nYêu cầu:|$)/s', $full_desc, $m)) $lich_hoc = trim($m[1]);
    if (preg_match('/Yêu cầu: (.*?)$/s', $full_desc, $m)) $yeu_cau = trim($m[1]);
}

// --- QUAN TRỌNG: Lấy số học viên từ cột DB, không phải từ mô tả ---
$so_hoc_vien = $class['max_students'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['tieu_de'];
    $subject = $_POST['mon_hoc'];
    $grade = $_POST['khoi_lop'];
    $method = $_POST['hinh_thuc'];
    $location = $_POST['khu_vuc_final'];
    
    $start_date = $_POST['ngay_bat_dau'];
    $end_date = $_POST['ngay_ket_thuc'];
    
    // Lấy số lượng mới
    $max_students = intval($_POST['so_hoc_vien']);

    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $interval = $date1->diff($date2);
    if ($date1 > $date2 || $interval->days < 7) {
        echo "<script>alert('Lỗi: Ngày kết thúc phải sau ngày bắt đầu ít nhất 1 tuần!'); window.history.back();</script>";
        exit();
    }
    
    $price_str = $_POST['hoc_phi'] . ' ' . $_POST['don_vi'];
    
    // Không cần lưu "Số học viên" vào text nữa, chỉ lưu các thông tin text khác
    $description_str = "Mô tả: " . $_POST['mo_ta'] . "\n" .
                       "Lịch học: " . $_POST['lich_hoc'] . "\n" .
                       "Yêu cầu: " . $_POST['yeu_cau'];

    // --- CẬP NHẬT UPDATE SQL: THÊM max_students ---
    $update_sql = "UPDATE classes SET title=?, subject=?, grade=?, method=?, location=?, price=?, description=?, max_students=?, start_date=?, end_date=? WHERE id=? AND tutor_id=?";
    $update_stmt = $conn->prepare($update_sql);
    // type: sssssssissii (12 params)
    $update_stmt->bind_param("sssssssissii", $title, $subject, $grade, $method, $location, $price_str, $description_str, $max_students, $start_date, $end_date, $class_id, $tutor_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='class_management.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh sửa lớp học</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10 col-xl-9">

            <form method="post" class="form-card bg-white shadow-sm" onsubmit="return validateDates()">
                <div class="form-header py-4 px-4 px-md-5">
                    <h3 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Chỉnh sửa thông tin lớp</h3>
                </div>

                <div class="p-4 p-md-5">
                    
                    <div class="mb-5">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-book me-2"></i> 1. Thông tin lớp học
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tiêu đề yêu cầu <span class="text-danger">*</span></label>
                            <input type="text" name="tieu_de" class="form-control form-control-lg" value="<?= htmlspecialchars($class['title']) ?>" required>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Môn học</label>
                                <select name="mon_hoc" class="form-select form-select-lg">
                                    <?php 
                                    $subjects = ['Toán', 'Văn', 'Anh', 'Lý', 'Hóa', 'Sinh', 'Sử', 'Địa', 'Tin học', 'Tiểu học', 'Khác'];
                                    foreach($subjects as $sub) {
                                        $selected = ($class['subject'] == $sub) ? 'selected' : '';
                                        echo "<option value='$sub' $selected>$sub</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Khối lớp</label>
                                <select name="khoi_lop" class="form-select form-select-lg">
                                    <?php 
                                    $grades = ['Lớp 1', 'Lớp 2', 'Lớp 3', 'Lớp 4', 'Lớp 5', 'Lớp 6', 'Lớp 7', 'Lớp 8', 'Lớp 9', 'Lớp 10', 'Lớp 11', 'Lớp 12', 'Ôn thi ĐH', 'Khác'];
                                    foreach($grades as $gr) {
                                        $selected = ($class['grade'] == $gr) ? 'selected' : '';
                                        echo "<option value='$gr' $selected>$gr</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Hình thức dạy</label>
                                <select name="hinh_thuc" id="methodSelect" class="form-select form-select-lg">
                                    <option value="Offline" <?= $class['method'] == 'Offline' ? 'selected' : '' ?>>Offline (Tại nhà)</option>
                                    <option value="Online" <?= $class['method'] == 'Online' ? 'selected' : '' ?>>Online (Trực tuyến)</option>
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label">Số lượng học viên tối đa</label>
                                <input type="number" name="so_hoc_vien" class="form-control form-control-lg" value="<?= htmlspecialchars($so_hoc_vien) ?>" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label mt-2">Mô tả chi tiết / Mục tiêu</label>
                                <textarea name="mo_ta" class="form-control" rows="5"><?= htmlspecialchars($mo_ta_part) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-calendar-range me-2"></i> 2. Thời gian & Địa điểm
                        </div>
                        
                        <div class="row g-3 mb-4 p-3 bg-light rounded border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày bắt đầu dự kiến</label>
                                <input type="date" name="ngay_bat_dau" id="startDate" class="form-control form-control-lg" 
                                       value="<?= $class['start_date'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày kết thúc dự kiến</label>
                                <input type="date" name="ngay_ket_thuc" id="endDate" class="form-control form-control-lg" 
                                       value="<?= $class['end_date'] ?>" required>
                                <div class="form-text text-danger d-none" id="dateError">Ngày kết thúc phải sau ngày bắt đầu ít nhất 1 tuần.</div>
                            </div>
                        </div>

                        <input type="hidden" id="oldLocation" value="<?= htmlspecialchars($class['location']) ?>">
                        <input type="hidden" name="khu_vuc_final" id="finalLocation" value="<?= htmlspecialchars($class['location']) ?>">

                        <div id="onlineInputGroup" class="d-none mb-3">
                            <label class="form-label">Link lớp học</label>
                            <input type="text" id="onlineLink" class="form-control form-control-lg" placeholder="Link Microsoft Teams / Zoom...">
                        </div>

                        <div id="offlineInputGroup" class="row g-3 mb-3">
                            <div class="col-12">
                                <div class="badge bg-primary mb-2">Khu vực: TP. Hồ Chí Minh</div>
                            </div>
                            <div class="col-md-4">
                                <select id="districtSelect" class="form-select form-select-lg">
                                    <option value="">-- Chọn Quận --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="wardSelect" class="form-select form-select-lg">
                                    <option value="">-- Chọn Phường --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="streetInput" class="form-control form-control-lg" placeholder="VD: 10 Nguyễn Huệ">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Lịch học dự kiến</label>
                            <input type="text" name="lich_hoc" class="form-control form-control-lg" value="<?= htmlspecialchars($lich_hoc) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-cash-coin me-2"></i> 3. Mức học phí & Yêu cầu
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mức học phí đề xuất</label>
                                <input type="number" name="hoc_phi" class="form-control form-control-lg" value="<?= htmlspecialchars($price_val) ?>" placeholder="Nhập số tiền" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Đơn vị tính</label>
                                <select name="don_vi" class="form-select form-select-lg">
                                    <option value="VND/Giờ" <?= $unit == 'VND/Giờ' ? 'selected' : '' ?>>VND/Giờ</option>
                                    <option value="VND/Buổi" <?= $unit == 'VND/Buổi' ? 'selected' : '' ?>>VND/Buổi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-3">
                        <a href="class_management.php" class="btn btn-light px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-brand px-5 fw-bold shadow-sm" onclick="return prepareLocationData()">Lưu thay đổi</button>
                    </div>
                </div>
            </form>
            <div class="pb-5"></div>
        </div>
    </div>
</div>

<script src="../assets/js/class_form.js"></script>

</body>
</html>