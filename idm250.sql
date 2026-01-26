-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 26, 2026 at 10:41 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `idm250`
--

-- --------------------------------------------------------

--
-- Table structure for table `cms_products`
--

CREATE TABLE `cms_products` (
  `id` int NOT NULL,
  `ficha` int NOT NULL,
  `sku` varchar(25) NOT NULL,
  `description` varchar(225) NOT NULL,
  `uom` varchar(25) NOT NULL,
  `piece` int NOT NULL,
  `length` int NOT NULL,
  `width` int NOT NULL,
  `height` decimal(10,2) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `assembly` tinyint(1) NOT NULL,
  `rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cms_products`
--

INSERT INTO `cms_products` (`id`, `ficha`, `sku`, `description`, `uom`, `piece`, `length`, `width`, `height`, `weight`, `assembly`, `rate`) VALUES
(1, 724, '1720813-0132', 'MDF ST LX C2-- 2465X1245X05.7MM P/EF/132', 'BUNDLE', 250, 96, 39, 29.65, 3945.22, 0, 15.16),
(2, 987, '1720814-0248', 'PINE CLR VG 2X4X8FT KD SELECT', 'BUNDLE', 200, 96, 42, 36.00, 2850.50, 0, 16.18),
(3, 337, '1720815-0156', 'OAK RED FAS 4/4 RGH KD 8-12FT', 'PALLET', 150, 120, 48, 42.00, 4125.75, 0, 15.16),
(4, 778, '1720816-0089', 'SPRUCE DIMENSION 2X6X12FT #2BTR', 'BUNDLE', 180, 144, 36, 30.00, 3280.00, 0, 14.50),
(5, 187, '1720817-0234', 'CEDAR WRC CVG 1X6X8FT CLR S4S', 'BUNDLE', 300, 96, 36, 24.00, 1890.25, 0, 20.06),
(6, 223, '1720818-0167', 'MAPLE HARD FAS 5/4 RGH KD 10FT', 'PALLET', 120, 120, 48, 38.00, 3750.80, 0, 16.18),
(7, 876, '1720819-0312', 'PLYWOOD BALTIC BIRCH 3/4X4X8', 'PALLET', 45, 96, 48, 36.00, 2980.00, 0, 17.02),
(8, 223, '1720820-0098', 'POPLAR FAS 4/4 RGH KD 8-14FT', 'BUNDLE', 175, 144, 42, 32.00, 2650.40, 0, 16.14),
(9, 991, '1720821-0445', 'WALNUT BLK FAS 4/4 RGH KD 8FT', 'PALLET', 80, 96, 48, 28.00, 2240.60, 0, 12.14),
(10, 901, '1720822-0223', 'DOUGLAS FIR CVG 2X10X16FT #1', 'BUNDLE', 100, 192, 48, 40.00, 4580.90, 0, 16.18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cms_products`
--
ALTER TABLE `cms_products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cms_products`
--
ALTER TABLE `cms_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
