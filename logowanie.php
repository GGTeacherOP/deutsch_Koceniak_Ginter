<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: konto.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, imie, nazwisko, email, haslo, rola FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['haslo']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['imie'] = $user['imie'];
                $_SESSION['nazwisko'] = $user['nazwisko'];
                $_SESSION['rola'] = $user['rola'];
                
                header('Location: ' . ($user['rola'] === 'admin' ? 'admin_panel.php' : 'konto.php'));
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
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        .error {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffeeee;
            border: 1px solid #ffcccc;
            border-radius: 5px;
        }
        
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
                        <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'pracodawca'): ?>
            <li><a href="panel_pracodawcy.php">panel pracodawcy</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Logowanie do konta</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

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

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
