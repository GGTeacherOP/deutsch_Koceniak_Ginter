<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['rola'] !== 'admin' && $_SESSION['rola'] !== 'pracodawca')) {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_offer'])) {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $wynagrodzenie_min = $_POST['wynagrodzenie_min'];
    $wynagrodzenie_max = $_POST['wynagrodzenie_max'];
    $lokalizacja = $_POST['lokalizacja'];
    $kategoria = $_POST['kategoria'];
    $firma = $_POST['firma'];
    $termin_aplikacji = $_POST['termin_aplikacji'];
    $zdalna = isset($_POST['zdalna']) ? 1 : 0; // Nowa zmienna dla zdalnej
    $id_pracodawcy = $_SESSION['user_id'];

    // Przygotowanie zapytania do dodania oferty
    $stmt1 = $conn->prepare("INSERT INTO oferty (tytul, opis, wynagrodzenie_min, wynagrodzenie_max, lokalizacja, kategoria, firma, termin_aplikacji, zdalna, id_pracodawcy, data_dodania) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt1 === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt1->bind_param("ssddssssii", $tytul, $opis, $wynagrodzenie_min, $wynagrodzenie_max, $lokalizacja, $kategoria, $firma, $termin_aplikacji, $zdalna, $id_pracodawcy);

    if ($stmt1->execute()) {
        header("Location: lista_ofert.php");
        exit();
    } else {
        echo "Błąd podczas dodawania oferty: " . $stmt1->error . "<br>";
    }
    $stmt1->close();
}

// Pobieranie kategorii z bazy danych
$kategorie = [];
$result = $conn->query("SELECT id, nazwa FROM kategorie");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $kategorie[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj ofertę</title>
    <link rel="stylesheet" href="styleindex.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
</head>
<body>

<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
    <h1>Dodaj nową ofertę</h1>
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
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <li><a href="admin_panel.php">Panel Admina</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section id="dodaj-oferte-formularz">
        <h2>Dodaj nową ofertę</h2>
        
        <form action="dodaj_oferte.php" method="post">
            <div class="form-group">
                <label for="tytul">Tytuł oferty:</label>
                <input type="text" id="tytul" name="tytul" required>
            </div>
            
            <div class="form-group">
                <label for="opis">Opis oferty:</label>
                <textarea id="opis" name="opis" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="wynagrodzenie_min">Wynagrodzenie minimalne:</label>
                <input type="number" id="wynagrodzenie_min" name="wynagrodzenie_min" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="wynagrodzenie_max">Wynagrodzenie maksymalne:</label>
                <input type="number" id="wynagrodzenie_max" name="wynagrodzenie_max" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="lokalizacja">Lokalizacja:</label>
                <input type="text" id="lokalizacja" name="lokalizacja" required>
            </div>

            <div class="form-group">
                <label for="kategoria">Kategoria:</label>
                <select id="kategoria" name="kategoria" required>
                    <option value="">Wybierz kategorię</option>
                    <?php foreach ($kategorie as $kategoria): ?>
                        <option value="<?php echo $kategoria['nazwa']; ?>"><?php echo $kategoria['nazwa']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="firma">Nazwa firmy:</label>
                <input type="text" id="firma" name="firma" required>
            </div>

            <div class="form-group">
                <label for="termin_aplikacji">Termin aplikacji:</label>
                <input type="date" id="termin_aplikacji" name="termin_aplikacji" required>
            </div>

            <div class="form-group">
                <label for="zdalna">Zdalna:</label>
                <input type="checkbox" id="zdalna" name="zdalna" value="1">
            </div>
            
            <button type="submit" name="add_offer">Dodaj ofertę</button>
            <a href="lista_ofert.php" class="button">Powrót do listy ofert</a>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
