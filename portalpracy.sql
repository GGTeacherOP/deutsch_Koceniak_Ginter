-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2024 at 12:45 PM
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
-- Struktura tabeli dla tabeli `aplikacje`
--

CREATE TABLE `aplikacje` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_oferty` int(11) NOT NULL,
  `data_aplikacji` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kontakt`
--

CREATE TABLE `kontakt` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `temat` varchar(255) DEFAULT NULL,
  `wiadomosc` text NOT NULL,
  `data_wyslania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oferty`
--

CREATE TABLE `oferty` (
  `id` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `opis` text NOT NULL,
  `firma` varchar(255) NOT NULL,
  `lokalizacja` varchar(255) DEFAULT NULL,
  `kategoria` varchar(100) DEFAULT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  `id_pracodawcy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oferty_kategorie`
--

CREATE TABLE `oferty_kategorie` (
  `id_oferty` int(11) NOT NULL,
  `id_kategorii` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `opinie`
--

CREATE TABLE `opinie` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `tresc` text DEFAULT NULL,
  `ocena` int(11) DEFAULT NULL CHECK (`ocena` between 1 and 5),
  `data_dodania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `rola` enum('uzytkownik','pracodawca','admin') DEFAULT 'uzytkownik',
  `data_rejestracji` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Wstawianie danych do tabeli `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `imie`, `nazwisko`, `email`, `haslo`, `rola`, `data_rejestracji`) VALUES
(1, 'Jan', 'Kowalski', 'jan.kowalski@example.com', 'password', 'uzytkownik', '2024-05-01 10:00:00'),
(2, 'Anna', 'Nowak', 'anna.nowak@example.com', 'password', 'pracodawca', '2024-05-02 11:15:00'),
(3, 'Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', 'password', 'admin', '2024-05-03 09:30:00'),
(4, 'Katarzyna', 'Wójcik', 'katarzyna.wojcik@example.com', 'password', 'uzytkownik', '2024-05-04 14:45:00'),
(5, 'Marek', 'Kowalczyk', 'marek.kowalczyk@example.com', 'password', 'pracodawca', '2024-05-05 16:20:00');

--
-- Wstawianie danych do tabeli `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazwa`) VALUES
(1, 'IT'),
(2, 'Budownictwo'),
(3, 'Medycyna'),
(4, 'Edukacja'),
(5, 'Handel');

--
-- Wstawianie danych do tabeli `oferty`
--

INSERT INTO `oferty` (`id`, `tytul`, `opis`, `firma`, `lokalizacja`, `kategoria`, `data_dodania`, `id_pracodawcy`) VALUES
(1, 'Programista PHP', 'Poszukujemy doświadczonego programisty PHP do pracy nad projektami e-commerce.', 'TechSolutions Sp. z o.o.', 'Warszawa', 'IT', '2024-05-10 09:00:00', 2),
(2, 'Murarz', 'Praca na budowie przy wznoszeniu budynków mieszkalnych.', 'Budimex SA', 'Kraków', 'Budownictwo', '2024-05-11 10:30:00', 5),
(3, 'Pielęgniarka', 'Praca na oddziale internistycznym w szpitalu miejskim.', 'Szpital Miejski', 'Gdańsk', 'Medycyna', '2024-05-12 11:45:00', 2),
(4, 'Nauczyciel matematyki', 'Prowadzenie zajęć z matematyki w liceum ogólnokształcącym.', 'Liceum Ogólnokształcące nr 1', 'Poznań', 'Edukacja', '2024-05-13 13:15:00', 5),
(5, 'Kierownik sklepu', 'Zarządzanie zespołem w sklepie spożywczym.', 'SuperMarket', 'Wrocław', 'Handel', '2024-05-14 14:30:00', 2);

--
-- Wstawianie danych do tabeli `oferty_kategorie`
--

INSERT INTO `oferty_kategorie` (`id_oferty`, `id_kategorii`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

--
-- Wstawianie danych do tabeli `aplikacje`
--

INSERT INTO `aplikacje` (`id`, `id_uzytkownika`, `id_oferty`, `data_aplikacji`) VALUES
(1, 1, 1, '2024-05-10 10:00:00'),
(2, 4, 1, '2024-05-10 11:30:00'),
(3, 1, 2, '2024-05-11 12:45:00'),
(4, 4, 3, '2024-05-12 14:20:00'),
(5, 1, 5, '2024-05-14 15:40:00');

--
-- Wstawianie danych do tabeli `opinie`
--

INSERT INTO `opinie` (`id`, `id_uzytkownika`, `tresc`, `ocena`, `data_dodania`) VALUES
(1, 1, 'Świetny portal, znalazłem pracę w ciągu tygodnia!', 5, '2024-05-15 09:10:00'),
(2, 4, 'Bardzo pomocna obsługa, polecam.', 4, '2024-05-16 10:25:00'),
(3, 1, 'Niektóre oferty są nieaktualne, ale ogólnie OK.', 3, '2024-05-17 11:40:00'),
(4, 4, 'Prosty w obsłudze i skuteczny.', 5, '2024-05-18 13:55:00'),
(5, 1, 'Można znaleźć ciekawe oferty pracy.', 4, '2024-05-19 15:05:00');

--
-- Wstawianie danych do tabeli `kontakt`
--

INSERT INTO `kontakt` (`id`, `imie`, `email`, `temat`, `wiadomosc`, `data_wyslania`) VALUES
(1, 'Adam Nowak', 'adam.nowak@example.com', 'Problem z kontem', 'Nie mogę zalogować się na swoje konto.', '2024-05-20 09:30:00'),
(2, 'Ewa Kowalska', 'ewa.kowalska@example.com', 'Pytanie o ofertę', 'Czy oferta dla programisty jest nadal aktualna?', '2024-05-21 10:45:00'),
(3, 'Tomasz Wiśniewski', 'tomasz.wisniewski@example.com', 'Współpraca', 'Chciałbym zapytać o możliwość współpracy.', '2024-05-22 12:00:00'),
(4, 'Magdalena Zielińska', 'magdalena.zielinska@example.com', 'Błąd w systemie', 'Wystąpił błąd podczas dodawania oferty.', '2024-05-23 14:15:00'),
(5, 'Robert Lewandowski', 'robert.lewandowski@example.com', 'Reklamacja', 'Moja aplikacja nie została rozpatrzona.', '2024-05-24 16:30:00');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `aplikacje`
--
ALTER TABLE `aplikacje`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_uzytkownika` (`id_uzytkownika`,`id_oferty`),
  ADD KEY `id_oferty` (`id_oferty`);

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `oferty`
--
ALTER TABLE `oferty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pracodawcy` (`id_pracodawcy`);

--
-- Indeksy dla tabeli `oferty_kategorie`
--
ALTER TABLE `oferty_kategorie`
  ADD PRIMARY KEY (`id_oferty`,`id_kategorii`),
  ADD KEY `id_kategorii` (`id_kategorii`);

--
-- Indeksy dla tabeli `opinie`
--
ALTER TABLE `opinie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indeksy dla tabeli `uzytkownicy`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `oferty`
--
ALTER TABLE `oferty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `opinie`
--
ALTER TABLE `opinie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `oferty`
--
ALTER TABLE `oferty`
  ADD CONSTRAINT `oferty_ibfk_1` FOREIGN KEY (`id_pracodawcy`) REFERENCES `uzytkownicy` (`id`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;