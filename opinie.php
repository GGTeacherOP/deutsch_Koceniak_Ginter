<?php
session_start();
require_once 'config.php'; // Plik z konfiguracją połączenia z bazą danych

// Inicjalizacja zmiennych
$errors = [];
$success = '';

// Obsługa formularza dodawania opinii
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzenie, czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php");
        exit();
    }

    $id_uzytkownika = $_SESSION['user_id'];
    $tresc = trim($_POST['tresc']);
    $ocena = intval($_POST['ocena']); // Pobranie oceny

    // Walidacja treści opinii
    if (empty($tresc)) {
        $errors[] = "Treść opinii jest wymagana.";
    } elseif (strlen($tresc) < 10) {
        $errors[] = "Treść opinii musi mieć co najmniej 10 znaków.";
    }

    // Walidacja oceny
    if ($ocena < 1 || $ocena > 5) {
        $errors[] = "Ocena musi być w zakresie od 1 do 5.";
    }

    // Jeśli nie ma błędów, zapisz opinię do bazy
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO opinie (id_uzytkownika, tresc, ocena, data_dodania) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("isi", $id_uzytkownika, $tresc, $ocena); // Dodano ocena
            if ($stmt->execute()) {
                $success = "Opinia została dodana pomyślnie!";
            } else {
                $errors[] = "Wystąpił błąd podczas dodawania opinii: " . $conn->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "Wystąpił błąd: " . $e->getMessage();
        }
    }
}

// Pobieranie wszystkich opinii
$opinie = [];
try {
    $stmt = $conn->prepare("SELECT o.tresc, u.imie, u.nazwisko, o.ocena FROM opinie o JOIN uzytkownicy u ON o.id_uzytkownika = u.id ORDER BY o.data_dodania DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $opinie[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opinie – Portal z ofertami pracy</title>
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny arkusz stylów -->
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        /* Styl dla sekcji opinii */
        #opinie-lista {
            text-align: center;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(800px, 1fr));
            gap: 1.5rem;
            margin: 0 auto; /* Wyśrodkowanie kontenera */
            padding: 20px; /* Dodatkowy padding */
        }
        .opinia {
            text-align: center;
            background-color: #f0f8ff; /* Ustawienie koloru tła */
            border: 1px solid #004080; /* Ustawienie koloru ramki */
            border-radius: 10px;
            padding: 1rem; /* Dostosowanie paddingu */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex; /* Umożliwia elastyczne rozmieszczenie elementów */
            justify-content: space-between; /* Rozmieszcza elementy wzdłuż osi poziomej */
            align-items: center; /* Wyrównuje elementy w pionie */
            margin: 10px 0; /* Marginesy między opiniami */
        }
        .opinia:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        .opinia strong {
            color: #004080;
            margin-right: 10px; /* Odstęp między imieniem a treścią */
        }
        .tresc-opinii {
            text-align: center;
            flex-grow: 1; /* Pozwala treści opinii zająć dostępne miejsce */
        }
        .ocena-opinii {
            text-align: center;
            font-size: 1rem;
            color: black;
            margin-left: 10px; /* Odstęp między treścią a oceną */
            flex-shrink: 0; /* Zapobiega zmniejszaniu się elementu */
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
            <li><a href="opinie.php">Opinie</a></li>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'pracodawca'): ?>
            <li><a href="panel_pracodawcy.php">panel pracodawcy</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h2>Opinie użytkowników</h2>

    <!-- Wyświetlanie błędów walidacji -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Wyświetlanie komunikatu o sukcesie -->
    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Formularz dodawania opinii -->
    <form action="opinie.php" method="post">
        <div class="form-group">
            <label for="tresc">Treść opinii:</label>
            <textarea id="tresc" name="tresc" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="ocena">Ocena:</label>
            <select id="ocena" name="ocena" required>
                <option value="">Wybierz ocenę</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <button type="submit">Dodaj opinię</button>
    </form>

    <h3>Wszystkie opinie:</h3>
    <div id="opinie-lista">
        <?php if (empty($opinie)): ?>
            <p>Brak opinii do wyświetlenia.</p>
        <?php else: ?>
            <div class="opinie-container">
                <?php foreach ($opinie as $opinia): ?>
                    <div class="opinia">
                        <strong><?= htmlspecialchars($opinia['imie'] . ' ' . $opinia['nazwisko']) ?>:</strong>
                        <span class="tresc-opinii"><?= htmlspecialchars($opinia['tresc']) ?></span>
                        <span class="ocena-opinii">(Ocena: <?= htmlspecialchars($opinia['ocena']) ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>
