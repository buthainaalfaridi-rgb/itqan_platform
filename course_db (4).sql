-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2026 at 08:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `user_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`user_id`, `playlist_id`) VALUES
(4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `book_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `num_pages` int(11) DEFAULT NULL,
  `book_file` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `tutor_id`, `title`, `book_image`, `description`, `author`, `num_pages`, `book_file`, `upload_date`, `category_id`) VALUES
(1, 1, 'iji', '3YNfbLNFuC1D1pRj6JxK.jpg', 'kihik', 'kn', 567, 'Jh9Xz3g3IRfmfqAU07tX.pdf', '2026-01-10 00:02:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `name_en`) VALUES
(1, 'تصوير', 'Photography');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `video` varchar(100) DEFAULT NULL,
  `thumb` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `tutor_id`, `playlist_id`, `title`, `description`, `video`, `thumb`, `date`, `status`) VALUES
(1, 1, 26, 'jhi', 'jkhj', 'HfWCXvsHhytbnOUPxUZ5.mp4', 'wjKNyfNn8Zcr0Ig4rLbb.png', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `email_otps`
--

CREATE TABLE `email_otps` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_otps`
--

INSERT INTO `email_otps` (`id`, `name`, `email`, `code`, `expires_at`, `used`, `created_at`) VALUES
(2, '', 'buthainaalfaridi@gmail.com', '248402', '2026-01-11 18:33:26', 1, '2026-01-11 17:28:26'),
(3, '', 'buthainaalfaridi@gmail.com', '591253', '2026-01-11 18:34:11', 0, '2026-01-11 17:29:11'),
(4, '', 'buthainaalfaridi@gmail.com', '106902', '2026-01-11 18:37:13', 0, '2026-01-11 17:32:13'),
(5, 'sara', 'nooradfgjjg@gmail.com', '729389', '2026-01-12 02:12:09', 0, '2026-01-12 01:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mcq`
--

CREATE TABLE `mcq` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mcq_option`
--

CREATE TABLE `mcq_option` (
  `id` int(11) NOT NULL,
  `mcq_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `thumb` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`, `category_id`) VALUES
(1, 1, 'hkh', 'lhhhhhf', 'N21Noa30wG9FpRJToYNY.webp', NULL, 'active', 1),
(2, 1, 'jhjhjhjj', 'hjhj', 'f9X8y6uYWwhkjE0RGGqi.png', NULL, 'active', 1),
(3, 1, 'لبغابل', 'الغالا', 'at3MJaFtu4O7L4vuNpC0.png', NULL, 'active', 1),
(4, 1, 'لبغابل', 'الغالا', 'NKpVusxzQr9HE7BTpiXQ.png', NULL, 'active', 1),
(5, 1, 'لبغابل', 'الغالا', 'qizBg7zE9xjgdZNZf1Fl.png', NULL, 'active', 1),
(6, 1, 'هاه', 'تلاتلا', 'HpfED0cqHe9pmJwGqjtm.png', NULL, 'active', 1),
(7, 1, 'هاه', 'تلاتلا', 'Yri2fkD4uluk3vfdG3S3.png', NULL, 'active', 1),
(8, 1, 'تلا', 'ىلارتى', 'QTWaN7TJry82UsqMEolx.png', NULL, 'deactive', 1),
(9, 1, 'تلا', 'ىلارتى', 'Jh5IqYgU8MfuzOhjH0iN.png', NULL, 'deactive', 1),
(10, 1, 'تلا', 'ىلارتى', 'iiZlZ4jQmwYd8IVohHdL.png', NULL, 'deactive', 1),
(11, 1, 'تلا', 'ىلارتى', 'KWZwB61DSUcrUijbBPnp.png', NULL, 'deactive', 1),
(12, 1, 'ييي', 'ييببي', 'dpIQwT7FEVoDJJotBQEC.png', NULL, 'deactive', 1),
(13, 1, 'نتىةنىنىن', 'نةنةنة\r\n', 'N9Yu9oHcT8ju3aNDGE0X.png', NULL, 'deactive', 1),
(15, 1, 'ة ة ةنىتنلاىت', 'مةمةمة', '69a58c164c417_playlist.png', NULL, 'active', 1),
(16, 1, 'رلابر', 'بربر', '69a69b744137f.png', NULL, 'active', 1),
(17, 1, 'ت', 'ارار', '69a69e4cf2a80.png', NULL, 'active', 1),
(18, 1, 'ت', 'ارار', '69a6a0b0c3b0b.png', NULL, 'active', 1),
(19, 1, 'ت', 'ارار', '69a6a0e447d94.png', NULL, 'active', 1),
(20, 1, 'ت', 'ارار', '69a6a32ba1725.png', NULL, 'active', 1),
(21, 1, 'ت', 'ارار', '69a6a372692f8.png', NULL, 'active', 1),
(22, 1, 'ت', 'ارار', '69a6a38741b12.png', NULL, 'active', 1),
(23, 1, 'ؤؤؤ', 'ءسؤ', '69a6e5844d1d5.jpg', NULL, 'active', 1),
(24, 1, 'يصي', 'صي', '69a6eabd632b2.jpg', NULL, 'active', 1),
(25, 1, 'ةىن', 'ةىنة', '69a7fe04802c0.jpg', NULL, 'active', 1),
(26, 1, 'ةىن', 'ةىنة', '69a7fe7edcc98.jpg', NULL, 'active', 1),
(27, 1, 'ىلاتلا', 'تلاتلاتلاتعلع', '69a7fea386d8d.png', NULL, 'active', 1),
(28, 1, 'تلاىتنلا', 'اهاهناها', '69a7fee6056a1.jpg', NULL, 'active', 1),
(29, 1, 'تتتتت', 'لالالالالالارة', '69a7ff0740a66.jpg', NULL, 'active', 1),
(69, 1, 'تتتتت', 'لالالالالالارة', '69a800715366c.jpg', NULL, 'active', 1),
(70, 1, 'تالعتاع', 'ننىن', '69a804fdd6764.jpg', NULL, 'active', 1),
(71, 1, 'تالعتاع', 'ننىن', '69a80588e2e1e.jpg', NULL, 'active', 1),
(72, 1, 'تالعتاع', 'ننىن', '69a8061e8e864.jpg', NULL, 'active', 1),
(73, 1, 'تالعتاع', 'ننىن', '69a8062040146.jpg', NULL, 'active', 1),
(74, 1, 'تالعتاع', 'ننىن', '69a8062761a85.jpg', NULL, 'active', 1),
(75, 1, 'تالعتاع', 'ننىن', '69a80634038dd.jpg', NULL, 'active', 1),
(76, 1, 'تالعتاع', 'ننىن', '69a8065c98eb6.jpg', NULL, 'active', 1),
(77, 1, 'تالعتاع', 'ننىن', '69a8065f8f575.jpg', NULL, 'active', 1),
(78, 1, 'تالعتاع', 'ننىن', '69a807406adee.jpg', NULL, 'active', 1),
(79, 1, 'تالعتاع', 'ننىن', '69a80b6b19776.jpg', NULL, 'active', 1),
(80, 1, 'تالعتاع', 'ننىن', '69a80b7130fe0.jpg', NULL, 'active', 1);

-- --------------------------------------------------------

--
-- Table structure for table `playlist_questions`
--

CREATE TABLE `playlist_questions` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  `answer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist_questions`
--

INSERT INTO `playlist_questions` (`id`, `playlist_id`, `tutor_id`, `question`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
(6, 12, 1, 'ت', 'ت', 'ت', 'ل', 'ل', 'option2'),
(7, 13, 1, 'مخ', 'ن', 'ن', 'ن', 'ن', 'option1'),
(8, 13, 1, 'ن', 'و', 'وة', 'ة', 'و', 'option1'),
(9, 23, 1, 'يسصيي\r\n', 'سيءس', 'سيسي', 'سيسصي', 'يءسيس', 'option2'),
(10, 24, 1, 'نةنة\r\n', 'وة', 'ةم', 'ة ة', 'ة ', 'option1'),
(12, 3, 1, 's', 'd', 'd', 'd', 'd', 'option1'),
(13, 3, 1, 's', 'd', 'd', 'd', 'd', 'option1'),
(14, 3, 1, 'ydty', '1', '1', '1', '1', '1'),
(16, 1, 1, '2', '1', '1', '2', '3', 'option4');

-- --------------------------------------------------------

--
-- Table structure for table `playlist_question_options`
--

CREATE TABLE `playlist_question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `answered_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_certificates`
--

CREATE TABLE `quiz_certificates` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `score` int(3) NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `issued_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_options`
--

CREATE TABLE `quiz_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `question_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(50) DEFAULT NULL,
  `profession_ar` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `name`, `profession`, `profession_ar`, `email`, `password`, `image`) VALUES
