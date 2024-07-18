-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2024 at 09:23 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `published_date` date DEFAULT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `shelf_id` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `genre`, `published_date`, `isbn`, `available`, `shelf_id`, `year`, `image_path`) VALUES
(9, 'Harry Potter', 'J.K Rowling', 'Fiction', '1998-12-11', '27991647731', 1, 1, 1998, 'uploads/books/images (1).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `user_id`, `amount`, `description`, `created_at`) VALUES
(1, 8, '5.00', 'Cancellation fee for reservation within 1 day', '2024-07-16 19:14:14');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `borrowed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `returned_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `penalty_type_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penalties`
--

INSERT INTO `penalties` (`id`, `user_id`, `penalty_type_id`, `amount`, `reason`, `paid`, `created_at`) VALUES
(1, 8, 2, '0.00', 'hilang', 1, '2024-06-23 16:51:05'),
(3, 8, 2, '99.00', '7 Hari Mencintaimu', 0, '2024-06-24 17:51:28'),
(4, 8, 4, '1.00', 'late', 1, '2024-06-24 17:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `penalty_settings`
--

CREATE TABLE `penalty_settings` (
  `id` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `duration_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penalty_settings`
--

INSERT INTO `penalty_settings` (`id`, `price_per_day`, `duration_days`) VALUES
(1, '0.40', 14);

-- --------------------------------------------------------

--
-- Table structure for table `penalty_types`
--

CREATE TABLE `penalty_types` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penalty_types`
--

INSERT INTO `penalty_types` (`id`, `type`, `amount`, `name`) VALUES
(4, '', '0.60', 'Late Return');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `reserve_date` date NOT NULL,
  `status` enum('reserved','cancelled','fulfilled') DEFAULT 'reserved',
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reserve`
--

CREATE TABLE `reserve` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `reserve_date` date NOT NULL,
  `status` enum('reserved','cancelled','fulfilled') DEFAULT 'reserved',
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shelves`
--

CREATE TABLE `shelves` (
  `id` int(11) NOT NULL,
  `shelf_number` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelves`
--

INSERT INTO `shelves` (`id`, `shelf_number`, `location`) VALUES
(1, '1A', 'First Floor'),
(2, '1B', 'First Floor'),
(3, '3A', 'First Floor'),
(4, '1C', 'First Floor');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(6, 'Churchill', 'churchill@email.com', '$2y$10$MQUXkwCl0TEeM3CP2enKCe9882WDkKBtVoNP8qPorCong8Yzjp6jy', '2024-06-15 16:53:51'),
(7, 'Ekay', 'ekay@gmail.com', '$2y$10$2hsV1pjcM/SpKauZ8Z7.segAX5J.QubuMipKb24Wxp47GRFy49uFe', '2024-06-15 16:57:56'),
(8, 'Una', 'una@email.com', '$2y$10$dAvece/gFjCJ.0hGfTUuIeQgPs9eYgNGyfEIxdeF9DKEDmEAhnhme', '2024-06-21 19:56:48'),
(9, 'Hunaizi', 'hunaizi@gmail.com', '$2y$10$.kY6qQSeH1NB.no.PBSoJ./VC9VE7t6XoZuCc2IMg2naV8eoVlXHm', '2024-07-03 04:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `is_admin` tinyint(1) DEFAULT 0,
  `is_staff` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `banned` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `user_id` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `approved`, `is_admin`, `is_staff`, `created_at`, `banned`, `reset_token`, `user_id`) VALUES
(2, 'admin', 'admin@example.com', '$2y$10$9Ya.V1QnO6mlCxm3mqwBLuErKjWwxwjsmBYNNdhRYPrweChc7sqTG', 1, 1, 0, '2024-06-12 17:39:33', 0, NULL, 'admin'),
(3, 'Aiman', 'aiman@email.com', '$2y$10$SIVEJOvBHEG9uF4Zq58UE.eM4Z6JD7bOa0eQzUIa8rbxcgjA2GxCC', 1, 0, 0, '2024-06-12 18:11:23', 0, NULL, '7790427'),
(5, 'Jason', 'jason@email.com', '$2y$10$4hREKdTqiDjzcJf1sSERieaJ1B11AF.zjzkwBYxhBkAN32Y7.Ip.y', 1, 0, 0, '2024-06-14 18:48:40', 1, NULL, ''),
(8, 'Fakhrul Iqbal', 'iqbal@email.com', '$2y$10$19695CW1TjGlt92ZgYWC4eZeUsxRE9ViicQm2y2w9m4dVsqib/l4e', 1, 0, 0, '2024-06-21 20:01:22', 0, NULL, '6743823'),
(9, 'Desmond', 'desmondd@gmail.com', '$2y$10$SY/vHLwGb2MrUWXH5Ngt9ueoZe1bBXfdrZvFNDx3mEs08D1tKna/S', 1, 0, 0, '2024-06-24 20:40:47', 0, NULL, '8854803'),
(10, 'Simon Sim', 'sims@gmail.com', '$2y$10$ssak1TqWlnLvDcjqswFT5eV3IjE2QPt5MTuSPR2EfSnK0vbO81SLO', 1, 0, 0, '2024-06-24 20:44:30', 0, NULL, '7390004'),
(12, 'Hillary Harry', 'harryh@gmail.com', '$2y$10$u/kkuqoGizgJ22ViITKROesFLj/mli2bnLmywyN6xlMufkQ2n6nEm', 1, 0, 0, '2024-06-24 20:46:17', 0, NULL, '0934992'),
(13, 'Zakirin Zahid', 'zzahid@hotmail.com', '$2y$10$5L8g7F11GPLLzOLZ1bgSoutxXnX7Z9ZdfAIJaa/pcdg3Onj6/Facy', 0, 0, 0, '2024-06-24 20:48:33', 0, NULL, '4673086'),
(14, 'Hassan Husni', 'husnih@yahoo.com', '$2y$10$ApWwbm7JOTa8SzyAT7sR8.fT6.he3FxVbFWWDtRyBV3jau4yzYu6C', 1, 0, 0, '2024-06-24 20:50:25', 0, NULL, '7673799'),
(15, 'Saging', 'sagging@gmail.com', '$2y$10$zfhgjGgBKt2Tm9HYK2TZ8ueZfEG9/cULzMIK0gEWz6YeuR/jX3tJO', 1, 0, 0, '2024-07-03 04:05:24', 0, NULL, '1932657');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `penalty_settings`
--
ALTER TABLE `penalty_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penalty_types`
--
ALTER TABLE `penalty_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `reserve`
--
ALTER TABLE `reserve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `shelves`
--
ALTER TABLE `shelves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penalty_settings`
--
ALTER TABLE `penalty_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penalty_types`
--
ALTER TABLE `penalty_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reserve`
--
ALTER TABLE `reserve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shelves`
--
ALTER TABLE `shelves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `penalties`
--
ALTER TABLE `penalties`
  ADD CONSTRAINT `penalties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `reserve`
--
ALTER TABLE `reserve`
  ADD CONSTRAINT `reserve_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reserve_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
