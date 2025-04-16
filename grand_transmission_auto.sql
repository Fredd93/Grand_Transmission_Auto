-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 06, 2025 at 03:20 PM
-- Server version: 11.7.2-MariaDB-ubu2404
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grand_transmission_auto`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `employee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `engine_spec` varchar(100) DEFAULT NULL,
  `car_condition` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `lease_available` tinyint(1) DEFAULT 0,
  `lease_terms` text DEFAULT NULL,
  `on_sale` enum('yes','no') DEFAULT 'no',
  `discount` decimal(5,2) DEFAULT 0.00,
  `status` enum('sold','available','reserved') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `brand`, `model`, `year`, `transmission`, `engine_spec`, `car_condition`, `description`, `image_path`, `color`, `price`, `lease_available`, `lease_terms`, `on_sale`, `discount`, `status`, `created_at`) VALUES
(1, 'Toyota', 'Corolla', 2002, 'Automatic', '1.8L I4', 'New', 'A reliable sedan with excellent fuel efficiency.', '../../assets/images/Toyota_corolla_2002_front.jpg', 'Silver', 22000.00, 1, '36 months lease with low down payment', 'no', 0.00, 'available', '2021-01-02 09:15:00'),
(2, 'Ford', 'Mustang', 2001, 'Manual', '5.0L V8', 'Used', 'A powerful sports car in great condition.', '../../assets/images/Ford_mustang_2001_front.jpg', 'Red', 35000.00, 0, NULL, 'no', 0.00, 'available', '2021-03-17 09:15:00'),
(3, 'Honda', 'Civic', 2003, 'Automatic', '2.0L I4', 'New', 'A compact car known for efficiency and style.', '../../assets/images/Honda_civic_2003_front.jpeg', 'Silver', 21000.00, 1, '24 months lease available with moderate down payment', 'no', 0.00, 'available', '2021-03-17 09:15:00'),
(4, 'BMW', 'X3', 2022, 'Automatic', '2.0L Turbo I4', 'New', 'A luxury compact SUV offering excellent performance and comfort.', '../../assets/images/BMW-X3-2022-front.jpg', 'Black', 42000.00, 1, '36 months lease with flexible options', 'no', 0.00, 'available', '2025-04-04 13:33:41'),
(5, 'Audi', 'A4', 2021, 'Automatic', '2.0L Turbo I4', 'Used', 'A premium sedan with advanced technology features.', '../../assets/images/Audi-A4-2020-front.jpg', 'Lead', 38000.00, 0, NULL, 'no', 0.00, 'available', '2025-04-04 13:33:41'),
(6, 'Mercedes', 'C-Class', 2020, 'Automatic', '2.0L Turbo I4', 'Used', 'A stylish and sophisticated sedan with a smooth drive.', '../../assets/images/Mercedes-Benz-C-Class-2019-front.jpg', 'Blue', 35000.00, 1, '48 months lease with low interest rates', 'no', 0.00, 'available', '2024-05-20 09:15:00'),
(7, 'Nissan', 'Altima', 2022, 'Automatic', '2.5L I4', 'New', 'A mid-size sedan known for its comfort and safety features.', '../../assets/images/Nissan-Altima-2023-front.jpg', 'White', 25000.00, 0, NULL, 'no', 0.00, 'available', '2025-04-04 13:33:41'),
(8, 'Mitsubishi', 'ASX', 2020, 'Automatic', '2.0L I4', 'Used', 'First car record created mid-March.', '../../assets/images/Mitsubishi-ASX-2020-front.jpg', 'Red', 21000.00, 1, '24 month lease available', 'yes', 7.50, 'available', '2025-03-15 10:00:00'),
(9, 'Dodge', 'Hellcat', 2021, 'Manual', '2.2L I4', 'New', 'Second car record created mid-March.', '../../assets/images/Dodge-Charger_SRT_Hellcat_Redeye-2021-front.jpg', 'White', 25000.00, 0, NULL, 'yes', 5.50, 'reserved', '2025-03-16 11:30:00'),
(10, 'Volkswagen', 'Golf', 2019, 'Automatic', '2.0L I4', 'Used', 'Third car record created mid-March.', '../../assets/images/Volkswagen-Golf_GTI_TCR-2019-front.jpg', 'Red', 19000.00, 1, '36 month lease plan', 'yes', 20.00, 'sold', '2025-03-17 09:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `order_type` enum('purchase','lease') NOT NULL,
  `status` enum('pending','approved','denied','completed') DEFAULT 'pending',
  `down_payment` decimal(10,2) DEFAULT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('employee','manager') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'employee1', '$2y$12$cJSl7I6oK9axeAgVo3cknOooho2j4LFyfPc5UDUSd.l4/t4oA./nW', 'employee', '2025-04-04 13:30:50'),
(2, 'manager1', '$2y$12$.lYZMrasvsHeWvRoyHNeJe8ImNizSACPW.rNH9p2wBayRnmTtQKaK', 'manager', '2025-04-04 13:30:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
