// Nowa wersja - maksymalnie kompatybilna
function initSlider() {
    const opinie = document.getElementsByClassName('opinia');
    let current = 0;
    
    // Pokazuje aktualną opinię
    function showOpinion(index) {
        for (let i = 0; i < opinie.length; i++) {
            opinie[i].classList.remove('aktywna');
            opinie[i].style.display = 'none';
        }
        opinie[index].classList.add('aktywna');
        opinie[index].style.display = 'block';
    }
    
    // Inicjalizacja
    showOpinion(0);
    
    // Event listeners dla przycisków
    document.getElementById('poprzednia').onclick = function() {
        current = (current - 1 + opinie.length) % opinie.length;
        showOpinion(current);
    };
    
    document.getElementById('nastepna').onclick = function() {
        current = (current + 1) % opinie.length;
        showOpinion(current);
    };
}

// Uruchom slider gdy strona się załaduje
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSlider);
} else {
    initSlider();
}
// Obsługa formularza (przykładowa)
document.querySelector('form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Formularz został wysłany!');
    // Tutaj można dodać faktyczną logikę wysyłania formularza
});