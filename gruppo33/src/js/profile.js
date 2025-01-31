/* FUNZIONAMENTO PULSANTI LATERALI PER GESTIRE LA VISUALIZZAZIONE */
const bagsButton = document.getElementById('bagsButton');
const infoButton = document.getElementById('infoButton');

const bags = document.getElementById('bags'); // vista delle valige dell'utente
const info = document.getElementById('info'); // vista delle informazioni dell'utente

// mostro le valige dell'utente
bagsButton.addEventListener('click', () => {
	bags.classList.remove('hidden');
    info.classList.add('hidden');
});

// mostro le informazioni dell'utente
infoButton.addEventListener('click', () => {
    info.classList.remove('hidden');
    bags.classList.add('hidden');
});

// Porta l'utente all'inizio della pagina quando si ricarica
function scrollToTop() {
    const urlParams = new URLSearchParams(window.location.search);
    // profile.php?show=bags, dopo una modifica alla valigia
    if (urlParams.get('show') === 'bags') {
        bagsButton.click();
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
window.onload = scrollToTop;

/* ----------------BAGS---------------- */
// aggiunta di una valigia -> riporta ad index.php#step-1 (creazione di una nuova valigia)
function addBagForm() {
    const currentUrl = window.location.href;
    const index = currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length;
    const substring = currentUrl.substring(0, index); 
    window.location.href = substring + 'index.php#step-1';
}

// visualizzazione di una valigia esistente
function showBag(bag) { 
    const bagID = bag.getAttribute('bagID');
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'bag.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'bagID';
    input.value = bagID;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
/* -- FUNZIONE PER ORDINARE LE VALIGIE 
La funzione putBagsInOrder(bags) prende in input le valige ordinatate
e le inserisce nell'HTML (all'interno del div con id="bagsContainer")
*/
function putBagsInOrder(bags) {
    const bagsContainer = document.getElementById('bagsContainer');
    bagsContainer.innerHTML = '';
    bags.forEach(bag => {
        const preferenze = JSON.parse(bag.preferenze);
        const destinazione = preferenze.destinazione;
        const date = preferenze.date;
        const dimensione = preferenze.dimensione;

        const bagDiv = document.createElement('div');
        bagDiv.classList.add('behindBag');

        const bagElement = document.createElement('div');
        bagElement.classList.add('bag');
        bagElement.setAttribute('bagID', bag.id);
        bagElement.addEventListener('click', () => showBag(bagElement));
        bagDiv.appendChild(bagElement);

        const bagHeader = document.createElement('div');
        bagHeader.classList.add('bagHeader');
        bagElement.appendChild(bagHeader);

        const bagName = document.createElement('p');
        bagName.classList.add('bagName');
        bagName.textContent = bag.nome;
        bagHeader.appendChild(bagName);

        const destination = document.createElement('p');
        destination.classList.add('destination');
        destination.textContent = 'Destinazione: ' + destinazione;
        bagHeader.appendChild(destination);

        const bagDetails = document.createElement('div');
        bagDetails.classList.add('bagDetails');
        bagElement.appendChild(bagDetails);

        const date1 = document.createElement('div');
        date1.classList.add('date');
        bagDetails.appendChild(date1);

        const label1 = document.createElement('p');
        label1.classList.add('label');
        label1.textContent = 'Partenza';
        date1.appendChild(label1);

        const value1 = document.createElement('p');
        value1.classList.add('value');
        value1.textContent = date[0];
        date1.appendChild(value1);

        const date2 = document.createElement('div');
        date2.classList.add('date');
        bagDetails.appendChild(date2);

        const label2 = document.createElement('p');
        label2.classList.add('label');
        label2.textContent = 'Ritorno';
        date2.appendChild(label2);

        const value2 = document.createElement('p');
        value2.classList.add('value');
        value2.textContent = date[1];
        date2.appendChild(value2);

        const image = document.createElement('div');
        image.classList.add('image');
        bagDetails.appendChild(image);

        const img = document.createElement('img');
        switch(dimensione){
            case 'piccola':
                img.src = '../images/profile/valigiaPiccola.png';
                img.alt = 'Valigia Piccola';
                break;
            case 'media':
                img.src = '../images/profile/valigiaMedia.png';
                img.alt = 'Valigia Media';
                break;
            case 'grande':
                img.src = '../images/profile/valigiaGrande.png';
                img.alt = 'Valigia Grande';
                break;
            default:
                img.src = '../images/profile/valigiaBase.png';
                img.alt = 'immagine Valigia';
        }
        image.appendChild(img);
        bagsContainer.appendChild(bagDiv);
    });
    // Aggiungo il pulsante per aggiungere una nuova valigia
    const behindAdd = document.createElement('div');
    behindAdd.classList.add('behindBag');
    behindAdd.id = 'behindAdd';

    const addBag = document.createElement('div');
    addBag.classList.add('addBag');
    addBag.addEventListener('click', addBagForm);
    const p = document.createElement('p');
    p.textContent = 'Clicca per aggiungere una nuova valigia';
    addBag.appendChild(p);
    behindAdd.appendChild(addBag);

    bagsContainer.appendChild(behindAdd);
}

/* ORDINAMENTO DELLE VALIGIE */
function sortBags() {
    const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/profile.php"
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/getBags.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.error) {
                console.error(response.error);
            } else {
                const bags = response.bags;
                putBagsInOrder(bags);
            }
        }
    };
    const sortingSelect = document.getElementById('sortingSelect');
    const orderSelect = document.getElementById('orderSelect');

    const sortCriteria = sortingSelect.value;
    const sortOrder = orderSelect.value;
    xhr.send(`sortCriteria=${sortCriteria}&sortOrder=${sortOrder}`);
}
// primo ordinamento delle valigie
sortBags();

