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
                             <div class="col-12 mt-3">
                                <label class="form-label">Yêu cầu với gia sư</label>
                                <textarea name="yeu_cau" class="form-control" rows="3"><?= htmlspecialchars($yeu_cau) ?></textarea>
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

<script>
// --- LOGIC VALIDATE NGÀY ---
function validateDates() {
    const startDate = new Date(document.getElementById('startDate').value);
    const endDate = new Date(document.getElementById('endDate').value);
    const errorMsg = document.getElementById('dateError');

    if (!startDate || !endDate) return true;

    const diffTime = endDate - startDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

    if (diffDays < 7) {
        errorMsg.classList.remove('d-none');
        document.getElementById('endDate').classList.add('is-invalid');
        prepareLocationData(); // Vẫn gọi để xử lý địa chỉ nhưng trả về false
        return false; 
    } else {
        errorMsg.classList.add('d-none');
        document.getElementById('endDate').classList.remove('is-invalid');
        return prepareLocationData();
    }
}

// ... (Copy toàn bộ script hcmDataOld, parseOldLocation... từ file edit_class.php trước đó vào đây) ...
// DỮ LIỆU TP.HCM (CẤU TRÚC CŨ: Giữ nguyên Q2, Q9, Q.Thủ Đức)
const hcmDataOld = {
    "Quận 1": ["Phường Tân Định", "Phường Đa Kao", "Phường Bến Nghé", "Phường Bến Thành", "Phường Nguyễn Thái Bình", "Phường Phạm Ngũ Lão", "Phường Cầu Ông Lãnh", "Phường Cô Giang", "Phường Nguyễn Cư Trinh", "Phường Cầu Kho"],
    "Quận 2": ["Phường Thảo Điền", "Phường An Phú", "Phường Bình An", "Phường Bình Trưng Đông", "Phường Bình Trưng Tây", "Phường Bình Khánh", "Phường An Khánh", "Phường Cát Lái", "Phường Thạnh Mỹ Lợi", "Phường An Lợi Đông", "Phường Thủ Thiêm"],
    "Quận 3": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14"],
    "Quận 4": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 6", "Phường 8", "Phường 9", "Phường 10", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 18"],
    "Quận 5": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận 6": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14"],
    "Quận 7": ["Phường Tân Thuận Đông", "Phường Tân Thuận Tây", "Phường Tân Kiểng", "Phường Tân Hưng", "Phường Bình Thuận", "Phường Tân Quy", "Phường Phú Thuận", "Phường Tân Phú", "Phường Tân Phong", "Phường Phú Mỹ"],
    "Quận 8": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16"],
    "Quận 9": ["Phường Long Bình", "Phường Long Thạnh Mỹ", "Phường Tân Phú", "Phường Hiệp Phú", "Phường Tăng Nhơn Phú A", "Phường Tăng Nhơn Phú B", "Phường Phước Long B", "Phường Phước Long A", "Phường Trường Thạnh", "Phường Long Phước", "Phường Long Trường", "Phường Phước Bình", "Phường Phú Hữu"],
    "Quận 10": ["Phường 1", "Phường 2", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận 11": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16"],
    "Quận 12": ["Phường Thạnh Xuân", "Phường Thạnh Lộc", "Phường Hiệp Thành", "Phường Thới An", "Phường Tân Chánh Hiệp", "Phường An Phú Đông", "Phường Tân Thới Hiệp", "Phường Trung Mỹ Tây", "Phường Tân Hưng Thuận", "Phường Đông Hưng Thuận", "Phường Tân Thới Nhất"],
    "Quận Bình Thạnh": ["Phường 1", "Phường 2", "Phường 3", "Phường 5", "Phường 6", "Phường 7", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 17", "Phường 19", "Phường 21", "Phường 22", "Phường 24", "Phường 25", "Phường 26", "Phường 27", "Phường 28"],
    "Quận Thủ Đức": ["Phường Linh Xuân", "Phường Bình Chiểu", "Phường Linh Trung", "Phường Tam Bình", "Phường Tam Phú", "Phường Hiệp Bình Phước", "Phường Hiệp Bình Chánh", "Phường Linh Chiểu", "Phường Linh Tây", "Phường Linh Đông", "Phường Bình Thọ", "Phường Trường Thọ"],
    "Quận Gò Vấp": ["Phường 1", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15", "Phường 16", "Phường 17"],
    "Quận Phú Nhuận": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 13", "Phường 15", "Phường 17"],
    "Quận Tân Bình": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường 6", "Phường 7", "Phường 8", "Phường 9", "Phường 10", "Phường 11", "Phường 12", "Phường 13", "Phường 14", "Phường 15"],
    "Quận Tân Phú": ["Phường Tân Sơn Nhì", "Phường Tây Thạnh", "Phường Sơn Kỳ", "Phường Tân Quý", "Phường Tân Thành", "Phường Phú Thọ Hòa", "Phường Phú Thạnh", "Phường Phú Trung", "Phường Hòa Thạnh", "Phường Hiệp Tân", "Phường Tân Thới Hòa"],
    "Quận Bình Tân": ["Phường Bình Hưng Hòa", "Phường Bình Hưng Hòa A", "Phường Bình Hưng Hòa B", "Phường Bình Trị Đông", "Phường Bình Trị Đông A", "Phường Bình Trị Đông B", "Phường Tân Tạo", "Phường Tân Tạo A", "Phường An Lạc", "Phường An Lạc A"],
    "Huyện Bình Chánh": ["Thị trấn Tân Túc", "Xã Phạm Văn Hai", "Xã Vĩnh Lộc A", "Xã Vĩnh Lộc B", "Xã Bình Lợi", "Xã Lê Minh Xuân", "Xã Tân Nhựt", "Xã Tân Kiên", "Xã Bình Hưng", "Xã Phong Phú", "Xã An Phú Tây", "Xã Hưng Long", "Xã Đa Phước", "Xã Tân Quý Tây", "Xã Bình Chánh", "Xã Quy Đức"],
    "Huyện Củ Chi": ["Thị trấn Củ Chi", "Xã Phú Mỹ Hưng", "Xã An Phú", "Xã Trung Lập Thượng", "Xã An Nhơn Tây", "Xã Nhuận Đức", "Xã Phạm Văn Cội", "Xã Phú Hòa Đông", "Xã Trung Lập Hạ", "Xã Trung An", "Xã Phước Thạnh", "Xã Phước Hiệp", "Xã Tân An Hội", "Xã Phước Vĩnh An", "Xã Thái Mỹ", "Xã Tân Thạnh Tây", "Xã Hòa Phú", "Xã Tân Thạnh Đông", "Xã Bình Mỹ", "Xã Tân Phú Trung", "Xã Tân Thông Hội"],
    "Huyện Hóc Môn": ["Thị trấn Hóc Môn", "Xã Tân Hiệp", "Xã Nhị Bình", "Xã Đông Thạnh", "Xã Tân Thới Nhì", "Xã Thới Tam Thôn", "Xã Xuân Thới Sơn", "Xã Tân Xuân", "Xã Xuân Thới Đông", "Xã Trung Chánh", "Xã Xuân Thới Thượng", "Xã Bà Điểm"],
    "Huyện Nhà Bè": ["Thị trấn Nhà Bè", "Xã Phước Kiển", "Xã Phước Lộc", "Xã Nhơn Đức", "Xã Phú Xuân", "Xã Long Thới", "Xã Hiệp Phước"],
    "Huyện Cần Giờ": ["Thị trấn Cần Thạnh", "Xã Bình Khánh", "Xã Tam Thôn Hiệp", "Xã An Thới Đông", "Xã Thạnh An", "Xã Long Hòa", "Xã Lý Nhơn"]
};

document.addEventListener('DOMContentLoaded', function() {
    const methodSelect = document.getElementById('methodSelect');
    const onlineInput = document.getElementById('onlineInputGroup');
    const offlineInput = document.getElementById('offlineInputGroup');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');
    const streetInput = document.getElementById('streetInput');
    const onlineLink = document.getElementById('onlineLink');
    const oldLocation = document.getElementById('oldLocation').value;

    // 1. Đổ dữ liệu Quận
    for (let district in hcmDataOld) {
        let option = document.createElement('option');
        option.value = district;
        option.text = district;
        districtSelect.appendChild(option);
    }

    // 2. Logic đổi Quận -> Phường
    districtSelect.addEventListener('change', function() {
        wardSelect.innerHTML = '<option value="">-- Chọn Phường --</option>';
        const selectedDistrict = this.value;
        if (selectedDistrict && hcmDataOld[selectedDistrict]) {
            hcmDataOld[selectedDistrict].forEach(function(ward) {
                let option = document.createElement('option');
                option.value = ward;
                option.text = ward;
                wardSelect.appendChild(option);
            });
            wardSelect.disabled = false;
        } else {
            wardSelect.disabled = true;
        }
    });

    // 3. Logic hiển thị lại dữ liệu cũ khi sửa
    function parseOldLocation(loc) {
        // Format: "Số nhà, Phường, Quận, TP. Hồ Chí Minh"
        // Tách chuỗi bằng dấu phẩy
        const parts = loc.split(',').map(p => p.trim());
        if (parts.length >= 4) {
            // Phần cuối là TP.HCM, bỏ qua
            const dist = parts[parts.length - 2];
            const wd = parts[parts.length - 3];
            
            // Tìm và set Quận
            for(let i=0; i<districtSelect.options.length; i++){
                if(districtSelect.options[i].value === dist){
                    districtSelect.selectedIndex = i;
                    // Trigger sự kiện change để load phường
                    districtSelect.dispatchEvent(new Event('change'));
                    break;
                }
            }
            
            // Tìm và set Phường (sau khi load xong)
            setTimeout(() => {
                for(let i=0; i<wardSelect.options.length; i++){
                    if(wardSelect.options[i].value === wd){
                        wardSelect.selectedIndex = i;
                        break;
                    }
                }
            }, 100);

            // Phần còn lại là tên đường
            // Lấy từ đầu đến trước Phường
            const streetPart = parts.slice(0, parts.length - 3).join(', ');
            streetInput.value = streetPart;
        }
    }

    function toggleLocationInputs() {
        if (methodSelect.value === 'Online') {
            onlineInput.classList.remove('d-none');
            offlineInput.classList.add('d-none');
            if (oldLocation.includes('http') || oldLocation.includes('Teams') || oldLocation.includes('Zoom')) {
                onlineLink.value = oldLocation;
            }
        } else {
            onlineInput.classList.add('d-none');
            offlineInput.classList.remove('d-none');
            
            // Nếu là Offline và có dữ liệu cũ, thử parse
            if (oldLocation && !oldLocation.includes('http')) {
                // Chỉ parse 1 lần đầu tiên nếu các ô đang trống
                if(districtSelect.value === "") {
                    parseOldLocation(oldLocation);
                }
            }
        }
    }
    methodSelect.addEventListener('change', toggleLocationInputs);
    toggleLocationInputs();
});

function prepareLocationData() {
    const method = document.getElementById('methodSelect').value;
    const finalLocationInput = document.getElementById('finalLocation');

    if (method === 'Online') {
        const link = document.getElementById('onlineLink').value.trim();
        if (!link) { alert("Vui lòng nhập link lớp học Online!"); return false; }
        finalLocationInput.value = link;
    } else {
        const street = document.getElementById('streetInput').value.trim();
        const ward = document.getElementById('wardSelect').value;
        const district = document.getElementById('districtSelect').value;

        if (!street || !ward || !district) {
            alert("Vui lòng nhập đầy đủ địa chỉ (Số nhà, Phường, Quận)!");
            return false;
        }
        // Gộp chuỗi: Số nhà, Phường, Quận, TP. Hồ Chí Minh
        finalLocationInput.value = `${street}, ${ward}, ${district}, TP. Hồ Chí Minh`;
    }
    return true;
}
</script>

</body>
</html>