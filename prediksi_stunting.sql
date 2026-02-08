-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 12:17 PM
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
-- Database: `prediksi_stunting`
--

-- --------------------------------------------------------

--
-- Table structure for table `faktor_risiko`
--

CREATE TABLE `faktor_risiko` (
  `id` int(11) NOT NULL,
  `prediksi_id` int(11) NOT NULL,
  `parameter` varchar(50) NOT NULL,
  `nilai` float NOT NULL,
  `kontribusi` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faktor_risiko`
--

INSERT INTO `faktor_risiko` (`id`, `prediksi_id`, `parameter`, `nilai`, `kontribusi`) VALUES
(1, 1, 'usia', 25, 0.243358),
(2, 1, 'tinggi_badan', 165, 0.248589),
(3, 1, 'lila', 22, 0.243423),
(4, 1, 'hb', 10, 0.26463),
(5, 2, 'usia', 27, 0.2364),
(6, 2, 'tinggi_badan', 155, 0.2491),
(7, 2, 'lingkar_lengan_atas', 0, 0.2429),
(8, 2, 'kadar_hb', 0, 0.2716),
(9, 3, 'usia', 24, 0.2389),
(10, 3, 'tinggi_badan', 156, 0.2513),
(11, 3, 'lingkar_lengan_atas', 0, 0.2368),
(12, 3, 'kadar_hb', 0, 0.273),
(13, 4, 'usia', 35, 0.2404),
(14, 4, 'tinggi_badan', 155, 0.2461),
(15, 4, 'lingkar_lengan_atas', 0, 0.2344),
(16, 4, 'kadar_hb', 0, 0.2791),
(17, 5, 'usia', 26, 0.2404),
(18, 5, 'tinggi_badan', 176, 0.2461),
(19, 5, 'lingkar_lengan_atas', 0, 0.2344),
(20, 5, 'kadar_hb', 0, 0.2791),
(21, 6, 'usia', 31, 0.2398),
(22, 6, 'tinggi_badan', 162, 0.2527),
(23, 6, 'lingkar_lengan_atas', 0, 0.2354),
(24, 6, 'kadar_hb', 0, 0.2721),
(25, 7, 'usia', 22, 0.241),
(26, 7, 'tinggi_badan', 149, 0.2564),
(27, 7, 'lingkar_lengan_atas', 0, 0.2345),
(28, 7, 'kadar_hb', 0, 0.2681),
(29, 8, 'usia', 21, 0.2387),
(30, 8, 'tinggi_badan', 161, 0.2564),
(31, 8, 'lingkar_lengan_atas', 0, 0.2362),
(32, 8, 'kadar_hb', 0, 0.2687);

-- --------------------------------------------------------

--
-- Table structure for table `ibu_hamil`
--

CREATE TABLE `ibu_hamil` (
  `id` int(11) NOT NULL,
  `nama_ibu` varchar(100) NOT NULL,
  `usia` int(11) NOT NULL,
  `tinggi_badan` float NOT NULL,
  `lingkar_lengan_atas` float NOT NULL,
  `kadar_hb` float NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ibu_hamil`
--

INSERT INTO `ibu_hamil` (`id`, `nama_ibu`, `usia`, `tinggi_badan`, `lingkar_lengan_atas`, `kadar_hb`, `created_by`, `created_at`) VALUES
(1, 'Zandra', 25, 165, 22, 10, NULL, '2026-02-05 05:03:12'),
(5, 'Ibunda', 27, 155, 24, 10, NULL, '2026-02-05 07:03:48'),
(6, 'Mumalah', 24, 156, 24, 10, NULL, '2026-02-05 07:09:34'),
(7, 'Zahra', 35, 155, 24, 10.5, NULL, '2026-02-05 07:11:47'),
(8, 'Manda', 26, 176, 25, 14, NULL, '2026-02-05 07:13:00'),
(9, 'Miri', 31, 162, 25, 13, NULL, '2026-02-05 08:22:02'),
(10, 'Vandra', 22, 149, 27, 14.3, NULL, '2026-02-05 09:30:27'),
(11, 'Linda', 21, 161, 24, 12.2, 1, '2026-02-05 10:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `parameter`
--

CREATE TABLE `parameter` (
  `id` int(11) NOT NULL,
  `nama_parameter` varchar(50) NOT NULL,
  `tipe_data` enum('int','float','string','date') NOT NULL DEFAULT 'float',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parameter`
--

INSERT INTO `parameter` (`id`, `nama_parameter`, `tipe_data`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'usia', 'int', 1, '2026-02-05 06:28:12', '2026-02-05 06:28:12'),
(2, 'tinggi_badan', 'float', 1, '2026-02-05 06:28:12', '2026-02-05 06:28:12'),
(3, 'lingkar_lengan_atas', 'float', 1, '2026-02-05 06:28:12', '2026-02-05 06:28:36'),
(4, 'kadar_hb', 'float', 1, '2026-02-05 06:28:12', '2026-02-05 06:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `prediksi`
--

CREATE TABLE `prediksi` (
  `id` int(11) NOT NULL,
  `ibu_id` int(11) NOT NULL,
  `hasil` enum('Berisiko Stunting','Tidak Berisiko') NOT NULL,
  `probabilitas` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prediksi`
--

INSERT INTO `prediksi` (`id`, `ibu_id`, `hasil`, `probabilitas`, `created_at`) VALUES
(1, 1, 'Berisiko Stunting', 0.5838, '2026-02-05 05:03:35'),
(2, 5, 'Berisiko Stunting', 0.965, '2026-02-05 07:03:52'),
(3, 6, 'Berisiko Stunting', 0.615, '2026-02-05 07:09:39'),
(4, 7, 'Berisiko Stunting', 0.615, '2026-02-05 07:11:51'),
(5, 8, 'Tidak Berisiko', 0.275, '2026-02-05 07:13:05'),
(6, 9, 'Tidak Berisiko', 0.125, '2026-02-05 08:22:07'),
(7, 10, 'Berisiko Stunting', 0.96, '2026-02-05 09:30:31'),
(8, 11, 'Tidak Berisiko', 0.15, '2026-02-05 10:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$QQTi1yqWhhH3hRZ4rplFpOF/huo5tx8C3OOhp0frY8w5v2DC8lg5G', 'admin', '2026-02-05 09:52:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faktor_risiko`
--
ALTER TABLE `faktor_risiko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prediksi_id` (`prediksi_id`);

--
-- Indexes for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `parameter`
--
ALTER TABLE `parameter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_parameter` (`nama_parameter`);

--
-- Indexes for table `prediksi`
--
ALTER TABLE `prediksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ibu_id` (`ibu_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faktor_risiko`
--
ALTER TABLE `faktor_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `parameter`
--
ALTER TABLE `parameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prediksi`
--
ALTER TABLE `prediksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faktor_risiko`
--
ALTER TABLE `faktor_risiko`
  ADD CONSTRAINT `faktor_risiko_ibfk_1` FOREIGN KEY (`prediksi_id`) REFERENCES `prediksi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  ADD CONSTRAINT `ibu_hamil_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prediksi`
--
ALTER TABLE `prediksi`
  ADD CONSTRAINT `prediksi_ibfk_1` FOREIGN KEY (`ibu_id`) REFERENCES `ibu_hamil` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
