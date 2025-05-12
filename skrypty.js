document.addEventListener('DOMContentLoaded', function() {
    const opinie = document.querySelectorAll('.opinia');
    const btnPoprzednia = document.getElementById('poprzednia');
    const btnNastepna = document.getElementById('nastepna');
    let aktualnaOpinia = 0;

    // Funkcja pokazująca opinię
    function pokazOpinie(index) {
        opinie.forEach((opinia, i) => {
            opinia.classList.toggle('aktywna', i === index);
        });
    }

    // Inicjalizacja - pokaż pierwszą opinię
    pokazOpinie(0);

    // Nasłuchiwanie przycisków
    btnPoprzednia.addEventListener('click', function() {
        aktualnaOpinia = (aktualnaOpinia - 1 + opinie.length) % opinie.length;
        pokazOpinie(aktualnaOpinia);
    });

    btnNastepna.addEventListener('click', function() {
        aktualnaOpinia = (aktualnaOpinia + 1) % opinie.length;
        pokazOpinie(aktualnaOpinia);
    });
});