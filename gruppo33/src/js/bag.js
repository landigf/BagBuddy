const editable = localStorage.getItem('editable');
let cancelOperation = true;
const itemMap = new Map();
// legge gli elementi salvati in localStorage.ge('items') e li aggiunge alla mappa
function loadItems() {
  const itemsJson = localStorage.getItem('items');
  if (itemsJson) {
    const items = JSON.parse(itemsJson);
    for (const [item, quantity] of Object.entries(items))
      for (let i = 0; i < quantity; i++)
        addItem(item);
  } else {
    alert('Errore nel caricamento degli elementi!');
    // Riporta l'utente a "index.php" da gruppo33/pages/bag.php a gruppo33/index.php
    window.location.href = '../index.php';
  }
}
loadItems(); // A questo punto, itemMap contiene gli elementi della valigia
let itemMapCopy = new Map(itemMap); // Copia di backup per il caso di annullamento delle modifiche


function updateItemMap(itemName, quantity) {
  if (itemMap.has(itemName)) {
    itemMap.set(itemName, itemMap.get(itemName) + quantity);
  } else {
    itemMap.set(itemName, quantity);
  }
  if (itemMap.get(itemName) <= 0)
    itemMap.delete(itemName);

  if (editable === 'true') {
    const saveButton = document.getElementById('saveButton');
    if(itemMap.size > 0) {
      saveButton.disabled = false; 
    } else {
      saveButton.disabled = true; 
    }
  }
}



/* salvataggio valigia
chiedi il nome per la valida (di default mette quello della destinazione)
se l'utente è loggato -> salva sul db e vai nello storico 
altrimeti, porta a login/register, salva sul db - >vai nello storico
*/
function onSaveBag(){
  if (itemMap.size === 0) {
    alert('La valigia è vuota!');
    return;
  }
  let bagName = document.getElementById('bagName').value.trim();
  if (bagName === '') {
    bagName = document.getElementById('destinazioneRecap').textContent;
  }
  //alert('Salvataggio valigia: ' + bagName);

  const items = {};
  itemMap.forEach((quantity, item) => {
    items[item] = quantity;
  });
  //alert('Elementi: ' + JSON.stringify(items));
  let attivita = document.getElementById('attivitaRecap');
  if (attivita) {
    attivita = attivita.textContent.split(',').map(attivita => attivita.trim());
  } else {
    attivita = [];
  }
  const bagID = localStorage.getItem('bagID');
  if (bagID === null || bagID === '' || isNaN(bagID)) {
    alert('ID della valigia non valido! Prova ad aggiornare la pagina.');
    return;
  }
  
  const bag = {
    id: bagID,
    nome: bagName,
    preferenze: {
      destinazione: document.getElementById('destinazioneRecap').textContent,
      date: document.getElementById('dataRecap').textContent.split(' - ').map(date => date.trim()),
      dimensione: document.getElementById('dimensioneRecap').textContent,
      compagnia: document.getElementById('compagniaRecap').textContent,
      tipoDiViaggio: document.getElementById('tipoDiViaggioRecap').textContent,
      attivita
    },
    items
  };
  // Salviamo per sicuerra la bag modificata in localStorage in caso di errore
  localStorage.setItem('modifiedBag', JSON.stringify(bag));
  updateBag(bag);
  //alert('Valigia aggiornata: ' + JSON.stringify(bag));
}


