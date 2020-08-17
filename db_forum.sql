-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2020 at 08:11 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id_category` bigint(20) NOT NULL,
  `category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) NOT NULL,
  `is_delete` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`id_category`, `category`, `created_at`, `created_by_updated_at`, `updated_by`, `is_delete`, `is_active`) VALUES
(1, 'Android', '2020-07-24 17:10:41', '2020-07-24 17:10:41', 0, 0, 0),
(3, 'Web', '2020-07-25 02:05:40', '2020-07-25 02:05:40', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_komentar`
--

CREATE TABLE `tbl_komentar` (
  `id_komentar` bigint(20) NOT NULL,
  `id_pertanyaan` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `komentar` text NOT NULL,
  `like` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_updated_at` varchar(50) NOT NULL,
  `updated_by` bigint(20) NOT NULL,
  `is_delete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_komentar`
--

INSERT INTO `tbl_komentar` (`id_komentar`, `id_pertanyaan`, `id_user`, `komentar`, `like`, `created_at`, `created_by_updated_at`, `updated_by`, `is_delete`) VALUES
(7, 10, 6, 'bukan gitu', 0, '2020-07-25 01:58:43', '2020-07-25 03:58:43', 6, 0),
(8, 10, 6, 'mantap gan', 0, '2020-07-26 05:55:09', '', 0, 0),
(9, 10, 6, 'kayaknya bisa deh gan', 0, '2020-07-26 05:55:26', '', 0, 0),
(11, 10, 6, 'silahkan gan', 3, '2020-07-26 17:00:52', '', 0, 0),
(12, 11, 6, 'silahkan gan', 5, '2020-07-26 17:00:48', '', 0, 0),
(13, 11, 6, 'tes gan', 2, '2020-07-26 17:00:56', '', 0, 0),
(14, 11, 6, 'tes gan', 0, '2020-08-03 13:29:35', '', 0, 0),
(15, 14, 7, 'fa', 0, '2020-08-04 05:00:39', '2020-08-04 12:00:39', 7, 1),
(16, 14, 7, 'first', 0, '2020-08-03 13:25:37', '2020-08-03 20:25:37', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pertanyaan`
--

CREATE TABLE `tbl_pertanyaan` (
  `id_pertanyaan` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `id_category` bigint(20) NOT NULL,
  `pertanyaan` text NOT NULL,
  `like` int(11) NOT NULL,
  `comment` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_updated_at` varchar(220) NOT NULL,
  `updated_by` bigint(20) NOT NULL,
  `is_delete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_pertanyaan`
--

INSERT INTO `tbl_pertanyaan` (`id_pertanyaan`, `id_user`, `id_category`, `pertanyaan`, `like`, `comment`, `created_at`, `created_by_updated_at`, `updated_by`, `is_delete`) VALUES
(10, 6, 1, 'izin bertanyasa', 0, 0, '2020-07-26 05:52:28', '2020-07-25 03:46:42', 6, 0),
(11, 6, 1, 'izin bertanya', 0, 0, '2020-07-26 13:41:37', '', 0, 0),
(13, 6, 1, 'first', 0, 0, '2020-08-03 15:58:51', '2020-08-03 22:58:51', 6, 0),
(14, 6, 3, 'perbedaan mobile dengan web', 0, 0, '2020-08-03 15:55:17', '2020-08-03 22:55:17', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `token_fcm` varchar(200) NOT NULL,
  `image_profile` text NOT NULL,
  `nohp` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` bigint(20) NOT NULL,
  `is_delete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `name`, `email`, `password`, `token_fcm`, `image_profile`, `nohp`, `created_at`, `created_by_updated_at`, `updated_by`, `is_delete`) VALUES
(6, 'fikri', 'razitulis@gmail.com', '$2y$10$NmgFa4iuNNTevLB96.Uvh.ZbMmw2pAMASMn3P8wUF0.7Fnf2CG.3e', 'razits', 'IMG_20200730_172748_791.jpg', '', '2020-08-04 05:18:09', '2020-08-04 05:18:09', 0, 0),
(7, 'razit', 'razitulikhlas@gmail.com', '$2y$10$NUV.GJdKY9VOfwCgkqhOQOw4W2SrtH0z23DjitAWaWk5jCsQKtjva', '159357', '', '082169146904', '2020-08-03 06:19:24', '2020-08-03 06:19:24', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `tbl_komentar`
--
ALTER TABLE `tbl_komentar`
  ADD PRIMARY KEY (`id_komentar`),
  ADD KEY `id_pertanyaan` (`id_pertanyaan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tbl_pertanyaan`
--
ALTER TABLE `tbl_pertanyaan`
  ADD PRIMARY KEY (`id_pertanyaan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_category` (`id_category`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id_category` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_komentar`
--
ALTER TABLE `tbl_komentar`
  MODIFY `id_komentar` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_pertanyaan`
--
ALTER TABLE `tbl_pertanyaan`
  MODIFY `id_pertanyaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_komentar`
--
ALTER TABLE `tbl_komentar`
  ADD CONSTRAINT `tbl_komentar_ibfk_1` FOREIGN KEY (`id_pertanyaan`) REFERENCES `tbl_pertanyaan` (`id_pertanyaan`),
  ADD CONSTRAINT `tbl_komentar_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id`);

--
-- Constraints for table `tbl_pertanyaan`
--
ALTER TABLE `tbl_pertanyaan`
  ADD CONSTRAINT `tbl_pertanyaan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id`),
  ADD CONSTRAINT `tbl_pertanyaan_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `tbl_category` (`id_category`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
