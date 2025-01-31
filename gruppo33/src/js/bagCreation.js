let itemMap = new Map();

try{
  const generateButton = document.getElementById('submitButton');
  generateButton.addEventListener('click', () => {
    itemMap.clear();
  });
} catch (error) {
  console.error('Errore:', error);
} finally {
  itemMap.clear();
  console.log('Mappa degli elementi svuotata');
}

function acceptItem(button) {
  addItem(button.parentElement.parentElement.children[0].textContent);
  const list = document.getElementById('list');
  list.removeChild(button.parentElement.parentElement); 
}
/*
acceptButtons.forEach((button) => {
  button.addEventListener('click', () => {
    addItem(button.parentElement.parentElement.children[0].textContent);
    list.removeChild(button.parentElement.parentElement); 
  });
});
*/

function refuseItem(button) {
  const list = document.getElementById('list');
  list.removeChild(button.parentElement.parentElement);
}
/*
refuseButtons.forEach((button) => {
  button.addEventListener('click', () => {
    list.removeChild(button.parentElement.parentElement);
  });
});
*/

//const newItemInput = document.getElementById('newItem');
//const addButton = document.getElementById('addButton');
//const saveButton = document.getElementById('saveButton');
//const list = document.getElementById('list'); 
//const removeButtons = document.querySelectorAll('.removeButton'); 
//const items = document.querySelectorAll('.item');



function updateItemMap(itemName, quantity) {
  if (itemMap.has(itemName)) {
    itemMap.set(itemName, itemMap.get(itemName) + quantity);
  } else {
    itemMap.set(itemName, quantity);
  }
  if (itemMap.get(itemName) <= 0)
    itemMap.delete(itemName);

  const saveButton = document.getElementById('saveButton');
  if(itemMap.size > 0) {
    saveButton.disabled = false; 
  } else {
    saveButton.disabled = true; 
  }

}



/* 
// NON LA USIAMO PIÙ
function removeItem(button) {
  const list = document.getElementById('list');
  list.removeChild(button.parentElement);
}
*/
/*
removeButtons.forEach(function(button) {
    button.addEventListener('click', (event) => {
      list.removeChild(button.parentElement);
    });
});
*/

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
  localStorage.setItem('savingBag', 'true');
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

  const bag = {
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
  saveBag(bag);
  //alert('Valigia salvata: ' + JSON.stringify(bag));
}


function saveBag(bag) {
  // Controlla se l'utente è loggato
  // variabile salvata dopo il login
  // const user = localStorage.getItem('user');
  // console.log('Utente loggato: ' + user);
  // Trova il percorso base dinamicamente
  const currentUrl = window.location.pathname; // Es. "/~landigf/TSW/gruppo33/"
  const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/') + 1) + 'server/api/saveBag.php';
  //alert('API URL: ' + apiUrl);
  //alert('Utente loggato: ' + user.username);
  //const userId = user.id;
  // Crea una nuova richiesta XMLHttpRequest
  const xhttp = new XMLHttpRequest();
  xhttp.open('POST', apiUrl, true);
  xhttp.setRequestHeader('Content-Type', 'application/json');

  // Gestione della risposta
  xhttp.onload = function () {
      if (xhttp.status === 200) {
          const response = JSON.parse(xhttp.responseText);
          if (response.success) {
              //alert('Valigia salvata con successo!');
              // Redirect alla pagina dello storico valigie
              window.location.href = 'pages/profile.php?show=bags';
              localStorage.setItem('savingBag', 'false');
          } else {
              alert('Errore durante il salvataggio: ' + response.message);
          }
      } else if (xhttp.status === 403) {
          // L'utente non è loggato: reindirizza al login
          //alert('Effettua il login per salvare la tua valigia.');
          localStorage.setItem('unsavedBag', JSON.stringify(bag)); // Salva temporaneamente la valigia
          window.location.href = 'pages/login.php';
          localStorage.setItem('savingBag', 'false');
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

  // Invio di bag come JSON
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
    const listItem = document.getElementById(`${text}ListItem`);
    const p = listItem.querySelector('.itemText');
    p.style.fontWeight = 'bold';
    setTimeout(() => {
      p.style.fontWeight = 'normal';
    }, 500);
    list.scrollTo({
      top: listItem.offsetTop - list.offsetTop,
      behavior: 'smooth'
    });
    return;
  }

  const listItem = document.createElement('li');
  const p = document.createElement('p');
  const button = document.createElement('button');

  listItem.setAttribute('class', 'list'); 
  listItem.setAttribute('id', `${text}ListItem`);
  p.textContent = text;
  p.setAttribute('class', 'itemText');
  button.textContent = "Rimuovi"; 
  button.setAttribute("class", "removeButton"); 

  const div = document.createElement('div');
  const quantityInput = document.createElement('input');
  quantityInput.setAttribute('id', `${text}Quantity`);
  quantityInput.setAttribute('type', 'number');
  quantityInput.setAttribute('value', '1');
  quantityInput.setAttribute('min', '0');

  div.appendChild(quantityInput);
  div.appendChild(button);

  listItem.appendChild(p);
  listItem.appendChild(div);
  list.appendChild(listItem);

  updateItemMap(text, 1);
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

  p.style.fontWeight = 'bold';
  setTimeout(() => {
    p.style.fontWeight = 'normal';
  }, 500);

  list.scrollTo({
    top: list.scrollHeight,
    behavior: 'smooth'
  }); // quando viene aggiunto un elemento, la lista scorre verso il basso in modo fluido
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
/* -------------------- INSERIMENTO ELEMENTI ALL'INTERNO DELLA LISTA -------------------- */