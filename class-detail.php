<?php
include '../includes/header.php';

$id = $_GET['id'] ?? 0;

/* TẠM GIẢ LẬP DỮ LIỆU */
$classes = [
  1 => [
    'title' => 'Gia sư Toán lớp 9 luyện thi vào 10',
    'tutor' => 'Nguyễn Văn A',
    'price' => '200.000đ / giờ',
    'desc'  => 'Lớp học dành cho học sinh lớp 9 cần củng cố kiến thức Toán, luyện thi vào lớp 10.',
    'tags'  => ['Toán', 'Offline', 'Cầu Giấy', '3 buổi / tuần']
  ],
  2 => [
    'title' => 'Gia sư Tiếng Anh giao tiếp cho người đi làm',
    'tutor' => 'Trần Thị B',
    'price' => '250.000đ / giờ',
    'desc'  => 'Lớp học tập trung giao tiếp thực tế, phản xạ nhanh, phù hợp người đi làm.',
    'tags'  => ['Tiếng Anh', 'Online', 'Toàn quốc', '2 buổi / tuần']
  ]
];

$class = $classes[$id] ?? null;
?>

<section class="py-4">
  <div class="container">

<?php if ($class): ?>

    <div class="row">

      <!-- MAIN -->
      <main class="col-md-8">
        <div class="border rounded p-4 bg-white mb-4">

          <h4 class="text-success mb-2">
            <?= $class['title'] ?>
          </h4>

          <div class="mb-2 text-muted">
            Gia sư: <strong><?= $class['tutor'] ?></strong>
          </div>

          <div class="mb-3">
            💰 <?= $class['price'] ?>
          </div>

          <div class="mb-4">
            <?php foreach ($class['tags'] as $tag): ?>
              <span class="badge bg-light text-dark border me-1">
                <?= $tag ?>
              </span>
            <?php endforeach; ?>
          </div>

          <h6>Mô tả lớp học</h6>
          <p><?= $class['desc'] ?></p>

        </div>
      </main>

      <!-- SIDEBAR -->
      <aside class="col-md-4">
        <div class="border rounded p-3 bg-white">

          <h6 class="mb-3">Đăng ký lớp học</h6>

          <p class="text-muted">
            Vui lòng đăng nhập để đăng ký lớp học này.
          </p>

          <a href="/N14Lurny/auth/login.php" class="btn btn-success w-100 mb-2">
            Đăng nhập
          </a>

          <a href="find-class.php" class="btn btn-outline-secondary w-100">
            Quay lại danh sách
          </a>

        </div>
      </aside>

    </div>

<?php else: ?>
    <div class="alert alert-danger">
      Lớp học không tồn tại.
    </div>
<?php endif; ?>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
