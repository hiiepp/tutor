-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 15, 2026 lúc 08:39 PM
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
(14, 12, 'xZSD', 'Toán', 'Lớp 1', '300000 VND/Giờ', 'Mô tả: sdfcvW\nSố học viên: 1\nLịch học: ÁcdSAC\nYêu cầu: ', 'Online', 'XVASDVF', 'hidden', 1, '2026-01-16', '2026-01-23', '2026-01-15 18:23:54'),
(15, 12, 'xxx', 'Toán', 'Lớp 1', '390000 VND/Giờ', 'Mô tả: xxx\nSố học viên: 10\nLịch học: xxx\nYêu cầu: ', 'Offline', 'xx, Phường Trung Mỹ Tây, Quận 12, TP. Hồ Chí Minh', 'hidden', 1, '2026-01-24', '2026-02-07', '2026-01-15 18:49:11'),
(16, 12, 'ấ', 'Toán', 'Lớp 1', '300000 VND/Giờ', 'Mô tả: á\nSố học viên: 5\nLịch học: qàewq\nYêu cầu: ', 'Offline', 'qadfqewa, Phường 11, Quận Phú Nhuận, TP. Hồ Chí Minh', 'active', 1, '2026-01-24', '2026-02-07', '2026-01-15 19:04:57'),
(17, 12, 'ads', 'Toán', 'Lớp 1', '4000000 VND/Giờ', 'Mô tả: \nLịch học: ZX C\nYêu cầu: ', 'Offline', 'zx c, Phường 4, Quận 8, TP. Hồ Chí Minh', 'active', 6, '2026-01-24', '2026-02-08', '2026-01-15 19:14:50');

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
(2, 12, 9, 'pending', 'âsá', NULL, '2026-01-14 09:17:36'),
(4, 14, 9, 'accepted', 'ad', NULL, '2026-01-15 18:36:25'),
(5, 15, 7, 'accepted', 'ccc', NULL, '2026-01-15 19:03:40'),
(6, 17, 9, 'accepted', 'szvdfc', NULL, '2026-01-15 19:15:58');

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Người báo cáo',
  `class_id` int(11) NOT NULL COMMENT 'Lớp bị báo cáo',
  `reason` text NOT NULL COMMENT 'Lý do báo cáo',
  `status` enum('pending','processed') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái xử lý',
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `avatar`, `dob`, `gender`, `phone`, `bio`, `major`, `degree`, `experience`, `address`, `school`, `grade`, `created_at`) VALUES
(1, 'Admin Quản Trị', 'admin@gmail.com', '$2y$10$fNXThoEgdxdf9nsvXPJwn.9Qpp0mpves1RJ0hPVfafcn4Hm46jnhm', 'admin', NULL, NULL, 'Nam', '0909000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12'),
(2, 'Nguyễn Văn Gia Sư', 'tutor1@gmail.com', '$2y$10$YourHashedPasswordHere', 'tutor', NULL, NULL, 'Nam', '0912345678', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12'),
(3, 'Trần Thị Cô Giáo', 'tutor2@gmail.com', '$2y$10$YourHashedPasswordHere', 'tutor', NULL, NULL, 'Nam', '0987654321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12'),
(4, 'abc', 'student1@gmail.com', '$2y$10$YourHashedPasswordHere', 'student', NULL, NULL, 'Nam', '0933333333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 09:16:12'),
(7, 'abc', 'abc@gmail.com', '$2y$10$Hch33NYyxBQ4s5HKfooy3.W29yCMuPanBww/7TxrClHJD7wv7UCl2', 'student', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-31 15:10:09'),
(8, 'hhh', 'h@gmail.com', '$2y$10$2nDuIl3VMzdW42hxToBXvOUsGNzgtMcF5Hzv9/yNP0XstHmDzdbU6', 'tutor', NULL, NULL, 'Nam', '0134567865', 'đẹp trai', '', 'Sinh viên', '2 năm', '', NULL, NULL, '2026-01-07 08:15:37'),
(9, 'a', 'a@gmail.com', '$2y$10$1kJpyarTH6jeny50w6j8FevWCjslReHpbgfVKvrZituocwprzCj9q', 'student', NULL, NULL, 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 08:16:35'),
(10, 'ad', 'ad@gmail.com', '$2y$10$1OhJVY8X0pBqId86BlJmIOQNc8B7.YibU63yP0FsVW8qu4JGUrMX2', 'tutor', NULL, NULL, 'Nam', 'adàda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-14 07:13:22'),
(11, 'abc', 'z@gmail.com', '$2y$10$3.8QH143KzmP3xhbLgNDZOFbb50CZ87m1tULOQOKJ6NjCeckv4yMO', 'tutor', 'user_11_1768376282.png', '2006-02-09', 'Nam', '0323456789', '', '', '', '', '', NULL, NULL, '2026-01-14 07:21:40'),
(12, 'trâm khùng', 't@gmail.com', '$2y$10$Sl0lln3gzVjOpDDEjHvMsOPsazarK9YUbaKCZ4LxhwB1t2J4CEMzm', 'tutor', NULL, NULL, 'Nam', '0346584598', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-15 17:56:13');

--
-- Chỉ mục cho các bảng đã đổ
--

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
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `class_registrations`
--
ALTER TABLE `class_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Các ràng buộc cho các bảng đã đổ
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
