// Porta l'utente all'inizio della pagina quando si ricarica
function scrollToTop() {
    const url = window.location.href;
    if(!url.includes('#'))
        window.scrollTo({ top: 0, behavior: 'smooth' });
}
window.onload = scrollToTop;

/* GESTIONE GENERAZIONE VALIGIA
- Listener del pulsante submit (genera consigliati)
    - AJAX per ottenere una pagina contenente i consigliati
    da inserire nel div id="results" in index.php
*/

const form = document.getElementById('valigia-form'); // Riferimento al form

// Evita l'invio del form premendo Enter
form.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
    }
});

document.getElementById('submitButton').addEventListener('click', (event) => {
    isShowingBag = true;

    event.preventDefault(); // Evita il ricaricamento della pagina
    const existingScript = document.querySelector('script[src="src/js/bagCreation.js"]');
    if (existingScript) {
        existingScript.remove();
    }

    const results = document.getElementById('results'); // Sezione dove visualizzare i risultati

    results.classList.remove('hidden');
    results.classList.add('visible');
    // scrolla a results

    // Crea un oggetto FormData per raccogliere i dati del form
    const formData = new FormData(form);

    // Effettua la richiesta AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true); // Usa l'attributo "action" del form per sapere dove inviare i dati
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // Mostra la risposta del server
                
                results.innerHTML = xhr.responseText;
                const script = document.createElement('script');
                script.src = 'src/js/bagCreation.js';
                document.body.appendChild(script);
                
                window.scrollTo({ top: results.offsetTop + 300, behavior: 'smooth' });
                isShowingBag = false;
            } else {
                results.innerHTML = `<p>Errore durante la richiesta: ${xhr.status}</p>`;
            }
        }
    };

    // Invia i dati del form
    xhr.send(formData);
});



/* Listener per impostare la data minima di ritorno */
document.getElementById("departurDate").addEventListener('change', () => {
    selected = document.getElementById("departurDate").value;
    document.getElementById('returnDate').setAttribute('min', selected);
    document.getElementById('returnDate').value = null;
}); 

let isReady = true;
// Funzione per validare lo step-1 ed avanzare allo step-2
document.getElementById('destinazione').addEventListener('change', validateStep1);
document.getElementById('returnDate').addEventListener('change', validateStep1);
// Event listener per avanzare allo step 3
document.getElementById('dimensione').addEventListener('input', () => {
    document.getElementById('step-3').classList.remove('hidden'); // Mostra step-3
    document.getElementById('step-3').setAttribute('class', 'visible');
    scrollToStep('step-3'); // Scroll animato a step-3
});
// Event listener per avanzare allo step 4
document.getElementById('compagnia').addEventListener('input', () => {
    document.getElementById('step-4').classList.remove('hidden'); // Mostra step-4
    document.getElementById('step-4').setAttribute('class', 'visible');
    scrollToStep('step-4'); // Scroll animato a step-4
});
// Event listener per avanzare allo step 5
document.getElementById('tipoDiViaggio').addEventListener('input', () => {
    document.getElementById('step-5').classList.remove('hidden'); // Mostra step-5
    document.getElementById('step-5').setAttribute('class', 'visible');
    scrollToStep('step-5'); // Scroll animato a step-5
});


function validateStep1() {
    const destinazione = document.getElementById('destinazione').value.trim();
    const go = document.getElementById('departurDate').value;
    const back = document.getElementById('returnDate').value;
    if (destinazione && go && back) {
        document.getElementById('step-2').classList.remove('hidden'); // Mostra step-2
        document.getElementById('step-2').setAttribute('class', 'visible');
        isReady = isReady && true;
        scrollToStep('step-2'); // Scroll animato a step-2
    }
}


// Funzione per scorrere verso lo step specificato
function scrollToStep(stepId) {
    const step = document.getElementById(stepId);
    step.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Gestione dello scrolling per mantenere centrato lo step attivo
let isScrolling = false;
let isShowingBag = false;
window.addEventListener('scroll', () => {
    if (isScrolling || isShowingBag) return; // Evita loop durante lo scrolling
    const steps = document.querySelectorAll('form > div'); // Tutti gli step
    let activeStep = null;

    steps.forEach(step => {
        const rect = step.getBoundingClientRect();
        if (rect.top >= 0 && rect.top < window.innerHeight / 2) {
            activeStep = step;
        }
    });

    if (activeStep) {
        isScrolling = true;
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => isScrolling = false, 500); // Sblocca dopo l'animazione
    }
});


