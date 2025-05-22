<?php
/**
 * Panel administracyjny systemu zarządzania użytkownikami
 * 
 * Skrypt umożliwia administratorowi:
 * - przeglądanie listy wszystkich użytkowników
 * - edycję danych użytkowników (imię, nazwisko, email, rola, pensja)
 * - kontrolę dostępu tylko dla użytkowników z rolą 'admin'
 */

// Inicjalizacja sesji i sprawdzenie uprawnień
session_start();
require_once 'config.php'; // Plik z konfiguracją połączenia z bazą danych

/**
 * Sprawdzenie czy użytkownik jest zalogowany i ma uprawnienia administratora
 * Jeśli nie - przekierowanie do strony logowania
 */
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
    header('Location: logowanie.php');
    exit();
}

/**
 * Obsługa formularza aktualizacji danych użytkownika
 * Wykonywana tylko gdy żądanie jest typu POST i przesłano formularz update_user
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    // Pobranie danych z formularza
    $user_id = $_POST['user_id'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $rola = $_POST['rola'];
    $pensja = $_POST['pensja']; // Nowe pole pensji dodane do systemu

    // Przygotowanie i wykonanie zapytania SQL do aktualizacji danych
    $stmt = $conn->prepare("UPDATE uzytkownicy SET imie = ?, nazwisko = ?, email = ?, rola = ?, pensja = ? WHERE id = ?");
    $stmt->bind_param('ssssdi', $imie, $nazwisko, $email, $rola, $pensja, $user_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Pobranie listy wszystkich użytkowników z bazy danych
 * Wykorzystanie prepared statement dla bezpieczeństwa
 */
$stmt = $conn->prepare("SELECT * FROM uzytkownicy");
if ($stmt === false) {
    die("Błąd w przygotowaniu zapytania: " . $conn->error); // Zakończenie skryptu w przypadku błędu
}

$stmt->execute();
$result = $stmt->get_result(); // Pobranie wyników zapytania
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny plik CSS -->
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        /* Style specyficzne dla panelu admina */
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
    </style>
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
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
             <li><a href="opinie.php">opinie</a></li>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <h2>Witaj w panelu admina!</h2>
    
    <!-- Tabela z listą użytkowników -->
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
            /**
             * Wyświetlenie danych użytkowników w tabeli
             * Jeśli nie ma użytkowników - wyświetl komunikat
             */
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
                                <button class='edit-button' onclick='editUser({$user['id']}, \"{$user['imie']}\", \"{$user['nazwisko']}\", \"{$user['email']}\", \"{$user['rola']}\", \"{$user['pensja']}\")'>Edytuj</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Brak użytkowników w bazie danych.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Formularz edycji użytkownika (domyślnie ukryty) -->
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

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<!-- Skrypty JavaScript -->
<script>
    /**
     * Funkcja otwierająca formularz edycji i wypełniająca go danymi użytkownika
     * @param {number} id - ID użytkownika
     * @param {string} imie - Imię użytkownika
     * @param {string} nazwisko - Nazwisko użytkownika
     * @param {string} email - Email użytkownika
     * @param {string} rola - Rola użytkownika
     * @param {number} pensja - Pensja użytkownika
     */
    function editUser(id, imie, nazwisko, email, rola, pensja) {
        document.getElementById('user_id').value = id;
        document.getElementById('imie').value = imie;
        document.getElementById('nazwisko').value = nazwisko;
        document.getElementById('email').value = email;
        document.getElementById('rola').value = rola;
        document.getElementById('pensja').value = pensja;
        document.getElementById('editForm').style.display = 'block';
    }

    /**
     * Funkcja zamykająca formularz edycji
     */
    function closeEditForm() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>

</body>
</html>

<?php
// Zamknięcie połączenia z bazą danych
$stmt->close();
?>