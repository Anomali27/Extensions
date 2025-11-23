-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 23, 2025 at 12:12 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `playstation-biling`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `quantity_available` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `type`, `quantity_available`) VALUES
(1, 'PS4', 10),
(2, 'PS5', 10),
(3, 'VR', 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `start_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration` int NOT NULL COMMENT 'Durasi dalam menit',
  `package` varchar(100) DEFAULT 'Custom',
  `price` bigint NOT NULL,
  `status` enum('pending','active','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `room_id`, `start_date`, `start_time`, `duration`, `package`, `price`, `status`, `created_at`) VALUES
(29, 33, 8, '2025-11-23', '00:00:00', 720, 'VIP', 90000, 'completed', '2025-11-22 15:20:14'),
(30, 33, 5, '2025-11-22', '13:00:00', 360, 'Full', 45000, 'completed', '2025-11-22 15:21:09'),
(31, 33, 9, '2025-11-23', '17:00:00', 120, 'Reguler', 10000, 'active', '2025-11-23 06:38:57'),
(32, 33, 10, '2025-11-23', '02:00:00', 180, 'Hemat', 25000, 'completed', '2025-11-23 08:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `user_id`, `order_id`, `amount`, `description`, `created_at`) VALUES
(1, 33, 29, '-90000.00', 'Pembayaran booking untuk room ID 8', '2025-11-22 15:20:14'),
(2, 33, 30, '-45000.00', 'Pembayaran booking untuk room ID 5', '2025-11-22 15:21:09'),
(3, 33, 31, '-10000.00', 'Pembayaran booking untuk room ID 9', '2025-11-23 06:38:57'),
(4, 33, 32, '-25000.00', 'Pembayaran booking untuk room ID 10', '2025-11-23 08:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('PS5','PS4','VR') NOT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `type`, `status`, `created_at`) VALUES
(1, 'PS5 Room 1', 'PS5', 'available', '2025-11-04 14:52:26'),
(2, 'PS5 Room 2', 'PS5', 'available', '2025-11-04 14:52:26'),
(3, 'PS5 Room 3', 'PS5', 'available', '2025-11-04 14:52:26'),
(4, 'PS5 Room 4', 'PS5', 'available', '2025-11-04 14:52:26'),
(5, 'PS5 Room 5', 'PS5', 'available', '2025-11-04 14:52:26'),
(6, 'PS5 Room 6', 'PS5', 'booked', '2025-11-04 14:52:26'),
(7, 'PS5 Room 7', 'PS5', 'booked', '2025-11-04 14:52:26'),
(8, 'PS5 Room 8', 'PS5', 'available', '2025-11-04 14:52:26'),
(9, 'PS5 Room 9', 'PS5', 'available', '2025-11-04 14:52:26'),
(10, 'PS5 Room 10', 'PS5', 'available', '2025-11-04 14:52:26'),
(11, 'PS5 Room 11', 'PS5', 'available', '2025-11-04 14:52:26'),
(12, 'PS5 Room 12', 'PS5', 'available', '2025-11-04 14:52:26'),
(13, 'PS5 Room 13', 'PS5', 'available', '2025-11-04 14:52:26'),
(14, 'PS5 Room 14', 'PS5', 'available', '2025-11-04 14:52:26'),
(15, 'PS5 Room 15', 'PS5', 'available', '2025-11-04 14:52:26'),
(16, 'PS4 Room 1', 'PS4', 'available', '2025-11-04 14:52:26'),
(17, 'PS4 Room 2', 'PS4', 'available', '2025-11-04 14:52:26'),
(18, 'PS4 Room 3', 'PS4', 'available', '2025-11-04 14:52:26'),
(19, 'PS4 Room 4', 'PS4', 'available', '2025-11-04 14:52:26'),
(20, 'PS4 Room 5', 'PS4', 'available', '2025-11-04 14:52:26'),
(21, 'PS4 Room 6', 'PS4', 'available', '2025-11-04 14:52:26'),
(22, 'PS4 Room 7', 'PS4', 'available', '2025-11-04 14:52:26'),
(23, 'PS4 Room 8', 'PS4', 'available', '2025-11-04 14:52:26'),
(24, 'PS4 Room 9', 'PS4', 'available', '2025-11-04 14:52:26'),
(25, 'PS4 Room 10', 'PS4', 'available', '2025-11-04 14:52:26'),
(26, 'VR Room 1', 'VR', 'available', '2025-11-04 14:52:26'),
(27, 'VR Room 2', 'VR', 'available', '2025-11-04 14:52:26'),
(28, 'VR Room 3', 'VR', 'available', '2025-11-04 14:52:26'),
(29, 'VR Room 4', 'VR', 'available', '2025-11-04 14:52:26'),
(30, 'VR Room 5', 'VR', 'available', '2025-11-04 14:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `topup_history`
--

CREATE TABLE `topup_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` int NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `topup_history`
--

INSERT INTO `topup_history` (`id`, `user_id`, `amount`, `method`, `status`, `created_at`) VALUES
(1, 33, 50000, 'Dana', 'success', '2025-11-20 04:09:20'),
(2, 33, 100000, 'QRIS', 'success', '2025-11-20 04:12:51'),
(3, 25, 500000, 'QRIS', 'success', '2025-11-20 06:12:56'),
(4, 25, 50000, 'OVO', 'success', '2025-11-21 08:56:05'),
(5, 33, 5000, 'Dana', 'success', '2025-11-22 15:20:40'),
(6, 33, 50000, 'GoPay', 'success', '2025-11-23 08:34:47'),
(7, 25, 10000, 'Dana', 'success', '2025-11-23 10:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('online','offline') DEFAULT 'offline',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `saldo` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`, `saldo`) VALUES
(25, 'ADMIN WEB EXTENSION', 'Edward@gmail.com', '$2y$10$NWA5QOk9VgCQxq/1py3U7.zSYbB5ReoWcADemuhqON2T6BKh1E6IO', 'admin', 'offline', '2025-11-04 13:40:18', 560000),
(31, 'Silvio', 'silvio@gmail.com', '$2y$10$pnV2u3qZGiakD038cN0ApOOUwulfn.CZQmYt9hIYYcXr.XXaw/JxS', 'user', 'offline', '2025-11-12 02:04:35', 0),
(33, 'achai12', 'achai@gmail.com', '$2y$10$K0NVd5cfXuBp//DOeojcLe1GTadsPwURKMqOJ2VfldAPNmCcaHEd.', 'user', 'offline', '2025-11-20 01:13:39', 35000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topup_history`
--
ALTER TABLE `topup_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_topup_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `topup_history`
--
ALTER TABLE `topup_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payment_history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `topup_history`
--
ALTER TABLE `topup_history`
  ADD CONSTRAINT `fk_topup_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
