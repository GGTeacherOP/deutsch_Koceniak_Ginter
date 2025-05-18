<?php
session_start();
require_once 'config.php';

// Obsługa wysłania formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie']);
    $email = trim($_POST['email']);
    $temat = trim($_POST['temat']);
    $wiadomosc = trim($_POST['wiadomosc']);
    
    // Walidacja
    $errors = [];
    if (empty($imie)) $errors[] = "Imię i nazwisko jest wymagane";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Podaj poprawny email";
    if (empty($temat)) $errors[] = "Temat jest wymagany";
    if (empty($wiadomosc)) $errors[] = "Wiadomość jest wymagana";
    
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO kontakt (imie, email, temat, wiadomosc, data_wyslania) 
                                  VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $imie, $email, $temat, $wiadomosc);
            
            if ($stmt->execute()) {
                $success = "Wiadomość została wysłana pomyślnie!";
            } else {
                $errors[] = "Błąd podczas wysyłania wiadomości: " . $conn->error;
            }
        } catch (Exception $e) {
            $errors[] = "Błąd bazy danych: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt – Dojczland Praca</title>
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
            <li><a href="kontakt.php" class="active">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section id="kontakt-formularz">
        <h2>Skontaktuj się z nami</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="imie" placeholder="Imię i nazwisko" required 
                   value="<?= isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : '' ?>">
            
            <input type="email" name="email" placeholder="Adres e-mail" required
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            
            <input type="text" name="temat" placeholder="Temat wiadomości" required
                   value="<?= isset($_POST['temat']) ? htmlspecialchars($_POST['temat']) : '' ?>">
            
            <textarea name="wiadomosc" placeholder="Twoja wiadomość" required><?= 
                isset($_POST['wiadomosc']) ? htmlspecialchars($_POST['wiadomosc']) : '' 
            ?></textarea>
            
            <button type="submit">Wyślij wiadomość</button>
        </form>
    </section>
    <section id="dane-kontaktowe">
        <h2>Dane kontaktowe</h2>
        <p><strong>Portal Dojczland Praca</strong></p>
        <p>ul. Przykładowa 12, 00-000 Warszawa</p>
        <p>Telefon: +48 123 456 789</p>
        <p>E-mail: kontakt@dojczlandpraca.pl</p>
        <p>Godziny pracy: Pon–Pt, 9:00–17:00</p>
    </section>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>