<?php
session_start();
require_once 'config.php'; // Plik z konfiguracją połączenia z bazą

// Sprawdzenie, czy użytkownik jest zalogowany i ma rolę admina
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

// Przykład zapytania do bazy danych
$stmt = $conn->prepare("SELECT * FROM uzytkownicy");
if ($stmt === false) {
    die("Błąd w przygotowaniu zapytania: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
    <link rel="stylesheet" href="styleindex.css">
</head>
<body>

<header>
    <h1>Panel Admina</h1>
    <nav>
        <ul>
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Witaj w panelu admina!</h2>
    
    <h3>Lista użytkowników</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Data rejestracji</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$user['id']}</td>
                            <td>{$user['imie']}</td>
                            <td>{$user['nazwisko']}</td>
                            <td>{$user['email']}</td>
                            <td>{$user['rola']}</td>
                            <td>{$user['data_rejestracji']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Brak użytkowników w bazie danych.</td></tr>";
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

<?php
$stmt->close();
?>
