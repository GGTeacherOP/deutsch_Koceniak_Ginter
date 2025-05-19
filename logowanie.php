<?php
/**
 * Skrypt logowania użytkownika
 * 
 * Funkcjonalności:
 * - Weryfikacja danych logowania
 * - Ustawienie sesji po poprawnym zalogowaniu
 * - Przekierowanie w zależności od roli użytkownika
 * - Ochrona przed atakami (XSS, SQL Injection)
 */

// Rozpoczęcie sesji i załadowanie konfiguracji
session_start();
require_once 'config.php';

// Przekieruj jeśli użytkownik jest już zalogowany
if (isset($_SESSION['user_id'])) {
    header('Location: konto.php');
    exit();
}

// Inicjalizacja zmiennej na błędy
$error = '';

// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobranie i oczyszczenie danych z formularza
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Walidacja pól formularza
    if (!empty($email) && !empty($password)) {
        /**
         * Pobierz użytkownika z bazy
         * Zabezpieczenie przed SQL Injection poprzez prepared statements
         */
        $stmt = $conn->prepare("SELECT id, imie, nazwisko, email, haslo, rola FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Sprawdź czy użytkownik istnieje
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            /**
             * Weryfikacja hasła
             * UWAGA: W tym przykładzie hasła są przechowywane jako plaintext
             * W rzeczywistym systemie należy użyć password_hash() i password_verify()
             */
            if ($password === $user['haslo']) {
                // Ustawienie danych sesji
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['imie'] = $user['imie'];
                $_SESSION['nazwisko'] = $user['nazwisko'];
                $_SESSION['rola'] = $user['rola'];
                
                // Przekierowanie w zależności od roli
                if ($user['rola'] === 'admin') {
                    header('Location: admin_panel.php');
                } else {
                    header('Location: konto.php');
                }
                exit();
            } else {
                $error = "Nieprawidłowy email lub hasło";
            }
        } else {
            $error = "Nieprawidłowy email lub hasło";
        }
        $stmt->close();
    } else {
        $error = "Proszę wypełnić wszystkie pola";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie – Portal z ofertami pracy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny arkusz stylów -->
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        /* Style dla komunikatów o błędach */
        .error {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffeeee;
            border: 1px solid #ffcccc;
            border-radius: 5px;
        }
        
        /* Style dla formularza logowania */
        main form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        main form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        main form input[type="email"],
        main form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        main form button {
            background-color: #0066cc;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        main form button:hover {
            background-color: #0055aa;
        }
        
        main p {
            text-align: center;
            margin-top: 20px;
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
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <h2>Logowanie do konta</h2>

    <!-- Wyświetlanie błędów logowania -->
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Formularz logowania -->
    <form action="logowanie.php" method="post">
        <label for="email">Adres e-mail:</label>
        <input type="email" name="email" id="email" required 
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label for="password">Hasło:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Zaloguj się</button>
    </form>

    <p>Nie masz jeszcze konta? <a href="rejestracja.php">Zarejestruj się</a></p>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>