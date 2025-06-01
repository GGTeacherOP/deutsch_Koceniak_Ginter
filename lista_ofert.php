<?php
/**
 * Skrypt listy ofert pracy
 * 
 * Funkcjonalności:
 * - Filtrowanie ofert po słowach kluczowych, lokalizacji i kategorii
 * - Dynamiczne ładowanie ofert przez AJAX
 * - Wyświetlanie unikalnych kategorii
 * - Responsywny interfejs użytkownika
 */

require_once 'config.php';

// Włącz raportowanie błędów dla celów debugowania
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pobierz parametry filtrowania z GET
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

/**
 * Pobierz unikalne kategorie z ofert dla selecta
 * DISTINCT zapewnia unikalne wartości
 */
$categories = [];
try {
    $stmt = $conn->prepare("SELECT DISTINCT kategoria FROM oferty WHERE kategoria IS NOT NULL AND kategoria != '' ORDER BY kategoria");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['kategoria'])) {
            $categories[] = $row['kategoria'];
        }
    }
    
    $stmt->close();
} catch (Exception $e) {
    die("Wystąpił błąd: " . $e->getMessage());
}

// Jeśli to żądanie AJAX, zwróć tylko HTML z ofertami
if (isset($_GET['ajax'])) {
    getFilteredOffers($keyword, $location, $category, $conn);
    exit();
}

/**
 * Funkcja pobierająca i wyświetlająca oferty zgodne z filtrami
 * @param string $keyword Słowo kluczowe do wyszukania
 * @param string $location Lokalizacja do filtrowania
 * @param string $category Kategoria do filtrowania
 * @param mysqli $conn Połączenie z bazą danych
 */
