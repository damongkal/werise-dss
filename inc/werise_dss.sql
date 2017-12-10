-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2017 at 01:41 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `werise_dss`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslog`
--

CREATE TABLE IF NOT EXISTS `accesslog` (
  `id` int(10) unsigned NOT NULL,
  `userid` smallint(5) unsigned NOT NULL,
  `pageaction` varchar(50) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_access`
--

CREATE TABLE IF NOT EXISTS `oryza_access` (
  `userid` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `station_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_access_log`
--

CREATE TABLE IF NOT EXISTS `oryza_access_log` (
  `userid` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'ID',
  `station_id` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `wtype` enum('r','f') DEFAULT 'r',
  `variety` varchar(100) NOT NULL,
  `fert` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `rcm_fertilizer`
--

CREATE TABLE IF NOT EXISTS `rcm_fertilizer` (
  `id` smallint(6) NOT NULL,
  `ftype` enum('1','2') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  `country_code` char(2) CHARACTER SET latin1 NOT NULL,
  `station_id` smallint(5) unsigned NOT NULL,
  `variety` varchar(255) CHARACTER SET latin1 NOT NULL,
  `yld` decimal(4,2) NOT NULL,
  `n1` smallint(6) NOT NULL DEFAULT '0',
  `n1day` tinyint(3) unsigned NOT NULL,
  `n2` smallint(6) NOT NULL DEFAULT '0',
  `n2day` tinyint(3) unsigned NOT NULL,
  `n3` smallint(6) NOT NULL DEFAULT '0',
  `n3day` tinyint(3) unsigned NOT NULL,
  `p1` smallint(6) DEFAULT NULL,
  `p2` smallint(6) DEFAULT NULL,
  `p3` smallint(6) DEFAULT NULL,
  `k1` smallint(6) DEFAULT NULL,
  `k2` smallint(6) DEFAULT NULL,
  `k3` smallint(6) DEFAULT NULL,
  `date_created` date NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=ascii;

--
-- Dumping data for table `rcm_fertilizer`
--

INSERT INTO `rcm_fertilizer` (`id`, `ftype`, `country_code`, `station_id`, `variety`, `yld`, `n1`, `n1day`, `n2`, `n2day`, `n3`, `n3day`, `p1`, `p2`, `p3`, `k1`, `k2`, `k3`, `date_created`) VALUES
(21, '2', '00', 0, '', '3.30', 0, 14, 30, 30, 30, 45, 21, NULL, NULL, 20, NULL, 20, '2014-01-01'),
(22, '2', '00', 0, '', '3.80', 20, 14, 26, 30, 26, 45, 24, NULL, NULL, 24, NULL, 24, '2014-01-01'),
(23, '2', '00', 0, '', '4.30', 20, 14, 32, 30, 32, 45, 26, NULL, NULL, 28, NULL, 28, '2014-01-01'),
(24, '2', '00', 0, '', '4.90', 20, 14, 37, 30, 37, 45, 29, NULL, NULL, 32, NULL, 32, '2014-01-01'),
(25, '2', '00', 0, '', '5.50', 30, 14, 38, 30, 38, 45, 32, NULL, NULL, 36, NULL, 36, '2014-01-01'),
(26, '2', '00', 0, '', '6.10', 30, 14, 44, 30, 44, 45, 35, NULL, NULL, 40, NULL, 40, '2014-01-01'),
(27, '2', '00', 0, '', '6.60', 35, 14, 48, 30, 48, 45, 38, NULL, NULL, 44, NULL, 44, '2014-01-01'),
(28, '2', '00', 0, '', '7.80', 40, 14, 51, 30, 51, 45, 41, NULL, NULL, 47, NULL, 47, '2014-01-01'),
(4, '1', 'PH', 0, '', '0.00', 30, 22, 60, 40, 30, 57, NULL, NULL, NULL, NULL, NULL, NULL, '2014-01-01'),
(3, '1', 'LA', 0, '', '0.00', 30, 22, 60, 40, 30, 57, NULL, NULL, NULL, NULL, NULL, NULL, '2014-01-01'),
(2, '1', 'TH', 0, '', '0.00', 30, 22, 60, 40, 30, 57, NULL, NULL, NULL, NULL, NULL, NULL, '2014-01-01'),
(1, '1', 'ID', 0, '', '0.00', 30, 22, 60, 40, 30, 57, NULL, NULL, NULL, NULL, NULL, NULL, '2014-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE IF NOT EXISTS `regions` (
  `region_id` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'ID',
  `parent_region` smallint(5) unsigned DEFAULT NULL,
  `region_name` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`region_id`, `country_code`, `parent_region`, `region_name`) VALUES
(1, 'ID', NULL, 'Central Java'),
(2, 'ID', NULL, 'South Sulawesi'),
(3, 'ID', NULL, 'Nusa Tenggara Barat'),
(4, 'ID', NULL, 'North Sumatra'),
(5, 'ID', NULL, 'East Java'),
(6, 'ID', 1, 'Pati, Jaken'),
(7, 'ID', 2, 'Maros, Moncongloe'),
(8, 'ID', 3, 'Lombok Tengah'),
(9, 'ID', 4, 'Deli Serdang'),
(10, 'ID', 5, 'Pasyruan, Wonojero'),
(11, 'PH', NULL, 'Reqion IV'),
(12, 'PH', 11, 'Laguna'),
(53, 'ID', 56, 'unknown'),
(13, 'LA', NULL, 'Campasak'),
(14, 'ID', NULL, 'Khammouane'),
(15, 'LA', NULL, 'Savannahkhét'),
(16, 'LA', NULL, 'Vientiane'),
(17, 'ID', 2, 'Bantaeng'),
(18, 'ID', 1, 'Banyumas'),
(19, 'ID', 1, 'Batang'),
(20, 'ID', 1, 'Blora'),
(21, 'LA', 15, 'Champone'),
(22, 'ID', 1, 'Cilacap'),
(51, 'ID', 54, 'unknown'),
(24, 'ID', 1, 'Demak'),
(25, 'ID', 1, 'Grobogan'),
(26, 'ID', 1, 'Jepara'),
(27, 'ID', 1, 'Karanganyar'),
(28, 'ID', 1, 'Kebumen'),
(29, 'ID', 1, 'Kendal'),
(30, 'ID', 1, 'Kota Pekalongan'),
(31, 'ID', 1, 'Kota Surakarta'),
(32, 'ID', 1, 'Kota Tegal'),
(33, 'ID', 1, 'Kudus'),
(52, 'ID', 55, 'unknown'),
(35, 'ID', 1, 'Magelang'),
(36, 'LA', 15, 'Naphok'),
(37, 'LA', 15, 'Outhoumphone'),
(38, 'LA', 13, 'Pak Sé'),
(39, 'ID', 1, 'Pati'),
(40, 'ID', 16, 'Phonhong'),
(41, 'LA', 15, 'Savannahkhét'),
(42, 'ID', 1, 'Semarang'),
(43, 'ID', 49, 'Subang'),
(44, 'ID', 1, 'Temanggung'),
(45, 'LA', 14, 'Thakek'),
(46, 'ID', 1, 'Wonogiri'),
(47, 'ID', 1, 'Wonosobo'),
(48, 'LA', 15, 'Xépôn'),
(50, 'ID', 54, 'unknown'),
(49, 'ID', NULL, 'West Java'),
(54, 'TH', NULL, 'Nakhon Phanom'),
(55, 'ID', NULL, 'Nakhon Ratchasima'),
(56, 'TH', NULL, 'Mukdahan'),
(57, 'PH', NULL, 'Region III'),
(58, 'PH', NULL, 'NCR'),
(59, 'PH', 57, 'Nueva Ecija');

-- --------------------------------------------------------

--
-- Table structure for table `system_options`
--

CREATE TABLE IF NOT EXISTS `system_options` (
  `id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `otype` enum('string','integer','boolean') CHARACTER SET latin1 NOT NULL DEFAULT 'string',
  `ovalue` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

--
-- Dumping data for table `system_options`
--

INSERT INTO `system_options` (`id`, `otype`, `ovalue`) VALUES
('_ADM_SHOW_MENU', 'boolean', '1'),
('_OPT_SHOW_DATAGRID', 'boolean', '0'),
('_ORYZACHART_SHOW_ALLDATES', 'boolean', '0'),
('_ORYZACHART_SHOW_NPK', 'boolean', '0'),
('_ADM_ORYZA_LOAD_TEST', 'string', '0'),
('_ADM_SHOW_LOAD_ORYZA_DETAIL', 'string', '0'),
('_ORYZA_VARIETIES', 'string', 'IR64.J96'),
('_ORYZA_LCHK_LA', 'string', 'Ciherang.E12'),
('_ORYZA_LCHK_TH', 'string', 'IR64.J96'),
('_ORYZA_LCHK_PH', 'string', 'IR64.J96'),
('_OPT_GOOGLE_ANALYTICS', 'string', '0'),
('_SHOW_MAP', 'string', '0'),
('_DB_DATA', 'string', 'werise_dss_data'),
('_ORYZA_VERSION', 'string', '3'),
('_ORYZA_NITROENV_NOFERT', 'string', 'NITROGEN BALANCE'),
('_ORYZA_NITROENV_GENFERT', 'string', 'NITROGEN BALANCE'),
('_ORYZA_LCHK_ID', 'string', 'Ciherang.E12'),
('_SHOW_HISTORICAL', 'string', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` smallint(5) unsigned NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(256) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `is_enabled` tinyint(4) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=ascii;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `fullname`, `address`, `email`, `phone`, `is_enabled`, `date_created`) VALUES
(1, 'admin', '1253fc81e0a0ae9ec47e954992d5b1a3', 'archie roland llorca', 'irri', 'archie_llorca@hotmail.com', '12345678901234567890', 1, '2016-01-15 00:00:00'),
(2, 'archie', '0fbcdfabbea47323ec48d1eae442e44a', '', '', '', '', 1, '2016-01-08 00:00:00'),
(4, 'archie2', '25d55ad283aa400af464c76d713c07ad', 'archie llorca', '', 'archie@test.com', '', 1, '2016-01-16 21:33:40'),
(5, 'archie3', '25d55ad283aa400af464c76d713c07ad', 'archie roland llorca', '', 'archie3@test.com', '', 1, '2016-01-16 22:20:32'),
(6, 'archie4', '25d55ad283aa400af464c76d713c07ad', 'archie llorca', '', 'archie4@test.com', '', 1, '2016-01-17 20:38:32'),
(7, 'archie6', '25d55ad283aa400af464c76d713c07ad', 'archie', '', 'archie6@test.com', '', 1, '2016-01-17 20:58:46'),
(8, 'archie7', '25d55ad283aa400af464c76d713c07ad', 'archie', 'asga sdfhsdh hf sfgjsrjhsfhsf fj df jf dfj', 'archie7@tet.o', '542522452562', 1, '2016-01-17 21:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `users_info`
--

CREATE TABLE IF NOT EXISTS `users_info` (
  `userid` smallint(5) unsigned NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_info`
--

INSERT INTO `users_info` (`userid`, `reason`) VALUES
(4, 'this is a test. this is a test. this is a test. this is a test. this is a test. this is a test. this is a test. this is a test. this is a test. this is a test. '),
(5, 'this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. '),
(6, 'this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. this is test. '),
(7, 'teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd teshisgs. dhsdh. hsdd '),
(8, 'asdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj jasdhsd sdhsd fj j');

-- --------------------------------------------------------

--
-- Table structure for table `varieties`
--

CREATE TABLE IF NOT EXISTS `varieties` (
  `variety_id` smallint(5) unsigned NOT NULL,
  `variety_code` varchar(15) NOT NULL,
  `variety_name` varchar(50) NOT NULL,
  `maturity_min` tinyint(3) unsigned NOT NULL,
  `maturity_max` tinyint(3) unsigned NOT NULL,
  `maturity_group` enum('S','M','L') NOT NULL,
  `yield_avg` decimal(4,2) DEFAULT NULL,
  `yield_potential` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `varieties`
--

INSERT INTO `varieties` (`variety_id`, `variety_code`, `variety_name`, `maturity_min`, `maturity_max`, `maturity_group`, `yield_avg`, `yield_potential`) VALUES
(1, 'Ciherang.E12', 'Ciherang', 116, 125, 'L', '5.00', '8.40'),
(2, '', 'Hipa 10', 114, 114, 'M', '8.10', '9.40'),
(3, '', 'Hipa 11', 114, 114, 'M', '8.40', '10.60'),
(4, '', 'Hipa 12 SBU', 105, 105, 'S', '7.70', '10.50'),
(5, '', 'Hipa 13', 105, 105, 'S', '7.70', '10.50'),
(6, '', 'Hipa 14 SBU', 112, 112, 'M', '8.40', '12.10'),
(7, '', 'Hipa 18', 113, 113, 'M', '7.80', '10.30'),
(8, '', 'Hipa 19', 111, 111, 'M', '1.80', '10.10'),
(9, '', 'Hipa 3', 116, 120, 'L', '8.50', '11.67'),
(10, '', 'Hipa 4', 114, 116, 'M', '8.01', '10.43'),
(11, '', 'Hipa 5 Ceva', 114, 129, 'L', '7.29', '8.40'),
(12, '', 'Hipa 6 Jete', 101, 128, 'L', '7.36', '10.60'),
(13, '', 'Hipa 7', 112, 112, 'M', '7.60', '11.40'),
(14, '', 'Hipa 8', 115, 115, 'M', '7.50', '10.40'),
(15, '', 'Hipa 9', 115, 115, 'M', '8.10', '10.40'),
(16, '', 'Hipa Jatim 1', 119, 119, 'M', '8.20', '10.00'),
(17, '', 'Hipa Jatim 3', 117, 117, 'M', '8.50', '10.70'),
(18, '', 'Hipa Katim 2', 119, 119, 'M', '9.30', '10.90'),
(19, '', 'Inpago 10', 115, 115, 'M', '4.00', '7.30'),
(20, '', 'Inpago 4', 124, 124, 'L', '4.10', '6.10'),
(21, '', 'Inpago 5', 118, 118, 'M', '4.00', '6.20'),
(22, '', 'Inpago 6', 113, 113, 'M', '3.90', '5.80'),
(23, '', 'Inpago 7', 111, 111, 'M', '4.60', '7.40'),
(24, '', 'Inpago 8', 119, 119, 'M', '5.20', '8.10'),
(25, '', 'Inpago 9', 109, 109, 'S', '5.20', '8.40'),
(26, '', 'Inpago Lipigo 4', 113, 113, 'M', '4.20', '7.10'),
(27, '', 'Inpara 1', 131, 131, 'L', '5.65', '6.47'),
(28, '', 'Inpara 2', 128, 128, 'L', '5.49', '6.08'),
(29, '', 'Inpara 3', 127, 127, 'L', '4.60', '5.60'),
(30, '', 'Inpara 4', 135, 135, 'L', '4.70', '7.60'),
(31, '', 'Inpara 5', 115, 115, 'M', '4.50', '7.20'),
(32, '', 'Inpara 6', 117, 117, 'M', '4.70', '6.00'),
(33, '', 'Inpara 8 Agritan', 105, 105, 'S', '4.70', '6.02'),
(34, '', 'Inpara 9 Agritan', 115, 115, 'M', '4.20', '5.60'),
(35, '', 'Inpari 1', 108, 108, 'S', '7.30', '10.00'),
(36, '', 'Inpari 10 Laeya', 112, 112, 'M', '4.80', '7.00'),
(37, '', 'Inpari 11', 105, 105, 'S', '6.50', '8.80'),
(38, '', 'Inpari 12', 99, 99, 'S', '6.20', '8.00'),
(39, '', 'Inpari 13', 99, 99, 'S', '6.60', '8.00'),
(40, '', 'Inpari 14 Pakuan', 113, 113, 'M', '6.60', '8.20'),
(41, '', 'Inpari 15 Parahyangan', 117, 117, 'M', '6.10', '7.50'),
(42, '', 'Inpari 16 Pasundan', 118, 118, 'M', '6.30', '7.60'),
(43, '', 'Inpari 17', 111, 111, 'M', '6.20', '7.90'),
(44, '', 'Inpari 18', 102, 102, 'S', '6.70', '9.50'),
(45, '', 'Inpari 19', 104, 104, 'S', '6.70', '9.50'),
(46, '', 'Inpari 2', 115, 115, 'M', '5.83', '7.30'),
(47, '', 'Inpari 20', 104, 104, 'S', '6.40', '8.80'),
(48, '', 'Inpari 21 Batipuah', 120, 120, 'L', '6.40', '8.20'),
(49, '', 'Inpari 22', 118, 118, 'M', '5.80', '7.90'),
(50, '', 'Inpari 23 Bantul', 113, 113, 'M', '6.90', '9.20'),
(51, '', 'Inpari 24 Gabusan', 111, 111, 'M', '6.70', '7.70'),
(52, '', 'Inpari 25 Opak Jaya', 115, 115, 'M', '7.00', '9.40'),
(53, '', 'Inpari 26', 124, 124, 'L', '5.70', '7.90'),
(54, '', 'Inpari 27', 125, 125, 'L', '5.70', '7.60'),
(55, '', 'Inpari 28 Kerinci', 120, 120, 'L', '6.60', '9.50'),
(56, '', 'Inpari 29 Rendaman', 110, 110, 'S', '6.50', '9.50'),
(57, '', 'Inpari 3', 110, 110, 'S', '6.05', '7.52'),
(58, '', 'Inpari 30 Ciherang', 111, 111, 'M', '7.20', '9.60'),
(59, '', 'Inpari 31', 119, 119, 'M', '6.00', '8.50'),
(60, '', 'Inpari 32 HDB', 120, 120, 'L', '6.30', '8.42'),
(61, '', 'Inpari 33', 107, 107, 'S', '6.60', '9.80'),
(62, '', 'Inpari 34 Salin Agritan', 102, 102, 'S', '5.10', '8.10'),
(63, '', 'Inpari 35 Salin Agritan', 106, 106, 'S', '5.30', '8.30'),
(64, '', 'Inpari 38', 115, 115, 'M', '5.71', '8.16'),
(65, '', 'Inpari 39', 115, 115, 'M', '5.89', '8.45'),
(66, '', 'Inpari 4', 115, 115, 'M', '6.04', '8.80'),
(67, '', 'Inpari 40', 116, 116, 'M', '5.79', '9.60'),
(68, '', 'Inpari 41 Agritan', 114, 114, 'M', '5.57', '7.83'),
(69, '', 'Inpari 5 Merawu', 115, 115, 'M', '5.74', '7.20'),
(70, '', 'Inpari 6 Jete', 118, 118, 'M', '6.82', '8.60'),
(71, '', 'Inpari 7 Lanrang', 110, 115, 'M', '6.20', '8.70'),
(72, '', 'Inpari 8', 125, 125, 'L', '6.30', '9.90'),
(73, '', 'Inpari 9 Elo', 125, 125, 'L', '6.40', '9.30'),
(74, '', 'Inpari Sidenuk', 103, 103, 'S', '6.90', '9.10'),
(75, '', 'Inpari Unsoed 79 Agritan', 109, 109, 'S', '4.90', '8.20'),
(76, '', 'Maro', 113, 113, 'M', '6.24', '8.85'),
(77, '', 'Mekongga', 116, 125, 'L', NULL, '6.00'),
(78, '', 'Rokan', 115, 115, 'M', '6.44', '9.24'),
(79, '', 'Situ Bagendit', 110, 120, 'L', '3.00', NULL),
(80, '', 'Situ Patenggang', 110, 120, 'L', '3.60', NULL),
(129, 'IR64.J96', 'IR64', 116, 125, 'L', '5.00', '9.20');

-- --------------------------------------------------------

--
-- Table structure for table `weather_access`
--

CREATE TABLE IF NOT EXISTS `weather_access` (
  `userid` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `station_id` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `weather_access_log`
--

CREATE TABLE IF NOT EXISTS `weather_access_log` (
  `userid` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT 'ID',
  `station_id` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `wtype` enum('r','f') CHARACTER SET utf8 NOT NULL DEFAULT 'r',
  `create_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `weather_stations`
--

CREATE TABLE IF NOT EXISTS `weather_stations` (
  `station_id` int(10) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `station_name` varchar(100) NOT NULL,
  `region_id` smallint(5) unsigned DEFAULT NULL,
  `full_name` text,
  `geo_lat` decimal(10,5) DEFAULT NULL,
  `geo_lon` decimal(10,5) DEFAULT NULL,
  `geo_alt` decimal(10,2) DEFAULT NULL,
  `is_enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `gps_confirmed` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `weather_stations`
--

INSERT INTO `weather_stations` (`station_id`, `country_code`, `station_name`, `region_id`, `full_name`, `geo_lat`, `geo_lon`, `geo_alt`, `is_enabled`, `gps_confirmed`) VALUES
(43, 'ID', 'Jakenan', 39, 'Jakenan Research Institute for Food Crops Jakenan Experiment Station, Pati District, Indonesia', '-6.75000', '111.15000', '7.00', 1, 0),
(55, 'ID', 'Adisumarno', 31, NULL, '-7.52000', '110.55000', NULL, 1, 0),
(56, 'ID', 'Babadan Magelang', 35, NULL, '-7.52000', '110.40000', NULL, 1, 0),
(57, 'ID', 'Batang (Kebun Percob.)', 19, NULL, '-6.90000', '109.76000', NULL, 1, 0),
(58, 'ID', 'Bawen(SMT Pert.)', 42, NULL, '-7.25000', '110.42000', NULL, 1, 0),
(60, 'ID', 'Borobudur', 35, NULL, '-7.71000', '110.24000', NULL, 1, 0),
(62, 'ID', 'Colo-Kudus', 33, NULL, '-6.67000', '111.08000', NULL, 1, 0),
(63, 'ID', 'Demark Mranggen', 24, NULL, '-7.03000', '110.53000', NULL, 1, 0),
(65, 'ID', 'Gunungpati(BPP)', 42, NULL, '-7.09000', '110.38000', NULL, 1, 0),
(66, 'ID', 'Karanganya', 27, NULL, '-7.30000', '110.50000', NULL, 1, 0),
(67, 'ID', 'Kb.Curug', NULL, NULL, '-7.30000', '110.50000', NULL, 0, 0),
(68, 'ID', 'Kebun Beji Jepara', 26, NULL, '-6.44000', '110.81000', NULL, 1, 0),
(69, 'ID', 'Kertek', 47, NULL, '-7.38000', '110.10000', NULL, 1, 0),
(72, 'ID', 'Kudus Dawe', 33, NULL, '-6.70000', '110.89000', NULL, 1, 0),
(73, 'ID', 'Medini(PT)', 42, NULL, '-7.16000', '110.33000', NULL, 1, 0),
(75, 'ID', 'Mijen(BBP)', 42, NULL, '-7.08000', '110.31000', NULL, 1, 0),
(76, 'ID', 'Ngablak(BPP)', 35, NULL, '-7.41000', '110.40000', NULL, 1, 0),
(79, 'ID', 'Pakopen', 42, NULL, '-7.21000', '110.39000', NULL, 1, 0),
(80, 'ID', 'Parakan', NULL, NULL, '-7.28000', '110.09000', NULL, 0, 0),
(81, 'ID', 'Pati Juwana', 39, NULL, '-6.72000', '111.14000', NULL, 1, 0),
(82, 'ID', 'Rendole-Pati', 39, NULL, '-6.72000', '111.02000', NULL, 1, 0),
(83, 'ID', 'Selogiri', 46, NULL, '-7.80000', '110.92000', NULL, 1, 0),
(86, 'ID', 'Sendangharjo-Blora', 20, NULL, '-6.92000', '111.43000', NULL, 1, 0),
(87, 'ID', 'SMPK Laboratorium Kedu', NULL, NULL, '-7.42000', '110.23000', NULL, 0, 0),
(88, 'ID', 'SMPK Seneng', 35, NULL, '-7.76000', '110.83000', NULL, 1, 0),
(90, 'ID', 'SMPK-Getas-Sltg(10)', 42, NULL, '-7.37000', '110.17000', NULL, 1, 0),
(91, 'ID', 'Soropadan(P3Pal)', 44, NULL, '-7.37000', '110.27000', NULL, 1, 0),
(95, 'ID', 'Ungaran', 42, NULL, '-7.86000', '110.12000', NULL, 1, 0),
(96, 'ID', 'Wadaslintang', 47, NULL, '-7.62000', '110.92000', NULL, 1, 0),
(2, 'LA', 'Pakse', 38, NULL, '14.39000', '105.58000', '100.00', 1, 0),
(3, 'LA', 'Vientiane', 40, NULL, '17.96000', '102.61000', '172.00', 1, 0),
(11, 'LA', 'Donghen', NULL, NULL, '16.70000', '105.27000', NULL, 1, 0),
(12, 'LA', 'Kengkok', NULL, NULL, '16.44000', '105.20000', NULL, 1, 0),
(13, 'LA', 'Lahanam', NULL, NULL, '16.27000', '105.27000', NULL, 1, 0),
(19, 'LA', 'Pakxong', NULL, NULL, '16.24000', '105.20000', NULL, 1, 0),
(20, 'LA', 'Phalan', NULL, NULL, '16.66000', '105.56000', NULL, 1, 0),
(21, 'LA', 'Savannakhet', 41, NULL, '16.55000', '104.75000', '144.00', 1, 0),
(24, 'LA', 'Xeno', NULL, NULL, '16.76000', '105.00000', NULL, 1, 0),
(28, 'LA', 'Kengkabao', NULL, NULL, '16.80000', '104.75000', NULL, 1, 0),
(29, 'LA', 'Nakoutchan', NULL, NULL, '16.83000', '105.33000', NULL, 1, 0),
(30, 'LA', 'Naphok', 36, NULL, '16.82000', '104.48000', '168.00', 1, 0),
(31, 'LA', 'Outhoumphone (AWS)', 37, NULL, '16.67000', '105.00000', NULL, 1, 0),
(32, 'LA', 'Pailom', NULL, NULL, '16.32000', '105.07000', NULL, 1, 0),
(34, 'LA', 'Thakek', 45, NULL, '17.24000', '104.49000', '144.00', 1, 0),
(1, 'PH', 'IRRI Wetland', 12, NULL, '14.22000', '121.25000', NULL, 1, 0),
(2, 'PH', 'IRRI Dryland', 12, NULL, '14.13000', '121.25000', NULL, 1, 0),
(3, 'PH', 'UPLB NAS', 12, NULL, '14.17000', '121.25000', NULL, 1, 0),
(29, 'TH', 'Nakhon Phanom', 51, NULL, '16.53000', '104.78000', NULL, 1, 0),
(82, 'TH', 'Mukdahan', 53, NULL, '16.53000', '104.75000', NULL, 1, 0),
(83, 'TH', 'Nakhon Phanom Agromet', 50, NULL, '17.43000', '104.78000', NULL, 1, 0),
(59, 'ID', 'Bojongsari - Banyumas', 18, NULL, '-7.41666', '109.48333', NULL, 1, 0),
(61, 'ID', 'Cilacap', 22, NULL, '-7.73333', '109.01666', NULL, 1, 0),
(64, 'ID', 'Gamer - Batang', 30, NULL, '-6.88333', '109.68333', NULL, 1, 0),
(70, 'ID', 'Kledung Temanggung', 44, NULL, '-7.38333', '110.01666', NULL, 1, 0),
(71, 'ID', 'Kr. Kemiri', 18, NULL, '-7.38333', '109.55000', NULL, 1, 0),
(74, 'ID', 'Meteorologi Tegal', 32, NULL, '-6.85000', '110.15000', NULL, 1, 0),
(77, 'ID', 'Ngambak Kampung - Grobongan', 25, NULL, '-7.01666', '110.61666', NULL, 1, 0),
(78, 'ID', 'Ngepos Gunung Merapi Magelang', 35, NULL, '-6.93333', '110.38333', NULL, 1, 0),
(84, 'ID', 'Semarang', 42, NULL, '-6.98333', '110.38333', NULL, 1, 0),
(85, 'ID', 'Sempor - Kebumen', 28, NULL, '-7.48333', '109.31666', NULL, 1, 0),
(89, 'ID', 'SMPK Wil Surakarta', 31, NULL, '-7.75000', '110.83333', NULL, 1, 0),
(92, 'ID', 'Srimadono - Kebumen', 28, NULL, '-7.66666', '109.81666', NULL, 1, 0),
(93, 'ID', 'Sukorejo', 29, NULL, '-7.06666', '110.05000', NULL, 1, 0),
(94, 'ID', 'Tambi', 47, NULL, '-6.85000', '109.15000', NULL, 1, 0),
(97, 'ID', 'Pati Juwana', 39, NULL, '-6.71666', '111.13333', NULL, 1, 0),
(1, 'LA', 'TODO', NULL, NULL, NULL, NULL, NULL, 1, 0),
(25, 'LA', 'Xepon', 48, NULL, NULL, NULL, NULL, 1, 0),
(36, 'LA', 'Champone', 21, NULL, NULL, NULL, NULL, 1, 0),
(35, 'TH', 'Nakhon Ratchasima', 52, NULL, NULL, NULL, NULL, 1, 0),
(2, 'ID', 'Sukamandi', 43, 'Sukamandi (Agric. Research Station, CRIFC)', '-6.25000', '107.75000', NULL, 1, 0),
(102, 'ID', 'Karangsono', 25, 'Pasaluang', '-7.60000', '119.50000', '7.00', 1, 0),
(99, 'ID', 'Subang', 43, NULL, '-6.30000', '108.30000', '7.00', 1, 0),
(101, 'ID', 'Bonto Marannu', 17, 'Maros', '-5.10000', '119.50000', '7.00', 1, 0),
(100, 'ID', 'Segala anyar', 8, NULL, '-8.80000', '116.30000', '7.00', 1, 0),
(98, 'ID', 'Serdang', 9, NULL, '-3.60000', '98.70000', '7.00', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslog`
--
ALTER TABLE `accesslog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oryza_access_log`
--
ALTER TABLE `oryza_access_log`
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `rcm_fertilizer`
--
ALTER TABLE `rcm_fertilizer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`region_id`);

--
-- Indexes for table `system_options`
--
ALTER TABLE `system_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users_info`
--
ALTER TABLE `users_info`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `varieties`
--
ALTER TABLE `varieties`
  ADD PRIMARY KEY (`variety_id`);

--
-- Indexes for table `weather_access_log`
--
ALTER TABLE `weather_access_log`
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `weather_stations`
--
ALTER TABLE `weather_stations`
  ADD PRIMARY KEY (`country_code`,`station_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslog`
--
ALTER TABLE `accesslog`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rcm_fertilizer`
--
ALTER TABLE `rcm_fertilizer`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `region_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `varieties`
--
ALTER TABLE `varieties`
  MODIFY `variety_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=130;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
