-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2020 at 11:14 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `private_chat`
--

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `room`, `user`, `created_date`, `modified_date`) VALUES
(1, 1, 1, '2020-01-09', '2020-01-09'),
(2, 2, 2, '2020-01-09', '2020-01-09'),
(3, 1, 3, '2020-01-09', '2020-01-09'),
(4, 2, 3, '2020-01-09', '2020-01-09'),
(5, 13, 1, '2020-01-10', '2020-01-10'),
(6, 14, 1, '2020-01-10', '2020-01-10'),
(7, 16, 6, '2020-01-10', '2020-01-10'),
(8, 17, 1, '2020-01-10', '2020-01-10'),
(9, 19, 6, '2020-01-10', '2020-01-10'),
(10, 20, 1, '2020-01-10', '2020-01-10'),
(11, 3, 1, '2020-01-10', '2020-01-10'),
(12, 1, 1, '2020-01-10', '2020-01-10'),
(13, 1, 1, '2020-01-10', '2020-01-10'),
(14, 1, 1, '2020-01-10', '2020-01-10'),
(15, 21, 6, '2020-01-10', '2020-01-10'),
(16, 1, 1, '2020-01-10', '2020-01-10'),
(17, 1, 1, '2020-01-10', '2020-01-10'),
(18, 1, 1, '2020-01-10', '2020-01-10'),
(19, 1, 6, '2020-01-10', '2020-01-10'),
(20, 1, 6, '2020-01-10', '2020-01-10'),
(21, 1, 6, '2020-01-10', '2020-01-10'),
(22, 1, 6, '2020-01-10', '2020-01-10'),
(23, 1, 1, '2020-01-10', '2020-01-10'),
(24, 19, 1, '2020-01-10', '2020-01-10'),
(25, 19, 6, '2020-01-10', '2020-01-10'),
(26, 19, 6, '2020-01-10', '2020-01-10'),
(27, 19, 6, '2020-01-10', '2020-01-10'),
(28, 19, 6, '2020-01-10', '2020-01-10'),
(29, 19, 6, '2020-01-10', '2020-01-10'),
(30, 19, 6, '2020-01-10', '2020-01-10'),
(31, 19, 6, '2020-01-10', '2020-01-10'),
(32, 19, 6, '2020-01-10', '2020-01-10'),
(33, 19, 6, '2020-01-10', '2020-01-10'),
(34, 19, 6, '2020-01-10', '2020-01-10');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user`, `room`, `message`, `created_datetime`, `modified_datetime`) VALUES
(1, 1, 1, 'hello room1 from user1', '2020-01-09 04:14:17', '2020-01-09 04:14:17'),
(2, 2, 2, 'hello room 2 from user 2', '2020-01-09 04:14:17', '2020-01-09 04:14:17'),
(3, 3, 1, 'hello room 1 from user 3', '2020-01-09 04:14:17', '2020-01-09 04:14:17'),
(4, 3, 2, 'hello room 2 from user 3 ', '2020-01-09 04:14:17', '2020-01-09 04:14:17'),
(5, 1, 1, 'test from api 1', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 1, 1, 'test from api 2', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 1, 1, 'test from api 2', '2020-01-10 10:55:50', '2020-01-10 10:55:50'),
(8, 1, 1, 'test from api 2', '2020-01-10 10:55:58', '2020-01-10 10:55:58'),
(9, 1, 1, 'test from api 2', '2020-01-10 10:56:35', '2020-01-10 10:56:35');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `owner` int(11) NOT NULL,
  `code` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `name`, `owner`, `code`, `created_date`, `modified_date`) VALUES
(1, 'Room 1', 1, 'R1Code', '2020-01-09', '2020-01-09'),
(2, 'Room 2', 2, 'R2Code', '2020-01-09', '2020-01-09'),
(3, 'room11', 1, '1578646323', '2020-01-10', '2020-01-10'),
(5, 'test1', 6, '1578646573', '2020-01-10', '2020-01-10'),
(7, 'room12', 1, '1578647219', '2020-01-10', '2020-01-10'),
(9, 'room13', 1, '1578647309', '2020-01-10', '2020-01-10'),
(11, 'room14', 1, '1578647387', '2020-01-10', '2020-01-10'),
(12, 'room15', 1, '1578647419', '2020-01-10', '2020-01-10'),
(13, 'room16', 1, '1578647451', '2020-01-10', '2020-01-10'),
(14, 'room17', 1, '1578647523', '2020-01-10', '2020-01-10'),
(16, 'test2', 6, '1578647572', '2020-01-10', '2020-01-10'),
(17, 'room18', 1, '1578647620', '2020-01-10', '2020-01-10'),
(19, 'test21', 6, '1578647645', '2020-01-10', '2020-01-10'),
(20, 'room19', 1, '1578647846', '2020-01-10', '2020-01-10'),
(21, 'test22', 6, '1578648945', '2020-01-10', '2020-01-10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` date NOT NULL,
  `modified_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `phone_no`, `created_date`, `modified_date`) VALUES
(1, 'user1', 'user1', '0996123', '2020-01-09', '2020-01-09'),
(2, 'user2', 'user2', '09961234', '2020-01-09', '2020-01-09'),
(3, 'user3', 'user3', '099612345', '2020-01-09', '2020-01-09'),
(4, 'test1', 'test1', '', '2020-01-10', '2020-01-10'),
(6, 'arkar', '12345', '', '2020-01-10', '2020-01-10'),
(7, '', '', '', '2020-01-10', '2020-01-10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender` (`user`),
  ADD KEY `room` (`room`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `owner` (`owner`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `member_ibfk_2` FOREIGN KEY (`room`) REFERENCES `room` (`id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`room`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `message_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
