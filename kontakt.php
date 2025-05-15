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
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        
        header {
            background: linear-gradient(135deg, #004080 0%, #0066cc 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 0.8rem;
        }
        
        nav ul li {
            margin: 0 1.2rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem 1rem;
        }
        
        nav ul li a.active {
            color: #ffcc00;
        }
        
        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        #kontakt-formularz, #dane-kontaktowe {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #0066cc;
            margin-top: 0;
        }
        
        input, textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        textarea {
            height: 150px;
            resize: vertical;
        }
        
        button {
            background: #0066cc;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #004080;
        }
        
        .error {
            color: red;
            margin-bottom: 1rem;
        }
        
        .success {
            color: green;
            margin-bottom: 1rem;
        }
        
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 2rem;
        }
        
        footer a {
            color: #ffcc00;
            text-decoration: none;
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
            <li><a href="kontakt.php" class="active">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
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