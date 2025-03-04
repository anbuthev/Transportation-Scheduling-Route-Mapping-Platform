-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2025 at 06:07 PM
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
-- Database: `user_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `crews`
--

CREATE TABLE `crews` (
  `id` int(11) NOT NULL,
  `crew_name` varchar(255) NOT NULL,
  `crew_member1` varchar(100) DEFAULT NULL,
  `crew_member2` varchar(100) DEFAULT NULL,
  `crew_member1_role` varchar(100) DEFAULT NULL,
  `crew_member2_role` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crews`
--

INSERT INTO `crews` (`id`, `crew_name`, `crew_member1`, `crew_member2`, `crew_member1_role`, `crew_member2_role`) VALUES
(14, 'monday', '7', '8', '0', '0'),
(15, 'tuesday', '9', '10', '0', '0'),
(16, 'wednesday', '11', '12', '0', '0'),
(17, 'thursday', '13', '14', '0', '0'),
(18, 'friday', '15', '16', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `duty_assignments`
--

CREATE TABLE `duty_assignments` (
  `id` int(11) NOT NULL,
  `crew_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `trip_type` enum('up','down') NOT NULL,
  `trip_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `duty_assignments`
--

INSERT INTO `duty_assignments` (`id`, `crew_id`, `vehicle_id`, `route_id`, `start_time`, `end_time`, `trip_type`, `trip_number`) VALUES
(17, 14, 1, 30, '2025-03-01 06:15:00', '2025-03-01 07:05:00', 'up', 1),
(18, 14, 1, 30, '2025-03-01 08:00:00', '2025-02-28 08:50:00', 'down', 1),
(19, 15, 2, 37, '2025-03-01 09:05:00', '2025-03-01 11:05:00', 'up', 1),
(20, 15, 2, 37, '2025-03-01 12:05:00', '2025-03-01 14:05:00', 'down', 1),
(21, 16, 3, 35, '2025-03-01 11:20:00', '2025-03-01 12:06:00', 'up', 1),
(22, 16, 3, 35, '2025-03-01 12:30:00', '2025-02-28 13:06:00', 'down', 1),
(23, 17, 4, 34, '2025-03-01 16:05:00', '2025-03-01 17:15:00', 'up', 1),
(24, 17, 4, 34, '2025-03-01 17:45:00', '2025-03-01 19:05:00', 'down', 1),
(25, 18, 5, 36, '2025-03-01 20:05:00', '2025-03-01 20:55:00', 'up', 1),
(26, 18, 5, 30, '2025-03-01 21:15:00', '2025-03-01 22:10:00', 'down', 1);

-- --------------------------------------------------------

--
-- Table structure for table `intermediate_stops`
--

CREATE TABLE `intermediate_stops` (
  `stop_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `stop_name` varchar(255) NOT NULL,
  `stop_latitude` decimal(10,6) NOT NULL,
  `stop_longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intermediate_stops`
--

INSERT INTO `intermediate_stops` (`stop_id`, `route_id`, `stop_name`, `stop_latitude`, `stop_longitude`) VALUES
(42, 30, 'Saravanampatti', 11.078425, 77.003703),
(43, 30, 'Ganapathy', 11.035957, 76.978253),
(44, 31, 'P.N Palayam', 11.152182, 76.944529),
(45, 31, 'Thudiyalur', 11.079755, 76.941663),
(46, 31, 'New B.S', 11.030593, 76.951134),
(47, 32, 'Singanallur', 11.002835, 77.029596),
(48, 32, 'Sulur', 11.028678, 77.129589),
(49, 33, 'Gandhi park', 11.000797, 76.948494),
(50, 33, 'Vadavalli', 11.026835, 76.905515),
(51, 34, 'Ukkadam', 10.988672, 76.961664),
(52, 34, 'Kuniyamuthur', 10.956510, 76.953979),
(53, 34, 'Madhukarai', 10.914570, 76.948777),
(54, 35, 'Saravanampatti', 11.078426, 77.003702),
(55, 35, 'Ganapathy', 11.035960, 76.978251),
(56, 36, 'Karumathampatti', 11.107190, 77.176750),
(57, 36, 'Neelambur', 11.060768, 77.085561),
(58, 37, 'Coonoor', 11.344105, 76.791520),
(59, 37, 'Aruvankadu', 11.367006, 76.766517),
(60, 38, 'Karamadai', 11.242136, 76.959089),
(61, 38, 'Tholampalayam', 11.181223, 76.834361),
(62, 39, 'Nalroad', 11.443594, 77.146348),
(63, 39, 'Sirumugai', 11.321402, 77.008600);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_number` varchar(50) NOT NULL,
  `start_location` varchar(255) NOT NULL,
  `start_latitude` decimal(10,6) NOT NULL,
  `start_longitude` decimal(10,6) NOT NULL,
  `end_location` varchar(255) NOT NULL,
  `end_latitude` decimal(10,6) NOT NULL,
  `end_longitude` decimal(10,6) NOT NULL,
  `total_distance` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_number`, `start_location`, `start_latitude`, `start_longitude`, `end_location`, `end_latitude`, `end_longitude`, `total_distance`) VALUES
(30, '111', 'Thudiyalur', 11.080484, 76.942078, 'Gandhipuram', 11.016452, 76.968981, 16.66),
(31, '102', 'Karamadai', 11.239048, 76.960229, 'Gandhipuram', 11.016391, 76.969007, 29.26),
(32, '105', 'Ukkadam', 10.988672, 76.961664, 'Karanampettai', 11.017478, 77.184266, 26.90),
(33, '64', 'Gandhipuram', 11.016424, 76.968989, 'Thondamuthur', 10.991390, 76.841853, 19.59),
(34, '96', 'Gandhipuram', 11.016457, 76.968981, 'Walayar', 10.843314, 76.838998, 29.34),
(35, '45', 'Annur', 11.233917, 77.102306, 'Gandhipuram', 11.016452, 76.968981, 30.11),
(36, '65', 'Avinashi', 11.187707, 77.278248, 'Gandhipuram', 11.016457, 76.968981, 42.24),
(37, '100', 'Mettupalayam', 11.302842, 76.938125, 'Ooty', 11.404326, 76.696734, 50.44),
(38, '37', 'Mettupalayam', 11.302847, 76.938125, 'Anaikatti', 11.116886, 76.746670, 39.98),
(39, '48', 'Sathyamangalam', 11.498635, 77.244837, 'Mettupalayam', 11.302842, 76.938125, 45.03);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('manager','driver','conductor','otheruser') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `id_proof_number` varchar(50) NOT NULL,
  `experience` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `first_name`, `last_name`, `phone_no`, `id_proof_number`, `experience`, `created_at`) VALUES
(3, 'admin', 'admin@example.com', '$2y$10$dcjZfQN.5ZuMgvF00cJuWO.R8z3B5biJL.pkfrb0RutdUSecqjSce', 'manager', 'Gopi', 'Nath', '7654321098', '6398-5874-6521', 3, '2025-02-19 17:14:04'),
(7, 'user1', 'user1@example.com', '$2y$10$tHB1iXEpGlObqWOplaAebOwnR28qj.doI53F.a.KAICvTa5W.FwWq', 'driver', 'suresh', 'kumar', '9632147852', '2145-8569-7430', 5, '2025-02-28 05:53:58'),
(8, 'user2', 'user2@example.com', '$2y$10$x0n8weukRyjdLQB2wRuk1eDglktvnMelolkiR0R7TX8MpRRasDFhG', 'conductor', 'Rama', 'chandran', '6325874125', '3214-8745-6523', 8, '2025-02-28 05:56:28'),
(9, 'user3', 'user3@example.com', '$2y$10$13zq9NX7iEp4VJj5fJLib.mlGj/NYGpEubx4ySKpTiPge3q7I9vsC', 'driver', 'kalai', 'selvi', '6325552103', '3320-9630-2541', 4, '2025-02-28 05:57:40'),
(10, 'user4', 'user4@example.com', '$2y$10$Mw0Fi2hUuuDDEAyvN9fV5u7tlVFtOJDG.05l8LlZk01G89RT9.dlq', 'conductor', 'vimal', 'pandy', '7412589630', '2221-2458-6325', 7, '2025-02-28 05:59:10'),
(11, 'user5', 'user5@example.com', '$2y$10$f/EOL7aQ6OXc8XR/wnEVkuSpr/NgANZmQFSBX/sFdT4dXhwDgqqC.', 'driver', 'arul', 'vadivu', '6325874169', '6523-8965-2549', 6, '2025-02-28 06:01:49'),
(12, 'user6', 'user6@example.com', '$2y$10$R1S7XgejoYMxv3OxWIkneuHfPfWqE.8z4Vo1W.4YFdGKEK69bjXxe', 'conductor', 'senbaka', 'moorthy', '9003256874', '6542-8301-9547', 11, '2025-02-28 06:06:10'),
(13, 'user7', 'user7@example.com', '$2y$10$1BPkGNRChhqDC0Yi9PGvKe.TSQdnEeuyFua5v0NDi4PbFgZSQaOk2', 'driver', 'vivek', 'anand', '7458210365', '5554-6321-2014', 9, '2025-02-28 06:08:28'),
(14, 'user8', 'user8@example.com', '$2y$10$0J/TxIIR0vYG2ECLtERJv.4o/mARmPwHN4RJOce6WO3kOOYI7uyDe', 'conductor', 'mathan', 'adhithiya', '9510234796', '3330-2145-3015', 1, '2025-02-28 06:10:27'),
(15, 'user9', 'user9@example.com', '$2y$10$ZKrhuXU7yRFhtcNAWSBoeec0I5wqJ3OCsgEpT8gJZoONB1TbEeGOO', 'driver', 'aravind', 'das', '6324852014', '0003-6521-8883', 8, '2025-02-28 06:13:13'),
(16, 'user10', 'user10@example.com', '$2y$10$C32jwQjBOFXClCi7rDg0Ces87NaG2qIRHaf3zV6I8ut1HipH5TYzO', 'conductor', 'silam', 'barasan', '6300186002', '4446-8878-6999', 2, '2025-02-28 06:14:20'),
(17, 'user11', 'user11@example.com', '$2y$10$DFIdZ.vvCitSkkkCN0w2A.p4DkxPu6eNs2boPCZU7LY8TcH1b9oQK', 'otheruser', 'subash', 'mandella', '7421008547', '9658-7420-6665', 0, '2025-02-28 06:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `make_year` year(4) NOT NULL,
  `model_type` varchar(50) NOT NULL,
  `due_for_next_service` date NOT NULL,
  `due_for_insurance` date NOT NULL,
  `due_for_fc` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_number`, `capacity`, `make_year`, `model_type`, `due_for_next_service`, `due_for_insurance`, `due_for_fc`, `created_at`) VALUES
(1, 'TN-01-AB-1234', 50, '2020', 'BS6', '2025-06-15', '2025-05-01', '2025-07-16', '2025-02-19 17:16:27'),
(2, 'KA-05-CD-5678', 40, '2018', 'BS4', '2024-11-10', '2024-09-25', '2025-12-01', '2025-02-19 17:16:27'),
(3, 'MH-12-EF-9101', 30, '2019', 'BS4', '2025-01-05', '2025-02-15', '2025-08-10', '2025-02-19 17:16:27'),
(4, 'TN-58-CD-4589', 70, '2024', 'BS6', '2025-02-20', '2025-03-20', '2025-10-15', '2025-02-19 17:20:39'),
(5, 'KL-40-SM-2416', 60, '2019', 'BS6', '2025-03-30', '2025-04-15', '2025-05-05', '2025-02-28 06:21:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crews`
--
ALTER TABLE `crews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `duty_assignments`
--
ALTER TABLE `duty_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crew_id` (`crew_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `intermediate_stops`
--
ALTER TABLE `intermediate_stops`
  ADD PRIMARY KEY (`stop_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD UNIQUE KEY `route_number` (`route_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_no` (`phone_no`),
  ADD UNIQUE KEY `id_proof_number` (`id_proof_number`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crews`
--
ALTER TABLE `crews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `duty_assignments`
--
ALTER TABLE `duty_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `intermediate_stops`
--
ALTER TABLE `intermediate_stops`
  MODIFY `stop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `duty_assignments`
--
ALTER TABLE `duty_assignments`
  ADD CONSTRAINT `duty_assignments_ibfk_1` FOREIGN KEY (`crew_id`) REFERENCES `crews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_assignments_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_assignments_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE;

--
-- Constraints for table `intermediate_stops`
--
ALTER TABLE `intermediate_stops`
  ADD CONSTRAINT `intermediate_stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
