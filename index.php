<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal z ofertami pracy w Niemczech</title>
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
        </ul>
    </nav>
</header>

<main>
    <!-- Wyszukiwarka ofert -->
    <section id="wyszukiwarka">
        <h2>Wyszukiwarka ofert</h2>
        <form action="lista_ofert.php" method="GET">
            <input type="text" name="keyword" placeholder="Stanowisko lub firma">
            <input type="text" name="location" placeholder="Miasto, region">
            <button type="submit">Szukaj</button>
        </form>
    </section>

    <!-- Popularne oferty -->
    <section id="popularne-oferty">
        <h2>Popularne oferty</h2>
        <div class="oferta">
            <h3>Spawacz - Monachium</h3>
            <p>Firma ABC GmbH • Pełny etat • od 3000€</p>
            <a href="oferta_szczegoly.php?id=1">Zobacz szczegóły</a>
        </div>
        <div class="oferta">
            <h3>Opiekun seniora - Berlin</h3>
            <p>Firma SeniorenCare • Część etatu • od 2000€</p>
            <a href="oferta_szczegoly.php?id=2">Zobacz szczegóły</a>
        </div>
    </section>

    <!-- Opinie użytkowników -->
    <section id="opinie">
        <h2>Opinie użytkowników</h2>
        <div class="slider">
            <div class="opinia aktywna">
                <blockquote>"Świetna strona! Pracę znalazłem w tydzień."</blockquote>
                <footer>– Janek z Krakowa</footer>
            </div>
            <div class="opinia">
                <blockquote>"Prosta obsługa, dobre oferty, polecam!"</blockquote>
                <footer>– Kasia z Wrocławia</footer>
            </div>
            <div class="opinia">
                <blockquote>"Dzięki tej stronie zacząłem pracę w Monachium!"</blockquote>
                <footer>– Marek z Gdańska</footer>
            </div>
        </div>
        <div class="slider-buttons">
            <button onclick="poprzednia()">←</button>
            <button onclick="nastepna()">→</button>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script src="skrypty.js"></script>
</body>
</html>