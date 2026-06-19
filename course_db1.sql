-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 01:38 AM
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
  `user_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`user_id`, `playlist_id`) VALUES
('', ''),
('', ''),
('', ''),
('', '');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `book_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `num_pages` int(11) NOT NULL,
  `book_file` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `name_en`) VALUES
(1, 'التصوير', 'Photography'),
(2, 'التطوير', 'Development'),
(3, 'التصميم', 'Design'),
(5, 'التسويق', 'Marketing'),
(6, 'الموسيقى', 'Music'),
(7, 'البرمجيات', 'Software');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` varchar(20) NOT NULL,
  `content_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `user_id`, `tutor_id`, `comment`, `date`) VALUES
('', '', '', '', '', '0000-00-00'),
('', '', '', '', '', '0000-00-00'),
('', '', '', '', '', '0000-00-00'),
('', '', '', '', '', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `message` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
('', '', '', '', 0, ''),
('', '', '', '', 0, ''),
('', '', '', '', 0, ''),
('', '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `video` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `tutor_id`, `playlist_id`, `title`, `description`, `video`, `thumb`, `date`, `status`) VALUES
('', '', '', '', '', '', '', '0000-00-00', ''),
('', '', '', '', '', '', '', '0000-00-00', ''),
('', '', '', '', '', '', '', '0000-00-00', ''),
('', '', '', '', '', '', '', '0000-00-00', ''),
('', '', '', '', '', '', '', '0000-00-00', ''),
('bTwUQ0vbosavKmaH074e', 'lf27NgsOyYU8sla1jEQs', 'zx0g0aPMgrNMtl9lZnTN', 'تعلم', 'لبيس', 'gXnwKIN96n6N7q523P6E.mp4', 'YkSOY5zZwM4YyPGYeUoV.png', '2026-01-01', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `content_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`user_id`, `tutor_id`, `content_id`) VALUES
('', '', ''),
('', '', ''),
('', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive',
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`, `category_id`) VALUES
('95xuzJZ4LfmLJWserOjZ', 'lf27NgsOyYU8sla1jEQs', 'تعلم لغة البايثون', 'تهدف هذه القائمة إلى تعليم لغة البرمجة بايثون (Python) من الأساسيات حتى المستوى المتقدم بطريقة مبسطة وعملية.\r\nتغطي الدروس مفاهيم البرمجة الأساسية مثل المتغيرات، الجمل الشرطية، الحلقات، والدوال، ثم تنتقل إلى مواضيع أكثر تقدمًا مثل التعامل مع الملفات، البرمجة الكائنية (OOP)، والمكتبات الشهيرة.\r\n\r\nتحتوي القائمة على أمثلة تطبيقية وتمارين عملية تساعد المتعلم على فهم المفاهيم وتطبيقها في مشاريع حقيقية، مما يجعلها مناسبة للمبتدئين وكذلك لمن لديهم معرفة مسبقة ويرغبون بتطوير مهاراتهم في بايثون.', 'DHmUL7Us2G9L3xv4nS2i.webp', '2025-12-30', 'active', 7),
('RWJCuBUcDHV3ZutIy764', 'lf27NgsOyYU8sla1jEQs', 'قواعد البيانات وإدارة البيانات', 'هذه القائمة موجهة لكل من يريد تعلم كيفية تنظيم وإدارة البيانات بشكل احترافي. ستتعلم SQL وNoSQL، تصميم قواعد البيانات، الاستعلامات المعقدة، تحسين الأداء، وإدارة البيانات الكبيرة. كما ستتعلم كيفية ربط قواعد البيانات بالتطبيقات البرمجية،', 'pJR5O0fVnlptjUsw7n2b.webp', '2025-12-30', 'active', 7),
('w9y6o95of7BFxRubg0Lc', 'lf27NgsOyYU8sla1jEQs', 'الأمن السيبراني وحماية المعلومات', 'هذه القائمة مهمة لكل من يريد حماية أنظمته ومشاريعه البرمجية من المخاطر الإلكترونية. ستتعلم أساسيات الأمن السيبراني، التشفير، تحليل الثغرات، إدارة المخاطر، وتقنيات الحماية الحديثة. كما تشمل الدروس حالات واقعية، أدوات اختبار الاختراق، واستراتيجيات للحفاظ على البيانات والأنظمة بأمان من أي تهديد محتمل.', 'yNVcsitlhv4JUFB9Bx18.webp', '2025-12-30', 'deactive', 7),
('GK8rKBHdQONqTQBXcFBd', 'lf27NgsOyYU8sla1jEQs', 'تصميم واجهات المستخدم وتجربة المستخدم (UI/UX)', 'هذه الدورة تركز على تعلم كيفية تصميم واجهات استخدام تطبيقات ومواقع الإنترنت بطريقة سلسة وجذابة. ستتعلم كيفية تحليل تجربة المستخدم، بناء النماذج الأولية (Wireframes وPrototypes)، اختيار الألوان والخطوط المناسبة، وتحسين التفاعل بين المستخدم والتطبيق. الدورة تشمل مشاريع عملية تمكنك من تطوير مهاراتك في التصميم الرقمي بطريقة احترافية.', 'hqEWay0melLICNvqlHNu.png', '2025-12-30', 'active', 3),
('PdQID8ba1kpg1dDAv9JW', 'lf27NgsOyYU8sla1jEQs', 'التصميم الرقمي والرسوم المتحركة', 'دورة مخصصة لتعلم تصميم الرسوم المتحركة والمؤثرات البصرية للأفلام والفيديوهات والمحتوى الرقمي. ستتعلم استخدام برامج التصميم مثل Adobe After Effects وPhotoshop وIllustrator لإنشاء الرسوم المتحركة، التأثيرات البصرية، والتصاميم التفاعلية. الدورة تقدم أمثلة عملية تساعدك على تحويل الأفكار الإبداعية إلى محتوى بصري ديناميكي واحترافي.', 'LgWRyaoDrETnHfOsQdeH.webp', '2025-12-30', 'active', 3),
('uNBFrp1f5tzc09KiQtsA', 'lf27NgsOyYU8sla1jEQs', 'أساسيات التصميم الجرافيكي', 'دورة مخصصة للمبتدئين الذين يريدون تعلم مبادئ التصميم من البداية. ستتعلم أساسيات التصميم مثل الألوان، التباين، التنسيق، الخطوط، والتوازن البصري. الدورة تحتوي على أمثلة عملية لتصميم شعارات وبوسترات وبطاقات أعمال، مع تدريبات تساعدك على فهم كيفية تحويل الأفكار إلى تصاميم جذابة واحترافية.', 'ypW7RGrhB8dHg4YKFT8u.webp', '2025-12-30', 'deactive', 3),
('8xz9bF07iJeJJJWVEn1R', 'lf27NgsOyYU8sla1jEQs', 'أساسيات التصوير الفوتوغرافي', 'دورة مخصصة للمبتدئين الذين يريدون تعلم فن التصوير من الصفر. ستتعلم أساسيات استخدام الكاميرا، الإعدادات الأساسية مثل سرعة الغالق، فتحة العدسة، ISO، والتقاط الصور بطريقة صحيحة. تشمل الدورة نصائح حول التكوين البصري، الإضاءة، اختيار الزوايا، وكيفية تحسين الصور لتبدو احترافية.', 'cvviJh4pjTp5epTQ9rwN.webp', '2025-12-30', 'active', 2),
('4s5fcy7AsyT3ahFZLjzw', 'lf27NgsOyYU8sla1jEQs', 'كورس المونتاج الشامل للمبتدئين', 'هذه الدورة مخصصة للمبتدئين الذين يريدون تعلم فن التصوير من الصفر. ستتعلم أساسيات استخدام الكاميرا، الإعدادات الأساسية مثل سرعة الغالق، فتحة العدسة، ISO، والتقاط الصور بطريقة صحيحة. تشمل الدورة نصائح حول التكوين البصري، الإضاءة، اختيار الزوايا، وكيفية تحسين الصور لتبدو احترافية', 'c0KBWFeuzqziM9WV0rlj.webp', '2025-12-30', 'active', 2),
('zx0g0aPMgrNMtl9lZnTN', 'lf27NgsOyYU8sla1jEQs', 'أساسيات التسويق الرقمي', 'دورة مخصصة للمبتدئين الراغبين في فهم أساسيات التسويق عبر الإنترنت. ستتعلم المبادئ الأساسية للتسويق الرقمي، استراتيجيات الوصول إلى الجمهور، بناء الهوية الرقمية للعلامة التجارية، وكيفية استخدام القنوات المختلفة مثل البريد الإلكتروني ووسائل التواصل الاجتماعي. الدورة تتضمن أمثلة عملية على حملات تسويقية ناجحة ونصائح لتحليل الأداء وقياس النتائج.', 'n8ljhAwM6IZwhqr2lDlV.webp', '2025-12-30', 'active', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `profession_ar` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `name`, `profession`, `email`, `password`, `image`, `profession_ar`) VALUES
('lf27NgsOyYU8sla1jEQs', 'buthaina', 'developer', 'buthaina@gmail.com', '20eabe5d64b0e216796e834f52d61fd0b70332fc', 'Jgy65SoCk2EHu8HKEaYp.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`) VALUES
('6tji7uHar0j1DsFu1vcD', 'sara', 'sara@gmail.com', '20eabe5d64b0e216796e834f52d61fd0b70332fc', 'tkWVhLgQR3ZNmO0rAgKI.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_chats`
--

CREATE TABLE `user_chats` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('incoming','outgoing') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chats`
--

INSERT INTO `user_chats` (`id`, `user_id`, `message`, `message_type`, `created_at`, `timestamp`) VALUES
(2, 'ksYUzio63BkCgUdpSpw9', 'hi', 'outgoing', '2025-12-31 07:03:19', '2025-12-31 07:03:19'),
(3, 'ksYUzio63BkCgUdpSpw9', 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2025-12-31 07:03:20', '2025-12-31 07:03:20'),
(4, 'ksYUzio63BkCgUdpSpw9', 'l', 'outgoing', '2025-12-31 07:03:27', '2025-12-31 07:03:27'),
(5, 'ksYUzio63BkCgUdpSpw9', 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2025-12-31 07:03:28', '2025-12-31 07:03:28'),
(6, 'ksYUzio63BkCgUdpSpw9', 'l', 'outgoing', '2025-12-31 07:03:30', '2025-12-31 07:03:30'),
(7, 'ksYUzio63BkCgUdpSpw9', 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2025-12-31 07:03:30', '2025-12-31 07:03:30'),
(8, 'ksYUzio63BkCgUdpSpw9', 'u', 'outgoing', '2025-12-31 07:03:33', '2025-12-31 07:03:33'),
(9, 'ksYUzio63BkCgUdpSpw9', 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2025-12-31 07:03:34', '2025-12-31 07:03:34'),
(10, 'ksYUzio63BkCgUdpSpw9', 'r', 'outgoing', '2025-12-31 07:11:53', '2025-12-31 07:11:53'),
(11, 'ksYUzio63BkCgUdpSpw9', 'فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية.', 'incoming', '2025-12-31 07:11:54', '2025-12-31 07:11:54'),
(24, '6tji7uHar0j1DsFu1vcD', 'g', 'outgoing', '2025-12-31 09:25:16', '2025-12-31 09:25:16'),
(25, '6tji7uHar0j1DsFu1vcD', 'Failed to connect to the internet or you have reached the maximum usage of messages per day.', 'incoming', '2025-12-31 09:25:17', '2025-12-31 09:25:17'),
(26, '6tji7uHar0j1DsFu1vcD', 'gg', 'outgoing', '2025-12-31 09:26:27', '2025-12-31 09:26:27'),
(27, '6tji7uHar0j1DsFu1vcD', 'Failed to connect to the internet or you have reached the maximum usage of messages per day.', 'incoming', '2025-12-31 09:26:28', '2025-12-31 09:26:28'),
(28, '6tji7uHar0j1DsFu1vcD', 'j', 'outgoing', '2025-12-31 22:04:53', '2025-12-31 22:04:53'),
(30, '6tji7uHar0j1DsFu1vcD', 'h', 'outgoing', '2026-01-01 01:12:25', '2026-01-01 01:12:25'),
(32, '6tji7uHar0j1DsFu1vcD', 'j', 'outgoing', '2026-01-01 01:14:23', '2026-01-01 01:14:23'),
(33, '6tji7uHar0j1DsFu1vcD', 'Failed to connect to the internet or you have reached the maximum usage of messages per day.', 'incoming', '2026-01-01 01:14:24', '2026-01-01 01:14:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_chats`
--
ALTER TABLE `user_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--CREATE TABLE mcq ( id INT AUTO_INCREMENT PRIMARY KEY, content_id INT NOT NULL, question VARCHAR(255) NOT NULL, FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE );

--CREATE TABLE mcq_option ( id INT AUTO_INCREMENT PRIMARY KEY, mcq_id INT NOT NULL, option_text VARCHAR(255) NOT NULL, is_correct TINYINT(1) DEFAULT 0, FOREIGN KEY (mcq_id) REFERENCES mcq(id) ON DELETE CASCADE );

