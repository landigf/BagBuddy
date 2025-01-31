<?php
session_start();

require_once '../server/logindb.php';

$db = pg_connect($connection_string) or die('Impossibile connettersi al database: ' . pg_last_error());

$LoginError = isset($_GET['login_error']) ? $_GET['login_error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';

function displayLoginErrorMessage($LoginError) {
    switch ($LoginError) {
        case 'invalid_credentials':
            return 'Email o password non validi.';
        case 'db_error':
            return 'Errore di connessione al database. Riprova più tardi.';
        case 'security_mismatch':
            return 'La risposta alla domanda di sicurezza non corrisponde.';
        default:
            return '';
    }
}


function displaySuccessMessage($success) {
    switch ($success) {
        case 'registered':
            return 'Registrazione completata con successo. Ora puoi accedere!';
        case 'password_changed':
            return 'Password modificata con successo. Ora puoi accedere!';
        default:
            return '';
    }
}

$LoginErrorMessage = displayLoginErrorMessage($LoginError);
$successMessage = displaySuccessMessage($success);


// Operazioni su form di registrazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $_GET['register'] = true; 
    error_log("GET['register']: " . $_GET['register']);
    $onRegister = true;


    $_SESSION['register_data'] = $_POST;

    // Prelievo dei dati dal form 
    $nome = trim($_POST['name']);
    $cognome = trim($_POST['surname']);
    $username = trim($_POST['username']);
    $domanda_sicurezza = trim($_POST['securityQuestion']);
    $risposta_sicurezza = trim($_POST['securityAnswer']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $repeat_password = $_POST['repeatPassword'];

    if ($_POST['password'] !== $repeat_password) {
        $_SESSION['registerPw_error'] = "Le password non coincidono.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?register=true");
        exit();
    }

    if (!checkPassword($_POST['password'])) {
        $_SESSION['registerPw_error'] = "La password non rispetta i criteri stabiliti.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?register=true");
        exit();
    }

    // Check per vedere se non esistono altri utenti con la stessa email
    $query = "SELECT id FROM utenti WHERE email = $1";
    $prep = pg_prepare($db, "check_email", $query);
    $result = pg_execute($db, "check_email", [$email]);

    if ($result && pg_num_rows($result) === 1) {
        $_SESSION['registerMail_error'] = "Utente già registrato con questa email.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?register=true");
        exit();
    }

    $query = "INSERT INTO utenti (username, password, email, domanda_sicurezza, risposta_sicurezza, info, preferences)
              VALUES ($1, $2, $3, $4, $5, $6, $7)";
    $prep = pg_prepare($db, "register_user", $query);
    $result = pg_execute($db, "register_user", [
        $username,
        $password,
        $email,
        $domanda_sicurezza,
        $risposta_sicurezza,
        json_encode(array('nome' => $nome, 'cognome' => $cognome)),
        json_encode(array('sortCriteria' => 'created_at', 'sortOrder' => 'DESC'))
    ]);

    if ($result) {
        unset($_SESSION['register_data']);
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=registered");
    } else {
        $_SESSION['registerDb_error'] = "Errore di connessione al database. Riprova più tardi.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?register=true");
    }

    pg_close($db);
    exit();
}

// Gestione visulizzazione dei messaggi d'errore
$errorRegisterMessage = '';
$errorRegisterMailMessage = '';
$errorRegisterDbMessage = '';
if (isset($_SESSION['registerPw_error'])) {
    $errorRegisterMessage = $_SESSION['registerPw_error'];
    unset($_SESSION['registerPw_error']); 
}

if (isset($_SESSION['registerMail_error'])) {
    $errorRegisterMailMessage = $_SESSION['registerMail_error'];
    unset($_SESSION['registerMail_error']);
}
if (isset($_SESSION['registerDb_error'])) {
    $errorRegisterDbMessage = $_SESSION['registerDb_error'];
    unset($_SESSION['registerDb_error']); 
}

// check per verifica dei criteri della password lato server
function checkPassword($password) {
    $password = trim($password);

    // Regex per verificare la password:
    // - Da 8 a 16 caratteri
    // - Almeno una maiuscola
    // - Almeno un simbolo fra %&@!
    if (!preg_match('/^(?=.*[A-Z])(?=.*[%&@!])[A-Za-z0-9%&@!]{8,16}$/', $password)) {
        return false;
    }    

    return true;
}

// Aggiornamento campi alla ricarica della pagina di registrazione (sticky form)
$nome = isset($_SESSION['register_data']['name']) ? htmlspecialchars($_SESSION['register_data']['name']) : '';
$cognome = isset($_SESSION['register_data']['surname']) ? htmlspecialchars($_SESSION['register_data']['surname']) : '';
$username = isset($_SESSION['register_data']['username']) ? htmlspecialchars($_SESSION['register_data']['username']) : '';
$domanda_sicurezza = isset($_SESSION['register_data']['securityQuestion']) ? htmlspecialchars($_SESSION['register_data']['securityQuestion']) : '';
$risposta_sicurezza = isset($_SESSION['register_data']['securityAnswer']) ? htmlspecialchars($_SESSION['register_data']['securityAnswer']) : '';
$email = isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : '';

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login e Registrazione</title>
    <link rel="stylesheet" href="../src/css/login.css">
    <link rel="stylesheet" href="../src/css/style.css">
