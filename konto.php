<?php
/**
 * Skrypt panelu użytkownika
 * 
 * Funkcjonalności:
 * - Weryfikacja zalogowania użytkownika
 * - Pobieranie danych użytkownika z bazy
 * - Pobieranie historii aplikacji użytkownika
 * - Obsługa wylogowania
 */

// Rozpoczęcie sesji i załadowanie konfiguracji
session_start();
require_once 'config.php';

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header('Location: logowanie.php');
    exit();
}

// Pobranie ID użytkownika z sesji
$user_id = $_SESSION['user_id'];
$user_data = [];

/**
 * Pobranie danych użytkownika z bazy
 * Zabezpieczenie przed SQL injection poprzez prepared statements
 */
try {
    $stmt = $conn->prepare("SELECT imie, nazwisko, email, rola FROM uzytkownicy WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
    } else {
        // Jeśli nie znaleziono użytkownika, wyczyść sesję i przekieruj
        session_destroy();
        header('Location: logowanie.php');
        exit();
    }
    $stmt->close();
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

/**
 * Pobranie historii aplikacji użytkownika
 * Łączenie z tabelą ofert, aby pobrać tytuły
 */
$applications = [];
try {
    $stmt = $conn->prepare("SELECT a.id, o.tytul, a.data_aplikacji, a.status 
                           FROM aplikacje a 
                           JOIN oferty o ON a.id_oferty = o.id 
                           WHERE a.id_uzytkownika = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

// Obsługa wylogowania
if (isset($_GET['wyloguj'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje konto – Portal z ofertami pracy w Niemczech</title>
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny arkusz stylów -->
    <link rel="stylesheet" href="konto.css"> <!-- Dodatkowe style dla panelu konta -->
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
            <li><a href="konto.php" class="active">Moje konto</a></li>
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
    <!-- Sekcja danych osobowych i ustawień konta -->
    <section class="konto-panel">
        <div class="dane-osobowe">
            <h3>Dane osobowe</h3>
            <p><strong>Imię:</strong> <?= htmlspecialchars($user_data['imie']) ?></p>
            <p><strong>Nazwisko:</strong> <?= htmlspecialchars($user_data['nazwisko']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
            <p class="rola-info">Typ konta: <?= $user_data['rola'] === 'pracodawca' ? 'Pracodawca' : 'Użytkownik' ?></p>
        </div>

        <div class="ustawienia-konta">
            <h3>Ustawienia konta</h3>
            <ul>
                <li><a href="zmiana_hasla.php">Zmień hasło</a></li>
                <li><a href="edytuj_dane.php">Zaktualizuj dane</a></li>
                <li><a href="?wyloguj=1">Wyloguj się</a></li>
            </ul>
        </div>
    </section>

    <!-- Sekcja historii aplikacji -->
    <section id="aplikacje">
        <h2>Moje aplikacje</h2>
        <?php if (empty($applications)): ?>
            <p>Nie złożyłeś jeszcze żadnych aplikacji.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tytuł oferty</th>
                        <th>Data aplikacji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?= htmlspecialchars($application['tytul']) ?></td>
                            <td><?= date('d.m.Y', strtotime($application['data_aplikacji'])) ?></td>
                            <td><?= htmlspecialchars($application['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>