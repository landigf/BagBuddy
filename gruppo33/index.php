<!--Gestire errori di compilazione del form
error=invalid_request
error=db_connection_error
error=select_query_error
error=calcolaConsigliati_error
-->
<?php
$error = isset($_GET['error']) ? $_GET['error'] : '';

function displayErrorMessage($error) {
    switch ($error) {
        case 'invalid_request':
            return 'Richiesta non valida.';
        case 'db_connection_error':
            return 'Errore di connessione al database.';
        case 'select_query_error':
            return 'Errore nella query di selezione.';
        case 'calcolaConsigliati_error':
            return 'Errore nel calcolo dei consigliati.';
        default:
            return '';
    }
}

$errorMessage = displayErrorMessage($error);
session_start();

$logged = isset($_SESSION['user']);
// if (!$logged && isset($_COOKIE['user_session'])) {
//     // Assuming you have a function to validate the session cookie and retrieve user data
//     $userData = validateSessionCookie($_COOKIE['user_session']);
//     if ($userData) {
//         $_SESSION['user'] = $userData;
//         $logged = true;
//     }
// }
if ($logged) {
    $user = $_SESSION['user'];
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagBuddy</title>
    <link rel="stylesheet" href="src/css/style.css">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="icon" type="image/x-icon" href="images/favicon/favicon.ico">
    <script>
        /* Listener per impostare la data minima di partenz */
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('departurDate').setAttribute('min', today);
        });
    </script>
