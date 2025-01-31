# BagBuddy - README

## Descrizione del Progetto
BagBuddy è un sito web progettato per assistere gli utenti nella preparazione e gestione delle proprie valigie in vista dei viaggi. Il sistema permette di comporre virtualmente la valigia con suggerimenti automatici basati sulle informazioni fornite dall’utente, garantendo una gestione efficiente e organizzata dei bagagli.

## Come Provare il Progetto
### 1. Installazione di PostgreSQL e Configurazione del Database
Per eseguire BagBuddy, è necessario disporre di un database **PostgreSQL**. 

#### Creazione manuale del database:
Creare un database con le seguenti credenziali:
```php
$host = 'localhost';
$port = '5432';
$db = 'gruppo33';
$username = 'www';
$password = 'tw2024';
$connection_string = "host=$host dbname=$db user=$username password=$password";
```

#### Restore del database:
Se si è installato PostgreSQL con **XAMPP**, seguire questi passaggi:
- Aprire **pgAdmin** e creare un database chiamato `gruppo33` con owner `www`.
- Da terminale eseguire:
  - **Mac:**
    ```sh
    sudo /Applications/XAMPP/xamppfiles/pgsql/16/bin/psql -d gruppo33 -U www -f PATH_SAVE/gruppo33.sql
    ```
  - **Windows:**
    ```sh
    "C:\Program Files\PostgreSQL\16\bin\psql.exe" -d gruppo33 -U www -f PATH_SAVE/gruppo33.sql
    ```

Se si è installato PostgreSQL tramite **Homebrew** (su Mac) o altri package manager, eseguire:
```sh
psql -d gruppo33 -U www -f PATH_SAVE/gruppo33.sql
```

### 2. Avvio del Progetto
1. Avviare un server locale (ad esempio **XAMPP** o **Apache con PHP**).
2. Posizionare il progetto nella cartella `htdocs` o nella root del server.
3. Aprire il browser e accedere a `http://localhost/gruppo33/index.php`.

## Account di Prova
Per testare il sito, è possibile utilizzare il seguente account di prova:
- **Email:** mario.rossi@gmail.com
- **Password:** !MarioRossi1

## Funzionalità Principali
- **Creazione della Valigia:** Gli utenti possono generare una valigia compilando un form con informazioni sulla destinazione, date e attività.
- **Suggerimenti Intelligenti:** Il sistema consiglia gli oggetti in base a un **algoritmo di scoring** e alle condizioni meteo fornite dall’API di **OpenWeatherMap**.
- **Gestione delle Valigie:** Gli utenti registrati possono salvare, modificare e consultare le loro valigie in un archivio personale.
- **Condivisione delle Valigie:** È possibile condividere una valigia generando un link di sola lettura.
- **Autenticazione Utente:** Login, registrazione, recupero password con domanda di sicurezza.

## Assunzioni e Limitazioni
### Assunzioni
- La creazione della valigia si basa su **destinazione, date di viaggio, tipo di viaggio, compagnia, dimensione e attività previste**.
- Le valigie possono essere salvate solo dagli **utenti autenticati**.
- Le password vengono cifrate per garantire la sicurezza.
- Il sistema funziona **solo online**, in quanto richiede una connessione a Internet per recuperare dati meteo e suggerimenti.

### Limitazioni
- Non è disponibile un’app mobile dedicata.
- Il sito è disponibile solo in **italiano**.
- Le valigie eliminate **non possono essere recuperate**.
- Gli oggetti suggeriti sono basati su un database statico, senza utilizzo di **intelligenza artificiale**.
- Le previsioni meteo funzionano solo per date entro **6 giorni** dalla data attuale.

## Contatti
Per supporto o segnalazioni, scrivere a: **support@bagbuddy.com**