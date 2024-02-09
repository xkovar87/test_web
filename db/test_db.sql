-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2024 at 06:08 PM
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
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `user_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 'Nový článek', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Curabitur sagittis hendrerit ante. Aenean fermentum risus id tortor. Sed vel lectus. Donec odio tempus molestie, porttitor ut, iaculis quis, sem. Integer malesuada. Nulla quis diam. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ra', '2024-02-07 15:57:55'),
(2, 1, 'Další nový článek', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque ipsum. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. Mauris tincidunt sem sed arcu. Quisque porta. Integer in sapien. Vivamus porttitor turpis ac leo. Ut tempus purus at lorem. Fusce tellus. Nullam eget nisl. Nullam lectus justo, vulputate eget mollis sed, tempor sed magna. Donec iaculis gravida nulla. Integer tempor. Aliquam ante. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. Mauris suscipit, ligula sit amet pharetra semper, nibh ante cursus purus, vel sagittis velit mauris vel metus. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit.', '2024-02-08 14:48:02'),
(3, 2, 'Radaktorův článek', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque ipsum. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. Mauris tincidunt sem sed arcu. Quisque porta. Integer in sapien. Vivamus porttitor turpis ac leo. Ut tempus purus at lorem. Fusce tellus. Nullam eget nisl. Nullam lectus justo, vulputate eget mollis sed, tempor sed magna. Donec iaculis gravida nulla. Integer tempor. Aliquam ante. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. Mauris suscipit, ligula sit amet pharetra semper, nibh ante cursus purus, vel sagittis velit mauris vel metus. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit.', '2024-02-08 16:12:26'),
(4, 3, 'Článek redaktora2', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla non lectus sed nisl molestie malesuada. Duis risus. Phasellus et lorem id felis nonummy placerat. Nam quis nulla. Phasellus enim erat, vestibulum vel, aliquam a, posuere eu, velit. Mauris dolor felis, sagittis at, luctus sed, aliquam non, tellus. Duis viverra diam non justo. Praesent in mauris eu tortor porttitor accumsan. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. In enim a arcu imperdiet malesuada.', '2024-02-09 11:20:55');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Správce'),
(2, 'Redaktor');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin@emailserver.cz', '$2y$12$RW5wQtIx12irimc3ETFbQeCdik1gSxrT58RTWZ2oxWMReAjl.H20y', 1),
(2, 'redaktor', 'redaktor@emailserver.cz', '$2y$12$qV7mVk6vI3WZEseOWlCOzunGNJEx39dpBs6lkSQF9U0OTKP8HxjyG', 2),
(3, 'redaktor2', 'redaktor2@emailserver.cz', '$2y$12$D6sjwIOBB6E16p.Dfjbr/eu93.afmdQ6SSIG3cku.qUVqrTgHGCGq', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