/* ----------------INFO---------------- */
//password attuale inserita --> si abilitano i campi per la nuova password
const currentPassword = document.getElementById('currentPassword');
const newPassword = document.getElementById('newPassword');
const repeatPassword = document.getElementById('repeatPassword'); 


/* FORM PER IL CAMBIO PASSWORD */
const changePassword = document.getElementById('changePassword');
const passwordButton = document.getElementById('passwordButton');

// Funzione per pulire i campi della password
function clearPasswordFields() {
    currentPassword.value = '';
    newPassword.value = '';
    repeatPassword.value = '';
    newPassword.disabled = true;
    repeatPassword.disabled = true;
    saveButton.disabled = true; 
}

passwordButton.addEventListener('click', () => {
    if(changePassword.classList.contains('hidden')) {
        clearPasswordFields();
        changePassword.classList.remove('hidden');
        passwordButton.classList.add('hidden'); 
    } 
});



currentPassword.addEventListener('change', () => {
    if (currentPassword.value.length > 0) {
        newPassword.disabled = false;
        checkCorrectOldPassword();
    } else {
        newPassword.disabled = true;
        repeatPassword.disabled = true;
        newPassword.value = '';
        repeatPassword.value = '';
        const passwordErrorMessage = document.getElementById('passwordErrorMessage');
        if (!passwordErrorMessage.classList.contains('hidden')) {
            passwordErrorMessage.classList.add('hidden');
        }
    }
}); 

const passwordDiverseErrorMessage = document.getElementById('passwordDiverseErrorMessage');
const passwordNonValidaErrorMessage = document.getElementById('passwordNonValidaErrorMessage');

newPassword.addEventListener('input', () => {
    if (newPassword.value.length > 0) {
        repeatPassword.disabled = false;

        if (repeatPassword.value.length > 0) {
            if (repeatPassword.value === newPassword.value) {
                // togli le password non coincidono
                if (!passwordDiverseErrorMessage.classList.contains('hidden')) {
                    passwordDiverseErrorMessage.classList.add('hidden');
                }
            } else {
                // metti le password non coincidono
                if (passwordDiverseErrorMessage.classList.contains('hidden')){
                    passwordDiverseErrorMessage.classList.remove('hidden');
                }
            }
        }

        if (repeatPassword.value !== newPassword.value) {
            saveButton.disabled = true;
        } else {
            checkCorrectOldPassword();
        }
    } else {
        // rimuovi i messaggi d'errore
        if (!passwordNonValidaErrorMessage.classList.contains('hidden')){
            passwordNonValidaErrorMessage.classList.add('hidden');
        }
        if (!passwordDiverseErrorMessage.classList.contains('hidden')){
            passwordDiverseErrorMessage.classList.add('hidden');
        }
        repeatPassword.disabled = true;
        repeatPassword.value = '';
    }
});

newPassword.addEventListener('change', () => {
    if (newPassword.value.length > 0) {
        if (checkCorrectNewPassword(newPassword.value)) {
            // togli l'errore di password non valida
            if (!passwordNonValidaErrorMessage.classList.contains('hidden')){
                passwordNonValidaErrorMessage.classList.add('hidden');
            }
            checkCorrectOldPassword();
        } else {
            // metti l'errore di password non valida
            const submitButton = document.getElementById('saveButton');
            submitButton.disabled = true;
            if (passwordNonValidaErrorMessage.classList.contains('hidden')){
                passwordNonValidaErrorMessage.classList.remove('hidden');
            }
        }
    }
});


