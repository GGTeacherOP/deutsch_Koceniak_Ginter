<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: logowanie.php');
    exit();
}

$error = '';
$success = '';

// Pobierz aktualne dane użytkownika
$stmt = $conn->prepare("SELECT imie, nazwisko, email FROM uzytkownicy WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);
    
    // Walidacja
    if (empty($imie) || empty($nazwisko) || empty($email)) {
        $error = 'Wszystkie pola są wymagane';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s]{2,50}$/', $imie)) {
        $error = 'Imię może zawierać tylko litery (2-50 znaków)';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]{2,50}$/', $nazwisko)) {
        $error = 'Nazwisko może zawierać tylko litery i myślniki (2-50 znaków)';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nieprawidłowy format adresu email';
    } else {
        try {
            // Sprawdź czy email już istnieje (u kogoś innego)
            $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE email = ? AND id != ?");
            $stmt->bind_param('si', $email, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = 'Podany adres email jest już zajęty';
            } else {
                $stmt = $conn->prepare("UPDATE uzytkownicy SET imie = ?, nazwisko = ?, email = ? WHERE id = ?");
                $stmt->bind_param('sssi', $imie, $nazwisko, $email, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = 'Dane zostały zaktualizowane pomyślnie';
                    // Odśwież dane w zmiennej
                    $user_data['imie'] = $imie;
                    $user_data['nazwisko'] = $nazwisko;
                    $user_data['email'] = $email;
                } else {
                    $error = 'Wystąpił błąd podczas aktualizacji danych';
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
    <title>Edycja danych – Portal z ofertami pracy</title>
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
    <h2>Edycja danych osobowych</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="edytuj_dane.php" method="post">
        <div class="form-group">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required 
                   value="<?= htmlspecialchars($user_data['imie']) ?>">
        </div>
        
        <div class="form-group">
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" required 
                   value="<?= htmlspecialchars($user_data['nazwisko']) ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required 
                   value="<?= htmlspecialchars($user_data['email']) ?>">
        </div>
        
        <button type="submit">Zapisz zmiany</button>
        <a href="konto.php" class="button" style="background: #666; margin-left: 1rem;">Anuluj</a>
    </form>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>