/*
// Simulazione di utente loggato
const isLoggedIn = false; // Cambia in true per testare lo stato loggato
const userAction = document.getElementById('user-action');
if (isLoggedIn) {
    userAction.textContent = 'Profilo';
    userAction.href = 'profilo.php';
}
*/


/* vibrazione iniziale */
document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector('main');    
    const scorri = document.getElementById("scorri"); 
    // Funzione per attivare la vibrazione
    setTimeout(() => {
        if(window.scrollY === 0)
            main.classList.add('vibrating'); // Aggiungi la classe vibrating per avviare l'animazione
    }, 3000); 
    window.addEventListener('scroll', () => {
        if (main.classList.contains('vibrating')) {
            main.classList.remove('vibrating'); // Rimuove la vibrazione quando si scrolla
        }
    });

    // Funzione per andare a step-1 se viene cliccato 'scorri'
    scorri.addEventListener('click', () => {
        scrollToStep('step-1');
    }); 

    const sottotitolo = document.getElementById('sottotitolo'); 

    window.addEventListener('scroll', () => {
        if (scorri.classList.contains('fadeIn')) {
            scorri.classList.remove('fadeIn');
            scorri.classList.add('fadeOut');
        }
    });

    setTimeout(() => {
        scorri.classList.remove('opacity');
        scorri.classList.add('fadeIn');
        sottotitolo.classList.remove('fadeIn');
        sottotitolo.classList.add('fadeOut');
    }, 5000);

});

/* rende opaco l'header in seguito allo scroll */
window.addEventListener('scroll', () => {
    const headerContainer = document.querySelector('.headerContainer'); 
    if (window.scrollY === 0) {
        headerContainer.classList.remove('opacity'); 
    } else {
        headerContainer.classList.add('opacity'); 
    }
});



// Suggerimenti per le destinazioni
const apiKey = "99497e02fdb0b41bdb8ca9a17fdd6bae";
const input = document.getElementById("destinazione");
const suggestionsList = document.getElementById("suggestions-list");

let currentRequest = null; // Variabile per tracciare la richiesta corrente

input.addEventListener("input", () => {
    const query = input.value.trim();
    
    if (query.length > 2) {
        // Annulla la richiesta precedente, se esiste
        if (currentRequest) {
            currentRequest.abort();
        }
        
        // Crea una nuova richiesta
        currentRequest = new XMLHttpRequest();
        const apiUrl = `http://api.openweathermap.org/geo/1.0/direct?q=${query}&limit=5&appid=${apiKey}`;
        currentRequest.open('GET', apiUrl, true);

        currentRequest.onreadystatechange = function () {
            if (currentRequest.readyState === 4) {
                if (currentRequest.status === 200) {
                    const data = JSON.parse(currentRequest.responseText);
                    suggestionsList.innerHTML = "";
                    data.forEach(location => {
                        const listItem = document.createElement("li");
                        listItem.textContent = `${location.name}, ${location.state || ""}, ${location.country}`;
                        listItem.addEventListener("click", () => {
                            input.value = `${location.name}, ${location.state || ""}, ${location.country}`;
                            suggestionsList.innerHTML = "";
                        });
                        suggestionsList.appendChild(listItem);
                    });
                } else {
                    //console.error("Errore nell'autocompletamento:", currentRequest.status);
                }
                currentRequest = null; // Reset della variabile alla fine della richiesta
            }
        };

        currentRequest.send();
    } else {
        suggestionsList.innerHTML = "";
        // Annulla eventuale richiesta in corso se l'input è troppo corto
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }
    }
});



/* --------------- ALLERT PER MODIFICHE NON SALVATE --------------- */
localStorage.setItem('savingBag', 'false');
window.addEventListener('beforeunload', (event) => {
    if (localStorage.getItem('savingBag') === 'false') {
        const form = document.getElementById('valigia-form');
        const formData = new FormData(form);
        let isFormFilled = false;

        for (let value of formData.values()) {
            if (value.trim() !== "") {
                isFormFilled = true;
                break;
            }
        }
        if (isFormFilled) {
            event.preventDefault();
            if (confirm('Sei sicuro di voler abbandonare la pagina? Le modifiche non salvate andranno perse.')) {
                return;
            }
        }
    } else {
        alert('Salvataggio in corso. Attendere...');
    }
});