<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notification'])) {
    $id = $_POST['id'];
    $tytul = $_POST['tytul'];
    $tresc = $_POST['tresc'];
    $typ = $_POST['typ'];
    $przeczytana = isset($_POST['przeczytana']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE powiadomienia SET tytul = ?, tresc = ?, typ = ?, przeczytana = ? WHERE id = ?");
    $stmt->bind_param('sssii', $tytul, $tresc, $typ, $przeczytana, $id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT p.*, u.imie, u.nazwisko 
                       FROM powiadomienia p
                       JOIN uzytkownicy u ON p.id_uzytkownika = u.id");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Powiadomienia</title>
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

        /* Styles for Dodaj and Anuluj buttons */
        button[type="submit"], button[type="button"] {
            background-color: #008CBA; /* blue */
            color: white;
            border: none;
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #007B9E;
        }
        button[type="button"]:hover {
            background-color: #005f73;
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

    <h3>Powiadomienia</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Użytkownik</th>
                <th>Tytuł</th>
                <th>Treść</th>
                <th>Typ</th>
                <th>Przeczytana</th>
                <th>Data wysłania</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($notification = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$notification['id']}</td>
                            <td>{$notification['imie']} {$notification['nazwisko']}</td>
                            <td>{$notification['tytul']}</td>
                            <td>{$notification['tresc']}</td>
                            <td>{$notification['typ']}</td>
                            <td>".($notification['przeczytana'] ? 'Tak' : 'Nie')."</td>
                            <td>{$notification['data_wyslania']}</td>
                            <td>
                                <button class='edit-button' onclick='editNotification({$notification['id']}, \"{$notification['tytul']}\", \"{$notification['tresc']}\", \"{$notification['typ']}\", {$notification['przeczytana']})'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Brak powiadomień w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj powiadomienie</h3>
        <form method="POST">
            <input type="hidden" name="id" id="notification_id">
            <label for="tytul">Tytuł:</label>
            <input type="text" name="tytul" id="notification_tytul" required>
            <br>
            <label for="tresc">Treść:</label>
            <textarea name="tresc" id="notification_tresc" required></textarea>
            <br>
            <label for="typ">Typ:</label>
            <select name="typ" id="notification_typ" required>
                <option value="system">System</option>
                <option value="aplikacja">Aplikacja</option>
                <option value="wiadomosc">Wiadomość</option>
                <option value="oferta">Oferta</option>
            </select>
            <br>
            <label for="przeczytana">Przeczytana:</label>
            <input type="checkbox" name="przeczytana" id="notification_przeczytana" value="1">
            <br>
            <button type="submit" name="update_notification">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>
<script>
    function editNotification(id, tytul, tresc, typ, przeczytana) {
        document.getElementById('notification_id').value = id;
        document.getElementById('notification_tytul').value = tytul;
        document.getElementById('notification_tresc').value = tresc;
        document.getElementById('notification_typ').value = typ;
        document.getElementById('notification_przeczytana').checked = przeczytana == 1;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>
</body>
</html>
