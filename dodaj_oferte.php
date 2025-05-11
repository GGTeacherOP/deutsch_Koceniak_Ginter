<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj ofertę pracy – Portal z ofertami pracy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <h2>Dodaj nową ofertę pracy</h2>

    <form action="#" method="post">
        <label for="tytul">Tytuł oferty:</label><br>
        <input type="text" id="tytul" name="tytul" required><br><br>

        <label for="firma">Nazwa firmy:</label><br>
        <input type="text" id="firma" name="firma" required><br><br>

        <label for="lokalizacja">Lokalizacja:</label><br>
        <input type="text" id="lokalizacja" name="lokalizacja" required><br><br>

        <label for="opis">Opis oferty:</label><br>
        <textarea id="opis" name="opis" rows="6" required></textarea><br><br>

        <label for="wymagania">Wymagania:</label><br>
        <textarea id="wymagania" name="wymagania" rows="4"></textarea><br><br>

        <label for="kontakt">E-mail kontaktowy:</label><br>
        <input type="email" id="kontakt" name="kontakt" required><br><br>

        <button type="submit">Dodaj ofertę</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>