<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/N14Lurny'; 

// --- LOGIC LẤY THÔNG BÁO CHO HỌC SINH ---
$notif_count = 0;
$notifications = [];

if (isset($_SESSION['user_id'])) {
    // Đảm bảo đường dẫn tới config/db.php đúng
    require_once dirname(__FILE__) . '/../config/db.php'; 
    $uid = $_SESSION['user_id'];

    // Đếm số thông báo chưa đọc
    $count_sql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $uid AND is_read = 0";
    $count_res = $conn->query($count_sql);
    if($count_res) $notif_count = $count_res->fetch_assoc()['total'];

    // Lấy 5 thông báo mới nhất
    $list_sql = "SELECT * FROM notifications WHERE user_id = $uid ORDER BY created_at DESC LIMIT 5";
    $list_res = $conn->query($list_sql);
    if($list_res) {
        // --- SỬA LỖI Ở ĐÂY: Đổi $row thành $notif_row để không trùng lặp ---
        while($notif_row = $list_res->fetch_assoc()) {
            $notifications[] = $notif_row;
        }
    }
}
?>

<nav class="navbar navbar-expand-lg bg-white border-bottom py-2 fixed-top-nav" style="position: relative; z-index: 1050;">
  <div class="container">
    <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="<?= $base_url ?>/index.php">
        <i class="bi bi-mortarboard-fill me-2 fs-3"></i> N14Lurny
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link" href="<?= $base_url ?>/index.php">Trang chủ</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $base_url ?>/find-class.php">Tìm lớp học</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/N14Lurny/find-tutor.php">Tìm Gia Sư</a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
                $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Học viên';
                $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
                $firstLetter = mb_substr($fullname, 0, 1, 'UTF-8');
                
                // Lấy Avatar người dùng
                $user_avatar = '';
                $u_id = $_SESSION['user_id'];
                $u_res = $conn->query("SELECT avatar FROM users WHERE id=$u_id");
                if($u_res && $u_row = $u_res->fetch_assoc()) {
                    $user_avatar = $u_row['avatar'];
                }
            ?>

            <div class="dropdown me-4">
                <a href="#" class="text-secondary position-relative text-decoration-none" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <?php if($notif_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem;">
                            <?= $notif_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header fw-bold border-bottom d-flex justify-content-between">
                        <span>Thông báo</span>
                        <?php if($notif_count > 0): ?>
                            <span class="badge bg-danger rounded-pill"><?= $notif_count ?> mới</span>
                        <?php endif; ?>
                    </li>
                    
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach($notifications as $notif): ?>
                            <?php 
                                $bg_class = ($notif['is_read'] == 0) ? 'bg-light' : 'bg-white';
                                $fw_class = ($notif['is_read'] == 0) ? 'fw-bold' : 'fw-normal';
                                $icon_color = ($notif['is_read'] == 0) ? 'text-primary' : 'text-secondary';
                                
                                $target_url = $base_url . '/' . $notif['link']; 
                                $final_link = $base_url . '/includes/mark_read.php?id=' . $notif['id'] . '&url=' . urlencode($target_url);
                            ?>
                            <li>
                                <a class="dropdown-item py-2 border-bottom <?= $bg_class ?>" href="<?= $final_link ?>">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill <?= $icon_color ?> mt-1 me-2 fs-5"></i>
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between">
                                                <strong class="d-block text-dark small mb-1"><?= htmlspecialchars($notif['title']) ?></strong>
                                                <?php if($notif['is_read'] == 0): ?>
                                                    <span class="badge bg-primary p-1 rounded-circle" style="width: 8px; height: 8px;"> </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="text-muted text-wrap small <?= $fw_class ?>" style="line-height: 1.4;">
                                                <?= $notif['message'] // Cho phép hiển thị HTML (thẻ strong) ?>
                                            </div>
                                            
                                            <small class="text-secondary d-block mt-1" style="font-size: 0.7rem;">
                                                <?= date('H:i d/m/Y', strtotime($notif['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li><a class="dropdown-item text-center small text-primary py-2 fw-bold" href="#">Xem tất cả</a></li>
                    <?php else: ?>
                        <li class="p-4 text-center text-muted small">
                            <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                            Chưa có thông báo nào
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="studentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <?php if (!empty($user_avatar) && file_exists(dirname(__FILE__) . '/../assets/uploads/avatars/' . $user_avatar)): ?>
                        <img src="<?= $base_url ?>/assets/uploads/avatars/<?= $user_avatar ?>" class="rounded-circle me-2 object-fit-cover shadow-sm border" style="width: 35px; height: 35px;">
                    <?php else: ?>
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px;">
                            <?= $firstLetter ?>
                        </div>
                    <?php endif; ?>
                    
                    <span class="fw-bold d-none d-sm-block"><?= htmlspecialchars($fullname); ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0 shadow border-0" aria-labelledby="studentDropdown">
                    <li class="p-3 border-bottom bg-light rounded-top">
                        <div class="fw-bold text-dark"><?= htmlspecialchars($fullname); ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($email); ?></div>
                        <div class="badge bg-success mt-2">HỌC VIÊN</div>
                    </li>
                    
                    <li>
                        <a class="dropdown-item mt-2 py-2" href="<?= $base_url ?>/student/profile.php">
                            <i class="bi bi-person-circle me-2 text-success"></i> Hồ sơ cá nhân
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= $base_url ?>/student/dashboard.php">
                            <i class="bi bi-journal-bookmark-fill me-2 text-success"></i> Lớp học của tôi
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider m-0"></li>
                    
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="<?= $base_url ?>/auth/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>

        <?php else: ?>
            <a href="<?= $base_url ?>/auth/login_register.php" class="btn btn-outline-success me-2 fw-semibold">Đăng nhập</a>
            <a href="<?= $base_url ?>/auth/login_register.php" class="btn btn-success fw-semibold">Đăng ký</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>