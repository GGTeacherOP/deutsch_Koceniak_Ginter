<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_message'])) {
    $id = $_POST['id'];
    $temat = $_POST['temat'];
    $tresc = $_POST['tresc'];
    $przeczytana = isset($_POST['przeczytana']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE wiadomosci SET temat = ?, tresc = ?, przeczytana = ? WHERE id = ?");
    $stmt->bind_param('ssii', $temat, $tresc, $przeczytana, $id);
    $stmt->execute();
    $stmt->close();
}

$messages = $conn->query("SELECT w.*, 
                         n.imie as nadawca_imie, n.nazwisko as nadawca_nazwisko,
                         o.imie as odbiorca_imie, o.nazwisko as odbiorca_nazwisko
                         FROM wiadomosci w
                         JOIN uzytkownicy n ON w.id_nadawcy = n.id
                         JOIN uzytkownicy o ON w.id_odbiorcy = o.id");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Wiadomości</title>
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
        .admin-links a:hover { background-color: #45a049; }    </style>
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

    <h3>Wiadomości</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nadawca</th>
                <th>Odbiorca</th>
                <th>Temat</th>
                <th>Treść</th>
                <th>Przeczytana</th>
                <th>Data wysłania</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($messages->num_rows > 0) {
                while ($message = $messages->fetch_assoc()) {
                    echo "<tr>
                            <td>{$message['id']}</td>
                            <td>{$message['nadawca_imie']} {$message['nadawca_nazwisko']}</td>
                            <td>{$message['odbiorca_imie']} {$message['odbiorca_nazwisko']}</td>
                            <td>{$message['temat']}</td>
                            <td>{$message['tresc']}</td>
                            <td>".($message['przeczytana'] ? 'Tak' : 'Nie')."</td>
                            <td>{$message['data_wyslania']}</td>
                            <td>
                                <button class='edit-button' onclick='editMessage({$message['id']}, \"{$message['temat']}\", \"{$message['tresc']}\", {$message['przeczytana']})'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Brak wiadomości w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj wiadomość</h3>
        <form method="POST">
            <input type="hidden" name="id" id="message_id">
            <label for="temat">Temat:</label>
            <input type="text" name="temat" id="message_temat">
            <br>
            <label for="tresc">Treść:</label>
            <textarea name="tresc" id="message_tresc" required></textarea>
            <br>
            <label for="przeczytana">Przeczytana:</label>
            <input type="checkbox" name="przeczytana" id="message_przeczytana" value="1">
            <br>
            <button type="submit" name="update_message">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>


<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>
<script>
    function editMessage(id, temat, tresc, przeczytana) {
        document.getElementById('message_id').value = id;
        document.getElementById('message_temat').value = temat;
        document.getElementById('message_tresc').value = tresc;
        document.getElementById('message_przeczytana').checked = przeczytana == 1;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>
</body>
</html>