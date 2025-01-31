<?php
    session_start();
    $editable = true;
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $bagID = $_POST['bagID'];
    } else {
        $editable = false;
        if (!(isset($_GET['bagID']))) {
            // scrivi l'errore nell'error log
            error_log("Errore: bagID non valido o mancante");
            header('Location: ../index.php');
            exit();
        } else {
            $bagID = $_GET['bagID'];
        }
    }
    
        
    require_once '../server/logindb.php';

    // Connessione al database
    $conn = pg_connect($connection_string);

    // Controllo connessione
    if (!$conn) {
        header('Location: ../index.php?error=db_connection_error');
        exit();
    }

    // Recupero la valigia con l'id bagID evitando SQL Injection
    $query = "SELECT * FROM valigie WHERE id = $1";
    $result = pg_prepare($conn, "get_bag", $query);
    $result = pg_execute($conn, "get_bag", array($bagID));

    if (!$result) {
        error_log("Errore: query fallita per bagID = $bagID");
        //echo '<script>alert("Errore: link non valido");</script>';
        header('Location: ../index.php');
        exit();
    }

    // Verifica che ci sia solo una valigia
    $valigia = pg_fetch_assoc($result);
    if (!$valigia || pg_num_rows($result) != 1) {
        error_log("Errore: valigia non trovata per bagID = $bagID");
        header('Location: ../index.php');
        exit();
    }
    

    
    // Verifico se l'utente è loggato e se possiede una valigia con l'id bagID
    $valigia_userID = $valigia['id_utente'];
    // troviamo l'utente associato alla valigia
    $query = "SELECT * FROM utenti WHERE id = $1";
    $result = pg_prepare($conn, "get_user", $query);
    $result = pg_execute($conn, "get_user", array($valigia_userID));
    if (!$result) {
        error_log("Errore: query fallita per userID = $valigia_userID");
        header('Location: ../index.php');
        exit();
    }
    $user = pg_fetch_assoc($result);
    if (!$user || pg_num_rows($result) != 1) {
        error_log("Errore: nessun utente trovato per userID = $valigia_userID");
        header('Location: ../index.php');
        exit();
    }
    $username = $user['username'];
    $userID = $user['id'];
    // Verifica se l'utente loggato è il proprietario della valigia
    if ($editable && isset($_SESSION['user']) && $_SESSION['user']['id'] == $valigia_userID && $_SESSION['user']['id'] == $user['id']) {
        $editable = true;
    } else {
        $editable = false;
    }

    pg_close($conn);

    // Query per ottenere i dettagli della valigia
    $preferenze = json_decode($valigia['preferenze'], true);
    $items = json_decode($valigia['lista_oggetti'], true);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BagCreation</title>
    <link rel="stylesheet" href="../src/css/bag.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon/favicon.ico">
</head>
<body>
    <!--HEADER-->
    <header>
        <div class="headerContainer">
            <div class="logoHeader"> 
                <a href="../index.php">
                    <img src="../images/index/logoHeader.png" alt="logoHeader">
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
    <section>
        <?php
            // Salvo gli elementi presenti nella valiga in localStorage (per leggerli in JS)
            echo '<script>localStorage.setItem("items", \'' . json_encode($items) . '\');</script>';
            if ($editable && $userID) {
                echo '<script>localStorage.setItem("editable", true);</script>';
                echo '<script>localStorage.setItem("bagID", ' . json_encode($bagID) . ');</script>';
                echo '<script>localStorage.setItem("userID", ' . json_encode($userID) . ');</script>';
            } else {
                echo '<script>localStorage.setItem("editable", false);</script>';
                echo '<h1 id="usernameTxt" class="fadeIn"><span id="username">'. htmlspecialchars($username) .'</span> ha condiviso la  sua valigia con te!</h1>';
            }
        ?>
        <div class="listContainer">
            <nav>
                <div class="recap" id="recap">
                    <h4>Hai selezionato: </h4>
                    <ul>
                    <?php
                    echo "<li id='destinazioneRecap'>". htmlspecialchars($preferenze['destinazione']) . "</li>";
                    echo "<li id='dataRecap'>" . implode(' - ', $preferenze['date']) . "</li>";
                    echo "<li>Dimensione valigia: <span id='dimensioneRecap'>" . htmlspecialchars($preferenze['dimensione']) . "</span></li>";
                    echo "<li>Compagnia: <span id='compagniaRecap'>" . htmlspecialchars($preferenze['compagnia']) . "</span></li>";
                    echo "<li>Tipo di Viaggio: <span id='tipoDiViaggioRecap'>" . htmlspecialchars($preferenze['tipoDiViaggio']) . "</span></li>";
                    $attivita = $preferenze['attivita'];
                    if (!empty($attivita)) { 
                        echo "<li>Attività: <span id='attivitaRecap'>";
                        echo implode(", ", $attivita);
                    }
                    echo "</span></li>";
                    echo "<li> <button id='shareButton'> Condividi Valigia </button></li>"
                    ?>
                    </ul>
                </div>
            </nav>
        
            <article>
                <div class="bagList">
                    <?php
                        echo '<h4 for="bagName">Nome della valigia:</h4>';
                        if($editable){
                            echo '<input type="text" id="bagName" value="' . htmlspecialchars($valigia['nome']). '"/>';
                        } else { 
                            echo '<span id="bagName">'. htmlspecialchars($valigia['nome']) . '</span>';
                        }
                    ?>
                    <ul id="list">
                    </ul>
                    <?php
                    if($editable){
                    echo '<div class="add onEdit">';
                    echo '    <h4 for="newItem">Aggiungi un elemento alla valigia: </h4>';
                    echo '    <div class="addFlex">'; 
                    echo '          <input type="text" id="newItem" oninput="onTextInput(event)" onkeydown="onEnterPress(event)"/>';
                    echo '          <button id="addButton" disabled="true" onclick="addTextItem()">Aggiungi</button>';
                    echo '    </div>';
                    echo '</div>';
                    /* in fase di visualizzazione per l'utente: */
                    echo '<div class="bottomButtons onView">';
                    echo '    <button id="editButton" onclick="onEditBag()">Modifica</button>'; 
                    echo '    <button id="deleteButton" onclick="onDeleteBag()">Elimina Valigia</button>';
                    echo '</div>';
                    /* in fase di modifica per l'utente: */
                    echo '<div class="bottomButtons onEdit">';
                    echo '    <button id="cancelButton" onclick="onCancelBag()">Annulla</button>'; 
                    echo '    <button id="saveButton" disabled="true" onclick="onSaveBag()">Salva Valigia</button>';
                    echo '</div>';
                    }
                    ?>
                </div>
            </article>
        </div>
    </section>
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
            <address>Hai riscontrato un errore oppure hai bisogno di aiuto? <a href="mailto:support@bagbuddy.com" >Contattaci</a></address>
            </div>
            <div>
                <p>© 2025 BagBuddy.</p>
            </div>
        </div>
    </footer>
    <!--FOOTER-->
    <script src="../src/js/bag.js"></script>
</body>
</html>