function updateBag(bag) {
  // Trova il percorso base dinamicamente
  const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/bag.php"
  const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/updateBag.php';
  // Crea una nuova richiesta XMLHttpRequest
  const xhttp = new XMLHttpRequest();
  xhttp.open('POST', apiUrl, true);
  xhttp.setRequestHeader('Content-Type', 'application/json');

  // Gestione della risposta
  xhttp.onload = function () {
      if (xhttp.status === 200) {
          const response = JSON.parse(xhttp.responseText);
          if (response.success) {
              //alert('Valigia aggiornata con successo!');
              // Redirect alla pagina dello storico valigie
              // window.location.href = 'profile.php?show=bags';
              itemMapCopy.clear();
              itemMapCopy = new Map(itemMap);
              onViewBag();
          } else {
              alert('Errore durante la modifica: ' + response.message);
          }
      } else if (xhttp.status === 403) {
          alert('Errore: Accesso negato!');
          // Redirect alla pagina di login
          window.location.href = 'login.php';
      } else if (xhttp.status === 404) {
          alert('Errore: URL non trovato: ' + apiUrl);
      } else {
          alert('Errore di connessione al server. Codice: ' + xhttp.status);
      }
  };

  // Gestione degli errori
  xhttp.onerror = function () {
      alert('Errore di connessione al server.');
  };

  // Invio dei dati
  xhttp.send(JSON.stringify(bag));
}


/* -------------------- INSERIMENTO ELEMENTI ALL'INTERNO DELLA LISTA -------------------- */

// inserimento elementi della lista tramite input
// tramite click

// AGGIUNGERE NELLA PARTE DI HTML
// addButton.addEventListener('click', addTextItem); 

function onTextInput(event) {
  const textInput = event.target.value;
  const addButton = document.getElementById('addButton');
  if (textInput.trim() !== '')
    addButton.disabled = false;
  else
    addButton.disabled = true;
}
/*
newItemInput.addEventListener('input', () => {
  if (newItemInput.value.trim() !== '')
    addButton.disabled = false;
  else
    addButton.disabled = true;
});
*/

// premendo invio
function onEnterPress(event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    addTextItem();
  }
}
/*
newItemInput.addEventListener('keydown', (event) => {
  if (event.key === 'Enter')
    addTextItem();
});
*/

// inserimento elementi della lista tramite click sugli elementi dello slider
function onSliderItemClick(item) {
  addItem(item.children[1].textContent);
}
/*
items.forEach((item) => {
  item.addEventListener('click', () => {
    addItem(item.children[1].textContent); 
  });
});
*/


function onDragStart(event) {
  event.dataTransfer.setData('text/plain', event.target.children[1].textContent);
}
/*
items.forEach((item) => {
  item.addEventListener('dragstart', (event) => { // intercetta quando un elemento viene trascinato dallo slider
    event.dataTransfer.setData('text/plain', item.children[1].textContent); // il testo dell'elemento è il secondo figlio (il p) del contenitore item
  });
});
*/

function onDrop(event) {
  event.preventDefault(); 
  const text = event.dataTransfer.getData('text/plain'); 
  addItem(text); 
}
/*
// inserimento elementi della lista tramite drag & drop 
const dropZone = document.getElementById('dropZone');  // unica zona in cui è possibile rilasciare gli elementi: la lista

dropZone.addEventListener('drop', (event) => { // intercetta quando un elemento viene rilasciato nella lista
  event.preventDefault(); 
  const text = event.dataTransfer.getData('text/plain'); // recupera il testo dell'elemento trascinato
  addItem(text); // aggiunge l'elemento alla lista
});
*/

function onDragOver(event) {
  event.preventDefault(); // permette il drop
}
/*
dropZone.addEventListener('dragover', (event) => { // intercetta quando un elemento viene trascinato sopra la lista
  event.preventDefault();  
}); 
*/

