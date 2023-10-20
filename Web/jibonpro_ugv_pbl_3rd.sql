-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2023 at 09:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jibonpro_ugv_pbl_3rd`
--

-- --------------------------------------------------------

--
-- Table structure for table `food_group`
--

CREATE TABLE IF NOT EXISTS `food_group` (
  `group_id` int(255) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(1024) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `food_group`
--

INSERT IGNORE INTO `food_group` (`group_id`, `group_name`) VALUES
(1, 'Snacks'),
(2, 'Meals'),
(3, 'Launch'),
(4, 'Dinner');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE IF NOT EXISTS `food_items` (
  `item_id` int(255) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(1024) NOT NULL,
  `group_id` varchar(255) NOT NULL,
  `item_pic` varchar(2048) NOT NULL,
  `item_price` varchar(255) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `food_items`
--

INSERT IGNORE INTO `food_items` (`item_id`, `item_name`, `group_id`, `item_pic`, `item_price`) VALUES
(1, 'Pran Potato Chips', '1', 'uploads/2023/Sep/image-1695492774-1902344841.png', '10'),
(2, 'Milk Shake', '2', 'uploads/2023/Sep/image-1695492790-444767671.jpeg', '125'),
(3, 'Pepper Rice with Egg', '3', 'uploads/2023/Sep/image-1695492868-2128230293.png', '230'),
(4, 'Kacchi', '4', 'uploads/2023/Sep/image-1695492943-182355788.png', '300');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders_item`
--

CREATE TABLE IF NOT EXISTS `food_orders_item` (
  `order_item_id` int(255) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `item_quantity` varchar(255) NOT NULL,
  `name_then` varchar(1024) NOT NULL,
  `price_then` varchar(1024) NOT NULL,
  PRIMARY KEY (`order_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `food_orders_item`
--

INSERT IGNORE INTO `food_orders_item` (`order_item_id`, `order_id`, `item_quantity`, `name_then`, `price_then`) VALUES
(18, '13', '2', 'Milk Shake', '125.0'),
(19, '13', '2', 'Kacchi', '300.0'),
(20, '13', '1', 'Pepper Rice With Egg', '230.0');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders_list`
--

CREATE TABLE IF NOT EXISTS `food_orders_list` (
  `order_id` int(255) NOT NULL AUTO_INCREMENT,
  `status` varchar(1024) NOT NULL DEFAULT 'OPEN' COMMENT 'OPEN : order is not finished yet\r\nCLOSED : same as it''s mean for',
  `student_id` varchar(255) NOT NULL,
  `order_time` varchar(1024) NOT NULL DEFAULT '0',
  `billed_time` varchar(1024) NOT NULL DEFAULT '0',
  `paid_time` varchar(1024) NOT NULL DEFAULT '0',
  `total_when_booked` int(255) NOT NULL COMMENT 'without vat',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_orders_list`
--

INSERT IGNORE INTO `food_orders_list` (`order_id`, `status`, `student_id`, `order_time`, `billed_time`, `paid_time`, `total_when_booked`) VALUES
(13, 'CLOSED', '5', '1697830560', '1697831063', '0', 1080);

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE IF NOT EXISTS `info` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `info`
--

INSERT IGNORE INTO `info` (`id`, `name`, `value`) VALUES
(1, 'title', 'UGV Cafeteria Order Manager'),
(2, 'vat', '25'),
(3, '4e3b9acd4385b58c539b70445301f400', '');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(32) NOT NULL,
  `student_phone` int(10) NOT NULL,
  `student_name` varchar(1024) NOT NULL,
  `password` varchar(1024) NOT NULL COMMENT 'code where can device connect',
  `time` varchar(1024) NOT NULL COMMENT 'device adding time',
  `status` varchar(1024) NOT NULL COMMENT 'VERIFIED\r\nUNVERIFIED\r\nBANNED',
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `student_phone` (`student_phone`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `students`
--

INSERT IGNORE INTO `students` (`id`, `student_id`, `student_phone`, `student_name`, `password`, `time`, `status`) VALUES
(5, '12221059', 1600301810, 'MD Jibon Howlader', '1b1f72b3b5a4676772523edbcb7bd119', '1697829972', 'BANNED');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `txn_id` varchar(1024) NOT NULL,
  `time` varchar(32) NOT NULL DEFAULT '0',
  `credit_amount` varchar(4) NOT NULL DEFAULT '0',
  `debit_amount` varchar(4) NOT NULL DEFAULT '0',
  `currency` varchar(4) NOT NULL DEFAULT 'BDT',
  `order_id` varchar(255) NOT NULL DEFAULT 'N/A',
  `pay_by` varchar(1024) NOT NULL DEFAULT 'ACCOUNT',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `code` varchar(1024) NOT NULL COMMENT 'code where can device connect',
  `time` varchar(1024) NOT NULL COMMENT 'device adding time',
  `removed_time` varchar(1024) NOT NULL COMMENT 'device removed time',
  `username` varchar(1024) NOT NULL COMMENT 'name to identify on admin panel',
  `student_id` varchar(32) NOT NULL,
  `current_balance` int(11) NOT NULL,
  `status` varchar(1024) NOT NULL COMMENT 'active: device connected \r\nremoved: device removed\r\ninactive: device isn''t connected yet',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT IGNORE INTO `users` (`id`, `code`, `time`, `removed_time`, `username`, `student_id`, `current_balance`, `status`) VALUES
(1, '76075411', '1695492452', '0', 'jibon', '', 0, 'ACTIVE'),
(2, '72216174', '1695541106', '0', 'riadul', '', 0, 'ACTIVE');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
