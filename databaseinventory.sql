-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2024 at 03:57 AM
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
-- Database: `databaseinventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `historis`
--

CREATE TABLE `historis` (
  `ID` int(11) NOT NULL,
  `pengguna` varchar(30) NOT NULL,
  `stok_id` int(6) UNSIGNED NOT NULL,
  `waktu` datetime NOT NULL DEFAULT curtime(),
  `quantity` int(6) NOT NULL,
  `activity` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historis`
--

-- --------------------------------------------------------

--
-- Table structure for table `masterbahan`
--

CREATE TABLE `masterbahan` (
  `stok_id` int(6) UNSIGNED NOT NULL,
  `kelompok` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `quantity` int(12) NOT NULL,
  `deskripsi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `masterkelompok`
--

CREATE TABLE `masterkelompok` (
  `kelompok_id` int(11) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masterkelompok`
--

-- --------------------------------------------------------

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `ID` int(6) NOT NULL,
  `produk` varchar(30) NOT NULL,
  `nama_bahan` varchar(255) NOT NULL,
  `quantity` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stokbahan`
--

CREATE TABLE `stokbahan` (
  `stok_id` int(6) UNSIGNED NOT NULL,
  `quantity` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `historis`
--
ALTER TABLE `historis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `historis_ibfk_1` (`stok_id`);

--
-- Indexes for table `masterbahan`
--
ALTER TABLE `masterbahan`
  ADD PRIMARY KEY (`stok_id`);

--
-- Indexes for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  ADD PRIMARY KEY (`kelompok_id`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `stokbahan`
--
ALTER TABLE `stokbahan`
  ADD PRIMARY KEY (`stok_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `historis`
--
ALTER TABLE `historis`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `masterbahan`
--
ALTER TABLE `masterbahan`
  MODIFY `stok_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  MODIFY `kelompok_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `historis`
--
ALTER TABLE `historis`
  ADD CONSTRAINT `historis_ibfk_1` FOREIGN KEY (`stok_id`) REFERENCES `masterbahan` (`stok_id`);

--
-- Constraints for table `stokbahan`
--
ALTER TABLE `stokbahan`
  ADD CONSTRAINT `stokbahan_ibfk_1` FOREIGN KEY (`stok_id`) REFERENCES `masterbahan` (`stok_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
