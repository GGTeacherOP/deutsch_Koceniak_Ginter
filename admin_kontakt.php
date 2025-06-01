<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_kontakt'])) {
    $id = $_POST['id'];
    $imie = $_POST['imie'];
    $email = $_POST['email'];
    $temat = $_POST['temat'];
    $wiadomosc = $_POST['wiadomosc'];

    $stmt = $conn->prepare("UPDATE kontakt SET imie = ?, email = ?, temat = ?, wiadomosc = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $imie, $email, $temat, $wiadomosc, $id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM kontakt");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Kontakt</title>
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
    <h1>Panel Admina - kontakt</h1>
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
        <a href="admin_ulubione_oferty.php">Ulubione oferty</a>
        <a href="admin_umiejetnosci.php">Umiejętności</a>
        <a href="admin_uzytkownicy_umiejetnosci.php">Użytkownicy-Umiejętności</a>
        <a href="admin_wiadomosci.php">Wiadomości</a>
    </div>

    <h3>Wiadomości kontaktowe</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Email</th>
                <th>Temat</th>
                <th>Wiadomość</th>
                <th>Data wysłania</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($kontakt = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$kontakt['id']}</td>
                            <td>{$kontakt['imie']}</td>
                            <td>{$kontakt['email']}</td>
                            <td>{$kontakt['temat']}</td>
                            <td>{$kontakt['wiadomosc']}</td>
                            <td>{$kontakt['data_wyslania']}</td>
                            <td>
                                <button class='edit-button' onclick='editKontakt({$kontakt['id']}, \"{$kontakt['imie']}\", \"{$kontakt['email']}\", \"{$kontakt['temat']}\", \"{$kontakt['wiadomosc']}\")'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Brak wiadomości w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj wiadomość</h3>
        <form method="POST">
            <input type="hidden" name="id" id="kontakt_id">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="kontakt_imie" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="kontakt_email" required>
            <br>
            <label for="temat">Temat:</label>
            <input type="text" name="temat" id="kontakt_temat">
            <br>
            <label for="wiadomosc">Wiadomość:</label>
            <textarea name="wiadomosc" id="kontakt_wiadomosc" required></textarea>
            <br>
            <button type="submit" name="update_kontakt">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
    function editKontakt(id, imie, email, temat, wiadomosc) {
        document.getElementById('kontakt_id').value = id;
        document.getElementById('kontakt_imie').value = imie;
        document.getElementById('kontakt_email').value = email;
        document.getElementById('kontakt_temat').value = temat;
        document.getElementById('kontakt_wiadomosc').value = wiadomosc;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>
</body>
</html>