/* FUNZIONI PER L'INSERIMENTO DI ELEMENTI ALL'INTERNO DELLA LISTA */
function addItem(text) {
  const list = document.getElementById('list');
  if (itemMap.has(text)) {
    updateItemMap(text, 1);
    const quantityInput = document.getElementById(`${text}Quantity`);
    quantityInput.value = itemMap.get(text);
    if(editable && !cancelOperation) {
      const listItem = document.getElementById(`${text}ListItem`);
      const p = listItem.querySelector('.itemText');
      list.scrollTo({
        top: listItem.offsetTop - list.offsetTop,
        behavior: 'smooth'
      });
    }
    return;
  }
  updateItemMap(text, 1); // elemento non presente nella lista
  const listItem = document.createElement('li');
  const p = document.createElement('p');
  listItem.setAttribute('class', 'list'); 
  p.textContent = text;
  listItem.appendChild(p);
  listItem.setAttribute('id', `${text}ListItem`);

  const quantityInput = document.createElement('input');
  quantityInput.setAttribute('id', `${text}Quantity`);
  quantityInput.setAttribute('type', 'number');
  quantityInput.setAttribute('value', itemMap.get(text)); // value = 1
  quantityInput.setAttribute('min', '0');
  quantityInput.setAttribute('class', 'quantityInput');

  if (editable === 'false') { // utente non loggato
    listItem.appendChild(quantityInput);
    list.appendChild(listItem);
  }
  
  p.setAttribute('class', 'itemText');
  const button = document.createElement('button'); 
  button.textContent ="Rimuovi"; 
  button.setAttribute("class", "removeButton"); 
  button.setAttribute("class", "onEdit");

  const div = document.createElement('div');
  div.appendChild(quantityInput);
  div.appendChild(button);
  listItem.appendChild(div);
  list.appendChild(listItem);

  // Listener per il pulsante di rimozione di un elemento
  button.addEventListener('click', () => {
    updateItemMap(text, -1);
    if (itemMap.has(text)) {
      quantityInput.value = itemMap.get(text);
    } else {
      list.removeChild(listItem);
    }
  });

  // Listener per la quantità di un elemento
  quantityInput.addEventListener('change', () => {
    if (quantityInput.value === '' || isNaN(quantityInput.value)) {
      quantityInput.value = itemMap.get(text);
      return;
    }
    if (quantityInput.value < 0) {
      quantityInput.value = 0;
    }
    const quantity = parseInt(quantityInput.value);
    updateItemMap(text, quantity - itemMap.get(text));
    if (itemMap.has(text)) {
      quantityInput.value = itemMap.get(text);
    } else {
      list.removeChild(listItem);
    }
  });

  
  if (!cancelOperation) {
    list.scrollTo({
      top: list.scrollHeight,
      behavior: 'smooth'
    }); // quando viene aggiunto un elemento, la lista scorre verso il basso in modo fluido
  }
}

function addTextItem() {
  const newItemInput = document.getElementById('newItem');
  const newItemText = newItemInput.value.trim();
  if (newItemText != '') {
    addItem(newItemText); 
    newItemInput.value = '';
    const addButton = document.getElementById('addButton');
    addButton.disabled = true;
  }
}

function deleteBag(bagID) {
  // Trova il percorso base dinamicamente
  const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/pages/bag.php"
  const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/deleteBag.php';
  // Crea una nuova richiesta XMLHttpRequest
  const xhttp = new XMLHttpRequest();
  xhttp.open('POST', apiUrl, true);
  xhttp.setRequestHeader('Content-Type', 'application/json');

  // Gestione della risposta
  xhttp.onload = function () {
    if (xhttp.status === 200) {
      const response = JSON.parse(xhttp.responseText);
      if (response.success) {
        //alert('Valigia eliminata con successo!');
        // Redirect alla pagina dello storico valigie
        window.location.href = 'profile.php?show=bags';
      } else {
        alert('Errore durante l\'eliminazione: ' + response.message);
      }
    } else if (xhttp.status === 403) {
      alert('Errore: Accesso negato!');
      // Redirect alla pagina di login
      window.location.href = 'login.php';
    } else if (xhttp.status === 404) {
      alert('Errore: URL non trovato: ' + apiUrl);
    } else {
      alert('Errore di connessione al server. Codice: ' + xhttp.status);
    }
  };

  // Gestione degli errori
  xhttp.onerror = function () {
    alert('Errore di connessione al server.');
  };

  // Invio dei dati
  xhttp.send(JSON.stringify({ id: bagID }));
}
/* -------------------- INSERIMENTO ELEMENTI ALL'INTERNO DELLA LISTA -------------------- */


/* -------------------- VALIGIA VISUALIZZABILE -- VS -- VALIGIA EDITABILE -------------------- */

