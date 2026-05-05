-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 12:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `terra_fusion`
--

-- --------------------------------------------------------

--
-- Table structure for table `book_a_table`
--

CREATE TABLE `book_a_table` (
  `booking_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_number` varchar(20) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `number_of_people` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `customer_id`, `created_at`) VALUES
(1, 2, '2025-12-13 22:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `meal_id`, `quantity`) VALUES
(12, 1, 4, 1),
(13, 1, 5, 1),
(15, 1, 2, 1),
(21, 1, 8, 3);

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `sender` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL,
  `meal_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(6,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `meal_type` enum('Appetizers','Main Courses','Desserts') NOT NULL,
  `availability` enum('Available','Out of Stock') DEFAULT 'Available',
  `quantity` int(5) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `meal_name`, `description`, `price`, `image`, `created_at`, `meal_type`, `availability`, `quantity`) VALUES
(1, 'Bruschetta Trio', 'Three varieties of fresh bruschetta with tomatoes, basil, and mozzarella', 60.95, 'images/meals-imgs/Bruschetta Trio.jpg', '2025-12-13 18:46:26', 'Appetizers', 'Available', 10),
(2, 'Truffle Arancini', 'Crispy risotto balls with black truffle and parmesan', 55.00, 'images/meals-imgs/Truffle Arancini.jpg', '2025-12-13 18:46:26', 'Appetizers', 'Available', 4),
(3, 'Lasagna', 'Layers of tender pasta sheets, seasoned minced meat, and tomato sauce, topped with creamy béchamel and melted cheese, baked to golden perfection.', 225.50, 'images/meals-imgs/Lasagna.jpg', '2025-12-17 16:55:55', 'Main Courses', 'Available', 9),
(4, 'Truffle Pasta', 'Fettuccine pasta in a creamy sauce infused with aromatic truffle oil, with parmesan cheese and a touch of black pepper.', 280.00, 'images/meals-imgs/Truffle Pasta.jpg', '2025-12-13 18:46:26', 'Main Courses', 'Available', 12),
(5, 'Tiramisu', 'Classic Italian dessert made with layers of coffee-soaked ladyfingers and creamy mascarpone cheese, dusted with rich cocoa powder.', 110.50, 'images/meals-imgs/Tiramisu.jpg', '2025-12-13 18:46:26', 'Desserts', 'Available', 12),
(6, 'Chicken Alfredo Pasta', 'Grilled chicken breast tossed in a rich, creamy Alfredo sauce, blended with butter, garlic, and parmesan cheese.', 250.00, 'images/meals-imgs/Chicken Alfredo Pasta.jpg', '2025-12-17 15:18:17', 'Main Courses', 'Available', 12),
(7, 'Tomato Sauce Pasta', 'Penne pasta tossed in a fresh, slow-simmered tomato sauce infused with garlic, olive oil, aromatic herbs, and grated parmesan.', 235.00, 'images/meals-imgs/Tomato Sauce Pasta.jpg', '2025-12-17 15:18:17', 'Main Courses', 'Available', 12),
(8, 'Samosa', 'Golden pastry filled with a flavorful mixture of minced meat and aromatic herbs.', 42.00, 'images/meals-imgs/Samosa.jpg', '2025-12-17 16:51:33', 'Appetizers', 'Available', 12),
(9, 'Grilled Salmon Delight', 'Perfectly grilled salmon fillet seasoned with herbs and spices, served juicy and tender with a light lemon butter glaze', 340.99, 'images/meals-imgs/Grilled Salmon Delight.jpg', '2025-12-13 18:46:26', 'Main Courses', 'Available', 12),
(10, 'Mahshi', 'Grape leaves stuffed with a flavorful blend of rice, herbs, and aromatic spices, slowly cooked until perfectly soft and served with a fresh taste.', 200.45, 'images/meals-imgs/mahshi.jpg', '2025-12-17 17:02:12', 'Main Courses', 'Available', 10),
(11, 'Tres Leches Cake', 'Light, fluffy sponge cake soaked in a rich blend of three types of milks, topped with airy whipped cream for a moist and strawberry.', 90.75, 'images/meals-imgs/Tres Leches Cake.jpg', '2025-12-17 17:06:25', 'Desserts', 'Available', 10),
(12, 'Mini Lemon Cheesecakes', 'Creamy cheesecakes with a zesty lemon flavor, set on a buttery graham cracker crust and topped with a light lemon glaze and a lemon slice.', 60.00, 'images/meals-imgs/Mini Lemon Cheesecakes.jpg', '2025-12-17 17:11:46', 'Desserts', 'Available', 10),
(13, 'Fruit Tart', 'A buttery, golden tart crust filled with smooth vanilla pastry cream and topped with a colorful selection of fresh strawberries, lightly glazed for a beautiful shine.', 50.00, 'images/meals-imgs/1766426498_Fruit_Tart.jpg', '2025-12-22 18:01:38', 'Desserts', 'Available', 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `table_number` int(5) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('New','Preparing','Ready','Served','Paid') DEFAULT 'New',
  `order_date` datetime DEFAULT current_timestamp(),
  `served_by_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `table_number`, `total_amount`, `status`, `order_date`, `served_by_fk`) VALUES