function getFilteredOffers($keyword, $location, $category, $conn) {
    // Podstawowe zapytanie SQL
    $query = "SELECT o.id, o.tytul, o.opis, o.firma, o.lokalizacja, o.data_dodania, o.kategoria
              FROM oferty o
              WHERE 1=1";

    $params = [];
    $types = '';

    // Dodaj filtry do zapytania w zależności od parametrów
    if (!empty($keyword)) {
        $query .= " AND (o.tytul LIKE ? OR o.firma LIKE ? OR o.opis LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $types .= 'sss';
    }

    if (!empty($location)) {
        $query .= " AND o.lokalizacja LIKE ?";
        $params[] = "%$location%";
        $types .= 's';
    }

    if (!empty($category)) {
        $query .= " AND o.kategoria = ?";
        $params[] = $category;
        $types .= 's';
    }

    // Sortuj wyniki od najnowszych
    $query .= " ORDER BY o.data_dodania DESC";

    // Pobierz oferty z bazy
    $offers = [];
    try {
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $offers[] = $row;
        }
        
        $stmt->close();
    } catch (Exception $e) {
        die("Wystąpił błąd: " . $e->getMessage());
    }

    // Wygeneruj HTML z ofertami
    if (empty($offers)) {
        echo '<div class="brak-ofert">';
        echo '<p>Nie znaleziono ofert spełniających kryteria wyszukiwania.</p>';
        echo '<a href="lista_ofert.php" class="button">Wyświetl wszystkie oferty</a>';
        echo '</div>';
    } else {
        foreach ($offers as $offer) {
            echo '<div class="oferta">';
            echo '<h3>' . htmlspecialchars($offer['tytul']) . '</h3>';
            echo '<p class="firma">' . htmlspecialchars($offer['firma']) . '</p>';
            echo '<p class="lokalizacja">📍 ' . htmlspecialchars($offer['lokalizacja']) . '</p>';
            if (!empty($offer['kategoria'])) {
                echo '<p class="kategoria">🏷️ Kategoria: ' . htmlspecialchars($offer['kategoria']) . '</p>';
            } else {
                echo '<p class="kategoria">🏷️ Brak kategorii</p>';
            }
            echo '<p class="data">📅 ' . date('d.m.Y', strtotime($offer['data_dodania'])) . '</p>';
            echo '<a href="oferta_szczegoly.php?id=' . $offer['id'] . '">Zobacz szczegóły</a>';
            echo '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista ofert pracy – Dojczland</title>
    <link rel="stylesheet" href="styleindex.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
    <style>
        /* Style specyficzne dla listy ofert */
        #wyszukiwarka {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        /* Responsywny układ formularza */
        #wyszukiwarka form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        /* Stylowanie ofert pracy */
        #lista-ofert {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(800px, 1fr));
            gap: 2rem;
        }
        
        .oferta {
            text-align:center;
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #0066cc;
            transition: all 0.3s ease;
        }
        
        .oferta:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Dodatkowe style dla elementów oferty */
        .oferta h3 {
            color: #004080;
            margin-bottom: 0.5rem;
        }
        
        .oferta .firma {
            font-weight: 600;
            color: #0066cc;
        }
        
        .brak-ofert {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .loading {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            display: none;
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
            <li><a href="lista_ofert.php" class="active">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <li><a href="opinie.php">opinie</a></li>
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
    <!-- Sekcja wyszukiwarki z filtrami -->
    <section id="wyszukiwarka">
        <h2>Filtruj oferty pracy</h2>
        <form id="filter-form">
            <div class="form-group">
                <label for="keyword">Szukaj:</label>
                <input type="text" id="keyword" name="keyword" placeholder="Stanowisko, firma lub słowa kluczowe" 
                       value="<?= htmlspecialchars($keyword) ?>">
            </div>
            
            <div class="form-group">
                <label for="location">Lokalizacja:</label>
                <input type="text" id="location" name="location" placeholder="Miasto lub region" 
                       value="<?= htmlspecialchars($location) ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Kategoria:</label>
                <select id="category" name="category">
                    <option value="">Wszystkie kategorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Filtruj</button>
            <a href="lista_ofert.php" class="button" style="background: #666;">Wyczyść filtry</a>
        </form>
    </section>

    <!-- Sekcja listy ofert -->
    <section id="lista-ofert">
        <h2 id="results-count">Znalezione oferty pracy</h2>
        <br>
        <div class="loading">Ładowanie...</div>
        <div id="offers-container">
            <?php getFilteredOffers($keyword, $location, $category, $conn); ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const offersContainer = document.getElementById('offers-container');
    const loadingElement = document.querySelector('.loading');
    const resultsCount = document.getElementById('results-count');
    
    /**
     * Obsługa wysłania formularza
     * Wykorzystuje Fetch API do dynamicznego ładowania ofert
     */
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Pokaż animację ładowania
        loadingElement.style.display = 'block';
        offersContainer.innerHTML = '';
        
        // Przygotuj parametry URL
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Wykonaj żądanie AJAX
        fetch(`lista_ofert.php?ajax=1&${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                offersContainer.innerHTML = html;
                updateResultsCount();
                loadingElement.style.display = 'none';
                
                // Zaktualizuj URL bez przeładowania strony
                history.pushState(null, null, `lista_ofert.php?${params.toString()}`);
            })
            .catch(error => {
                console.error('Error:', error);
                loadingElement.style.display = 'none';
            });
    });
    
    // Automatyczne filtrowanie przy zmianie wartości (opcjonalne)
    const inputs = filterForm.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.dispatchEvent(new Event('submit'));
        });
    });
    
    /**
     * Aktualizuje licznik znalezionych ofert
     */
    function updateResultsCount() {
        const offers = offersContainer.querySelectorAll('.oferta');
        const noResults = offersContainer.querySelector('.brak-ofert');
        
        if (noResults) {
            resultsCount.textContent = 'Brak ofert spełniających kryteria';
        } else {
            const count = offers.length;
            resultsCount.textContent = `Znalezione oferty pracy (${count})`;
        }
    }
    
    // Inicjalizacja licznika
    updateResultsCount();
});
</script>
</body>
</html>