repeatPassword.addEventListener('input', () => {
    if (newPassword.value.length > 0) {
        if (repeatPassword.value === newPassword.value) {
            checkCorrectOldPassword();
        } else {
            saveButton.disabled = true;
        }
    } else {
        // rimuovi il messaggio d'errore
        if (!passwordDiverseErrorMessage.classList.contains('hidden')) {
            passwordDiverseErrorMessage.classList.add('hidden');
        }
        saveButton.disabled = true;
    }
});

repeatPassword.addEventListener('change', () => {
    if (repeatPassword.value.length > 0 && newPassword.value.length > 0) {
        if (repeatPassword.value === newPassword.value) {
            // togli le password non coincidono
            if (!passwordDiverseErrorMessage.classList.contains('hidden')) {
                passwordDiverseErrorMessage.classList.add('hidden');
            }
        } else {
            // metti le password non coincidono
            if (passwordDiverseErrorMessage.classList.contains('hidden')) {
                passwordDiverseErrorMessage.classList.remove('hidden');
            }
        }
    }
});

// funzione per cambiare la password dell'utente
function changeUserPassword(oldPassword, newPassword) {
    const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/profile.php"
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/changePassword.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.error) {
                console.error(response.error);
                alert('Errore: ' + response.error);
            } else {
                alert('Password cambiata con successo.'); 
            }
        }
    };
    xhr.send(`oldPassword=${oldPassword}&newPassword=${newPassword}`);
}

function clearAllData(removeUser = false) {
    // elimina tutti i dati della sessione dell'utente
    const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/profile.php"
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/logout.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Sessione eliminata');
            sessionStorage.clear();
            localStorage.clear();
            window.location.href = '../index.php';
        }
    };
    xhr.send(`removeUser=${removeUser}`);
}

/* ELIMINA ACCOUNT */
const deleteButton = document.getElementById('deleteButton');
deleteButton.addEventListener('click', () => { 
    const confirmDelete = confirm('Sei sicuro di voler eliminare il tuo account?');
    if(confirmDelete) {
        // eliminare l'account
        clearAllData(removeUser = true);
    }
});

