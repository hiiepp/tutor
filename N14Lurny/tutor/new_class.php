<?php 
include '../includes/header_tutor.php'; 
require_once '../config/db.php'; 

// 1. THIẾT LẬP MÚI GIỜ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tutor_id = $_SESSION['user_id'];
    $title = $_POST['tieu_de'];
    $subject = $_POST['mon_hoc'];
    $grade = $_POST['khoi_lop'];
    $method = $_POST['hinh_thuc'];
    $location = $_POST['khu_vuc_final'];
    
    $start_date = $_POST['ngay_bat_dau'];
    $end_date = $_POST['ngay_ket_thuc'];

    // LẤY SỐ HỌC VIÊN TỪ FORM
    $max_students = intval($_POST['so_hoc_vien']); 

    $price = $_POST['hoc_phi'] . ' ' . $_POST['don_vi'];
    
    // Vẫn giữ trong mô tả nếu bạn muốn hiển thị text, nhưng quan trọng là phải lưu vào cột riêng
    $description = "Mô tả: " . $_POST['mo_ta'] . "\n" .
                   "Lịch học: " . $_POST['lich_hoc'] . "\n" .
                   "Yêu cầu: " . $_POST['yeu_cau'];
    $status = 'pending'; 

    // Validate ngày
    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $interval = $date1->diff($date2);
    
    if ($date1 > $date2 || $interval->days < 7) {
        echo "<script>alert('Lỗi: Ngày kết thúc phải sau ngày bắt đầu ít nhất 1 tuần!'); window.history.back();</script>";
        exit();
    }

    // --- CẬP NHẬT CÂU LỆNH SQL: THÊM max_students ---
    $sql = "INSERT INTO classes (tutor_id, title, subject, grade, price, description, method, location, status, max_students, start_date, end_date, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    // Chuỗi type: i(tutor) s(title) s(sub) s(grade) s(price) s(desc) s(meth) s(loc) s(stat) i(max) s(start) s(end)
    // Tổng: issssssssiss
    $stmt->bind_param("issssssssiss", $tutor_id, $title, $subject, $grade, $price, $description, $method, $location, $status, $max_students, $start_date, $end_date);

    if ($stmt->execute()) {
        echo "<script>
                alert('Đăng lớp thành công! Đang chờ duyệt.'); 
                window.location.href='class_management.php';
              </script>";
    } else {
        echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng lớp học mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10 col-xl-9">
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <a href="javascript:history.back()" class="back-link text-decoration-none fw-bold fs-5">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <form method="POST" class="form-card bg-white shadow-sm" onsubmit="return validateDates()">
                <div class="form-header py-4 px-4 px-md-5">
                    <h3 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Đăng yêu cầu tìm Học Viên</h3>
                </div>

                <div class="p-4 p-md-5">
                    
                    <div class="mb-5">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-book me-2"></i> 1. Thông tin lớp học
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tiêu đề yêu cầu <span class="text-danger">*</span></label>
                            <input type="text" name="tieu_de" class="form-control form-control-lg" placeholder="VD: Tìm gia sư Toán lớp 9 ôn thi vào 10" required>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Môn học</label>
                                <select name="mon_hoc" class="form-select form-select-lg">
                                    <option>Toán</option><option>Văn</option><option>Anh</option><option>Lý</option><option>Hóa</option>
                                    <option>Sinh</option><option>Sử</option><option>Địa</option><option>Tin học</option><option>Tiểu học</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Khối lớp</label>
                                <select name="khoi_lop" class="form-select form-select-lg">
                                    <option>Lớp 1</option><option>Lớp 2</option><option>Lớp 3</option><option>Lớp 4</option><option>Lớp 5</option>
                                    <option>Lớp 6</option><option>Lớp 7</option><option>Lớp 8</option><option>Lớp 9</option>
                                    <option>Lớp 10</option><option>Lớp 11</option><option>Lớp 12</option><option>Ôn thi ĐH</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Hình thức dạy</label>
                                <select name="hinh_thuc" id="methodSelect" class="form-select form-select-lg">
                                    <option value="Offline">Offline (Tại nhà)</option>
                                    <option value="Online">Online (Trực tuyến)</option>
                                </select>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label">Số lượng học viên tối đa</label>
                                <input type="number" name="so_hoc_vien" class="form-control form-control-lg" value="1" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label mt-2">Mô tả chi tiết / Mục tiêu</label>
                                <textarea name="mo_ta" class="form-control" rows="5" placeholder="Mô tả học lực học sinh, mục tiêu cần đạt..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-calendar-range me-2"></i> 2. Thời gian & Địa điểm
                        </div>
                        <div class="row g-3 mb-4 p-3 bg-light rounded border">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày bắt đầu dự kiến <span class="text-danger">*</span></label>
                                <input type="date" name="ngay_bat_dau" id="startDate" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày kết thúc dự kiến <span class="text-danger">*</span></label>
                                <input type="date" name="ngay_ket_thuc" id="endDate" class="form-control form-control-lg" required>
                                <div class="form-text text-danger d-none" id="dateError">Ngày kết thúc phải sau ngày bắt đầu ít nhất 1 tuần (7 ngày).</div>
                            </div>
                        </div>
                        <input type="hidden" name="khu_vuc_final" id="finalLocation">
                        <div id="onlineInputGroup" class="d-none mb-3">
                            <label class="form-label">Link lớp học</label>
                            <input type="text" id="onlineLink" class="form-control form-control-lg" placeholder="Link Microsoft Teams / Zoom...">
                        </div>
                        <div id="offlineInputGroup" class="row g-3 mb-3">
                            <div class="col-12"><div class="badge bg-primary mb-2">Khu vực: TP. Hồ Chí Minh</div></div>
                            <div class="col-md-4"><select id="districtSelect" class="form-select form-select-lg"><option value="">-- Chọn Quận --</option></select></div>
                            <div class="col-md-4"><select id="wardSelect" class="form-select form-select-lg" disabled><option value="">-- Chọn Phường --</option></select></div>
                            <div class="col-md-4"><input type="text" id="streetInput" class="form-control form-control-lg" placeholder="VD: 10 Nguyễn Huệ"></div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Lịch học trong tuần</label>
                            <input type="text" name="lich_hoc" class="form-control form-control-lg" placeholder="VD: Tối thứ 2-4-6 (18h-20h)">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="section-title text-primary fs-5 border-bottom pb-2 mb-4">
                            <i class="bi bi-cash-coin me-2"></i> 3. Mức học phí & Yêu cầu
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mức học phí đề xuất</label>
                                <input type="number" name="hoc_phi" class="form-control form-control-lg" placeholder="Nhập số tiền" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Đơn vị tính</label>
                                <select name="don_vi" class="form-select form-select-lg">
                                    <option>VND/Giờ</option>
                                    <option>VND/Buổi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                        <button type="reset" class="btn btn-light btn-lg px-4">Làm lại</button>
                        <button type="submit" class="btn btn-brand btn-lg px-5 fw-bold shadow-sm" onclick="return prepareLocationData()">Đăng lớp ngay</button>
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