</head>
<body>
    <!--HEADER-->
    <header>
        <div class="headerContainer">
            <div class="logoHeader"> 
                <a href="index.php">
                    <img src="images/index/logoHeader.png" alt="logoHeader">
                </a>
            </div>
            <div class="headerButtons">
                <?php
                if (!$logged) : ?>
                    <form id="loginButton" action="pages/login.php">
                        <input type="submit" value="Accedi"/>
                    </form>
                    <form id="registerButton" action="pages/login.php">
                        <input hidden name="register" value="true"/>
                        <input type="submit" value="Registrati"/>
                    </form>
                <?php else : ?>
                    <form id="newBagLink" action="index.php#step-1">
                        <input type="submit" value="Nuova Valigia"/>
                    </form>
                    <form id="profileButton" action="pages/profile.php">
                        <input type="submit" value="Profilo"/>
                    </form>
                    <form id="logoutHeaderButton" action="server/api/logout.php" method="POST">
                        <input type="submit" value="Logout"/>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!--HEADER-->

    <main>

        <div class="introductionContainer">
            <div class="introduction">
                <?php if (!$logged) : ?>
                    <h1 class="fadeIn">Benvenuto su</h1>
                <?php else : ?>
                    <h1 class="fadeIn">Ciao <?php echo $user['username']; ?>, bentornato su</h1>
                <?php endif; ?>
                <img id="logo" class="fadeIn" class="hidden" src="images/index/logo.png" alt="logo">
                <p class="fadeIn" id="sottotitolo">il tuo assistente personale per aiutarti a comporre la valigia!</p>
                <p id="scorri" class="opacity">scorri verso il basso per iniziare</p>
            </div>
        </div>
        <div>
            <img src="images/index/wave.png" id="wave">
        </div>

        <!--Aggiunta di un messaggio di errore-->
        <div id="error-message" class=<?php echo $errorMessage === '' ? 'hidden' : 'visible'; ?>>
            <h3>Siamo spiacenti, si è verificato un errore: <?php echo $errorMessage; ?></h3>
            <p>Per favore, prova a ricompilare il form.</p>
        </div>
        <form id="valigia-form" method="POST" action="server/api/bagCreation.php">
            <!--Aggiungere sessionID hidden da passare a php-->
            <!-- Step 1 -->
            <div id="step-1" class="visible">
                <label for="destinazione" class="big">Dove vuoi andare?</label><br>
                <label for="destinazione">Seleziona la tua destinazione</label><br>
                <input type="text" id="destinazione" name="destinazione" placeholder="Inserisci destinazione" autocomplete="off" autocapitalize="on" required>
                <ul id="suggestions-list" class="suggestions"></ul><br><br>
                <div class="date">
                    <div>
                        <label for="departurDate" class="date">Andata:</label><br>
                        <input type="date" id="departurDate" name="departurDate" required><br><br>
                    </div>
                    <div>
                        <label for="returnDate" class="date">Ritorno:</label><br>
                        <input type="date" id="returnDate" name="returnDate" required><br><br>
                    </div>
                </div>
            </div>
            
            <!-- Step 2 -->

            <!-- Step 2 -->
            <div id="step-2" class="hidden">
                <label for="dimensione" class="big">Dimensione della valigia:</label><br>
                <select id="dimensione" name="dimensione" required>
                    <option value="" disabled selected>Seleziona</option>
                    <option value="piccola">Piccola</option>
                    <option value="media">Media</option>
                    <option value="grande">Grande</option>
                </select><br><br>
            </div>

            <!-- Step 3 -->
            <div id="step-3" class="hidden">
                <label for="compagnia" class="big">Viaggi: </label><br>
                <select id="compagnia" name="compagnia" required>
                    <option value="" disabled selected>Seleziona</option>
                    <option value="solo">da solo</option>
                    <option value="coppia">in coppia</option>
                    <option value="famiglia">in famiglia</option>
                    <option value="amici">con amici</option>
                </select><br><br>
            </div>

            <!-- Step 4 -->
            <div id="step-4" class="hidden">
                <label for="tipoDiViaggio" class="big">Viaggi per: </label><br>
                <select id="tipoDiViaggio" name="tipoDiViaggio" required>
                    <option value="" disabled selected>Seleziona</option>
                    <option value="lavoro">Lavoro</option>
                    <option value="vacanza">Vacanza</option>
                    <option value="studio">Studio</option>
                    <option value="altro">Altro</option>
                </select><br><br>
            </div>

            <!-- Step 5  -->
            <div id="step-5" class="hidden">
                <p class="big">Seleziona le attività</p>
                    <div id="step-5-interno">
                        <div class="spiaggia">
                            <span> 
                                <input type="checkbox" value="spiaggia" name="attivita[]" id="spiaggia">
                                <label for="spiaggia">
                                    <img src="images/index/icons/spiaggia.png">
                                    <p>Spiaggia</p>
                                </label>
                            </span>
                        </div>
                        
                        <div class="montagna">
                            <span> 
                                <input type="checkbox" value="montagna" name="attivita[]" id="montagna">
                                <label for="montagna">
                                    <img src="images/index/icons/montagna.png">
                                    <p>Montagna</p>
                                </label>
                            </span>
                        </div>

                        <div class="escursionismo">
                            <span> 
                                <input type="checkbox" value="escursionismo" name="attivita[]" id="escursionismo">
                                <label for="escursionismo">
                                    <img src="images/index/icons/escursionismo.png">
                                    <p>Escursionismo</p>
                                </label>
                            </span>
                        </div>

                        <div class="sport">
                            <span> 
                                <input type="checkbox" value="sport" name="attivita[]" id="sport">
                                <label for="sport">
                                    <img src="images/index/icons/sport.png">
                                    <p>Sport</p>
                                </label>
                            </span>
                        </div>

                        <div class="relax">
                            <span>
                                <input type="checkbox" value="relax" name="attivita[]" id="relax">
                                <label for="relax">
                                    <img src="images/index/icons/relax.png">
                                    <p>Relax</p>
                                </label>
                            </span>
                        </div>

                        <div class="campeggio">
                            <span> 
                                <input type="checkbox" value="campeggio" name="attivita[]" id="campeggio">
                                <label for="campeggio">
                                    <img src="images/index/icons/campeggio.png">
                                    <p>Campeggio</p>
                                </label>
                            </span>
                        </div>

                    </div>
                    <button type="submit" id="submitButton">Genera Valigia Consigliata</button>
            </div>
        </form>

        <!--GENERAZIONE DELLA VALIGIA
        1) Effettuare la richiesta con AJAX a bagCreation.php per ottenere i consigliati in base alle preferenze dell'utente
        2) Mostrare una schermata di caricamento
        3) Mostrare i risultati
        -->

        <div id="results" class="hidden">
                <!--
                Aggiunta del div contentente i risultati
                ottenuto tramite la richiesta AJAX
                -->
        </div>
    </main>

    <div class="waveContainer">
        <img src="images/index/bottomWave.png" id="bottomWave">
    </div>   

    <!--FOOTER-->
    <footer> 
        <div class="footerContainer">
          <div class="logoFooter">
            <img src="images/index/logoFooter.png" alt="logoFooter">
          </div>
          <div>
            <p>Il tuo assistente per organizzare la valigia perfetta. Dai viaggi di lavoro alle vacanze, ti aiutiamo a non dimenticare nulla!</p>
          </div>
          <div>
              <a href="pages/login.php">Accedi o registrati</a>
          </div>
          <div>
            <address>Hai riscontrato un errore oppure hai bisogno di aiuto? <a href="mailto:support@bagbuddy.com">Contattaci</a></address>
          </div>
          <div>
              <p>©2025 BagBuddy.</p>
          </div>
        </div>
    </footer>
    <!--FOOTER-->
    <script src="src/js/index.js"></script>
</body>
</html>
