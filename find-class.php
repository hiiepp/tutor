<?php
include __DIR__ . '/../includes/header.php';

// Dữ liệu mẫu (Thay bằng query SQL thực tế)
$classes = [
    [
        'id' => 1,
        'title' => 'Dạy tiểu học',
        'tutor_name' => 'Hương Đào',
        'desc' => 'chủ yếu luyện bài tập về nhà thành thạo lên lớp',
        'price_range' => '130.000 đ - 160.000 đ',
        'unit' => 'giờ',
        'subject' => 'Toán',
        'method' => 'OFFLINE',
        'status' => 'Đang tuyển',
        'grade' => 'Lớp 1',
        'location' => 'Hà Nội',
        'schedule' => '2-3b/ tuần'
    ],
    [
        'id' => 2,
        'title' => 'Ôn Hoá từ cơ bản đến nâng cao, lấy lại gốc',
        'tutor_name' => 'Lương Thị Ngọc Anh',
        'desc' => 'Lựa chọn giảng dạy tuỳ theo học lực. Giảng về bản chất giúp học sinh tư duy, hiểu và dễ nhớ kiến thức. Có giải thắc mắc, bài tập ngoài giờ học',
        'price_range' => '200.000 đ - 250.000 đ',
        'unit' => 'buổi',
        'subject' => 'Hoá học',
        'method' => 'OFFLINE',
        'status' => 'Đang tuyển',
        'grade' => 'Lớp 10',
        'location' => 'Hà Nội',
        'schedule' => '1 đến 2 buổi (2 tiếng rưỡi)'
    ]
];
?>

<div class="container py-4">
    <h3 class="fw-bold mb-4" style="color: #198754;">Tìm lớp học</h3>
    
    <div class="row mb-4">
        <div class="col-12">
            <form action="" method="GET" class="input-group search-bar shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Tìm theo môn học, lớp, tên gia sư, địa điểm..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button class="btn btn-success px-4 py-2" type="submit">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <aside class="col-lg-3 mb-4">
            <form action="" method="GET" class="filter-sidebar border rounded p-3 bg-white shadow-sm">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Bộ lọc</h6>
                
                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Môn học</label>
                    <select name="subject" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tất cả</option>
                        <option value="math">Toán</option>
                        <option value="chemistry">Hóa học</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Khối lớp</label>
                    <select name="grade" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tất cả</option>
                        <?php for($i=1; $i<=12; $i++) echo "<option value='$i'>Lớp $i</option>"; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Hình thức</label>
                    <select name="type" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tất cả</option>
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted mb-1">Khu vực</label>
                    <select name="location" class="form-select form-select-sm border-secondary-subtle">
                        <option value="">Tất cả</option>
                        <option value="hanoi">Hà Nội</option>
                        <option value="hcm">TP. HCM</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-muted mb-1">Khoảng giá (VNĐ)</label>
                    <div class="d-flex gap-2">
                        <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ">
                        <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 mb-2 py-2">Áp dụng bộ lọc</button>
                <a href="find-class.php" class="btn btn-outline-secondary w-100 btn-sm py-2">Xóa bộ lọc</a>
            </form>
        </aside>

        <main class="col-lg-9">
            <div class="mb-3">
                <span class="small text-muted">Tìm thấy <strong><?php echo count($classes); ?></strong> lớp học</span>
            </div>

            <?php foreach ($classes as $class): ?>
            <div class="class-card-wrapper mb-4 p-4 border rounded bg-white shadow-sm">
                <div class="d-flex gap-2 mb-3">
                    <span class="badge-custom badge-subject"><?php echo $class['subject']; ?></span>
                    <span class="badge-custom badge-method"><?php echo $class['method']; ?></span>
                    <span class="badge-custom badge-status"><?php echo $class['status']; ?></span>
                </div>

                <h5 class="fw-bold mb-2"><?php echo $class['title']; ?></h5>
                
                <div class="mb-3">
                    <span class="text-success fw-bold me-1 text-uppercase small">Gia sư:</span>
                    <span class="text-dark fw-medium"><?php echo $class['tutor_name']; ?></span>
                </div>

                <p class="text-secondary small mb-4 lh-base" style="max-width: 90%;">
                    <?php echo $class['desc']; ?>
                </p>

                <div class="row g-3 mb-4 text-muted small">
                    <div class="col-sm-3 col-6"><i class="bi bi-mortarboard me-2"></i><?php echo $class['grade']; ?></div>
                    <div class="col-sm-3 col-6"><i class="bi bi-geo-alt me-2"></i><?php echo $class['location']; ?></div>
                    <div class="col-sm-6 col-12"><i class="bi bi-clock me-2"></i><?php echo $class['schedule']; ?></div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <div class="h5 fw-bold text-success mb-0">
                        <?php echo $class['price_range']; ?> <span class="text-muted small fw-normal">/ <?php echo $class['unit']; ?></span>
                    </div>
                    <a href="#" class="btn btn-outline-success btn-sm px-4 py-2">Xem chi tiết</a>
                </div>
            </div>
            <?php endforeach; ?>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>