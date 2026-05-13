-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 12, 2026 lúc 04:07 AM
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
-- Cơ sở dữ liệu: `suviettoanthu`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_reminders`
--

CREATE TABLE `ai_reminders` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ai_reminders`
--

INSERT INTO `ai_reminders` (`id`, `student_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Gửi nhắc lịch học: 30 phút/Chiều.', 0, '2026-05-01 16:50:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fb_feedback`
--

CREATE TABLE `fb_feedback` (
  `fb_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fb_content` text NOT NULL,
  `fb_status` tinyint(4) DEFAULT 0 COMMENT '0: Mới, 1: Đã hồi đáp',
  `fb_created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fb_notifications`
--

CREATE TABLE `fb_notifications` (
  `fb_notif_id` int(11) NOT NULL,
  `fb_receiver_id` int(11) DEFAULT 0 COMMENT '0: Gửi tất cả, >0: Gửi cá nhân',
  `fb_title` varchar(255) NOT NULL,
  `fb_message` text NOT NULL,
  `fb_created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `flashcard_log`
--

CREATE TABLE `flashcard_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `cards_done` int(11) DEFAULT 0,
  `log_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `flashcard_log`
--

INSERT INTO `flashcard_log` (`id`, `user_id`, `lesson_id`, `cards_done`, `log_date`) VALUES
(1, 1, 6, 24, '2026-04-13'),
(25, 16, 6, 5, '2026-04-14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lessons_content`
--

CREATE TABLE `lessons_content` (
  `ct_id` int(11) NOT NULL,
  `ct_lesson_id` int(11) NOT NULL,
  `ct_lesson_name` varchar(255) DEFAULT NULL,
  `ct_youtube_link` text DEFAULT NULL,
  `ct_slide_link` text DEFAULT NULL,
  `ct_flashcard_question` text DEFAULT NULL,
  `ct_flashcard_answer` text DEFAULT NULL,
  `ct_image_path` text DEFAULT NULL,
  `ct_file_path` text DEFAULT NULL,
  `ct_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `tab_opened` varchar(30) DEFAULT NULL,
  `pct_done` tinyint(4) DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `last_seen` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `user_id`, `lesson_id`, `tab_opened`, `pct_done`, `completed`, `last_seen`) VALUES
(1, 1, 6, NULL, 100, 1, '2026-05-07 18:32:33'),
(2, 1, 7, NULL, 100, 1, '2026-04-18 13:52:44'),
(3, 1, 8, NULL, 100, 1, '2026-04-07 03:17:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `live_sessions`
--

CREATE TABLE `live_sessions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `topic` text DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `room_id` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `live_sessions`
--

INSERT INTO `live_sessions` (`id`, `title`, `topic`, `scheduled_at`, `room_id`, `is_visible`, `created_by`, `created_at`) VALUES
(7, 'b', 'b', '2026-05-01 23:15:00', 'SuViet_HaoKhi_1777651972_69f4d104aaafd', 1, 20, '2026-05-01 16:12:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `content`, `answer`, `status`, `created_at`) VALUES
(1, 1, 'ALO', NULL, 'pending', '2026-05-07 15:16:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_scores`
--

CREATE TABLE `quiz_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL,
  `total_q` tinyint(4) DEFAULT 10,
  `taken_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `revenue_report`
--

CREATE TABLE `revenue_report` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `transaction_code` varchar(50) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `revenue_report`
--

INSERT INTO `revenue_report` (`id`, `user_id`, `request_id`, `amount`, `transaction_code`, `report_date`, `created_at`) VALUES
(1, 1, 9, 999000, 'Gửi kèm ảnh bill', '2026-05-01', '2026-05-01 12:33:57'),
(2, 22, 10, 999000, 'Gửi kèm ảnh bill', '2026-05-07', '2026-05-07 15:37:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `student_profile`
--

CREATE TABLE `student_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `school` varchar(150) DEFAULT NULL,
  `grade` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `avg_history_11` float DEFAULT NULL,
  `last_test_score` float DEFAULT NULL,
  `self_level` varchar(20) DEFAULT NULL,
  `interest_level` int(11) DEFAULT NULL,
  `difficulties` text DEFAULT NULL,
  `study_sessions_per_week` int(11) DEFAULT NULL,
  `study_time_per_session` varchar(50) DEFAULT NULL,
  `study_time_per_day_session` varchar(50) DEFAULT NULL,
  `study_time_of_day` varchar(50) DEFAULT NULL,
  `study_methods` text DEFAULT NULL,
  `has_study_plan` varchar(20) DEFAULT NULL,
  `target_score` float DEFAULT NULL,
  `target_time_frame` varchar(100) DEFAULT NULL,
  `specific_goals` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `student_profile`
--

INSERT INTO `student_profile` (`id`, `user_id`, `avatar`, `fullname`, `gender`, `birthday`, `school`, `grade`, `city`, `avg_history_11`, `last_test_score`, `self_level`, `interest_level`, `difficulties`, `study_sessions_per_week`, `study_time_per_session`, `study_time_per_day_session`, `study_time_of_day`, `study_methods`, `has_study_plan`, `target_score`, `target_time_frame`, `specific_goals`, `created_at`) VALUES
(1, 1, '1774083141_f10e820b22a9baa8807e4ed75ae6035a.jpg', 'Lưu Tú Chi', 'Nữ', '2005-09-13', 'THPT Ba Vì', '12B', '0', 8, 8, 'Khá', 0, '', 8, '30-60', '', 'Chiều', 'Đọc sách,Trắc nghiệm,Video,Học nhóm,Sơ đồ', 'Có', 9.5, '2 tháng', 'Thi đại học,Hiểu sâu', '2026-03-21 08:52:21'),
(2, 12, '1774088476_f10e820b22a9baa8807e4ed75ae6035a.jpg', 'Lê Bích Diệp', 'Nữ', '2001-01-11', 'THPT Ba Vì', '12C', '0', 7.5, 6, 'Khá', 0, '', 7, '30-60', '', 'Chiều', 'Trắc nghiệm,Video', 'Có', 9.5, '3 tháng', 'Đỗ tốt nghiệp,Thi đại học', '2026-03-21 10:21:16'),
(3, 13, '1774154185_login-bg.jpg', 'Chu Thảo Linh', 'Nữ', '2008-02-12', 'THPT Ba Vì', '12B', '0', 8.2, 8, 'Khá', 0, '', 5, '30-60', '', 'Tối', 'Đọc sách,Học nhóm', 'Có', 9, '2 tháng', 'Đỗ tốt nghiệp,Thi đại học', '2026-03-22 04:36:25'),
(4, 16, '1776127189_logo-dai-hoc-quoc-gia-ha-noi-inkythuatso-01-23-15-44-39.jpg', 'Nguyễn Thị Duyên', 'Nữ', '2008-01-01', 'THPT Ba Vì', '12B', '0', 7.5, 7.3, 'Khá', 0, '', 7, '30-60', '', 'Tối', 'Đọc sách,Trắc nghiệm,Tóm tắt', 'Không', 8.5, 'Đến kỳ thi', 'Đỗ tốt nghiệp,Cải thiện điểm', '2026-04-14 00:39:49'),
(5, 6, '1776496355_Screenshot (2488).png', 'chii', 'Nam', '2006-04-18', 'THPT Ba Vì', '12A', '0', 9, 9.5, 'Giỏi', 0, '', 3, '>60', '', 'Khuya', 'Đọc sách,Trắc nghiệm,Video,Sơ đồ,Tóm tắt', 'Có', 10, '3 tháng', 'Đỗ tốt nghiệp,Thi đại học,Học bổng,Hiểu sâu', '2026-04-18 07:12:35'),
(6, 22, '1778168167_2b179e24-15a6-4ecc-9e07-5bb44e8b4e88.png', 'Lê Minh Nam', 'Nam', '2008-07-09', 'THPT Ba Vì', '12D', '0', 8, 7.9, 'Khá', 0, '', 6, '30-60', '', 'Chiều', 'Đọc sách,Trắc nghiệm,Video', 'Có', 10, '2 tháng', 'Đỗ tốt nghiệp,Thi đại học,Hiểu sâu', '2026-05-07 15:36:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `study_sessions`
--

CREATE TABLE `study_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `duration_s` int(11) DEFAULT 0,
  `study_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `study_sessions`
--

INSERT INTO `study_sessions` (`id`, `user_id`, `lesson_id`, `started_at`, `ended_at`, `duration_s`, `study_date`) VALUES
(1, 1, 6, '2026-04-02 18:32:17', '2026-04-02 18:32:19', 2, '2026-04-02'),
(2, 1, 6, '2026-04-02 18:32:23', '2026-04-02 18:32:28', 5, '2026-04-02'),
(3, 1, 7, '2026-04-02 18:33:03', '2026-04-02 18:33:08', 5, '2026-04-02'),
(4, 1, 7, '2026-04-02 18:33:12', '2026-04-02 18:33:17', 4, '2026-04-02'),
(5, 1, 8, '2026-04-02 18:33:19', '2026-04-02 18:33:24', 5, '2026-04-02'),
(6, 1, 8, '2026-04-02 18:33:26', '2026-04-02 18:33:32', 6, '2026-04-02'),
(7, 1, 8, '2026-04-02 18:33:34', '2026-04-02 18:33:40', 6, '2026-04-02'),
(8, 1, 8, '2026-04-02 18:33:43', '2026-04-02 18:33:49', 6, '2026-04-02'),
(9, 1, 8, '2026-04-02 18:33:53', '2026-04-02 18:33:59', 6, '2026-04-02'),
(10, 1, 7, '2026-04-02 18:34:22', '2026-04-02 18:34:39', 17, '2026-04-02'),
(11, 1, 6, '2026-04-02 18:34:41', '2026-04-02 18:34:49', 8, '2026-04-02'),
(12, 1, 8, '2026-04-02 18:34:52', '2026-04-02 18:34:55', 3, '2026-04-02'),
(13, 1, 8, '2026-04-02 18:34:59', '2026-04-02 18:36:24', 85, '2026-04-02'),
(14, 1, 8, '2026-04-02 18:36:24', '2026-04-02 18:36:27', 3, '2026-04-02'),
(15, 1, 8, '2026-04-02 18:36:30', '2026-04-02 18:36:38', 8, '2026-04-02'),
(16, 1, 8, '2026-04-02 18:38:20', '2026-04-02 18:38:34', 14, '2026-04-02'),
(17, 1, 6, '2026-04-03 16:59:11', '2026-04-03 16:59:29', 18, '2026-04-03'),
(18, 1, 6, '2026-04-03 18:28:41', '2026-04-03 18:28:50', 9, '2026-04-03'),
(19, 1, 7, '2026-04-03 18:28:51', '2026-04-03 18:29:03', 12, '2026-04-03'),
(20, 1, 6, '2026-04-03 18:29:05', '2026-04-03 18:29:17', 12, '2026-04-03'),
(21, 1, 6, '2026-04-03 18:29:18', '2026-04-03 18:29:23', 6, '2026-04-03'),
(22, 1, 6, '2026-04-07 02:59:38', '2026-04-07 02:59:58', 20, '2026-04-07'),
(23, 1, 6, '2026-04-07 02:59:58', '2026-04-07 03:00:26', 28, '2026-04-07'),
(24, 1, 6, '2026-04-07 03:15:46', '2026-04-07 03:16:27', 41, '2026-04-07'),
(25, 1, 6, '2026-04-07 03:16:28', '2026-04-07 03:17:43', 75, '2026-04-07'),
(26, 1, 8, '2026-04-07 03:17:46', '2026-04-07 03:19:00', 73, '2026-04-07'),
(27, 1, 6, '2026-04-07 03:48:28', '2026-04-07 03:52:57', 269, '2026-04-07'),
(28, 1, 6, '2026-04-07 03:55:59', '2026-04-07 03:56:29', 29, '2026-04-07'),
(29, 1, 6, '2026-04-13 15:37:15', '2026-04-13 15:37:32', 17, '2026-04-13'),
(30, 1, 6, '2026-04-13 15:43:00', '2026-04-13 15:43:17', 17, '2026-04-13'),
(31, 1, 6, '2026-04-13 15:47:03', '2026-04-13 15:47:19', 16, '2026-04-13'),
(32, 1, 6, '2026-04-13 15:53:09', '2026-04-13 15:53:26', 17, '2026-04-13'),
(33, 1, 6, '2026-04-13 15:53:45', '2026-04-13 15:57:55', 250, '2026-04-13'),
(34, 1, 6, '2026-04-13 16:00:46', '2026-04-13 16:00:53', 7, '2026-04-13'),
(35, 1, 6, '2026-04-13 16:00:58', '2026-04-13 16:01:17', 18, '2026-04-13'),
(36, 1, 6, '2026-04-14 11:16:36', '2026-04-14 11:17:15', 39, '2026-04-14'),
(37, 1, 6, '2026-04-14 16:46:33', '2026-04-14 16:54:50', 498, '2026-04-14'),
(38, 1, 6, '2026-04-14 16:54:51', '2026-04-14 16:55:16', 25, '2026-04-14'),
(39, 1, 6, '2026-04-14 16:56:09', '2026-04-14 16:56:45', 36, '2026-04-14'),
(40, 1, 6, '2026-04-14 17:05:52', '2026-04-14 17:07:28', 97, '2026-04-14'),
(41, 1, 6, '2026-04-14 17:07:30', '2026-04-14 17:12:07', 277, '2026-04-14'),
(42, 1, 6, '2026-04-14 17:12:07', '2026-04-14 17:12:43', 36, '2026-04-14'),
(43, 1, 7, '2026-04-14 17:12:46', '2026-04-14 17:12:53', 7, '2026-04-14'),
(44, 1, 8, '2026-04-14 17:12:55', '2026-04-14 17:13:03', 8, '2026-04-14'),
(45, 1, 6, '2026-04-14 17:20:54', '2026-04-14 17:20:55', 2, '2026-04-14'),
(46, 1, 6, '2026-04-14 02:19:46', '2026-04-14 02:19:54', 8, '2026-04-14'),
(47, 1, 6, '2026-04-14 02:39:54', '2026-04-14 02:40:11', 16, '2026-04-14'),
(48, 1, 6, '2026-04-14 02:40:19', '2026-04-14 02:40:35', 17, '2026-04-14'),
(49, 1, 6, '2026-04-14 02:40:42', '2026-04-14 02:41:11', 28, '2026-04-14'),
(50, 1, 7, '2026-04-14 02:42:38', '2026-04-14 02:47:00', 262, '2026-04-14'),
(51, 1, 6, '2026-04-14 02:48:10', '2026-04-14 02:48:20', 10, '2026-04-14'),
(52, 1, 6, '2026-04-14 02:51:44', '2026-04-14 03:51:21', 3576, '2026-04-14'),
(53, 1, 6, '2026-04-21 03:53:51', '2026-04-21 03:54:00', 9, '2026-04-21'),
(54, 1, 6, '2026-05-07 18:32:27', '2026-05-07 18:32:35', 8, '2026-05-07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teacher_qa`
--

CREATE TABLE `teacher_qa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `answered_by` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` enum('pending','answered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `answered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teacher_qa`
--

INSERT INTO `teacher_qa` (`id`, `user_id`, `question`, `answer`, `answered_by`, `is_featured`, `status`, `created_at`, `answered_at`) VALUES
(1, 1, 'alo', NULL, NULL, 0, 'pending', '2026-05-07 15:31:45', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'student',
  `is_vip` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `vip_since` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `role`, `is_vip`, `status`, `created_at`, `vip_since`) VALUES
(1, 'chi', 'chi', 'nb351471@gmail.com', '$2y$10$lkGcx4JP1nbRljCJE1kYpOp9iDj5I4BW3iQclE.mCLAtXzY5pETu2', 'student', 1, 'active', '2026-03-08 10:57:59', '2026-05-01 19:33:57'),
(3, 'djsjkd', '23010425', 'nb35147@gmail.com', '$2y$10$T3oIv14zh6Pgl3XRKYvMsO9O0rVr5vOupsqshBoobdBleU5fcxZ2a', 'student', 0, 'active', '2026-03-08 11:24:16', NULL),
(4, 'Quản trị viên', 'admin', 'admin@suviet.vn', '$2y$10$Gj3WEeyQLDOyun2DwfoOYuZF.JwbV1v32HStVItLtNhp/hTXpL7pq', 'admin', 0, 'active', '2026-03-08 17:57:02', NULL),
(6, 'ltc', 'ltc', 'nb1@gmail.com', '$2y$10$nvyqZqT0jMDfMR02AGUK5e9Yp2WDxyd9cMZKhBQesFuwxcQkM/x7m', 'student', 0, 'active', '2026-03-09 16:44:47', NULL),
(7, 'chi', 'chi chi chành chành', 'nb111@gmail.com', '$2y$10$FO.cbzREUao9/89xp9aVo.FNwWKto4zqn7RNPeiK7p652ElemCrA2', 'student', 0, 'active', '2026-03-10 00:19:46', NULL),
(8, 'Lê Lung Linh', 'Linh111', 'lelunglinh111@gmail.com', '$2y$10$hts8/gY3f20jXPYtYtWvzOx88GVF4zyqcVvvhLORcDHt7rrFBAsXm', 'student', 0, 'locked', '2026-03-10 00:28:50', NULL),
(9, 'chi', 'chichi', 'nb3331@gmail.com', '$2y$10$TqrgTwPXKOwVOXSJH5VUcO7NKafU8Abn1NBpIpzkAGG4lRisf1P/6', 'student', 0, 'active', '2026-03-10 01:02:01', NULL),
(10, 'diêp', 'diep', 'nb31@gmail.com', '$2y$10$aCPqkcFKkOSAFrw.dve5zOhmi0OKCl1.bJh376weu//qwdq0vpnQW', 'student', 0, 'active', '2026-03-21 10:02:40', NULL),
(11, 'diep', 'diep1', 'nbnn31@gmail.com', '$2y$10$CCLN2i6xVMGCacInxHW/9ehJL7c84BEx68TyI/NNCZjhuk/b.ffbq', 'student', 0, 'active', '2026-03-21 10:19:23', NULL),
(12, 'chi', 'dp', 'n1@gmail.com', '$2y$10$tVDt4rGpFVVmKBaRrXgh1.Lz2W/ia9ZrLHMlaa5qfAM59tT4iWnau', 'student', 0, 'active', '2026-03-21 10:20:02', NULL),
(13, 'linh', 'linh', 'n231@gmail.com', '$2y$10$5/CKjIPaYPGFgh8aURO/Zuwdki110Vrxf4dVUT.SBrAhyzvNwxDci', 'student', 0, 'active', '2026-03-22 04:34:58', NULL),
(14, 'Bình', 'binh', 'nb3471@gmail.com', '$2y$10$XW5zKn8etE6Grby96QIbe.7aUR7V.jR7YlKADrzWfbIXkgVfyeXgi', 'student', 0, 'active', '2026-04-02 14:56:25', NULL),
(15, 'chii', 'chuii', 'nb3571@gmail.com', '$2y$10$AkW65ByguLRWZlDwOfBotOwqbq//UNbuLZwiEAqwBa9mtzfAw9ofy', 'student', 0, 'active', '2026-04-07 01:03:35', NULL),
(16, 'Nguyễn Thị Duyên', 'duyen', '471@gmail.com', '$2y$10$1dY88lZPwJxls2dmO0UJBehIytti9ZxA11KLw7IEbvAijCysn5p9q', 'student', 0, 'active', '2026-04-14 00:37:14', NULL),
(20, 'Mentor Chi', 'giaovien', 'mentor@suviet.vn', '$2y$10$bVq7C.oSAe3Lqb4P50IaTeff1PVKha5zkLNrhBB9ZMHiMnjyP6XMq', 'teacher', 1, 'active', '2026-04-25 04:11:39', NULL),
(21, 'Nguyễn Thị Diệp', 'nguyenthidiep', 'qi123456@gmail.com', '$2y$10$4pp8oCCvH7iLjxQoerXlsO07kH9.S1tC1wFXQntAUlOh1W9BV7TWG', 'teacher', 0, 'active', '2026-05-07 14:16:31', NULL),
(22, 'Lê Minh Nam', 'minhnam', 'minhnam@gmail.com', '$2y$10$pRcTWxKAxG5i//lUzvVKK.YtU3/WFU0Cr0DwONp5zku/YgYw4ZWgu', 'student', 1, 'active', '2026-05-07 15:34:21', '2026-05-07 22:37:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vip_requests`
--

CREATE TABLE `vip_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT 0,
  `transaction_code` varchar(50) DEFAULT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vip_requests`
--

INSERT INTO `vip_requests` (`id`, `user_id`, `amount`, `transaction_code`, `proof_image`, `status`, `created_at`) VALUES
(9, 1, 999000, 'Gửi kèm ảnh bill', 'uploads/proofs/proof_1_1777631483.png', 'approved', '2026-05-01 10:31:23'),
(10, 22, 999000, 'Gửi kèm ảnh bill', 'uploads/proofs/proof_22_1778168184.png', 'approved', '2026-05-07 15:36:24');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ai_reminders`
--
ALTER TABLE `ai_reminders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `fb_feedback`
--
ALTER TABLE `fb_feedback`
  ADD PRIMARY KEY (`fb_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fb_status` (`fb_status`);

--
-- Chỉ mục cho bảng `fb_notifications`
--
ALTER TABLE `fb_notifications`
  ADD PRIMARY KEY (`fb_notif_id`),
  ADD KEY `fb_receiver_id` (`fb_receiver_id`);

--
-- Chỉ mục cho bảng `flashcard_log`
--
ALTER TABLE `flashcard_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_lesson_date` (`user_id`,`lesson_id`,`log_date`),
  ADD KEY `idx_fl_user` (`user_id`,`log_date`);

--
-- Chỉ mục cho bảng `lessons_content`
--
ALTER TABLE `lessons_content`
  ADD PRIMARY KEY (`ct_id`),
  ADD UNIQUE KEY `ct_lesson_id` (`ct_lesson_id`);

--
-- Chỉ mục cho bảng `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_lesson` (`user_id`,`lesson_id`),
  ADD KEY `idx_lp_user` (`user_id`);

--
-- Chỉ mục cho bảng `live_sessions`
--
ALTER TABLE `live_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `quiz_scores`
--
ALTER TABLE `quiz_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_lesson` (`user_id`,`lesson_id`),
  ADD KEY `idx_qs_user` (`user_id`);

--
-- Chỉ mục cho bảng `revenue_report`
--
ALTER TABLE `revenue_report`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `student_profile`
--
ALTER TABLE `student_profile`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `study_sessions`
--
ALTER TABLE `study_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ss_user` (`user_id`,`study_date`);

--
-- Chỉ mục cho bảng `teacher_qa`
--
ALTER TABLE `teacher_qa`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `vip_requests`
--
ALTER TABLE `vip_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`status`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ai_reminders`
--
ALTER TABLE `ai_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `fb_feedback`
--
ALTER TABLE `fb_feedback`
  MODIFY `fb_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `fb_notifications`
--
ALTER TABLE `fb_notifications`
  MODIFY `fb_notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `flashcard_log`
--
ALTER TABLE `flashcard_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `lessons_content`
--
ALTER TABLE `lessons_content`
  MODIFY `ct_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `live_sessions`
--
ALTER TABLE `live_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `quiz_scores`
--
ALTER TABLE `quiz_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `revenue_report`
--
ALTER TABLE `revenue_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `student_profile`
--
ALTER TABLE `student_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `study_sessions`
--
ALTER TABLE `study_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT cho bảng `teacher_qa`
--
ALTER TABLE `teacher_qa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `vip_requests`
--
ALTER TABLE `vip_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
