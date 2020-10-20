-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 02. Mrz 2020 um 03:03
-- Server-Version: 10.4.12-MariaDB
-- PHP-Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `nanonymity`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `account` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denomination` decimal(39,0) NOT NULL,
  `submission_epoch` int(10) NOT NULL DEFAULT current_timestamp(),
  `fully_received_epoch` int(10) DEFAULT NULL,
  `mixer_tx` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mixer_epoch` int(10) DEFAULT NULL,
  `fulfillment_account` varchar(65) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fulfillment_tx` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fulfillment_epoch` int(10) DEFAULT NULL,
  `fulfillment_deadline_epoch` int(10) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `account` (`account`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
