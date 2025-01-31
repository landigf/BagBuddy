<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !isset($_POST['oldPassword']) || !isset($_POST['newPassword'])) {
    echo json_encode(['error' => 'Richiesta non valida']);
    exit;
}

require_once '../logindb.php';
$userID = $_SESSION['user']['id'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];

// Verifica che la nuova password sia valida
if (!preg_match('/^(?=.*[A-Z])(?=.*[%&@!])[A-Za-z0-9%&@!]{8,16}$/', $newPassword)) {
    echo json_encode(['error' => 'La nuova password non è valida. Deve contenere almeno una lettera maiuscola, un carattere speciale (%&@!), e deve essere lunga tra 8 e 16 caratteri.']);
    exit;
}

$conn = pg_connect($connection_string) or die(json_encode(['error' => 'Connessione al database fallita']));
$query = "SELECT password FROM utenti WHERE id = $1";
$result = pg_query_params($conn, $query, [$userID]) or die(json_encode(['error' => 'Query fallita']));
$user = pg_fetch_assoc($result);
if (!$user) {
    pg_close($conn);
    echo json_encode(['error' => 'Utente non trovato']);
    exit;
}
if (!password_verify($oldPassword, $user['password'])) {
    pg_close($conn);
    echo json_encode(['error' => 'Password errata']);
    exit;
}
$query = "UPDATE utenti SET password = $1 WHERE id = $2";
$result = pg_query_params($conn, $query, [password_hash($newPassword, PASSWORD_DEFAULT), $userID]) or die(json_encode(['error' => 'Query fallita']));
pg_close($conn);

$response = array("status" => "success", "message" => "Password cambiata con successo");
echo json_encode($response);
?>