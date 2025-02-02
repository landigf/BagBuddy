<?php
session_start();

require_once '../logindb.php';

$db = pg_connect($connection_string) or die('Impossibile connettersi al database: ' . pg_last_error());

// Operazioni su form di login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $email = trim($_POST['email']); 
    $password = $_POST['password'];

    $query = "SELECT * FROM utenti WHERE email = $1";
    $prep = pg_prepare($db, "login_user", $query);
    $result = pg_execute($db, "login_user", [$email]);

    if ($result && pg_num_rows($result) === 1) {
        $user = pg_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
           
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'info' => json_decode($user['info'], true),
                'preferences' => json_decode($user['preferences'], true)
            );

            if (isset($_SESSION['unsavedBag'])) {
                $bag = $_SESSION['unsavedBag'];
                $bagName = pg_escape_string($db, $bag['nome']);
                $userId = $user['id'];
                $prefereze = pg_escape_string($db, json_encode($bag['preferenze']));
                $items = pg_escape_string($db, json_encode($bag['items']));

                $query = "
                    INSERT INTO valigie (nome, id_utente, preferenze, lista_oggetti)
                    VALUES ('$bagName', $userId, '$prefereze', '$items')
                ";
                $result = pg_query($db, $query);

                unset($_SESSION['unsavedBag']);
                header('Location: ../../pages/profile.php?show=bags');  
                pg_close($db);
                exit();
            } 
            
            header('Location: ../../index.php'); 
        } else {
            header('Location: ../../pages/login.php?login_error=invalid_credentials');
        }
    } else {
        header('Location: ../../pages/login.php?login_error=invalid_credentials');
    }

    pg_close($db);
    exit();
}

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

// Operazione di cambio password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_Password'])) {
    $emailChange = trim($_POST['emailChangePassword']); 
    $newPassword = $_POST['newPassword'];
    $repeatPassword = $_POST['newRepeatPassword'];

    if ($newPassword !== $repeatPassword) {
        header('Location: ../../pages/login.php?login_error=password_mismatch');
        exit();
    }

    if (!checkPassword($newPassword)) {
        header('Location: ../../pages/login.php?login_error=invalid_password');
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $update_query = "UPDATE utenti SET password = $1 WHERE email = $2";
    $update_prep = pg_prepare($db, "update_password", $update_query);
    $update_result = pg_execute($db, "update_password", [$hashedPassword, $emailChange]);

    if ($update_result) {
        header('Location: ../../pages/login.php?success=password_changed');
    } else {
        header('Location: ../../pages/login.php?login_error=db_error');
    }

    pg_close($db);
    exit();
}


// Check per mail gia stata utilizzata (se l'utente è gia stato registrato)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkEmailDb'])) {
    $email = trim($_POST['checkEmailDb']);

    $query = "SELECT id FROM utenti WHERE email = $1";
    $prep = pg_prepare($db, "check_email", $query);
    $result = pg_execute($db, "check_email", [$email]);

    if ($result && pg_num_rows($result) === 1) {
        echo 'exists';
    } else {
        echo 'available';
    }

    pg_close($db);
}

// Mostra domanda di sicurezza associata alla email inserita nella fase di recupero 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['getSecurityEmail'])) {
    $email = trim($_POST['getSecurityEmail']);
    
    // Query per recuperare la domanda di sicurezza
    $query = "SELECT domanda_sicurezza FROM utenti WHERE email = $1";
    $prep = pg_prepare($db, "get_security_question", $query);
    $result = pg_execute($db, "get_security_question", [$email]);

    if ($result && pg_num_rows($result) === 1) {
        $row = pg_fetch_assoc($result);
        echo $row['domanda_sicurezza']; 
    } else {
        echo ''; 
    }

    pg_close($db);
}

// Check correttezza della risposta alla domanda di sicurezza inserita dall'utente 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailSecurityQuestion'], $_POST['securityAnswer'])) {
    $emailSecurity = trim($_POST['emailSecurityQuestion']);
    $answerSecurity = trim($_POST['securityAnswer']);

    $query = "SELECT risposta_sicurezza FROM utenti WHERE email = $1";
    $prep = pg_prepare($db, "check_security_answer", $query);
    $result = pg_execute($db, "check_security_answer", [$emailSecurity]);

    if ($result && pg_num_rows($result) === 1) {
        $row = pg_fetch_assoc($result);

        if ($row['risposta_sicurezza'] === $answerSecurity) {
            echo 'success'; 
        } else {
            echo 'errorAnswer'; 
        }
    } else {
        echo 'errorUser'; 
    }

    pg_close($db);
    exit();
}
?>