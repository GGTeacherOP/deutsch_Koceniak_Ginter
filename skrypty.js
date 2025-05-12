let aktualnaOpinia = 0;
const opinie = document.querySelectorAll('.opinia');
const totalOpinie = opinie.length;

// Funkcja inicjalizująca slider (pokazuje pierwszą opinię)
function inicjalizujSlider() {
    opinie.forEach((opinia, index) => {
        if (index === 0) {
            opinia.classList.add('aktywna');
        } else {
            opinia.classList.remove('aktywna');
        }
    });
}

// Przejście do poprzedniej opinii
function poprzednia() {
    opinie[aktualnaOpinia].classList.remove('aktywna');
    aktualnaOpinia = (aktualnaOpinia - 1 + totalOpinie) % totalOpinie;
    opinie[aktualnaOpinia].classList.add('aktywna');
}

// Przejście do następnej opinii
function nastepna() {
    opinie[aktualnaOpinia].classList.remove('aktywna');
    aktualnaOpinia = (aktualnaOpinia + 1) % totalOpinie;
    opinie[aktualnaOpinia].classList.add('aktywna');
}

// Inicjalizacja slidera po załadowaniu strony
document.addEventListener('DOMContentLoaded', inicjalizujSlider);