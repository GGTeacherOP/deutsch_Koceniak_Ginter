<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_skill'])) {
        $id = $_POST['id'];
        $nazwa = $_POST['nazwa'];

        $stmt = $conn->prepare("UPDATE umiejetnosci SET nazwa = ? WHERE id = ?");
        $stmt->bind_param('si', $nazwa, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_skill'])) {
        $nazwa = $_POST['nazwa'];

        $stmt = $conn->prepare("INSERT INTO umiejetnosci (nazwa) VALUES (?)");
        $stmt->bind_param('s', $nazwa);
        $stmt->execute();
        $stmt->close();
    }
}

$skills = $conn->query("SELECT * FROM umiejetnosci");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Umiejętności</title>
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

    <h3>Lista umiejętności</h3>
    <button onclick="showAddForm()">Dodaj nową umiejętność</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($skills->num_rows > 0) {
                while ($skill = $skills->fetch_assoc()) {
                    echo "<tr>
                            <td>{$skill['id']}</td>
                            <td>{$skill['nazwa']}</td>
                            <td>
                                <button class='edit-button' onclick='editSkill({$skill['id']}, \"{$skill['nazwa']}\")'>Edytuj</button>
                                <button class='delete-button' onclick='confirmDelete({$skill['id']})'>Usuń</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Brak umiejętności w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj umiejętność</h3>
        <form method="POST">
            <input type="hidden" name="id" id="skill_id">
            <label for="nazwa">Nazwa:</label>
            <input type="text" name="nazwa" id="skill_nazwa" required>
            <br>
            <button type="submit" name="update_skill">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>

    <div id="addForm" style="display:none;">
        <h3>Dodaj nową umiejętność</h3>
        <form method="POST">
            <label for="nazwa">Nazwa:</label>
            <input type="text" name="nazwa" id="add_nazwa" required>
            <br>
            <button type="submit" name="add_skill">Dodaj</button>
            <button type="button" onclick="closeAddForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>
<script>
    function editSkill(id, nazwa) {
        document.getElementById('skill_id').value = id;
        document.getElementById('skill_nazwa').value = nazwa;
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('addForm').style.display = 'none';
    }

    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
        document.getElementById('editForm').style.display = 'none';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }

    function closeAddForm() {
        document.getElementById('addForm').style.display = 'none';
    }

    function confirmDelete(id) {
        if (confirm('Czy na pewno chcesz usunąć tę umiejętność?')) {
            window.location.href = 'delete_skill.php?id=' + id;
        }
    }
</script>
</body>
</html>