<?php
/**
 * Skrypt strony szczegółów oferty pracy
 * 
 * Główne funkcjonalności:
 * - Pobieranie i wyświetlanie szczegółów oferty pracy
 * - Pobieranie informacji o pracodawcy
 * - Wyświetlanie powiązanych kategorii
 * - Pokazywanie liczby złożonych aplikacji
 */

// Ładowanie konfiguracji połączenia z bazą danych
require_once 'config.php';

/**
 * Walidacja parametru ID oferty
 * - Sprawdzenie czy parametr istnieje w URL
 * - Przekierowanie na listę ofert jeśli brak parametru
 */
if (!isset($_GET['id'])) {
    header("Location: lista_ofert.php");
    exit();
}

/**
 * Bezpieczne pobranie ID oferty
 * - Konwersja na liczbę całkowitą (zabezpieczenie przed SQL injection)
 */
$id_oferty = intval($_GET['id']);

// Inicjalizacja zmiennej przechowującej dane oferty
$oferta = null;

/**
 * Pobieranie szczegółów oferty z bazy danych
 * - Użycie prepared statements dla bezpieczeństwa
 * - Łączenie tabel oferty i użytkownicy (pracodawcy)
 */
try {
    $stmt = $conn->prepare("SELECT o.*, u.imie, u.nazwisko, u.email as email_pracodawcy 
                          FROM oferty o 
                          JOIN uzytkownicy u ON o.id_pracodawcy = u.id 
                          WHERE o.id = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Jeśli oferta nie istnieje - przekieruj na listę
    if ($result->num_rows === 0) {
        header("Location: lista_ofert.php");
        exit();
    }
    
    // Pobranie danych oferty jako tablica asocjacyjna
    $oferta = $result->fetch_assoc();
} catch (Exception $e) {
    // Obsługa błędów połączenia z bazą danych
    die("Wystąpił błąd: " . $e->getMessage());
}

// Inicjalizacja tablicy na kategorie oferty
$kategorie = [];

/**
 * Pobieranie kategorii przypisanych do oferty
 * - Wykorzystanie tabeli łączącej oferty_kategorie
 * - Łączenie z tabelą kategorie
 */
try {
    $stmt = $conn->prepare("SELECT k.nazwa 
                           FROM oferty_kategorie ok 
                           JOIN kategorie k ON ok.id_kategorii = k.id 
                           WHERE ok.id_oferty = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Pobieranie wszystkich kategorii do tablicy
    while ($row = $result->fetch_assoc()) {
        $kategorie[] = $row['nazwa'];
    }
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

// Inicjalizacja zmiennej przechowującej liczbę aplikacji
$liczba_aplikacji = 0;

/**
 * Pobieranie liczby złożonych aplikacji na ofertę
 * - Proste zapytanie COUNT
 */
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as liczba FROM aplikacje WHERE id_oferty = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $liczba_aplikacji = $row['liczba'];
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szczegóły oferty - <?= htmlspecialchars($oferta['tytul']) ?></title>
    <!-- Główny arkusz stylów strony -->
    <link rel="stylesheet" href="styleindex.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
    <nav>
        <ul>
            <!-- Podstawowe linki nawigacyjne -->
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <!-- Link do panelu admina widoczny tylko dla administratorów -->
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <!-- Sekcja szczegółów oferty -->
    <section id="szczegoly-oferty">
        <h2><?= htmlspecialchars($oferta['tytul']) ?></h2>
        
        <div class="oferta-szczegoly">
            <!-- Nagłówek oferty z podstawowymi informacjami -->
            <div class="oferta-naglowek">
                <h3><?= htmlspecialchars($oferta['firma']) ?> • <?= htmlspecialchars($oferta['lokalizacja']) ?></h3>
                <p class="data-dodania">Dodano: <?= date('d.m.Y', strtotime($oferta['data_dodania'])) ?></p>
                
                <!-- Wyświetlanie kategorii jeśli istnieją -->
                <?php if (!empty($kategorie)): ?>
                    <div class="kategorie">
                        <span>Kategorie: </span>
                        <?php foreach ($kategorie as $kategoria): ?>
                            <span class="kategoria"><?= htmlspecialchars($kategoria) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Główna treść oferty -->
            <div class="oferta-tresc">
                <h4>Opis stanowiska:</h4>
                <p><?= nl2br(htmlspecialchars($oferta['opis'])) ?></p>
                
                <!-- Sekcja wymagań (opcjonalna) -->
                <?php if (!empty($oferta['wymagania'])): ?>
                    <h4>Wymagania:</h4>
                    <p><?= nl2br(htmlspecialchars($oferta['wymagania'])) ?></p>
                <?php endif; ?>
                
                <!-- Sekcja oferowanych benefitów (opcjonalna) -->
                <?php if (!empty($oferta['oferujemy'])): ?>
                    <h4>Oferujemy:</h4>
                    <p><?= nl2br(htmlspecialchars($oferta['oferujemy'])) ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Dodatkowe informacje -->
            <div class="oferta-dodatkowe">
                <!-- Informacje o pracodawcy -->
                <div class="pracodawca-info">
                    <h4>Informacje o pracodawcy:</h4>
                    <p>Kontakt: <?= htmlspecialchars($oferta['imie'] . ' ' . $oferta['nazwisko']) ?></p>
                    <p>Email: <?= htmlspecialchars($oferta['email_pracodawcy']) ?></p>
                </div>
                
                <!-- Statystyki oferty -->
                <div class="statystyki">
                    <p>Liczba aplikacji: <?= $liczba_aplikacji ?></p>
                </div>
            </div>
            
            <!-- Przyciski akcji -->
            <div class="oferta-akcje">
                <a href="aplikuj.php?id=<?= $id_oferty ?>" class="button">Aplikuj na to stanowisko</a>
                <a href="lista_ofert.php" class="button">Powrót do listy ofert</a>
            </div>
        </div>
    </section>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>