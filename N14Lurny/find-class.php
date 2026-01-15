<?php
session_start();
require 'config/db.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$method = isset($_GET['method']) ? $_GET['method'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

$sql = "SELECT classes.*, users.full_name 
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
    $sql .= " AND classes.location = '$e_location'";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="search-header bg-light py-4 border-bottom">
  <div class="container">
    <form action="find-class.php" method="GET" class="d-flex justify-content-center">
      <input
        type="text"
        name="keyword"
        value="<?php echo htmlspecialchars($keyword); ?>"
        class="form-control me-2 w-75"
        placeholder="Nhập môn học, lớp, hoặc tiêu đề..."
      >
      <button type="submit" class="btn btn-success px-4">
        Tìm kiếm
      </button>
    </form>
  </div>
</section>

<section class="find-class py-4">
  <div class="container">
    <div class="row">

      <aside class="col-md-3">
        <form action="find-class.php" method="GET" class="filter-box p-3 border rounded bg-white">

          <h6 class="mb-3 fw-bold text-success">Bộ lọc tìm kiếm</h6>
          
          <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">

          <label class="form-label fw-bold">Lớp</label>
          <select name="grade" class="form-select mb-3">
            <option value="Tất cả">Tất cả</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <?php $val = "Lớp $i"; ?>
              <option value="<?php echo $val; ?>" <?php if($grade == $val) echo 'selected'; ?>>
                <?php echo $val; ?>
              </option>
            <?php endfor; ?>
          </select>

          <label class="form-label fw-bold">Môn học</label>
          <select name="subject" class="form-select mb-3">
            <option value="Tất cả">Tất cả</option>
            <?php 
            $subjects = ['Toán', 'Văn', 'Tiếng Anh', 'Vật lý', 'Hóa học', 'Sinh học', 'Tin học'];
            foreach($subjects as $sub): 
            ?>
              <option value="<?php echo $sub; ?>" <?php if($subject == $sub) echo 'selected'; ?>>
                <?php echo $sub; ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label class="form-label fw-bold">Hình thức</label>
          <select name="method" class="form-select mb-3">
            <option value="Tất cả">Tất cả</option>
            <option value="Offline" <?php if($method == 'Offline') echo 'selected'; ?>>Offline (Tại nhà)</option>
            <option value="Online" <?php if($method == 'Online') echo 'selected'; ?>>Online (Trực tuyến)</option>
          </select>

          <label class="form-label fw-bold">Khu vực (TP.HCM)</label>
          <select name="location" class="form-select mb-4">
            <option value="Tất cả">Tất cả</option>
            <?php 
            $locations = [
                'TP. Thủ Đức',
                'Quận 1', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 6', 
                'Quận 7', 'Quận 8', 'Quận 10', 'Quận 11', 'Quận 12',
                'Quận Bình Thạnh', 'Quận Gò Vấp', 'Quận Phú Nhuận', 
                'Quận Tân Bình', 'Quận Tân Phú', 'Quận Bình Tân',
                'Huyện Bình Chánh', 'Huyện Củ Chi', 'Huyện Hóc Môn', 
                'Huyện Nhà Bè', 'Huyện Cần Giờ'
            ];
            
            foreach($locations as $loc):
            ?>
              <option value="<?php echo $loc; ?>" <?php if($location == $loc) echo 'selected'; ?>>
                <?php echo $loc; ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="btn btn-success w-100">
            Áp dụng bộ lọc
          </button>
          
          <div class="mt-2 text-center">
              <a href="find-class.php" class="text-decoration-none small text-secondary">Xóa bộ lọc</a>
          </div>

        </form>
      </aside>

      <main class="col-md-9">
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <strong>Đã xảy ra lỗi xử lý:</strong><br>
            <?php echo $error_message; ?>
            <br><em>Vui lòng kiểm tra lại cấu trúc Database hoặc liên hệ Admin.</em>
        </div>
    
    <?php elseif ($result && $result->num_rows > 0): ?>
        
        <h5 class="mb-3">
            Kết quả: <span class="text-success fw-bold"><?php echo $result->num_rows; ?></span> lớp học
        </h5>

        <?php while($row = $result->fetch_assoc()): ?>
            <div class="class-card border rounded p-3 mb-4 shadow-sm bg-white">
                 <h6 class="class-title mb-2">
                      <a href="class-detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-success fw-bold fs-5">
                        <?php echo $row['title']; ?>
                      </a>
                  </h6>
                  </div>
        <?php endwhile; ?>

    <?php else: ?>
        <div class="text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" alt="No data" class="mb-3 opacity-50">
            <h5 class="text-secondary">Không tìm thấy lớp học nào!</h5>
            <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm khác.</p>
            <a href="find-class.php" class="btn btn-outline-success mt-2">Xóa bộ lọc</a>
        </div>
    <?php endif; ?>

</main>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>