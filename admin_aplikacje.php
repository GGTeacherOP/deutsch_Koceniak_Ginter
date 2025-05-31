<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_aplikacja'])) {
    $id = $_POST['id'];
    $id_uzytkownika = $_POST['id_uzytkownika'];
    $id_oferty = $_POST['id_oferty'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE aplikacje SET id_uzytkownika = ?, id_oferty = ?, status = ? WHERE id = ?");
    $stmt->bind_param('iisi', $id_uzytkownika, $id_oferty, $status, $id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM aplikacje");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina - Aplikacje</title>
    <link rel="stylesheet" href="styleindex.css">
</head>
<body>
    <style>
    table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .edit-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        #editForm {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 5px;
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
        }
        .admin-links a:hover {
            background-color: #45a049;
        }
        </style>
<header>
    <h1>Panel Admina - Aplikacje</h1>
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

    <h3>Lista aplikacji</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Użytkownika</th>
                <th>ID Oferty</th>
                <th>Status</th>
                <th>Data aplikacji</th>
                <th>Data aktualizacji</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($aplikacja = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$aplikacja['id']}</td>
                            <td>{$aplikacja['id_uzytkownika']}</td>
                            <td>{$aplikacja['id_oferty']}</td>
                            <td>{$aplikacja['status']}</td>
                            <td>{$aplikacja['data_aplikacji']}</td>
                            <td>{$aplikacja['data_aktualizacji']}</td>
                            <td>
                                <button class='edit-button' onclick='editAplikacja({$aplikacja['id']}, {$aplikacja['id_uzytkownika']}, {$aplikacja['id_oferty']}, \"{$aplikacja['status']}\")'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Brak aplikacji w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj aplikację</h3>
        <form method="POST">
            <input type="hidden" name="id" id="id">
            <label for="id_uzytkownika">ID Użytkownika:</label>
            <input type="number" name="id_uzytkownika" id="id_uzytkownika" required>
            <br>
            <label for="id_oferty">ID Oferty:</label>
            <input type="number" name="id_oferty" id="id_oferty" required>
            <br>
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="złożona">Złożona</option>
                <option value="w recenzji">W recenzji</option>
                <option value="odrzucona">Odrzucona</option>
                <option value="zaakceptowana">Zaakceptowana</option>
                <option value="w trakcie rozmowy">W trakcie rozmowy</option>
            </select>
            <br>
            <button type="submit" name="update_aplikacja">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
    function editAplikacja(id, id_uzytkownika, id_oferty, status) {
        document.getElementById('id').value = id;
        document.getElementById('id_uzytkownika').value = id_uzytkownika;
        document.getElementById('id_oferty').value = id_oferty;
        document.getElementById('status').value = status;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>

</body>
</html>