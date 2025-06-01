<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_relation'])) {
        $id_uzytkownika = $_POST['id_uzytkownika'];
        $id_umiejetnosci = $_POST['id_umiejetnosci'];
        $poziom = $_POST['poziom'];

        $stmt = $conn->prepare("INSERT INTO uzytkownicy_umiejetnosci (id_uzytkownika, id_umiejetnosci, poziom) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $id_uzytkownika, $id_umiejetnosci, $poziom);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_relation'])) {
        $id_uzytkownika = $_POST['id_uzytkownika'];
        $id_umiejetnosci = $_POST['id_umiejetnosci'];

        $stmt = $conn->prepare("DELETE FROM uzytkownicy_umiejetnosci WHERE id_uzytkownika = ? AND id_umiejetnosci = ?");
        $stmt->bind_param('ii', $id_uzytkownika, $id_umiejetnosci);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update_relation'])) {
        $id_uzytkownika = $_POST['id_uzytkownika'];
        $id_umiejetnosci = $_POST['id_umiejetnosci'];
        $poziom = $_POST['poziom'];

        $stmt = $conn->prepare("UPDATE uzytkownicy_umiejetnosci SET poziom = ? WHERE id_uzytkownika = ? AND id_umiejetnosci = ?");
        $stmt->bind_param('sii', $poziom, $id_uzytkownika, $id_umiejetnosci);
        $stmt->execute();
        $stmt->close();
    }
}

$relations = $conn->query("SELECT uu.*, u.imie, u.nazwisko, um.nazwa as umiejetnosc_nazwa 
                          FROM uzytkownicy_umiejetnosci uu
                          JOIN uzytkownicy u ON uu.id_uzytkownika = u.id
                          JOIN umiejetnosci um ON uu.id_umiejetnosci = um.id");

$users = $conn->query("SELECT id, imie, nazwisko FROM uzytkownicy");
$skills = $conn->query("SELECT id, nazwa FROM umiejetnosci");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Użytkownicy-Umiejętności</title>
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
        <a href="admin_wiadomosci.php">Wiadomości</a>    </div>

    <h3>Relacje Użytkownicy-Umiejętności</h3>
    <button onclick="showAddForm()">Dodaj nową relację</button>
    <table>
        <thead>
            <tr>
                <th>ID Użytkownika</th>
                <th>Użytkownik</th>
                <th>ID Umiejętności</th>
                <th>Umiejętność</th>
                <th>Poziom</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($relations->num_rows > 0) {
                while ($relacja = $relations->fetch_assoc()) {
                    echo "<tr>
                            <td>{$relacja['id_uzytkownika']}</td>
                            <td>{$relacja['imie']} {$relacja['nazwisko']}</td>
                            <td>{$relacja['id_umiejetnosci']}</td>
                            <td>{$relacja['umiejetnosc_nazwa']}</td>
                            <td>{$relacja['poziom']}</td>
                            <td>
                                <button class='edit-button' onclick='editRelation({$relacja['id_uzytkownika']}, {$relacja['id_umiejetnosci']}, \"{$relacja['poziom']}\")'>Edytuj</button>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='id_uzytkownika' value='{$relacja['id_uzytkownika']}'>
                                    <input type='hidden' name='id_umiejetnosci' value='{$relacja['id_umiejetnosci']}'>
                                    <button type='submit' name='delete_relation' class='delete-button'>Usuń</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Brak relacji w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj relację</h3>
        <form method="POST">
            <input type="hidden" name="id_uzytkownika" id="edit_id_uzytkownika">
            <input type="hidden" name="id_umiejetnosci" id="edit_id_umiejetnosci">
            <label for="poziom">Poziom:</label>
            <select name="poziom" id="edit_poziom" required>
                <option value="podstawowy">Podstawowy</option>
                <option value="średni">Średni</option>
                <option value="zaawansowany">Zaawansowany</option>
                <option value="ekspert">Ekspert</option>
            </select>
            <br>
            <button type="submit" name="update_relation">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>

    <div id="addForm" style="display:none;">
        <h3>Dodaj nową relację</h3>
        <form method="POST">
            <label for="id_uzytkownika">Użytkownik:</label>
            <select name="id_uzytkownika" id="id_uzytkownika" required>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>"><?= $user['imie'] ?> <?= $user['nazwisko'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="id_umiejetnosci">Umiejętność:</label>
            <select name="id_umiejetnosci" id="id_umiejetnosci" required>
                <?php while ($skill = $skills->fetch_assoc()): ?>
                    <option value="<?= $skill['id'] ?>"><?= $skill['nazwa'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="poziom">Poziom:</label>
            <select name="poziom" id="poziom" required>
                <option value="podstawowy">Podstawowy</option>
                <option value="średni">Średni</option>
                <option value="zaawansowany">Zaawansowany</option>
                <option value="ekspert">Ekspert</option>
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
    function editRelation(id_uzytkownika, id_umiejetnosci, poziom) {
        document.getElementById('edit_id_uzytkownika').value = id_uzytkownika;
        document.getElementById('edit_id_umiejetnosci').value = id_umiejetnosci;
        document.getElementById('edit_poziom').value = poziom;
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
</script>
</body>
</html>