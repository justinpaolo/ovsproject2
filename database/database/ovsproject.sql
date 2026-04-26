-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 28, 2025 at 04:41 PM
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
-- Database: `ovsproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `admin_name`) VALUES
(1, 'admin', '$2y$10$jssw28vzpyp.O4XfaqUY7eZnFJyea87QjhrJsnC.JsFLG2UrGdyye', 'Administrator'),
(2, 'admin', '$2y$10$CI063fyLHQljxN8wbwa3IuMsdOsaDJWvWxhvdjsvoQ7F.N6ly6vJW', '');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `candidate_id` int(11) NOT NULL,
  `election_type` varchar(50) DEFAULT NULL,
  `roll_no` varchar(50) NOT NULL,
  `img` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `party` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'approved',
  `department` varchar(100) NOT NULL,
  `date_registered` datetime NOT NULL DEFAULT current_timestamp(),
  `id_number` varchar(255) NOT NULL,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`candidate_id`, `election_type`, `roll_no`, `img`, `firstname`, `lastname`, `position`, `grade_level`, `gender`, `party`, `status`, `department`, `date_registered`, `id_number`, `approval_status`) VALUES
(45, 'National', '', '373462247_337228572217562_218000798700424048_n.jpg', 'John Fritz', 'Centeno', 'PRESIDENT', '4th Year', 'Male', 'Kusog sang Pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 20:57:12', '', 'Pending'),
(46, 'National', '', 'justin\'.jpg', 'Justin', 'Salvana', 'VP-INTERNAL', '4th Year', 'Male', 'Kusog sang Pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 20:59:26', '', 'Pending'),
(48, 'National', '', 'justinE.jpg', 'Justin', 'Estrevillo', 'VP-INTERNAL', '3rd Year', 'Male', 'Kusog sang Pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 21:00:51', '', 'Pending'),
(50, 'National', '', 'gabriel.jpg', 'Gabriel', 'Limsiaco', 'VP-EXTERNAL', '4th Year', 'Male', 'Kusog sang pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 21:02:24', '', 'Pending'),
(51, 'National', '', 'carl.jpg', 'Carl luis', 'Gonzales', 'VP-EXTERNAL', '4th Year', 'Male', 'Kusog sang pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 21:03:42', '', 'Pending'),
(52, 'Local', '', 'jooseph.jpg', 'Joseph', 'Detoyato', 'Chairman', '4th Year', 'Male', 'Kusog sang pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 21:08:38', '', 'Pending'),
(54, 'Local', '', 'Eduardo.jpg', 'Eduardo', 'Corcino', 'Chairman', '3rd Year', 'Male', 'Student Democratic Alliance (SDA)', 'approved', 'College Of Criminal Justice Education', '2025-10-28 21:14:28', '', 'Pending'),
(55, 'Local', '', 'ashley.jpg', 'Ashley', 'Gayares', 'Vice Chairman', '4th Year', 'Female', 'Kusog sang pamatan-on (KSP)', 'approved', 'College Of Criminal Justice Education', '2025-10-28 21:16:04', '', 'Pending'),
(56, 'Local', '', 'fenny.jpg', 'Fenny', 'Paguntalan', 'Secretary', '4th Year', 'Female', 'Kusog sang pamatan-on (KSP)', 'approved', 'College Of Criminal Justice Education', '2025-10-28 21:18:46', '', 'Pending'),
(57, 'Local', '', 'amelia.jpg', 'Amela', 'Cataluna', 'Secretary', '4th Year', 'Female', 'Student Democratic Alliance (SDA)', 'approved', 'College of Information Technology', '2025-10-28 21:20:32', '', 'Pending'),
(58, 'Local', '', 'janreb.jpg', 'Janreb', 'Piad', 'Vice Chairman', '4th Year', 'Male', 'Kusog sang pamatan-on (KSP)', 'approved', 'College of Information Technology', '2025-10-28 21:21:23', '', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `voting_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `voting_status`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `voter_id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `program` varchar(100) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `account` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'voter',
  `validation_status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`voter_id`, `id_number`, `email`, `name`, `firstname`, `lastname`, `program`, `year_level`, `status`, `account`, `date`, `role`, `validation_status`) VALUES
