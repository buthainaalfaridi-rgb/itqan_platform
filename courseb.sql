-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2024 at 07:23 PM
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
('JtxfmqGgYFOp1w1sGAyn', 'FQrgNJg8JZC0Ii1y7Zmg'),
('6SY7BAGLbGRV5mbyUwFD', 'G6RJxwEtTIXIVQXFnMmp'),
('6', 'G6RJxwEtTIXIVQXFnMmp'),
('6', 'FQrgNJg8JZC0Ii1y7Zmg');

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

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `tutor_id`, `title`, `book_image`, `description`, `author`, `num_pages`, `book_file`, `upload_date`) VALUES
(2, 'jxYYCFmrGYpMDSi6vAiv', 'Alice&#39;s Adventures in Wonderland!!', 't33fUNatwcmC22ulQTem.jpg', 'a story about Alice who falls down a rabbit hole and lands into a fantasy world that is full of weird, wonderful people and animals.', 'Lewis Caroll Alaa', 202, '8GviP5cZv6OOIOTR7jCa.pdf', '2024-07-30 23:02:20'),
(41, 'jxYYCFmrGYpMDSi6vAiv', 'A million to one', 'clmTXEgeDRzLJqH9azli.jpg', 'test test test !!', 'Tony', 1024, 'Pi5vl50ox1hKA4rvZqgx.pdf', '2024-07-31 22:07:09');

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
('spph61aKkvKjqJwGkn53', 'J7EpbSj3bNaZ1AENMRrP', 'JtxfmqGgYFOp1w1sGAyn', 'jxYYCFmrGYpMDSi6vAiv', 'فيديو رائع للغاية', '2023-12-20'),
('6kebBYu3JFJ5IrdTQZ8c', '4xaHMOJJgfUW20vEcN51', '6', 'jxYYCFmrGYpMDSi6vAiv', 'مرحباً!!', '2024-06-08'),
('Y0fG0qeyGy6uAoEccyaz', 'n4cRn0sc5Jn7CQhz4zGK', '6', 'ydbr0tB2zes542yxzAjI', 'روعة', '2024-06-08'),
('GLqkQsCVpjHsjv02ks3j', '4xaHMOJJgfUW20vEcN51', '6', 'jxYYCFmrGYpMDSi6vAiv', 'روعة.', '2024-06-15');

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
('', '', 'لبلب', 'alazokari@hotmail.com', 5454454, 'بليببلبي'),
('', '', 'alaa', 'te@gmail.com', 735722589, 'heelo'),
('', '', 'alaa', 'te@gmail.com', 54552554, 'hello\r\n'),
('', '', 'alaa', 'alazokari@hotmail.com', 2147483647, 'fffffff');

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
('J7EpbSj3bNaZ1AENMRrP', 'jxYYCFmrGYpMDSi6vAiv', 'FQrgNJg8JZC0Ii1y7Zmg', 'الدرس الثاني!', 'html bascics', 'kTHqlC1kRCeASgZ1ox6f.mp4', 'NKTMHDr02kLLzx3Rbkee.png', '2023-12-18', 'active'),
('r6MpcX9mOBw0V7yJxPjb', 'jxYYCFmrGYpMDSi6vAiv', 'FQrgNJg8JZC0Ii1y7Zmg', 'الدرس الثالث', 'الدرس الثالث من دورة HTML', '5YhPEjASupfhtOVNtKwA.mp4', 'REEUa7sSB1aO4DfPpoQO.png', '2023-12-18', 'active'),
('4xaHMOJJgfUW20vEcN51', 'jxYYCFmrGYpMDSi6vAiv', 'FQrgNJg8JZC0Ii1y7Zmg', 'الدرس الأول', 'الدرس الأول في دورة HTML', 'dcgAWiUjHz6eTBQL9Cvn.MP4', 'RAWkJZRAIx3Y7aFGvyTF.png', '2023-12-22', 'active'),
('n4cRn0sc5Jn7CQhz4zGK', 'ydbr0tB2zes542yxzAjI', 'G6RJxwEtTIXIVQXFnMmp', 'HTML lesson 1', 'test test test ', 'yn0aZGigTueOKcZ1WSyT.mp4', 'QTDxXWB0jOMTMCgkU4AW.jpg', '2024-06-01', 'active'),
('qBjX08OUgpkecyhlW9PA', 'ydbr0tB2zes542yxzAjI', 'G6RJxwEtTIXIVQXFnMmp', 'test', 'cccc', '4uA2n4h3irlwBVh8Ytbf.mp4', 'o2O6j5zB3a5Vp2NLYKVw.png', '2024-06-05', 'active');

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
('6SY7BAGLbGRV5mbyUwFD', 'jxYYCFmrGYpMDSi6vAiv', '4xaHMOJJgfUW20vEcN51'),
('6SY7BAGLbGRV5mbyUwFD', 'ydbr0tB2zes542yxzAjI', 'n4cRn0sc5Jn7CQhz4zGK'),
('6', 'jxYYCFmrGYpMDSi6vAiv', '4xaHMOJJgfUW20vEcN51');

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
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`) VALUES
('FQrgNJg8JZC0Ii1y7Zmg', 'jxYYCFmrGYpMDSi6vAiv', 'HTML', 'تعلم HTML من الصفر .. ', 'h0jjbFYIX0vs7PWHnQpp.png', '2023-12-18', 'active');

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
('jxYYCFmrGYpMDSi6vAiv', 'Alaa ', 'desginer', 'alaa@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'AZl4EV6LY2rFcsTzNS0K.jpg', 0),
('ydbr0tB2zes542yxzAjI', 'alaa', 'accountant', 'test@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'odtznxbSdQh5M6PUyrM6.jpeg', 0),
('Im8BMuIUrmsQoWozxjbs', 'new', 'desginer', 'test1@gmail.com', '7ab395624ef7fa7cdecb7a6a4bebfae20c81a2e4', 'P3GRp4ppBbDpmWLyx0uz.jpg', 0),
('LIo1VMtqbyEclbYU1tie', 'admin', 'developer', 'admin1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '1tgvCueqvqcJV0SX2qwD.png', 0),
('W3krjT4eiAujywfmHuxk', 'ahmed', 'developer', 'hello@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'zVRFhoKeTYfE0tWUV6aQ.jpg', 0),
('3eFQURUmc3TQ3t6szhaw', 'Mr Phorg', 'developer', 'mrphorg@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', '5xcYxlqAIEj9ytKlcWcV.jpg', 0);

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
('1', 'Ahmed Ali', 'alazokari@hotmail.co', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'o79p5UOsW7mmt018Ka3V.png'),
('2', 'Khaled', 'test1@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'G1QAS8y2NEMGFhxj3NKy.png'),
('6', 'أحمد', 'user@gmail.com', '7ab395624ef7fa7cdecb7a6a4bebfae20c81a2e4', 'vgueOgJTDNiEMActzVdN.jpg'),
('UGR6TdumP4kwy95F5QCt', 'Mr Phorg', 'mrphorg@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', 'yGja78i8yd0otXuxdd9p.jpg');


-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`name`) VALUES
('التطوير'),
('التصوير'),
('التصميم'),
('الاعمال'),
('التسويق'),
('الموسيقى'),
('البرمجيات'),
('العلوم');

-- --------------------------------------------------------


--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
