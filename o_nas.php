<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O nas – Dojczland Praca</title>
    <link rel="stylesheet" href="style_o_nas.css">
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
            <li><a href="o_nas.php" class="active">O nas</a></li>
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section id="kim-jestesmy">
        <h2>Kim jesteśmy?</h2>
        <p>
            Jesteśmy polskim portalem ogłoszeniowym specjalizującym się w ofertach pracy w Niemczech. 
            Od lat wspieramy osoby szukające zatrudnienia poza granicami kraju, łącząc je z rzetelnymi pracodawcami.
        </p>
    </section>

    <section id="nasza-misja">
        <h2>Nasza misja</h2>
        <p>
            Naszym celem jest uproszczenie procesu poszukiwania pracy za granicą oraz zapewnienie bezpiecznych, przejrzystych ofert. 
            Chcemy, aby każdy kandydat mógł znaleźć pracę zgodną z jego kwalifikacjami i oczekiwaniami.
        </p>
    </section>

    <section id="warto-nam-zaufac">
        <h2>Dlaczego warto nam zaufać?</h2>
        <ul>
            <li>Sprawdzone i aktualne oferty pracy</li>
            <li>Prosty proces aplikacji</li>
            <li>Wsparcie techniczne i merytoryczne</li>
            <li>Wieloletnie doświadczenie na rynku</li>
        </ul>
    </section>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script src="skrypty.js"></script>
</body>
</html>
