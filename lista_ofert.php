<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista ofert pracy – Dojczland</title>
    <link rel="stylesheet" href="styleindex.css">
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
    <!-- Wyszukiwarka z dodatkowymi filtrami -->
    <section id="wyszukiwarka">
        <h2>Filtruj oferty pracy</h2>
        <form action="lista_ofert.php" method="GET">
            <input type="text" name="keyword" placeholder="Stanowisko lub firma">
            <input type="text" name="location" placeholder="Miasto, region">
            <select name="employment_type">
                <option value="">Wszystkie typy zatrudnienia</option>
                <option value="pelny">Pełny etat</option>
                <option value="czesc">Część etatu</option>
                <option value="tymczasowa">Tymczasowa</option>
            </select>
            <button type="submit">Filtruj</button>
        </form>
    </section>

    <!-- Lista wszystkich ofert -->
    <section id="lista-ofert">
        <h2>Wszystkie oferty pracy</h2>

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

        <div class="oferta">
            <h3>Pracownik magazynu - Hamburg</h3>
            <p>Firma LogiTrans • Tymczasowa • od 2500€</p>
            <a href="oferta_szczegoly.php?id=3">Zobacz szczegóły</a>
        </div>

        <!-- Możesz dynamicznie generować więcej ofert tutaj z PHP/MySQL -->
    </section>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script src="skrypty.js"></script>
</body>
</html>
