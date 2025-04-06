-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2022 at 10:26 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `music_website_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `bio` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `bio`, `user_id`, `image`) VALUES
(1, 'Diamond', '', 1, 'uploads/Diamond-Platnumz-press-2021-bb11-2021-billboard-1548-1628187990.jpg'),
(2, 'Ed sheeran', '', 1, 'uploads/791a047636136702e25ba1096b11cfe7.jpg'),
(4, 'alicia keyz', 'this is an artist bio.\r\n\r\n\r\nWhat is Lorem Ipsum?\r\n\r\nLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\n\r\nthis is the end', 1, 'uploads/Billy-Joe-Armstrong.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category` varchar(30) NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `disabled`) VALUES
(1, 'Pop', 0),
(2, 'Country', 0),
(3, 'R&B', 0),
(4, 'Dance', 0);

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL,
  `image` varchar(1024) NOT NULL,
  `file` varchar(1024) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `views` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `user_id`, `artist_id`, `image`, `file`, `category_id`, `date`, `views`, `slug`, `featured`) VALUES
(1, 'One in a million', 1, 1, 'uploads/9464b890819e224b.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 4, '2022-06-24 12:52:59', 13, 'one-in-a-million', 0),
(2, 'Mad over me', 1, 1, 'uploads/452771.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 4, '2022-06-24 12:59:04', 0, 'mad-over-me', 1),
(3, 'baby', 1, 2, 'uploads/791a047636136702e25ba1096b11cfe7.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 1, '2022-06-24 13:23:49', 13, 'baby', 1),
(4, 'Soulful', 1, 2, 'uploads/pexels-photo-4654051.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 1, '2022-06-25 09:04:08', 0, 'soulful', 0),
(5, 'Say something', 1, 4, 'uploads/91234813c5767cf0fdb35529f756cf74.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 3, '2022-06-25 09:06:27', 0, 'say-something', 0),
(6, 'Someday', 1, 4, 'uploads/pexels-photo-3756774.jpeg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 2, '2022-06-25 10:24:01', 0, 'someday', 0),
(7, 'Everywhere is home', 1, 2, 'uploads/pexels-photo-3757004.jpeg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 2, '2022-06-25 10:24:43', 0, 'everywhere-is-home', 0),
(8, 'She be mine', 1, 1, 'uploads/Rihanna.-Photo-W-Magazine.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 3, '2022-06-25 10:25:42', 0, 'she-be-mine', 0),
(9, 'Play all the way', 1, 4, 'uploads/Sarkodie.jpg', 'uploads/05.One_inna_million ft Rj Kanierra.mp3', 1, '2022-06-25 10:26:16', 0, 'play-all-the-way', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `date`) VALUES
(1, 'admin', 'email@email.com', '$2y$10$QKjydkzNslVlmJKZ5S2t0Ogf553y8AzR16bEsxz.EtzkDG3woZuJq', 'admin', '2022-06-24 09:48:57'),
(3, 'John', 'john@email.com', '$2y$10$70yB6Eh8FyqIp7bR9IMCy.GYq4dKdmGjwpNrUXo4/8Nr0b.NhhQW2', 'user', '2022-06-24 10:44:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disabled` (`disabled`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `views` (`views`),
  ADD KEY `date` (`date`),
  ADD KEY `title` (`title`),
  ADD KEY `slug` (`slug`),
  ADD KEY `featured` (`featured`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`),
  ADD KEY `date` (`date`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
