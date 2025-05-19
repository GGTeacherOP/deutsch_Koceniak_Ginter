<?php
// Rozpoczęcie sesji i załączenie konfiguracji
session_start();
require_once 'config.php';

// Sprawdzenie czy przekazano ID oferty i czy użytkownik jest zalogowany
if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: lista_ofert.php");
    exit();
}

// Pobranie i zabezpieczenie ID oferty
$id_oferty = intval($_GET['id']);

// Pobieranie informacji o ofercie z bazy danych
try {
    $stmt = $conn->prepare("SELECT tytul FROM oferty WHERE id = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    $oferta = $result->fetch_assoc();
    
    // Jeśli oferta nie istnieje - przekieruj
    if (!$oferta) {
        header("Location: lista_ofert.php");
        exit();
    }
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Meta dane i tytuł strony -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie aplikacji - <?= htmlspecialchars($oferta['tytul']) ?></title>
    <!-- Link do arkusza stylów -->
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
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <!-- Warunkowe wyświetlanie linków w zależności od statusu logowania -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="wyloguj.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <?php endif; ?>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <li><a href="opinie.php">opinie</a></li>
            <!-- Link do panelu admina widoczny tylko dla administratorów -->
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <section id="potwierdzenie-aplikacji">
        <h2>Potwierdzenie aplikacji</h2>
        
        <!-- Komunikat potwierdzający wysłanie aplikacji -->
        <div class="success-box">
            <p>Twoja aplikacja na stanowisko <strong><?= htmlspecialchars($oferta['tytul']) ?></strong> została wysłana pomyślnie!</p>
            <p>Pracodawca został powiadomiony o Twojej aplikacji. Możesz śledzić status aplikacji w sekcji <a href="konto.php">Moje konto</a>.</p>
        </div>
        
        <!-- Przyciski akcji -->
        <div class="actions">
            <a href="oferta_szczegoly.php?id=<?= $id_oferty ?>" class="button">Powrót do oferty</a>
            <a href="lista_ofert.php" class="button">Przeglądaj więcej ofert</a>
            <a href="konto.php" class="button">Moje aplikacje</a>
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