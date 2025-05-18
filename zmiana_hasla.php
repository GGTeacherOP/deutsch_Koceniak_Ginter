<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: logowanie.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Walidacja
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Wszystkie pola są wymagane';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Nowe hasła nie są identyczne';
    } elseif (strlen($new_password) < 6) {
        $error = 'Nowe hasło musi mieć co najmniej 6 znaków';
    } else {
        try {
            // Sprawdź aktualne hasło
            $stmt = $conn->prepare("SELECT haslo FROM uzytkownicy WHERE id = ?");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            // W prawdziwym systemie użyj password_verify()
            // if (!password_verify($current_password, $user['haslo'])) {
            if ($current_password !== $user['haslo']) {
                $error = 'Aktualne hasło jest nieprawidłowe';
            } else {
                // W prawdziwym systemie użyj password_hash()
                // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $hashed_password = $new_password;
                
                $stmt = $conn->prepare("UPDATE uzytkownicy SET haslo = ? WHERE id = ?");
                $stmt->bind_param('si', $hashed_password, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = 'Hasło zostało pomyślnie zmienione';
                } else {
                    $error = 'Wystąpił błąd podczas zmiany hasła';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmiana hasła – Portal z ofertami pracy</title>
    <link rel="stylesheet" href="styleindex.css">
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
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Zmiana hasła</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="zmiana_hasla.php" method="post">
        <div class="form-group">
            <label for="current_password">Aktualne hasło:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">Nowe hasło (min. 6 znaków):</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Powtórz nowe hasło:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit">Zmień hasło</button>
        <a href="konto.php" class="button" style="background: #666; margin-left: 1rem;">Anuluj</a>
    </form>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>