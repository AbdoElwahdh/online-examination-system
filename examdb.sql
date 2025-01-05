-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2025 at 03:56 AM
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
-- Database: `examdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `examinee_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `examinee_id`, `exam_id`, `question_id`, `answer_text`) VALUES
(0, 303, 51, 34, 'int x=10'),
(0, 303, 51, 35, 'True'),
(0, 303, 52, 36, 'True'),
(0, 303, 49, 31, 'DBMS'),
(0, 33, 51, 34, 'name='),
(0, 33, 51, 35, 'True'),
(0, 303, 55, 43, 'Aritificial intelegence'),
(0, 303, 55, 44, 'BFS'),
(0, 303, 55, 45, 'False'),
(0, 33, 55, 43, 'Aritificial intelegence'),
(0, 33, 55, 44, 'Hill climbing'),
(0, 33, 55, 45, 'True'),
(0, 28, 55, 43, 'Aritificial intelegence'),
(0, 28, 55, 44, 'A*'),
(0, 28, 55, 45, 'True');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `created_by`, `created_at`) VALUES
(1, 'C++', 'C++ Dess', 10, '2024-12-19 22:44:53'),
(3, 'Web Development', 'Full-stack development course', 12, '2024-12-20 14:00:00'),
(4, 'Machine Learning', 'Introduction to ML', 13, '2024-12-21 16:00:00'),
(5, 'Artificial Intelligence', 'AI and its applications', 11, '2024-12-22 18:00:00'),
(6, 'Data Analysis', 'Data Analysis with Python', 11, '2024-12-19 12:00:00'),
(7, 'Python Programming', 'Learn Python basics', 10, '2024-12-18 10:00:00'),
(304, 'os', 'operating system', 123, '2024-12-22 07:28:50'),
(307, 'os', 'operating system', 32, '2025-01-04 14:36:23'),
(308, 'Data Structure', 'The study of data structures ', 305, '2025-01-05 03:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enroll_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`ID`, `user_id`, `course_id`, `enroll_at`, `accepted`) VALUES
(2, 29, 6, '2024-12-21 15:28:43', 0),
(3, 29, 1, '2024-12-21 15:28:43', 0),
(4, 29, 7, '2024-12-23 00:55:17', 1),
(10, 29, 4, '2024-12-23 01:21:41', 0),
(11, 29, 3, '2024-12-23 01:28:11', 0),
(18, 33, 5, '2025-01-05 01:40:08', 1),
(19, 303, 5, '2025-01-05 01:40:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `examinee_exams`
--

CREATE TABLE `examinee_exams` (
  `id` int(11) NOT NULL,
  `examinee_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `score` double DEFAULT NULL,
  `duration` datetime DEFAULT (`end_time` - `start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinee_exams`
--

INSERT INTO `examinee_exams` (`id`, `examinee_id`, `exam_id`, `start_time`, `end_time`, `score`, `duration`) VALUES
(5, 303, 55, '2025-01-05 03:02:54', '2025-01-05 04:03:06', 33.333333333333, '0000-00-00 00:00:00'),
(6, 33, 55, '2025-01-05 03:03:23', '2025-01-05 04:03:30', 100, '0000-00-00 00:00:00'),
(7, 28, 55, '2025-01-05 03:03:47', '2025-01-05 04:03:56', 66.666666666667, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `duration` int(11) DEFAULT 60
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `description`, `course_id`, `created_by`, `created_at`, `duration`) VALUES
(49, 'midterm exam', 'for CS and IS departments', 304, 123, '2025-01-04 16:22:04', 60),
(51, 'midterm exam', 'for IS and CS departments', 7, 10, '2025-01-04 17:00:28', 60),
(52, 'oral exam', 'CS and AI departments', 1, 10, '2025-01-04 18:28:36', 60),
(55, 'Ai oral', 'CA,AI DEPARTMENTS', 5, 11, '2025-01-05 04:02:36', 60),
(56, 'data analysis quiz', 'Ai dep', 6, 11, '2025-01-05 04:08:10', 60);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `user_id`, `course_id`, `feedback_text`, `receiver_id`, `response`, `created_at`) VALUES
(18, 303, 1, 'that was a funny thing🤣', 10, 'thank you\r\ni\'m a cool teacher aint I 😎😁', '2025-01-04 22:44:32'),
(19, 33, 1, 'i hoped it\'s was a bit easier midterm😓😥', 10, 'keep pushing yourself you are doing great🥰', '2025-01-04 22:55:45'),
(25, 303, 1, 'you asked for a testing feedback\r\n\r\nglad to help😁', 1, 'thank you \r\ni really appreciate it 🥰', '2025-01-05 02:49:45');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(25, 31, 'mediary between user and hardware', 1),
(26, 31, 'powerpoint', 0),
(27, 31, 'DBMS', 0),
(28, 31, 'Calculation program', 0),
(35, 34, 'x=4', 0),
(36, 34, 'y=True', 0),
(37, 34, 'int x=10', 1),
(38, 34, 'name=\"ahmed\"', 0),
(39, 35, 'True', 1),
(40, 35, 'False', 0),
(41, 36, 'True', 1),
(42, 36, 'False', 0),
(63, 43, 'Aritificial intelegence', 1),
(64, 43, 'Artificial hand', 0),
(65, 43, 'Artificial ideas', 0),
(66, 43, 'none of the above', 0),
(67, 44, 'Hill climbing', 1),
(68, 44, 'A*', 0),
(69, 44, 'Greedy', 0),
(70, 44, 'BFS', 0),
(71, 45, 'True', 1),
(72, 45, 'False', 0),
(73, 46, 'True', 1),
(74, 46, 'False', 0),
(75, 47, 'True', 1),
(76, 47, 'False', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('MCQ','TrueFalse','Normal') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `correct_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `question_text`, `question_type`, `created_at`, `correct_answer`) VALUES
(31, 49, 'what\'s os means;', 'MCQ', '2025-01-04 16:22:04', 'mediary between user and hardware'),
(34, 51, 'what in the following is wrong variable declaration?', 'MCQ', '2025-01-04 17:00:28', 'int x=10'),
(35, 51, 'python is an interpreted language', 'TrueFalse', '2025-01-04 17:00:28', 'True'),
(36, 52, 'do you love c++;', 'TrueFalse', '2025-01-04 18:28:36', 'True'),
(43, 55, 'ai stands for ', 'MCQ', '2025-01-05 04:02:36', 'Aritificial intelegence'),
(44, 55, 'most suitable algorithm for real-time game-playing agent is', 'MCQ', '2025-01-05 04:02:36', 'Hill climbing'),
(45, 55, 'computer vision is an ai application', 'TrueFalse', '2025-01-05 04:02:36', 'True'),
(46, 56, 'python used for datascience?', 'TrueFalse', '2025-01-05 04:08:10', 'True'),
(47, 56, 'panda is a data handling library', 'TrueFalse', '2025-01-05 04:08:10', 'True'),
(48, 56, '', '', '2025-01-05 04:08:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'instructor'),
(3, 'examinee');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin', 'Admin@gmail.com', 1, '2024-12-23 06:25:20', '2024-12-23 06:25:20'),
(10, 'Ahmed', '123', 'ahmed@gmail.com', 2, '2024-12-19 22:44:31', '2024-12-19 22:44:31'),
(11, 'Xavi', 'xavi', 'xavi@gmail.com', 2, '2024-12-20 03:20:06', '2024-12-20 03:20:06'),
(12, 'abdulhalem', 'abdulhalem', 'abdulhalem@gmail.com', 1, '2024-12-20 03:20:44', '2024-12-20 03:20:44'),
(13, 'Paulo', 'Paulo', 'Paulo@gmail.com', 2, '2024-12-23 04:26:26', '2024-12-23 04:26:26'),
(27, 'Shahab', 'shahab', 'shahab@gmail.com', 2, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(28, 'Abdul-halem', 'abdulhalem', 'abdul_halem@gmail.com', 3, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(29, 'alex', 'alex', 'alex@gmail.com', 3, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(31, 'Abdulhamed', 'abdulhamed', 'abdulhamed@gmail.com', 2, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(32, 'Bido', 'bido', 'bido@gmail.com', 2, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(33, 'Hegazi', 'Hegazi', 'Hegazi@gmail.com', 3, '2024-12-21 03:34:01', '2024-12-21 03:34:01'),
(123, 'abdo', 'vvn', 'abdo@gmail.com', 2, '2024-12-22 07:26:52', '2024-12-22 07:26:52'),
(125, 'hegazy', 'admin', 'hegazy@gmail.com', 1, '2024-12-23 08:08:51', '2024-12-23 08:08:51'),
(303, 'shehab', '1234', 'shehab@gmail.com', 3, '2024-12-23 10:59:29', '2024-12-23 10:59:29'),
(305, 'instructor', 'insttuctor', 'instructor@gmail.com', 2, '2025-01-05 03:06:21', '2025-01-05 03:06:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_instructor` (`name`,`created_by`) USING HASH,
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `examinee_exams`
--
ALTER TABLE `examinee_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examinee_id` (`examinee_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `exams_ibfk_1` (`course_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `options_ibfk_1` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_ibfk_1` (`exam_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING HASH;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`) USING HASH,
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `examinee_exams`
--
ALTER TABLE `examinee_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `examinee_exams`
--
ALTER TABLE `examinee_exams`
  ADD CONSTRAINT `examinee_exams_ibfk_1` FOREIGN KEY (`examinee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `examinee_exams_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `feedbacks_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