(1, NULL, NULL, 0.00, 'New', '2025-12-22 18:54:56', 8),
(2, NULL, NULL, 0.00, 'New', '2025-12-22 18:57:19', 9),
(3, NULL, NULL, 0.00, 'New', '2025-12-22 18:57:47', 10),
(4, NULL, NULL, 0.00, 'New', '2025-12-22 18:59:04', 11),
(5, NULL, NULL, 0.00, 'New', '2025-12-22 19:09:52', 2),
(6, NULL, NULL, 0.00, 'New', '2025-12-22 20:16:02', 3),
(7, 'salma ahmed', 0, 72.05, 'Ready', '2025-12-22 20:16:48', 3);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `order_fk` int(11) NOT NULL,
  `item_fk` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`detail_id`, `order_fk`, `item_fk`, `quantity`, `price_at_sale`) VALUES
(2, 7, 1, 1, 60.95);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `party_size` int(3) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `status` enum('Confirmed','Seated','Cancelled') DEFAULT 'Confirmed',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `role` enum('Manager','Chef Boss','Table Manager','Waiter','Customer') NOT NULL DEFAULT 'Customer',
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `phone`, `email`, `password_hash`, `created_at`, `profile_pic`, `role`, `last_login`, `is_active`) VALUES
(2, 'jana nasr', 'jana', '01551826991', 'jana@gmail.com', '$2y$10$ONWgrdpSmezCKIfPDISobuHk4q7apammMPTZgGanGyLcLRHG8x5H2', '2025-12-13 13:58:22', NULL, 'Customer', NULL, 1),
(3, 'salma ahmed', 'salma', '01551826990', 'salma@gmail.com', '$2y$10$W9pa.AJ1jUdEECb7dVu6e.RoOlqISIUqy.DvXcJiA9ednEKtsvmtK', '2025-12-13 16:35:38', NULL, 'Customer', NULL, 1),
(8, 'manager', '', '01551826993', 'manager@gmail.com', '$2y$10$0ff3nGnOUUm.zf2eO7x0Qecl4tLH.JsFjjFcQEtAfmD1oMc6d5A8G', '2025-12-22 16:54:56', NULL, 'Manager', NULL, 1),
(9, 'waiter', '', '01551826995', 'waiter@gmail.com', '$2y$10$dgNZiYO781IRrKk0Sl8HteFbeYVQHoHq7g.4DoxQM4d01q5crGY6K', '2025-12-22 16:57:19', NULL, 'Waiter', NULL, 1),
(10, 'chef', '', '01551826994', 'chef@gmail.com', '$2y$10$iEYWbmvR2QYfjoKYC91gROP5sRoHfmNdawd6PGRT5XDXIINitKCu6', '2025-12-22 16:57:47', NULL, 'Chef Boss', NULL, 1),
(11, 'table manager', '', '01551826996', 'tm@gmail.com', '$2y$10$kCZuf53FOAd68DZMVh7sRODVBNBrcMHvVrOkLN12fhYPS1sDJJHUe', '2025-12-22 16:59:04', NULL, 'Table Manager', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book_a_table`
--
ALTER TABLE `book_a_table`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_customer` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `cart_id` (`cart_id`,`meal_id`),
  ADD KEY `fk_meal` (`meal_id`);

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`meal_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `served_by_fk` (`served_by_fk`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_fk` (`order_fk`),
  ADD KEY `item_fk` (`item_fk`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_a_table`
--
ALTER TABLE `book_a_table`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_meal` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`served_by_fk`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_fk`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`item_fk`) REFERENCES `meals` (`meal_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
