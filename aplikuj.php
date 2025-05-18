<?php
/**
 * Skrypt aplikacyjny do składania wniosków o pracę
 * 
 * Funkcjonalności:
 * - Weryfikacja ID oferty
 * - Sprawdzenie uprawnień użytkownika
 * - Obsługa przesyłania dokumentów (CV i list motywacyjny)
 * - Walidacja danych formularza
 * - Zapisywanie aplikacji w bazie danych
 * - Wysyłanie powiadomień
 */

session_start();
require_once 'config.php'; // Plik konfiguracyjny z danymi do połączenia z bazą

/**
 * Weryfikacja czy przekazano prawidłowe ID oferty
 * Jeśli nie - przekierowanie do listy ofert
 */
if (!isset($_GET['id'])) {
    header("Location: lista_ofert.php");
    exit();
}

$id_oferty = intval($_GET['id']); // Konwersja na integer dla bezpieczeństwa

/**
 * Pobranie tytułu oferty z bazy danych
 * Wykorzystanie prepared statement dla ochrony przed SQL injection
 */
$tytul_oferty = '';
try {
    $stmt = $conn->prepare("SELECT tytul FROM oferty WHERE id = ?");
    $stmt->bind_param("i", $id_oferty);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Jeśli oferta nie istnieje - przekieruj
    if ($result->num_rows === 0) {
        header("Location: lista_ofert.php");
        exit();
    }
    
    $oferta = $result->fetch_assoc();
    $tytul_oferty = htmlspecialchars($oferta['tytul']); // Zabezpieczenie przed XSS
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

/**
 * Obsługa formularza aplikacji
 * Wykonywana tylko dla żądań POST
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzenie czy użytkownik jest zalogowany
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['login_redirect'] = "aplikuj.php?id=$id_oferty";
        header("Location: logowanie.php");
        exit();
    }

    $id_uzytkownika = $_SESSION['user_id'];
    $wiadomosc = trim($_POST['wiadomosc'] ?? '');
    
    // Tablica na błędy walidacji
    $errors = [];
    
    // Zmienne na nazwy plików
    $cv_nazwa = '';
    $list_nazwa = '';
    
    /**
     * Walidacja i przetwarzanie pliku CV
     * - Sprawdzenie czy plik został przesłany
     * - Weryfikacja rozszerzenia (PDF, DOC, DOCX)
     * - Sprawdzenie rozmiaru (max 2MB)
     * - Generowanie unikalnej nazwy pliku
     * - Przeniesienie pliku do folderu uploads
     */
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $cv_info = pathinfo($_FILES['cv']['name']);
        $cv_ext = strtolower($cv_info['extension']);
        
        if (!in_array($cv_ext, ['pdf', 'doc', 'docx'])) {
            $errors[] = 'CV musi być w formacie PDF, DOC lub DOCX';
        } elseif ($_FILES['cv']['size'] > 2097152) { // 2MB
            $errors[] = 'Plik CV jest zbyt duży (maksymalnie 2MB)';
        } else {
            $cv_nazwa = 'cv_' . $id_uzytkownika . '_' . uniqid() . '.' . $cv_ext;
            $upload_path = 'uploads/' . $cv_nazwa;
            
            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $upload_path)) {
                $errors[] = 'Wystąpił problem podczas zapisywania CV';
            }
        }
    } else {
        $errors[] = 'Proszę załączyć plik CV';
    }
    
    /**
     * Walidacja i przetwarzanie listu motywacyjnego (opcjonalny)
     * Analogiczna logika jak dla CV
     */
    if (isset($_FILES['list']) && $_FILES['list']['error'] === UPLOAD_ERR_OK) {
        $list_info = pathinfo($_FILES['list']['name']);
        $list_ext = strtolower($list_info['extension']);
        
        if (!in_array($list_ext, ['pdf', 'doc', 'docx'])) {
            $errors[] = 'List motywacyjny musi być w formacie PDF, DOC lub DOCX';
        } elseif ($_FILES['list']['size'] > 2097152) { // 2MB
            $errors[] = 'Plik listu motywacyjnego jest zbyt duży (maksymalnie 2MB)';
        } else {
            $list_nazwa = 'list_' . $id_uzytkownika . '_' . uniqid() . '.' . $list_ext;
            $upload_path = 'uploads/' . $list_nazwa;
            
            if (!move_uploaded_file($_FILES['list']['tmp_name'], $upload_path)) {
                $errors[] = 'Wystąpił problem podczas zapisywania listu motywacyjnego';
            }
        }
    }
    
    // Jeśli nie ma błędów, zapisz aplikację
    if (empty($errors)) {
        try {
            // Rozpoczęcie transakcji dla zapewnienia atomowości operacji
            $conn->begin_transaction();
            
            // Zapisywanie aplikacji do bazy danych
            $stmt = $conn->prepare("INSERT INTO aplikacje (id_uzytkownika, id_oferty, wiadomosc, cv_plik, list_plik, data_aplikacji, status) 
                                   VALUES (?, ?, ?, ?, ?, NOW(), 'złożona')");
            $stmt->bind_param("iisss", $id_uzytkownika, $id_oferty, $wiadomosc, $cv_nazwa, $list_nazwa);
            $stmt->execute();
            
            // Pobranie ID pracodawcy dla powiadomienia
            $stmt = $conn->prepare("SELECT id_pracodawcy FROM oferty WHERE id = ?");
            $stmt->bind_param("i", $id_oferty);
            $stmt->execute();
            $result = $stmt->get_result();
            $oferta = $result->fetch_assoc();
            $id_pracodawcy = $oferta['id_pracodawcy'];
            
            // Przygotowanie powiadomienia
            $tytul = "Nowa aplikacja na stanowisko: " . $tytul_oferty;
            $tresc = "Nowa aplikacja na stanowisko: " . $tytul_oferty;
            
            $stmt = $conn->prepare("INSERT INTO powiadomienia (id_uzytkownika, tytul, tresc, typ, data_wyslania) 
                                   VALUES (?, ?, ?, 'aplikacja', NOW())");
            $stmt->bind_param("iss", $id_pracodawcy, $tytul, $tresc);
            $stmt->execute();
            
            // Zatwierdzenie transakcji
            $conn->commit();
            
            $_SESSION['success'] = "Aplikacja została wysłana pomyślnie!";
            header("Location: potwierdzenie_aplikacji.php?id=$id_oferty");
            exit();
        } catch (Exception $e) {
            // Wycofanie transakcji w przypadku błędu
            $conn->rollback();
            
            // Usunięcie zapisanych plików
            if (!empty($cv_nazwa) && file_exists('uploads/' . $cv_nazwa)) {
                unlink('uploads/' . $cv_nazwa);
            }
            if (!empty($list_nazwa) && file_exists('uploads/' . $list_nazwa)) {
                unlink('uploads/' . $list_nazwa);
            }
            
            $errors[] = "Wystąpił błąd podczas zapisywania aplikacji: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikuj na stanowisko - <?= $tytul_oferty ?></title>
    <link rel="stylesheet" href="styleindex.css"> <!-- Główny plik CSS -->
    <link href="favicon.ico" rel="icon" type="image/x-icon">
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
    <nav>
        <ul>
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="wyloguj.php">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <?php endif; ?>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <li><a href="admin_panel.php">Panel Admina</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony -->
<main>
    <section id="aplikacja-formularz">
        <h2>Aplikuj na stanowisko: <?= $tytul_oferty ?></h2>
        
        <!-- Wyświetlanie błędów walidacji -->
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <h3>Wystąpiły błędy:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Wyświetlanie komunikatu o sukcesie -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-box">
                <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!-- Formularz aplikacji -->
        <form action="aplikuj.php?id=<?= $id_oferty ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="wiadomosc">Wiadomość do pracodawcy:</label>
                <textarea id="wiadomosc" name="wiadomosc" rows="5"><?= isset($_POST['wiadomosc']) ? htmlspecialchars($_POST['wiadomosc']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="cv">CV (PDF, DOC, DOCX):</label>
                <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
                <small>Maksymalny rozmiar pliku: 2MB</small>
            </div>
            
            <div class="form-group">
                <label for="list">List motywacyjny (PDF, DOC, DOCX, opcjonalnie):</label>
                <input type="file" id="list" name="list" accept=".pdf,.doc,.docx">
                <small>Maksymalny rozmiar pliku: 2MB</small>
            </div>
            
            <button type="submit" class="button">Wyślij aplikację</button>
            <a href="oferta_szczegoly.php?id=<?= $id_oferty ?>" class="button">Powrót do oferty</a>
        </form>
    </section>
</main>

<!-- Stopka strony -->
<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>