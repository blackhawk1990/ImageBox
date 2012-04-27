-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 27 Kwi 2012, 13:19
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
-- Struktura tabeli dla  `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `url` varchar(1000) character set utf8 default NULL,
  `data_dodania` date default NULL,
  `format` varchar(50) character set utf8 default NULL,
  `ilosc_pobran` int(11) default NULL,
  `nazwa` varchar(50) character set utf8 default NULL,
  `ocena` int(11) default NULL,
  `waga` varchar(50) character set utf8 default NULL,
  `fileID` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL,
  PRIMARY KEY  (`fileID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `File`
--

INSERT INTO `File` (`url`, `data_dodania`, `format`, `ilosc_pobran`, `nazwa`, `ocena`, `waga`, `fileID`, `userID`) VALUES
('http://www.google.pl/imgres?start=72&hl=pl&biw=1440&bih=780&gbv=2&tbm=isch&tbnid=TRI7OgSxKGVb_M:&imgrefurl=http://allegro.pl/magpro2-base-chiptuning-tool-leasing-i1978823451.html&docid=ckUoAX7LduRTaM&imgurl=http://cscs.medschl.cam.ac.uk/wp-content/uploads/2009/04/support-300x300.jpg&w=300&h=300&ei=DrJ6T4bXBobP4QT1ubCIBA&zoom=1&iact=hc&vpx=1174&vpy=57&dur=1853&hovh=225&hovw=225&tx=79&ty=249&sig=100501075653745369375&page=3&tbnh=139&tbnw=141&ndsp=40&ved=1t:429,r:39,s:72', '2012-04-03', 'jpg', 0, 'support', 0, '0,11', 1, 1);
