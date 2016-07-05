-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2016-07-05 16:30:20
-- 服务器版本： 5.7.13-log
-- PHP Version: 5.6.23

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
-- 表的结构 `alarms`
--

CREATE TABLE `alarms` (
  `id` int(10) UNSIGNED NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_num` tinyint(3) UNSIGNED NOT NULL,
  `i_num` smallint(5) UNSIGNED NOT NULL,
  `tem` tinyint(4) NOT NULL,
  `hum` tinyint(4) NOT NULL,
  `dev_id` int(10) UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `devs`
--

CREATE TABLE `devs` (
  `dev_id` int(10) UNSIGNED NOT NULL,
  `dev_number` char(11) NOT NULL,
  `dev_name` char(30) NOT NULL,
  `dev_phase` char(4) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `line_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `devs`
--

INSERT INTO `devs` (`dev_id`, `dev_number`, `dev_name`, `dev_phase`, `group_id`, `line_id`) VALUES
(1, '0912345678', '测试设备', 'A相', 1, 6),
(2, '0912345688', '测试设备', 'A相', 1, 6);

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE `groups` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `group_name` char(30) NOT NULL,
  `group_loc` char(30) NOT NULL,
  `line_id` int(10) UNSIGNED NOT NULL,
  `line_id2` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_loc`, `line_id`, `line_id2`) VALUES
(1, '1号杆塔', '圣惠路', 6, NULL),
(2, '1号杆塔', '圣惠路', 6, NULL),
(3, '1号杆塔', '圣惠路', 6, NULL),
(4, '1号杆塔', '圣惠路', 6, NULL),
(5, '1号杆塔', '圣惠路', 6, NULL),
(6, '1号杆塔', '圣惠路', 6, NULL),
(7, '1号杆塔', '圣惠路', 6, NULL),
(8, '1号杆塔', '圣惠路', 6, NULL),
(9, '1号杆塔', '圣惠路', 6, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `lines`
--

CREATE TABLE `lines` (
  `line_id` int(10) UNSIGNED NOT NULL,
  `line_name` varchar(50) NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `lines`
--

INSERT INTO `lines` (`line_id`, `line_name`, `add_time`) VALUES
(1, 'aaa', '2016-07-05 15:13:32'),
(2, 'mm', '2016-07-05 15:22:38'),
(3, 'ccc', '2016-07-05 15:24:14'),
(6, '高新线', '2016-07-05 15:49:34');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` char(18) NOT NULL,
  `passwd` char(32) NOT NULL,
  `last_login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_role` tinyint(3) UNSIGNED NOT NULL DEFAULT '10'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alarms`
--
ALTER TABLE `alarms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_time` (`action_time`),
  ADD KEY `FK_DEV_ID` (`dev_id`);

--
-- Indexes for table `devs`
--
ALTER TABLE `devs`
  ADD PRIMARY KEY (`dev_id`),
  ADD UNIQUE KEY `dev_number` (`dev_number`),
  ADD KEY `dev_id` (`dev_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `lines`
--
ALTER TABLE `lines`
  ADD PRIMARY KEY (`line_id`),
  ADD UNIQUE KEY `line_name` (`line_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `alarms`
--
ALTER TABLE `alarms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `devs`
--
ALTER TABLE `devs`
  MODIFY `dev_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- 使用表AUTO_INCREMENT `lines`
--
ALTER TABLE `lines`
  MODIFY `line_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
