<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_oferta'])) {
        $id = $_POST['id'];
        $tytul = $_POST['tytul'];
        $opis = $_POST['opis'];
        $firma = $_POST['firma'];
        $lokalizacja = $_POST['lokalizacja'];
        $wynagrodzenie_min = $_POST['wynagrodzenie_min'];
        $wynagrodzenie_max = $_POST['wynagrodzenie_max'];
        $typ_pracy = $_POST['typ_pracy'];
        $zdalna = isset($_POST['zdalna']) ? 1 : 0;
        $termin_aplikacji = $_POST['termin_aplikacji'];
        $kategoria = $_POST['kategoria'];
        $id_pracodawcy = $_POST['id_pracodawcy'];

        $stmt = $conn->prepare("UPDATE oferty SET tytul = ?, opis = ?, firma = ?, lokalizacja = ?, wynagrodzenie_min = ?, wynagrodzenie_max = ?, typ_pracy = ?, zdalna = ?, termin_aplikacji = ?, kategoria = ?, id_pracodawcy = ? WHERE id = ?");
        $stmt->bind_param('ssssddssisii', $tytul, $opis, $firma, $lokalizacja, $wynagrodzenie_min, $wynagrodzenie_max, $typ_pracy, $zdalna, $termin_aplikacji, $kategoria, $id_pracodawcy, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_oferta'])) {
        // Obsługa dodawania nowej oferty
    }
}

$stmt = $conn->prepare("SELECT * FROM oferty");
$stmt->execute();
$result = $stmt->get_result();

