<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !isset($_POST['name']) || !isset($_POST['surname']) || !isset($_POST['username'])) {
    echo json_encode(['error' => 'Richiesta non valida']);
    exit;
}

if (empty($_POST['name']) || empty($_POST['surname']) || empty($_POST['username'])) {
    echo json_encode(['error' => 'Compila tutti i campi']);
    exit;
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


require_once '../logindb.php';

$userID = $_SESSION['user']['id'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$username = $_POST['username'];

$conn = pg_connect($connection_string) or die(json_encode(['error' => 'Connessione al database fallita']));
// otteniamo prima info dell'utente dal database: info JSONB, -- per ora contiene nome, cognome
$query = "SELECT info FROM utenti WHERE id = $1";
$result = pg_prepare($conn, "get_info", $query);
$result = pg_execute($conn, "get_info", array($userID));
$row = pg_fetch_assoc($result);
$info = json_decode($row['info'], true);
$info['nome'] = $name;
$info['cognome'] = $surname;
$info = json_encode($info);


if (isset($_POST['oldPassword']) && isset($_POST['newPassword'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    if (!checkPassword($newPassword)) {
        echo json_encode(['error' => 'La password deve contenere da 8 a 16 caratteri, almeno una maiuscola e almeno un simbolo fra %&@!']);
        exit;
    }

    $query = "SELECT password FROM utenti WHERE id = $1";
    $result = pg_prepare($conn, "get_password", $query);
    $result = pg_execute($conn, "get_password", array($userID));
    $row = pg_fetch_assoc($result);
    if (!password_verify($oldPassword, $row['password'])) {
        echo json_encode(['error' => 'Password errata']);
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $query = "UPDATE utenti SET info = $1, username = $2, password = $3 WHERE id = $4";
    $result = pg_prepare($conn, "update_user", $query);
    $result = pg_execute($conn, "update_user", array($info, $username, $hashedPassword, $userID));
} else {
    $query = "UPDATE utenti SET info = $1, username = $2 WHERE id = $3";
    $result = pg_prepare($conn, "update_user", $query);
    $result = pg_execute($conn, "update_user", array($info, $username, $userID));
}

if ($result) {
    if (is_string($_SESSION['user']['info'])) {
        $_SESSION['user']['info'] = json_decode($_SESSION['user']['info'], true);
    }
    $_SESSION['user']['info']['nome'] = $name;
    $_SESSION['user']['info']['cognome'] = $surname;
    $_SESSION['user']['username'] = $username;
    echo json_encode(['success' => 'Profilo aggiornato con successo']);
} else {
    echo json_encode(['error' => 'Errore durante l\'aggiornamento del profilo']);
}

pg_close($conn);
exit;
?>