(1, 'buthaina', 'developer', '', 'buthaina@gmail.com', '20eabe5d64b0e216796e834f52d61fd0b70332fc', '6GBxmjlpkMwMQS5PfceU.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `upload`
--

CREATE TABLE `upload` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload`
--

INSERT INTO `upload` (`id`, `user_id`, `file_name`, `file_path`, `file_size`, `file_hash`, `uploaded_at`) VALUES
(1, 4, 'summer training 2 .pdf', 'uploaded_files/1772012955_summer training 2 .pdf', 933116, '313e64bd497d59be2bf741401254c101', '2026-02-25 09:49:15'),
(2, 4, 'summer training 2 Nagla.docx', 'uploaded_files/1772013555_summer training 2 Nagla.docx', 1453047, 'd85e8a284a777f8ae1821f1e3b2ba777', '2026-02-25 09:59:15');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `zip_name` varchar(255) NOT NULL,
  `upload_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `original_name`, `zip_name`, `upload_time`) VALUES
(1, 'Algorithmss OS.pdf', 'uploads/1771219258_Algorithmss OS.pdf', '2026-02-15 21:20:58'),
(2, 'AlgorithmssOS.pdf', 'uploads/1771219268_AlgorithmssOS.pdf', '2026-02-15 21:21:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `image` varchar(100) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `provider` varchar(20) DEFAULT 'local',
  `account_type` enum('basic','premium') DEFAULT 'basic',
  `storage_limit` bigint(20) DEFAULT 52428800
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `verification_code`, `verified`, `image`, `email_verified_at`, `provider`, `account_type`, `storage_limit`) VALUES
(4, 'sara', 'sara@gmail.com', '20eabe5d64b0e216796e834f52d61fd0b70332fc', NULL, 0, 'Dpid5vqrsaSvO4grh684.jpg', NULL, 'local', 'basic', 52428800);

