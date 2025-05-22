<?php
// Rozpoczęcie sesji i załączenie konfiguracji bazy danych
session_start();
require_once 'config.php'; // Plik z konfiguracją połączenia z bazą

// Inicjalizacja zmiennych dla komunikatów
$error = '';
$success = '';

// Przetwarzanie danych formularza rejestracji jeśli wysłano metodą POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i oczyszczenie danych z formularza
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);
    $opis = trim($_POST['opis']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $rola = $_POST['rola'];
    $regulamin = isset($_POST['regulamin']) ? true : false;
    $telefon = trim($_POST['telefon']); 

    // Walidacja danych wejściowych
    if (empty($imie) || empty($nazwisko) || empty($email) || empty($password) || empty($confirm) || empty($opis) || empty($telefon)) {
        $error = 'Wszystkie pola są wymagane';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]{2,50}$/', $imie)) {
        $error = 'Imię może zawierać tylko litery (2-50 znaków)';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]{2,50}$/', $nazwisko)) {
        $error = 'Nazwisko może zawierać tylko litery i myślniki (2-50 znaków)';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nieprawidłowy format adresu email';
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefon)) { // Walidacja numeru telefonu
        $error = 'Nieprawidłowy format numeru telefonu';
    }elseif ($password !== $confirm) {
        $error = 'Hasła nie są identyczne';
    } elseif (strlen($password) < 6) {
        $error = 'Hasło musi mieć co najmniej 6 znaków';
    } elseif (!$regulamin) {
        $error = 'Musisz zaakceptować regulamin';
    } else {
        // Jeśli walidacja OK, sprawdź unikalność emaila w bazie
        try {
            $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Podany adres email jest już zarejestrowany';
            } else {
                // W prawdziwym systemie powinno się użyć password_hash()
                // Tutaj używamy plaintext dla zgodności z Twoją bazą
                $hashed_password = $password;
                
                // Wstawienie nowego użytkownika do bazy
                $stmt = $conn->prepare("INSERT INTO uzytkownicy (imie, nazwisko, email, haslo, rola, opis, telefon) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss', $imie, $nazwisko, $email, $hashed_password, $rola, $opis ,$telefon);
                
                if ($stmt->execute()) {
                    $success = 'Rejestracja zakończona pomyślnie. Możesz się teraz zalogować.';
                    $_POST = array(); // Wyczyść formularz po udanej rejestracji
                } else {
                    $error = 'Wystąpił błąd podczas rejestracji: ' . $conn->error;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = 'Wystąpił błąd: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Meta dane strony -->
    <meta charset="UTF-8">
    <title>Rejestracja – Portal z ofertami pracy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Linki do arkuszy stylów -->
    <link rel="stylesheet" href="styleindex.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        /* Style dla komunikatów i formularza */
        .error { color: red; margin: 10px 0; padding: 10px; background: #ffebee; }
        .success { color: green; margin: 10px 0; padding: 10px; background: #e8f5e9; }
        .form-group { margin-bottom: 15px; }
        .role-selector { margin: 15px 0; }
        .role-selector label { margin-right: 15px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
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
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony - formularz rejestracji -->
<main>
    <h2>Rejestracja użytkownika</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Formularz rejestracji -->
    <form action="rejestracja.php" method="post">
        <div class="form-group">
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="imie" required 
                   value="<?= isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" id="nazwisko" required 
                   value="<?= isset($_POST['nazwisko']) ? htmlspecialchars($_POST['nazwisko']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="email">Adres e-mail:</label>
            <input type="email" name="email" id="email" required 
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Hasło (min. 6 znaków):</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="form-group">
            <label for="confirm">Powtórz hasło:</label>
            <input type="password" name="confirm" id="confirm" required>
        </div>
        <div class="form-group">
            <label for="opis">Opis:</label>
            <textarea name="opis" id="opis" rows="4" required><?= isset($_POST['opis']) ? htmlspecialchars($_POST['opis']) : '' ?></textarea>
        </div>
        <div class="form-group">
    <label for="telefon">Numer telefonu:</label>
    <input type="text" name="telefon" id="telefon" required 
           value="<?= isset($_POST['telefon']) ? htmlspecialchars($_POST['telefon']) : '' ?>">
    </div>
        <!-- Wybór typu konta -->
        <div class="role-selector">
            <label>Wybierz typ konta:</label><br>
            <label>
                <input type="radio" name="rola" value="uzytkownik" checked> Użytkownik szukający pracy
            </label>
            <label>
                <input type="radio" name="rola" value="pracodawca"> Pracodawca
            </label>
        </div>

        <!-- Akceptacja regulaminu -->
        <div class="form-group">
            <label>
                <input type="checkbox" name="regulamin" required 
                       <?= isset($_POST['regulamin']) ? 'checked' : '' ?>>
                Akceptuję <a href="regulamin.php">regulamin</a>
            </label>
        </div>

        <button type="submit">Zarejestruj się</button>
    </form>

    <p>Masz już konto? <a href="logowanie.php">Zaloguj się</a></p>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>