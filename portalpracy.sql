-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 18, 2025 at 07:21 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portalpracy`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `aplikacje`
--

CREATE TABLE `aplikacje` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_oferty` int(11) NOT NULL,
  `wiadomosc` text DEFAULT NULL,
  `cv_plik` varchar(255) DEFAULT NULL,
  `list_plik` varchar(255) DEFAULT NULL,
  `data_aplikacji` datetime DEFAULT current_timestamp(),
  `status` enum('złożona','w recenzji','odrzucona','zaakceptowana','w trakcie rozmowy') DEFAULT 'złożona',
  `data_aktualizacji` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aplikacje`
--

INSERT INTO `aplikacje` (`id`, `id_uzytkownika`, `id_oferty`, `wiadomosc`, `cv_plik`, `list_plik`, `data_aplikacji`, `status`, `data_aktualizacji`) VALUES
(1, 1, 1, 'Mam 5-letnie doświadczenie w PHP i chętnie dołączę do zespołu.', NULL, NULL, '2024-05-10 10:00:00', 'w trakcie rozmowy', '2024-05-12 14:30:00'),
(2, 4, 1, 'Interesuje mnie stanowisko pomimo braku doświadczenia komercyjnego.', NULL, NULL, '2024-05-10 11:30:00', 'odrzucona', '2024-05-11 09:15:00'),
(3, 1, 2, NULL, NULL, NULL, '2024-05-11 12:45:00', 'złożona', NULL),
(4, 4, 3, 'Mam wykształcenie medyczne i chciałabym zmienić branżę.', NULL, NULL, '2024-05-12 14:20:00', 'w recenzji', '2024-05-15 10:20:00'),
(5, 1, 5, 'Posiadam doświadczenie w zarządzaniu małymi zespołami.', NULL, NULL, '2024-05-14 15:40:00', 'zaakceptowana', '2024-05-20 11:45:00'),
(6, 3, 4, 'Proszę o odpowiedz', 'cv_3_6829e429019cd.pdf', 'list_3_6829e42901ba2.pdf', '2025-05-18 15:44:09', 'złożona', NULL),
(7, 3, 1, 'Prosze bardzo o przyjęcie', 'cv_3_6829e5c879621.pdf', 'list_3_6829e5c8797e3.pdf', '2025-05-18 15:51:04', 'złożona', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazwa`) VALUES
(2, 'Budownictwo'),
(4, 'Edukacja'),
(5, 'Handel'),
(1, 'IT'),
(3, 'Medycyna');

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

--
-- Dumping data for table `kontakt`
--

INSERT INTO `kontakt` (`id`, `imie`, `email`, `temat`, `wiadomosc`, `data_wyslania`) VALUES
(1, 'Adam Nowak', 'adam.nowak@example.com', 'Problem z kontem', 'Nie mogę zalogować się na swoje konto.', '2024-05-20 09:30:00'),
(2, 'Ewa Kowalska', 'ewa.kowalska@example.com', 'Pytanie o ofertę', 'Czy oferta dla programisty jest nadal aktualna?', '2024-05-21 10:45:00'),
(3, 'Tomasz Wiśniewski', 'tomasz.wisniewski@example.com', 'Współpraca', 'Chciałbym zapytać o możliwość współpracy.', '2024-05-22 12:00:00'),
(4, 'Magdalena Zielińska', 'magdalena.zielinska@example.com', 'Błąd w systemie', 'Wystąpił błąd podczas dodawania oferty.', '2024-05-23 14:15:00'),
(5, 'Robert Lewandowski', 'robert.lewandowski@example.com', 'Reklamacja', 'Moja aplikacja nie została rozpatrzona.', '2024-05-24 16:30:00');

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
  `wynagrodzenie_min` decimal(10,2) DEFAULT NULL,
  `wynagrodzenie_max` decimal(10,2) DEFAULT NULL,
  `typ_pracy` enum('pełny etat','część etatu','tymczasowa','staż','praktyka','freelance') DEFAULT 'pełny etat',
  `zdalna` tinyint(1) DEFAULT 0,
  `termin_aplikacji` date DEFAULT NULL,
  `kategoria` varchar(100) DEFAULT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  `id_pracodawcy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oferty`
--

