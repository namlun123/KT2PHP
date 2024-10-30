-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 09:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qlbanhang`
--

-- --------------------------------------------------------

--
-- Table structure for table `chitietdathang`
--

CREATE TABLE `chitietdathang` (
  `sohoadon` int(11) NOT NULL,
  `mahang` varchar(30) NOT NULL,
  `soluong` int(11) NOT NULL,
  `giaban` decimal(10,0) NOT NULL,
  `nguoisua` int(11) NOT NULL,
  `ngaysua` date NOT NULL,
  `nguoixoa` int(11) NOT NULL,
  `ngayxoa` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chitietdathang`
--

INSERT INTO `chitietdathang` (`sohoadon`, `mahang`, `soluong`, `giaban`, `nguoisua`, `ngaysua`, `nguoixoa`, `ngayxoa`) VALUES
(1, 'SP01', 10, 200000, 0, '0000-00-00', 0, '0000-00-00'),
(1, 'SP40', 5, 0, 0, '0000-00-00', 0, '0000-00-00'),
(2, 'SP40', 5, 0, 0, '0000-00-00', 0, '0000-00-00'),
(3, 'SP112', 1, 0, 0, '0000-00-00', 0, '0000-00-00'),
(3, 'SP40', 5, 0, 0, '0000-00-00', 0, '0000-00-00'),
(4, 'SP03', 30, 250000, 0, '0000-00-00', 0, '0000-00-00'),
(4, 'SP112', 1, 0, 0, '0000-00-00', 0, '0000-00-00'),
(5, 'SP01', 10, 200000, 0, '0000-00-00', 0, '0000-00-00'),
(5, 'SP20', 10, 0, 0, '0000-00-00', 0, '0000-00-00'),
(6, 'SP03', 30, 250000, 0, '0000-00-00', 0, '0000-00-00'),
(6, 'SP2', 20, 1500000, 0, '0000-00-00', 0, '0000-00-00'),
(7, 'SP01', 1, 200000, 0, '0000-00-00', 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `dondathang`
--

CREATE TABLE `dondathang` (
  `sohoadon` int(5) NOT NULL,
  `ngaychonhang` date NOT NULL,
  `nguoidathang` varchar(30) NOT NULL,
  `chedo` int(1) NOT NULL,
  `ngaydathang` date NOT NULL,
  `ngaythanhtoan` date NOT NULL,
  `nguoinhanhang` varchar(50) NOT NULL,
  `diachinhanhang` varchar(100) NOT NULL,
  `sodienthoai` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dondathang`
--

INSERT INTO `dondathang` (`sohoadon`, `ngaychonhang`, `nguoidathang`, `chedo`, `ngaydathang`, `ngaythanhtoan`, `nguoinhanhang`, `diachinhanhang`, `sodienthoai`) VALUES
(1, '0000-00-00', 'nguyen thi hoa', 1, '0000-00-00', '0000-00-00', '', 'ha oi', 'sfdaf'),
(2, '0000-00-00', 'admin', 1, '0000-00-00', '0000-00-00', 'Tráº§n thá»‹ bÃ¬nh', '37 Phan Huy ChÃº', '0253243543'),
(3, '0000-00-00', 'admin', 1, '0000-00-00', '0000-00-00', 'Tráº§n thá»‹ bÃ¬nh', '37 Phan Huy ChÃº', '0253243543'),
(4, '0000-00-00', 'admin', 1, '0000-00-00', '0000-00-00', '', '', ''),
(5, '0000-00-00', 'admin', 1, '0000-00-00', '0000-00-00', 'trang', '23424', '53543'),
(6, '0000-00-00', 'admin', 1, '0000-00-00', '0000-00-00', 'lan', '34 hhoangf mai', '875755565'),
(7, '0000-00-00', 'admin', 0, '0000-00-00', '0000-00-00', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `loaihang`
--

CREATE TABLE `loaihang` (
  `maloai` varchar(5) NOT NULL,
  `tenloai` varchar(50) NOT NULL,
  `mota` varchar(100) NOT NULL,
  `nguoithem` int(11) NOT NULL,
  `ngaythem` date NOT NULL,
  `nguoisua` int(11) NOT NULL,
  `ngaysua` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `mahang` varchar(5) NOT NULL,
  `tenhang` varchar(30) NOT NULL,
  `giahang` decimal(10,0) NOT NULL,
  `hinhanh` varchar(30) NOT NULL,
  `soluong` int(5) NOT NULL,
  `maloai` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nguoithem` int(11) DEFAULT NULL,
  `ngaythem` date DEFAULT NULL,
  `nguoisua` int(11) DEFAULT NULL,
  `ngaysua` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`mahang`, `tenhang`, `giahang`, `hinhanh`, `soluong`, `maloai`, `nguoithem`, `ngaythem`, `nguoisua`, `ngaysua`) VALUES
('SP01', 'Bot Giat OMO', 200000, 'botgiatomo.jpg', 100, 'M01', NULL, NULL, NULL, NULL),
('SP03', 'Bot giat Sufff', 250000, 'botgiatsuf.jpg', 100, 'M01', NULL, NULL, NULL, NULL),
('SP111', 'sdfsafdsafsa', 0, 'bai-3.png', 0, 'M01', NULL, NULL, NULL, NULL),
('SP112', 'fdfgdsgfds', 0, 'anh7.jpg', 0, 'M01', NULL, NULL, NULL, NULL),
('SP114', 'San pham tot', 0, 'anh7.jpg', 0, 'M01', NULL, NULL, NULL, NULL),
('SP115', 'San pham chua tot', 0, 'anh7.jpg', 0, 'M01', NULL, NULL, NULL, NULL),
('SP15', 'San pham kem danh rang', 0, 'anh1.jpg', 0, 'L01', NULL, NULL, NULL, NULL),
('SP2', 'Sua rua mat tri mun', 1500000, 'botgiatlix.jpg', 200, 'M02', NULL, NULL, NULL, NULL),
('SP20', 'kem danh rang', 0, 'anh4.jpg', 0, 'L03', NULL, NULL, NULL, NULL),
('SP4', 'Sua tam LIX', 1500000, 'botgiatlix.jpg', 2000, 'M03', NULL, NULL, NULL, NULL),
('SP40', 'May do hoi nuoc', 0, 'anh4.jpg', 0, 'M05', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblnguoidung`
--

CREATE TABLE `tblnguoidung` (
  `id` int(11) NOT NULL,
  `tentaikhoan` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `matkhau` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `quyen` int(1) NOT NULL,
  `hoatdong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblnguoidung`
--

INSERT INTO `tblnguoidung` (`id`, `tentaikhoan`, `matkhau`, `quyen`, `hoatdong`) VALUES
(1, 'admin', '12345678', 1, 1),
(2, 'LT07', '12345678', 2, 1),
(3, 'hung12', '1354622', 2, 1),
(4, 'thuy', '12345', 2, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chitietdathang`
--
ALTER TABLE `chitietdathang`
  ADD PRIMARY KEY (`sohoadon`,`mahang`) USING BTREE;

--
-- Indexes for table `dondathang`
--
ALTER TABLE `dondathang`
  ADD PRIMARY KEY (`sohoadon`);

--
-- Indexes for table `loaihang`
--
ALTER TABLE `loaihang`
  ADD PRIMARY KEY (`maloai`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`mahang`);

--
-- Indexes for table `tblnguoidung`
--
ALTER TABLE `tblnguoidung`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dondathang`
--
ALTER TABLE `dondathang`
  MODIFY `sohoadon` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
