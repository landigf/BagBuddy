<?php
    // Inizio sessione 
    session_start();
    require_once '../server/logindb.php';

    // DEBUG: Simulazione di un utente loggato
    // $_SESSION['user'] = array(
    //     'userID' => 1,
    //     'username' => 'elettrapalmisano',
    //     'email' => 'ellipa@gmail.com',
    //     'info' => array(
    //         'nome' => 'Elettra',
    //         'cognome' => 'Palmisano'
    //     ),
    //     'preferences' => array(
    //         'sortCriteria' => 'created_at', // created_at, nome
    //         'sortOrder' => 'DESC' // ASC o DESC
    //         // Default: 'sorting' => 'created_at', 'order' => 'DESC'
    //     )
    // );

    // Controllo se l'utente è loggato
    if(!isset($_SESSION['user'])){
        header("Location: login.php");
    }
    $userID = $_SESSION['user']['id']; // userID dell'utente loggato per recuperare le valigie

    // Connessione al database e query per ottenere le valigie dell'utente
    $conn = pg_connect($connection_string) or die('Connessione al database fallita');

    // Recupero dell'utente nel database
    $query = "SELECT * FROM utenti WHERE id = $userID";
    $result = pg_query($conn, $query) or die('Query non riuscita');
    $user = pg_fetch_assoc($result);
    if(!$user){
        header("Location: login.php");
    }
 


    // Chiusura della connessione al database
    pg_close($conn);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../src/css/profile.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon/favicon.ico">
