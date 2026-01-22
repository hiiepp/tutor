-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 22, 2026 lúc 11:30 PM
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
(1, 2, 'Gia sư Toán Lớp 9 lấy gốc', 'Toán', 'Lớp 9', '200000', 'Nhận dạy kèm Toán 9, ôn thi vào 10. Cam kết tiến bộ sau 1 tháng.', 'Offline', 'Quận Gò Vấp', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(2, 2, 'Luyện thi Đại học môn Lý', 'Vật lý', 'Lớp 12', '300000', 'Chuyên luyện đề Lý 12, mục tiêu 8+.', 'Offline', 'Quận Bình Thạnh', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(3, 3, 'Tiếng Anh giao tiếp Online', 'Tiếng Anh', 'Lớp 10', '150000', 'Học giao tiếp phản xạ qua Zoom/Google Meet.', 'Online', 'Toàn quốc', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(4, 3, 'Rèn chữ đẹp cho bé', 'Văn', 'Lớp 1', '120000', 'Cô giáo kiên nhẫn, rèn chữ, tập đọc.', 'Offline', 'Quận 7', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(5, 2, 'Hóa học nâng cao 11', 'Hóa học', 'Lớp 11', '250000', 'Dạy chuyên sâu Hóa hữu cơ.', 'Offline', 'TP. Thủ Đức', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(6, 3, 'Toán tư duy cho trẻ em', 'Toán', 'Lớp 3', '180000', 'Phương pháp mới giúp bé tính nhanh.', 'Offline', 'Quận 1', 'active', 1, NULL, NULL, '2025-12-29 09:16:12'),
(7, 8, 'hiep dep trai', 'Toán', 'Lớp 1', '20000', 'Mô tả: học bao rớt môn\nSố học viên: 5\nLịch học: adsacd\nYêu cầu: ', 'Offline', 'âs', '', 1, NULL, NULL, '2026-01-07 08:43:58'),
(8, 8, 'ádc', 'Toán', 'Lớp 1', '2000000 VND/Giờ', 'Mô tả: xcƯDVCA\nSố học viên: 1\nLịch học: ACDSƯDA\nYêu cầu: DFCQỪC', 'Offline', 'CÁDC', 'active', 1, NULL, NULL, '2026-01-13 01:37:44'),
(9, 8, 'a', 'Toán', 'Lớp 1', '200000 VND/Giờ', 'Mô tả: ádc\nSố học viên: 1\nLịch học: \nYêu cầu: 23rf3rq', 'Offline', '', 'hidden', 1, NULL, NULL, '2026-01-13 04:45:56'),
(10, 8, 'aaâf', 'Toán', 'Lớp 1', '300000 VND/Giờ', 'Mô tả: fff\nSố học viên: 1\nLịch học: \nYêu cầu: ', 'Offline', '', 'active', 1, NULL, NULL, '2026-01-13 04:50:26'),
(11, 8, 'scasa', 'Lý', 'Lớp 1', '2000000 VND/Giờ', 'Mô tả: casc\nSố học viên: 6\nLịch học: cac\nYêu cầu: wfwer', 'Online', 'asc', 'active', 1, NULL, NULL, '2026-01-13 09:43:35'),
(12, 11, 'adddaa', 'Toán', 'Lớp 1', '2000000 VND/Giờ', 'Mô tả: âsấ\nSố học viên: 1\nLịch học: ắè\nYêu cầu: ầẻdsf', 'Offline', '10 nguyễn xuân hiệp, Xã Trung Lập Hạ, Huyện Củ Chi, TP. Hồ Chí Minh', 'active', 1, NULL, NULL, '2026-01-14 07:45:00'),
(18, 13, 'âcs', 'Toán', 'Lớp 1', '3000000 VND/Giờ', 'Mô tả: dasa\nLịch học: áda\nYêu cầu: ', 'Offline', 'cfasdcasecd, Phường Linh Tây, Quận Thủ Đức, TP. Hồ Chí Minh', 'closed', 1, '2026-01-20', '2026-01-30', '2026-01-20 10:14:00'),
(19, 13, 'WQEF', 'Toán', 'Lớp 1', '3000000 VND/Giờ', 'Mô tả: \nLịch học: ằGWEARF\nYêu cầu: ', 'Offline', 'qwfawe, Phường 22, Quận Bình Thạnh, TP. Hồ Chí Minh', 'hidden', 1, '2026-01-29', '2026-02-06', '2026-01-20 10:15:34'),
(20, 13, 'vsef', 'Toán', 'Lớp 1', '30000 VND/Giờ', 'Mô tả: qè\nLịch học: wafw\nYêu cầu: ', 'Offline', 'fqwef, Phường 12, Quận Gò Vấp, TP. Hồ Chí Minh', 'active', 6, '2026-01-24', '2026-02-07', '2026-01-22 18:16:34');

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
(1, 7, 9, 'accepted', 'ád', NULL, '2026-01-09 04:21:18'),
(7, 19, 14, 'accepted', 'qwdw', NULL, '2026-01-20 10:16:45'),
(8, 20, 15, 'accepted', 'qd', NULL, '2026-01-22 18:17:38');

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
(10, 9, 'Cập nhật báo cáo ℹ️', 'Báo cáo của bạn bị hủy do gia sư khiếu nại thành công.', 1, 'class-detail.php?id=14', '2026-01-22 21:39:19');

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
-- Cấu trúc bảng cho bảng `tutor_proofs`
--

CREATE TABLE `tutor_proofs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `is_banned` tinyint(1) DEFAULT 0 COMMENT '1: Bị cấm dạy, 0: Bình thường'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `avatar`, `dob`, `gender`, `phone`, `bio`, `major`, `degree`, `experience`, `address`, `school`, `grade`, `created_at`, `warnings_count`, `is_banned`) VALUES
(1, 'Admin Quản Trị', 'admin@gmail.com', '$2y$10$fNXThoEgdxdf9nsvXPJwn.9Qpp0mpves1RJ0hPVfafcn4Hm46jnhm', 'admin', NULL, NULL, 'Nam', '0909000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12', 0, 0),
(2, 'Nguyễn Văn Gia Sư', 'tutor1@gmail.com', '$2y$10$YourHashedPasswordHere', 'tutor', NULL, NULL, 'Nam', '0912345678', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12', 0, 0),
(3, 'Trần Thị Cô Giáo', 'tutor2@gmail.com', '$2y$10$YourHashedPasswordHere', 'tutor', NULL, NULL, 'Nam', '0987654321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12', 0, 0),
(4, 'abc', 'student1@gmail.com', '$2y$10$YourHashedPasswordHere', 'student', NULL, NULL, 'Nam', '0933333333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12', 0, 0),
(7, 'abc', 'abc@gmail.com', '$2y$10$Hch33NYyxBQ4s5HKfooy3.W29yCMuPanBww/7TxrClHJD7wv7UCl2', 'student', 'stu_7_1769103497.jpg', '2015-10-17', 'Nam', '0394684282', NULL, NULL, NULL, NULL, 'âsá', 'dqwdwe', 'Lớp 10', '2025-12-31 15:10:09', 0, 0),
(8, 'hhh', 'h@gmail.com', '$2y$10$2nDuIl3VMzdW42hxToBXvOUsGNzgtMcF5Hzv9/yNP0XstHmDzdbU6', 'tutor', NULL, NULL, 'Nam', '0134567865', 'đẹp trai', '', 'Sinh viên', '2 năm', '', NULL, NULL, '2026-01-07 08:15:37', 0, 0),
(9, 'a', 'a@gmail.com', '$2y$10$1kJpyarTH6jeny50w6j8FevWCjslReHpbgfVKvrZituocwprzCj9q', 'student', 'stu_9_1768897966.jpg', '2018-06-14', 'Nam', '0123456789', NULL, NULL, NULL, NULL, '', '', '', '2026-01-07 08:16:35', 0, 0),
(10, 'ad', 'ad@gmail.com', '$2y$10$1OhJVY8X0pBqId86BlJmIOQNc8B7.YibU63yP0FsVW8qu4JGUrMX2', 'tutor', NULL, NULL, 'Nam', 'adàda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-14 07:13:22', 0, 0),
(11, 'abc', 'z@gmail.com', '$2y$10$3.8QH143KzmP3xhbLgNDZOFbb50CZ87m1tULOQOKJ6NjCeckv4yMO', 'tutor', 'user_11_1768376282.png', '2006-02-09', 'Nam', '0323456789', '', '', '', '', '', NULL, NULL, '2026-01-14 07:21:40', 0, 0),
(13, 'ádf', 'q@gmail.com', '$2y$10$fdJDXrvOoz5SMeWxq/DUHeNR0rqullwVstPMCKQuwknkgZ8KKCwP2', 'tutor', 'user_13_1769103159.jpg', NULL, 'Nam', '0913434524', '', '', '', '', '', NULL, NULL, '2026-01-20 10:12:47', 0, 0),
(14, '1', 'w@gmail.com', '$2y$10$jDmA7PNW0qMDux92ny3bLuymiJGKPvBqdqrwggshNwO1G2mwPGnhi', 'student', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 10:16:33', 0, 0),
(15, '1', '1@gmail.com', '$2y$10$x6nxa17VcG0ey0TW7v6JNOe1MlDt4cs0wcURX0hG4umCc5hL85Uue', 'student', 'stu_15_1769108774.jpg', '0000-00-00', 'Nam', '', NULL, NULL, NULL, NULL, '', '', '', '2026-01-22 18:17:21', 0, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `class_registrations`
--
ALTER TABLE `class_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tutor_proofs`
--
ALTER TABLE `tutor_proofs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
-- Các ràng buộc cho bảng `tutor_proofs`
--
ALTER TABLE `tutor_proofs`
  ADD CONSTRAINT `tutor_proofs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