-- --------------------------------------------------------

--
-- Table structure for table `user_chats`
--

CREATE TABLE `user_chats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `message_type` enum('incoming','outgoing') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chats`
--

INSERT INTO `user_chats` (`id`, `user_id`, `message`, `message_type`, `timestamp`) VALUES
(1, 4, 'f', 'outgoing', '2026-02-25 12:33:22'),
(2, 4, 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2026-02-25 12:33:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`user_id`,`playlist_id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Indexes for table `email_otps`
--
ALTER TABLE `email_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`user_id`,`content_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `mcq`
--
ALTER TABLE `mcq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `mcq_option`
--
ALTER TABLE `mcq_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mcq_id` (`mcq_id`);

--
-- Indexes for table `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `playlist_questions`
--
ALTER TABLE `playlist_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`),
  ADD KEY `fk_tutor` (`tutor_id`);

--
-- Indexes for table `playlist_question_options`
--
ALTER TABLE `playlist_question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_option_id` (`selected_option_id`);

--
-- Indexes for table `quiz_certificates`
--
ALTER TABLE `quiz_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Indexes for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `upload`
--
ALTER TABLE `upload`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_file_unique` (`user_id`,`file_hash`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_otps`
--
ALTER TABLE `email_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mcq`
--
ALTER TABLE `mcq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mcq_option`
--
ALTER TABLE `mcq_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist`
--
ALTER TABLE `playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `playlist_questions`
--
ALTER TABLE `playlist_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `playlist_question_options`
--
ALTER TABLE `playlist_question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_certificates`
--
ALTER TABLE `quiz_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_options`
--
ALTER TABLE `quiz_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upload`
--
ALTER TABLE `upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `user_chats`
--
ALTER TABLE `user_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_ibfk_2` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `likes_ibfk_3` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mcq`
--
ALTER TABLE `mcq`
  ADD CONSTRAINT `mcq_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mcq_option`
--
ALTER TABLE `mcq_option`
  ADD CONSTRAINT `mcq_option_ibfk_1` FOREIGN KEY (`mcq_id`) REFERENCES `mcq` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist`
--
ALTER TABLE `playlist`
  ADD CONSTRAINT `playlist_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `playlist_questions`
--
ALTER TABLE `playlist_questions`
  ADD CONSTRAINT `fk_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playlist_questions_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_question_options`
--
ALTER TABLE `playlist_question_options`
  ADD CONSTRAINT `playlist_question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `playlist_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`selected_option_id`) REFERENCES `quiz_options` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quiz_certificates`
--
ALTER TABLE `quiz_certificates`
  ADD CONSTRAINT `quiz_certificates_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_options`
--
ALTER TABLE `quiz_options`
  ADD CONSTRAINT `quiz_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD CONSTRAINT `user_chats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
