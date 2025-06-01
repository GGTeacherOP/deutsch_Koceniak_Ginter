<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_opinion'])) {
    $id = $_POST['id'];
    $tresc = $_POST['tresc'];
    $ocena = $_POST['ocena'];

    $stmt = $conn->prepare("UPDATE opinie SET tresc = ?, ocena = ? WHERE id = ?");
    $stmt->bind_param('sii', $tresc, $ocena, $id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT o.*, u.imie, u.nazwisko 
                       FROM opinie o
                       JOIN uzytkownicy u ON o.id_uzytkownika = u.id");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Opinie</title>
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
        <a href="admin_wiadomosci.php">Wiadomości</a>    </div>

    <h3>Opinie użytkowników</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Użytkownik</th>
                <th>Treść</th>
                <th>Ocena</th>
                <th>Data dodania</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($opinion = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$opinion['id']}</td>
                            <td>{$opinion['imie']} {$opinion['nazwisko']}</td>
                            <td>{$opinion['tresc']}</td>
                            <td>{$opinion['ocena']}/5</td>
                            <td>{$opinion['data_dodania']}</td>
                            <td>
                                <button class='edit-button' onclick='editOpinion({$opinion['id']}, \"{$opinion['tresc']}\", {$opinion['ocena']})'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Brak opinii w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj opinię</h3>
        <form method="POST">
            <input type="hidden" name="id" id="opinion_id">
            <label for="tresc">Treść:</label>
            <textarea name="tresc" id="opinion_tresc" required></textarea>
            <br>
            <label for="ocena">Ocena (1-5):</label>
            <input type="number" name="ocena" id="opinion_ocena" min="1" max="5" required>
            <br>
            <button type="submit" name="update_opinion">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>
<script>
    function editOpinion(id, tresc, ocena) {
        document.getElementById('opinion_id').value = id;
        document.getElementById('opinion_tresc').value = tresc;
        document.getElementById('opinion_ocena').value = ocena;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>
</body>
</html>