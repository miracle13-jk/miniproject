-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 06:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cv_path` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `student_id`, `cv_path`, `applied_at`) VALUES
(2, 4, 7, 'uploads/cvs/599731c7e0306.pdf', '2025-05-11 16:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Contract','Internship') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `description`, `requirements`, `location`, `posted_at`, `deadline`, `salary`, `job_type`, `is_active`) VALUES
(3, 'Developer', 'TCS', '<p>We are looking for a developer in out company,</p>\r\n\r\n<p>If you are interested you can apply.</p>', '<p>Qualification: MCA</p>\r\n\r\n<p>Experience:1-2 years</p>\r\n\r\n<p>Must have programming skils</p>', 'guwahati', '2025-05-11 16:16:12', '2025-05-30', '7k', 'Contract', 1),
(4, 'Data-Entry', 'AJ', '<p>speed typing</p>', '<p>Any one with typing speed can apply</p>', 'guwahati', '2025-05-11 16:19:00', '2025-05-30', '4k', 'Part-time', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `username` varchar(8) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `role` enum('student','employer','admin','') NOT NULL DEFAULT 'student',
  `cv_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `cv_path`) VALUES
(2, '0', 'phili', '0', '0', 'student', NULL),
(3, 'Miracle Rani', 'mira', 'miraclerani718@gmail', '$2y$10$HAd7zuo8h./k1', 'student', NULL),
(4, 'carol', 'carol', 'addy12@gmail.com', '$2y$10$pVVHf56vCFSJq', 'student', NULL),
(5, 'Winnie', 'winnie', 'winnie@gmail.com', 'winnie12', 'student', 'uploads/cvs/599731c7e0306.pdf'),
(6, 'Glaricia', 'dred', 'gla@gmail.com', 'gla@12', 'employer', NULL),
(7, 'Sylvia', 'sylvia', 'syl@gmail.com', 'syl@123', 'student', 'uploads/cvs/599731c7e0306.pdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
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
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
