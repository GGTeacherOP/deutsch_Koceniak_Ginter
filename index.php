<?php
session_start();
$user_id = $_SESSION['user_id'];
$user_data = [];
/**
 * Skrypt strony głównej portalu z ofertami pracy
 * 
 * Funkcjonalności:
 * - Pobieranie losowych ofert pracy
 * - Pobieranie najnowszych opinii użytkowników
 * - Wyszukiwarka ofert
 * - Wyświetlanie slidera z opiniami
 */

// Dołączenie pliku konfiguracyjnego z danymi połączenia do bazy
require_once 'config.php';

/**
 * Pobieranie 2 losowych ofert pracy
 * Zapytanie wykorzystuje ORDER BY RAND() do losowego wyboru rekordów
 */
$popularne_oferty = [];
try {
    $stmt = $conn->prepare("SELECT o.id, o.tytul, o.firma, o.lokalizacja, o.opis 
                           FROM oferty o 
                           ORDER BY RAND() LIMIT 2");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $popularne_oferty[] = $row;
    }
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

/**
 * Pobieranie 3 najnowszych opinii użytkowników
 * Opinie są łączone z tabelą użytkowników, aby pobrać dane autora
 */
$opinie = [];
try {
    $stmt = $conn->prepare("SELECT o.tresc, u.imie, u.nazwisko 
                           FROM opinie o
                           JOIN uzytkownicy u ON o.id_uzytkownika = u.id
                           ORDER BY o.data_dodania DESC LIMIT 3");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $opinie[] = $row;
    }
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal z ofertami pracy w Niemczech</title>
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny arkusz stylów -->
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <script>
    /**
     * Funkcja obsługująca wyszukiwanie ofert
     * Przekierowuje do listy ofert z parametrami wyszukiwania
     */
    function submitSearch() {
        const keyword = document.getElementById('search-keyword').value;
        const location = document.getElementById('search-location').value;
        
        // Przekieruj tylko jeśli któryś z filtrów jest wypełniony
        if (keyword || location) {
            window.location.href = `lista_ofert.php?keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`;
        } else {
            // Jeśli żaden filtr nie jest wypełniony, pokaż wszystkie oferty
            window.location.href = 'lista_ofert.php';
        }
    }
    </script>
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
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
            <li><a href="opinie.php">opinie</a></li>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
            <?php endif; ?>
                        <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'pracodawca'): ?>
            <li><a href="panel_pracodawcy.php">panel pracodawcy</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <!-- Sekcja wyszukiwarki ofert -->
    <section id="wyszukiwarka">
        <h2>Wyszukiwarka ofert</h2>
        <div class="search-form">
            <input type="text" id="search-keyword" placeholder="Stanowisko lub firma">
            <input type="text" id="search-location" placeholder="Miasto, region">
            <button onclick="submitSearch()">Szukaj</button>
        </div>
    </section>

    <!-- Sekcja popularnych ofert -->
    <section id="popularne-oferty">
        <h2>Popularne oferty</h2>
        <?php foreach ($popularne_oferty as $oferta): ?>
            <div class="oferta">
                <h3><?= htmlspecialchars($oferta['tytul']) ?> - <?= htmlspecialchars($oferta['lokalizacja']) ?></h3>
                <p><?= htmlspecialchars($oferta['firma']) ?> • <?= htmlspecialchars(substr($oferta['opis'], 0, 100)) ?>...</p>
                <a href="oferta_szczegoly.php?id=<?= $oferta['id'] ?>">Zobacz szczegóły</a>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Sekcja opinii użytkowników -->
    <section id="opinie">
        <h2>Opinie użytkowników</h2>
        <div class="slider-container">
            <?php foreach ($opinie as $index => $opinia): ?>
                <div class="opinia <?= $index === 0 ? 'aktywna' : '' ?>">
                    <blockquote>"<?= htmlspecialchars($opinia['tresc']) ?>"</blockquote>
                    <footer>– <?= htmlspecialchars($opinia['imie'] . ' ' . substr($opinia['nazwisko'], 0, 1)) ?>.</footer>
                </div>
            <?php endforeach; ?>
            <div class="slider-nav">
                <button id="poprzednia">Poprzednia</button>
                <button id="nastepna">Następna</button>
            </div>
        </div>
    </section>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
/**
 * Skrypt do obsługi slidera opinii
 * - Przełączanie między opiniami
 * - Automatyczne przewijanie
 */
document.addEventListener('DOMContentLoaded', function() {
    const opinie = document.querySelectorAll('.opinia');
    let currentIndex = 0;
    
    // Funkcja pokazująca wybraną opinię
    function showOpinion(index) {
        opinie.forEach(opinia => opinia.classList.remove('aktywna'));
        opinie[index].classList.add('aktywna');
        currentIndex = index;
    }
    
    // Obsługa przycisku "Następna"
    document.getElementById('nastepna').addEventListener('click', function() {
        const nextIndex = (currentIndex + 1) % opinie.length;
        showOpinion(nextIndex);
    });
    
    // Obsługa przycisku "Poprzednia"
    document.getElementById('poprzednia').addEventListener('click', function() {
        const prevIndex = (currentIndex - 1 + opinie.length) % opinie.length;
        showOpinion(prevIndex);
    });
    
    // Automatyczne przewijanie co 5 sekund
    setInterval(function() {
        const nextIndex = (currentIndex + 1) % opinie.length;
        showOpinion(nextIndex);
    }, 5000);
});
</script>

</body>
</html>