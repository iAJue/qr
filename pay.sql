-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-12-19 18:41:10
-- 服务器版本： 5.5.62-log
-- PHP Version: 7.1.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qrpay`
--

-- --------------------------------------------------------

--
-- 表的结构 `qr`
--

CREATE TABLE IF NOT EXISTS `qr` (
  `id` int(11) NOT NULL,
  `qr` int(128) NOT NULL,
  `name` int(10) NOT NULL,
  `alipay` int(255) NOT NULL,
  `qq` int(255) NOT NULL,
  `wechat` int(255) NOT NULL,
  `time` int(11) NOT NULL,
  `ip` int(128) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
