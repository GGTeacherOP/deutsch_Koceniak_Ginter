<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $rola = $_POST['rola'];
    $pensja = $_POST['pensja'];

    $stmt = $conn->prepare("UPDATE uzytkownicy SET imie = ?, nazwisko = ?, email = ?, rola = ?, pensja = ? WHERE id = ?");
    $stmt->bind_param('ssssdi', $imie, $nazwisko, $email, $rola, $pensja, $user_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM uzytkownicy");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
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
    <h1>Panel Admina</h1>
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
    <h2>Witaj w panelu admina!</h2>
    
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

    <h3>Lista użytkowników</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Pensja</th>
                <th>Data rejestracji</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$user['id']}</td>
                            <td>{$user['imie']}</td>
                            <td>{$user['nazwisko']}</td>
                            <td>{$user['email']}</td>
                            <td>{$user['rola']}</td>
                            <td>{$user['pensja']}</td>
                            <td>{$user['data_rejestracji']}</td>
                            <td>
                                <button class='edit-button' onclick='editUser ({$user['id']}, \"{$user['imie']}\", \"{$user['nazwisko']}\", \"{$user['email']}\", \"{$user['rola']}\", \"{$user['pensja']}\")'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Brak użytkowników w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="editForm" style="display:none;">
        <h3>Edytuj użytkownika</h3>
        <form method="POST">
            <input type="hidden" name="user_id" id="user_id">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="imie" required>
            <br>
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" id="nazwisko" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <br>
            <label for="rola">Rola:</label>
            <select name="rola" id="rola" required>
                <option value="admin">Admin</option>
                <option value="uzytkownik">Użytkownik</option>
                <option value="pracodawca">Pracodawca</option>
            </select>
            <br>
            <label for="pensja">Pensja:</label>
            <input type="number" name="pensja" id="pensja" step="0.01" required>
            <br>
            <button type="submit" name="update_user">Zaktualizuj</button>
            <button type="button" onclick="closeEditForm()">Anuluj</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
    function editUser (id, imie, nazwisko, email, rola, pensja) {
        document.getElementById('user_id').value = id;
        document.getElementById('imie').value = imie;
        document.getElementById('nazwisko').value = nazwisko;
        document.getElementById('email').value = email;
        document.getElementById('rola').value = rola;
        document.getElementById('pensja').value = pensja;
        document.getElementById('editForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>

</body>
</html>