/* LOG OUT DALL'ACCOUNT */
const logOutButton = document.getElementById('logOutButton');
logOutButton.addEventListener('click', () => {
    const confirmLogout = confirm('Sei sicuro di voler uscire dal tuo account?');
    if(confirmLogout) {
        clearAllData(removeUser = false);
    }
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

/* -------------------- INFORMAZIONI DI PROFILO VISUALIZZABILI -- VS -- INFORMAZIONI DI PROFILO EDITABILI -------------------- */
const viewMode = document.querySelectorAll('.onView');
const editMode = document.querySelectorAll('.onEdit');
const editButton = document.getElementById('editButton'); 
const profileInfos = document.querySelectorAll('.profileInfo');
let userData = {
    name: document.getElementById('name').value,
    surname: document.getElementById('surname').value,
    username: document.getElementById('username').value,
};

function onViewProfile() {
  viewMode.forEach((element) => {
    element.classList.remove('hidden');
  });
  editMode.forEach((element) => {
    element.classList.add('hidden');
  });
  profileInfos.forEach((element) => {
    element.disabled = true;
    element.readonly = true;
  });

  // rende invisibile il form di cambio password in viewMode
  if(!changePassword.classList.contains("hidden"))
    changePassword.classList.add("hidden"); 
}

function onEditProfile() {
    userData = {
        name: document.getElementById('name').value,
        surname: document.getElementById('surname').value,
        username: document.getElementById('username').value,
    };
    if(!passwordChanges()) {
        saveButton.disabled = false;
    }
  viewMode.forEach((element) => {
    element.classList.add('hidden');
  });
  editMode.forEach((element) => {
    element.classList.remove('hidden');
  });
  profileInfos.forEach((element) => {
    element.disabled = false;
    element.readonly = false;
  });
}
onViewProfile(); // visualizzazione di default

editButton.addEventListener('click', ()=> {
    onEditProfile(); 
    passwordButton.classList.remove("hidden"); 
});
/* funzionamento in editMode */

// pulsante Annulla
const cancelButton = document.getElementById("cancelButton"); 

cancelButton.addEventListener('click', () => {
    let changes = anyChanges();
    if(changes) {
        if (!confirm("Ci sono modifiche non salvate. Sei sicuro di voler annullare?"))
            return;
    }
    const nome = document.getElementById('name');
    const cognome = document.getElementById('surname');
    const username = document.getElementById('username');
    nome.value = userData.name;
    cognome.value = userData.surname;
    username.value = userData.username;
    onViewProfile(); 
}); 

// pulsante Salva cambiamenti 
const saveButton = document.getElementById('saveButton');

saveButton.addEventListener('click', (event) => {
    if(!anyChanges()) {
        onViewProfile();
        return;
    }
    saveChanges(passwordChanges());
});


/* ------------------ Utility ------------------ */
function infoChanges() {
    let changes = false;
    profileInfos.forEach((element) => {
        if(element.value !== userData[element.id]) {
            changes = true;
        }
    });
    return changes;
}

function passwordChanges() {
    return (currentPassword.value.length > 0);
}

function anyChanges() {
    return infoChanges() || passwordChanges();
}

function anyInfoMissing() {
    let missing = false;
    profileInfos.forEach((element) => {
        if(element.value.length === 0) {
            missing = true;
        }
    });
    return missing;
}

// withPassword = false -> non si cambia la password (default)
function saveChanges(withPassword = false) {
    const name = document.getElementById('name').value;
    const surname = document.getElementById('surname').value;
    const username = document.getElementById('username').value;
    if (anyInfoMissing()) {
        alert('Compila tutti i campi');
        return;
    }
    const oldPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const repeatPassword = document.getElementById('repeatPassword').value;

    const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/profile.php"
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/updateProfile.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.error) {
                console.error(response.error);
                alert('Errore: ' + response.error);
                oldPassword.value = '';
                newPassword.value = '';
                repeatPassword.value = '';
                
            } else {
                userData = {
                    name: name,
                    surname: surname,
                    username: username,
                };
                oldPassword.value = '';
                newPassword.value = '';
                repeatPassword.value = '';
                onViewProfile();
            }
        }
    };
    if (withPassword) {
        if (newPassword !== repeatPassword) {
            alert('Le password non corrispondono');
            return;
        }
        if (oldPassword.length === 0 || newPassword.length === 0 || repeatPassword.length === 0) {
            alert('Compila tutti i campi');
            return;
        }
        if (!checkCorrectNewPassword(newPassword)) {
            alert('La nuova password deve essere di 8-16 caratteri con almeno una maiuscola e un simbolo fra %&@!');
            return;
        }
        xhr.send(`name=${name}&surname=${surname}&username=${username}&oldPassword=${oldPassword}&newPassword=${newPassword}`);
    } else {
        xhr.send(`name=${name}&surname=${surname}&username=${username}`);
    }
}

function checkCorrectOldPassword() {
    const oldPassword = document.getElementById('currentPassword').value;
    const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/profile.php"
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/checkPassword.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.error) {
                console.error(response.error);
                alert('Errore: ' + response.error);
            } else {
                if (response.correct) {
                    const saveButton = document.getElementById('saveButton');
                    if (newPassword.value.length > 0 && newPassword.value === repeatPassword.value)
                        saveButton.disabled = false;
                    const errorMessage = document.getElementById('passwordErrorMessage');
                    if (!errorMessage.classList.contains('hidden')) {
                        errorMessage.classList.add('hidden');
                    }
                } else {
                    const saveButton = document.getElementById('saveButton');
                    saveButton.disabled = true;
                    const errorMessage = document.getElementById('passwordErrorMessage');
                    if (errorMessage.classList.contains('hidden')) {
                        errorMessage.classList.remove('hidden');
                    }
                }
            }
        }
    };
    xhr.send(`oldPassword=${oldPassword}`);
}

function checkCorrectNewPassword(newPassword){
    // Regex per verificare la password:
    // - Da 8 a 16 caratteri
    // - Almeno una maiuscola
    // - Almeno un simbolo fra %&@!
    return (/^(?=.*[A-Z])(?=.*[%&@!])[A-Za-z0-9%&@!]{8,16}$/.test(newPassword));
}

/* --------------- ALLERT PER MODIFICHE NON SALVATE --------------- */
window.addEventListener('beforeunload', (event) => {
    if (infoChanges()) {
      event.preventDefault();
      if (confirm('Sei sicuro di voler abbandonare la pagina? Le modifiche non salvate andranno perse.')) {
        return;
      }
    }
});