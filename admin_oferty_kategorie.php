<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_relation'])) {
        $id_oferty = $_POST['id_oferty'];
        $id_kategorii = $_POST['id_kategorii'];

        $stmt = $conn->prepare("INSERT INTO oferty_kategorie (id_oferty, id_kategorii) VALUES (?, ?)");
        $stmt->bind_param('ii', $id_oferty, $id_kategorii);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_relation'])) {
        $id_oferty = $_POST['id_oferty'];
        $id_kategorii = $_POST['id_kategorii'];

        $stmt = $conn->prepare("DELETE FROM oferty_kategorie WHERE id_oferty = ? AND id_kategorii = ?");
        $stmt->bind_param('ii', $id_oferty, $id_kategorii);
        $stmt->execute();
        $stmt->close();
    }
}

$relations = $conn->query("SELECT ok.*, o.tytul, k.nazwa 
                          FROM oferty_kategorie ok
                          JOIN oferty o ON ok.id_oferty = o.id
                          JOIN kategorie k ON ok.id_kategorii = k.id");

$oferty = $conn->query("SELECT id, tytul FROM oferty");
$kategorie = $conn->query("SELECT id, nazwa FROM kategorie");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Oferty-Kategorie</title>
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

    <h3>Relacje Oferty-Kategorie</h3>
    <button onclick="showAddForm()">Dodaj nową relację</button>
    <table>
        <thead>
            <tr>
                <th>ID Oferty</th>
                <th>Tytuł oferty</th>
                <th>ID Kategorii</th>
                <th>Nazwa kategorii</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($relations->num_rows > 0) {
                while ($relacja = $relations->fetch_assoc()) {
                    echo "<tr>
                            <td>{$relacja['id_oferty']}</td>
                            <td>{$relacja['tytul']}</td>
                            <td>{$relacja['id_kategorii']}</td>
                            <td>{$relacja['nazwa']}</td>
                            <td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='id_oferty' value='{$relacja['id_oferty']}'>
                                    <input type='hidden' name='id_kategorii' value='{$relacja['id_kategorii']}'>
                                    <button type='submit' name='delete_relation' class='delete-button'>Usuń</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Brak relacji w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="addForm" style="display:none;">
        <h3>Dodaj nową relację</h3>
        <form method="POST">
            <label for="id_oferty">Oferta:</label>
            <select name="id_oferty" id="id_oferty" required>
                <?php while ($oferta = $oferty->fetch_assoc()): ?>
                    <option value="<?= $oferta['id'] ?>"><?= $oferta['tytul'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="id_kategorii">Kategoria:</label>
            <select name="id_kategorii" id="id_kategorii" required>
                <?php while ($kategoria = $kategorie->fetch_assoc()): ?>
                    <option value="<?= $kategoria['id'] ?>"><?= $kategoria['nazwa'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <button type="submit" name="add_relation">Dodaj</button>
            <button type="button" onclick="closeAddForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
    }

    function closeAddForm() {
        document.getElementById('addForm').style.display = 'none';
    }
</script>
</body>
</html>