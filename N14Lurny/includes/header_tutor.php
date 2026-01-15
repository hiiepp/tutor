<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/N14Lurny'; // Đường dẫn gốc dự án

// Xử lý logic lấy thông báo nếu đã đăng nhập
$notif_count = 0;
$notifications = [];
if (isset($_SESSION['user_id'])) {
    require_once dirname(__FILE__) . '/../config/db.php'; // Đảm bảo đường dẫn đúng tới config
    $uid = $_SESSION['user_id'];

    // Đếm số thông báo chưa đọc
    $count_sql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $uid AND is_read = 0";
    $count_res = $conn->query($count_sql);
    if($count_res) $notif_count = $count_res->fetch_assoc()['total'];

    // Lấy 5 thông báo mới nhất
    $list_sql = "SELECT * FROM notifications WHERE user_id = $uid ORDER BY created_at DESC LIMIT 5";
    $list_res = $conn->query($list_sql);
    if($list_res) {
        while($row = $list_res->fetch_assoc()) {
            $notifications[] = $row;
        }
    }
}
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom py-2 fixed-top-nav">
  <div class="container">
    <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="<?= $base_url ?>/index.php">
        <i class="bi bi-mortarboard-fill me-2 fs-3"></i> N14Lurny
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/index.php">Trang chủ</a></li>
        </ul>

        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <div class="dropdown me-3">
                    <a href="#" class="text-secondary position-relative text-decoration-none" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <?php if($notif_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem;">
                                <?= $notif_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header fw-bold border-bottom">Thông báo</li>
                        
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach($notifications as $notif): ?>
                                <li>
                                    <a class="dropdown-item py-2 border-bottom <?= $notif['is_read'] == 0 ? 'bg-light' : '' ?>" href="<?= $base_url ?>/tutor/<?= $notif['link'] ?>">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle-fill text-primary mt-1 me-2"></i>
                                            <div>
                                                <strong class="d-block text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($notif['title']) ?></strong>
                                                <small class="text-muted d-block text-wrap" style="font-size: 0.8rem; line-height: 1.3;"><?= htmlspecialchars($notif['message']) ?></small>
                                                <small class="text-secondary" style="font-size: 0.7rem;"><?= date('H:i d/m/Y', strtotime($notif['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li><a class="dropdown-item text-center small text-primary py-2" href="#">Xem tất cả</a></li>
                        <?php else: ?>
                            <li class="p-3 text-center text-muted small">Chưa có thông báo nào</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px;">
                            <?= mb_substr($_SESSION['fullname'] ?? 'U', 0, 1) ?>
                        </div>
                        <span class="fw-bold d-none d-sm-block"><?= htmlspecialchars($_SESSION['fullname'] ?? 'User') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0 shadow border-0">
                        <li><a class="dropdown-item py-2" href="<?= $base_url ?>/auth/logout.php">Đăng xuất</a></li>
                    </ul>
                </div>

            <?php else: ?>
                <a href="<?= $base_url ?>/auth/login_register.php" class="btn btn-outline-success">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
  </div>
</nav>

<script>
document.getElementById('notifDropdown').addEventListener('show.bs.dropdown', function () {
    // Có thể thêm AJAX ở đây để update is_read = 1 trong database khi mở menu
});
</script>