// Pobierz listę pracodawców dla selecta
$pracodawcy = $conn->query("SELECT id, imie, nazwisko FROM uzytkownicy WHERE rola = 'pracodawca'");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Oferty</title>
    <link rel="stylesheet" href="styleindex.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .edit-button { background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .delete-button { background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        #editForm, #addForm { margin-top: 20px; padding: 20px; background-color: #f8f8f8; border-radius: 5px; }
        .admin-links { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .admin-links a { padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        .admin-links a:hover { background-color: #45a049; }

        /* Dodany styl do przycisku Dodaj nową ofertę */
        button[onclick="showAddForm()"] {
            background-color: #2196F3;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        button[onclick="showAddForm()"]:hover {
            background-color: #1976D2;
        }

        /* Style dla przycisków Zaktualizuj i Anuluj w formularzu edycji */
        #editForm button[name="update_oferta"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        #editForm button[name="update_oferta"]:hover {
            background-color: #45a049;
        }

        #editForm button[type="button"] {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        #editForm button[type="button"]:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
<header>
    <h1>Panel Admina - Kategorie</h1>
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
            <li><a href="admin_panel.php">Panel Admina</a></li>
        </ul>
    </nav>
</header>

<main>
    <div class="admin-links">
        <a href="admin_panel.php">Użytkownicy</a>
        <a href="admin_aplikacje.php">Aplikacje</a>
        <a href="admin_kategorie.php">Kategorie</a>
        <a href="admin_kontakt.php">Kontakt</a>
        <a href="admin_oferty.php">Oferty</a>
        <a href="admin_oferty_kategorie.php">Oferty-Kategorie</a>
        <a href="admin_opinie.php">Opinie</a>
        <a href="admin_powiadomienia.php">Powiadomienia</a>
        <a href="admin_umiejetnosci.php">Umiejętności</a>
        <a href="admin_uzytkownicy_umiejetnosci.php">Użytkownicy-Umiejętności</a>
        <a href="admin_wiadomosci.php">Wiadomości</a>
    </div>

    <h3>Lista ofert pracy</h3>
    <button onclick="showAddForm()">Dodaj nową ofertę</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Firma</th>
                <th>Lokalizacja</th>
                <th>Wynagrodzenie</th>
                <th>Typ pracy</th>
                <th>Zdalna</th>
                <th>Termin</th>
                <th>Kategoria</th>
                <th>ID Pracodawcy</th>
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
                            <td>{$oferta['firma']}</td>
                            <td>{$oferta['lokalizacja']}</td>
                            <td>{$oferta['wynagrodzenie_min']} - {$oferta['wynagrodzenie_max']}</td>
                            <td>{$oferta['typ_pracy']}</td>
                            <td>".($oferta['zdalna'] ? 'Tak' : 'Nie')."</td>
                            <td>{$oferta['termin_aplikacji']}</td>
                            <td>{$oferta['kategoria']}</td>
                            <td>{$oferta['id_pracodawcy']}</td>
                            <td>
                                <button class='edit-button' onclick='editOferta({$oferta['id']}, \"{$oferta['tytul']}\", \"{$oferta['opis']}\", \"{$oferta['firma']}\", \"{$oferta['lokalizacja']}\", {$oferta['wynagrodzenie_min']}, {$oferta['wynagrodzenie_max']}, \"{$oferta['typ_pracy']}\", {$oferta['zdalna']}, \"{$oferta['termin_aplikacji']}\", \"{$oferta['kategoria']}\", {$oferta['id_pracodawcy']})'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>Brak ofert w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj ofertę</h3>
        <form method="POST">
            <input type="hidden" name="id" id="oferta_id">
            <label for="tytul">Tytuł:</label>
            <input type="text" name="tytul" id="oferta_tytul" required><br>
            <label for="opis">Opis:</label>
            <textarea name="opis" id="oferta_opis" required></textarea><br>
            <label for="firma">Firma:</label>
            <input type="text" name="firma" id="oferta_firma" required><br>
            <label for="lokalizacja">Lokalizacja:</label>
            <input type="text" name="lokalizacja" id="oferta_lokalizacja"><br>
            <label for="wynagrodzenie_min">Wynagrodzenie min:</label>
            <input type="number" step="0.01" name="wynagrodzenie_min" id="oferta_wynagrodzenie_min"><br>
            <label for="wynagrodzenie_max">Wynagrodzenie max:</label>
            <input type="number" step="0.01" name="wynagrodzenie_max" id="oferta_wynagrodzenie_max"><br>
            <label for="typ_pracy">Typ pracy:</label>
            <select name="typ_pracy" id="oferta_typ_pracy" required>
                <option value="pełny etat">Pełny etat</option>
                <option value="część etatu">Część etatu</option>
                <option value="tymczasowa">Tymczasowa</option>
                <option value="staż">Staż</option>
                <option value="praktyka">Praktyka</option>
                <option value="freelance">Freelance</option>
            </select><br>
            <label for="zdalna">Zdalna:</label>
            <input type="checkbox" name="zdalna" id="oferta_zdalna" value="1"><br>
            <label for="termin_aplikacji">Termin aplikacji:</label>
            <input type="date" name="termin_aplikacji" id="oferta_termin_aplikacji"><br>
            <label for="kategoria">Kategoria:</label>
            <input type="text" name="kategoria" id="oferta_kategoria"><br>
            <label for="id_pracodawcy">ID Pracodawcy:</label>
            <select name="id_pracodawcy" id="oferta_id_pracodawcy" required>
                <?php while ($pracodawca = $pracodawcy->fetch_assoc()): ?>
                    <option value="<?= $pracodawca['id'] ?>"><?= $pracodawca['imie'] ?> <?= $pracodawca['nazwisko'] ?></option>
                <?php endwhile; ?>
            </select><br>
            <button type="submit" name="update_oferta">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>
<script>
    function editOferta(id, tytul, opis, firma, lokalizacja, wynagrodzenie_min, wynagrodzenie_max, typ_pracy, zdalna, termin_aplikacji, kategoria, id_pracodawcy) {
        document.getElementById('oferta_id').value = id;
        document.getElementById('oferta_tytul').value = tytul;
        document.getElementById('oferta_opis').value = opis;
        document.getElementById('oferta_firma').value = firma;
        document.getElementById('oferta_lokalizacja').value = lokalizacja;
        document.getElementById('oferta_wynagrodzenie_min').value = wynagrodzenie_min;
        document.getElementById('oferta_wynagrodzenie_max').value = wynagrodzenie_max;
        document.getElementById('oferta_typ_pracy').value = typ_pracy;
        document.getElementById('oferta_zdalna').checked = zdalna == 1;
        document.getElementById('oferta_termin_aplikacji').value = termin_aplikacji;
        document.getElementById('oferta_kategoria').value = kategoria;
        document.getElementById('oferta_id_pracodawcy').value = id_pracodawcy;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }

    function showAddForm() {
        alert("Dodawanie oferty jeszcze niezaimplementowane.");
    }
</script>
</body>
</html>
