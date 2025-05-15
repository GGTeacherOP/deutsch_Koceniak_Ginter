<?php
session_start();
require_once 'config.php'; // Plik z konfiguracją połączenia z bazą

// Przekieruj jeśli użytkownik jest już zalogowany
if (isset($_SESSION['user_id'])) {
    header('Location: konto.php');
    exit();
}

// Obsługa formularza logowania
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Pobierz użytkownika z bazy
        $stmt = $conn->prepare("SELECT id, imie, nazwisko, email, haslo, rola FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Weryfikacja hasła (w Twojej bazie hasła są plaintext, w prawdziwym systemie użyj password_verify())
            if ($password === $user['haslo']) {
                // Ustaw sesję
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
    <link rel="stylesheet" href="styleindex.css">
    <style>
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header>
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
        </ul>
    </nav>
</header>

<main>
    <h2>Logowanie do konta</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="logowanie.php" method="post">
        <label for="email">Adres e-mail:</label><br>
        <input type="email" name="email" id="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"><br><br>

        <label for="password">Hasło:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Zaloguj się</button>
    </form>

    <p>Nie masz jeszcze konta? <a href="rejestracja.php">Zarejestruj się</a></p>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>