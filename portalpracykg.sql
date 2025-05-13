-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2024 at 11:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portal_pracy`
--

-- --------------------------------------------------------

--
-- Table structure for table `aplikacje`
--

CREATE TABLE `aplikacje` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_oferty` int(11) NOT NULL,
  `data_aplikacji` datetime DEFAULT current_timestamp(),
  `status` enum('oczekujaca','rozpatrywana','zaakceptowana','odrzucona') DEFAULT 'oczekujaca',
  `wiadomosc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finanse`
--

CREATE TABLE `finanse` (
  `id` int(11) NOT NULL,
  `id_oferty` int(11) NOT NULL,
  `prowizja` decimal(10,2) NOT NULL COMMENT 'Prowizja od oferty',
  `cena` decimal(10,2) NOT NULL COMMENT 'Całkowita cena usługi',
  `liczba_ofert` int(11) DEFAULT 1 COMMENT 'Liczba powiązanych ofert',
  `data_transakcji` datetime DEFAULT current_timestamp(),
  `status` enum('oczekujaca','zaplacona','anulowana') DEFAULT 'oczekujaca',
  `id_pracownika` int(11) DEFAULT NULL COMMENT 'Pracownik obsługujący'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `ikona` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kontakt`
--

CREATE TABLE `kontakt` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `temat` varchar(255) DEFAULT NULL,
  `wiadomosc` text NOT NULL,
  `data_wyslania` datetime DEFAULT current_timestamp(),
  `status` enum('nowa','w_trakcie','zamknieta') DEFAULT 'nowa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oferty`
--

CREATE TABLE `oferty` (
  `id` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `opis` text NOT NULL,
  `firma` varchar(255) NOT NULL,
  `lokalizacja` varchar(255) DEFAULT NULL,
  `kategoria` varchar(100) DEFAULT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  `data_waznosci` date DEFAULT NULL,
  `id_pracodawcy` int(11) NOT NULL,
  `id_pracownika` int(11) DEFAULT NULL COMMENT 'Pracownik obsługujący ofertę',
  `wynagrodzenie` varchar(100) DEFAULT NULL,
  `wymagania` text DEFAULT NULL,
  `benefity` text DEFAULT NULL,
  `status` enum('aktywna','w_trakcie','zarchiwizowana','wygasla') DEFAULT 'aktywna'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oferty_kategorie`
--

CREATE TABLE `oferty_kategorie` (
  `id_oferty` int(11) NOT NULL,
  `id_kategorii` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opinie`
--

CREATE TABLE `opinie` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `tresc` text DEFAULT NULL,
  `ocena` int(11) DEFAULT NULL CHECK (`ocena` between 1 and 5),
  `data_dodania` datetime DEFAULT current_timestamp(),
  `status` enum('oczekujaca','zatwierdzona','odrzucona') DEFAULT 'oczekujaca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `stanowisko` varchar(100) NOT NULL,
  `wynagrodzenie_brutto` decimal(10,2) NOT NULL,
  `wynagrodzenie_netto` decimal(10,2) NOT NULL,
  `data_zatrudnienia` date NOT NULL,
  `data_zakonczenia` date DEFAULT NULL,
  `aktywny` tinyint(1) DEFAULT 1,
  `dodatkowe_informacje` text DEFAULT NULL,
  `dzial` varchar(100) DEFAULT NULL,
  `umowa` enum('umowa_o_prace','umowa_zlecenie','umowa_o_dzielo','kontrakt') DEFAULT 'umowa_o_prace'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `rola` enum('uzytkownik','pracodawca','admin','pracownik') DEFAULT 'uzytkownik',
  `data_rejestracji` datetime DEFAULT current_timestamp(),
  `telefon` varchar(20) DEFAULT NULL,
  `adres` text DEFAULT NULL,
  `miasto` varchar(100) DEFAULT NULL,
  `kraj` varchar(100) DEFAULT 'Polska',
  `kod_pocztowy` varchar(10) DEFAULT NULL,
  `aktywny` tinyint(1) DEFAULT 1,
  `zdjecie_profilowe` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aplikacje`
--
ALTER TABLE `aplikacje`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_uzytkownika` (`id_uzytkownika`,`id_oferty`),
  ADD KEY `id_oferty` (`id_oferty`);

--
-- Indexes for table `finanse`
--
ALTER TABLE `finanse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_oferty` (`id_oferty`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indexes for table `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indexes for table `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oferty`
--
ALTER TABLE `oferty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracodawcy` (`id_pracodawcy`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indexes for table `oferty_kategorie`
--
ALTER TABLE `oferty_kategorie`
  ADD PRIMARY KEY (`id_oferty`,`id_kategorii`),
  ADD KEY `id_kategorii` (`id_kategorii`);

--
-- Indexes for table `opinie`
--
ALTER TABLE `opinie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indexes for table `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aplikacje`
--
ALTER TABLE `aplikacje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finanse`
--
ALTER TABLE `finanse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oferty`
--
ALTER TABLE `oferty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opinie`
--
ALTER TABLE `opinie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aplikacje`
--
ALTER TABLE `aplikacje`
  ADD CONSTRAINT `aplikacje_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aplikacje_ibfk_2` FOREIGN KEY (`id_oferty`) REFERENCES `oferty` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `finanse`
--
ALTER TABLE `finanse`
  ADD CONSTRAINT `finanse_ibfk_1` FOREIGN KEY (`id_oferty`) REFERENCES `oferty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `finanse_ibfk_2` FOREIGN KEY (`id_pracownika`) REFERENCES `pracownicy` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `oferty`
--
ALTER TABLE `oferty`
  ADD CONSTRAINT `oferty_ibfk_1` FOREIGN KEY (`id_pracodawcy`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oferty_ibfk_2` FOREIGN KEY (`id_pracownika`) REFERENCES `pracownicy` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `oferty_kategorie`
--
ALTER TABLE `oferty_kategorie`
  ADD CONSTRAINT `oferty_kategorie_ibfk_1` FOREIGN KEY (`id_oferty`) REFERENCES `oferty` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `oferty_kategorie_ibfk_2` FOREIGN KEY (`id_kategorii`) REFERENCES `kategorie` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opinie`
--
ALTER TABLE `opinie`
  ADD CONSTRAINT `opinie_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD CONSTRAINT `pracownicy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;