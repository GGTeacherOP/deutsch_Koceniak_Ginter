<?php
require_once 'config.php';

// Pobierz parametry filtrowania z GET
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Pobierz kategorie dla selecta
$categories = [];
try {
    $stmt = $conn->prepare("SELECT id, nazwa FROM kategorie ORDER BY nazwa");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
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

function getFilteredOffers($keyword, $location, $category, $conn) {
    // Przygotuj zapytanie SQL z filtrami
    $query = "SELECT o.id, o.tytul, o.opis, o.firma, o.lokalizacja, o.data_dodania, 
                     GROUP_CONCAT(k.nazwa SEPARATOR ', ') AS kategorie
              FROM oferty o
              LEFT JOIN oferty_kategorie ok ON o.id = ok.id_oferty
              LEFT JOIN kategorie k ON ok.id_kategorii = k.id
              WHERE 1=1";

    $params = [];
    $types = '';

    // Dodaj filtry do zapytania
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

    if ($category > 0) {
        $query .= " AND ok.id_kategorii = ?";
        $params[] = $category;
        $types .= 'i';
    }

    // Grupuj wyniki i sortuj
    $query .= " GROUP BY o.id ORDER BY o.data_dodania DESC";

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
            if (!empty($offer['kategorie'])) {
                echo '<p class="kategorie">🏷️ ' . htmlspecialchars($offer['kategorie']) . '</p>';
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
    <style>
        /* Dodatkowe style dla listy ofert */
        #wyszukiwarka {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        #wyszukiwarka form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        #wyszukiwarka button {
            height: fit-content;
            padding: 0.8rem;
        }
        
        #lista-ofert {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .oferta {
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
        
        .oferta h3 {
            color: #004080;
            margin-bottom: 0.5rem;
        }
        
        .oferta p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .oferta .firma {
            font-weight: 600;
            color: #0066cc;
        }
        
        .oferta .kategorie {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 1rem;
            font-style: italic;
        }
        
        .oferta .data {
            font-size: 0.8rem;
            color: #999;
            margin-bottom: 1rem;
        }
        
        .oferta a {
            color: #0066cc;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background-color: rgba(0, 102, 204, 0.1);
        }
        
        .oferta a:hover {
            color: #004080;
            background-color: rgba(0, 102, 204, 0.2);
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
        </ul>
    </nav>
</header>

<main>
    <!-- Wyszukiwarka z filtrami -->
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
                    <option value="0">Wszystkie kategorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Filtruj</button>
            <a href="lista_ofert.php" class="button" style="background: #666;">Wyczyść filtry</a>
        </form>
    </section>

    <!-- Lista ofert -->
    <section id="lista-ofert">
        <h2 id="results-count">Znalezione oferty pracy</h2>
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
    
    // Obsługa wysłania formularza
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Pokaż animację ładowania
        loadingElement.style.display = 'block';
        offersContainer.innerHTML = '';
        
        // Pobierz wartości z formularza
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
    
    // Obsługa zmian w polach formularza (opcjonalne automatyczne filtrowanie)
    const inputs = filterForm.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.dispatchEvent(new Event('submit'));
        });
    });
    
    // Funkcja aktualizująca liczbę wyników
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