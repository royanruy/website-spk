-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 04:45 AM
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
-- Database: `spk_saw`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `nama`, `email`, `password`, `role`, `foto`) VALUES
(3, 'admin2', 'andre', 'andre12345@gmail.com', '$2y$10$UhLNiAxzvmpW4x/NqIRvqOEIOuSApDD2iR.6Lyb0u5a0F7jx6N5dq', 'admin', 'uploads/1751595996_andre.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `data_kriteria`
--

CREATE TABLE `data_kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `jenis` enum('benefit','cost') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_kriteria`
--

INSERT INTO `data_kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `bobot`, `jenis`) VALUES
(1, 'C1 ', 'Kedisiplinan', 0.90, 'benefit'),
(2, 'C2', 'Produktivitas', 0.80, 'benefit'),
(3, 'C3', 'Kerjasama', 0.75, 'benefit'),
(4, 'C4', 'Kemampuan Bicara', 0.80, 'benefit'),
(5, 'C234', 'TEST', 0.90, 'benefit');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_preferensi`
--

CREATE TABLE `hasil_preferensi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `nilai_preferensi` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_preferensi`
--

INSERT INTO `hasil_preferensi` (`id`, `id_karyawan`, `nilai_preferensi`) VALUES
(85, 1, 3.16667),
(86, 2, 2.88333),
(87, 3, 2.70556),
(88, 4, 2.88889);

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama_karyawan`) VALUES
(1, 'Royann'),
(2, 'Andre'),
(3, 'Nifana'),
(4, 'Salsa');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id_penilaian`, `id_karyawan`, `id_kriteria`, `nilai`) VALUES
(9, 2, 1, 8),
(10, 2, 2, 8),
(11, 2, 3, 9),
(12, 2, 4, 7),
(17, 4, 1, 8),
(18, 4, 2, 8),
(19, 4, 3, 8),
(20, 4, 4, 8),
(57, 3, 1, 7),
(58, 3, 2, 7),
(59, 3, 3, 7),
(60, 3, 4, 9),
(65, 1, 1, 9),
(66, 1, 2, 9),
(67, 1, 3, 8),
(68, 1, 4, 9);

-- --------------------------------------------------------

--
-- Table structure for table `sub_kriteria`
--

CREATE TABLE `sub_kriteria` (
  `id_sub` int(11) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `nama_sub` varchar(100) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_kriteria`
--

INSERT INTO `sub_kriteria` (`id_sub`, `nama_kriteria`, `nama_sub`, `nilai`) VALUES
(1, 'Kedisiplinan', 'Sangat Baik', 9),
(2, 'Kedisiplinan', 'Baik', 8),
(3, 'Kedisiplinan', 'Kurang Baik', 7),
(4, 'Produktivitas', 'Sangat Baik', 9),
(5, 'Produktivitas', 'Baik', 8),
(6, 'Produktivitas', 'Kurang Baik', 7),
(7, 'Kerjasama', 'Sangat Baik', 9),
(8, 'Kerjasama', 'Baik', 8),
(9, 'Kerjasama', 'Kurang Baik', 7),
(10, 'Kemampuan Bicara', 'Sangat Baik', 9),
(11, 'Kemampuan Bicara', 'Baik', 8),
(12, 'Kemampuan Bicara', 'Kurang Baik', 7);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `email`, `foto`) VALUES
(2, 'user2', '$2y$10$JM.BwlF9X2kg5IuNd.THyOAqMNkF8OBYJKJQ/lqs2DOHxZlpCPBju', 'jihan', 'jihan123@gmail.com', 'uploads/1751596016_jihan.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `data_kriteria`
--
ALTER TABLE `data_kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD UNIQUE KEY `kode_kriteria` (`kode_kriteria`);

--
-- Indexes for table `hasil_preferensi`
--
ALTER TABLE `hasil_preferensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD PRIMARY KEY (`id_sub`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `data_kriteria`
--
ALTER TABLE `data_kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hasil_preferensi`
--
ALTER TABLE `hasil_preferensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id_sub` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_preferensi`
--
ALTER TABLE `hasil_preferensi`
  ADD CONSTRAINT `hasil_preferensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `data_kriteria` (`id_kriteria`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
