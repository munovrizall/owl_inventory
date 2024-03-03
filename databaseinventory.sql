-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 03, 2024 at 04:57 PM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.0.3

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
CREATE DATABASE IF NOT EXISTS `databaseinventory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `databaseinventory`;

-- --------------------------------------------------------

--
-- Table structure for table `bahan_produksi`
--

CREATE TABLE `bahan_produksi` (
  `ID` int(6) NOT NULL,
  `produk` varchar(30) NOT NULL,
  `nama_bahan` varchar(255) NOT NULL,
  `quantity` int(6) NOT NULL,
  `harga_bahan` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(6) NOT NULL,
  `nama_client` varchar(255) NOT NULL,
  `nama_korespondensi` varchar(255) NOT NULL,
  `alamat_perusahaan` varchar(500) NOT NULL,
  `username` varchar(40) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `nama_client`, `nama_korespondensi`, `alamat_perusahaan`, `username`, `password`) VALUES
(1, 'OWL', 'admin', '...', '', ''),

-- --------------------------------------------------------

--
-- Table structure for table `detail_maintenance`
--

CREATE TABLE `detail_maintenance` (
  `detail_id` int(6) NOT NULL,
  `transaksi_id` int(6) NOT NULL,
  `produk_mt` varchar(255) DEFAULT NULL,
  `no_sn` int(10) NOT NULL,
  `garansi` binary(1) DEFAULT NULL,
  `keterangan` varchar(255) NOT NULL,
  `kedatangan` binary(1) NOT NULL,
  `cek_barang` binary(1) NOT NULL,
  `berita_as` binary(1) NOT NULL,
  `administrasi` binary(1) NOT NULL,
  `pengiriman` binary(1) NOT NULL,
  `no_resi` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `firmware_setup`
--

CREATE TABLE `firmware_setup` (
  `produk` varchar(20) NOT NULL,
  `firmware` varchar(10) NOT NULL,
  `hardware` varchar(10) NOT NULL,
  `path` varchar(200) DEFAULT NULL,
  `flag_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historis`
--

CREATE TABLE `historis` (
  `ID` int(6) NOT NULL,
  `pengguna` varchar(40) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `waktu` datetime(6) NOT NULL,
  `quantity` int(6) NOT NULL,
  `activity` varchar(20) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventaris_produk`
--

