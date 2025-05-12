let aktualnaOpinia = 0;
const opinie = document.querySelectorAll('.opinia');
const totalOpinie = opinie.length;

// Inicjalizacja - ukryj wszystkie opinie poza pierwszą
function inicjalizujSlider() {
    opinie.forEach((opinia, index) => {
        if (index === 0) {
            opinia.classList.add('aktywna');
        } else {
            opinia.classList.remove('aktywna');
        }
    });
}

function poprzednia() {
    // Ukrywamy obecną opinię
    opinie[aktualnaOpinia].classList.remove('aktywna');
    
    // Przechodzimy do poprzedniej opinii, z uwzględnieniem cykliczności
    aktualnaOpinia = (aktualnaOpinia - 1 + totalOpinie) % totalOpinie;
    
    // Pokazujemy nową opinię
    opinie[aktualnaOpinia].classList.add('aktywna');
}

function nastepna() {
    // Ukrywamy obecną opinię
    opinie[aktualnaOpinia].classList.remove('aktywna');
    
    // Przechodzimy do następnej opinii, z uwzględnieniem cykliczności
    aktualnaOpinia = (aktualnaOpinia + 1) % totalOpinie;
    
    // Pokazujemy nową opinię
    opinie[aktualnaOpinia].classList.add('aktywna');
}

// Inicjalizacja slidera po załadowaniu strony
document.addEventListener('DOMContentLoaded', inicjalizujSlider);