function onViewBag() {
  const viewMode = document.querySelectorAll('.onView');
  const editMode = document.querySelectorAll('.onEdit');
  const allQuantityInputs = document.querySelectorAll('.quantityInput');
  const bagName = document.getElementById('bagName');
  bagName.disabled = true;

  allQuantityInputs.forEach((quantityInput) => {
    quantityInput.disabled = true;
  });
  viewMode.forEach((element) => {
    element.classList.remove('hidden');
  });
  editMode.forEach((element) => {
    element.classList.add('hidden');
  });

  const recap = document.querySelector('nav');
  const list = document.querySelector('article'); 
  list.classList.remove('editable'); 
  recap.classList.remove('editable'); 
}

function onEditBag() {
  const viewMode = document.querySelectorAll('.onView');
  const editMode = document.querySelectorAll('.onEdit');
  const allQuantityInputs = document.querySelectorAll('.quantityInput');
  const bagName = document.getElementById('bagName');

  bagName.disabled = false;

  allQuantityInputs.forEach((quantityInput) => {
    quantityInput.disabled = false;
  });
  viewMode.forEach((element) => {
    element.classList.add('hidden');
  });
  editMode.forEach((element) => {
    element.classList.remove('hidden');
  });

  const recap = document.querySelector('nav');
  const list = document.querySelector('article'); 
  list.classList.add('editable'); 
  recap.classList.add('editable'); 
}
onViewBag(); // visualizzazione di default
cancelOperation = false;


function onDeleteBag() {
  if (!confirm("Sei sicuro di voler eliminare questa valigia?")) {
    return;
  }
  const bagID = localStorage.getItem('bagID');
  if (bagID === null || bagID === '' || isNaN(bagID)) {
    alert('ID della valigia non valido! Prova ad aggiornare la pagina.');
    return;
  }
  deleteBag(bagID);
}

function onCancelBag() {
  cancelOperation = true;
  if (itemMap.size !== itemMapCopy.size || [...itemMap].some(([key, value]) => itemMapCopy.get(key) !== value)) {
    if (!confirm("Sei sicuro di voler annullare le modifiche?")) {
      return;
    }
  }
  const listElement = document.getElementById('list');
  listElement.innerHTML = '';
  document.getElementById('newItem').value = '';
  itemMap.clear();
  itemMapCopy.forEach((quantity, item) => {
    for (let i = 0; i < quantity; i++){
      addItem(item);
    }
  });
  onViewBag();
  cancelOperation = false;
}

/* --------------- ALLERT PER MODIFICHE NON SALVATE --------------- */
window.addEventListener('beforeunload', (event) => {
  if (itemMap.size !== itemMapCopy.size || [...itemMap].some(([key, value]) => itemMapCopy.get(key) !== value)) {
    event.preventDefault();
    if (confirm('Sei sicuro di voler abbandonare la pagina? Le modifiche non salvate andranno perse.')) {
      return;
    }
  }
});

/* --------------- Funzionalità Aggiuntive --------------- */
// Bottone per la condivisione della valigia tramite link
const shareButton = document.getElementById('shareButton');
shareButton.addEventListener('click', () => {
  // Ottieni l'indirizzo IP del computer in esecuzione XAMPP
  const ipAddress = '192.168.1.177'; // Sostituisci con il tuo IP locale
  const pathToProject = '/~landigf/TSW'; // Sostituisci con il percorso del progetto
  const shareLink = `http://${ipAddress}/${pathToProject}/gruppo33/pages/bag.php?bagID=${localStorage.getItem('bagID')}`;
  
  navigator.clipboard.writeText(shareLink).then(() => {
    alert('Link copiato negli appunti!' + '\n' + 'NB. Il link è: ' + shareLink + '\n' + 'Per provarlo nella rete locale modifica l\'indiriizzo IP e il percorso del progetto.');
  }).catch(err => {
    alert('Errore durante la copia del link: ' + err);
  });
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
