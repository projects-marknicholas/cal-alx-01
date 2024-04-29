-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2024 at 03:31 PM
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
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `sell`
--

CREATE TABLE `sell` (
  `id` int(11) NOT NULL,
  `slip_no` text DEFAULT NULL,
  `product_id` text NOT NULL,
  `quantity` float NOT NULL,
  `sell_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell`
--

INSERT INTO `sell` (`id`, `slip_no`, `product_id`, `quantity`, `sell_date`) VALUES
(180, '1714393303709', 'PR-03230', 1, '2024-04-29 12:21:43'),
(181, '1714393330772', 'PR-03230', 1, '2024-04-29 12:22:10'),
(182, '1714393342606', 'PR-03230', 1, '2024-04-29 12:22:22'),
(183, '1714393411275', 'PR-03230', 1, '2024-04-29 12:23:31'),
(184, '1714393424828', 'PR-03230', 1, '2024-04-29 12:23:44'),
(185, '1714393580314', 'PR-03230', 1, '2024-04-29 12:26:20'),
(186, '1714393613608', 'PR-03230', 1, '2024-04-29 12:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` int(11) NOT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `stocks_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `product_id`, `product_name`, `description`, `quantity`, `unit_price`, `stocks_date`) VALUES
(19, 'PR-23235', 'BFF Fries', 'Made with premium potatoes such as the Russet Burbank and the Shepody. With 0g of trans fat per labeled serving, these epic fries are crispy and golden on the outside and fluffy on the inside.', 86, 158.00, '2024-04-29 10:36:57'),
(20, 'PR-03230', '1-pc. Spicy Chicken McDo & Fries Meal', 'Golden brown chicken that\'s tender and juicy served with the classic spaghetti with ground beef and signature sauce.', 1, 179.00, '2024-04-29 12:26:53'),
(21, 'PR-35604', '1-pc. Chicken McDo & Mushroom Soup Meal', 'Comes with Mushroom Soup and Large Drink.', 64, 160.00, '2024-04-29 11:12:30'),
(23, 'PR-41104', 'Quarter Pounder w/ Cheese, Lettuce, & Tomatoes Meal', 'Crisp shredded lettuce and three Roma tomato slices top a ¼ lb.* of 100% McDonald\'s fresh beef that\'s hot, deliciously juicy and cooked when you order.', 100, 271.00, '2024-04-23 15:40:04'),
(24, 'PR-58820', 'McCrispy Chicken Sandwich w/ Lettuce & Tomatoes Meal', 'A crispy chicken sandwich that is comprised of chicken patty, mayonnaise, and sesame seed bun', 99, 166.00, '2024-04-26 13:30:41'),
(25, 'PR-51815', 'Cheesy Burger McDo w/ Lettuce & Tomatoes Meal', 'A double layer of sear-sizzled, juicy 100% pure beef mingled with special sauce on a sesame seed bun and topped with melty American cheese, crisp lettuce.', 100, 160.00, '2024-04-23 15:40:57'),
(26, 'PR-49655', 'Snack Burger McShare', 'This meal is designed for sharing purposes at McDonald\'s and three persons can easily enjoy this meal.', 100, 380.00, '2024-04-23 15:41:46'),
(27, 'PR-55935', '6-pc. Chicken McShare Box', 'Chicken McDo McShare Bundle (Good for 3) Comes with 6-pc. Chicken McDo, 1 BFF Fries, 3 Rice, 3 Drinks.', 95, 437.00, '2024-04-29 11:03:07'),
(28, 'PR-44309', '8-pc. Chicken McShare Box', 'Chicken McDo McShare Bundle (Good for 4) Comes with 8-pc. Chicken McDo, 1 BFF Fries, 4 Rice, 4 Drinks.', 100, 579.00, '2024-04-23 15:43:06'),
(29, 'PR-75857', 'McSpaghetti Platter', ' piece of crispy, golden brown chicken that\'s tender and juicy served with the classic spaghetti with ground beef and signature sauce.', 100, 242.00, '2024-04-23 15:43:48'),
(30, 'PR-27527', 'BFF Fries N’ McFloat Combo', 'BFF Fries N\' McFloat Combo · Home / PINOY FOOD DELIVERY / FAST FOOD / McDO Menu / GROUP MEALS / BFF Fries N\' Coke McFloat Combo. BFF Fries N\' Coke McFloat Combo.', 99, 265.00, '2024-04-23 19:51:21'),
(31, 'PR-49400', 'BFF Shake Shake Fries BBQ N’ McFloat Combo', 'Order BFF Shake Shake Fries BBQ N\' McFloat Combo thru the all-in-one delivery app', 99, 281.00, '2024-04-23 19:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `email` text NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`) VALUES
(2, 'Mark Nicholas', 'Razon', 'razonmarknicholas.cdlb@gmail.com', '$2y$10$UJP0.xIKx5MuuevOa4x2Mub.6gDhFiaBhPOrf2a4FS4Lm5i0TPGJK'),
(9, 'Chin Chin ', 'Marinay', 'chinchinmarinay21@gmail.com', '$2y$10$VCwlvc7mg3UNFEbcNLvPmeMfdd5BThn9Hyq2aB54mxUq6VQGPWggi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sell`
--
ALTER TABLE `sell`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sell`
--
ALTER TABLE `sell`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
