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
-- Struktura tabeli dla  `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(5) NOT NULL auto_increment,
  `tytul` varchar(100) NOT NULL,
  `autor` varchar(40) NOT NULL,
  `tekst` text NOT NULL,
  `data` varchar(20) NOT NULL,
  `rodzaj` enum('normalny','skrocony') NOT NULL default 'normalny',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Zrzut danych tabeli `news`
--

INSERT INTO `news` (`id`, `tytul`, `autor`, `tekst`, `data`, `rodzaj`) VALUES
(35, 'Witamy w Specjalnym Serwisie Fotograficznym Kortowiady 2012', 'Administrator', '<p>Witamy w Specjalnym Serwisie Fotograficznym Kortowiady 2012\r\n</p><p>\r\nStrona ta powstała w celu ułatwienia dostępu do zdjęć powstających podczas Kortowiady 2012. Na bieżąco będziemy tworzyć tu fotograficzny obraz wydarzeń z właśnie zaczynającego się naszego ulubionego studenckiego święta!\r\n</p>\r\n<p>\r\nJak co roku będzie kolorowo.....\r\n</p>\r\n', '2011-10-05 18:59:19', '');