</head>
<body>
    <!--HEADER-->
    <header>
        <div class="headerContainer">
            <div class="logoHeader"> 
                <a href="../index.php">
                    <img src="../images/login/logoHeader.png" alt="logoHeader">
                </a>
            </div>
            <div class="headerButtons">
                <a href="#" id="headerLoginButton" onclick="showLoginHideRegisterByRegister()">
                    <div>Accedi</div></a> 
                <a href="#" class="message" id="headerRegisterButton" onclick="showRegisterHideLogin()">Registrati</a>
            </div>
        </div>
    </header>
    <!--HEADER-->

    <main>
        <!-- Form di login --> 
        <div class="formPart <?php echo isset($_GET['register']) ? 'hidden' : ''; ?>" id="loginContainer" >
            <h2>Login</h2>

            <!-- mostra messaggi di errore relativi al login --> 
            <?php if (!empty($LoginErrorMessage)): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($LoginErrorMessage); ?></p>
                </div>
            <?php endif; ?>

            <form action="../server/api/login_action.php" method="POST">
                <div class="emailInputLogin">
                <input type="text" id="emailLogin" name="email" placeholder="email" autocomplete="email">
                </div>
                <div class="passwordInputLogin">
                    <input type="password" id="passwordLogin" name="password" placeholder="password" autocomplete="current-password" >
                </div>
    
                <div class="loginButton">
                    <button type="submit" id="loginButton" name="login">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        Accedi
                    </button> 
                </div>
            </form>

            <p class="message"> Non hai un account? 
                <a href="#" class="message" id="showRegisterByLogin" onclick="showRegisterHideLogin()"> Registrati! </a> 
            </p> 

            <p>
                <a href="#" class="message" id="showSecurityQuestion" onclick="showSecurityQuestion()">Hai dimenticato la password?</a>
            </p> 
            
        </div>



        <!-- Form di registrazione  -->
        <div class="formPart" id="registerContainer">
            <h2>Registrazione</h2>
            <form name="registerForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">

                <?php if ($errorRegisterDbMessage) {
                    echo "<div class='error-message'>" . htmlspecialchars($errorRegisterDbMessage) . '</div>';
                }
                ?>

                <div class="nameInputRegister">
                    <input type="text" id="nameRegister" name="name" value="<?php echo htmlspecialchars($nome); ?>" required placeholder="nome" oninput="inputNamesCheck(event)" autocomplete="name"> 
                    <span class="formError" id="checkName" >Hai inserito un carattere ambiguo nel campo nome</span>
                </div>

                <div class="surnameInputRegister">
                    <input type="text" id="surnameRegister" name="surname" value="<?php echo htmlspecialchars($cognome); ?>" required placeholder="cognome" oninput="inputNamesCheck(event)" autocomplete="family-name">
                    <span class="formError" id="checkSurname">Hai inserito un carattere ambiguo nel campo cognome</span>
                </div>

                <div class="usernameInputRegister">
                    <input type="text" id="usernameRegister" name="username" value="<?php echo htmlspecialchars($username); ?>" required placeholder="username" autocomplete="username">
                </div>

                <div class="securityQuestionInputRegister">
                <select id="securityQuestionRegister" name="securityQuestion" required>
                    <option value="" disabled <?php echo empty($domanda_sicurezza) ? 'selected' : ''; ?>>Seleziona una domanda di sicurezza</option>
                    <option value="motherMaidenName" <?php echo ($domanda_sicurezza == "motherMaidenName") ? 'selected' : ''; ?>>Qual è il nome da nubile di tua madre?</option>
                    <option value="firstPetName" <?php echo ($domanda_sicurezza == "firstPetName") ? 'selected' : ''; ?>>Qual è il nome del tuo primo animale domestico?</option>
                    <option value="favoriteColor" <?php echo ($domanda_sicurezza == "favoriteColor") ? 'selected' : ''; ?>>Qual è il tuo colore preferito?</option>
                    <option value="firstSchool" <?php echo ($domanda_sicurezza == "firstSchool") ? 'selected' : ''; ?>>Qual è il nome della tua prima scuola?</option>
                    <option value="birthCity" <?php echo ($domanda_sicurezza == "birthCity") ? 'selected' : ''; ?>>In quale città sei nato?</option>
                    <option value="bestFriend" <?php echo ($domanda_sicurezza == "bestFriend") ? 'selected' : ''; ?>>Chi è il tuo migliore amico/a dell'infanzia?</option>
                    <option value="favoriteSport" <?php echo ($domanda_sicurezza == "favoriteSport") ? 'selected' : ''; ?>>Qual è il tuo sport preferito?</option>
                </select>
                </div>

                <div class="securityAnswerInputRegister">
                    <input type="text" id="securityAnswerRegister" name="securityAnswer" value="<?php echo htmlspecialchars($risposta_sicurezza); ?>"required placeholder="risposta">
                </div>

                <div class="emailInputRegister">
                    <?php if ($errorRegisterMailMessage) {
                        echo "<div class='error-message'>" . htmlspecialchars($errorRegisterMailMessage) . '</div>';
                    }
                    ?>
                    <input type="email" id="emailRegister" name="email" required placeholder="email" onblur="inputMailCheck(event); checkEmailAvailability();" autocomplete="email"> 
                    <span class="formError" id="checkMail" >Formato della mail non corretto</span>
                    <span class="formError" id="checkMailDb" >Utente gia registrato con questa mail</span>
                </div>

                <div class="passwordInputRegister">
                    <input type="password" id="passwordRegister" name="password" required placeholder="password" onblur="inputPasswordCheck(event); inputPasswordFormCheck(event)"> 
                    <?php if ($errorRegisterMessage) {
                        echo "<div class='error-message'>" . htmlspecialchars($errorRegisterMessage) . '</div>';
                    }
                    ?>
                    <ul class="passwordCriteria">
                        <li>Da 8 a 16 caratteri</li>
                        <li>Almeno una lettera maiuscola</li>
                        <li>Almeno un simbolo fra <strong>%</strong>, <strong>&</strong>, <strong>@</strong>, <strong>!</strong></li>
                    </ul>
                    <span class="formError" id="checkPassword" >La password non rispetta i criteri stabiliti</span>
                </div>

                <div class="repeatPasswordInputRegister">
                    <input type="password" id="repeatPasswordRegister" name="repeatPassword" required  placeholder="conferma password" onblur="inputRepeatPasswordCheck(event)"> 
                    <span class="formError" id="checkRepeatPassword" >Le password non coincidono</span>
                </div>

                <div class="registerButton">
                    <button id="registerButton" type="submit" name="register" disabled>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        Registrati
                    </button>  
                </div>

                </form>
            <p class="message"> Hai già un account? 
                <a href="#" id="showLoginbyRegister" onclick="showLoginHideRegisterByRegister()"> Accedi!</a> 
            </p>
        </div>

         <!-- Form di Recupero password -->
        <div class="formPart" id="securityQuestionContainer">
            <form>
                <div class="usernameQuestion">
                    <input type="text" id="emailSecurityQuestion" name="emailSecurity" required placeholder="email">
                    <button type="button" id="buttonEmailSecurityQuestion" name="email_button" onclick="getSecurityQuestion()">Avanti</button> 
                </div>
                <div id="answerSecurityQuestion" class="securityQuestionInput">
                    <div id="shownUserSecurityQuestion"> </div>
                    <input type="text" id="securityAnswer" name="securityAnswer" required placeholder="risposta" >
                    <button type="button" id="buttonAnswerToChangePassword" name="answer_Security" onclick="showChangePasswordFormWithAnswerCheck()" >Avanti</button> 
                </div>
            </form>
        </div>

        
         <!-- Form di Cambio password  -->
         <div class="formPart" id="changePasswordContainer" >
            <form action="../server/api/login_action.php" method="POST">
                <div class="newPasswordInput"> 
                    <input type="hidden" id="emailChangePassword" name="emailChangePassword"> 
                    <input type="password" id="newPasswordOfChangePassword" name="newPassword" required placeholder="nuova password" onblur="inputPasswordCheck(event), inputPasswordFormCheck(event)">
                    <ul class="passwordCriteria" >
                        <li>Da 8 a 16 caratteri</li>
                        <li>Almeno una lettera maiuscola</li>
                        <li>Almeno un simbolo fra <strong>%</strong>, <strong>&</strong>, <strong>@</strong>, <strong>!</strong></li>
                    </ul>
                    <span class="formError" id="checkNewPassword" >La password non rispetta i criteri stabiliti</span>
                </div>
                <div class="confirmPasswordInput">  
                    <input type="password" id="confirmPasswordOfChangePassword" name="newRepeatPassword" required placeholder="conferma password" onblur="inputRepeatPasswordCheck(event)">
                    <span class="formError" id="checkRepeatNewPassword" >Le password non coincidono</span>
                </div>
                <div class="changePasswordButton">
                    <button type="submit" id="buttonChangePassword" name="change_Password" >Cambia Password</button> 
                </div>
            </form>
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
            <address>Hai riscontrato un errore oppure hai bisogno di aiuto? <a href="mailto:support@bagbuddy.com" >Contattaci</a></address>
            </div>
            <div>
                <p>© 2025 BagBuddy.</p>
            </div>
        </div>
    </footer>
    <!--FOOTER-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../src/js/login.js"></script>
</body>
</html>