-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-07-05 09:17:04
-- 服务器版本： 5.7.9
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hfkeding_gis`
--

-- --------------------------------------------------------

--
-- 表的结构 `action_msg`
--

DROP TABLE IF EXISTS `action_msg`;
CREATE TABLE IF NOT EXISTS `action_msg` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_num` tinyint(3) UNSIGNED NOT NULL,
  `i_num` smallint(5) UNSIGNED NOT NULL,
  `tem` tinyint(4) NOT NULL,
  `hum` tinyint(4) NOT NULL,
  `dev_id` int(10) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `action_time` (`action_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `dev_list`
--

DROP TABLE IF EXISTS `dev_list`;
CREATE TABLE IF NOT EXISTS `dev_list` (
  `dev_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dev_number` char(11) NOT NULL,
  `dev_name` char(30) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `line_id` int(10) UNSIGNED NOT NULL,
  `dev_phase` char(4) NOT NULL,
  PRIMARY KEY (`dev_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `group_list`
--

DROP TABLE IF EXISTS `group_list`;
CREATE TABLE IF NOT EXISTS `group_list` (
  `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` char(30) NOT NULL,
  `group_loc` char(50) NOT NULL,
  `line_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `line_list`
--

DROP TABLE IF EXISTS `line_list`;
CREATE TABLE IF NOT EXISTS `line_list` (
  `line_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `line_name` varchar(50) NOT NULL,
  `add_time` timestamp NOT NULL,
  PRIMARY KEY (`line_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `user_list`
--

DROP TABLE IF EXISTS `user_list`;
CREATE TABLE IF NOT EXISTS `user_list` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` char(18) NOT NULL,
  `passwd` char(32) NOT NULL,
  `last_login_time` timestamp NOT NULL,
  `register_time` timestamp NOT NULL,
  `user_role` tinyint(3) UNSIGNED NOT NULL DEFAULT '10',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