</head>
<body>
    <!--HEADER-->
    <header>
        <div class="headerContainer">
            <div class="logoHeader"> 
                <a href="../index.php">
                    <img src="../images/profile/logoHeader.png" alt="logoHeader">
                </a>
            </div>
            <div class="headerButtons">
                <?php $logged = isset($_SESSION['user']);
                if (!$logged) : ?>
                    <form id="loginButton" action="login.php">
                        <input type="submit" value="Accedi"/>
                    </form>
                    <form id="registerButton" action="login.php">
                        <input hidden name="register" value="true"/>
                        <input type="submit" value="Registrati"/>
                    </form>
                <?php else : ?>
                    <form id="newBagLink" action="../index.php#step-1">
                        <input type="submit" value="Nuova Valigia"/>
                    </form>
                    <form id="profileButton" action="profile.php">
                        <input type="submit" value="Profilo"/>
                    </form>
                    <form id="logoutHeaderButton" action="../server/api/logout.php" method="POST">
                        <input type="submit" value="Logout"/>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!--HEADER-->

    <main>
        <div class="profileContainer">
            <!-- MENU -->
            <div class="menu">
                <nav class="tabs">
                    <input type="radio" id="infoButton" name="tab" checked="true" />
                    <label for="infoButton">Info</label>
                    <input type="radio" id="bagsButton" name="tab" />
                    <label for="bagsButton">Valigie</label>
                    <div class="marker">
                        <div id="top"></div>
                        <div id="bottom"></div>
                    </div>
                </nav>
            

            <!-- VISUALIZATION -->
            <div class="visualization">
                
                <!-- BAGS -->
                <div id="bags" class="hidden">
                    <h1>I miei Viaggi</h1>
                    <!-- ORDINAMENTO -->
                    <div id="sorting" onchange="sortBags()">
                        <p>Ordina per:</p>
                        <?php
                            $preferenze = json_decode($user['preferences'], true);
                            try {
                                if (is_string($_SESSION['user']['preferences'])) {
                                    $_SESSION['user']['preferences'] = json_decode($_SESSION['user']['preferences'], true);
                                }
                                $sortCriteria = $_SESSION['user']['preferences']['sortCriteria'];
                                $sortOrder = $_SESSION['user']['preferences']['sortOrder'];
                            } catch (Exception $e) {
                                $sortCriteria = $preferenze['sortCriteria'];
                                $sortOrder = $preferenze['sortOrder'];
                            }
                        ?>
                        <select id="sortingSelect">
                            <option value="created_at" <?php if($sortCriteria == 'created_at') echo 'selected'; ?>>Data di creazione</option>
                            <option value="nome" <?php if($sortCriteria == 'nome') echo 'selected'; ?>>Nome</option>
                        </select>
                        <select id="orderSelect">
                            <option value="ASC" <?php if($sortOrder == 'ASC') echo 'selected'; ?>>Crescente</option>
                            <option value="DESC" <?php if($sortOrder == 'DESC') echo 'selected'; ?>>Decrescente</option>
                        </select>
                    </div>
                    <!-- FINE ORDINAMENTO -->
                    <div class="bags" id="bagsContainer">
                        <!-- AGGIUNGO LE VALIGIE TRAMITE AJAX, funzione sortBags()-->
                    </div>
                </div>  
                <!-- BAGS -->

                <!-- INFO -->
                <div id="info">
                    <h1>Informazioni Personali</h1>
                    <?php
                        $info = json_decode($user['info'], true);
                        $nome = $info['nome'] ?? '';
                        $cognome = $info['cognome'] ?? '';
                        $username = $user['username'];
                        $email = $user['email'];
                    ?>
                    <p>Nome: <input type="text" class="profileInfo" id="name" value="<?php echo htmlspecialchars($nome) ?>" placeholder="Inserisci il tuo nome"/></p>
                    <p>Cognome: <input type="text" class="profileInfo" id="surname" value="<?php echo htmlspecialchars($cognome) ?>" placeholder="Inserisci il tuo cognome"/></p>
                    <p>Username: <input type="text" class="profileInfo" id="username" value="<?php echo htmlspecialchars($username) ?>" placeholder="Inserisci il tuo username"/></p>
                    <p>Email: <input type="email" disabled readonly id="email" value="<?php echo htmlspecialchars($email) ?>" placeholder="La tua email"/></p>
                    <p class="onEdit"> <button id="passwordButton">Cambia password</button></p>
                    <form id="changePassword" class="hidden">
                        <fieldset>
                            <div>
                                <p for="currentPassword">
                                    Inserire la password attuale: 
                                    <input type="password" id="currentPassword" autofocus/>
                                    <span id="passwordErrorMessage" class="hidden error">Password errata</span>
                                </p>
                            </div>
                            <div>
                                <p for="newPassword">
                                    Inserire la nuova password:
                                    <input type="password" id="newPassword" disabled="true" title="La nuova password deve essere di 8-16 caratteri con almeno una maiuscola e un simbolo fra %&@!"/>
                                    <span id="passwordNonValidaErrorMessage" class="hidden error">La password non rispetta i criteri stabiliti!</span>
                                </p>
                            </div>
                            <div>
                                <p for="repeatPassword">
                                    Ripetere la nuova password:
                                    <input type="password" id="repeatPassword" disabled="true"/>
                                    <span id="passwordDiverseErrorMessage" class="hidden error">Le password non coincidono</span>
                                </p>
                            </div>
                        </fieldset>
                    </form> 
                    <div class="bottomButtons">
                        <div>
                            <button class="onEdit negButtons" id="cancelButton">Annulla</button>
                            <button class="onEdit posButtons" id="saveButton">Salva cambiamenti</button>
                            <button class="onView" id="editButton">Modifica profilo</button>
                        </div>
                        <div class="rightButtons">
                            <button class="negButtons" id="deleteButton">Elimina Account</button>
                            <button class="posButtons" id="logOutButton">Log out</button>
                        </div>
                    </div>
                </div> 
                <!-- INFO -->
            </div>
            </div>
        </div>
    </main>
    
    

    <!--FOOTER-->
    <footer> 
        <div class="footerContainer">
            <div class="logoFooter">
            <img src="../images/index/logoFooter.png" alt="logoFooter">
            </div>
            <div>
            <p>Il tuo assistente per organizzare la valigia perfetta. Dai viaggi di lavoro alle vacanze, ti aiutiamo a non dimenticare nulla!</p>
            </div>
            <div>
                <a href="login.php">Accedi o registrati</a>
            </div>
            <div>
            <address>Hai riscontrato un errore oppure hai bisogno di aiuto? <a href="mailto:bagbuddy33@gmail.com">Contattaci</a></address>
            </div>
            <div>
              <p>&copy; 2025 BagBuddy.</p>
            </div>
        </div>
    </footer>
    <!--FOOTER-->
    <script src="../src/js/profile.js"></script>
</body>
</html>