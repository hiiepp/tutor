-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 29, 2026 lúc 05:44 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tutor_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appeals`
--

CREATE TABLE `appeals` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL COMMENT 'Khiếu nại cho báo cáo nào',
  `tutor_id` int(11) NOT NULL COMMENT 'Gia sư khiếu nại',
  `content` text NOT NULL COMMENT 'Nội dung khiếu nại',
  `evidence_image` varchar(255) DEFAULT NULL COMMENT 'Ảnh minh chứng',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_reply` text DEFAULT NULL COMMENT 'Lý do từ chối của Admin',
  `attempt_number` int(11) DEFAULT 1 COMMENT 'Lần khiếu nại thứ mấy (max 2)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `price` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'Offline' COMMENT 'Online hoặc Offline',
  `location` varchar(100) NOT NULL DEFAULT 'TP. Hồ Chí Minh' COMMENT 'Quận/Huyện tại TP.HCM',
  `status` enum('pending','active','hidden','closed','rejected') DEFAULT 'pending',
  `max_students` int(11) NOT NULL DEFAULT 1,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`id`, `tutor_id`, `title`, `subject`, `grade`, `price`, `description`, `method`, `location`, `status`, `max_students`, `start_date`, `end_date`, `created_at`) VALUES
(22, 18, 'Lớp Toán nâng cao học sinh chuyên 10', 'Toán', 'Lớp 10', '50000 VND/Giờ', 'Mô tả: Chào mừng em đến với lớp học! Đến với lớp học, em sẽ được học bắt đầu từ kiến thức cơ bản đến vận dụng, vận dụng cao. Bên cạnh việc học, em cũng sẽ có các bài kiểm tra giúp em biết được lực học của mình từ đó khắc phục điểm còn yếu.\nLịch học: Tối 3-4-7 (18h-20h30)\nYêu cầu: ', 'Offline', '235/23 Tân Hiệp, Xã Tân Hiệp, Huyện Hóc Môn, TP. Hồ Chí Minh', 'active', 10, '2026-01-29', '2026-02-05', '2026-01-29 03:08:44'),
(23, 21, 'Hóa 12 - Ôn thi THPT QG', 'Hóa', 'Ôn thi ĐH', '40000 VND/Giờ', 'Mô tả: Giúp các bạn có thể làm tốt các bài tập từ dễ đến khó và nằm chắc lý thuyết. CÓ THỂ HỌC THỬ 1 BUỔI FREE!!!! \nLịch học: Tối 2-4-6 (20h-22h)\nYêu cầu: ', 'Offline', '34 Đường số 2, Linh Chiểu, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam, Phường Linh Chiểu, Quận Thủ Đức', 'active', 10, '2026-01-29', '2026-02-05', '2026-01-29 03:17:42'),
(24, 21, 'Hóa 10 - Bồi dưỡng kiến thức ', 'Hóa', 'Lớp 10', '35000 VND/Giờ', 'Mô tả: Hỗ trợ các bạn mất gốc môn Hoá hoặc muốn cải thiện điểm lên khá giỏi.\nLịch học: Sáng T7-CN (6h-8h)\nYêu cầu: ', 'Offline', '34 Đường số 2, Linh Chiểu, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam, Phường Linh Chiểu, Quận Thủ Đức', 'active', 5, '2026-01-29', '2026-02-06', '2026-01-29 03:26:17'),
(25, 18, 'Toán 11 - Lấy gốc chuẩn bị cho THPT QG', 'Toán', 'Lớp 11', '100000 VND/Buổi', 'Mô tả: Giảng dạy theo từng chuyên đề, kết hợp làm bài tập tổng hợp, tận tâm, nhiệt huyết, có hỗ trợ ngoài giờ học...\nLịch học: Sáng 2-3-4 (9h-11h)\nYêu cầu: ', 'Offline', 'PJ45+3H4, phường 7, Quận 8, Thành phố Hồ Chí Minh, Việt Nam, Phường 7, Quận 8, TP. Hồ Chí Minh', 'active', 10, '2026-01-29', '2026-02-06', '2026-01-29 04:09:07'),
(26, 18, 'Toán 10 - Chương trình mới', 'Toán', 'Lớp 10', '30000 VND/Giờ', 'Mô tả: Xây dựng nội dung bám sát theo chương trình mới...\nLịch học: Tối 6-7-CN (19h-20h30)\nYêu cầu: ', 'Online', 'https://meet.google.com/landing', 'active', 10, '2026-01-29', '2026-02-05', '2026-01-29 04:11:28'),
(27, 24, 'Tiếng việt - Lớp 1', 'Văn', 'Lớp 1', '50000 VND/Giờ', 'Mô tả: Rèn luyện chữ viết đúng viết đẹp, cung cấp các kiến thức Tiếng Việt cần thiết\nLịch học: Chiều 2-4-6 (13h-15h)\nYêu cầu: ', 'Offline', '69 Đ. Số 7, Phước Kiển, Nhà Bè, Thành phố Hồ Chí Minh 700000, Việt Nam, Xã Phước Kiển, Huyện Nhà Bè,', 'pending', 5, '2026-01-29', '2026-02-06', '2026-01-29 04:41:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `class_registrations`
--

CREATE TABLE `class_registrations` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `tutor_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `class_registrations`
--

INSERT INTO `class_registrations` (`id`, `class_id`, `student_id`, `status`, `message`, `tutor_note`, `created_at`) VALUES
(11, 22, 20, 'accepted', 'Em muốn tham gia để nâng cao kiến thức toán học.', NULL, '2026-01-29 03:13:27'),
(12, 24, 19, 'accepted', 'Em muốn được cải thiện điểm.', NULL, '2026-01-29 03:27:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Người nhận thông báo (Gia sư)',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0 COMMENT '0: Chưa xem, 1: Đã xem',
  `link` varchar(255) DEFAULT NULL COMMENT 'Link để click vào xem chi tiết',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `link`, `created_at`) VALUES
(1, 14, 'Đăng ký thành công! ✅', 'Chúc mừng! Gia sư đã duyệt bạn vào lớp: WQEF. Xem chi tiết để lấy SĐT liên hệ.', 0, 'class-detail.php?id=19', '2026-01-20 10:17:19'),
(2, 13, 'Học viên mới đăng ký 🎓', 'Học viên <strong>1</strong> vừa đăng ký lớp: <strong>vsef</strong>. Hãy kiểm tra ngay.', 1, 'see_details.php?id=20', '2026-01-22 18:17:38'),
(3, 15, 'Đăng ký thành công! ✅', 'Chúc mừng! Gia sư đã duyệt bạn vào lớp: <strong>vsef</strong>. Nhấn vào đây để xem thông tin liên hệ.', 1, 'class-detail.php?id=20', '2026-01-22 18:18:05'),
(4, 9, 'Báo cáo bị từ chối ❌', 'Báo cáo bị từ chối.<br>Lý do: fwefwe', 1, NULL, '2026-01-22 21:03:59'),
(5, 9, 'Báo cáo bị từ chối ❌', 'Báo cáo bị từ chối.<br>Lý do: fwefwe', 1, NULL, '2026-01-22 21:04:23'),
(6, 9, 'Báo cáo thành công ✅', 'Báo cáo của bạn đã được xử lý.', 1, NULL, '2026-01-22 21:04:47'),
(7, 9, 'Báo cáo bị từ chối ❌', 'Báo cáo bị từ chối.<br>Lý do: c fg', 1, NULL, '2026-01-22 21:05:02'),
(8, 9, 'Báo cáo bị từ chối ❌', 'Báo cáo bị từ chối.<br>Nhấn vào đây để xem lý do chi tiết từ Admin.', 1, 'class-detail.php?id=14', '2026-01-22 21:39:11'),
(9, 12, 'Khiếu nại thành công ✅', 'Cảnh cáo đã được gỡ bỏ.', 1, 'tutor/class_management.php', '2026-01-22 21:39:19'),
(10, 9, 'Cập nhật báo cáo ℹ️', 'Báo cáo của bạn bị hủy do gia sư khiếu nại thành công.', 1, 'class-detail.php?id=14', '2026-01-22 21:39:19'),
(11, 13, 'Học viên mới đăng ký 🎓', 'Học viên <strong>a</strong> vừa đăng ký lớp: <strong>vsef</strong>. Hãy kiểm tra ngay.', 0, 'see_details.php?id=20', '2026-01-28 18:19:32'),
(12, 16, 'Học viên mới đăng ký 🎓', 'Học viên <strong>a</strong> vừa đăng ký lớp: <strong>fgbn</strong>. Hãy kiểm tra ngay.', 0, 'see_details.php?id=21', '2026-01-28 18:22:16'),
(13, 9, 'Đăng ký thành công! ✅', 'Chúc mừng! Gia sư đã duyệt bạn vào lớp: <strong>fgbn</strong>. Nhấn vào đây để xem thông tin liên hệ.', 1, 'class-detail.php?id=21', '2026-01-28 18:22:29'),
(14, 18, 'Học viên mới đăng ký 🎓', 'Học viên <strong>Nguyễn Thị D</strong> vừa đăng ký lớp: <strong>Lớp Toán nâng cao học sinh chuyên 10</strong>. Hãy kiểm tra ngay.', 1, 'see_details.php?id=22', '2026-01-29 03:13:27'),
(15, 21, 'Học viên mới đăng ký 🎓', 'Học viên <strong>Nguyễn Thị C</strong> vừa đăng ký lớp: <strong>Hóa 10 - Bồi dưỡng kiến thức </strong>. Hãy kiểm tra ngay.', 1, 'see_details.php?id=24', '2026-01-29 03:27:34'),
(16, 19, 'Đăng ký thành công! ✅', 'Chúc mừng! Gia sư đã duyệt bạn vào lớp: <strong>Hóa 10 - Bồi dưỡng kiến thức </strong>. Nhấn vào đây để xem thông tin liên hệ.', 1, 'class-detail.php?id=24', '2026-01-29 03:27:49'),
(17, 20, 'Đăng ký thành công! ✅', 'Chúc mừng! Gia sư đã duyệt bạn vào lớp: <strong>Lớp Toán nâng cao học sinh chuyên 10</strong>. Nhấn vào đây để xem thông tin liên hệ.', 0, 'class-detail.php?id=22', '2026-01-29 03:42:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL COMMENT 'ID Học viên báo cáo',
  `tutor_id` int(11) NOT NULL COMMENT 'ID Gia sư bị báo cáo',
  `class_id` int(11) NOT NULL COMMENT 'ID Lớp học',
  `reason` varchar(255) NOT NULL COMMENT 'Lý do chính',
  `description` text NOT NULL COMMENT 'Mô tả chi tiết sự việc',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL COMMENT 'Lý do từ chối/Chấp thuận của Admin',
  `image_proof` varchar(255) DEFAULT NULL COMMENT 'Ảnh minh chứng báo cáo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL COMMENT 'Số sao từ 1-5',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `class_id`, `student_id`, `tutor_id`, `rating`, `comment`, `created_at`) VALUES
(2, 24, 19, 21, 5, 'Gia sư tận tâm, chỉ dạy rất nhiệt tình giúp em nhanh tiếp thu kiến thức.', '2026-01-29 04:12:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tutor_proofs`
--

CREATE TABLE `tutor_proofs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tutor_proofs`
--

INSERT INTO `tutor_proofs` (`id`, `user_id`, `image_path`, `created_at`) VALUES
(13, 18, 'proof_18_1769659516_0.jpg', '2026-01-29 04:05:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tutor','student') NOT NULL DEFAULT 'student',
  `avatar` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL COMMENT 'Giới thiệu bản thân',
  `major` varchar(100) DEFAULT NULL COMMENT 'Chuyên ngành',
  `degree` varchar(100) DEFAULT NULL COMMENT 'Bằng cấp/Trình độ',
  `experience` varchar(100) DEFAULT NULL COMMENT 'Kinh nghiệm',
  `address` varchar(255) DEFAULT NULL COMMENT 'Địa chỉ/Khu vực',
  `school` varchar(255) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `warnings_count` int(11) DEFAULT 0 COMMENT 'Số lần bị cảnh cáo',
  `is_banned` tinyint(1) DEFAULT 0 COMMENT '1: Bị cấm dạy, 0: Bình thường',
  `avg_rating` float DEFAULT 0,
  `review_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `avatar`, `dob`, `gender`, `phone`, `bio`, `major`, `degree`, `experience`, `address`, `school`, `grade`, `created_at`, `warnings_count`, `is_banned`, `avg_rating`, `review_count`) VALUES
(1, 'Admin Quản Trị', 'admin@gmail.com', '$2y$10$fNXThoEgdxdf9nsvXPJwn.9Qpp0mpves1RJ0hPVfafcn4Hm46jnhm', 'admin', NULL, NULL, 'Nam', '0909000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12', 0, 0, 0, 0),
(17, 'Nguyễn Văn A', 'a@gmail.com', '$2y$10$HGBX6J6Mepj6xbCCd6Bg3O0xt1.YKQtg5qiN7fKKXcgo1pJyMTWuW', 'student', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-29 02:59:22', 0, 0, 0, 0),
(18, 'Nguyễn Văn B', 'b@gmail.com', '$2y$10$6PmTXfaOWV.OX3s7MMgLJu/UbdfcpQBXxxv/E3etmOwgGLfoGOH1m', 'tutor', NULL, '1999-02-11', 'Nam', '0945063678', '', 'Toán', 'Giáo viên', '5 năm dạy học', '323/3 Ấp Chánh 1, Tân Xuân, Hóc Môn, Thành phố Hồ Chí Minh, Việt Nam', NULL, NULL, '2026-01-29 02:59:50', 0, 0, 0, 0),
(19, 'Nguyễn Thị C', 'c@gmail.com', '$2y$10$Y0dJLNto8VCWgCfClYqAAeCVBENwE7OSZLZEBpet3nceP12dbSlaO', 'student', NULL, '2010-02-11', 'Nam', '0876112345', NULL, NULL, NULL, NULL, '9 Đường Số 9 - Cư Xá Vườn Dâu, Linh Chiểu, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam', '', 'Lớp 10', '2026-01-29 03:00:12', 0, 0, 0, 0),
(20, 'Nguyễn Thị D', 'd@gmail.com', '$2y$10$MZwEZPtJZ5C8MQ63TM79o.DoRNqolJ7.tePi0i7jSONZyWPu950oG', 'student', 'user_20_1769657814.png', NULL, 'Nam', '', NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-29 03:00:42', 0, 0, 0, 0),
(21, 'Đỗ Thị A', 'da@gmail.com', '$2y$10$7Nv3ULbNh6iTgo74cjU39O4fEu.Ycy61lH7I7qNt9XFyZdXkK0gPW', 'tutor', NULL, '1995-07-07', 'Nam', '0967754222', '', 'Hóa học', 'Giáo viên', '6 năm giảng dạy THPT', '34 Đường số 2, Linh Chiểu, Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam', NULL, NULL, '2026-01-29 03:01:17', 0, 0, 5, 1),
(22, 'Hoàng Văn B', 'hb@gmail.com', '$2y$10$12HHcZbY/H4H/pa47Z5UCOyhy07tgDK17zMs7VKNj3VMUq4YEIwpK', 'tutor', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-29 03:01:46', 0, 0, 0, 0),
(23, 'Nguyễn Thị C', 'nc@gmail.com', '$2y$10$HIpD8RuFY/0CJNd.cQhPCu8GSOrCn4jhZY7ZaTuhd8kLDp8jTjLh.', 'tutor', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-29 03:02:10', 0, 0, 0, 0),
(24, 'Hoàng B', 'bh@gmail.com', '$2y$10$SneHyLt5hmjxd619naUDn.62C21zHhmVuAf6XMKZoVo3estfDiclm', 'tutor', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-29 03:02:51', 0, 0, 0, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `appeals`
--
ALTER TABLE `appeals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appeal_report` (`report_id`),
  ADD KEY `fk_appeal_tutor` (`tutor_id`);

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Chỉ mục cho bảng `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Chỉ mục cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_report_student` (`student_id`),
  ADD KEY `fk_report_tutor` (`tutor_id`),
  ADD KEY `fk_report_class` (`class_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Chỉ mục cho bảng `tutor_proofs`
--
ALTER TABLE `tutor_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `appeals`
--
ALTER TABLE `appeals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `class_registrations`
--
ALTER TABLE `class_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tutor_proofs`
--
ALTER TABLE `tutor_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `appeals`
--
ALTER TABLE `appeals`
  ADD CONSTRAINT `fk_appeal_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appeal_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_classes_users` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD CONSTRAINT `class_registrations_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_registrations_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enroll_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enroll_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_report_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tutor_proofs`
--
ALTER TABLE `tutor_proofs`
  ADD CONSTRAINT `tutor_proofs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
