<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'pracodawca') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: panel_pracodawcy.php");
    exit();
}

$id_oferty = intval($_GET['id']);

// Pobieranie aplikacji dla danej oferty
$stmt = $conn->prepare("SELECT a.*, u.imie, u.nazwisko FROM aplikacje a JOIN uzytkownicy u ON a.id_uzytkownika = u.id WHERE a.id_oferty = ?");
$stmt->bind_param("i", $id_oferty);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikacje na ofertę</title>
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
            <li><a href="opinie.php">Opinie</a></li>
            <li><a href="panel_pracodawcy.php">Panel Pracodawcy</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Aplikacje na ofertę</h2>
    <table>
        <thead>
            <tr>
                <th>ID Aplikacji</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Wiadomość</th>
                <th>CV</th>
                <th>List motywacyjny</th>
                <th>Data aplikacji</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($aplikacja = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$aplikacja['id']}</td>
                            <td>{$aplikacja['imie']}</td>
                            <td>{$aplikacja['nazwisko']}</td>
                            <td>{$aplikacja['wiadomosc']}</td>
                            <td><a href='uploads/{$aplikacja['cv_plik']}' target='_blank'>Pobierz CV</a></td>
                            <td><a href='uploads/{$aplikacja['list_plik']}' target='_blank'>Pobierz List</a></td>
                            <td>{$aplikacja['data_aplikacji']}</td>
                            <td>{$aplikacja['status']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Brak aplikacji na tę ofertę.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