CREATE TABLE `inventaris_produk` (
  `id` int(6) NOT NULL,
  `type_produk` varchar(20) DEFAULT NULL,
  `produk` varchar(40) DEFAULT NULL,
  `chip_id` int(12) DEFAULT NULL,
  `no_sn` int(10) DEFAULT NULL,
  `nama_client` varchar(40) DEFAULT NULL,
  `garansi_awal` date DEFAULT NULL,
  `garansi_akhir` date DEFAULT NULL,
  `garansi_void` tinyint(1) DEFAULT NULL,
  `keterangan_void` varchar(40) DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  `mac_wifi` varchar(20) DEFAULT NULL,
  `mac_bluetooth` varchar(20) DEFAULT NULL,
  `firmware_version` varchar(6) DEFAULT NULL,
  `hardware_version` varchar(6) DEFAULT NULL,
  `free_ram` int(6) DEFAULT NULL,
  `min_ram` int(6) DEFAULT NULL,
  `batt_low` int(4) DEFAULT NULL,
  `batt_high` int(4) DEFAULT NULL,
  `temperature` float DEFAULT NULL,
  `status_error` int(4) DEFAULT NULL,
  `gps_latitude` float DEFAULT NULL,
  `gps_longitude` float DEFAULT NULL,
  `status_qc_sensor_1` varchar(255) DEFAULT NULL,
  `status_qc_sensor_2` varchar(255) DEFAULT NULL,
  `status_qc_sensor_3` varchar(255) DEFAULT NULL,
  `status_qc_sensor_4` varchar(255) DEFAULT NULL,
  `status_qc_sensor_5` varchar(255) DEFAULT NULL,
  `status_qc_sensor_6` varchar(255) DEFAULT NULL,
  `last_online` datetime DEFAULT NULL,
  `bat` int(5) DEFAULT NULL,
  `pt` varchar(10) DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `masterbahan`
--

CREATE TABLE `masterbahan` (
  `stok_id` int(6) UNSIGNED NOT NULL,
  `kelompok` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `quantity` int(12) NOT NULL,
  `harga_bahan` int(10) DEFAULT NULL,
  `lokasi_penyimpanan` varchar(50) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `masterkelompok`
--

CREATE TABLE `masterkelompok` (
  `kelompok_id` int(11) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` int(6) NOT NULL,
  `nama_produk` varchar(20) NOT NULL,
  `quantity` int(6) NOT NULL,
  `hpp_produk` int(10) DEFAULT NULL,
  `gambar_produk` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_maintenance`
--

CREATE TABLE `transaksi_maintenance` (
  `transaksi_id` int(11) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `nama_client` varchar(255) DEFAULT NULL,
  `last_edit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `account_id` int(6) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(10) NOT NULL,
  `tanda_tangan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`account_id`, `nama_lengkap`, `username`, `password`, `role`, `tanda_tangan`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', ''),
COMMIT;

--
-- Indexes for table `bahan_produksi`
--
ALTER TABLE `bahan_produksi`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `nama_bahan` (`nama_bahan`),
  ADD KEY `produk` (`produk`);

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
  ADD KEY `id_transaksi` (`transaksi_id`),
  ADD KEY `produk_mt` (`produk_mt`),
  ADD KEY `no_sn` (`no_sn`);

--
-- Indexes for table `historis`
--
ALTER TABLE `historis`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `inventaris_produk`
--
ALTER TABLE `inventaris_produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_sn` (`no_sn`),
  ADD KEY `produk` (`produk`);

--
-- Indexes for table `masterbahan`
--
ALTER TABLE `masterbahan`
  ADD PRIMARY KEY (`stok_id`),
  ADD UNIQUE KEY `nama` (`nama`),
  ADD KEY `kelompok` (`kelompok`);

--
-- Indexes for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  ADD PRIMARY KEY (`kelompok_id`),
  ADD UNIQUE KEY `nama_kelompok` (`nama_kelompok`),
  ADD UNIQUE KEY `nama_kelompok_2` (`nama_kelompok`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`),
  ADD UNIQUE KEY `nama_produk` (`nama_produk`);

--
-- Indexes for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  ADD PRIMARY KEY (`transaksi_id`),
  ADD KEY `nama_client` (`nama_client`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_produksi`
--
ALTER TABLE `bahan_produksi`
  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `detail_maintenance`
--
ALTER TABLE `detail_maintenance`
  MODIFY `detail_id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historis`
--
ALTER TABLE `historis`
  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventaris_produk`
--
ALTER TABLE `inventaris_produk`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `masterbahan`
--
ALTER TABLE `masterbahan`
  MODIFY `stok_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `masterkelompok`
--
ALTER TABLE `masterkelompok`
  MODIFY `kelompok_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `produk_id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  MODIFY `transaksi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `account_id` int(6) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bahan_produksi`
--
ALTER TABLE `bahan_produksi`
  ADD CONSTRAINT `bahan_produksi_ibfk_1` FOREIGN KEY (`nama_bahan`) REFERENCES `masterbahan` (`nama`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bahan_produksi_ibfk_2` FOREIGN KEY (`produk`) REFERENCES `produk` (`nama_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_maintenance`
--
ALTER TABLE `detail_maintenance`
  ADD CONSTRAINT `detail_maintenance_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi_maintenance` (`transaksi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_maintenance_ibfk_2` FOREIGN KEY (`produk_mt`) REFERENCES `produk` (`nama_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_maintenance_ibfk_3` FOREIGN KEY (`no_sn`) REFERENCES `inventaris_produk` (`no_sn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventaris_produk`
--
ALTER TABLE `inventaris_produk`
  ADD CONSTRAINT `inventaris_produk_ibfk_2` FOREIGN KEY (`produk`) REFERENCES `produk` (`nama_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `masterbahan`
--
ALTER TABLE `masterbahan`
  ADD CONSTRAINT `masterbahan_ibfk_1` FOREIGN KEY (`kelompok`) REFERENCES `masterkelompok` (`nama_kelompok`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_maintenance`
--
ALTER TABLE `transaksi_maintenance`
  ADD CONSTRAINT `transaksi_maintenance_ibfk_1` FOREIGN KEY (`nama_client`) REFERENCES `client` (`nama_client`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
