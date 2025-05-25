<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Sekcja meta danych strony -->
    <meta charset="UTF-8">
    <title>Regulamin – Portal z ofertami pracy</title>
    <!-- Responsywność strony -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link do zewnętrznego arkusza stylów -->
    <link rel="stylesheet" href="style_regulamin.css">
    <link href="favicon.ico" rel="icon" type="image/x-icon">
</head>
<body>

<!-- Nagłówek strony z menu nawigacyjnym -->
<header>
    <img src="logo.png" alt="Logo" style=" float:left;margin-left:10px;">
    <h1>Portal z ofertami pracy w Dojczlandzie</h1>
    <nav>
        <ul>
            <!-- Główne menu nawigacyjne -->
            <li><a href="index.php">Strona główna</a></li>
            <li><a href="lista_ofert.php">Oferty pracy</a></li>
            <li><a href="dodaj_oferte.php">Dodaj ofertę</a></li>
            <li><a href="konto.php">Moje konto</a></li>
            <!-- Linki do logowania/rejestracji -->
            <li><a href="rejestracja.php">Rejestracja</a> / <a href="logowanie.php">Logowanie</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
            <li><a href="o_nas.php">O nas</a></li>
            <li><a href="opinie.php">opinie</a></li>
            <!-- Warunkowe wyświetlenie panelu admina -->
            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <li><a href="admin_panel.php">Panel Admina</a></li>
        <?php endif; ?>
                    <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'pracodawca'): ?>
            <li><a href="panel_pracodawcy.php">panel pracodawcy</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Główna zawartość strony z regulaminem -->
<main>
    <h2>Regulamin korzystania z portalu</h2>

    <!-- Sekcja 1: Postanowienia ogólne -->
    <section>
        <h3>1. Postanowienia ogólne</h3>
        <p>Portal ma na celu pośrednictwo w ofertach pracy pomiędzy pracodawcami a kandydatami. Korzystanie z portalu oznacza akceptację niniejszego regulaminu.</p>
    </section>

    <!-- Sekcja 2: Rejestracja użytkownika -->
    <section>
        <h3>2. Rejestracja użytkownika</h3>
        <p>Rejestracja jest dobrowolna, ale niezbędna do pełnego korzystania z funkcji portalu, takich jak dodawanie ofert lub składanie aplikacji.</p>
    </section>

    <!-- Sekcja 3: Dodawanie ofert -->
    <section>
        <h3>3. Dodawanie ofert</h3>
        <p>Oferty pracy mogą być dodawane tylko przez zarejestrowanych pracodawców. Treść oferty musi być zgodna z obowiązującym prawem.</p>
    </section>

    <!-- Sekcja 4: Ochrona danych osobowych -->
    <section>
        <h3>4. Ochrona danych osobowych</h3>
        <p>Dane osobowe użytkowników są przetwarzane zgodnie z obowiązującymi przepisami i naszą polityką prywatności.</p>
    </section>

    <!-- Sekcja 5: Postanowienia końcowe -->
    <section>
        <h3>5. Postanowienia końcowe</h3>
        <p>Portal zastrzega sobie prawo do zmian w regulaminie. Zmiany będą publikowane na tej stronie.</p>
    </section>
</main>

<!-- Stopka strony z podstawowymi informacjami -->
<footer>
    <p>&copy; 2025 ginterkoceniakXDDDDD3wnocy – Wszystkie prawa zastrzeżone</p>
    <!-- Linki do regulaminu i polityki prywatności -->
    <a href="regulamin.php">Regulamin</a> | <a href="polityka_prywatnosci.php">Polityka prywatności</a>
</footer>

</body>
</html>