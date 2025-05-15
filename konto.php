<?php
session_start();
require_once 'config.php';

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header('Location: logowanie.php');
    exit();
}

// Pobierz dane użytkownika z bazy
$user_id = $_SESSION['user_id'];
$user_data = [];

try {
    $stmt = $conn->prepare("SELECT imie, nazwisko, email, rola FROM uzytkownicy WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
    } else {
        // Jeśli nie znaleziono użytkownika, wyloguj
        session_destroy();
        header('Location: logowanie.php');
        exit();
    }
    $stmt->close();
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

// Obsługa wylogowania
if (isset($_GET['wyloguj'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje konto – Portal z ofertami pracy w Niemczech</title>
    <link rel="stylesheet" href="styleindex.css">
    <link rel="stylesheet" href="konto.css">
    <style>
        /* Dodatkowe style specyficzne dla strony konta */
        .konto-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .dane-osobowe,
        .ustawienia-konta {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 64, 128, 0.1);
        }
        
        .dane-osobowe h3,
        .ustawienia-konta h3 {
            color: #004080;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffcc00;
        }
        
        .dane-osobowe p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .dane-osobowe strong {
            color: #0066cc;
            min-width: 100px;
            display: inline-block;
        }
        
        .ustawienia-konta ul {
            list-style: none;
        }
        
        .ustawienia-konta li {
            margin-bottom: 1rem;
        }
        
        .ustawienia-konta a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background-color: rgba(0, 102, 204, 0.1);
        }
        
        .ustawienia-konta a:hover {
            color: #004080;
            background-color: rgba(0, 102, 204, 0.2);
            transform: translateY(-2px);
        }
        
        .rola-info {
            font-style: italic;
            color: #666;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #ccc;
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
            <li><a href="konto.php" class="active">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="konto-panel">
        <div class="dane-osobowe">
            <h3>Dane osobowe</h3>
            <p><strong>Imię:</strong> <?= htmlspecialchars($user_data['imie']) ?></p>
            <p><strong>Nazwisko:</strong> <?= htmlspecialchars($user_data['nazwisko']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
            <p class="rola-info">Typ konta: <?= $user_data['rola'] === 'pracodawca' ? 'Pracodawca' : 'Użytkownik' ?></p>
        </div>

        <div class="ustawienia-konta">
            <h3>Ustawienia konta</h3>
            <ul>
                <li><a href="zmiana_hasla.php">Zmień hasło</a></li>
                <li><a href="edytuj_dane.php">Zaktualizuj dane</a></li>
                <li><a href="?wyloguj=1">Wyloguj się</a></li>
            </ul>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script src="skrypty.js"></script>
</body>
</html>