<?php
// Ładujemy plik konfiguracyjny zawierający dane do połączenia z bazą danych
require_once 'config.php';

// Sprawdzamy czy w URL przekazano parametr 'id' (identyfikator oferty)
if (!isset($_GET['id'])) {
    // Jeśli nie ma parametru 'id', przekierowujemy użytkownika na stronę listy ofert
    header("Location: lista_ofert.php");
    // Przerywamy wykonywanie skryptu
    exit();
}

// Pobieramy ID oferty z parametru URL i konwertujemy na liczbę całkowitą (zabezpieczenie)
$id_oferty = intval($_GET['id']);

// Inicjalizujemy zmienną, która będzie przechowywać dane oferty
$oferta = null;

// Pobieranie szczegółów oferty z bazy danych
try {
    // Przygotowujemy zapytanie SQL z parametrem (zabezpieczenie przed SQL injection)
    $stmt = $conn->prepare("SELECT o.*, u.imie, u.nazwisko, u.email as email_pracodawcy 
                          FROM oferty o 
                          JOIN uzytkownicy u ON o.id_pracodawcy = u.id 
                          WHERE o.id = ?");
    
    // Wiążemy parametr (id oferty) z zapytaniem
    $stmt->bind_param("i", $id_oferty);
    
    // Wykonujemy zapytanie
    $stmt->execute();
    
    // Pobieramy wyniki zapytania
    $result = $stmt->get_result();
    
    // Sprawdzamy czy znaleziono ofertę o podanym ID
    if ($result->num_rows === 0) {
        // Jeśli nie znaleziono oferty, przekierowujemy na listę ofert
        header("Location: lista_ofert.php");
        exit();
    }
    
    // Pobieramy dane oferty jako tablicę asocjacyjną
    $oferta = $result->fetch_assoc();
} catch (Exception $e) {
    // W przypadku błędu wyświetlamy komunikat i przerywamy działanie skryptu
    die("Wystąpił błąd: " . $e->getMessage());
}

// Inicjalizujemy tablicę na kategorie oferty
$kategorie = [];

// Pobieranie kategorii dla oferty
try {
    // Przygotowujemy zapytanie do pobrania kategorii
    $stmt = $conn->prepare("SELECT k.nazwa 
                           FROM oferty_kategorie ok 
                           JOIN kategorie k ON ok.id_kategorii = k.id 
                           WHERE ok.id_oferty = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Pobieramy wszystkie kategorie i dodajemy do tablicy
    while ($row = $result->fetch_assoc()) {
        $kategorie[] = $row['nazwa'];
    }
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

// Inicjalizujemy zmienną na liczbę aplikacji
$liczba_aplikacji = 0;

// Pobieranie liczby aplikacji dla tej oferty
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
    <link rel="stylesheet" href="styleindex.css">
</head>
<body>

<header>
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
    <nav>
        <ul>
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
        </ul>
    </nav>
</header>

<main>
    <section id="szczegoly-oferty">
        <h2><?= htmlspecialchars($oferta['tytul']) ?></h2>
        
        <div class="oferta-szczegoly">
            <div class="oferta-naglowek">
                <h3><?= htmlspecialchars($oferta['firma']) ?> • <?= htmlspecialchars($oferta['lokalizacja']) ?></h3>
                <p class="data-dodania">Dodano: <?= date('d.m.Y', strtotime($oferta['data_dodania'])) ?></p>
                <?php if (!empty($kategorie)): ?>
                    <div class="kategorie">
                        <span>Kategorie: </span>
                        <?php foreach ($kategorie as $kategoria): ?>
                            <span class="kategoria"><?= htmlspecialchars($kategoria) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="oferta-tresc">
                <h4>Opis stanowiska:</h4>
                <p><?= nl2br(htmlspecialchars($oferta['opis'])) ?></p>
                
                <?php if (!empty($oferta['wymagania'])): ?>
                    <h4>Wymagania:</h4>
                    <p><?= nl2br(htmlspecialchars($oferta['wymagania'])) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($oferta['oferujemy'])): ?>
                    <h4>Oferujemy:</h4>
                    <p><?= nl2br(htmlspecialchars($oferta['oferujemy'])) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="oferta-dodatkowe">
                <div class="pracodawca-info">
                    <h4>Informacje o pracodawcy:</h4>
                    <p>Kontakt: <?= htmlspecialchars($oferta['imie'] . ' ' . $oferta['nazwisko']) ?></p>
                    <p>Email: <?= htmlspecialchars($oferta['email_pracodawcy']) ?></p>
                </div>
                
                <div class="statystyki">
                    <p>Liczba aplikacji: <?= $liczba_aplikacji ?></p>
                </div>
            </div>
            
            <div class="oferta-akcje">
                <a href="aplikuj.php?id=<?= $id_oferty ?>" class="button">Aplikuj na to stanowisko</a>
                <a href="lista_ofert.php" class="button">Powrót do listy ofert</a>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
