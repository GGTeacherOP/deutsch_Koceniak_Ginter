<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_kategoria'])) {
        $id = $_POST['id'];
        $nazwa = $_POST['nazwa'];

        $stmt = $conn->prepare("UPDATE kategorie SET nazwa = ? WHERE id = ?");
        $stmt->bind_param('si', $nazwa, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_kategoria'])) {
        $nazwa = $_POST['nazwa'];

        $stmt = $conn->prepare("INSERT INTO kategorie (nazwa) VALUES (?)");
        $stmt->bind_param('s', $nazwa);
        $stmt->execute();
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM kategorie");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Kategorie</title>
    <link rel="stylesheet" href="styleindex.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 5px 0;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        #addButton {
            background-color: #2196F3;
            padding: 12px 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        #addButton:hover {
            background-color: #0b7dda;
        }
        
        .edit-button { 
            background-color: #4CAF50; 
        }
        
        .delete-button { 
            background-color: #f44336; 
        }
        
        .delete-button:hover {
            background-color: #d32f2f;
        }
        
        #editForm, #addForm { 
            margin-top: 20px; 
            padding: 20px; 
            background-color: #f8f8f8; 
            border-radius: 5px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .admin-links { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            margin-bottom: 20px; 
        }
        
        .admin-links a { 
            padding: 10px; 
            background-color: #4CAF50; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            transition: background-color 0.3s;
        }
        
        .admin-links a:hover { 
            background-color: #45a049; 
        }
        
        input[type="text"] {
            padding: 8px;
            width: 100%;
            max-width: 300px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
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

    <h3>Lista kategorii</h3>
    <button id="addButton" onclick="showAddForm()">Dodaj nową kategorię</button>
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
            if ($result->num_rows > 0) {
                while ($kategoria = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$kategoria['id']}</td>
                            <td>{$kategoria['nazwa']}</td>
                            <td>
                                <button class='edit-button' onclick='editKategoria({$kategoria['id']}, \"{$kategoria['nazwa']}\")'>Edytuj</button>
                                <button class='delete-button' onclick='confirmDelete({$kategoria['id']})'>Usuń</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Brak kategorii w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj kategorię</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <label for="edit_nazwa">Nazwa:</label>
            <input type="text" name="nazwa" id="edit_nazwa" required>
            <br>
            <button type="submit" name="update_kategoria">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>

    <div id="addForm" style="display:none;">
        <h3>Dodaj nową kategorię</h3>
        <form method="POST">
            <label for="add_nazwa">Nazwa:</label>
            <input type="text" name="nazwa" id="add_nazwa" required>
            <br>
            <button type="submit" name="add_kategoria">Dodaj</button>
            <button type="button" onclick="closeAddForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
    function editKategoria(id, nazwa) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nazwa').value = nazwa;
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('addForm').style.display = 'none';
        window.scrollTo({
            top: document.getElementById('editForm').offsetTop - 20,
            behavior: 'smooth'
        });
    }

    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
        document.getElementById('editForm').style.display = 'none';
        window.scrollTo({
            top: document.getElementById('addForm').offsetTop - 20,
            behavior: 'smooth'
        });
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }

    function closeAddForm() {
        document.getElementById('addForm').style.display = 'none';
    }

    function confirmDelete(id) {
        if (confirm('Czy na pewno chcesz usunąć tę kategorię?')) {
            window.location.href = 'delete_kategoria.php?id=' + id;
        }
    }
</script>
</body>
</html>