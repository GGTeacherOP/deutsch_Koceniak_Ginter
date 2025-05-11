<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt – Dojczland Praca</title>
    <link rel="stylesheet" href="style_kontakt.css">
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
            <li><a href="kontakt.php" class="active">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
        </ul>
    </nav>
</header>

<main>
    <!-- Formularz kontaktowy -->
    <section id="kontakt-formularz">
        <h2>Skontaktuj się z nami</h2>
        <form action="kontakt_wyslij.php" method="POST">
            <input type="text" name="name" placeholder="Imię i nazwisko" required>
            <input type="email" name="email" placeholder="Adres e-mail" required>
            <textarea name="message" placeholder="Twoja wiadomość" rows="6" required></textarea>
            <button type="submit">Wyślij wiadomość</button>
        </form>
    </section>

    <!-- Dane kontaktowe -->
    <section id="dane-kontaktowe">
        <h2>Dane kontaktowe</h2>
        <p><strong>Portal Dojczland Praca</strong></p>
        <p>ul. Przykładowa 12, 00-000 Warszawa</p>
        <p>Telefon: +48 123 456 789</p>
        <p>E-mail: kontakt@dojczlandpraca.pl</p>
        <p>Godziny pracy: Pon–Pt, 9:00–17:00</p>
    </section>
</main>

<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

<script src="skrypty.js"></script>
</body>
</html>
