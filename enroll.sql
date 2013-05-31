-- phpMyAdmin SQL Dump
-- version 4.0.0
-- http://www.phpmyadmin.net
--
-- Host: localhost

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `enroll`
--

-- --------------------------------------------------------

--
-- Table structure for table `colloquiums`
--

CREATE TABLE IF NOT EXISTS `colloquiums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `image` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `preferred_room` text NOT NULL,
  `preferred_class_size` int(11) NOT NULL,
  `preferred_lunch_block` char(1) NOT NULL,
  `freshmen` tinyint(1) NOT NULL,
  `sophomores` tinyint(1) NOT NULL,
  `juniors` tinyint(1) NOT NULL,
  `seniors` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;


-- --------------------------------------------------------

--
-- Table structure for table `c_assignments`
--

CREATE TABLE IF NOT EXISTS `c_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duration` char(1) NOT NULL,
  `semester` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `class_size` int(11) DEFAULT '0',
  `room` text,
  `lunch_block` char(1) DEFAULT NULL,
  `notes` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `final` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;


-- --------------------------------------------------------

--
-- Table structure for table `c_enrollments`
--

CREATE TABLE IF NOT EXISTS `c_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `c_assignments_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `c_assignments_id` (`c_assignments_id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `dates`
--

CREATE TABLE IF NOT EXISTS `dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `schedule` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `date_2` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;



-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `freshman` int(11) DEFAULT NULL,
  `sophomore` int(11) DEFAULT NULL,
  `junior` int(11) DEFAULT NULL,
  `senior` int(11) DEFAULT NULL,
  `col1_freshman_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col1_sophomore_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col1_junior_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col1_senior_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col1_end` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col2_freshman_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col2_sophomore_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col2_junior_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col2_senior_start` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `col2_end` datetime NOT NULL DEFAULT '2013-05-27 07:00:00',
  `xy_num_days_open` int(11) NOT NULL DEFAULT '6',
  `xy_time_open` time NOT NULL DEFAULT '07:00:00',
  `xy_num_days_close` int(11) NOT NULL DEFAULT '0',
  `xy_time_close` time NOT NULL DEFAULT '00:00:00',
  `rooms` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `freshman`, `sophomore`, `junior`, `senior`, `col1_freshman_start`, `col1_sophomore_start`, `col1_junior_start`, `col1_senior_start`, `col1_end`, `col2_freshman_start`, `col2_sophomore_start`, `col2_junior_start`, `col2_senior_start`, `col2_end`, `xy_num_days_open`, `xy_time_open`, `xy_num_days_close`, `xy_time_close`, `rooms`) VALUES
(1, 2016, 2015, 2014, 2013, '2013-05-01 07:00:00', '2013-08-13 12:00:00', '2013-08-13 07:00:00', '2013-08-12 07:00:00', '2013-08-19 07:00:00', '2014-01-01 07:00:00', '2014-01-01 07:00:00', '2014-01-01 07:00:00', '2014-01-01 07:00:00', '2014-01-01 07:00:00', 300, '07:00:00', 0, '00:00:00', '1,2,3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` text NOT NULL,
  `firstname` text NOT NULL,
  `username` text NOT NULL,
  `role` text,
  `secondary_role` text,
  `graduation_year` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1172 ;


-- --------------------------------------------------------

--
-- Table structure for table `xy`
--

CREATE TABLE IF NOT EXISTS `xy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `image` text NOT NULL,
  `category` tinyint(4) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `preferred_room` text NOT NULL,
  `preferred_class_size` int(11) NOT NULL,
  `freshmen` tinyint(1) NOT NULL,
  `sophomores` tinyint(1) NOT NULL,
  `juniors` tinyint(1) NOT NULL,
  `seniors` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;


-- --------------------------------------------------------

--
-- Table structure for table `xy_assignments`
--

CREATE TABLE IF NOT EXISTS `xy_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xy_id` int(11) NOT NULL,
  `date_id` int(11) NOT NULL,
  `class_size` int(11) DEFAULT '0',
  `room` text,
  `notes` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `preferred_block` varchar(5) NOT NULL,
  `block` varchar(5) DEFAULT NULL,
  `final` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `xy_id` (`xy_id`),
  KEY `date_id` (`date_id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;


-- --------------------------------------------------------

--
-- Table structure for table `xy_enrollments`
--

CREATE TABLE IF NOT EXISTS `xy_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xy_assignments_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `xy_assignments_id` (`xy_assignments_id`),
  KEY `users_id` (`users_id`),
  KEY `xy_assignments_id_2` (`xy_assignments_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `colloquiums`
--
ALTER TABLE `colloquiums`
  ADD CONSTRAINT `colloquiums_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `c_assignments`
--
ALTER TABLE `c_assignments`
  ADD CONSTRAINT `c_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `c_enrollments`
--
ALTER TABLE `c_enrollments`
  ADD CONSTRAINT `c_enrollments_ibfk_1` FOREIGN KEY (`c_assignments_id`) REFERENCES `c_assignments` (`id`),
  ADD CONSTRAINT `c_enrollments_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `xy`
--
ALTER TABLE `xy`
  ADD CONSTRAINT `xy_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `xy_assignments`
--
ALTER TABLE `xy_assignments`
  ADD CONSTRAINT `xy_assignments_ibfk_1` FOREIGN KEY (`xy_id`) REFERENCES `xy` (`id`),
  ADD CONSTRAINT `xy_assignments_ibfk_2` FOREIGN KEY (`date_id`) REFERENCES `dates` (`id`),
  ADD CONSTRAINT `xy_assignments_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `xy_enrollments`
--
ALTER TABLE `xy_enrollments`
  ADD CONSTRAINT `xy_enrollments_ibfk_1` FOREIGN KEY (`xy_assignments_id`) REFERENCES `xy_assignments` (`id`),
  ADD CONSTRAINT `xy_enrollments_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
