-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2013 at 12:20 PM
-- Server version: 10.0.4-MariaDB-1~wheezy-log
-- PHP Version: 5.5.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `movlib`
--

--
-- Dumping data for table `movies_titles`
--

INSERT INTO `movies_titles` (`movie_id`, `language_id`, `title`, `dyn_comments`, `is_display_title`) VALUES
(2, 52, 'Die Verurteilten', '\0\0\0\0\0\0Cdeen!deutsches Kommentar!english comment', 1),
(3, 41, 'Léon: The Professional', '', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;