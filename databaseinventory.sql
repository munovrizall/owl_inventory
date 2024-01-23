-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2024 at 04:09 AM
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
-- Database: `databaseinventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(6) NOT NULL,
  `nama_client` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `detail_maintenance`
--

CREATE TABLE `detail_maintenance` (
  `detail_id` int(6) NOT NULL,
  `transaksi_id` int(6) NOT NULL,
  `produk_mt` varchar(255) NOT NULL,
  `no_sn` int(14) NOT NULL,
  `garansi` binary(1) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `kedatangan` binary(1) NOT NULL,
  `cek_barang` binary(1) NOT NULL,
  `berita_as` binary(1) NOT NULL,
  `administrasi` binary(1) NOT NULL,
  `pengiriman` binary(1) NOT NULL,
  `no_resi` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `historis`
--

CREATE TABLE `historis` (
  `ID` int(11) NOT NULL,
  `pengguna` varchar(30) NOT NULL,
  `stok_id` int(6) UNSIGNED NOT NULL,
  `waktu` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` int(6) NOT NULL,
  `activity` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `masterbahan`
--

CREATE TABLE `masterbahan` (
  `stok_id` int(6) UNSIGNED NOT NULL,
  `kelompok` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `quantity` int(12) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `masterkelompok`
--

CREATE TABLE `masterkelompok` (
  `kelompok_id` int(11) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` int(6) NOT NULL,
  `nama_produk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `ID` int(6) NOT NULL,
  `produk` varchar(30) NOT NULL,
  `nama_bahan` varchar(255) NOT NULL,
  `quantity` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `transaksi_maintenance`
--

CREATE TABLE `transaksi_maintenance` (
  `transaksi_id` int(11) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `nama_client` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `nama_client` (`nama_client`);

--
-- Indexes for table `detail_maintenance`
--
ALTER TABLE `detail_maintenance`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `id_transaksi` (`transaksi_id`);

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
  ADD PRIMARY KEY (`stok_id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  ADD PRIMARY KEY (`kelompok_id`),
  ADD UNIQUE KEY `nama_kelompok` (`nama_kelompok`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`),
  ADD UNIQUE KEY `nama_produk` (`nama_produk`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  ADD PRIMARY KEY (`transaksi_id`),
  ADD KEY `nama_client` (`nama_client`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `detail_maintenance`
--
ALTER TABLE `detail_maintenance`
  MODIFY `detail_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `historis`
--
ALTER TABLE `historis`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `masterbahan`
--
ALTER TABLE `masterbahan`
  MODIFY `stok_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  MODIFY `kelompok_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `produk_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  MODIFY `transaksi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_maintenance`
--
ALTER TABLE `detail_maintenance`
  ADD CONSTRAINT `detail_maintenance_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi_maintenance` (`transaksi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `historis`
--
ALTER TABLE `historis`
  ADD CONSTRAINT `historis_ibfk_1` FOREIGN KEY (`stok_id`) REFERENCES `masterbahan` (`stok_id`);

--
-- Constraints for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  ADD CONSTRAINT `transaksi_maintenance_ibfk_1` FOREIGN KEY (`nama_client`) REFERENCES `client` (`nama_client`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