INSERT INTO `oferty` (`id`, `tytul`, `opis`, `firma`, `lokalizacja`, `wynagrodzenie_min`, `wynagrodzenie_max`, `typ_pracy`, `zdalna`, `termin_aplikacji`, `kategoria`, `data_dodania`, `id_pracodawcy`) VALUES
(1, 'Programista PHP', 'Poszukujemy doświadczonego programisty PHP do pracy nad projektami e-commerce.', 'TechSolutions Sp. z o.o.', 'Warszawa', 8000.00, 12000.00, 'pełny etat', 1, '2024-07-15', 'IT', '2024-05-10 09:00:00', 2),
(2, 'Murarz', 'Praca na budowie przy wznoszeniu budynków mieszkalnych.', 'Budimex SA', 'Kraków', 5000.00, 7000.00, 'pełny etat', 0, '2024-06-30', 'Budownictwo', '2024-05-11 10:30:00', 5),
(3, 'Pielęgniarka', 'Praca na oddziale internistycznym w szpitalu miejskim.', 'Szpital Miejski', 'Gdańsk', 5500.00, 6500.00, 'pełny etat', 0, '2024-07-10', 'Medycyna', '2024-05-12 11:45:00', 2),
(4, 'Nauczyciel matematyki', 'Prowadzenie zajęć z matematyki w liceum ogólnokształcącym.', 'Liceum Ogólnokształcące nr 1', 'Poznań', 4500.00, 5500.00, 'pełny etat', 0, '2024-08-20', 'Edukacja', '2024-05-13 13:15:00', 5),
(5, 'Kierownik sklepu', 'Zarządzanie zespołem w sklepie spożywczym.', 'SuperMarket', 'Wrocław', 6000.00, 8000.00, 'pełny etat', 0, '2024-07-05', 'Handel', '2024-05-14 14:30:00', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oferty_kategorie`
--

CREATE TABLE `oferty_kategorie` (
  `id_oferty` int(11) NOT NULL,
  `id_kategorii` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `oferty_kategorie`
--

INSERT INTO `oferty_kategorie` (`id_oferty`, `id_kategorii`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

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

--
-- Dumping data for table `opinie`
--

INSERT INTO `opinie` (`id`, `id_uzytkownika`, `tresc`, `ocena`, `data_dodania`) VALUES
(1, 1, 'Świetny portal, znalazłem pracę w ciągu tygodnia!', 5, '2024-05-15 09:10:00'),
(2, 4, 'Bardzo pomocna obsługa, polecam.', 4, '2024-05-16 10:25:00'),
(3, 1, 'Niektóre oferty są nieaktualne, ale ogólnie OK.', 3, '2024-05-17 11:40:00'),
(4, 4, 'Prosty w obsłudze i skuteczny.', 5, '2024-05-18 13:55:00'),
(5, 1, 'Można znaleźć ciekawe oferty pracy.', 4, '2024-05-19 15:05:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `powiadomienia`
--

CREATE TABLE `powiadomienia` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `tytul` varchar(255) NOT NULL,
  `tresc` text NOT NULL,
  `typ` enum('system','aplikacja','wiadomosc','oferta') NOT NULL,
  `przeczytana` tinyint(1) DEFAULT 0,
  `data_wyslania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `powiadomienia`
--

INSERT INTO `powiadomienia` (`id`, `id_uzytkownika`, `tytul`, `tresc`, `typ`, `przeczytana`, `data_wyslania`) VALUES
(1, 1, 'Nowa oferta pracy', 'Dodano nową ofertę: Kierownik sklepu', 'oferta', 1, '2024-05-14 14:35:00'),
(2, 1, 'Status aplikacji', 'Twoja aplikacja na stanowisko Programista PHP została zaakceptowana', 'aplikacja', 1, '2024-05-12 14:35:00'),
(3, 4, 'Nowa wiadomość', 'Masz nową wiadomość od pracodawcy', 'wiadomosc', 0, '2024-05-16 09:20:00'),
(4, 2, 'Nowa aplikacja', 'Nowa aplikacja na stanowisko Programista PHP', 'aplikacja', 1, '2024-05-10 10:05:00'),
(5, 5, 'Aktualizacja systemu', 'Planowane prace serwisowe w nocy', 'system', 0, '2024-05-25 22:00:00'),
(6, 5, 'Nowa aplikacja na stanowisko: Nauczyciel matematyki', 'Nowa aplikacja na stanowisko: Nauczyciel matematyki', 'aplikacja', 0, '2025-05-18 15:44:09'),
(7, 2, 'Nowa aplikacja na stanowisko: Programista PHP', 'Nowa aplikacja na stanowisko: Programista PHP', 'aplikacja', 0, '2025-05-18 15:51:04');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ulubione_oferty`
--

CREATE TABLE `ulubione_oferty` (
  `id_uzytkownika` int(11) NOT NULL,
  `id_oferty` int(11) NOT NULL,
  `data_dodania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulubione_oferty`
--

INSERT INTO `ulubione_oferty` (`id_uzytkownika`, `id_oferty`, `data_dodania`) VALUES
(1, 3, '2024-05-12 12:00:00'),
(4, 1, '2024-05-10 09:45:00'),
(4, 4, '2024-05-13 14:30:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `umiejetnosci`
--

CREATE TABLE `umiejetnosci` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `umiejetnosci`
--

INSERT INTO `umiejetnosci` (`id`, `nazwa`) VALUES
(3, 'HTML/CSS'),
(2, 'JavaScript'),
(5, 'Murarstwo'),
(4, 'MySQL'),
(7, 'Nauczanie'),
(1, 'PHP'),
(6, 'Pielęgniarstwo'),
(8, 'Zarządzanie zespołem');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `zdjecie_profilowe` varchar(255) DEFAULT NULL,
  `haslo` varchar(255) NOT NULL,
  `opis` text DEFAULT NULL,
  `rola` enum('uzytkownik','pracodawca','admin') DEFAULT 'uzytkownik',
  `aktywny` tinyint(1) DEFAULT 1,
  `data_rejestracji` datetime DEFAULT current_timestamp(),
  `ostatnie_logowanie` datetime DEFAULT NULL,
  `pensja` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `imie`, `nazwisko`, `email`, `telefon`, `zdjecie_profilowe`, `haslo`, `opis`, `rola`, `aktywny`, `data_rejestracji`, `ostatnie_logowanie`, `pensja`) VALUES
(1, 'Maciek', 'Kowalski', 'jan.kowalski@example.com', '+48123456789', NULL, 'password', 'Programista z 5-letnim doświadczeniem w PHP i JavaScript', 'uzytkownik', 1, '2024-05-01 10:00:00', '2024-06-28 09:15:00', 9500.00),
(2, 'Anna', 'Nowak', 'anna.nowak@example.com', '+48987654321', NULL, 'password', 'HR w TechSolutions Sp. z o.o.', 'pracodawca', 1, '2024-05-02 11:15:00', '2024-06-28 10:30:00', 12000.00),
(3, 'Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', '+48111222333', NULL, 'password', 'Administrator systemu', 'admin', 1, '2024-05-03 09:30:00', '2024-06-28 08:45:00', 15000.00),
(4, 'Katarzyna', 'Wójcik', 'katarzyna.wojcik@example.com', '+48444555666', NULL, 'password', 'Nauczyciel matematyki poszukujący nowych wyzwań', 'uzytkownik', 1, '2024-05-04 14:45:00', '2024-06-27 16:20:00', 5000.00),
(5, 'Marek', 'Kowalczyk', 'marek.kowalczyk@example.com', '+48777888999', NULL, 'password', 'Kierownik ds. rekrutacji w Budimex SA', 'pracodawca', 1, '2024-05-05 16:20:00', '2024-06-28 11:10:00', 11000.00),
(9, 'Jan', 'Kowalski', 'janek.kowalski@example.com', '+48929257389', NULL, 'jan123', 'Właściciel', 'admin', 1, '2025-05-18 17:13:37', '2025-05-15 19:20:25', 18000.00),
(10, 'Anna', 'Nowak', 'anina.nowak@example.com', '+48129836547', NULL, 'anna123', 'HR w TechSolutions Sp. z o.o.', 'pracodawca', 1, '2025-05-18 17:13:37', '2025-05-01 19:20:31', 12500.00),
(11, 'Piotr', 'Zalewski', 'piotrek.zalewski@example.com', '+48723679604', NULL, 'piotr123', 'Poszukujący pracy nierób', 'uzytkownik', 1, '2025-05-18 17:13:37', '2025-05-29 19:20:38', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy_umiejetnosci`
--

CREATE TABLE `uzytkownicy_umiejetnosci` (
  `id_uzytkownika` int(11) NOT NULL,
  `id_umiejetnosci` int(11) NOT NULL,
  `poziom` enum('podstawowy','średni','zaawansowany','ekspert') DEFAULT 'średni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy_umiejetnosci`
--

INSERT INTO `uzytkownicy_umiejetnosci` (`id_uzytkownika`, `id_umiejetnosci`, `poziom`) VALUES
(1, 1, 'zaawansowany'),
(1, 2, 'średni'),
(1, 3, 'zaawansowany'),
(1, 4, 'zaawansowany'),
(4, 7, 'ekspert');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wiadomosci`
--

CREATE TABLE `wiadomosci` (
  `id` int(11) NOT NULL,
  `id_nadawcy` int(11) NOT NULL,
  `id_odbiorcy` int(11) NOT NULL,
  `temat` varchar(255) DEFAULT NULL,
  `tresc` text NOT NULL,
  `przeczytana` tinyint(1) DEFAULT 0,
  `data_wyslania` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wiadomosci`
--

INSERT INTO `wiadomosci` (`id`, `id_nadawcy`, `id_odbiorcy`, `temat`, `tresc`, `przeczytana`, `data_wyslania`) VALUES
(1, 2, 1, 'Zaproszenie na rozmowę', 'Dziękujemy za aplikację. Zapraszamy na rozmowę 15.05 o 10:00.', 1, '2024-05-12 14:30:00'),
(2, 5, 4, 'Pytanie o kwalifikacje', 'Proszę o przesłanie dodatkowych informacji o swoim wykształceniu medycznym.', 0, '2024-05-16 09:20:00'),
(3, 3, 1, 'Witamy w serwisie', 'Dziękujemy za rejestrację w naszym portalu!', 1, '2024-05-01 10:05:00'),
(4, 1, 2, 'Pytanie o ofertę', 'Czy możliwe jest zdalne wykonywanie pracy?', 1, '2024-05-10 15:20:00'),
(5, 4, 5, 'Aplikacja na stanowisko', 'Zainteresowała mnie oferta pracy w szkole.', 0, '2024-05-13 16:45:00');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `aplikacje`
--
ALTER TABLE `aplikacje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
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
-- Indeksy dla tabeli `powiadomienia`
--
ALTER TABLE `powiadomienia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indeksy dla tabeli `ulubione_oferty`
--
ALTER TABLE `ulubione_oferty`
  ADD PRIMARY KEY (`id_uzytkownika`,`id_oferty`),
  ADD KEY `id_oferty` (`id_oferty`);

--
-- Indeksy dla tabeli `umiejetnosci`
--
ALTER TABLE `umiejetnosci`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `uzytkownicy_umiejetnosci`
--
ALTER TABLE `uzytkownicy_umiejetnosci`
  ADD PRIMARY KEY (`id_uzytkownika`,`id_umiejetnosci`),
  ADD KEY `id_umiejetnosci` (`id_umiejetnosci`);

--
-- Indeksy dla tabeli `wiadomosci`
--
ALTER TABLE `wiadomosci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nadawcy` (`id_nadawcy`),
  ADD KEY `id_odbiorcy` (`id_odbiorcy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aplikacje`
--
ALTER TABLE `aplikacje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `powiadomienia`
--
ALTER TABLE `powiadomienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `umiejetnosci`
--
ALTER TABLE `umiejetnosci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- === RELACJE (FOREIGN KEYS) ===

ALTER TABLE aplikacje
ADD CONSTRAINT fk_aplikacje_uzytkownik
FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE aplikacje
ADD CONSTRAINT fk_aplikacje_oferta
FOREIGN KEY (id_oferty) REFERENCES oferty(id)
ON DELETE CASCADE;

ALTER TABLE oferty
ADD CONSTRAINT fk_oferty_pracodawca
FOREIGN KEY (id_pracodawcy) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE oferty_kategorie
ADD CONSTRAINT fk_oferty_kategorie_oferta
FOREIGN KEY (id_oferty) REFERENCES oferty(id)
ON DELETE CASCADE;

ALTER TABLE oferty_kategorie
ADD CONSTRAINT fk_oferty_kategorie_kategoria
FOREIGN KEY (id_kategorii) REFERENCES kategorie(id)
ON DELETE CASCADE;

ALTER TABLE opinie
ADD CONSTRAINT fk_opinie_uzytkownik
FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE powiadomienia
ADD CONSTRAINT fk_powiadomienia_uzytkownik
FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE ulubione_oferty
ADD CONSTRAINT fk_ulubione_uzytkownik
FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE ulubione_oferty
ADD CONSTRAINT fk_ulubione_oferta
FOREIGN KEY (id_oferty) REFERENCES oferty(id)
ON DELETE CASCADE;

ALTER TABLE uzytkownicy_umiejetnosci
ADD CONSTRAINT fk_umiejetnosci_uzytkownik
FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE uzytkownicy_umiejetnosci
ADD CONSTRAINT fk_umiejetnosci_umiejetnosc
FOREIGN KEY (id_umiejetnosci) REFERENCES umiejetnosci(id)
ON DELETE CASCADE;

ALTER TABLE wiadomosci
ADD CONSTRAINT fk_wiadomosci_nadawca
FOREIGN KEY (id_nadawcy) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

ALTER TABLE wiadomosci
ADD CONSTRAINT fk_wiadomosci_odbiorca
FOREIGN KEY (id_odbiorcy) REFERENCES uzytkownicy(id)
ON DELETE CASCADE;

