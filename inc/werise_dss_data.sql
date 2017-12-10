-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2017 at 01:43 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `werise_dss_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `keyid` varchar(20) NOT NULL,
  `cache_data` text NOT NULL,
  `cache_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `cdfdm_historical_log`
--

CREATE TABLE IF NOT EXISTS `cdfdm_historical_log` (
  `country_code` varchar(2) NOT NULL DEFAULT 'ID',
  `region_id` smallint(5) unsigned NOT NULL,
  `station_id` smallint(5) unsigned NOT NULL,
  `date_log` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_data`
--

CREATE TABLE IF NOT EXISTS `oryza_data` (
  `dataset_id` smallint(5) unsigned NOT NULL,
  `runnum` smallint(5) unsigned NOT NULL,
  `observe_date` date NOT NULL,
  `yield` decimal(4,2) NOT NULL,
  `fert` varchar(256) NOT NULL,
  `emergence` smallint(6) DEFAULT NULL,
  `panicle_init` smallint(6) DEFAULT NULL,
  `flowering` smallint(6) DEFAULT NULL,
  `harvest` smallint(6) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_datares`
--

CREATE TABLE IF NOT EXISTS `oryza_datares` (
  `dataset_id` smallint(5) unsigned NOT NULL,
  `runnum` smallint(5) unsigned NOT NULL,
  `day` smallint(5) unsigned NOT NULL,
  `dvs` decimal(3,2) NOT NULL,
  `zw` decimal(5,2) DEFAULT NULL,
  `doy` smallint(6) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_dataset`
--

CREATE TABLE IF NOT EXISTS `oryza_dataset` (
  `id` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'ID',
  `station_id` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `wtype` enum('r','f') DEFAULT 'r',
  `variety` varchar(100) NOT NULL,
  `fert` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oryza_ver` tinyint(3) unsigned DEFAULT NULL,
  `upload_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `oryza_dataset_display`
--

CREATE TABLE IF NOT EXISTS `oryza_dataset_display` (
  `dataset_id` int(10) unsigned NOT NULL,
  `is_disabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sintexf_raw`
--

CREATE TABLE IF NOT EXISTS `sintexf_raw` (
  `region_id` smallint(6) NOT NULL,
  `forecast_date` date NOT NULL,
  `pr` decimal(10,7) DEFAULT NULL,
  `tn` decimal(10,7) DEFAULT NULL,
  `tx` decimal(10,7) DEFAULT NULL,
  `ws` decimal(10,7) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `weather_acknowledge`
--

CREATE TABLE IF NOT EXISTS `weather_acknowledge` (
  `filename` varchar(30) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'ID',
  `station_id` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `wtype` enum('r','f') NOT NULL DEFAULT 'r',
  `remarks` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `weather_data`
--

CREATE TABLE IF NOT EXISTS `weather_data` (
  `dataset_id` smallint(5) unsigned NOT NULL,
  `observe_date` date NOT NULL,
  `rainfall` decimal(4,1) DEFAULT NULL,
  `min_temperature` decimal(5,2) DEFAULT NULL,
  `max_temperature` decimal(5,2) DEFAULT NULL,
  `irradiance` decimal(6,1) unsigned DEFAULT NULL,
  `sunshine_duration` decimal(3,1) unsigned DEFAULT NULL,
  `vapor_pressure` decimal(5,2) DEFAULT NULL,
  `mean_wind_speed` decimal(4,1) DEFAULT NULL,
  `decadal` smallint(5) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii;

-- --------------------------------------------------------

--
-- Table structure for table `weather_dataset`
--

CREATE TABLE IF NOT EXISTS `weather_dataset` (
  `id` smallint(5) unsigned NOT NULL,
  `country_code` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT 'ID',
  `station_id` smallint(5) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `wtype` enum('r','f') CHARACTER SET utf8 NOT NULL DEFAULT 'r',
  `notes` varchar(255) DEFAULT NULL,
  `oryza_ver` tinyint(3) unsigned DEFAULT NULL,
  `upload_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=ascii ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `weather_dataset_display`
--

CREATE TABLE IF NOT EXISTS `weather_dataset_display` (
  `dataset_id` int(10) unsigned NOT NULL,
  `is_disabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `weather_headers`
--

CREATE TABLE IF NOT EXISTS `weather_headers` (
  `header_id` smallint(5) unsigned NOT NULL,
  `dataset_id` smallint(5) unsigned NOT NULL,
  `header_field` varchar(20) NOT NULL,
  `header_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`keyid`);

--
-- Indexes for table `cdfdm_historical_log`
--
ALTER TABLE `cdfdm_historical_log`
  ADD PRIMARY KEY (`country_code`,`region_id`);

--
-- Indexes for table `oryza_data`
--
ALTER TABLE `oryza_data`
  ADD PRIMARY KEY (`dataset_id`,`observe_date`);

--
-- Indexes for table `oryza_datares`
--
ALTER TABLE `oryza_datares`
  ADD PRIMARY KEY (`dataset_id`,`runnum`,`day`);

--
-- Indexes for table `oryza_dataset`
--
ALTER TABLE `oryza_dataset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oryza_dataset_display`
--
ALTER TABLE `oryza_dataset_display`
  ADD PRIMARY KEY (`dataset_id`);

--
-- Indexes for table `sintexf_raw`
--
ALTER TABLE `sintexf_raw`
  ADD PRIMARY KEY (`region_id`,`forecast_date`);

--
-- Indexes for table `weather_acknowledge`
--
ALTER TABLE `weather_acknowledge`
  ADD PRIMARY KEY (`filename`,`wtype`);

--
-- Indexes for table `weather_data`
--
ALTER TABLE `weather_data`
  ADD PRIMARY KEY (`dataset_id`,`observe_date`);

--
-- Indexes for table `weather_dataset`
--
ALTER TABLE `weather_dataset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_code` (`country_code`,`station_id`,`year`,`wtype`);

--
-- Indexes for table `weather_headers`
--
ALTER TABLE `weather_headers`
  ADD PRIMARY KEY (`header_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oryza_dataset`
--
ALTER TABLE `oryza_dataset`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `weather_dataset`
--
ALTER TABLE `weather_dataset`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `weather_headers`
--
ALTER TABLE `weather_headers`
  MODIFY `header_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
