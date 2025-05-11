<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja – Portal z ofertami pracy</title>
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
    <h2>Rejestracja użytkownika</h2>

    <form action="#" method="post">
        <label for="email">Adres e-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Hasło:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <label for="confirm">Powtórz hasło:</label><br>
        <input type="password" name="confirm" id="confirm" required><br><br>

        <label>
            <input type="checkbox" name="regulamin" required>
            Akceptuję <a href="regulamin.php">regulamin</a>
        </label><br><br>

        <button type="submit">Zarejestruj się</button>
    </form>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>