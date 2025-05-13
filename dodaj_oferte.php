<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj ofertę pracy – Portal z ofertami pracy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_dodaj_oferte.css">
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

    <!-- Lewy animowany blok -->
    <div class="side-block left-block">
        <h3>Najczęstsze wymagania</h3>
        <ul>
            <li>Znajomość języka niemieckiego</li>
            <li>Doświadczenie w zawodzie</li>
            <li>Prawko kat. B</li>
            <li>Gotowość do relokacji</li>
            <li>Umiejętność pracy w zespole</li>
        </ul>
    </div>

    <!-- Główna zawartość -->
    <main>
        <h2>Dodaj nową ofertę pracy</h2>

        <form action="#" method="post">
            <div class="form-group">
                <label for="tytul">Tytuł oferty:</label>
                <input type="text" id="tytul" name="tytul" required>
            </div>

            <div class="form-group">
                <label for="firma">Nazwa firmy:</label>
                <input type="text" id="firma" name="firma" required>
            </div>

            <div class="form-group">
                <label for="lokalizacja">Lokalizacja:</label>
                <input type="text" id="lokalizacja" name="lokalizacja" required>
            </div>

            <div class="form-group">
                <label for="opis">Opis oferty:</label>
                <textarea id="opis" name="opis" rows="6" required></textarea>
            </div>

            <div class="form-group">
                <label for="wymagania">Wymagania:</label>
                <textarea id="wymagania" name="wymagania" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="kontakt">E-mail kontaktowy:</label>
                <input type="email" id="kontakt" name="kontakt" required>
            </div>

            <button type="submit">Dodaj ofertę</button>
        </form>
    </main>

    <!-- Prawy animowany blok -->
    <div class="side-block right-block">
        <h3>Najpopularniejsze stanowiska</h3>
        <ul>
            <li>Pracownik magazynowy</li>
            <li>Opiekunka osób starszych</li>
            <li>Kierowca</li>
            <li>Pracownik budowlany</li>
            <li>Spawacz</li>
        </ul>
    </div>

    <footer>
        <p>&copy; 2025 Portal z ofertami pracy – Wszystkie prawa zastrzeżone</p>
        <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
    </footer>

    <script src="skrypty.js"></script>
</body>
</html>