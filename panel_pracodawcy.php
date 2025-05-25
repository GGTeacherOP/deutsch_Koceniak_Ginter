<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'pracodawca') {
    header('Location: index.php');
    exit();
}

// Pobieranie ofert pracodawcy
$stmt = $conn->prepare("SELECT * FROM oferty WHERE id_pracodawcy = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pracodawcy</title>
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
    <h2>Panel Pracodawcy</h2>
    <h3>Lista ofert</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Opis</th>
                <th>Lokalizacja</th>
                <th>Data dodania</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($oferta = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$oferta['id']}</td>
                            <td>{$oferta['tytul']}</td>
                            <td>{$oferta['opis']}</td>
                            <td>{$oferta['lokalizacja']}</td>
                            <td>{$oferta['data_dodania']}</td>
                            <td>
                                <a href='edytuj_oferte.php?id={$oferta['id']}'>Edytuj</a> | 
                                <a href='aplikacje.php?id={$oferta['id']}'>Zobacz aplikacje</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Brak ofert.</td></tr>";
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