(81, '04-2223-030959', 'jusu.salvana.ui@phinmaed.com', 'Justin Paolo Subang Salva�a', 'Justin', 'Paolo Subang Salva�a', 'College Of Criminal Justice Education', '4', 1, 'Voter', '2025-10-28', 'voter', 'accepted'),
(82, '15', 'krpa.huerto.ui@phinmaed.com', 'KRYZ DANIEL PACIENTE HUERTO', 'KRYZ', 'DANIEL PACIENTE HUERTO', 'College of Information Technology', '3', 0, 'Voter', '2025-10-28', 'voter', 'accepted'),
(83, '04-2223-030959', 'jode.centeno.ui@phinmaed.com', 'JOHN FRITZ DE LA CRUZ CENTENO', 'JOHN', 'FRITZ DE LA CRUZ CENTENO', 'College of Information Technology', '4', 1, 'Voter', '2025-10-28', 'voter', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `vote_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `candidate_id`, `voter_id`, `vote_time`) VALUES
(230, 45, 81, '2025-10-28 15:17:00'),
(231, 51, 81, '2025-10-28 15:17:00'),
(232, 48, 81, '2025-10-28 15:17:00'),
(233, 54, 81, '2025-10-28 15:17:00'),
(234, 56, 81, '2025-10-28 15:17:00'),
(235, 55, 81, '2025-10-28 15:17:00'),
(236, 45, 83, '2025-10-28 15:17:14'),
(237, 51, 83, '2025-10-28 15:17:14'),
(238, 48, 83, '2025-10-28 15:17:14'),
(239, 52, 83, '2025-10-28 15:17:14'),
(240, 57, 83, '2025-10-28 15:17:14'),
(241, 58, 83, '2025-10-28 15:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `votes_archive`
--

CREATE TABLE `votes_archive` (
  `vote_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `vote_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes_archive`
--

INSERT INTO `votes_archive` (`vote_id`, `candidate_id`, `voter_id`, `vote_time`) VALUES
(103, 28, 54, '2025-10-08 06:47:20'),
(104, 25, 54, '2025-10-08 06:47:20'),
(105, 26, 54, '2025-10-08 06:47:20'),
(178, 28, 75, '2025-10-28 09:47:09'),
(179, 3, 75, '2025-10-28 09:47:09'),
(180, 25, 75, '2025-10-28 09:47:09'),
(181, 43, 75, '2025-10-28 09:47:09'),
(182, 44, 75, '2025-10-28 09:47:09'),
(206, 45, 83, '2025-10-28 15:03:47'),
(207, 51, 83, '2025-10-28 15:03:47'),
(208, 46, 83, '2025-10-28 15:03:47'),
(209, 52, 83, '2025-10-28 15:03:48'),
(210, 57, 83, '2025-10-28 15:03:48'),
(211, 58, 83, '2025-10-28 15:03:48'),
(212, 45, 81, '2025-10-28 15:12:08'),
(213, 51, 81, '2025-10-28 15:12:08'),
(214, 48, 81, '2025-10-28 15:12:08'),
(215, 54, 81, '2025-10-28 15:12:08'),
(216, 56, 81, '2025-10-28 15:12:08'),
(217, 55, 81, '2025-10-28 15:12:08'),
(218, 45, 81, '2025-10-28 15:14:41'),
(219, 51, 81, '2025-10-28 15:14:41'),
(220, 48, 81, '2025-10-28 15:14:41'),
(221, 54, 81, '2025-10-28 15:14:41'),
(222, 56, 81, '2025-10-28 15:14:41'),
(223, 55, 81, '2025-10-28 15:14:41'),
(224, 45, 81, '2025-10-28 15:15:07'),
(225, 51, 81, '2025-10-28 15:15:07'),
(226, 48, 81, '2025-10-28 15:15:07'),
(227, 54, 81, '2025-10-28 15:15:07'),
(228, 56, 81, '2025-10-28 15:15:07'),
(229, 55, 81, '2025-10-28 15:15:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`candidate_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`voter_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `voter_id` (`voter_id`);

--
-- Indexes for table `votes_archive`
--
ALTER TABLE `votes_archive`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `voter_id` (`voter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `voter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `votes_archive`
--
ALTER TABLE `votes_archive`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidate` (`candidate_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`voter_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
