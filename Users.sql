-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 15 Maj 2012, 09:27
-- Wersja serwera: 5.0.51
-- Wersja PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `maateoo_ibox`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `avatar` varchar(1000) default NULL,
  `email` varchar(50) default NULL,
  `nick` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `ranga` varchar(50) default NULL,
  `wolne_miejsce` int(11) NOT NULL default '1024',
  `limit_wielkości` enum('1024','2048','5120','10240') NOT NULL,
  `rola` enum('admin','user','premium-user','not-logged') default NULL,
  `userID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Zrzut danych tabeli `Users`
--

INSERT INTO `Users` (`avatar`, `email`, `nick`, `password`, `ranga`, `wolne_miejsce`, `limit_wielkości`, `rola`, `userID`) VALUES
('http://i587.photobucket.com/albums/ss317/mitch7896/twitter-bird-2-300x300.png', 'poniatek1@wp.pl', 'Poniatek', 'Poniatek', 'PRO', 1024, '1024', 'admin', 1),
('http://girl-wonder.org/web300x300px_72dpi_wecan.gif', 'poniatek@wp.pl', 'Robert', 'Robert', 'nowy', 1024, '1024', 'user', 2),
('http://macmcrae.com/wp-content/uploads/2007/09/_skull-300x300.jpg', 'bolek@gmail.com', 'Bolek', 'Bolek', 'nowy', 1024, '5120', 'premium-user', 3),
('http://www.mma.pl/wp-content/uploads/2009/12/shooto-logo-300x300.jpg', 'marek@onet.pl', 'Marek', 'Marek', 'nowy', 1024, '1024', 'user', 4),
('http://awakenbeyond.com/files/2010/03/happy-sad-face-300x300.jpg', 'edzio@gmail.com', 'edzio', 'edzio', 'normal', 1024, '1024', 'user', 5);
