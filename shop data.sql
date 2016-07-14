-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2016 at 09:40 PM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop`
--

-- --------------------------------------------------------

--
- --------------------------------------------------------

--
-- Table structure for table `shopcategory`
--

CREATE TABLE `shopcategory` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shopcategory`
--

INSERT INTO `shopcategory` (`id`, `shop_id`, `category`) VALUES
(1, 47, 'action'),
(2, 48, 'thriller'),
(3, 49, 'comedy'),
(11, 50, 'adventure'),
(12, 51, 'adventure'),
(13, 52, 'thriller'),
(15, 54, 'usa');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL,
  `image` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `name`, `description`, `price`, `image`, `quantity`) VALUES
(47, 'Hansel and Gretel', 'Fifteen years after Hansel (Jeremy Renner) and Gretel (Gemma Arterton) ', 10, 'product3.jpg', 5),
(48, 'Starred up', 'A troubled and explosively violent teenager is transferred to adult prison', 11, 'product2.jpg', 7),
(49, 'Dumb and Dumb to', 'Jim Carrey, Jeff Daniels, Rob Riggle', 15, 'product4.jpg', 8),
(50, 'Captain America: The Winter Soldier', 'As Steve Rogers struggles to embrace his role in the modern world, he teams up with another super soldier', 10, 'captain-america-black-widow-winter-soldier-2014.jpg', 6),
(51, 'ironclad', 'adventure', 5, 'Ironclad2.jpg', 0);

-- --------------------------------------------------------

--
--

--
-- Indexes for dumped tables
--



--
-- Indexes for table `shopcategory`
--
ALTER TABLE `shopcategory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shop_id` (`shop_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`);


-- AUTO_INCREMENT for table `shopcategory`
--
ALTER TABLE `shopcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--

