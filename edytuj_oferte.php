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

// Pobieranie danych oferty
$stmt = $conn->prepare("SELECT * FROM oferty WHERE id = ? AND id_pracodawcy = ?");
$stmt->bind_param("ii", $id_oferty, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: panel_pracodawcy.php");
    exit();
}

$oferta = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $wynagrodzenie_min = $_POST['wynagrodzenie_min'];
    $wynagrodzenie_max = $_POST['wynagrodzenie_max'];
    $lokalizacja = $_POST['lokalizacja'];
    $kategoria = $_POST['kategoria'];
    $firma = $_POST['firma'];
    $termin_aplikacji = $_POST['termin_aplikacji'];
    $zdalna = isset($_POST['zdalna']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE oferty SET tytul = ?, opis = ?, wynagrodzenie_min = ?, wynagrodzenie_max = ?, lokalizacja = ?, kategoria = ?, firma = ?, termin_aplikacji = ?, zdalna = ? WHERE id = ?");
    $stmt->bind_param("ssddsssi", $tytul, $opis, $wynagrodzenie_min, $wynagrodzenie_max, $lokalizacja, $kategoria, $firma, $termin_aplikacji, $zdalna, $id_oferty);

    if ($stmt->execute()) {
        header("Location: panel_pracodawcy.php");
        exit();
    } else {
        echo "Błąd podczas aktualizacji oferty: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj ofertę</title>
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
    <h2>Edytuj ofertę</h2>
    <form action="edytuj_oferte.php?id=<?= $id_oferty ?>" method="post">
        <div class="form-group">
            <label for="tytul">Tytuł oferty:</label>
            <input type="text" id="tytul" name="tytul" value="<?= htmlspecialchars($oferta['tytul']) ?>" required>
        </div>

        <div class="form-group">
            <label for="opis">Opis oferty:</label>
            <textarea id="opis" name="opis" rows="5" required><?= htmlspecialchars($oferta['opis']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="wynagrodzenie_min">Wynagrodzenie minimalne:</label>
            <input type="number" id="wynagrodzenie_min" name="wynagrodzenie_min" step="0.01" value="<?= htmlspecialchars($oferta['wynagrodzenie_min']) ?>" required>
        </div>

        <div class="form-group">
            <label for="wynagrodzenie_max">Wynagrodzenie maksymalne:</label>
            <input type="number" id="wynagrodzenie_max" name="wynagrodzenie_max" step="0.01" value="<?= htmlspecialchars($oferta['wynagrodzenie_max']) ?>" required>
        </div>

        <div class="form-group">
            <label for="lokalizacja">Lokalizacja:</label>
            <input type="text" id="lokalizacja" name="lokalizacja" value="<?= htmlspecialchars($oferta['lokalizacja']) ?>" required>
        </div>

        <div class="form-group">
            <label for="kategoria">Kategoria:</label>
            <input type="text" id="kategoria" name="kategoria" value="<?= htmlspecialchars($oferta['kategoria']) ?>" required>
        </div>

        <div class="form-group">
            <label for="firma">Nazwa firmy:</label>
            <input type="text" id="firma" name="firma" value="<?= htmlspecialchars($oferta['firma']) ?>" required>
        </div>

        <div class="form-group">
            <label for="termin_aplikacji">Termin aplikacji:</label>
            <input type="date" id="termin_aplikacji" name="termin_aplikacji" value="<?= htmlspecialchars($oferta['termin_aplikacji']) ?>" required>
        </div>

        <div class="form-group">
            <label for="zdalna">Zdalna:</label>
            <input type="checkbox" id="zdalna" name="zdalna" value="1" <?= $oferta['zdalna'] ? 'checked' : '' ?>>
        </div>
        
        <button type="submit">Zaktualizuj ofertę</button>
        <a href="panel_pracodawcy.php" class="button">Powrót do panelu</a>
    </form>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
