<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Sekcja meta danych strony -->
    <meta charset="UTF-8">
    <title>Polityka prywatności – Portal z ofertami pracy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link do zewnętrznego arkusza stylów -->
    <link rel="stylesheet" href="style_polityka.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
    <nav>
        <ul>
            <!-- Lista linków nawigacyjnych -->
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <!-- Warunkowe wyświetlanie linku do panelu admina -->
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony z polityką prywatności -->
<main>
    <h2>Polityka prywatności</h2>

    <!-- Sekcja 1: Informacje ogólne -->
    <section>
        <h3>1. Informacje ogólne</h3>
        <p>Twoja prywatność jest dla nas ważna. Niniejsza polityka prywatności wyjaśnia, jakie dane gromadzimy, jak je przechowujemy i wykorzystujemy.</p>
    </section>

    <!-- Sekcja 2: Gromadzenie danych -->
    <section>
        <h3>2. Gromadzenie danych</h3>
        <p>Gromadzimy dane takie jak imię, nazwisko, adres e-mail, numer telefonu i dane dotyczące ofert pracy podczas rejestracji i korzystania z portalu.</p>
    </section>

    <!-- Sekcja 3: Wykorzystanie danych -->
    <section>
        <h3>3. Wykorzystanie danych</h3>
        <p>Przetwarzamy dane użytkowników tylko w zakresie niezbędnym do świadczenia usług, kontaktu oraz administracji kontem.</p>
    </section>

    <!-- Sekcja 4: Udostępnianie danych -->
    <section>
        <h3>4. Udostępnianie danych</h3>
        <p>Nie udostępniamy Twoich danych osobowych osobom trzecim bez Twojej wyraźnej zgody, chyba że jest to wymagane przepisami prawa.</p>
    </section>

    <!-- Sekcja 5: Pliki cookies -->
    <section>
        <h3>5. Pliki cookies</h3>
        <p>Portal korzysta z plików cookies w celu poprawy działania strony i dostosowania jej do preferencji użytkowników.</p>
    </section>

    <!-- Sekcja 6: Zmiany polityki prywatności -->
    <section>
        <h3>6. Zmiany polityki prywatności</h3>
        <p>Zastrzegamy sobie prawo do wprowadzania zmian w polityce prywatności. Zmiany będą publikowane na tej stronie.</p>
    </section>
</main>

<!-- Stopka strony z linkami i informacją o prawach autorskich -->
<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>