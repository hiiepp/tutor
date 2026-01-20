<?php
include '../includes/header.php';

/* =========================
   TRẠNG THÁI HIỆN TẠI
========================= */
$status = $_GET['status'] ?? 'all';

/* =========================
   DỮ LIỆU GIẢ LẬP
========================= */
$classes = [
  [
    'id' => 1,
    'title' => 'Gia sư Toán lớp 9',
    'tutor' => 'Nguyễn Văn A',
    'status' => 'bidding'
  ],
  [
    'id' => 2,
    'title' => 'Gia sư Tiếng Anh giao tiếp',
    'tutor' => 'Trần Thị B',
    'status' => 'studying',
    'phone' => '0909 111 222',
    'email' => 'tutor@gmail.com'
  ],
  [
    'id' => 3,
    'title' => 'Gia sư Vật lý lớp 12',
    'tutor' => 'Lê Văn C',
    'status' => 'completed',
    'rating' => 4,
    'comment' => 'Gia sư dạy dễ hiểu'
  ],
  [
    'id' => 4,
    'title' => 'Gia sư Hóa lớp 10',
    'tutor' => 'Phạm Thị D',
    'status' => 'rejected'
  ]
];

/* =========================
   LỌC THEO TRẠNG THÁI
========================= */
if ($status === 'all') {
  $filteredClasses = $classes;
} else {
  $filteredClasses = array_filter($classes, function ($c) use ($status) {
    return $c['status'] === $status;
  });
}
?>

<section class="py-4">
  <div class="container">

    <h4 class="fw-bold mb-4">Quản lý lớp học</h4>

    <!-- TABS -->
    <div class="mb-4">
      <a href="?status=all" class="btn btn-sm <?= $status=='all'?'btn-success':'btn-outline-secondary' ?>">Tất cả</a>
      <a href="?status=bidding" class="btn btn-sm <?= $status=='bidding'?'btn-success':'btn-outline-secondary' ?>">Đã chào giá</a>
      <a href="?status=studying" class="btn btn-sm <?= $status=='studying'?'btn-success':'btn-outline-secondary' ?>">Đang học</a>
      <a href="?status=completed" class="btn btn-sm <?= $status=='completed'?'btn-success':'btn-outline-secondary' ?>">Hoàn thành</a>
      <a href="?status=rejected" class="btn btn-sm <?= $status=='rejected'?'btn-success':'btn-outline-secondary' ?>">Bị từ chối</a>
    </div>

    <?php if (empty($filteredClasses)): ?>
      <p class="text-muted">Không có lớp nào.</p>
    <?php endif; ?>

    <?php foreach ($filteredClasses as $class): ?>
      <div class="border rounded p-3 mb-3 bg-white">

        <h6 class="fw-bold mb-1"><?= $class['title'] ?></h6>
        <p class="text-muted mb-2">Gia sư: <?= $class['tutor'] ?></p>

        <!-- BADGE TRẠNG THÁI -->
        <span class="badge bg-light text-dark border mb-2">
          <?php
            echo match ($class['status']) {
              'bidding' => 'Đã chào giá',
              'studying' => 'Đang học',
              'completed' => 'Hoàn thành',
              'rejected' => 'Bị từ chối',
            };
          ?>
        </span>

        <!-- ĐÃ CHÀO GIÁ -->
        <?php if ($class['status'] === 'bidding'): ?>
          <div class="mt-2">
            <button class="btn btn-outline-danger btn-sm">
              Rút chào giá
            </button>
          </div>
        <?php endif; ?>

        <!-- ĐANG HỌC -->
        <?php if ($class['status'] === 'studying'): ?>
          <div class="mt-2">
            <div>📞 <?= $class['phone'] ?></div>
            <div>✉️ <?= $class['email'] ?></div>
          </div>

          <details class="mt-2">
            <summary class="btn btn-success btn-sm">
              Xác nhận hoàn thành
            </summary>

            <!-- FORM ĐÁNH GIÁ -->
            <form class="mt-3">

              <label class="form-label fw-bold">Đánh giá gia sư</label>

            <!-- CHỌN SAO -->
        <div class="rating mb-2">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio"
                  name="rating_<?= $class['id'] ?>"
                  id="star<?= $i ?>_<?= $class['id'] ?>"
                  value="<?= $i ?>">

            <label for="star<?= $i ?>_<?= $class['id'] ?>">★</label>
          <?php endfor; ?>
        </div>

              <textarea class="form-control mb-2"
                        rows="3"
                        placeholder="Nhận xét của bạn..."></textarea>

              <button class="btn btn-success btn-sm">
                Gửi đánh giá
              </button>
            </form>
          </details>
        <?php endif; ?>

        <!-- HOÀN THÀNH -->
        <?php if ($class['status'] === 'completed'): ?>
          <div class="text-warning mb-1">
            <?= str_repeat('⭐', $class['rating']) ?>
          </div>
          <p class="mb-0"><?= $class['comment'] ?></p>
        <?php endif; ?>

        <!-- BỊ TỪ CHỐI -->
        <?php if ($class['status'] === 'rejected'): ?>
          <p class="text-danger mb-0">
            Gia sư đã từ chối yêu cầu của bạn
          </p>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
