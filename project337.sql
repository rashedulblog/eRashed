-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2026 at 07:25 PM
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
-- Database: `project337`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Electronics', '2026-07-14 17:08:55'),
(2, 'Cloths', '2026-07-14 17:10:21'),
(3, 'Toys', '2026-07-14 17:10:33'),
(4, 'Shoes', '2026-07-14 17:27:36'),
(8, 'Caps', '2026-07-18 17:19:06'),
(9, 'Foods', '2026-07-18 17:19:10'),
(10, 'Baby', '2026-07-18 17:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` varchar(30) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `name`, `email`, `phone`, `amount`, `address`, `status`, `transaction_id`, `currency`, `user_id`, `created_at`) VALUES
(12, 'Asif Mohammad Abir', 'asif.abir@hotmail.com', '01955517560', 1310, '9 Sher-E-Bangla Road', 'Shift', 'SSLCZ_TEST_6a7b56e359848', 'BDT', NULL, '2026-08-11 17:07:47'),
(13, 'Asif Mohammad Abir', 'asif.abir@hotmail.com', '01955517560', 60460, '9 Sher-E-Bangla Road', 'Delivered', 'SSLCZ_TEST_6a7b58310ae98', 'BDT', NULL, '2026-08-11 17:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_total` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `product_id`, `quantity`, `item_total`, `order_id`, `created_at`) VALUES
(5, 12, 1, 350, 12, '2026-08-11 17:07:47'),
(6, 11, 2, 900, 12, '2026-08-11 17:07:47'),
(7, 8, 1, 58000, 13, '2026-08-11 17:13:21'),
(8, 10, 2, 900, 13, '2026-08-11 17:13:21'),
(9, 5, 1, 1500, 13, '2026-08-11 17:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `regular_price` int(11) NOT NULL,
  `discount_price` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `regular_price`, `discount_price`, `category_id`, `image`, `created_at`) VALUES
(1, 'Asus 4150', 60000, 55000, 1, '1784653729_6a5fa7a13caec_laptop2.webp', '2026-07-21 17:08:49'),
(2, 'Asus 4150', 60000, 55000, 1, '1784653746_6a5fa7b2ee991_laptop2.webp', '2026-07-21 17:09:06'),
(3, 'Asus 4150', 60000, 55000, 1, '1784653795_6a5fa7e3e3082_laptop2.webp', '2026-07-21 17:09:55'),
(4, 'iPhone 16 pro max', 60000, 50000, 1, '1784653897_6a5fa849601b0_iphone-16-pro-max-256gb-black-titanium.jpeg', '2026-07-21 17:11:37'),
(5, 'Remote toy car', 3000, 1500, 3, '1784653982_6a5fa89ea26e0_toycar.jpg', '2026-07-21 17:13:02'),
(6, 'Polo t-Shirt', 1500, 950, 2, '1784654034_6a5fa8d200b17_t-shirt.jpg', '2026-07-21 17:13:54'),
(7, 'Hp Probbok', 60000, 55000, 1, '1784654073_6a5fa8f953179_laptop4.webp', '2026-07-21 17:14:33'),
(8, 'Dell 2011', 60000, 58000, 1, '1784654099_6a5fa91329c5a_laptop5.webp', '2026-07-21 17:14:59'),
(9, 'Menz Shoes', 2500, 1600, 4, '1784654156_6a5fa94c3b290_juta.webp', '2026-07-21 17:15:56'),
(10, 'Menz Cap', 800, 450, 8, '1784654195_6a5fa973dafbe_images.jpg', '2026-07-21 17:16:35'),
(11, 'Cheese Burger', 600, 450, 9, '1784654287_6a5fa9cf4a955_burger.jpg', '2026-07-21 17:18:07'),
(12, 'Panjabi', 450, 350, 10, '1784654353_6a5faa11e4ace_panjabi.webp', '2026-07-21 17:19:13');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `house_number` varchar(20) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `cus_city` varchar(100) NOT NULL,
  `cus_postcode` varchar(50) NOT NULL,
  `customer_mobile` varchar(20) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `shipping_name` varchar(255) NOT NULL,
  `shipping_house_number` varchar(20) NOT NULL,
  `shipping_street_address` varchar(255) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_zip_code` varchar(50) NOT NULL,
  `shipping_mobile_number` varchar(20) NOT NULL,
  `order_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `customer_name`, `house_number`, `street_address`, `cus_city`, `cus_postcode`, `customer_mobile`, `customer_email`, `shipping_name`, `shipping_house_number`, `shipping_street_address`, `shipping_city`, `shipping_zip_code`, `shipping_mobile_number`, `order_id`, `created_at`) VALUES
(3, 'Asif Mohammad Abir', '9', 'Sher-E-Bangla Road', 'Dhaka', '1209', '01955517560', 'asif.abir@hotmail.com', 'Asif Mohammad Abir', '9', 'Sher-E-Bangla Road', 'Dhaka', '1209', '01955517560', 12, '2026-08-11 17:07:47'),
(4, 'Asif Mohammad Abir', '9', 'Sher-E-Bangla Road', 'Dhaka', '1209', '01955517560', 'asif.abir@hotmail.com', 'Asif Mohammad Abir', '9', 'Sher-E-Bangla Road', 'Dhaka', '1209', '01955517560', 13, '2026-08-11 17:13:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others','') NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` enum('user','admin','','') NOT NULL DEFAULT 'user',
  `created_at` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `gender`, `mobile`, `picture`, `address`, `role`, `created_at`) VALUES
(1, 'Asif Abir', 'asif.abir@hotmail.com', '$2y$10$qRJarAZJ7H76Ozy8qH5Cae6OqIDh/5urSxPNF8hHBk5u1Zbrz6iNm', 'Male', '01955517560', '6a4949314b07e1630831783187761Gemini_Generated_Image_itqbtaitqbtaitqb.png', '9 Sher-E-Bangla RoadrnHazaribagh', 'admin', 2147483647),
(2, 'Minhazul Asif', 'minhaz@gmail.com', '$2y$10$bVrr3HEknB0.RoN83mpmEOB6C/YhrhRvK2rBHL5dO9MOI/RiPtnAi', 'Male', '', '', '', 'user', 2147483647),
(3, 'Kamal Khan', 'kamal@khan.com', '$2y$10$EG0PzlzmWl2BUIS/E/nR/.EpUxGl35hino1GTUIoT/Y9Hr1rp3fh.', 'Male', '', '', '', 'user', 2147483647),
(4, 'Md Younus', 'aoulad1508@gmail.com', '$2y$10$8n5zae1H52yKTtHRZ.ZvvORqEK4j1ZrwgE5oHJlT0WRC6ba2lkkTm', 'Male', '', '', '', 'user', 2147483647);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
